<?php

namespace Database\Seeders;

use App\Models\Destination;
use Illuminate\Database\Seeder;

class DestinationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $destinations = [
            // Domestic
            [
                'name' => 'Goa',
                'country' => 'India',
            ],
            [
                'name' => 'Kerala',
                'country' => 'India',
            ],
            [
                'name' => 'Manali',
                'country' => 'India',
            ],
            [
                'name' => 'Rajasthan',
                'country' => 'India',
            ],
            [
                'name' => 'Kashmir',
                'country' => 'India',
            ],
            [
                'name' => 'Darjeeling',
                'country' => 'India',
            ],
            // International
            [
                'name' => 'Dubai',
                'country' => 'UAE',
            ],
            [
                'name' => 'Bangkok',
                'country' => 'Thailand',
            ],
            [
                'name' => 'Singapore',
                'country' => 'Singapore',
            ],
            [
                'name' => 'Maldives',
                'country' => 'Maldives',
            ],
            [
                'name' => 'Bali',
                'country' => 'Indonesia',
            ],
            [
                'name' => 'Switzerland',
                'country' => 'Switzerland',
            ],
            [
                'name' => 'Paris',
                'country' => 'France',
            ],
            [
                'name' => 'London',
                'country' => 'United Kingdom',
            ],
            [
                'name' => 'Tokyo',
                'country' => 'Japan',
            ],
        ];

        foreach ($destinations as $destination) {
            Destination::create($destination);
        }
    }
}
