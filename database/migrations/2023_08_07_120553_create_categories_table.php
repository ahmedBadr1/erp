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
        Schema::create('acc_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('code')->nullable()->index();
            $table->boolean('credit');
            $table->foreignId('parent_id')
                ->nullable()
                ->references('id')
                ->on('acc_categories')
                ->onUpdate('cascade');
            $table->boolean('active')->default(1);
            $table->boolean('usable')->default(0);
            $table->boolean('system')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acc_categories');
    }
};
