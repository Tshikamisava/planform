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
        Schema::create('drc_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('drc_id')->unique();
            $table->date('submission_date');
            $table->string('author');
            $table->string('recipient');
            $table->text('details');
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drc_submissions');
    }
};
