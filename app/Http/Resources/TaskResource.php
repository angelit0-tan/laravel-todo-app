<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'description'   => $this->description,
            'due_date'      => $this->due_date ?? null,
            'task_priority' => $this->task_priority ?? 0,
            'task_order'    => $this->task_order,
            'tags'          => $this->whenLoaded('tags', TagResource::collection($this->tags)),
            'attachments'   => $this->whenLoaded('attachments', AttachmentResource::collection($this->attachments)),
            'task_status'   => $this->task_status,
            'completed_at'  => $this->completed_at,
            'archived_at'   => $this->archived_at,
            'created_at'    => $this->created_at
        ];
    }
}
