@props([
    'title' => config('seo.site_name'),
    'description' => config('seo.site_description'),
    'keywords' => config('seo.site_keywords'),
    'image' => config('seo.default_image'),
    'url' => url()->current(),
    'type' => 'website',
    'locale' => config('seo.locale'),
    'siteName' => config('seo.site_name'),
    'twitterHandle' => config('seo.twitter_handle'),
    'canonical' => url()->current(),
    'robots' => 'index, follow',
    'jsonLd' => [],
])

<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
<meta name="keywords" content="{{ $keywords }}">
<meta name="robots" content="{{ $robots }}">
<link rel="canonical" href="{{ $canonical }}">

<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $url }}">
<meta property="og:image" content="{{ url($image) }}">
<meta property="og:type" content="{{ $type }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:locale" content="{{ $locale }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ url($image) }}">
@if($twitterHandle)
<meta name="twitter:site" content="{{ $twitterHandle }}">
@endif

@if(!empty($jsonLd))
<script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endif
