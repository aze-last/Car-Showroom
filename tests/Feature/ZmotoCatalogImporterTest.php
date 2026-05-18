<?php

use App\Models\Unit;
use App\Services\ZmotoCatalogImporter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

it('imports a vehicle listing and downloads its images', function (): void {
    Storage::fake('public');
    config()->set('filesystems.default', 'public');

    Http::fake([
        'https://www.zmoto.com.mx/shop' => Http::response(<<<'HTML'
            <html><body>
                <a class="text-reset" href="/shop/runga-pasajeros-23">RUNGA PASAJEROS</a>
            </body></html>
        HTML, 200),
        'https://www.zmoto.com.mx/shop/runga-pasajeros-23' => Http::response(<<<'HTML'
            <html><body>
                <h1>RUNGA PASAJEROS</h1>
                <div id="product_details">
                    <p>Passenger tricycle with utility seating.</p>
                    <span class="oe_currency_value">78,999.00</span>
                    <h2>Informacion tecnica de la unidad</h2>
                </div>
                <img src="/web/image/product.template/23/image_1920?unique=a87d311" />
                <img src="/web/image/product.image/46/image_1024/image_1920?unique=abc123" />
            </body></html>
        HTML, 200),
        'https://www.zmoto.com.mx/web/image/*' => Http::response('fake-image-binary', 200, [
            'Content-Type' => 'image/jpeg',
        ]),
    ]);

    $result = app(ZmotoCatalogImporter::class)->import(
        query: 'runga',
        pages: 1,
        withImages: true,
        refreshImages: false,
        withPrice: false,
        dryRun: false,
    );

    expect($result['matched'])->toBe(1)
        ->and($result['created'])->toBe(1);

    $unit = Unit::query()->first();

    expect($unit)->not->toBeNull()
        ->and($unit->name)->toBe('RUNGA PASAJEROS')
        ->and($unit->source_name)->toBe('zmoto')
        ->and($unit->source_external_id)->toBe('23')
        ->and($unit->show_price)->toBeFalse()
        ->and($unit->images()->count())->toBe(2);

    Storage::disk('public')->assertExists($unit->images()->first()->url);
});

it('skips non-vehicle catalog items', function (): void {
    Storage::fake('public');
    config()->set('filesystems.default', 'public');

    Http::fake([
        'https://www.zmoto.com.mx/shop' => Http::response(<<<'HTML'
            <html><body>
                <a class="text-reset" href="/shop/goma-de-base-de-faro-runga-250-999">GOMA DE BASE</a>
            </body></html>
        HTML, 200),
        'https://www.zmoto.com.mx/shop/goma-de-base-de-faro-runga-250-999' => Http::response(<<<'HTML'
            <html><body>
                <h1>GOMA DE BASE</h1>
                <div id="product_details">
                    <p>Replacement part.</p>
                    <span class="oe_currency_value">6.43</span>
                </div>
                <img src="/web/image/product.template/999/image_1920?unique=a87d311" />
            </body></html>
        HTML, 200),
    ]);

    $result = app(ZmotoCatalogImporter::class)->import(
        query: null,
        pages: 1,
        withImages: true,
        refreshImages: false,
        withPrice: false,
        dryRun: false,
    );

    expect($result['matched'])->toBe(0)
        ->and(Unit::query()->count())->toBe(0);
});
