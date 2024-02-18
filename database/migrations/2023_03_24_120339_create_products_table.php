<?php

use App\Models\Accounting\Tax;
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
            $table->integer('balance')->default(0);

            $table->string('part_number')->nullable();
            $table->string('sku')->nullable();
            $table->string('location')->nullable();
            $table->string('oe_number')->nullable();
            $table->string('e_code')->nullable();
            $table->string('e_code_type')->nullable();

            $table->string('image')->nullable();
            $table->string('origin_number')->nullable();
            $table->string('type')->nullable();

            $table->decimal('s_price', 15, 4)->default('0.0000');
            $table->decimal('d_price', 15, 4)->default('0.0000');
            $table->decimal('sd_price', 15, 4)->default('0.0000');
            $table->decimal('min_price', 15, 4)->default('0.0000');
            $table->decimal('ref_price', 15, 4)->default('0.0000');
            $table->decimal('pur_price', 15, 4)->default('0.0000');

            $table->decimal('last_cost', 15, 4)->default('0.0000');
            $table->decimal('avg_cost', 15, 4)->default('0.0000');
            $table->decimal('fifo', 15, 4)->default('0.0000');
            $table->decimal('lifo', 15, 4)->default('0.0000');
            $table->decimal('opening_balance', 15, 4)->default('0.0000');

            $table->decimal('profit_margin', 8, 4)->nullable(); // %10

            $table->tinyInteger('warranty')->nullable()->comment('in month');
            $table->tinyInteger('valid_to')->nullable()->comment('in month'); // 1 = 12 month
            $table->string('barcode')->nullable();
            $table->string('hs_code')->nullable();

            $table->string('batch_number')->nullable();
            $table->foreignIdFor(Tax::class)->nullable();
            $table->foreignIdFor(Tax::class,'withholding_tax_id')->nullable();

            $table->foreignIdFor(\App\Models\Inventory\Unit::class)->nullable();
            $table->foreignIdFor(\App\Models\Inventory\Brand::class)->nullable();
            $table->foreignIdFor(\App\Models\Purchases\Supplier::class)->nullable();
            $table->foreignIdFor(App\Models\User::class)->nullable();

            $table->decimal('max_limit', 12, 2)->nullable();
            $table->decimal('min_limit', 12, 2)->nullable();
            $table->decimal('reorder_limit', 12, 2)->nullable();

//            $table->boolean('available_online')->default(false);
//            $table->boolean('featured')->default(false);



            $table->boolean('track_stock')->default(false)->comment('send notifications on min - max limits');
            $table->boolean('require_serial')->default(false)->comment('enforce serial entry');
            $table->boolean('repeat_serial')->default(false)->comment('allow serial repeat');
            $table->boolean('negative_stock')->default(false);
            $table->boolean('use_batch_number')->default(false);
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
