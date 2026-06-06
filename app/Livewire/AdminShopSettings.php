<?php

namespace App\Livewire;

use App\Models\Setting;
use App\Models\Unit;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class AdminShopSettings extends Component
{
    use WithFileUploads;

    public string $activeTab = 'identity';

    // Identity
    public string $legal_name = '';

    public string $dba_name = '';

    public string $shop_phone = '';

    public string $sales_inquiry_email = '';

    public string $service_inquiry_email = '';

    public string $legal_inquiry_email = '';

    // Geography
    public string $shop_address = '';

    public string $shop_city = '';

    public string $shop_state = '';

    public string $shop_postal_code = '';

    public string $map_latitude = '14.5995'; // Manila Default

    public string $map_longitude = '120.9842'; // Manila Default

    // Socials
    public string $facebook_url = '';

    public string $instagram_url = '';

    public string $tiktok_url = '';

    // Appearance (Merged from AdminCustomization)
    public $palette;

    public $layout_preset;

    public $hero_unit_id;

    public $hero_headline;

    public $hero_subtitle;

    public $show_auctions;

    public $show_comparison;

    public $show_inquiries;

    public $design_logo;

    public ?string $current_design_logo_url = null;

    // Infrastructure
    public string $s3_bucket = '';

    public string $s3_region = '';

    public string $primary_color = '#000000';

    public string $accent_tone = '#565e74';

    public $logo;

    public ?string $current_logo_url = null;

    public function mount(): void
    {
        // Identity
        $this->legal_name = Setting::get('legal_name', 'The Gallery Automotive Group, LLC');
        $this->dba_name = Setting::get('dba_name', Setting::get('shop_name', 'The Gallery'));
        $this->shop_phone = Setting::get('shop_phone', '+63 917 123 4567');
        $this->sales_inquiry_email = Setting::get('sales_inquiry_email', Setting::get('shop_email', 'acquisitions@thegallery.com'));
        $this->service_inquiry_email = Setting::get('service_inquiry_email', 'concierge@thegallery.com');
        $this->legal_inquiry_email = Setting::get('legal_inquiry_email', 'legal@thegallery.com');

        // Geography
        $this->shop_address = Setting::get('shop_address', '');
        $this->shop_city = Setting::get('shop_city', 'Manila');
        $this->shop_state = Setting::get('shop_state', 'NCR');
        $this->shop_postal_code = Setting::get('shop_postal_code', '1000');
        $this->map_latitude = Setting::get('map_latitude', '14.5995');
        $this->map_longitude = Setting::get('map_longitude', '120.9842');

        // Socials
        $this->facebook_url = Setting::get('facebook_url', '');
        $this->instagram_url = Setting::get('instagram_url', '');
        $this->tiktok_url = Setting::get('tiktok_url', '');

        // Appearance
        $this->palette = Setting::get('design_palette', 'emerald');
        $this->layout_preset = Setting::get('design_layout', 'cinema');
        $this->hero_unit_id = Setting::get('design_hero_unit_id');
        $this->hero_headline = Setting::get('design_hero_headline', 'Automotive Excellence');
        $this->hero_subtitle = Setting::get('design_hero_subtitle', 'Curated collection of precision engineered assets.');
        $this->show_auctions = (bool) Setting::get('design_show_auctions', true);
        $this->show_comparison = (bool) Setting::get('design_show_comparison', true);
        $this->show_inquiries = (bool) Setting::get('design_show_inquiries', true);

        $designLogoPath = Setting::get('design_logo_path');
        if ($designLogoPath) {
            $this->current_design_logo_url = Storage::url($designLogoPath);
        }

        // Infrastructure
        $this->s3_bucket = config('filesystems.disks.s3.bucket', 'gallery-assets-prod');
        $this->s3_region = config('filesystems.disks.s3.region', 'ap-southeast-1');
        $this->primary_color = Setting::get('primary_color', '#000000');
        $this->accent_tone = Setting::get('accent_tone', '#565e74');

        $logoPath = Setting::get('shop_logo');
        if ($logoPath) {
            $this->current_logo_url = Storage::url($logoPath);
        }
    }

    public function save(): void
    {
        $this->validate([
            'logo' => 'nullable|image|max:2048',
            'design_logo' => 'nullable|image|max:2048',
            'sales_inquiry_email' => 'required|email',
            'service_inquiry_email' => 'nullable|email',
            'legal_inquiry_email' => 'nullable|email',
            'map_latitude' => 'required|numeric',
            'map_longitude' => 'required|numeric',
            'hero_headline' => 'required|string|max:100',
            'hero_subtitle' => 'required|string|max:255',
        ]);

        if ($this->logo) {
            $path = $this->logo->store('branding', 'public');
            $oldLogo = Setting::get('shop_logo');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }
            Setting::set('shop_logo', $path);
            $this->current_logo_url = Storage::url($path);
            $this->logo = null;
        }

        if ($this->design_logo) {
            $path = $this->design_logo->store('customization', 'public');
            $oldDesignLogo = Setting::get('design_logo_path');
            if ($oldDesignLogo) {
                Storage::disk('public')->delete($oldDesignLogo);
            }
            Setting::set('design_logo_path', $path);
            $this->current_design_logo_url = Storage::url($path);
            $this->design_logo = null;
        }

        // Persist Identity
        Setting::set('legal_name', $this->legal_name);
        Setting::set('dba_name', $this->dba_name);
        Setting::set('shop_name', $this->dba_name);
        Setting::set('shop_phone', $this->shop_phone);
        Setting::set('sales_inquiry_email', $this->sales_inquiry_email);
        Setting::set('shop_email', $this->sales_inquiry_email);
        Setting::set('service_inquiry_email', $this->service_inquiry_email);
        Setting::set('legal_inquiry_email', $this->legal_inquiry_email);

        // Persist Geography
        Setting::set('shop_address', $this->shop_address);
        Setting::set('shop_city', $this->shop_city);
        Setting::set('shop_state', $this->shop_state);
        Setting::set('shop_postal_code', $this->shop_postal_code);
        Setting::set('map_latitude', $this->map_latitude);
        Setting::set('map_longitude', $this->map_longitude);

        $this->dispatch('update-map', lat: $this->map_latitude, lng: $this->map_longitude);

        // Persist Socials
        Setting::set('facebook_url', $this->facebook_url);
        Setting::set('instagram_url', $this->instagram_url);
        Setting::set('tiktok_url', $this->tiktok_url);

        // Persist Appearance
        Setting::set('design_palette', $this->palette);
        Setting::set('design_layout', $this->layout_preset);
        Setting::set('design_hero_unit_id', $this->hero_unit_id);
        Setting::set('design_hero_headline', $this->hero_headline);
        Setting::set('design_hero_subtitle', $this->hero_subtitle);
        Setting::set('design_show_auctions', $this->show_auctions, 'boolean');
        Setting::set('design_show_comparison', $this->show_comparison, 'boolean');
        Setting::set('design_show_inquiries', $this->show_inquiries, 'boolean');

        // Persist Infrastructure
        Setting::set('primary_color', $this->primary_color);
        Setting::set('accent_tone', $this->accent_tone);

        session()->flash('status', 'System Master configurations updated successfully.');
    }

    public function render()
    {
        return view('livewire.admin-shop-settings', [
            'units' => Unit::orderBy('name')->get(),
            'palettes' => config('showroom.design.palettes'),
            'layouts' => config('showroom.design.layouts'),
        ])
            ->layout('layouts.admin-panel', [
                'title' => 'System Master',
            ]);
    }
}
