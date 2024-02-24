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
            $table->string('type')->nullable()->default('material');
            $table->text('description')->nullable();

            $table->foreignIdFor(\App\Models\User::class,'manager_id')->nullable();
            $table->foreignIdFor(\App\Models\Accounting\Account::class)->nullable();
            $table->foreignIdFor(\App\Models\Accounting\Account::class,'cog_account_id')->nullable()->comment('cost of goods account');

            $table->foreignIdFor(\App\Models\Accounting\Account::class,'p_account_id')->nullable()->comment('purchases account');
            $table->foreignIdFor(\App\Models\Accounting\Account::class,'pr_account_id')->nullable()->comment('purchases return account');
            $table->foreignIdFor(\App\Models\Accounting\Account::class,'pd_account_id')->nullable()->comment('purchases discount account');

            $table->foreignIdFor(\App\Models\Accounting\Account::class,'s_account_id')->nullable()->comment('sales account');
            $table->foreignIdFor(\App\Models\Accounting\Account::class,'sr_account_id')->nullable()->comment('sales return account');
            $table->foreignIdFor(\App\Models\Accounting\Account::class,'sd_account_id')->nullable()->comment('sales Discount account');

            $table->foreignIdFor(\App\Models\Accounting\Account::class,'ss_account_id')->nullable()->comment('Service account');
            $table->foreignIdFor(\App\Models\Accounting\Account::class,'or_account_id')->nullable()->comment('other revenue account');
            $table->foreignIdFor(\App\Models\Accounting\CostCenter::class)->nullable()->comment('sales account');


            $table->foreignIdFor(\App\Models\Sales\Client::class)->nullable();
            $table->foreignIdFor(\App\Models\Sales\PriceList::class)->nullable();

            $table->decimal('space',15,2)->nullable()->comment('space area by m2');
            $table->decimal('height',15,2)->nullable()->comment('height by m');

            $table->boolean('is_rma')->default(false);
            $table->boolean('is_rented')->default(false);
            $table->boolean('has_security')->default(false);
//            $table->boolean('connected')->default(true);
            $table->boolean('active')->default(false);
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
