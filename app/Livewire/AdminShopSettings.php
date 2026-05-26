<?php

namespace App\Livewire;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class AdminShopSettings extends Component
{
    use WithFileUploads;

    public string $legal_name = '';
    public string $dba_name = '';
    public string $shop_address = '';
    public string $shop_city = '';
    public string $shop_state = '';
    public string $shop_postal_code = '';

    public string $s3_bucket = '';
    public string $s3_region = '';

    public string $primary_color = '#000000';
    public string $accent_tone = '#565e74';

    public string $sales_inquiry_email = '';
    public string $service_inquiry_email = '';
    public string $legal_inquiry_email = '';

    public string $facebook_url = '';
    public string $instagram_url = '';
    public string $tiktok_url = '';

    public $logo;
    public ?string $current_logo_url = null;

    public function mount(): void
    {
        $this->legal_name = Setting::get('legal_name', 'The Gallery Automotive Group, LLC');
        $this->dba_name = Setting::get('dba_name', Setting::get('shop_name', 'The Gallery'));
        $this->shop_address = Setting::get('shop_address', '');
        $this->shop_city = Setting::get('shop_city', 'Beverly Hills');
        $this->shop_state = Setting::get('shop_state', 'CA');
        $this->shop_postal_code = Setting::get('shop_postal_code', '90210');

        $this->s3_bucket = config('filesystems.disks.s3.bucket', 'gallery-assets-prod');
        $this->s3_region = config('filesystems.disks.s3.region', 'us-west-1');

        $this->primary_color = Setting::get('primary_color', '#000000');
        $this->accent_tone = Setting::get('accent_tone', '#565e74');

        $this->sales_inquiry_email = Setting::get('sales_inquiry_email', Setting::get('shop_email', 'acquisitions@thegallery.com'));
        $this->service_inquiry_email = Setting::get('service_inquiry_email', 'concierge@thegallery.com');
        $this->legal_inquiry_email = Setting::get('legal_inquiry_email', 'legal@thegallery.com');

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
            'logo' => 'nullable|image|max:2048',
            'sales_inquiry_email' => 'required|email',
            'service_inquiry_email' => 'nullable|email',
            'legal_inquiry_email' => 'nullable|email',
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

        Setting::set('legal_name', $this->legal_name);
        Setting::set('dba_name', $this->dba_name);
        Setting::set('shop_name', $this->dba_name); // Keep sync with legacy shop_name
        Setting::set('shop_address', $this->shop_address);
        Setting::set('shop_city', $this->shop_city);
        Setting::set('shop_state', $this->shop_state);
        Setting::set('shop_postal_code', $this->shop_postal_code);

        Setting::set('primary_color', $this->primary_color);
        Setting::set('accent_tone', $this->accent_tone);

        Setting::set('sales_inquiry_email', $this->sales_inquiry_email);
        Setting::set('shop_email', $this->sales_inquiry_email); // Legacy sync
        Setting::set('service_inquiry_email', $this->service_inquiry_email);
        Setting::set('legal_inquiry_email', $this->legal_inquiry_email);

        Setting::set('facebook_url', $this->facebook_url);
        Setting::set('instagram_url', $this->instagram_url);
        Setting::set('tiktok_url', $this->tiktok_url);

        session()->flash('status', 'Global configurations updated successfully.');
    }

    public function render()
    {
        return view('livewire.admin-shop-settings')
            ->layout('layouts.admin-panel', [
                'title' => 'Global Settings',
            ]);
    }
}
