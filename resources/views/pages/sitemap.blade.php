<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ route('home') }}</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ route('about') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>{{ route('comparison') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    @if(\App\Models\Setting::get('design_show_auctions', true))
    <url>
        <loc>{{ route('auction.hall') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    @endif
    @foreach($units as $unit)
    <url>
        <loc>{{ route('units.show', $unit) }}</loc>
        <lastmod>{{ $unit->updated_at->tz('UTC')->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach
</urlset>
