<?php

namespace Vanguard\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
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
            'permission_id' => (int) $this->id,
            'permission_display_name' => $this->display_name,
            'permission_description' => $this->description,
            // 'name' => $this->name,
            // 'removable' => (boolean) $this->removable,
            // 'updated_at' => (string) $this->updated_at,
            // 'created_at' => (string) $this->created_at
        ];
    }
}
