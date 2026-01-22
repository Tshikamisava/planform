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
            // Impact analysis fields from create form
            $table->decimal('cost', 15, 2)->nullable()->after('impact_summary');
            $table->decimal('weight', 10, 3)->nullable()->after('cost');
            $table->boolean('tooling')->default(false)->after('weight');
            $table->text('tooling_desc')->nullable()->after('tooling');
            $table->boolean('inventory_scrap')->default(false)->after('tooling_desc');
            $table->json('parts')->nullable()->after('inventory_scrap');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('change_requests', function (Blueprint $table) {
            $table->dropColumn(['cost', 'weight', 'tooling', 'tooling_desc', 'inventory_scrap', 'parts']);
        });
    }
};
