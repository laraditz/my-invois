<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Laraditz\MyInvois\Models\MyinvoisClient;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('myinvois_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignIdFor(MyinvoisClient::class, 'client_id');
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

            $table->index('client_id');
            $table->index('action');
            $table->index('http_code');
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
