<?php

namespace App\Http\Resources;

use App\Enum\ProductStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'description' => $this->description,
            'regularPrice' => $this->regularPrice,
            'salePrice' => $this->salePrice,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'featuredImage' => $this->featuredImage,
            'status' => $this->status->value,
            'variations' => VariationResource::collection($this->whenLoaded('variations')),
            // 'createdAt' => $this->created_at,
            // 'updatedAt' => $this->updated_at,
        ];
    }
}
