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
            $table->string('code');//->index()->unique();
            $table->decimal('amount',15,2);
            $table->string('type')->index();
            $table->text('description')->nullable();
            $table->string('je_code')->nullable();
            $table->string('document_no')->nullable();
            $table->dateTime('due');
            $table->foreignIdFor(\App\Models\Accounting\Ledger::class);
            $table->foreignIdFor(\App\Models\Accounting\Account::class);
            $table->foreignIdFor(\App\Models\User::class);
            $table->nullableMorphs('reference');
            $table->boolean('posted')->default(false);
            $table->boolean('locked')->default(false);
            $table->boolean('system')->default(true);
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
