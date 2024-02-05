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
            $table->string('code');
            $table->string('type');
            $table->foreignIdFor(\App\Models\Accounting\Account::class,'treasury_id')->nullable(); // 1
            $table->foreignIdFor(\App\Models\System\ModelGroup::class,'group_id')->nullable();
            $table->foreignIdFor(\App\Models\Inventory\Warehouse::class); // 2
            $table->morphs('second_party');
            $table->foreignIdFor(\App\Models\Accounting\Currency::class)->nullable(); // 3
            $table->foreignIdFor(\App\Models\Accounting\Tax::class)->nullable(); // 3

            $table->float('ex_rate')->nullable();
            $table->decimal('currency_total',15,4)->nullable();



            $table->foreignIdFor(\App\Models\Inventory\Branch::class)->nullable();
            $table->foreignIdFor(\App\Models\System\Status::class);
            $table->foreignIdFor(\App\Models\User::class,'responsible_id')->nullable(); // 3
            $table->foreignIdFor(\App\Models\User::class,'created_by')->nullable(); // 3
            $table->foreignIdFor(\App\Models\User::class,'updated_by')->nullable(); // 3


            $table->string('paper_ref')->nullable();
            $table->dateTime('date');
            $table->dateTime('deliver_at')->nullable();

            $table->boolean('tax_exclusive');
            $table->boolean('tax_inclusive');
            $table->boolean('cost_allocation')->default(false);


            $table->decimal('paid',15,4)->nullable();
            $table->decimal('gross_total',15,4)->comment("total Bill Items value and qty");
            $table->decimal('discount',15,4)->comment("Discount cal to value not percentage")->default(0);
            $table->decimal('sub_total',15,4)->comment("Gross Total after discount");
            $table->decimal('tax_total',15,4)->comment("Tax value")->default(0);
            $table->decimal('total',15,4)->comment("Sub Total after tax");
            $table->text('note')->nullable();
            $table->boolean('canceled')->default(false);
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
