<?php

namespace App\Http\Controllers;

use App\Http\Resources\NoteResource;
use App\Http\Resources\UserResource;
use App\Models\Note;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', Note::class);

        $notes = auth()->user()
            ->notes()
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->withCount('sharedWith')
            ->get();

        $sharedNotes = auth()->user()
            ->sharedNotes()
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get();

        return Inertia::render('notes/Index', [
            'notes' => NoteResource::collection($notes),
            'sharedNotes' => NoteResource::collection($sharedNotes),
            'can' => [
                'createNotes' => auth()->user()->can('create', Note::class),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // To keep the app simple, a user is allowed to choose from a list
        // of all users who to share the note with. I wouldn't do this in
        // a real app.

        Gate::authorize('create', Note::class);

        return Inertia::render('notes/Create', [
            'users' => UserResource::collection($this->getUsers()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // I prefer explicit authorization and validation in controllers
        // rather than hiding them in form requests.
        //
        // In this simple app, I keep all business logic in controllers.
        // As the logic gets more complex, I would move the logic to
        // service/action classes.
        
        Gate::authorize('create', Note::class);

        $validated = $request->validate($this->rules());

        $note = DB::transaction(function () use ($validated) {
            $note = auth()->user()
                ->notes()
                ->create($validated);

            $note->sharedWith()
                ->sync(Arr::get($validated, 'sharedWith', []));

            return $note;
        });
        
        Log::info('Created note', ['note' => $note]);

        return redirect()->route('notes.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note)
    {
        Gate::authorize('view', $note);

        $isNoteOwner = auth()->id() === $note->user_id;

        if ($isNoteOwner) {
            $note->load('sharedWith');
        } else {
            $note->load('user');
        }
        
        return Inertia::render('notes/Show', [
            'note' => new NoteResource($note),
            'isNoteOwner' => $isNoteOwner,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Note $note)
    {
        Gate::authorize('update', $note);

        $note->load('sharedWith');

        return Inertia::render('notes/Edit', [
            'note' => new NoteResource($note),
            'users' => UserResource::collection($this->getUsers()),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Note $note)
    {
        Gate::authorize('update', $note);

        $validated = $request->validate($this->rules());

        DB::transaction(function () use ($note, $validated) {
            $note->update($validated);
        
            $changes = $note->sharedWith()
                ->sync(Arr::get($validated, 'sharedWith', []));

            // The UI sorts the notes based on the updated_at timestamp.
            // But the note's updated_at timestamp is not updated if update()
            // didn't change any data. To avoid UI inconsistencies, we update
            // the timestamp manually if the sharedWith relationship was
            // updated.
            if (collect($changes)->flatten()->isNotEmpty()) {
                $note->touch();
            }
        });

        Log::info('Updated note', ['note' => $note]);

        return redirect()->route('notes.show', $note);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        Gate::authorize('delete', $note);
        
        $note->delete();

        Log::info('Deleted note', ['note' => $note]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Note deleted',
        ]);

        return redirect()->route('notes.index');
    }

    /**
     * Returns all users except the current user.
     */
    private function getUsers()
    {
        return User::where('id', '<>', auth()->id())
            ->orderBy('email')
            ->get();
    }

    private function rules()
    {
        return [
            // In a real app I would configure values like the maximum
            // lengths in a config file or the database and use the configured
            // values here.
            'title' => [
                'required',
                Rule::string()->max(255),
            ],
            'details' => [
                'nullable',
                Rule::string()->max(10000),
            ],
            'sharedWith' => [
                'sometimes',
                'list',
            ],
            'sharedWith.*' => [
                'bail',
                'integer',
                // A user with the given id must exist but must not
                // be the current user.
                Rule::exists(User::class, 'id')
                    ->where(function ($query) {
                        $query->where('id', '<>', auth()->id());
                    }),
            ],
        ];
    }

}
