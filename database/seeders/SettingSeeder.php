<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'shop_name' => 'Car Showroom',
            'shop_address' => '123 Showroom St, Manila, Philippines',
            'shop_email' => 'contact@carshowroom.com',
            'shop_phone' => '+63 912 345 6789',
            'map_latitude' => '14.5995',
            'map_longitude' => '120.9842',
            'google_maps_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3861.123456789!2d120.1234567!3d14.1234567!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTTCsDA3JzM0LjQiTiAxMjDCsDA3JzM0LjQiRQ!5e0!3m2!1sen!2sph!4v1234567890',
            'facebook_url' => 'https://facebook.com/carshowroom',
            'instagram_url' => 'https://instagram.com/carshowroom',
            'tiktok_url' => 'https://tiktok.com/@carshowroom',
            
            // Design Customization
            'design_palette' => 'emerald',
            'design_layout' => 'cinema',
            'design_logo_path' => null,
            'design_hero_unit_id' => null,
            'design_hero_headline' => 'Automotive Excellence',
            'design_hero_subtitle' => 'Curated collection of precision engineered assets for the discerning collector.',
            'design_show_auctions' => true,
            'design_show_comparison' => true,
            'design_show_inquiries' => true,
        ];

        foreach ($settings as $key => $value) {
            $type = is_bool($value) ? 'boolean' : 'string';
            Setting::set($key, $value, $type);
        }
    }
}
