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
            $table->enum('closure_status', ['Open', 'Pending_Closure', 'Closed'])->default('Open')->after('status');
            $table->boolean('is_locked')->default(false)->after('closure_status');
            $table->boolean('is_archived')->default(false)->after('is_locked');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('change_requests', function (Blueprint $table) {
            $table->dropColumn(['closure_status', 'is_locked', 'is_archived']);
        });
    }
};
