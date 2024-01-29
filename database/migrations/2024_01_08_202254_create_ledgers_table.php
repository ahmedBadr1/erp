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
        Schema::create('ledgers', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount',15,2);
            $table->text('description')->nullable();
            $table->string('paper_ref')->nullable();
            $table->dateTime('due');
            $table->foreignIdFor(\App\Models\Accounting\TransactionGroup::class,'group_id')->nullable();
            $table->foreignIdFor(\App\Models\Accounting\Currency::class)->nullable();
            $table->float('ex_rate')->nullable();
            $table->foreignId('created_by')->constrained('users')->onUpdate('cascade');
            $table->foreignId('responsible_id')->nullable()->constrained('users')->onUpdate('cascade');
            $table->foreignId('edited_by')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('set null');
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
        Schema::dropIfExists('ledgers');
    }
};
