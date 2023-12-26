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
        Schema::create('invitations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
            $table->string('token', 64)->nullable();
            $table->timestamp('expire_at')->nullable();
            $table->foreignIdFor(\Spatie\Permission\Models\Role::class)->nullable();
            $table->foreignId('sent_by')->references('id')->on('users');
            $table->timestamp('registered_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
