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
        Schema::create('error_logs', function (Blueprint $table) {
            $table->id();
            $table->string('level')->default('error'); // error, warning, info
            $table->text('message');                   // Error message
            $table->text('file')->nullable();          // File where error occurred
            $table->integer('line')->nullable();       // Line number
            $table->text('trace')->nullable();         // Stack trace
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('error_logs');
    }
};
