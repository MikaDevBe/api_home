<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
          'name' => $this->name,
          'firstname' => $this->firstname,
          'identifiant' => $this->identifiant,
          'active' => $this->active,
          'mail' => $this->profile->mail ?? null,
          'phone' => $this->profile->phone ?? null,
          'address' => $this->profile->address ?? null,
          'town' => $this->profile->town ?? null,
          'postalCode' => $this->profile->postal_code ?? null,
          'country' => $this->profile->country ?? null,
          'image' => $this->profile->image ? asset('storage/profiles/' . $this->profile->image) : null,
        ];
    }
}
