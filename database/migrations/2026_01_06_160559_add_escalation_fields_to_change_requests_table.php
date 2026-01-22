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
        Schema::table('change_requests', function (Blueprint $table) {
            $table->boolean('auto_escalated')->default(false)->after('status');
            $table->timestamp('escalated_at')->nullable()->after('auto_escalated');
            $table->index('auto_escalated');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('change_requests', function (Blueprint $table) {
            $table->dropIndex(['auto_escalated']);
            $table->dropColumn(['auto_escalated', 'escalated_at']);
        });
    }
};
