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
        Schema::create('inv_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('code');//->index()->unique();
            $table->decimal('amount',15,4);
            $table->string('type')->index();
            $table->text('note')->nullable();
            $table->string('paper_ref')->nullable();
            $table->dateTime('due');
            $table->foreignIdFor(\App\Models\Inventory\Warehouse::class,'from_id');
            $table->foreignIdFor(\App\Models\Inventory\Warehouse::class,'to_id')->nullable();
            $table->foreignIdFor(\App\Models\Accounting\TransactionGroup::class,'group_id')->nullable();
            $table->foreignIdFor(\App\Models\Purchases\Supplier::class)->nullable();
            $table->foreignIdFor(\App\Models\Purchases\Bill::class)->nullable();
            $table->foreignIdFor(\App\Models\Sales\Client::class)->nullable();
            $table->foreignIdFor(\App\Models\Sales\Invoice::class)->nullable();

            $table->foreignIdFor(\App\Models\User::class,'responsible_id')->nullable();
            $table->foreignIdFor(\App\Models\User::class,'created_by');
            $table->foreignIdFor(\App\Models\User::class,'edited_by')->nullable();
            $table->string('reason')->nullable();
            $table->dateTime('accepted_at')->nullable();
            $table->boolean('pending')->default(true);
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
        Schema::dropIfExists('inv_transactions');
    }
};
