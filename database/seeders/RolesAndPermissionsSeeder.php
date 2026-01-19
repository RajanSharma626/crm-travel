<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Employee;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions
        $permissions = [
            // Leads
            'view leads',
            'create leads',
            'edit leads',
            'delete leads',
            'assign leads',
            'update lead status',
            
            // Payments
            'view payments',
            'create payments',
            'edit payments',
            'delete payments',
            
            // Cost Components
            'view costs',
            'create costs',
            'edit costs',
            'delete costs',
            
            // Operations
            'view operations',
            'create operations',
            'edit operations',
            'approve operations',
            
            // Documents
            'view documents',
            'upload documents',
            'delete documents',
            'verify documents',
            
            // Deliveries
            'view deliveries',
            'assign deliveries',
            'update deliveries',
            
            // Remarks
            'view remarks',
            'create remarks',
            'edit remarks',
            'delete remarks',
            
            // Reports
            'view reports',
            'export reports',
            
            // Incentives
            'view incentives',
            'calculate incentives',
            'approve incentives',
            'mark incentives paid',
            
            // Incentive Rules
            'view incentive rules',
            'create incentive rules',
            'edit incentive rules',
            'delete incentive rules',
            
            // Services
            'view services',
            'create services',
            'edit services',
            'delete services',
            
            // Destinations
            'view destinations',
            'create destinations',
            'edit destinations',
            'delete destinations',
            
            // Users
            'view users',
            'create users',
            'edit users',
            'delete users',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Admin - Full access
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Sales Manager
        $salesManagerRole = Role::firstOrCreate(['name' => 'Sales Manager']);
        $salesManagerRole->givePermissionTo([
            'view leads',
            'create leads',
            'edit leads',
            'assign leads',
            'update lead status',
            'view payments',
            'create payments',
            'edit payments',
            'view costs',
            'view operations',
            'view documents',
            'upload documents',
            'view deliveries',
            'view remarks',
            'create remarks',
            'edit remarks',
            'view reports',
            'export reports',
            'view incentives',
            'calculate incentives',
            'view services',
            'view destinations',
        ]);

        // Sales
        $salesRole = Role::firstOrCreate(['name' => 'Sales']);
        $salesRole->givePermissionTo([
            'view leads',
            'create leads',
            'edit leads',
            'update lead status',
            'view payments',
            'create payments',
            'view costs',
            'view operations',
            'view documents',
            'upload documents',
            'view deliveries',
            'view remarks',
            'create remarks',
            'edit remarks',
            'view reports',
            'view incentives',
            'view services',
            'view destinations',
        ]);

        // Operation Manager
        $operationManagerRole = Role::firstOrCreate(['name' => 'Operation Manager']);
        $operationManagerRole->givePermissionTo([
            'view leads',
            'edit leads',
            'view payments',
            'view costs',
            'create costs',
            'edit costs',
            'view operations',
            'create operations',
            'edit operations',
            'approve operations',
            'view documents',
            'upload documents',
            'verify documents',
            'view deliveries',
            'assign deliveries',
            'update deliveries',
            'view remarks',
            'create remarks',
            'view reports',
            'view services',
            'view destinations',
        ]);

        // Operation
        $operationRole = Role::firstOrCreate(['name' => 'Operation']);
        $operationRole->givePermissionTo([
            'view leads',
            'view payments',
            'view costs',
            'create costs',
            'edit costs',
            'view operations',
            'create operations',
            'edit operations',
            'view documents',
            'upload documents',
            'view deliveries',
            'update deliveries',
            'view remarks',
            'create remarks',
            'view services',
            'view destinations',
        ]);

        // Accounts Manager
        $accountsManagerRole = Role::firstOrCreate(['name' => 'Accounts Manager']);
        $accountsManagerRole->givePermissionTo([
            'view leads',
            'view payments',
            'create payments',
            'edit payments',
            'delete payments',
            'view costs',
            'create costs',
            'edit costs',
            'delete costs',
            'view operations',
            'view documents',
            'view deliveries',
            'view remarks',
            'view reports',
            'export reports',
            'view services',
            'view destinations',
        ]);

        // Accounts
        $accountsRole = Role::firstOrCreate(['name' => 'Accounts']);
        $accountsRole->givePermissionTo([
            'view leads',
            'view payments',
            'create payments',
            'edit payments',
            'view costs',
            'create costs',
            'edit costs',
            'view operations',
            'view documents',
            'view remarks',
            'view reports',
            'view services',
            'view destinations',
        ]);

        // Post Sales Manager
        $postSalesManagerRole = Role::firstOrCreate(['name' => 'Post Sales Manager']);
        $postSalesManagerRole->givePermissionTo([
            'view leads',
            'view payments',
            'view costs',
            'view operations',
            'view documents',
            'upload documents',
            'delete documents',
            'verify documents',
            'view deliveries',
            'assign deliveries',
            'update deliveries',
            'view remarks',
            'create remarks',
            'view services',
            'view destinations',
        ]);

        // Post Sales
        $postSalesRole = Role::firstOrCreate(['name' => 'Post Sales']);
        $postSalesRole->givePermissionTo([
            'view leads',
            'view documents',
            'upload documents',
            'view deliveries',
            'update deliveries',
            'view remarks',
            'create remarks',
            'view services',
            'view destinations',
        ]);

        // Delivery Manager
        $deliveryManagerRole = Role::firstOrCreate(['name' => 'Delivery Manager']);
        $deliveryManagerRole->givePermissionTo([
            'view leads',
            'view documents',
            'view deliveries',
            'assign deliveries',
            'update deliveries',
            'view remarks',
            'view services',
            'view destinations',
        ]);

        // Delivery
        $deliveryRole = Role::firstOrCreate(['name' => 'Delivery']);
        $deliveryRole->givePermissionTo([
            'view leads',
            'view documents',
            'view deliveries',
            'update deliveries',
            'view services',
            'view destinations',
        ]);

        // HR
        $hrRole = Role::firstOrCreate(['name' => 'HR']);
        $hrRole->givePermissionTo([
            'view leads',
            'view reports',
            'view users',
            'create users',
            'edit users',
        ]);

        // Customer Care
        $customerCareRole = Role::firstOrCreate(['name' => 'Customer Care']);
        $customerCareRole->givePermissionTo([
            'view leads',
            'create leads',
            'edit leads',
            'view remarks',
            'create remarks',
            'view reports',
            'view services',
            'view destinations',
        ]);

        // Developer - Full access (for development)
        $developerRole = Role::firstOrCreate(['name' => 'Developer']);
        $developerRole->givePermissionTo(Permission::all());

        // Assign roles to existing users based on their role field
        $users = \App\Models\User::all();
        foreach ($users as $user) {
            if ($user->role) {
                // Fix role name mismatch if any (e.g. 'Sales' vs 'Sales Manager' logic is handled by exact match)
                $role = Role::where('name', $user->role)->first();
                if ($role && !$user->hasRole($user->role)) {
                    $user->assignRole($role);
                }
            }
        }

        $this->command->info('Roles and permissions created successfully!');
    }
}
