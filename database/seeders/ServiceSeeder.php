<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Air Ticket'
            ],
            [
                'name' => 'Hotel(s)'
            ],
            [
                'name' => 'Tour Package(s)'
            ],
            [
                'name' => 'Activities'
            ],
            [
                'name' => 'Pilgrimage Tours'
            ],
            [
                'name' => 'Transport'
            ],
            [
                'name' => 'Bus'
            ],
            [
                'name' => 'Cruise'
            ],
            [
                'name' => 'Train'
            ],
            [
                'name' => 'Visa'
            ],
            [
                'name' => 'Insurance'
            ]

        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
