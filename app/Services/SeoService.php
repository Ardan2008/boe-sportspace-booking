<?php

namespace App\Services;

class SeoService
{
    protected array $data = [];

    public function __construct()
    {
        $this->data = [
            'title' => config('seo.site_name'),
            'description' => config('seo.site_description'),
            'keywords' => config('seo.site_keywords'),
            'image' => config('seo.default_image'),
            'url' => url()->current(),
            'type' => 'website',
            'locale' => config('seo.locale'),
            'site_name' => config('seo.site_name'),
            'twitter_handle' => config('seo.twitter_handle'),
            'canonical' => url()->current(),
            'robots' => 'index, follow',
        ];
    }

    public function setTitle(string $title): static
    {
        $this->data['title'] = $title . ' | ' . config('seo.site_name');
        return $this;
    }

    public function setDescription(string $description): static
    {
        $this->data['description'] = $description;
        return $this;
    }

    public function setKeywords(string $keywords): static
    {
        $this->data['keywords'] = $keywords;
        return $this;
    }

    public function setImage(string $image): static
    {
        $this->data['image'] = $image;
        return $this;
    }

    public function setUrl(string $url): static
    {
        $this->data['url'] = $url;
        $this->data['canonical'] = $url;
        return $this;
    }

    public function setType(string $type): static
    {
        $this->data['type'] = $type;
        return $this;
    }

    public function setRobots(string $robots): static
    {
        $this->data['robots'] = $robots;
        return $this;
    }

    public function setCanonical(string $canonical): static
    {
        $this->data['canonical'] = $canonical;
        return $this;
    }

    public function setJsonLd(array $jsonLd): static
    {
        $this->data['jsonLd'] = $jsonLd;
        return $this;
    }

    public function get(): array
    {
        return $this->data;
    }

    public static function buildJsonLd(array $data): array
    {
        $default = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => config('seo.site_name'),
            'description' => config('seo.site_description'),
            'url' => url('/'),
        ];

        return array_merge($default, $data);
    }

    public static function buildBreadcrumbJsonLd(array $items): array
    {
        $list = [];
        foreach ($items as $i => $item) {
            $list[] = [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $item['name'],
                'item' => $item['url'],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $list,
        ];
    }

    public static function buildOrganizationJsonLd(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'BBPPMPV BOE Malang',
            'url' => url('/'),
            'logo' => url('/image/logo/tutwuri-logo.svg'),
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => '(0341) 123456',
                'contactType' => 'customer service',
                'areaServed' => 'ID',
            ],
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'Jl. Teluk Mandar Tromol Arjosari',
                'addressLocality' => 'Malang',
                'addressRegion' => 'Jawa Timur',
                'postalCode' => '65126',
                'addressCountry' => 'ID',
            ],
        ];
    }
}
