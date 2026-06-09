@php
    use Carbon\Carbon;
    $today = Carbon::today()->toDateString();
@endphp
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/') }}</loc>
        <priority>1.0</priority>
        <changefreq>daily</changefreq>
    </url>
    <url>
        <loc>{{ url('/schedule_booking') }}</loc>
        <priority>0.8</priority>
        <changefreq>daily</changefreq>
    </url>
    <url>
        <loc>{{ url('/formBooking') }}</loc>
        <priority>0.7</priority>
        <changefreq>weekly</changefreq>
    </url>
    @foreach($facilities as $fasilitas)
    <url>
        <loc>{{ url('/fasilitas/' . $fasilitas->id . '/detail') }}</loc>
        <lastmod>{{ $fasilitas->updated_at ? $fasilitas->updated_at->toDateString() : $today }}</lastmod>
        <priority>0.9</priority>
        <changefreq>weekly</changefreq>
    </url>
    @endforeach
</urlset>
