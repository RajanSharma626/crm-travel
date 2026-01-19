<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Helper to create user with details
        $createUser = function ($name, $email, $role, $dept, $designation) {
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'user_id' => strtolower(str_replace(' ', '', $name)) . '001', // Simple ID generation
                    'role' => $role,
                    'department' => $dept,
                    'designation' => $designation,
                    'employment_status' => 'Active',
                    'employment_type' => 'Permanent',
                    'doj' => now(),
                    'password' => Hash::make('password123'),
                    'status' => 'Active',
                ]
            );

            // Refresh to ensure we have the latest data
            $user->refresh();

            // Create related records (placeholders for now) - use updateOrCreate to avoid duplicates
            if (!$user->empBasicInfo) {
            $user->empBasicInfo()->create([
                'present_address' => '123 Main St, City',
                'emergency_contact' => '9876543210'
            ]);
            } else {
                $user->empBasicInfo()->update([
                    'present_address' => '123 Main St, City',
                    'emergency_contact' => '9876543210'
                ]);
            }
            
            if (!$user->incentivePerformance) {
            $user->incentivePerformance()->create([
                'incentive_eligibility' => true,
                'incentive_type' => 'fixed',
                'monthly_target' => '100000'
            ]);
            } else {
                $user->incentivePerformance()->update([
                    'incentive_eligibility' => true,
                    'incentive_type' => 'fixed',
                    'monthly_target' => '100000'
                ]);
            }

            if (!$user->statutoryPayrollDetails) {
            $user->statutoryPayrollDetails()->create([
                'salary_structure' => 'CTC',
                'bank_name' => 'Demo Bank'
            ]);
            } else {
                $user->statutoryPayrollDetails()->update([
                    'salary_structure' => 'CTC',
                    'bank_name' => 'Demo Bank'
                ]);
            }

            if (!$user->exitClearance) {
            $user->exitClearance()->create([]);
            }
            
            // Assign role (only if role exists)
            try {
                if (!$user->hasRole($role)) {
            $user->assignRole($role);
                }
            } catch (\Exception $e) {
                // Role might not exist, that's okay
            }
            return $user;
        };

        // Admin
        $createUser('Admin User', 'admin@travelshravel.com', 'Admin', 'Management', 'Administrator');

        // Sales Users
        $createUser('Sales Manager', 'salesmanager@travelshravel.com', 'Sales Manager', 'Sales', 'Sales Manager');
        $createUser('Sales User 1', 'sales1@travelshravel.com', 'Sales', 'Sales', 'Sales Executive');
        $createUser('Sales User 2', 'sales2@travelshravel.com', 'Sales', 'Sales', 'Sales Executive');

        // Operation Users
        $createUser('Operation Manager', 'opsmanager@travelshravel.com', 'Operation Manager', 'Operation', 'Operation Manager');
        $createUser('Operation User 1', 'ops1@travelshravel.com', 'Operation', 'Operation', 'Operation Executive');

        // Accounts Users
        $createUser('Accounts Manager', 'accountsmanager@travelshravel.com', 'Accounts Manager', 'Accounts', 'Accounts Manager');
        $createUser('Accounts User 1', 'accounts1@travelshravel.com', 'Accounts', 'Accounts', 'Accounts Executive');

        // Delivery Users
        $createUser('Delivery Manager', 'deliverymanager@travelshravel.com', 'Delivery Manager', 'Delivery', 'Delivery Manager');
        $createUser('Delivery User 1', 'delivery1@travelshravel.com', 'Delivery', 'Delivery', 'Delivery Executive');

        // Post Sales Users
        $createUser('Post Sales Manager', 'postsalesmanager@travelshravel.com', 'Post Sales Manager', 'Post Sales', 'Post Sales Manager');
        $createUser('Post Sales User 1', 'postsales1@travelshravel.com', 'Post Sales', 'Post Sales', 'Post Sales Executive');

        $this->command->info('Users and profiles created successfully!');
        $this->command->info('Default password for all users: password123');
    }
}
