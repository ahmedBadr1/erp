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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_2')->nullable();
            $table->string('code')->unique();
            $table->foreignIdFor(\App\Models\Inventory\Warehouse::class)->nullable();
            $table->string('origin_number')->nullable();
            $table->string('type')->nullable();

            $table->decimal('price', 8, 2)->nullable();
            $table->decimal('d_price', 8, 2)->nullable();
            $table->decimal('sd_price', 8, 2)->nullable();
            $table->decimal('min_price', 8, 2)->nullable();
            $table->decimal('ref_price', 8, 2)->nullable();
//            $table->decimal('first_cost', 8, 2)->nullable(); // fifo filo
//            $table->decimal('last_cost', 8, 2)->nullable(); // avg cost
            $table->decimal('avg_cost', 8, 2)->nullable();
            $table->decimal('profit_margin', 8, 4)->nullable(); // %10

            $table->string('warranty')->nullable();
            $table->date('expire_date')->nullable(); // 1 = 12 month
            $table->string('barcode')->nullable();
            $table->string('hs_code')->nullable();
            $table->string('batch_number')->nullable();
            $table->foreignId('tax1_id')->nullable()->references('id')->on('taxes');
            $table->foreignId('tax2_id')->nullable()->references('id')->on('taxes');
            $table->foreignIdFor(\App\Models\Inventory\Unit::class)->nullable();
            $table->foreignIdFor(\App\Models\Inventory\Brand::class)->nullable();
            $table->foreignIdFor(\App\Models\Purchases\Supplier::class)->nullable();
            $table->foreignIdFor(App\Models\Employee\Employee::class)->nullable();

            $table->decimal('weight', 10, 2)->nullable();
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('length', 10, 2)->nullable();
            $table->decimal('height', 10, 2)->nullable();

            $table->decimal('max_limit', 12, 2)->nullable();
            $table->decimal('min_limit', 12, 2)->nullable();

            $table->boolean('require_barcode')->default(false);
            $table->boolean('repeat_barcode')->default(false);
            $table->boolean('negative_stock')->default(false);
            $table->boolean('can_be_sold')->default(true);
            $table->boolean('can_be_purchased')->default(true);
            $table->boolean('returnable')->default(true);
            $table->boolean('active')->default(true);

            $table->foreignIdFor(\App\Models\Accounting\Category::class)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
