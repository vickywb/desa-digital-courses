<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
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
            'name' => $this->file_name,
            'url' => $this->file_url,
            'path' => $this->file_path,
            'type' => $this->file_type,
            'size' => $this->file_size,
            'upload_at' => $this->created_at?->format('d-m-Y H:i:s'),
        ];
    }
}
