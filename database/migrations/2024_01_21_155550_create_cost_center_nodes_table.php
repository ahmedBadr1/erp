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
        Schema::create('cost_center_nodes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->index();//->nullable()
            $table->string('code')->unique()->index();//->nullable()->index();
            $table->foreignId('parent_id')
                ->nullable()
                ->references('id')
                ->on('cost_center_nodes')
                ->onUpdate('cascade');
            $table->boolean('active')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_center_nodes');
    }
};
