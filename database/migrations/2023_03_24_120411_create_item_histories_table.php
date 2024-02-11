<?php

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
        Schema::create('item_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Inventory\Product::class);
            $table->foreignIdFor(\App\Models\Inventory\Warehouse::class);
            $table->integer('quantity');
            $table->decimal('price',15,4)->nullable();
            $table->decimal('avg_cost',15,4)->nullable();
            $table->morphs('second_party');
            $table->integer('balance');


            $table->foreignIdFor(\App\Models\Inventory\Unit::class)->nullable();

            $table->foreignIdFor(\App\Models\Inventory\InvTransaction::class)->nullable();
//            $table->decimal('local_max_limit', 12, 2)->nullable();
//            $table->decimal('local_min_limit', 12, 2)->nullable();
//            $table->decimal('local_reorder_limit', 12, 2)->nullable();
            $table->boolean('in');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_histories');
    }
};
