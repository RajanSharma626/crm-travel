<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Functionality moved to UserSeeder to create Employees directly as the main users.
        $this->command->info('EmployeeSeeder is now empty (consolidated into UserSeeder).');
    }
}
