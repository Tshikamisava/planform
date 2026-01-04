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
        Schema::table('dcrs', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->text('implementation_notes')->nullable();
            $table->json('completed_attachments')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dcrs', function (Blueprint $table) {
            $table->dropColumn(['completed_at', 'closed_at', 'is_locked', 'implementation_notes', 'completed_attachments']);
        });
    }
};
