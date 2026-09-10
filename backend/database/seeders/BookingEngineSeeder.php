<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;
use App\Models\MembershipProperty;
use App\Models\Room;
use App\Models\Experience;

class BookingEngineSeeder extends Seeder
{
    public function run(): void
    {
        // ---------------------------------------------------------------------
        // Property 1: Unagi Mas Villas Ubud (Jeevawasa Group)
        // ---------------------------------------------------------------------
        $prop1 = Property::updateOrCreate(
            ['code' => 'UMV-UBUD'],
            [
                'name' => 'Unagi Mas Villas Ubud',
                'tagline' => 'Private Pool Sanctuary in the Sacred Valley of Ubud',
                'description' => 'Perched on the tranquil slopes of Ubud, Unagi Mas Villas offers handcrafted wooden architecture, private infinity plunge pools, and uninterrupted views of lush river valleys. Experience authentic Balinese hospitality paired with world-class wellness.',
                'address' => 'Jl. Suweta, Sambahan, Kecamatan Ubud, Kabupaten Gianyar, Bali 80571',
                'city' => 'Ubud, Bali',
                'country' => 'Indonesia',
                'star_rating' => 5,
                'review_score' => 4.96,
                'review_count' => 142,
                'badge' => 'Guest favorite',
                'image_url' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=1200&q=80',
                ],
                'amenities' => [
                    'Private Infinity Pool',
                    'Complimentary Afternoon Tea',
                    'High-speed Fiber Wi-Fi',
                    'Free Shuttle to Central Ubud',
                    'Tejas Spa & Wellness',
                    'Floating Breakfast Experience',
                ],
                'has_membership' => true,
            ]
        );

        MembershipProperty::updateOrCreate(
            ['property_id' => $prop1->id],
            [
                'x_tenant_domain' => 'jeevawasa.localhost',
                'client_id' => 'a0f17583-5f52-4a2c-bd5b-782e81f75052',
                'client_secret' => 'g6WqI2UpTE3Qw5SQJ1G45UuWUblr6OQDI6Jpdddv',
                'merchant_id' => 'a0f1662d-8a3a-4970-b026-f7a0ae944f18',
                'corporate_id' => '9d3406fb-3996-4775-a9c9-8ae86a15f005',
                'is_active' => true,
            ]
        );

        $prop1Rooms = [
            [
                'code' => 'UMV-ROOM-ROYAL',
                'name' => 'Royal Riverfront Pool Villa',
                'description' => 'Expansive one-bedroom private villa featuring a 10-meter riverfront infinity pool, outdoor sunken bathtub, sun deck, and panoramic jungle canyon vistas.',
                'capacity' => 2,
                'bed_type' => '1 Super King Bed',
                'size_sqm' => 125,
                'base_price' => 3200000,
                'image_url' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1591088398332-8a7791972843?auto=format&fit=crop&w=800&q=80',
                ],
                'features' => ['Private Pool', 'Sunken Bathtub', 'Gourmet Breakfast', 'Butler Service', 'River View'],
                'is_member_rate_applicable' => true,
                'tier_discount_rates' => ['Bronze' => 5, 'Silver' => 10, 'Gold' => 15, 'Diamond' => 20],
            ],
            [
                'code' => 'UMV-ROOM-DELUXE',
                'name' => 'Deluxe Forest Canopy Suite',
                'description' => 'Nestled among native banyan trees, this luxury suite features a private teak balcony with forest views, rain shower, and handwoven artisanal decor.',
                'capacity' => 2,
                'bed_type' => '1 King Bed',
                'size_sqm' => 85,
                'base_price' => 1850000,
                'image_url' => 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80',
                ],
                'features' => ['Forest Balcony', 'Rain Shower', 'Complimentary Minibar', 'Espresso Machine'],
                'is_member_rate_applicable' => true,
                'tier_discount_rates' => ['Bronze' => 5, 'Silver' => 10, 'Gold' => 15, 'Diamond' => 20],
            ],
            [
                'code' => 'UMV-ROOM-GARDEN',
                'name' => 'Garden Terrace Pavilion',
                'description' => 'Cozy modern pavilion with a private walled tropical garden, open-air stone bathtub, and sun loungers for peaceful relaxation.',
                'capacity' => 2,
                'bed_type' => '1 Queen Bed',
                'size_sqm' => 65,
                'base_price' => 1250000,
                'image_url' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80',
                ],
                'features' => ['Private Garden', 'Stone Bathtub', 'High-Speed Wi-Fi', 'Daily Mineral Water'],
                'is_member_rate_applicable' => true,
                'tier_discount_rates' => ['Bronze' => 5, 'Silver' => 10, 'Gold' => 15, 'Diamond' => 20],
            ],
            [
                'code' => 'UMV-ROOM-2BED-FAMILY',
                'name' => 'Two-Bedroom Family Residence Villa',
                'description' => 'Lavish two-story family villa with interconnected bedrooms, private 12m lap pool, open-plan dining pavilion, and full butler kitchen.',
                'capacity' => 4,
                'bed_type' => '2 King Beds',
                'size_sqm' => 195,
                'base_price' => 4800000,
                'image_url' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80',
                ],
                'features' => ['Private Lap Pool', 'Family Dining Room', 'Dedicated Butler', 'Tropical Garden View'],
                'is_member_rate_applicable' => true,
                'tier_discount_rates' => ['Bronze' => 5, 'Silver' => 10, 'Gold' => 15, 'Diamond' => 20],
            ],
            [
                'code' => 'UMV-ROOM-HONEYMOON',
                'name' => 'Honeymoon Valley Plunge Suite',
                'description' => 'Romantic sanctuary crafted for couples, with heated plunge pool suspended over the jungle valley, candlelit terrace, and flower petal bath service.',
                'capacity' => 2,
                'bed_type' => '1 King Bed',
                'size_sqm' => 95,
                'base_price' => 2650000,
                'image_url' => 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=800&q=80',
                ],
                'features' => ['Heated Plunge Pool', 'Romantic Flower Bath', 'Champagne on Arrival', 'Valley View'],
                'is_member_rate_applicable' => true,
                'tier_discount_rates' => ['Bronze' => 5, 'Silver' => 10, 'Gold' => 15, 'Diamond' => 20],
            ],
            [
                'code' => 'UMV-ROOM-PRESIDENTIAL',
                'name' => 'Presidential Riverfront Sanctuary Villa',
                'description' => 'The crown jewel of Unagi Mas. Features 3 en-suite master bedrooms, a 16m infinity edge pool, private chef kitchen, and private yoga shala.',
                'capacity' => 6,
                'bed_type' => '3 King Beds',
                'size_sqm' => 280,
                'base_price' => 6200000,
                'image_url' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=800&q=80',
                ],
                'features' => ['16m Infinity Pool', 'Private Chef', 'Private Shala', 'VIP Airport Transfer'],
                'is_member_rate_applicable' => true,
                'tier_discount_rates' => ['Bronze' => 5, 'Silver' => 10, 'Gold' => 15, 'Diamond' => 20],
            ],
            [
                'code' => 'UMV-ROOM-CANOPY-PENTHOUSE',
                'name' => 'Jungle Canopy Luxury Penthouse',
                'description' => 'Top-floor penthouse with 360-degree views of the sacred Ubud greenery, rooftop hydrotherapy Jacuzzi, and expansive cedar deck.',
                'capacity' => 2,
                'bed_type' => '1 Super King Bed',
                'size_sqm' => 140,
                'base_price' => 3800000,
                'image_url' => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=800&q=80',
                ],
                'features' => ['Rooftop Jacuzzi', '360° Panorama', 'Wine Cellar', 'Bose Sound System'],
                'is_member_rate_applicable' => true,
                'tier_discount_rates' => ['Bronze' => 5, 'Silver' => 10, 'Gold' => 15, 'Diamond' => 20],
            ],
            [
                'code' => 'UMV-ROOM-SUNRISE',
                'name' => 'Sunrise Valley Infinity Pool Villa',
                'description' => 'East-facing luxury villa offering spectacular Mount Agung sunrise views, infinity plunge pool, and serene private meditation garden.',
                'capacity' => 2,
                'bed_type' => '1 King Bed',
                'size_sqm' => 115,
                'base_price' => 3450000,
                'image_url' => 'https://images.unsplash.com/photo-1596394516093-501ba68a0ba6?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1596394516093-501ba68a0ba6?auto=format&fit=crop&w=800&q=80',
                ],
                'features' => ['Sunrise View', 'Infinity Pool', 'Floating Breakfast Included', 'Sunken Lounge'],
                'is_member_rate_applicable' => true,
                'tier_discount_rates' => ['Bronze' => 5, 'Silver' => 10, 'Gold' => 15, 'Diamond' => 20],
            ],
            [
                'code' => 'UMV-ROOM-WELLNESS',
                'name' => 'Tejas Spa Wellness Retreat Villa',
                'description' => 'Holistic haven with in-villa steam bath, herbal soaking tub, daily complimentary 60-min Balinese massage, and fresh detox elixirs.',
                'capacity' => 2,
                'bed_type' => '1 King Bed',
                'size_sqm' => 105,
                'base_price' => 2950000,
                'image_url' => 'https://images.unsplash.com/photo-1584132967334-10e028bd69f7?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1584132967334-10e028bd69f7?auto=format&fit=crop&w=800&q=80',
                ],
                'features' => ['In-Villa Steam Room', 'Daily Spa Treatment', 'Herbal Elixir Bar', 'Garden Patio'],
                'is_member_rate_applicable' => true,
                'tier_discount_rates' => ['Bronze' => 5, 'Silver' => 10, 'Gold' => 15, 'Diamond' => 20],
            ],
        ];

        foreach ($prop1Rooms as $r) {
            Room::updateOrCreate(
                ['code' => $r['code']],
                array_merge($r, ['property_id' => $prop1->id])
            );
        }

        Experience::updateOrCreate(
            ['code' => 'EXP-BREAKFAST'],
            [
                'property_id' => $prop1->id,
                'name' => 'Floating Breakfast & Tropical Flower Bath',
                'category' => 'Dining & Lifestyle',
                'duration' => '2 Hours',
                'price' => 450000,
                'image_url' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=600&q=80',
                'rating' => 4.98,
                'badge' => 'Signature',
                'is_member_rate_applicable' => true,
                'member_perk' => 'Complimentary for Diamond members (1x per stay)',
            ]
        );

        Experience::updateOrCreate(
            ['code' => 'EXP-SPA-HEALING'],
            [
                'property_id' => $prop1->id,
                'name' => 'Tejas Herbal Boreh Body & Energy Healing',
                'category' => 'Wellness & Spa',
                'duration' => '90 Mins',
                'price' => 650000,
                'image_url' => 'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?auto=format&fit=crop&w=600&q=80',
                'rating' => 4.95,
                'badge' => 'Top Rated',
                'is_member_rate_applicable' => true,
                'member_perk' => '15% discount for Silver, Gold & Diamond',
            ]
        );

        // ---------------------------------------------------------------------
        // Property 2: Adiwana Alas Harum Sanctuary
        // ---------------------------------------------------------------------
        $prop2 = Property::updateOrCreate(
            ['code' => 'AAH-UBUD'],
            [
                'name' => 'Adiwana Alas Harum Sanctuary',
                'tagline' => 'Iconic Rice Terrace Oasis in Tegallalang',
                'description' => 'Surrounded by the UNESCO-listed emerald rice terraces of Tegallalang, Adiwana Alas Harum blends bohemian luxury with Balinese agrarian charm.',
                'address' => 'Jl. Raya Tegallalang, Tegallalang, Kabupaten Gianyar, Bali 80561',
                'city' => 'Tegallalang, Bali',
                'country' => 'Indonesia',
                'star_rating' => 5,
                'review_score' => 4.92,
                'review_count' => 98,
                'badge' => 'Rare find',
                'image_url' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=1200&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80',
                ],
                'amenities' => [
                    'Multi-tier Infinity Pool',
                    'Paoman Restaurant & Bar',
                    'Terrace Yoga Shala',
                    'Organic Garden Tours',
                ],
                'has_membership' => true,
            ]
        );

        MembershipProperty::updateOrCreate(
            ['property_id' => $prop2->id],
            [
                'x_tenant_domain' => 'jeevawasa.localhost',
                'client_id' => 'a16bb106-ea39-4ecb-b58b-d4d00fe94342',
                'client_secret' => 'g6WqI2UpTE3Qw5SQJ1G45UuWUblr6OQDI6Jpdddv',
                'merchant_id' => 'a0601b49-5cdf-4e21-995f-1ee04990b614',
                'corporate_id' => '9d3406fb-3996-4775-a9c9-8ae86a15f005',
                'is_active' => true,
            ]
        );

        $prop2Rooms = [
            [
                'code' => 'AAH-ROOM-TERRACE',
                'name' => 'Valley Panorama Pool Villa',
                'description' => 'Unrestricted vistas over the undulating rice terraces with private saltwater plunge pool and open sun terrace.',
                'capacity' => 2,
                'bed_type' => '1 King Bed',
                'size_sqm' => 110,
                'base_price' => 2900000,
                'image_url' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=800&q=80'],
                'features' => ['Terrace View', 'Private Saltwater Pool', 'Outdoor Daybed'],
                'is_member_rate_applicable' => true,
                'tier_discount_rates' => ['Bronze' => 5, 'Silver' => 10, 'Gold' => 15, 'Diamond' => 20],
            ],
            [
                'code' => 'AAH-ROOM-JUNGLE',
                'name' => 'Jungle Sanctuary Suite',
                'description' => 'Spacious open-concept suite decorated in soothing earth tones with a private balcony overlooking wild tropical bamboo groves.',
                'capacity' => 2,
                'bed_type' => '1 King Bed',
                'size_sqm' => 75,
                'base_price' => 1650000,
                'image_url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80'],
                'features' => ['Bamboo Grove View', 'Deep Soaking Tub', 'Complimentary High Tea'],
                'is_member_rate_applicable' => true,
                'tier_discount_rates' => ['Bronze' => 5, 'Silver' => 10, 'Gold' => 15, 'Diamond' => 20],
            ],
            [
                'code' => 'AAH-ROOM-PREMIER-POOL',
                'name' => 'Premier Rice Terrace Villa',
                'description' => 'Front-row seat to the emerald Tegallalang contours with infinity lap pool and outdoor rain shower pavilion.',
                'capacity' => 2,
                'bed_type' => '1 Super King Bed',
                'size_sqm' => 130,
                'base_price' => 3400000,
                'image_url' => 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?auto=format&fit=crop&w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1580587771525-78b9dba3b914?auto=format&fit=crop&w=800&q=80'],
                'features' => ['Ricefield Panorama', 'Private Lap Pool', 'Floating Breakfast', 'Artisanal Teak Decor'],
                'is_member_rate_applicable' => true,
                'tier_discount_rates' => ['Bronze' => 5, 'Silver' => 10, 'Gold' => 15, 'Diamond' => 20],
            ],
            [
                'code' => 'AAH-ROOM-GRAND-SUITE',
                'name' => 'Alas Harum Grand Forest Suite',
                'description' => 'Generously proportioned suite with dedicated reading lounge, sunken stone bathtub, and private forest-canopy sun deck.',
                'capacity' => 2,
                'bed_type' => '1 King Bed',
                'size_sqm' => 90,
                'base_price' => 2250000,
                'image_url' => 'https://images.unsplash.com/photo-1591088398332-8a7791972843?auto=format&fit=crop&w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1591088398332-8a7791972843?auto=format&fit=crop&w=800&q=80'],
                'features' => ['Forest Canopy Deck', 'Sunken Stone Bath', 'Espresso Machine', 'Sunset View'],
                'is_member_rate_applicable' => true,
                'tier_discount_rates' => ['Bronze' => 5, 'Silver' => 10, 'Gold' => 15, 'Diamond' => 20],
            ],
            [
                'code' => 'AAH-ROOM-TERRACE-STUDIO',
                'name' => 'Agrarian Terrace Studio',
                'description' => 'Intimate studio with natural cane furnishings, private veranda overlooking agrarian orchards, and rain shower.',
                'capacity' => 2,
                'bed_type' => '1 Queen Bed',
                'size_sqm' => 60,
                'base_price' => 1450000,
                'image_url' => 'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?auto=format&fit=crop&w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?auto=format&fit=crop&w=800&q=80'],
                'features' => ['Veranda View', 'Eco Amenities', 'Fiber Wi-Fi', 'Daily Fresh Fruit'],
                'is_member_rate_applicable' => true,
                'tier_discount_rates' => ['Bronze' => 5, 'Silver' => 10, 'Gold' => 15, 'Diamond' => 20],
            ],
            [
                'code' => 'AAH-ROOM-BAMBOO-TREEHOUSE',
                'name' => 'Tegallalang Bamboo Treehouse Villa',
                'description' => 'Architectural bamboo treehouse nestled 8 meters above the valley canopy, featuring an outdoor net hammock and private plunge pool.',
                'capacity' => 2,
                'bed_type' => '1 King Bed',
                'size_sqm' => 85,
                'base_price' => 2750000,
                'image_url' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=800&q=80'],
                'features' => ['Suspended Net Bed', 'Canopy Plunge Pool', 'Artisan Bamboo Finish', 'Birdwatching Deck'],
                'is_member_rate_applicable' => true,
                'tier_discount_rates' => ['Bronze' => 5, 'Silver' => 10, 'Gold' => 15, 'Diamond' => 20],
            ],
            [
                'code' => 'AAH-ROOM-EMERALD-ROYAL',
                'name' => 'Emerald Ricefield Royal Penthouse',
                'description' => 'Two-bedroom royal penthouse with highest panoramic point above Tegallalang terraces, featuring an open glass-walled living pavilion.',
                'capacity' => 4,
                'bed_type' => '2 King Beds',
                'size_sqm' => 180,
                'base_price' => 4200000,
                'image_url' => 'https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=800&q=80'],
                'features' => ['Top-Tier Valley View', 'Two Master Suites', 'Dining Pavilion', 'Personal Concierge'],
                'is_member_rate_applicable' => true,
                'tier_discount_rates' => ['Bronze' => 5, 'Silver' => 10, 'Gold' => 15, 'Diamond' => 20],
            ],
            [
                'code' => 'AAH-ROOM-PAOMAN-EXEC',
                'name' => 'Paoman Valley View Executive Suite',
                'description' => 'Sophisticated retreat situated adjacent to Paoman culinary garden, with open-concept marble bathroom and private dining balcony.',
                'capacity' => 2,
                'bed_type' => '1 King Bed',
                'size_sqm' => 80,
                'base_price' => 2100000,
                'image_url' => 'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=800&q=80'],
                'features' => ['Garden View', 'Marble Bath', 'Wine Fridge', 'High-Speed Wi-Fi'],
                'is_member_rate_applicable' => true,
                'tier_discount_rates' => ['Bronze' => 5, 'Silver' => 10, 'Gold' => 15, 'Diamond' => 20],
            ],
            [
                'code' => 'AAH-ROOM-SACRED-PLUNGE',
                'name' => 'Sacred Lotus Plunge Pool Villa',
                'description' => 'Tranquil sanctuary villa bordering blooming lotus ponds, with private sun deck, crystal water plunge pool, and natural herbal amenities.',
                'capacity' => 2,
                'bed_type' => '1 King Bed',
                'size_sqm' => 120,
                'base_price' => 3100000,
                'image_url' => 'https://images.unsplash.com/photo-1576013551627-0cc20b96c2a7?auto=format&fit=crop&w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1576013551627-0cc20b96c2a7?auto=format&fit=crop&w=800&q=80'],
                'features' => ['Lotus Pond Border', 'Private Plunge Pool', 'Outdoor Daybed', 'Organic Aromatherapy'],
                'is_member_rate_applicable' => true,
                'tier_discount_rates' => ['Bronze' => 5, 'Silver' => 10, 'Gold' => 15, 'Diamond' => 20],
            ],
        ];

        foreach ($prop2Rooms as $r) {
            Room::updateOrCreate(
                ['code' => $r['code']],
                array_merge($r, ['property_id' => $prop2->id])
            );
        }

        // ---------------------------------------------------------------------
        // Property 3: Adiwana Svarga Loka Wellness
        // ---------------------------------------------------------------------
        $prop3 = Property::updateOrCreate(
            ['code' => 'ASL-UBUD'],
            [
                'name' => 'Adiwana Svarga Loka Wellness',
                'tagline' => 'Holistic Eco-Retreat along the Sacred Campuhan River',
                'description' => 'A haven of mindfulness and natural rejuvenation, Svarga Loka offers plant-based fine dining, holistic wellness consultations, and calming riverside accommodation.',
                'address' => 'Jl. Raya Penestanan, Sayan, Kecamatan Ubud, Kabupaten Gianyar, Bali 80571',
                'city' => 'Ubud, Bali',
                'country' => 'Indonesia',
                'star_rating' => 4,
                'review_score' => 4.88,
                'review_count' => 76,
                'badge' => 'Wellness Pick',
                'image_url' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=1200&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1584132967334-10e028bd69f7?auto=format&fit=crop&w=1200&q=80',
                ],
                'amenities' => [
                    'Ayusha Wellness Spa',
                    'Kemangi Ubud Vegetarian Resto',
                    'Riverfront Meditation Deck',
                    'Daily Sound Bath Sessions',
                ],
                'has_membership' => true,
            ]
        );

        MembershipProperty::updateOrCreate(
            ['property_id' => $prop3->id],
            [
                'x_tenant_domain' => 'jeevawasa.localhost',
                'client_id' => 'a29769f5-9cd7-4f31-89b2-8452c87f7720',
                'client_secret' => 'g6WqI2UpTE3Qw5SQJ1G45UuWUblr6OQDI6Jpdddv',
                'merchant_id' => '9e57d323-6111-497d-bf54-de729fe53072',
                'corporate_id' => '9d3406fb-3996-4775-a9c9-8ae86a15f005',
                'is_active' => true,
            ]
        );

        $prop3Rooms = [
            [
                'code' => 'ASL-ROOM-AYURVEDIC',
                'name' => 'Ayurvedic Wellness Suite',
                'description' => 'Designed with eco-friendly bamboo and reclaimed teak wood, this suite promotes restful sleep with natural soundscapes of the flowing Campuhan river.',
                'capacity' => 2,
                'bed_type' => '1 King Bed',
                'size_sqm' => 70,
                'base_price' => 2100000,
                'image_url' => 'https://images.unsplash.com/photo-1584132967334-10e028bd69f7?auto=format&fit=crop&w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1584132967334-10e028bd69f7?auto=format&fit=crop&w=800&q=80'],
                'features' => ['Riverfront Deck', 'Organic Toiletries', 'Daily Wellness Elixir'],
                'is_member_rate_applicable' => true,
                'tier_discount_rates' => ['Bronze' => 5, 'Silver' => 10, 'Gold' => 15, 'Diamond' => 20],
            ],
            [
                'code' => 'ASL-ROOM-RIVER-VILLA',
                'name' => 'Campuhan Riverside Sanctuary Villa',
                'description' => 'Directly overlooking the sacred convergence of Ubud rivers, with open meditation pavilion, river plunge pool, and natural herbal bath.',
                'capacity' => 2,
                'bed_type' => '1 Super King Bed',
                'size_sqm' => 115,
                'base_price' => 2800000,
                'image_url' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=800&q=80'],
                'features' => ['River Plunge Pool', 'Meditation Pavilion', 'Sound Healing Mat', 'Herbal Tea Bar'],
                'is_member_rate_applicable' => true,
                'tier_discount_rates' => ['Bronze' => 5, 'Silver' => 10, 'Gold' => 15, 'Diamond' => 20],
            ],
            [
                'code' => 'ASL-ROOM-PRANA',
                'name' => 'Prana Healing Studio',
                'description' => 'Zen-inspired minimalist studio featuring cedar wood floors, meditation cushions, natural linen bedding, and tranquil forest views.',
                'capacity' => 2,
                'bed_type' => '1 King Bed',
                'size_sqm' => 60,
                'base_price' => 1650000,
                'image_url' => 'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=800&q=80'],
                'features' => ['Zen Meditation Corner', 'Organic Tea Selection', 'Forest Balcony', 'Aromatherapy Diffuser'],
                'is_member_rate_applicable' => true,
                'tier_discount_rates' => ['Bronze' => 5, 'Silver' => 10, 'Gold' => 15, 'Diamond' => 20],
            ],
            [
                'code' => 'ASL-ROOM-CHAKRA',
                'name' => 'Chakra Harmonizing Duplex',
                'description' => 'Spacious split-level duplex suite with upstairs sleeping loft, downstairs wellness salon, and deep natural copper soaking tub.',
                'capacity' => 3,
                'bed_type' => '1 King Bed + 1 Daybed',
                'size_sqm' => 95,
                'base_price' => 2450000,
                'image_url' => 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=800&q=80'],
                'features' => ['Two-Story Loft', 'Copper Soaking Tub', 'Sound Bath Inclusion', 'River Garden Access'],
                'is_member_rate_applicable' => true,
                'tier_discount_rates' => ['Bronze' => 5, 'Silver' => 10, 'Gold' => 15, 'Diamond' => 20],
            ],
            [
                'code' => 'ASL-ROOM-ECO-DELUXE',
                'name' => 'Sayan Eco-Lodge Deluxe',
                'description' => 'Cozy eco-lodge room built with zero-plastic philosophy, featuring natural air currents, open river breeze, and organic cotton bathrobes.',
                'capacity' => 2,
                'bed_type' => '1 Queen Bed',
                'size_sqm' => 55,
                'base_price' => 1400000,
                'image_url' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80'],
                'features' => ['Eco-Friendly Amenities', 'River Breeze Balcony', 'Daily Morning Yoga', 'High-Speed Wi-Fi'],
                'is_member_rate_applicable' => true,
                'tier_discount_rates' => ['Bronze' => 5, 'Silver' => 10, 'Gold' => 15, 'Diamond' => 20],
            ],
            [
                'code' => 'ASL-ROOM-LOTUS-PAVILION',
                'name' => 'Riverside Lotus Meditation Pavilion',
                'description' => 'Standalone teak pavilion enveloped by blooming water gardens and the melodic rush of the sacred river, with outdoor rain shower.',
                'capacity' => 2,
                'bed_type' => '1 King Bed',
                'size_sqm' => 120,
                'base_price' => 2950000,
                'image_url' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80'],
                'features' => ['Lotus Garden View', 'Riverfront Terrace', 'Tibetan Singing Bowls', 'Vegan Minibar'],
                'is_member_rate_applicable' => true,
                'tier_discount_rates' => ['Bronze' => 5, 'Silver' => 10, 'Gold' => 15, 'Diamond' => 20],
            ],
            [
                'code' => 'ASL-ROOM-HEALING-MASTER',
                'name' => 'Ayusha Holistic Master Sanctuary',
                'description' => 'Two-bedroom master villa designed for deep rest and recovery, featuring private herbal sauna, hot hydrotherapy tub, and personal wellness host.',
                'capacity' => 4,
                'bed_type' => '2 King Beds',
                'size_sqm' => 160,
                'base_price' => 3600000,
                'image_url' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80'],
                'features' => ['Private Herbal Sauna', 'Hydrotherapy Tub', 'Two Master Suites', 'Daily Ayurvedic Consultation'],
                'is_member_rate_applicable' => true,
                'tier_discount_rates' => ['Bronze' => 5, 'Silver' => 10, 'Gold' => 15, 'Diamond' => 20],
            ],
            [
                'code' => 'ASL-ROOM-SOUNDBATH-LOFT',
                'name' => 'Campuhan Soundbath Holistic Loft',
                'description' => 'High-ceilinged timber loft engineered with acoustic harmony for sound meditation, offering panoramic vistas across the Campuhan gorge.',
                'capacity' => 2,
                'bed_type' => '1 King Bed',
                'size_sqm' => 75,
                'base_price' => 1950000,
                'image_url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80'],
                'features' => ['Acoustic Meditation Loft', 'Campuhan Gorge View', 'Organic Herbal Pillows', 'Sunrise Shala Access'],
                'is_member_rate_applicable' => true,
                'tier_discount_rates' => ['Bronze' => 5, 'Silver' => 10, 'Gold' => 15, 'Diamond' => 20],
            ],
            [
                'code' => 'ASL-ROOM-SPRING-RETREAT',
                'name' => 'Sacred Spring Eco Wellness Retreat',
                'description' => 'Expansive sanctuary villa beside holy spring waters, featuring private mineral plunge bath, outdoor living deck, and organic garden.',
                'capacity' => 4,
                'bed_type' => '2 King Beds',
                'size_sqm' => 200,
                'base_price' => 4100000,
                'image_url' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=800&q=80'],
                'features' => ['Mineral Spring Bath', 'Private Garden Deck', 'In-Villa Dining', 'Holistic Butler'],
                'is_member_rate_applicable' => true,
                'tier_discount_rates' => ['Bronze' => 5, 'Silver' => 10, 'Gold' => 15, 'Diamond' => 20],
            ],
        ];

        foreach ($prop3Rooms as $r) {
            Room::updateOrCreate(
                ['code' => $r['code']],
                array_merge($r, ['property_id' => $prop3->id])
            );
        }

        // ---------------------------------------------------------------------
        // Property 4: Grand Sahid City Hotel (Non-Membership Benchmark)
        // ---------------------------------------------------------------------
        $prop4 = Property::updateOrCreate(
            ['code' => 'GSH-JKT'],
            [
                'name' => 'Grand Sahid City Hotel',
                'tagline' => 'Contemporary Urban Elegance in Jakarta Central Business District',
                'description' => 'Strategic business and leisure hub in central Sudirman featuring sleek modern interiors, sky lounge, and direct access to premier financial centers.',
                'address' => 'Jl. Jend. Sudirman Kav. 86, Karet Tengsin, Jakarta Pusat 10220',
                'city' => 'Jakarta',
                'country' => 'Indonesia',
                'star_rating' => 4,
                'review_score' => 4.65,
                'review_count' => 310,
                'badge' => 'City Center',
                'image_url' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=1200&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=1200&q=80',
                ],
                'amenities' => [
                    'Olympic Size Swimming Pool',
                    'Executive Business Lounge',
                    '24/7 Gym & Fitness Studio',
                    'Japanese Teppanyaki Restaurant',
                ],
                'has_membership' => false,
            ]
        );

        $prop4Rooms = [
            [
                'code' => 'GSH-ROOM-EXECUTIVE',
                'name' => 'Executive City Skyline Suite',
                'description' => 'Modern metropolitan suite with floor-to-ceiling panoramic city skyline views, ergonomic work desk, and executive lounge privileges.',
                'capacity' => 2,
                'bed_type' => '1 King Bed',
                'size_sqm' => 55,
                'base_price' => 1100000,
                'image_url' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=800&q=80'],
                'features' => ['Skyline View', 'Executive Lounge Access', 'High-Speed Wi-Fi'],
                'is_member_rate_applicable' => false,
                'tier_discount_rates' => null,
            ],
            [
                'code' => 'GSH-ROOM-DELUXE',
                'name' => 'Deluxe Sudirman King Room',
                'description' => 'Comfortable contemporary room featuring premium pillow-top king bed, marble bathroom with rain shower, and bustling Sudirman avenue views.',
                'capacity' => 2,
                'bed_type' => '1 King Bed',
                'size_sqm' => 42,
                'base_price' => 850000,
                'image_url' => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=800&q=80'],
                'features' => ['Avenue View', 'Rain Shower', 'Complimentary Breakfast', 'Work Desk'],
                'is_member_rate_applicable' => false,
                'tier_discount_rates' => null,
            ],
            [
                'code' => 'GSH-ROOM-TWIN',
                'name' => 'Superior Metropolitan Twin Room',
                'description' => 'Versatile business room with two plush single beds, smart LED TV, ergonomic workstation, and soundproof double-glazed windows.',
                'capacity' => 2,
                'bed_type' => '2 Twin Beds',
                'size_sqm' => 38,
                'base_price' => 780000,
                'image_url' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80'],
                'features' => ['Twin Bed Setup', 'Soundproof Windows', 'Coffee Maker', 'City Center Location'],
                'is_member_rate_applicable' => false,
                'tier_discount_rates' => null,
            ],
            [
                'code' => 'GSH-ROOM-PRESIDENTIAL',
                'name' => 'Presidential Penthouse Suite',
                'description' => 'Top-floor presidential suite spanning 185 sqm with private meeting boardroom, butler service, jacuzzi, and 360-degree Jakarta skyline panorama.',
                'capacity' => 4,
                'bed_type' => '2 King Beds',
                'size_sqm' => 185,
                'base_price' => 3900000,
                'image_url' => 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=800&q=80'],
                'features' => ['Top Floor View', 'Private Boardroom', 'Jacuzzi', 'VIP Limousine Service'],
                'is_member_rate_applicable' => false,
                'tier_discount_rates' => null,
            ],
            [
                'code' => 'GSH-ROOM-STUDIO',
                'name' => 'Urban Studio Deluxe',
                'description' => 'Efficient smart suite tailored for solo business executives and digital nomads, complete with high-speed fiber connection and ergonomic Aeron chair.',
                'capacity' => 2,
                'bed_type' => '1 Queen Bed',
                'size_sqm' => 32,
                'base_price' => 650000,
                'image_url' => 'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?auto=format&fit=crop&w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?auto=format&fit=crop&w=800&q=80'],
                'features' => ['Ergonomic Workspace', 'Fiber Broadband', 'Smart TV', '24/7 Room Service'],
                'is_member_rate_applicable' => false,
                'tier_discount_rates' => null,
            ],
            [
                'code' => 'GSH-ROOM-DIPLOMATIC',
                'name' => 'Diplomatic Skyline Suite with Lounge',
                'description' => 'Corner luxury suite with private dining area, marble bathroom with city-view soaking tub, and full Club Lounge privileges.',
                'capacity' => 2,
                'bed_type' => '1 Super King Bed',
                'size_sqm' => 78,
                'base_price' => 1750000,
                'image_url' => 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=800&q=80'],
                'features' => ['Club Lounge Privileges', 'Marble Soaking Tub', 'Corner Skyline Panorama', 'Cocktail Hour Access'],
                'is_member_rate_applicable' => false,
                'tier_discount_rates' => null,
            ],
            [
                'code' => 'GSH-ROOM-CORNER-KING',
                'name' => 'Sudirman Corner Panoramic King Room',
                'description' => 'Double-aspect corner room bathed in natural sunlight with floor-to-ceiling vistas over central Jakarta landmarks and luxury bedding.',
                'capacity' => 2,
                'bed_type' => '1 King Bed',
                'size_sqm' => 48,
                'base_price' => 980000,
                'image_url' => 'https://images.unsplash.com/photo-1591088398332-8a7791972843?auto=format&fit=crop&w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1591088398332-8a7791972843?auto=format&fit=crop&w=800&q=80'],
                'features' => ['Dual Aspect Windows', 'Rain Shower', 'Espresso Pod Machine', 'Fast Wi-Fi'],
                'is_member_rate_applicable' => false,
                'tier_discount_rates' => null,
            ],
            [
                'code' => 'GSH-ROOM-AMBASSADOR',
                'name' => 'Ambassador Business Residence Suite',
                'description' => 'Extended-stay executive residence featuring separate living salon, kitchenette, guest powder room, and dedicated laundry amenities.',
                'capacity' => 3,
                'bed_type' => '1 King Bed + 1 Sofa Bed',
                'size_sqm' => 110,
                'base_price' => 2400000,
                'image_url' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80'],
                'features' => ['Kitchenette', 'Separate Living Room', 'Guest Powder Room', 'Club Lounge Access'],
                'is_member_rate_applicable' => false,
                'tier_discount_rates' => null,
            ],
            [
                'code' => 'GSH-ROOM-FAMILY-METRO',
                'name' => 'Metropolitan Two-Bedroom Family Suite',
                'description' => 'Interconnecting family residence with master king bedroom and secondary twin room, two full bathrooms, and spacious entertainment lounge.',
                'capacity' => 4,
                'bed_type' => '1 King Bed + 2 Twin Beds',
                'size_sqm' => 135,
                'base_price' => 2850000,
                'image_url' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80'],
                'features' => ['Two Bedrooms', 'Two Full Bathrooms', 'Family Entertainment Center', 'Buffet Breakfast for 4'],
                'is_member_rate_applicable' => false,
                'tier_discount_rates' => null,
            ],
        ];

        foreach ($prop4Rooms as $r) {
            Room::updateOrCreate(
                ['code' => $r['code']],
                array_merge($r, ['property_id' => $prop4->id])
            );
        }
    }
}
