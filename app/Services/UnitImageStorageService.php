<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class UnitImageStorageService
{
    public function store(TemporaryUploadedFile $file, string $directory): string
    {
        return $file->store(
            $directory,
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
