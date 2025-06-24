<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laraditz\MyInvois\Models\MyinvoisClient;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('myinvois_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('client_id', 50);
            $table->text('access_token')->nullable();
            $table->datetime('expires_at')->nullable();
            $table->string('type', 20)->nullable();
            $table->text('scopes')->nullable();
            $table->timestamps();

            $table->index('client_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('myinvois_access_tokens');
    }
};
