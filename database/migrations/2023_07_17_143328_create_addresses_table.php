<?php

use App\Models\System\City;
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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(City::class)->nullable();
            $table->string('district');
            $table->string('street')->nullable();
            $table->string('building')->nullable();
            $table->string('floor')->nullable();
            $table->string('apartment')->nullable();
            $table->string('landmarks')->nullable();
            $table->double('longitude')->nullable();
            $table->double('latitude')->nullable();
            $table->string('postal_code')->nullable();
            $table->morphs('addressable');
            $table->foreignIdFor(\App\Models\User::class);
            $table->timestamps();            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
