<?php

use App\Enum\ProductStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('regularPrice', 10, 2);
            $table->decimal('salePrice', 10, 2);
            $table->string('sku')->unique();
            $table->string(column: 'barcode')->unique();
            $table->string('featuredImage')->nullable();
            $table->enum("status", array_column(ProductStatus::cases(), 'name'))
                  ->default(ProductStatus::DRAFT->name);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
