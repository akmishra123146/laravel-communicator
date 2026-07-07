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
        $tableName = config('communication.otp.table', 'communication_otps');

        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('identifier')->index();
                $table->string('token');
                $table->integer('attempt_count')->default(0);
                $table->boolean('verified')->default(false);
                $table->timestamp('expires_at');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableName = config('communication.otp.table', 'communication_otps');
        Schema::dropIfExists($tableName);
    }
};
