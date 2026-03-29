<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 100)->unique();
            $table->string('email', 150)->unique();
            $table->string('password', 255);
            $table->enum('role', ['admin', 'user'])->default('user');
            $table->string('avatar', 500)->nullable()->default(null);
            $table->timestamp('email_verified_at')->nullable()->default(null);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
        });

        // Seed admin (pre-verified, password: jesselzapanta)
        DB::table('users')->insertOrIgnore([
            'username'          => 'jesselzapanta',
            'email'             => 'jesselzapanta@gmail.com',
            'password'          => '$2b$10$2Y/vKNqzuoXYNofyNQpIkeaUCCw7aJZ2wqZEMjfI78rUN8Z4LbvHm',
            'role'              => 'admin',
            'email_verified_at' => now(),
            'created_at'        => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
