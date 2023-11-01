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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount',15,2);
            $table->string('type');
            $table->text('description')->nullable();
            $table->dateTime('due');
            $table->foreignIdFor(\App\Models\User::class);
            $table->nullableMorphs('reference');
            $table->boolean('post')->default(false);
            $table->boolean('locked')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
