<?php

namespace Database\Seeders;

use App\Models\QuarterYear;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'role' => 'admin-1',
            'phone_number' => '0811223344',
            'password' => Hash::make('admininventory2024')
        ]);

        QuarterYear::factory()->create([
            'created_by' => 1,
            'year' => '2024',
            'start_tw_1' => '2024-01-01',
            'end_tw_1' => '2024-03-31',
            'start_tw_2' => '2024-04-01',
            'end_tw_2' => '2024-06-30',
            'start_tw_3' => '2024-07-01',
            'end_tw_3' => '2024-09-30',
            'start_tw_4' => '2024-10-01',
            'end_tw_4' => '2024-12-31',
        ]);
    }
}
