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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Inventory\Product::class)->nullable();
            $table->foreignIdFor(\App\Models\Purchases\Bill::class)->nullable();
            $table->string('quantity');
            $table->decimal('price',15,4);
            $table->decimal('cost',15,4);

//            $table->decimal('tax_exclusive',10,2);
//            $table->decimal('tax_inclusive',10,2);

            $table->foreignIdFor(\App\Models\Inventory\Unit::class)->nullable();

            $table->foreignIdFor(\App\Models\Inventory\Warehouse::class,'warehouse_id')->nullable();
            $table->foreignIdFor(\App\Models\Inventory\InvTransaction::class,)->nullable();

            $table->foreignIdFor(\App\Models\User::class);

            $table->text('comment')->nullable();


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
        Schema::dropIfExists('items');
    }
};
