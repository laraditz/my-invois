<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('myinvois_msic_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 7);
            $table->string('description')->nullable();
            $table->string('category', 3)->nullable(); // Default
            $table->timestamps();

            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('myinvois_msic_codes');
    }
};
