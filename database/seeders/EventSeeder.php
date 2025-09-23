<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'title' => 'Community Cleanup Day',
                'date' => '2023-07-15',
                'time' => '09:00 AM - 01:00 PM',
                'location' => 'Central Park, New York',
                'image_url' => 'https://images.unsplash.com/photo-1618477461853-cf6ed80faba5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'description' => "Join us for a community cleanup event at Central Park. We'll provide all necessary equipment. Help us make our community cleaner and greener!",
                'is_active' => true,
            ],
            [
                'title' => 'Sustainable Living Workshop',
                'date' => '2023-08-05',
                'time' => '02:00 PM - 04:00 PM',
                'location' => 'EcoCycle Headquarters, Boston',
                'image_url' => 'https://images.unsplash.com/photo-1556155092-490a1ba16284?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'description' => 'Learn practical tips and tricks for reducing your environmental footprint. This workshop covers composting, reducing waste, and sustainable shopping practices.',
                'is_active' => true,
            ],
            [
                'title' => 'E-Waste Collection Drive',
                'date' => '2023-08-20',
                'time' => '10:00 AM - 03:00 PM',
                'location' => 'City Hall Plaza, Chicago',
                'image_url' => 'https://images.unsplash.com/photo-1605600659873-d808a13e4d2a?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'description' => 'Bring your old electronics for responsible recycling. We accept computers, phones, TVs, and more. Help keep electronic waste out of landfills!',
                'is_active' => true,
            ],
        ];

        foreach ($events as $ev) {
            Event::updateOrCreate([
                'title' => $ev['title'],
                'date' => $ev['date'],
            ], $ev);
        }
    }
}
