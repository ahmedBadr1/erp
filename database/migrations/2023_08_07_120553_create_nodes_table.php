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
        Schema::create('nodes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');//->nullable()->index();
            $table->string('code')->unique()->index();//->nullable()->index();
            $table->boolean('credit');
            $table->foreignId('parent_id')
                ->nullable()
                ->references('id')
                ->on('nodes')
                ->onUpdate('cascade');
            $table->boolean('active')->default(1);
            $table->boolean('usable')->default(0);
            $table->boolean('system')->default(0);
            $table->foreignIdFor(\App\Models\Accounting\AccountType::class);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nodes');
    }
};
