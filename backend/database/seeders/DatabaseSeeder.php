<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            LocationSeeder::class,
            PropertySeeder::class,
        ]);
        
        $this->command->info('✅ تم إضافة جميع البيانات التجريبية بنجاح');
    }
}
