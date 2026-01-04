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
        Schema::table('users', function (Blueprint $table) {
            // Add new fields only if they don't exist
            if (!Schema::hasColumn('users', 'uuid')) {
                $table->char('uuid', 36)->unique()->after('id');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department', 100)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'position')) {
                $table->string('position', 100)->nullable()->after('department');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('position');
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('is_active');
            }

            // Drop the old role column only if it exists and user_roles table exists
            if (Schema::hasColumn('users', 'role') && Schema::hasTable('user_roles')) {
                $table->dropColumn('role');
            }
        });

        // Add indexes only if they don't exist
        Schema::table('users', function (Blueprint $table) {
            $indexes = Schema::getIndexListing('users');
            
            if (!in_array('users_uuid_index', $indexes)) {
                $table->index('uuid');
            }
            if (!in_array('users_is_active_index', $indexes)) {
                $table->index('is_active');
            }
            if (!in_array('users_department_index', $indexes)) {
                $table->index('department');
            }
        });

        // Add constraints (MySQL 8.0+ syntax for CHECK constraints)
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE users ADD CONSTRAINT chk_email_format CHECK (email REGEXP \'^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\\\\.[A-Za-z]{2,}$\')');
            DB::statement('ALTER TABLE users ADD CONSTRAINT chk_phone_format CHECK (phone IS NULL OR phone REGEXP \'^[0-9+\\\\-\\\\s()]+$\')');
        }

        // Update existing users with UUIDs
        DB::table('users')->whereNull('uuid')->update([
            'uuid' => DB::raw('(SELECT UUID())')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop constraints
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS chk_email_format');
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS chk_phone_format');
        }

        Schema::table('users', function (Blueprint $table) {
            // Add back the old role column
            $table->enum('role', ['Author', 'Recipient', 'DOM', 'Admin'])->default('Author')->after('password');

            // Drop new columns
            $table->dropColumn([
                'uuid',
                'phone', 
                'department',
                'position',
                'is_active',
                'last_login_at'
            ]);

            // Drop indexes
            $table->dropIndex(['uuid', 'is_active', 'department']);
        });
    }
};
