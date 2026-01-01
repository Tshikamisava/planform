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
        Schema::create('dcrs', function (Blueprint $table) {
            $table->id();
            $table->string('dcr_id')->unique();
            $table->foreignId('author_id')->constrained('users');
            $table->foreignId('recipient_id')->constrained('users');
            $table->string('request_type');
            $table->text('reason_for_change');
            $table->date('due_date');
            $table->string('status')->default('pending');
            $table->enum('impact', ['Low', 'Medium', 'High'])->nullable();
            $table->text('impact_summary')->nullable();
            $table->text('recommendations')->nullable();
            $table->foreignId('decision_maker_id')->nullable()->constrained('users');
            $table->json('attachments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dcrs');
    }
};
