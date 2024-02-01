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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->index();
            $table->string('type_code')->nullable();
            $table->string('name');
            $table->boolean('credit');
            $table->text('description')->nullable();
            $table->boolean('accept_cost_center')->default(0);
            $table->float('credit_limit')->nullable();
            $table->float('debit_limit')->nullable();
            $table->float('c_opening')->nullable();
            $table->float('d_opening')->nullable();
            $table->dateTime('opening_date')->nullable();
            $table->float('balance')->default(0);
            $table->boolean('system')->default(false)->index();
            $table->boolean('active')->default(true)->index();
            $table->foreignIdFor(\App\Models\Accounting\Node::class);
            $table->foreignIdFor(\App\Models\Accounting\AccountType::class)->nullable();
            $table->foreignIdFor(\App\Models\Accounting\AccountGroup::class)->nullable();
            $table->foreignIdFor(\App\Models\Accounting\CostCenter::class)->nullable();
            $table->foreignIdFor(\App\Models\Accounting\Currency::class)->nullable();
            $table->foreignIdFor(\App\Models\System\Status::class)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account');
    }
};
