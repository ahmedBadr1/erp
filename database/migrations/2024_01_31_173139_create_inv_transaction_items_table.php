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
        Schema::create('inv_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Inventory\InvTransaction::class);
            $table->foreignIdFor(\App\Models\Inventory\Product::class);
            $table->decimal('quantity',10);
            $table->decimal('price',15,4);
            $table->boolean('accepted')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inv_transaction_items');
    }
};
