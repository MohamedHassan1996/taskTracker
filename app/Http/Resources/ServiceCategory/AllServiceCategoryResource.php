<?php

namespace App\Http\Resources\ServiceCategory;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class AllServiceCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'serviceCategoryId' => $this->id,
            'name' => $this->name,
            'startAt' => Carbon::parse($this->start_at)->format('d/m/Y'),
            'endAt' => Carbon::parse($this->end_at)->format('d/m/Y'),
        ];
    }
}
