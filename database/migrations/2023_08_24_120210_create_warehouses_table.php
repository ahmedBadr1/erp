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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('material');
            $table->text('description')->nullable();

            $table->foreignIdFor(\App\Models\User::class,'manager_id')->nullable();
            $table->foreignIdFor(\App\Models\Accounting\Account::class);

            $table->foreignIdFor(\App\Models\Sales\Client::class)->nullable();
            $table->foreignIdFor(\App\Models\Sales\PriceList::class)->nullable();

            $table->decimal('space',15,2)->nullable()->comment('space area by m2');
            $table->decimal('height',15,2)->nullable()->comment('height by m');

            $table->boolean('is_rma')->default(false);
            $table->boolean('is_rented')->default(false);
            $table->boolean('has_security')->default(false);

            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
