<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->user_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'title' => $this->title,
            'details' => $this->details,
            'sharedWith' => UserResource::collection($this->whenLoaded('sharedWith')),
            'sharedWithCount' => $this->whenCounted('sharedWith'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
