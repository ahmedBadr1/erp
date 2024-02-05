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
            $table->decimal('amount',15,4);
            $table->string('type')->index();
            $table->text('note')->nullable();
            $table->string('paper_ref')->nullable();
            $table->dateTime('due');
            $table->foreignIdFor(\App\Models\System\ModelGroup::class,'group_id')->nullable();
            $table->foreignIdFor(\App\Models\Accounting\Ledger::class)->nullable();
            $table->foreignIdFor(\App\Models\Accounting\Currency::class)->nullable();
            $table->float('ex_rate')->nullable();
            $table->decimal('currency_total',15,4)->nullable();
            $table->foreignIdFor(\App\Models\Accounting\Account::class,'first_party_id');
            $table->foreignIdFor(\App\Models\Accounting\Account::class,'second_party_id');
            $table->foreignIdFor(\App\Models\User::class,'responsible_id')->nullable();
            $table->foreignIdFor(\App\Models\User::class,'created_by');
            $table->foreignIdFor(\App\Models\User::class,'edited_by')->nullable();
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
