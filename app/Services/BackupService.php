<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use ZipArchive;

class BackupService
{
    protected string $backupPath;

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');
        if (! File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }
    }

    public function create(): string
    {
        $zipName = 'backup-'.now()->format('Y-m-d-H-i-s').'.zip';
        $zipPath = $this->backupPath.'/'.$zipName;

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \Exception("Could not create ZIP file at {$zipPath}");
        }

        // 1. Export Database
        $this->exportDatabase($zip);

        // 2. Add unit images
        $this->exportStorage($zip);

        $zip->close();

        return $zipPath;
    }

    protected function exportDatabase(ZipArchive $zip): void
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        if ($driver === 'sqlite') {
            $dbPath = config("database.connections.{$connection}.database");
            if (File::exists($dbPath)) {
                $zip->addFile($dbPath, 'database.sqlite');
            }
        } elseif ($driver === 'mysql') {
            $dbConfig = config("database.connections.{$connection}");
            $tempSql = storage_path('app/temp_dump.sql');

            // Build mysqldump command
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s --port=%s %s > %s',
                escapeshellarg($dbConfig['username']),
                escapeshellarg($dbConfig['password']),
                escapeshellarg($dbConfig['host']),
                escapeshellarg($dbConfig['port']),
                escapeshellarg($dbConfig['database']),
                escapeshellarg($tempSql)
            );

            // Using shell_exec because Process component might struggle with the redirect character on some systems
            shell_exec($command);

            if (File::exists($tempSql)) {
                $zip->addFile($tempSql, 'database.sql');
                // We'll delete the temp file after closing the zip in a better implementation,
                // but ZipArchive::addFile doesn't read immediately.
                // For simplicity here, we'll keep it until the end of the request.
            }
        }
    }

    protected function exportStorage(ZipArchive $zip): void
    {
        $unitsPath = storage_path('app/public/units');
        if (File::exists($unitsPath)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($unitsPath),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $file) {
                if (! $file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = 'units/'.substr($filePath, strlen($unitsPath) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
        }
    }

    public function restore(string $zipPath): void
    {
        $zip = new ZipArchive;
        if ($zip->open($zipPath) !== true) {
            throw new \Exception('Could not open ZIP file');
        }

        $tempPath = storage_path('app/temp_restore');
        if (File::exists($tempPath)) {
            File::deleteDirectory($tempPath);
        }
        File::makeDirectory($tempPath);

        $zip->extractTo($tempPath);
        $zip->close();

        try {
            // 1. Restore Database
            $this->importDatabase($tempPath);

            // 2. Restore Images
            $this->importStorage($tempPath);
        } finally {
            File::deleteDirectory($tempPath);
        }
    }

    protected function importDatabase(string $tempPath): void
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        if ($driver === 'sqlite') {
            $sqlFile = $tempPath.'/database.sqlite';
            if (File::exists($sqlFile)) {
                $dbPath = config("database.connections.{$connection}.database");
                File::copy($sqlFile, $dbPath);
            }
        } elseif ($driver === 'mysql') {
            $sqlFile = $tempPath.'/database.sql';
            if (File::exists($sqlFile)) {
                $dbConfig = config("database.connections.{$connection}");

                $command = sprintf(
                    'mysql --user=%s --password=%s --host=%s --port=%s %s < %s',
                    escapeshellarg($dbConfig['username']),
                    escapeshellarg($dbConfig['password']),
                    escapeshellarg($dbConfig['host']),
                    escapeshellarg($dbConfig['port']),
                    escapeshellarg($dbConfig['database']),
                    escapeshellarg($sqlFile)
                );

                shell_exec($command);
            }
        }
    }

    protected function importStorage(string $tempPath): void
    {
        $unitsPath = storage_path('app/public/units');
        $tempUnits = $tempPath.'/units';

        if (File::exists($tempUnits)) {
            if (File::exists($unitsPath)) {
                File::deleteDirectory($unitsPath);
            }
            File::copyDirectory($tempUnits, $unitsPath);
        }
    }
}
