<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Services\BackupService;
use Illuminate\Support\Facades\Gate;

new class extends Component {
    use WithFileUploads;

    public $backupFile;
    public $showRestoreModal = false;

    public function rendering($view): void
    {
        $view->layout('layouts.admin-panel', ['title' => 'Backup & Restore']);
    }

    public function mount()
    {
        Gate::authorize('access-admin');
    }

    public function downloadBackup()
    {
        Gate::authorize('access-admin');
        return redirect()->route('admin.backup.download');
    }

    public function startRestore()
    {
        Gate::authorize('access-admin');
        
        $this->validate([
            'backupFile' => 'required|file|mimes:zip|max:51200', // 50MB max
        ]);

        $this->showRestoreModal = true;
    }

    public function performRestore(BackupService $backupService)
    {
        Gate::authorize('access-admin');

        try {
            $path = $this->backupFile->getRealPath();
            $backupService->restore($path);
            
            $this->showRestoreModal = false;
            $this->backupFile = null;

            session()->flash('success', 'Database and units restored successfully!');
            return redirect()->route('admin.backup.show');
        } catch (\Exception $e) {
            $this->showRestoreModal = false;
            session()->flash('error', 'Restore failed: ' . $e->getMessage());
        }
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-pages::settings.layout :heading="__('Backup & Restore')" :subheading="__('Securely backup your database and units, or restore from a previous backup.')">
        <div class="space-y-6">
            @if (session()->has('success'))
                <div class="rounded-xl bg-emerald-50 p-4 border border-emerald-100">
                    <p class="text-xs font-bold text-emerald-700">{{ session('success') }}</p>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="rounded-xl bg-red-50 p-4 border border-red-100">
                    <p class="text-xs font-bold text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Download Section -->
            <div class="p-6 rounded-2xl border border-zinc-100 bg-zinc-50/50">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex-1">
                        <h3 class="text-xs font-black uppercase tracking-widest text-zinc-900">{{ __('Export Data') }}</h3>
                        <p class="mt-1 text-[11px] font-bold text-zinc-400">{{ __('Generate a ZIP file containing your database and all vehicle images.') }}</p>
                    </div>
                    <flux:button wire:click="downloadBackup" variant="filled" class="bg-zinc-900 text-white hover:bg-zinc-800">
                        {{ __('Download ZIP') }}
                    </flux:button>
                </div>
            </div>

            <!-- Restore Section -->
            <div class="p-6 rounded-2xl border border-zinc-100 bg-zinc-50/50">
                <h3 class="text-xs font-black uppercase tracking-widest text-zinc-900 mb-4">{{ __('Import Data') }}</h3>
                
                <div class="space-y-4">
                    <flux:input 
                        type="file" 
                        wire:model="backupFile" 
                        label="Backup ZIP File" 
                        placeholder="Select a .zip backup"
                        description="Max size: 50MB. This will replace all current units and data."
                    />

                    <div class="flex justify-end">
                        <flux:button 
                            wire:click="startRestore" 
                            variant="filled" 
                            class="bg-red-600 text-white hover:bg-red-700"
                            :disabled="!$backupFile"
                        >
                            {{ __('Restore from Backup') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Restore Confirmation Modal -->
        <flux:modal name="confirm-restore" wire:model="showRestoreModal" class="max-w-md">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg" class="text-red-600 font-black uppercase tracking-widest">{{ __('Confirm Restoration') }}</flux:heading>
                    <flux:subheading class="mt-2 font-bold">
                        {{ __('Warning: This action will completely replace your current database and vehicle images. This cannot be undone.') }}
                    </flux:subheading>
                </div>

                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:modal.close>
                        <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>
                    <flux:button wire:click="performRestore" variant="filled" class="bg-red-600 text-white hover:bg-red-700">
                        {{ __('I Understand, Restore Now') }}
                    </flux:button>
                </div>
            </div>
        </flux:modal>
    </x-pages::settings.layout>
</section>
