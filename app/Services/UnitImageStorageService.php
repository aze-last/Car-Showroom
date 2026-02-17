<?php

namespace App\Services;

use App\Models\Unit;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class UnitImageStorageService
{
    public function storeForUnit(Unit $unit, TemporaryUploadedFile $file): string
    {
        return $file->store(
            'units/'.$unit->id,
            $this->diskName(),
        );
    }

    public function delete(string $path): bool
    {
        return Storage::disk($this->diskName())->delete($path);
    }

    public function diskName(): string
    {
        return config('filesystems.default');
    }
}
