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
        Schema::create('entries', function (Blueprint $table) {
            $table->id();
            $table->boolean('credit');
            $table->bigInteger('amount');
            $table->text('description');
            $table->foreignIdFor(\App\Models\Accounting\Account::class)->nullable();
            $table->foreignIdFor(\App\Models\Accounting\Transaction::class)->nullable();
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
        Schema::dropIfExists('entries');
    }
};
