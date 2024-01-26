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
            $table->decimal('amount',15,2);
            $table->foreignIdFor(\App\Models\Accounting\Account::class);
            $table->foreignIdFor(\App\Models\Accounting\Ledger::class);
            $table->foreignIdFor(\App\Models\Accounting\CostCenter::class)->nullable();
            $table->string('comment')->nullable();
            $table->boolean('posted')->default(false);
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
