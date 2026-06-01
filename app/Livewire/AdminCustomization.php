<?php

namespace App\Livewire;

use App\Models\Setting;
use App\Models\Unit;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class AdminCustomization extends Component
{
    use WithFileUploads;

    public $palette;
    public $layout;
    public $logo;
    public $hero_unit_id;
    public $hero_headline;
    public $hero_subtitle;
    public $show_auctions;
    public $show_comparison;
    public $show_inquiries;

    public $logo_path;

    public function mount()
    {
        $this->palette = Setting::get('design_palette', 'emerald');
        $this->layout = Setting::get('design_layout', 'cinema');
        $this->logo_path = Setting::get('design_logo_path');
        $this->hero_unit_id = Setting::get('design_hero_unit_id');
        $this->hero_headline = Setting::get('design_hero_headline', 'Automotive Excellence');
        $this->hero_subtitle = Setting::get('design_hero_subtitle', 'Curated collection of precision engineered assets.');
        $this->show_auctions = Setting::get('design_show_auctions', true);
        $this->show_comparison = Setting::get('design_show_comparison', true);
        $this->show_inquiries = Setting::get('design_show_inquiries', true);
    }

    public function save()
    {
        $this->validate([
            'logo' => 'nullable|image|max:1024',
            'hero_headline' => 'required|string|max:100',
            'hero_subtitle' => 'required|string|max:255',
        ]);

        if ($this->logo) {
            // Delete old logo
            if ($this->logo_path) {
                Storage::delete($this->logo_path);
            }
            $this->logo_path = $this->logo->store('customization', 'public');
            Setting::set('design_logo_path', $this->logo_path);
        }

        Setting::set('design_palette', $this->palette);
        Setting::set('design_layout', $this->layout);
        Setting::set('design_hero_unit_id', $this->hero_unit_id);
        Setting::set('design_hero_headline', $this->hero_headline);
        Setting::set('design_hero_subtitle', $this->hero_subtitle);
        Setting::set('design_show_auctions', $this->show_auctions, 'boolean');
        Setting::set('design_show_comparison', $this->show_comparison, 'boolean');
        Setting::set('design_show_inquiries', $this->show_inquiries, 'boolean');

        session()->flash('status', 'Design settings updated successfully.');
        
        return redirect()->route('admin.customization');
    }

    public function render()
    {
        return view('livewire.admin-customization', [
            'units' => Unit::orderBy('name')->get(),
            'palettes' => config('showroom.design.palettes'),
            'layouts' => config('showroom.design.layouts'),
        ])->layout('layouts.admin-panel');
    }
}
