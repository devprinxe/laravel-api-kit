<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Variation;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VariationType extends Model
{
    /** @use HasFactory<\Database\Factories\VariationTypeFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    public function variations(): HasMany
    {
        return $this->hasMany(Variation::class);
    }
}
