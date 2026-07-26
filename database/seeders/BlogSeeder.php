<?php

namespace Database\Seeders;

use App\Models\Blog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $topics = [
            'Best Hill Stations to Visit', 'Top Luxury Villas in India', 'Weekend Getaway Ideas',
            'Family-Friendly Destinations', 'Romantic Escapes for Couples', 'Budget Travel Tips',
            'Hidden Gems of Maharashtra', 'Best Monsoon Retreats', 'Solo Travel Guide',
            'Adventure Activities in Hills', 'Villa vs Hotel: What to Choose', 'How to Plan a Villa Stay',
            'Top Destinations Near Mumbai', 'Goa Villa Guide', 'Kerala Backwaters Experience',
            'Coorg Coffee Estate Stays', 'Manali Winter Travel', 'Ooty Travel Guide',
            'Panchgani Weekend Trip', 'Lonavala Villas Best Picks', 'Mahabaleshwar Strawberry Season',
            'Alibaug Beach Villa Guide', 'Uttarakhand Hill Resorts', 'Rajasthan Heritage Stays',
            'Himachal Pradesh Travel Tips', 'Things to Do in Coorg', 'Best Private Pool Villas',
            'Top 10 Villas for Groups', 'Pet-Friendly Villa Stays', 'Eco-Friendly Stays in India',
            'Treehouse Stay Experiences', 'Lakeside Villa Retreats', 'Mountain View Properties',
            'Forest Retreat Ideas', 'Beachfront Villa Experiences', 'Luxury Camping Alternatives',
            'Colonial Bungalow Stays', 'Farm Stay Holidays', 'Heritage Property Guide',
            'Wellness Retreat Options', 'Yoga Retreat Destinations', 'Spa Villa Experiences',
            'Kids Activities at Villas', 'Outdoor Swimming Pool Villas', 'BBQ Villa Picks',
            'Photography Spots in Villas', 'Night Sky Viewing Spots', 'Bird Watching Retreats',
            'Trekking Base Camp Stays', 'Cycling Holiday Ideas',
        ];

        $contentBlocks = [
            '<p>Discovering the perfect getaway has never been easier. Whether you are looking for a serene mountain escape or a beach retreat, India offers incredible options for every traveller.</p><p>Luxury villas provide an unmatched level of privacy and personalized experiences. From private pools to dedicated staff, these properties redefine vacation living.</p>',
            '<p>Planning a vacation requires careful consideration of your preferences, budget, and the season. The best destinations in India offer something unique every time of year.</p><p>From misty hills to golden beaches, each region has its own charm. Understanding what each place offers helps you make the most of your travel time.</p>',
            '<p>Travel is not just about reaching a destination — it is about the journey itself. Every road trip, flight, or train journey tells a story worth remembering.</p><p>Staying in a private villa adds a layer of comfort that traditional hotels rarely match. The freedom to set your own schedule, cook your own meals, and enjoy your own space is priceless.</p>',
            '<p>India is a land of diversity — culturally, geographically, and climatically. Whether you seek snow-capped peaks, tropical beaches, or lush green valleys, you will find it all within the subcontinent.</p><p>The rise of villa tourism has made it possible for families and groups to travel together while maintaining the comforts of home.</p>',
            '<p>Weekend trips are a great way to recharge and explore new places without taking too much time off work. India has countless weekend destinations within driving distance of major cities.</p><p>The key to a perfect weekend getaway is choosing a destination that offers both relaxation and adventure in equal measure.</p>',
        ];

        $metaDescs = [
            'Explore the best travel destinations and villa stays across India. Plan your perfect getaway with our expert guides.',
            'Discover luxury villa experiences, hidden destinations, and travel tips for your next Indian holiday.',
            'Your ultimate guide to villa stays, weekend getaways, and premium travel experiences across India.',
            'Find the best places to stay and visit in India. Expert travel advice for families, couples, and groups.',
            'Plan your dream vacation with insider tips on top destinations, luxury properties, and travel hacks.',
        ];

        $imagePath = 'blogs/1778088697_69fb7af9bee27.jpeg';

        $blogs = [];
        $now   = now();

        for ($i = 1; $i <= 1000; $i++) {
            $topicBase  = $topics[array_rand($topics)];
            $title      = $topicBase . ' — Part ' . $i;
            $baseSlug   = Str::slug($title);
            $slug       = $baseSlug . '-' . $i;

            $blogs[] = [
                'title'            => $title,
                'slug'             => $slug,
                'description'      => 'Explore ' . strtolower($topicBase) . '. A comprehensive guide to help you plan your perfect trip with expert insights and recommendations.',
                'content'          => $contentBlocks[array_rand($contentBlocks)],
                'image'            => $imagePath,
                'image_alt'        => $topicBase . ' travel guide',
                'status'           => 1,
                'meta_title'       => $title . ' | Hill Station Villas',
                'meta_description' => $metaDescs[array_rand($metaDescs)],
                'meta_keywords'    => 'villa stay, travel, ' . Str::lower($topicBase) . ', india travel',
                'schema'           => null,
                'other_images'     => null,
                'created_at'       => $now->copy()->subDays(rand(0, 365)),
                'updated_at'       => $now,
            ];

            // Insert in batches of 100 to avoid memory issues
            if (count($blogs) === 100) {
                Blog::insert($blogs);
                $blogs = [];
            }
        }

        // Insert any remaining
        if (!empty($blogs)) {
            Blog::insert($blogs);
        }

        $this->command->info('✅ 1000 blog records seeded successfully!');
    }
}
