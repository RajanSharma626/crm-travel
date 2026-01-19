<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\Payment;
use App\Models\CostComponent;
use App\Models\Operation;
use App\Models\LeadRemark;
use App\Models\LeadHistory;
use App\Models\Document;
use App\Models\Delivery;
use App\Models\Incentive;
use App\Models\Service;
use App\Models\Destination;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = Service::all();
        $destinations = Destination::all();
        $salesUsers = \App\Models\User::whereIn('role', ['Sales', 'Sales Manager'])->get();
        $operationUsers = \App\Models\User::whereIn('role', ['Operation', 'Operation Manager'])->get();
        $accountsUsers = \App\Models\User::whereIn('role', ['Accounts', 'Accounts Manager'])->get();
        $deliveryUsers = \App\Models\User::whereIn('role', ['Delivery', 'Delivery Manager'])->get();

        if ($services->isEmpty() || $destinations->isEmpty() || $salesUsers->isEmpty()) {
            $this->command->warn('Please run ServiceSeeder, DestinationSeeder, and UserSeeder first!');
            return;
        }

        $statuses = ['new', 'contacted', 'follow_up', 'priority', 'booked', 'closed'];
        $customers = [
            ['name' => 'Ramesh Kumar', 'phone' => '9876543210', 'email' => 'ramesh@example.com'],
            ['name' => 'Sita Devi', 'phone' => '9876543211', 'email' => 'sita@example.com'],
            ['name' => 'Amit Patel', 'phone' => '9876543212', 'email' => 'amit@example.com'],
            ['name' => 'Kavita Singh', 'phone' => '9876543213', 'email' => 'kavita@example.com'],
            ['name' => 'Vikram Sharma', 'phone' => '9876543214', 'email' => 'vikram@example.com'],
            ['name' => 'Neha Gupta', 'phone' => '9876543215', 'email' => 'neha@example.com'],
            ['name' => 'Rajesh Mehta', 'phone' => '9876543216', 'email' => 'rajesh@example.com'],
            ['name' => 'Pooja Reddy', 'phone' => '9876543217', 'email' => 'pooja@example.com'],
            ['name' => 'Suresh Iyer', 'phone' => '9876543218', 'email' => 'suresh@example.com'],
            ['name' => 'Anjali Nair', 'phone' => '9876543219', 'email' => 'anjali@example.com'],
        ];

        // Create 30 leads
        for ($i = 0; $i < 30; $i++) {
            $customer = $customers[array_rand($customers)];
            $service = $services->random();
            $destination = $destinations->random();
            $salesUser = $salesUsers->random();
            $status = $statuses[array_rand($statuses)];

            $sellingPrice = rand(20000, 200000);
            $bookedValue = $status === 'booked' ? $sellingPrice * (rand(80, 100) / 100) : null;

            $nameParts = preg_split('/\s+/', $customer['name']);
            $firstName = $nameParts[0] ?? $customer['name'];
            $lastName = null;
            $middleName = null;

            if (count($nameParts) > 1) {
                $lastName = array_pop($nameParts);
                if (!empty($nameParts)) {
                    $firstName = array_shift($nameParts);
                    if (!empty($nameParts)) {
                        $middleName = implode(' ', $nameParts);
                    }
                }
            }

            // Ensure first_name is set (required for customer_name generation)
            if (empty($firstName)) {
                $firstName = $customer['name'];
            }

            // Generate customer_name from first_name and last_name
            $customerName = trim(implode(' ', array_filter([$firstName, $lastName])));
            if (empty($customerName)) {
                $customerName = $firstName;
            }

            // Address details
            $addresses = [
                ['city' => 'Mumbai', 'state' => 'Maharashtra', 'country' => 'India', 'pin_code' => '400001'],
                ['city' => 'Delhi', 'state' => 'Delhi', 'country' => 'India', 'pin_code' => '110001'],
                ['city' => 'Bangalore', 'state' => 'Karnataka', 'country' => 'India', 'pin_code' => '560001'],
                ['city' => 'Pune', 'state' => 'Maharashtra', 'country' => 'India', 'pin_code' => '411001'],
            ];
            $selectedAddress = $addresses[array_rand($addresses)];

            // Children age buckets
            $totalChildren = rand(0, 2);
            $children2_5 = $totalChildren > 0 ? rand(0, $totalChildren) : 0;
            $children6_11 = $totalChildren - $children2_5;

            // Salutations
            $salutations = ['Mr', 'Mrs', 'Ms', 'Dr', null];

            $lead = Lead::create([
                'service_id' => $service->id,
                'destination_id' => $destination->id,
                'salutation' => $salutations[array_rand($salutations)],
                'customer_name' => $customerName, // Explicitly set to ensure it's populated
                'first_name' => $firstName,
                'middle_name' => $middleName,
                'last_name' => $lastName,
                'primary_phone' => $customer['phone'],
                'phone' => $customer['phone'], // Set phone explicitly
                'email' => $customer['email'],
                'address' => $selectedAddress['city'] . ', ' . $selectedAddress['state'],
                'address_line' => 'Street ' . rand(1, 100) . ', Area Name',
                'city' => $selectedAddress['city'],
                'state' => $selectedAddress['state'],
                'country' => $selectedAddress['country'],
                'pin_code' => $selectedAddress['pin_code'],
                'travel_date' => Carbon::now()->addDays(rand(30, 180))->format('Y-m-d'),
                'adults' => rand(1, 4),
                'children' => $totalChildren,
                'children_2_5' => $children2_5,
                'children_6_11' => $children6_11,
                'infants' => rand(0, 1),
                'assigned_user_id' => $salesUser->id,
                'created_by' => $salesUser->id, // Set created_by field
                'selling_price' => $sellingPrice,
                'booked_value' => $bookedValue,
                'status' => $status,
            ]);

            // Set booked_by and booked_on if status is booked
            if ($status === 'booked') {
                $lead->booked_by = $salesUser->id;
                $lead->booked_on = Carbon::now()->subDays(rand(1, 30));
                $lead->save();
            }

            // Create lead remarks
            if (rand(0, 1)) {
                LeadRemark::create([
                    'lead_id' => $lead->id,
                    'user_id' => $salesUser->id,
                    'remark' => 'Customer showed interest in ' . $destination->name . '. Follow up required.',
                    'follow_up_at' => Carbon::now()->addDays(rand(1, 7)),
                    'visibility' => rand(0, 1) ? 'public' : 'internal',
                ]);
            }

            

           

           


           

           

            
        }

        $this->command->info('Leads and related data created successfully!');
        $this->command->info('Created: 30 leads with payments, costs, operations, documents, deliveries, and incentives');
    }
}
