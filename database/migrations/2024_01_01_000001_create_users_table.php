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

        DB::table('users')->insertOrIgnore([
            [
                'id' => 1,
                'username' => 'admin',
                'email' => 'jesselzapanta@gmail.com',
                'password' => '$2b$10$2Y/vKNqzuoXYNofyNQpIkeaUCCw7aJZ2wqZEMjfI78rUN8Z4LbvHm',
                'role' => 'admin',
                'avatar' => '/uploads/avatars/avatar-1774789375.jpg',
                'email_verified_at' => '2026-03-28 23:34:59',
                'created_at' => '2026-03-28 23:34:59',
                'updated_at' => '2026-03-29 05:02:55',
            ],
            [
                'id' => 3,
                'username' => 'useraccount1234',
                'email' => 'useraccount1234@gmail.com',
                'password' => '$2y$10$qgLlILzqH/qX/7IOk7BI3.MfoJ.grKdnN9.Zac.j19/IRbrTr5V.q',
                'role' => 'admin',
                'avatar' => '/uploads/avatars/avatar-1774789666.png',
                'email_verified_at' => null,
                'created_at' => '2026-03-29 05:03:26',
                'updated_at' => '2026-03-29 05:07:46',
            ],
            [
                'id' => 4,
                'username' => 'Eren Yeager',
                'email' => 'erenyeager@gmail.com',
                'password' => '$2y$10$DkSjQe.AXEiRPgImzhHDwOvHzYbaDh6RwP2.4Jw7/QNkuF.nYlcGG',
                'role' => 'user',
                'avatar' => '/uploads/avatars/avatar-1774789447.png',
                'email_verified_at' => null,
                'created_at' => '2026-03-29 05:04:07',
                'updated_at' => '2026-03-29 05:04:07',
            ],
            [
                'id' => 5,
                'username' => 'raidenshogun',
                'email' => 'raidenshogun@gmail.com',
                'password' => '$2y$10$GavW0j2GPZXxEyHS35TOLeRgsN/y61afG5lAeGjrj5aduB75Lc6te',
                'role' => 'admin',
                'avatar' => '/uploads/avatars/avatar-1774789529.jpg',
                'email_verified_at' => null,
                'created_at' => '2026-03-29 05:05:15',
                'updated_at' => '2026-03-29 05:05:29',
            ],
            [
                'id' => 6,
                'username' => 'jeszapanta1211',
                'email' => 'jeszapanta1211@gmail.com',
                'password' => '$2y$10$185Bgs0DOetbCPaUuON/K.iZcKBxy8KvLlwyOerwC1Tk9nT76RZ/6',
                'role' => 'admin',
                'avatar' => null,
                'email_verified_at' => '2026-03-29 05:14:59',
                'created_at' => '2026-03-29 05:14:42',
                'updated_at' => '2026-03-29 05:16:03',
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
