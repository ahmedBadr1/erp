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
        Schema::create('item_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Inventory\Item::class);
            $table->double('quantity');
            $table->foreignId('from_id')
                ->references('id')
                ->on('warehouses')
                ->onDelete('cascade');
            $table->foreignId('to_id')
                ->references('id')
                ->on('warehouses')
                ->onDelete('cascade');
            $table->string('reason')->nullable();
            $table->dateTime('accepted_at')->nullable();
            $table->dateTime('received_at')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_transfers');
    }
};
