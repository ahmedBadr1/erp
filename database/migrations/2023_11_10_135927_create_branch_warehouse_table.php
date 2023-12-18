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
        Schema::create('branch_warehouse', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Hr\Branch::class);
            $table->foreignIdFor(\App\Models\Inventory\Warehouse::class);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_warehouse');
    }
};
