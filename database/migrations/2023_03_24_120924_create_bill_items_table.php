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
        Schema::create('bill_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Purchases\Bill::class)->nullable();
            $table->foreignIdFor(\App\Models\Inventory\Product::class);
            $table->foreignIdFor(\App\Models\Accounting\Tax::class)->nullable();

            $table->integer('quantity');
            $table->decimal('price',15,4);
            $table->decimal('cost',15,4);
            $table->decimal('avg_cost',15,4)->nullable();
            $table->decimal('tax_value',15,4);
            $table->decimal('sub_total',15,4);
            $table->decimal('total',15,4);
            $table->foreignIdFor(\App\Models\Inventory\InvTransaction::class,)->nullable();
            $table->string('comment')->nullable();
            $table->dateTime('expire_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_items');
    }
};
