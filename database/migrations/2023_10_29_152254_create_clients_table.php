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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('code');
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('type')->nullable();
            $table->string('image')->nullable();
            $table->decimal('credit_limit',15)->nullable();
            $table->foreignIdFor(\App\Models\System\Status::class);
            $table->dateTime('last_action_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
