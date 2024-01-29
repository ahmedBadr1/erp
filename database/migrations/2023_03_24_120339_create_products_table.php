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
            $table->string('short_name')->nullable();
            $table->foreignIdFor(\App\Models\Inventory\Warehouse::class)->nullable();
            $table->foreignIdFor(\App\Models\Inventory\WarehouseShelf::class)->nullable();
            $table->string('part_number')->nullable();
            $table->string('sku')->nullable();
            $table->string('location')->nullable();
            $table->string('oe_number')->nullable();
            $table->string('e_code')->nullable();
            $table->string('e_code_type')->nullable();

            $table->string('origin_number')->nullable();
            $table->string('type')->nullable();

            $table->decimal('s_price', 12, 2)->nullable();
            $table->decimal('d_price', 12, 2)->nullable();
            $table->decimal('sd_price', 12, 2)->nullable();
            $table->decimal('min_price', 12, 2)->nullable();
            $table->decimal('ref_price', 12, 2)->nullable();
            $table->decimal('pur_price', 12, 2)->nullable();

            $table->decimal('last_cost', 12, 2)->nullable();
            $table->decimal('avg_cost', 12, 2)->nullable();
            $table->decimal('fifo', 12, 2)->nullable();
            $table->decimal('lifo', 12, 2)->nullable();
            $table->decimal('opening_balance', 12, 2)->nullable();

            $table->decimal('profit_margin', 8, 4)->nullable(); // %10

            $table->tinyInteger('warranty')->nullable()->comment('in month');
            $table->tinyInteger('valid_to')->nullable()->comment('in month'); // 1 = 12 month
            $table->string('barcode')->nullable();
            $table->string('hs_code')->nullable();
            $table->string('batch_number')->nullable();
            $table->foreignIdFor(\App\Models\Inventory\Unit::class)->nullable();
            $table->foreignIdFor(\App\Models\Inventory\Brand::class)->nullable();
            $table->foreignIdFor(\App\Models\Purchases\Supplier::class)->nullable();
            $table->foreignIdFor(App\Models\User::class)->nullable();

            $table->decimal('max_limit', 12, 2)->nullable();
            $table->decimal('min_limit', 12, 2)->nullable();
            $table->decimal('reorder_limit', 12, 2)->nullable();


//            $table->boolean('has_serial')->default(false);
            $table->boolean('require_barcode')->default(false);
            $table->boolean('repeat_barcode')->default(false);
            $table->boolean('negative_stock')->default(false);
            $table->boolean('can_be_sold')->default(true);
            $table->boolean('can_be_purchased')->default(true);
            $table->boolean('returnable')->default(true);
            $table->boolean('active')->default(true);

            $table->foreignIdFor(\App\Models\Inventory\ProductCategory::class)->nullable();
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
