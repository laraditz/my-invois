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
        Schema::create('myinvois_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('action')->nullable();
            $table->text('url')->nullable();
            $table->json('payload')->nullable();
            $table->unsignedSmallInteger('http_code')->nullable();
            $table->json('response')->nullable();
            $table->string('correlation_id')->nullable();
            $table->string('error')->nullable();
            $table->string('error_code')->nullable();
            $table->string('error_message')->nullable();
            $table->string('error_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('myinvois_requests');
    }
};
