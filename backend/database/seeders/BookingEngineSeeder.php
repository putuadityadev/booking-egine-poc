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
        $prop1 = Property::create([
            'code' => 'UMV-UBUD',
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
        ]);

        MembershipProperty::create([
            'property_id' => $prop1->id,
            'x_tenant_domain' => 'jeevawasa.localhost',
            'client_id' => 'a0f17583-5f52-4a2c-bd5b-782e81f75052',
            'client_secret' => 'g6WqI2UpTE3Qw5SQJ1G45UuWUblr6OQDI6Jpdddv',
            'merchant_id' => 'a0f1662d-8a3a-4970-b026-f7a0ae944f18',
            'corporate_id' => '9d3406fb-3996-4775-a9c9-8ae86a15f005',
            'is_active' => true,
        ]);

        Room::create([
            'property_id' => $prop1->id,
            'code' => 'PROD-ROOM-ROYAL',
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
        ]);

        Room::create([
            'property_id' => $prop1->id,
            'code' => 'PROD-ROOM-DELUXE',
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
        ]);

        Room::create([
            'property_id' => $prop1->id,
            'code' => 'PROD-ROOM-GARDEN',
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
        ]);

        Experience::create([
            'property_id' => $prop1->id,
            'code' => 'EXP-BREAKFAST',
            'name' => 'Floating Breakfast & Tropical Flower Bath',
            'category' => 'Dining & Lifestyle',
            'duration' => '2 Hours',
            'price' => 450000,
            'image_url' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=600&q=80',
            'rating' => 4.98,
            'badge' => 'Signature',
            'is_member_rate_applicable' => true,
            'member_perk' => 'Complimentary for Diamond members (1x per stay)',
        ]);

        Experience::create([
            'property_id' => $prop1->id,
            'code' => 'EXP-SPA-HEALING',
            'name' => 'Tejas Herbal Boreh Body & Energy Healing',
            'category' => 'Wellness & Spa',
            'duration' => '90 Mins',
            'price' => 650000,
            'image_url' => 'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?auto=format&fit=crop&w=600&q=80',
            'rating' => 4.95,
            'badge' => 'Top Rated',
            'is_member_rate_applicable' => true,
            'member_perk' => '15% discount for Silver, Gold & Diamond',
        ]);

        // ---------------------------------------------------------------------
        // Property 2: Adiwana Alas Harum Sanctuary
        // ---------------------------------------------------------------------
        $prop2 = Property::create([
            'code' => 'AAH-UBUD',
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
        ]);

        MembershipProperty::create([
            'property_id' => $prop2->id,
            'x_tenant_domain' => 'jeevawasa.localhost',
            'client_id' => 'a16bb106-ea39-4ecb-b58b-d4d00fe94342',
            'client_secret' => 'g6WqI2UpTE3Qw5SQJ1G45UuWUblr6OQDI6Jpdddv',
            'merchant_id' => 'a0601b49-5cdf-4e21-995f-1ee04990b614',
            'corporate_id' => '9d3406fb-3996-4775-a9c9-8ae86a15f005',
            'is_active' => true,
        ]);

        Room::create([
            'property_id' => $prop2->id,
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
        ]);

        Room::create([
            'property_id' => $prop2->id,
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
        ]);

        // ---------------------------------------------------------------------
        // Property 3: Adiwana Svarga Loka Wellness
        // ---------------------------------------------------------------------
        $prop3 = Property::create([
            'code' => 'ASL-UBUD',
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
        ]);

        MembershipProperty::create([
            'property_id' => $prop3->id,
            'x_tenant_domain' => 'jeevawasa.localhost',
            'client_id' => 'a29769f5-9cd7-4f31-89b2-8452c87f7720',
            'client_secret' => 'g6WqI2UpTE3Qw5SQJ1G45UuWUblr6OQDI6Jpdddv',
            'merchant_id' => '9e57d323-6111-497d-bf54-de729fe53072',
            'corporate_id' => '9d3406fb-3996-4775-a9c9-8ae86a15f005',
            'is_active' => true,
        ]);

        Room::create([
            'property_id' => $prop3->id,
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
        ]);

        // ---------------------------------------------------------------------
        // Property 4: Grand Sahid City Hotel (Non-Membership Benchmark)
        // ---------------------------------------------------------------------
        $prop4 = Property::create([
            'code' => 'GSH-JKT',
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
        ]);

        Room::create([
            'property_id' => $prop4->id,
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
        ]);
    }
}
