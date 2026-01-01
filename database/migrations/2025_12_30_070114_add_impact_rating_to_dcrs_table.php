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
            // Only add fields that don't already exist
            if (!Schema::hasColumn('dcrs', 'impact_rating')) {
                $table->enum('impact_rating', ['Low', 'Medium', 'High'])->default('Low')->after('status');
            }
            if (!Schema::hasColumn('dcrs', 'auto_escalated')) {
                $table->boolean('auto_escalated')->default(false)->after('recommendations');
            }
            if (!Schema::hasColumn('dcrs', 'escalated_at')) {
                $table->timestamp('escalated_at')->nullable()->after('auto_escalated');
            }
            if (!Schema::hasColumn('dcrs', 'escalated_to')) {
                $table->unsignedBigInteger('escalated_to')->nullable()->after('escalated_at');
                $table->foreign('escalated_to')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dcrs', function (Blueprint $table) {
            $table->dropForeign(['escalated_to']);
            $table->dropColumn([
                'impact_rating',
                'auto_escalated',
                'escalated_at',
                'escalated_to'
            ]);
        });
    }
};
