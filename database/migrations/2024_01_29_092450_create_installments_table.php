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
        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            $table->boolean('credit');
            $table->decimal('amount',15,2)->nullable();
            $table->dateTime('due_at')->nullable();
            $table->string('note')->nullable();
            $table->string('paper_ref')->nullable();
            $table->string('comment')->nullable();
            $table->string('note_type')->nullable();
            $table->foreignIdFor(\App\Models\Accounting\Account::class);
            $table->foreignIdFor(\App\Models\Accounting\Account::class,'bank_id');
            $table->morphs('installmentable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
};
