<?php

namespace App\Http\Resources;

use App\Models\Template;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Template
 */
class TemplateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_by' => $this->created_by,
            'type' => $this->type,
        ];
    }
}
