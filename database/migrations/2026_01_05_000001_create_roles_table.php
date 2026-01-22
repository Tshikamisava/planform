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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->char('uuid', 36)->unique();
            $table->string('name', 50)->unique();
            $table->string('display_name', 100);
            $table->text('description')->nullable();
            $table->tinyInteger('level')->unsigned()->default(0);
            $table->boolean('is_system')->default(false);
            $table->timestamps();

            // Indexes
            $table->index('name');
            $table->index('level');
        });

        // Insert default roles
        DB::table('roles')->insert([
            [
                'uuid' => Str::uuid(),
                'name' => 'author',
                'display_name' => 'Author',
                'description' => 'Can create and submit change requests',
                'level' => 1,
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'recipient',
                'display_name' => 'Recipient',
                'description' => 'Can execute approved changes',
                'level' => 2,
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'dom',
                'display_name' => 'Decision Maker',
                'description' => 'Can assess impact and approve/reject changes',
                'level' => 3,
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'viewer',
                'display_name' => 'Viewer',
                'description' => 'Can view change requests (read-only access)',
                'level' => 1,
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Full system access and user management',
                'level' => 4,
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
