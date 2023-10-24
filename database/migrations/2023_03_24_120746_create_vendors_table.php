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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('business_name');
            $table->string('name');
            $table->string('code');
            $table->foreignIdFor(\App\Models\Accounting\Account::class);
            $table->foreignId('responsible_id')
                ->references('id')
                ->on('employees')
                ->onUpdate('cascade')
                ->nullable();
            $table->decimal('credit_limit',10)->nullable();
            $table->string('warranty')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('phone')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->string('website');
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
        Schema::dropIfExists('vendors');
    }
};
