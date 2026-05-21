<?php

namespace App\Livewire;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class AdminShopSettings extends Component
{
    use WithFileUploads;

    public string $shop_name = '';
    public string $shop_address = '';
    public string $shop_email = '';
    public string $shop_phone = '';
    public string $google_maps_url = '';
    public string $map_latitude = '';
    public string $map_longitude = '';
    public string $facebook_url = '';
    public string $instagram_url = '';
    public string $tiktok_url = '';

    public $logo;
    public ?string $current_logo_url = null;

    public function mount(): void
    {
        $this->shop_name = Setting::get('shop_name', 'Car Showroom');
        $this->shop_address = Setting::get('shop_address', '');
        $this->shop_email = Setting::get('shop_email', '');
        $this->shop_phone = Setting::get('shop_phone', '');
        $this->google_maps_url = Setting::get('google_maps_url', '');
        $this->map_latitude = Setting::get('map_latitude', '14.5995'); // Default to Manila if not set
        $this->map_longitude = Setting::get('map_longitude', '120.9842');
        $this->facebook_url = Setting::get('facebook_url', '');
        $this->instagram_url = Setting::get('instagram_url', '');
        $this->tiktok_url = Setting::get('tiktok_url', '');

        $logoPath = Setting::get('shop_logo');
        if ($logoPath) {
            $this->current_logo_url = Storage::url($logoPath);
        }
    }

    public function save(): void
    {
        $this->validate([
            'logo' => 'nullable|image|max:1024',
        ]);

        if ($this->logo) {
            $path = $this->logo->store('branding', 'public');
            
            // Delete old logo if exists
            $oldLogo = Setting::get('shop_logo');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }

            Setting::set('shop_logo', $path);
            $this->current_logo_url = Storage::url($path);
            $this->logo = null;
        }

        Setting::set('shop_name', $this->shop_name);
        Setting::set('shop_address', $this->shop_address);
        Setting::set('shop_email', $this->shop_email);
        Setting::set('shop_phone', $this->shop_phone);
        Setting::set('google_maps_url', $this->google_maps_url);
        Setting::set('map_latitude', $this->map_latitude);
        Setting::set('map_longitude', $this->map_longitude);
        Setting::set('facebook_url', $this->facebook_url);
        Setting::set('instagram_url', $this->instagram_url);
        Setting::set('tiktok_url', $this->tiktok_url);

        session()->flash('status', 'Shop settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.admin-shop-settings')
            ->layout('layouts.admin-panel', [
                'title' => 'Shop Settings',
            ]);
    }
}
