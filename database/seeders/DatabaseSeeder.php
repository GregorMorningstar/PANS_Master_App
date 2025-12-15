<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Department;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Najpierw tworzymy departamenty
        Department::factory(5)->create();

        // Tworzymy 3 stałych użytkowników (barcode generowany automatycznie w modelu)
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@test.com',
            'password' => bcrypt('qwer1234'),
            'role' => 'admin',
            'department_id' => Department::inRandomOrder()->value('id'),
        ]);

        User::create([
            'name' => 'Moderator',
            'email' => 'moderator@test.com',
            'password' => bcrypt('qwer1234'),
            'role' => 'moderator',
            'department_id' => Department::inRandomOrder()->value('id'),
        ]);

        User::create([
            'name' => 'Employee',
            'email' => 'employee@test.com',
            'password' => bcrypt('qwer1234'),
            'role' => 'employee',
            'department_id' => Department::inRandomOrder()->value('id'),
        ]);

        // Tworzymy 50 losowych użytkowników z rolą employee
        User::factory(50)->create([
            'role' => 'employee'
        ]);
    }
}
