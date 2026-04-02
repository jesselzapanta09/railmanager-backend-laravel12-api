<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trains', function (Blueprint $table) {
            $table->id();
            $table->string('train_name', 150);
            $table->decimal('price', 10, 2);
            $table->string('route', 255);
            $table->string('image', 500)->nullable()->default(null);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        DB::table('trains')->insertOrIgnore([
            [
                'id' => 1,
                'train_name' => 'LRT Line 1',
                'price' => 20.00,
                'route' => 'Baclaran - Fernando Poe Jr. Station',
                'image' => '/uploads/trains/train-1774789654.jpg',
                'created_at' => '2026-03-29 07:34:59',
                'updated_at' => '2026-03-29 05:07:34',
            ],
            [
                'id' => 2,
                'train_name' => 'LRT Line 2',
                'price' => 25.00,
                'route' => 'Recto - Antipolo',
                'image' => '/uploads/trains/train-1774789640.jpg',
                'created_at' => '2026-03-29 07:34:59',
                'updated_at' => '2026-03-29 05:07:20',
            ],
            [
                'id' => 3,
                'train_name' => 'MRT Line 3',
                'price' => 24.00,
                'route' => 'North Avenue - Taft Avenue',
                'image' => '/uploads/trains/train-1774789631.jpg',
                'created_at' => '2026-03-29 07:34:59',
                'updated_at' => '2026-03-29 05:07:11',
            ],
            [
                'id' => 4,
                'train_name' => 'PNR Metro Commuter Line',
                'price' => 30.00,
                'route' => 'Tutuban - Alabang',
                'image' => '/uploads/trains/train-1774789626.jpg',
                'created_at' => '2026-03-29 07:34:59',
                'updated_at' => '2026-03-29 05:07:06',
            ],
            [
                'id' => 5,
                'train_name' => 'PNR Bicol Express',
                'price' => 450.00,
                'route' => 'Manila - Naga',
                'image' => '/uploads/trains/train-1774789619.jpg',
                'created_at' => '2026-03-29 07:34:59',
                'updated_at' => '2026-03-29 05:06:59',
            ],
            [
                'id' => 6,
                'train_name' => 'PNR Mayon Limited',
                'price' => 500.00,
                'route' => 'Manila - Legazpi',
                'image' => '/uploads/trains/train-1774789613.jpg',
                'created_at' => '2026-03-29 07:34:59',
                'updated_at' => '2026-03-29 05:06:53',
            ],
            [
                'id' => 7,
                'train_name' => 'LRT Cavite Extension',
                'price' => 35.00,
                'route' => 'Baclaran - Niog',
                'image' => '/uploads/trains/train-1774789608.jpg',
                'created_at' => '2026-03-29 07:34:59',
                'updated_at' => '2026-03-29 05:06:48',
            ],
            [
                'id' => 8,
                'train_name' => 'MRT Line 7',
                'price' => 28.00,
                'route' => 'North Avenue - San Jose del Monte',
                'image' => '/uploads/trains/train-1774789596.jpg',
                'created_at' => '2026-03-29 07:34:59',
                'updated_at' => '2026-03-29 05:06:36',
            ],
            [
                'id' => 9,
                'train_name' => 'North–South Commuter Railway',
                'price' => 60.00,
                'route' => 'Clark - Calamba',
                'image' => '/uploads/trains/train-1774789589.jpg',
                'created_at' => '2026-03-29 07:34:59',
                'updated_at' => '2026-03-29 05:06:29',
            ],
            [
                'id' => 10,
                'train_name' => 'Metro Manila Subway',
                'price' => 35.00,
                'route' => 'Valenzuela - NAIA Terminal 3',
                'image' => '/uploads/trains/train-1774789585.jpg',
                'created_at' => '2026-03-29 07:34:59',
                'updated_at' => '2026-03-29 05:06:25',
            ],
            [
                'id' => 11,
                'train_name' => 'PNR South Long Haul',
                'price' => 800.00,
                'route' => 'Manila - Matnog',
                'image' => '/uploads/trains/train-1774789577.jpg',
                'created_at' => '2026-03-29 07:34:59',
                'updated_at' => '2026-03-29 05:06:17',
            ],
            [
                'id' => 12,
                'train_name' => 'Clark Airport Express',
                'price' => 120.00,
                'route' => 'Clark Airport - Manila',
                'image' => '/uploads/trains/train-1774789572.jpg',
                'created_at' => '2026-03-29 07:34:59',
                'updated_at' => '2026-03-29 05:06:12',
            ],
            [
                'id' => 13,
                'train_name' => 'Mindanao Railway Phase 1',
                'price' => 50.00,
                'route' => 'Tagum - Davao - Digos',
                'image' => '/uploads/trains/train-1774789568.jpg',
                'created_at' => '2026-03-29 07:34:59',
                'updated_at' => '2026-03-29 05:06:08',
            ],
            [
                'id' => 14,
                'train_name' => 'Panay Rail Revival',
                'price' => 40.00,
                'route' => 'Iloilo - Roxas City',
                'image' => '/uploads/trains/train-1774789563.jpg',
                'created_at' => '2026-03-29 07:34:59',
                'updated_at' => '2026-03-29 05:06:03',
            ],
            [
                'id' => 15,
                'train_name' => 'Cebu Monorail',
                'price' => 25.00,
                'route' => 'Cebu City - Mactan Airport',
                'image' => '/uploads/trains/train-1774789559.jpg',
                'created_at' => '2026-03-29 07:34:59',
                'updated_at' => '2026-03-29 05:05:59',
            ],
        ]);

        // Fix auto-increment (optional but smart)
        DB::statement('ALTER TABLE trains AUTO_INCREMENT = 16;');
    }

    public function down(): void
    {
        Schema::dropIfExists('trains');
    }
};
