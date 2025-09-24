<?php

namespace Database\Seeders;

use App\Models\CommunityPost;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommunityPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CommunityPost::create([
    'title' => 'AI-Generated Art Frame',
    'description' => 'A digital frame that displays AI-generated artwork based on time of day or weather.',
    'images' => json_encode(['art-frame.jpg']),
    'author_name' => 'Nariman',
    'author_avatar' => 'nariman.jpg',
    'author_username' => '@nariman',
    'tags' => json_encode(['art', 'frame', 'ai'])
]);

CommunityPost::create([
    'title' => 'Smart Plant Monitor',
    'description' => 'A DIY device that monitors soil moisture and sends alerts when your plant needs watering.',
    'images' => json_encode(['plant-monitor.jpg']),
    'author_name' => 'Youssef',
    'author_avatar' => 'youssef.jpg',
    'author_username' => '@youssef',
    'tags' => json_encode(['plant', 'iot', 'gardening'])
]);

CommunityPost::create([
    'title' => 'Voice-Controlled LED Lamp',
    'description' => 'Build a lamp that responds to voice commands using Arduino and Google Assistant.',
    'images' => json_encode(['led-lamp.jpg']),
    'author_name' => 'Salma',
    'author_avatar' => 'salma.jpg',
    'author_username' => '@salma',
    'tags' => json_encode(['led', 'voice', 'arduino'])
]);

CommunityPost::create([
    'title' => 'Mood-Based Music Box',
    'description' => 'A music box that selects songs based on your mood using facial expression or text sentiment.',
    'images' => json_encode(['music-box.jpg']),
    'author_name' => 'Omar',
    'author_avatar' => 'omar.jpg',
    'author_username' => '@omar',
    'tags' => json_encode(['music', 'ai', 'mood'])
]);

CommunityPost::create([
    'title' => 'Plastic Bottle Organizer',
    'description' => 'Turn recycled plastic bottles into a modular desk organizer with compartments for pens and tools.',
    'images' => json_encode(['bottle-organizer.jpg']),
    'author_name' => 'Laila',
    'author_avatar' => 'laila.jpg',
    'author_username' => '@laila',
    'tags' => json_encode(['recycling', 'plastic', 'desk'])
]);

CommunityPost::create([
    'title' => 'AI-Powered Recycling Sorter',
    'description' => 'Create a mini recycling sorter that uses image classification to detect and separate materials.',
    'images' => json_encode(['recycling-sorter.jpg']),
    'author_name' => 'Ahmed',
    'author_avatar' => 'ahmed.jpg',
    'author_username' => '@ahmed',
    'tags' => json_encode(['ai', 'recycling', 'sorter'])
]);
    }
}
