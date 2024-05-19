<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CardResource extends JsonResource
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
            'content' => $this->content,
            'position' => $this->position,
            'column' => $this->column_id,
            'title' => $this->title,
            'is_completed' => $this->is_completed,
            'parent' => $this->parent,
            'children' => $this->children,
            'deadline' => $this->deadline? $this->deadline -> format('Y-m-d H:i') : null,
        ];
    }
}
