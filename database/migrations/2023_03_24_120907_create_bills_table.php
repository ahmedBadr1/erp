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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Accounting\Account::class); // 1
            $table->foreignIdFor(\App\Models\Inventory\Warehouse::class); // 2
            $table->foreignIdFor(\App\Models\Inventory\Branch::class)->nullable();
            $table->foreignIdFor(\App\Models\Purchases\Supplier::class); // 3
            $table->foreignIdFor(\App\Models\System\Status::class);
            $table->boolean('tax_exclusive');
            $table->boolean('tax_inclusive');
            $table->string('code');
            $table->string('number')->nullable();
            $table->dateTime('billed_at');
            $table->dateTime('due_at');
            $table->foreignId('responsible_id')
                ->nullable()
                ->references('id')
                ->on('users')
                ->onUpdate('cascade');
            $table->decimal('paid',15,2)->nullable();
            $table->decimal('sub_total',15,2);
            $table->decimal('tax_total',15,2)->default(0);
            $table->decimal('discount',15,2)->default(0);
            $table->decimal('total',15,2);
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
