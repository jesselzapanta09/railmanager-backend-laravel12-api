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

        // Seed trains
        DB::table('trains')->insert([
            ['train_name' => 'LRT Line 1',                   'price' => 20.00,  'route' => 'Baclaran - Fernando Poe Jr. Station'],
            ['train_name' => 'LRT Line 2',                   'price' => 25.00,  'route' => 'Recto - Antipolo'],
            ['train_name' => 'MRT Line 3',                   'price' => 24.00,  'route' => 'North Avenue - Taft Avenue'],
            ['train_name' => 'PNR Metro Commuter Line',       'price' => 30.00,  'route' => 'Tutuban - Alabang'],
            ['train_name' => 'PNR Bicol Express',             'price' => 450.00, 'route' => 'Manila - Naga'],
            ['train_name' => 'PNR Mayon Limited',             'price' => 500.00, 'route' => 'Manila - Legazpi'],
            ['train_name' => 'LRT Cavite Extension',          'price' => 35.00,  'route' => 'Baclaran - Niog'],
            ['train_name' => 'MRT Line 7',                   'price' => 28.00,  'route' => 'North Avenue - San Jose del Monte'],
            ['train_name' => 'North–South Commuter Railway',  'price' => 60.00,  'route' => 'Clark - Calamba'],
            ['train_name' => 'Metro Manila Subway',           'price' => 35.00,  'route' => 'Valenzuela - NAIA Terminal 3'],
            ['train_name' => 'PNR South Long Haul',           'price' => 800.00, 'route' => 'Manila - Matnog'],
            ['train_name' => 'Clark Airport Express',         'price' => 120.00, 'route' => 'Clark Airport - Manila'],
            ['train_name' => 'Mindanao Railway Phase 1',      'price' => 50.00,  'route' => 'Tagum - Davao - Digos'],
            ['train_name' => 'Panay Rail Revival',            'price' => 40.00,  'route' => 'Iloilo - Roxas City'],
            ['train_name' => 'Cebu Monorail',                 'price' => 25.00,  'route' => 'Cebu City - Mactan Airport'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('trains');
    }
};
