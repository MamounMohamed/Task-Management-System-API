<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'due_date' => $this->due_date,
            'assignee' => $this->whenLoaded('assignee')->only(['id', 'name', 'email']),
            'creator' => $this->whenLoaded('creator')->only(['id', 'name', 'email']),
            'dependencies' => TaskResource::collection($this->whenLoaded('dependencies')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
