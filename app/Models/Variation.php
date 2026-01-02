<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\VariationType;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Variation extends Model
{
    /** @use HasFactory<\Database\Factories\VariationFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'variation_type_id',
        'title',
        'description',
    ];

    public function variationType(): BelongsTo
    {
        return $this->belongsTo(VariationType::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
