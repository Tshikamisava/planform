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
            if (!Schema::hasColumn('dcrs', 'uuid')) {
                $table->uuid('uuid')->nullable()->after('id');
            }
            if (!Schema::hasColumn('dcrs', 'title')) {
                $table->string('title', 255)->nullable()->after('dcr_id');
            }
            if (!Schema::hasColumn('dcrs', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
        });
        
        // Update existing records with UUIDs
        DB::statement('UPDATE dcrs SET uuid = UUID() WHERE uuid IS NULL');
        
        // Make uuid unique after populating (if not already unique)
        $indexes = Schema::getIndexListing('dcrs');
        if (!in_array('dcrs_uuid_unique', $indexes)) {
            Schema::table('dcrs', function (Blueprint $table) {
                $table->unique('uuid');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dcrs', function (Blueprint $table) {
            $table->dropUnique(['uuid']);
            $table->dropColumn(['uuid', 'title', 'description']);
        });
    }
};
