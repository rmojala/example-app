<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::where('is_admin', false)
            ->orderBy('email')
            ->get();

        return Inertia::render('admin/users/Index', [
            'users' => UserResource::collection($users),
        ]);
    }

    /**
     * Bulk updates the given users' ability to create notes. 
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'users' => [
                'required',
                'list',
            ],
            'users.*.id' => [
                'bail',
                'required',
                'integer',
                // A user with the given id must exist but must not
                // be an admin. In a real app I might do a post-update
                // check to handle the rare case where a user is
                // granted admin role during the processing of this
                // request.
                Rule::exists(User::class, 'id')
                    ->where(function ($query) {
                        $query->where('is_admin', false);
                    }),
            ],
            'users.*.canCreateNotes' => [
                'required',
                'boolean',
            ],
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['users'] as $user) {
                User::where('id', $user['id'])
                    ->update([
                        'can_create_notes' => $user['canCreateNotes'],
                    ]);
            }
        });
        
        return response()->json([
            'message' => 'Update successful',
        ]);
    }
}
