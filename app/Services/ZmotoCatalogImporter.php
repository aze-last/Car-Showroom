<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Unit;
use App\Models\UnitImage;
use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXPath;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ZmotoCatalogImporter
{
    private const BASE_URL = 'https://www.zmoto.com.mx';

    /**
     * @return array{scanned: int, matched: int, created: int, updated: int, skipped: int}
     */
    public function import(
        ?string $query = null,
        int $pages = 1,
        bool $withImages = true,
        bool $refreshImages = false,
        bool $withPrice = false,
        bool $dryRun = false,
        bool $onlyExisting = false,
    ): array {
        $pages = max(1, $pages);
        $normalizedQuery = $this->normalize($query);
        $scanned = 0;
        $matched = 0;
        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($this->collectProductUrls($pages, $query) as $productUrl) {
            $scanned++;
            $product = $this->fetchProduct($productUrl);

            if ($product === null || ! $product['is_vehicle']) {
                $skipped++;

                continue;
            }

            if ($normalizedQuery !== null && ! str_contains($this->normalize($product['name']), $normalizedQuery)) {
                $skipped++;

                continue;
            }

            $matched++;

            if ($dryRun) {
                continue;
            }

            $result = $this->upsertUnit(
                product: $product,
                withImages: $withImages,
                refreshImages: $refreshImages,
                withPrice: $withPrice,
                onlyExisting: $onlyExisting,
            );

            if ($result === 'created') {
                $created++;
            } elseif ($result === 'updated') {
                $updated++;
            } else {
                $skipped++;
            }
        }

        return [
            'scanned' => $scanned,
            'matched' => $matched,
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
        ];
    }

    /**
     * @return Collection<int, string>
     */
    private function collectProductUrls(int $pages, ?string $query = null): Collection
    {
        $urls = collect();
        $searchParam = $query ? '?search='.urlencode($query) : '';

        for ($page = 1; $page <= $pages; $page++) {
            $path = $page === 1 ? '/shop'.$searchParam : '/shop/page/'.$page.$searchParam;
            $html = $this->getHtml(self::BASE_URL.$path);
            $xpath = $this->makeXPath($html);
// ... (omitting unchanged lines for brevity in instruction, but will provide full block)

            foreach ($xpath->query('//form[contains(@class, "oe_product_cart")]//a[contains(@href, "/shop/")] | //a[contains(@href, "/shop/")]') as $anchor) {
                if (! $anchor instanceof DOMElement) {
                    continue;
                }

                $href = trim((string) $anchor->getAttribute('href'));
                if (
                    $href === ''
                    || str_contains($href, '/shop/category/')
                    || str_contains($href, '/shop/cart')
                ) {
                    continue;
                }

                $urls->push($this->absoluteUrl($href));
            }
        }

        return $urls->unique()->values();
    }

    /**
     * @return array{
     *     name: string,
     *     description: string,
     *     source_url: string,
     *     source_external_id: string,
     *     category_name: string,
     *     price: ?int,
     *     image_urls: array<int, string>,
     *     is_vehicle: bool
     * }|null
     */
    private function fetchProduct(string $url): ?array
    {
        $html = $this->getHtml($url);
        $xpath = $this->makeXPath($html);

        $name = $this->firstNodeText($xpath, '//h1');
        if ($name === null) {
            return null;
        }

        $description = $this->firstNodeText(
            $xpath,
            '//*[contains(@class, "product_description")]//p | //div[@id="product_details"]//p',
        ) ?? '';

        $priceText = $this->firstNodeText($xpath, '//*[contains(@class, "oe_currency_value")]');
        $sourceExternalId = $this->extractExternalId($url);
        $imageUrls = $this->extractImageUrls($xpath);
        $isVehicle = $this->isVehicleProduct($xpath, $imageUrls, $description);

        return [
            'name' => $name,
            'description' => $description,
            'source_url' => $url,
            'source_external_id' => $sourceExternalId,
            'category_name' => $this->inferCategoryName($name, $description),
            'price' => $this->parsePriceToInt($priceText),
            'image_urls' => $imageUrls,
            'is_vehicle' => $isVehicle,
        ];
    }

    /**
     * @param  array{
     *     name: string,
     *     description: string,
     *     source_url: string,
     *     source_external_id: string,
     *     category_name: string,
     *     price: ?int,
     *     image_urls: array<int, string>,
     *     is_vehicle: bool
     * }  $product
     */
    private function upsertUnit(
        array $product,
        bool $withImages,
        bool $refreshImages,
        bool $withPrice,
        bool $onlyExisting = false,
    ): string {
        $unit = Unit::query()
            ->where('source_name', 'zmoto')
            ->where('source_external_id', $product['source_external_id'])
            ->first();

        $wasRecentlyCreated = false;

        if ($unit === null) {
            $unit = Unit::query()
                ->where('name', $product['name'])
                ->first();
        }

        if ($unit === null) {
            if ($onlyExisting) {
                return 'skipped';
            }

            $unit = new Unit;
            $wasRecentlyCreated = true;
            $unit->status = Unit::STATUS_AVAILABLE;
        }

        $category = Category::query()->firstOrCreate([
            'name' => $product['category_name'],
        ]);

        $unit->fill([
            'category_id' => $category->id,
            'name' => $product['name'],
            'price_php' => $withPrice ? $product['price'] : null,
            'description' => $product['description'] !== '' ? $product['description'] : null,
            'show_price' => $withPrice && $product['price'] !== null,
            'source_name' => 'zmoto',
            'source_external_id' => $product['source_external_id'],
            'source_url' => $product['source_url'],
        ]);
        $unit->save();

        if ($withImages) {
            $this->syncImages($unit, $product['image_urls'], $refreshImages);
        }

        return $wasRecentlyCreated ? 'created' : 'updated';
    }

    /**
     * @param  array<int, string>  $imageUrls
     */
    private function syncImages(Unit $unit, array $imageUrls, bool $refreshImages): void
    {
        if ($imageUrls === []) {
            return;
        }

        $disk = Storage::disk(config('filesystems.default'));

        if (! $refreshImages && $unit->images()->exists()) {
            return;
        }

        if ($refreshImages) {
            foreach ($unit->images as $image) {
                $disk->delete($image->url);
                $image->delete();
            }
        }

        foreach (array_values($imageUrls) as $index => $imageUrl) {
            $response = Http::timeout(30)->get($imageUrl);
            if (! $response->successful()) {
                continue;
            }

            $extension = $this->extensionFromResponse(
                $response->header('Content-Type'),
                $imageUrl,
            );

            $path = 'units/'.$unit->id.'/'.Str::uuid().'.'.$extension;
            $disk->put($path, $response->body());

            UnitImage::query()->create([
                'unit_id' => $unit->id,
                'url' => $path,
                'sort_order' => $index,
            ]);
        }
    }

    /**
     * @return array<int, string>
     */
    private function extractImageUrls(DOMXPath $xpath): array
    {
        $urls = [];

        /** @var DOMNodeList<DOMElement> $images */
        $images = $xpath->query('//img');

        foreach ($images as $image) {
            $src = trim((string) $image->getAttribute('src'));
            if ($src === '' || ! str_contains($src, '/web/image/')) {
                continue;
            }

            if (
                ! str_contains($src, 'product.template')
                && ! str_contains($src, 'product.image/')
            ) {
                continue;
            }

            $urls[] = $this->absoluteUrl($src);
        }

        return array_values(array_unique($urls));
    }

    private function isVehicleProduct(DOMXPath $xpath, array $imageUrls, string $description): bool
    {
        $pageText = $this->normalize($xpath->document->textContent ?? '');

        if (str_contains($pageText, 'informacion tecnica de la unidad')) {
            return true;
        }

        if (str_contains($pageText, 'motor') && str_contains($pageText, 'chasis')) {
            return true;
        }

        return count($imageUrls) > 1 && $description !== '';
    }

    private function inferCategoryName(string $name, string $description): string
    {
        $text = $this->normalize($name.' '.$description);

        if (
            str_contains($text, 'runga')
            || str_contains($text, 'carguero')
            || str_contains($text, 'pasajeros')
        ) {
            return 'Vans';
        }

        return 'Motorcycle';
    }

    private function getHtml(string $url): string
    {
        $response = Http::timeout(30)
            ->withHeaders([
                'User-Agent' => 'Car-Showroom ZMoto importer',
            ])
            ->get($url);

        if ($response->status() === 404) {
            return '';
        }

        $response->throw();

        return $response->body();
    }

    private function makeXPath(string $html): DOMXPath
    {
        $document = new DOMDocument;
        @$document->loadHTML($html !== '' ? $html : '<html></html>');

        return new DOMXPath($document);
    }

    private function firstNodeText(DOMXPath $xpath, string $expression): ?string
    {
        $nodes = $xpath->query($expression);
        if ($nodes === false || $nodes->length === 0) {
            return null;
        }

        $text = trim((string) $nodes->item(0)?->textContent);

        return $text !== '' ? preg_replace('/\s+/u', ' ', $text) : null;
    }

    private function parsePriceToInt(?string $value): ?int
    {
        if ($value === null) {
            return null;
        }

        preg_match('/[\d,.]+/', $value, $matches);
        if (! isset($matches[0])) {
            return null;
        }

        $normalized = str_replace(',', '', $matches[0]);

        return (int) round((float) $normalized);
    }

    private function extractExternalId(string $url): string
    {
        if (preg_match('/-(\d+)(?:\?.*)?$/', $url, $matches) === 1) {
            return $matches[1];
        }

        return md5($url);
    }

    private function absoluteUrl(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return rtrim(self::BASE_URL, '/').'/'.ltrim($path, '/');
    }

    private function extensionFromResponse(?string $contentType, string $url): string
    {
        $contentType = strtolower((string) $contentType);

        return match (true) {
            str_contains($contentType, 'png') => 'png',
            str_contains($contentType, 'webp') => 'webp',
            str_contains($contentType, 'gif') => 'gif',
            str_contains($contentType, 'jpeg'),
            str_contains($contentType, 'jpg') => 'jpg',
            default => pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION) ?: 'jpg',
        };
    }

    private function normalize(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $ascii = Str::of($value)->ascii()->lower()->squish()->value();

        return $ascii !== '' ? $ascii : null;
    }
}
