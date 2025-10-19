<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user'  => new UserResource($this->resource['user']),
            'token' => $this->when(
                Arr::has($this->resource, 'token'),
                Arr::get($this->resource, 'token')
            ),
        ];
    }
}
