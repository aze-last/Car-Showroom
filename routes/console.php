<?php

use App\Services\ZmotoCatalogImporter;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command(
    'showroom:import-zmoto
        {--query= : Filter imported items by product name}
        {--pages=1 : Number of /shop pages to scan}
        {--without-images : Skip downloading product photos}
        {--refresh-images : Replace existing imported photos}
        {--with-price : Import MXN numeric prices into the local price field}
        {--only-existing : Only update items that already exist in your database}
        {--dry-run : Scan and report matches without saving anything}',
    function (ZmotoCatalogImporter $importer): void {
        $result = $importer->import(
            query: $this->option('query') ?: null,
            pages: (int) $this->option('pages'),
            withImages: ! (bool) $this->option('without-images'),
            refreshImages: (bool) $this->option('refresh-images'),
            withPrice: (bool) $this->option('with-price'),
            dryRun: (bool) $this->option('dry-run'),
            onlyExisting: (bool) $this->option('only-existing'),
        );

        $this->table(
            ['Scanned', 'Matched', 'Created', 'Updated', 'Skipped'],
            [[
                $result['scanned'],
                $result['matched'],
                $result['created'],
                $result['updated'],
                $result['skipped'],
            ]],
        );

        if (! $this->option('with-price')) {
            $this->warn('Prices are skipped by default because the source catalog is in MXN while this app displays PHP.');
        }
    },
)->purpose('Import vehicle listings and photos from the ZMoto catalog');
