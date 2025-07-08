<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Laraditz\MyInvois\Models\MyinvoisClient;
use Illuminate\Database\Migrations\Migration;
use Laraditz\MyInvois\Models\MyinvoisRequest;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('myinvois_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(MyinvoisClient::class, 'client_id');
            $table->foreignIdFor(MyinvoisRequest::class, 'request_id');
            $table->string('code_number');
            $table->string('type')->nullable();
            $table->string('format', 10);
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->string('disk', 20)->nullable();
            $table->string('hash')->nullable();
            $table->string('submission_uid')->nullable();
            $table->string('uuid')->nullable();
            $table->json('error')->nullable();
            $table->string('error_code', 10)->nullable();
            $table->string('error_message')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();

            $table->index('client_id');
            $table->index('request_id');
            $table->index('code_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('myinvois_documents');
    }
};
