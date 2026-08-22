<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

// Resource: chỉ lộ trường cần cho FE
class CategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => $this->id, 'name' => $this->name, 'slug' => $this->slug, 'description' => $this->description];
    }
}
