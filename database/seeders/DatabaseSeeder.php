<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Seeding database...');
        
        // First, seed roles and permissions
        $this->call(RolesAndPermissionsSeeder::class);
        
        // Then seed services and destinations
        $this->call(ServiceSeeder::class);
        $this->call(DestinationSeeder::class);
        
        // Seed users (will assign roles)
        $this->call(UserSeeder::class);
        
        // Seed employees
        $this->call(EmployeeSeeder::class);
        
        // Seed incentive rules
        $this->call(IncentiveRuleSeeder::class);
        
        // Finally, seed leads with all related data
        $this->call(LeadSeeder::class);
        
        $this->command->info('Database seeding completed successfully!');
    }
}
