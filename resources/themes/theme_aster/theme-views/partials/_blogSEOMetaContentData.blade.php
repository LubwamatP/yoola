@if(isset($blogDetails) && isset($metaContentData))
    {{-- Title (from CMS or blog title) --}}
    @if($metaContentData?->title)
        <title>{{ $metaContentData?->title }}</title>
        <meta name="title" content="{{ $metaContentData?->title }}">
        <meta property="og:title" content="{{ $metaContentData?->title }}">
        <meta name="twitter:title" content="{{ $metaContentData?->title }}">
    @else
        <title>{{ $blogDetails?->name }}</title>
        <meta name="title" content="{{ $blogDetails?->name }}">
        <meta property="og:title" content="{{ $blogDetails?->name }}">
        <meta name="twitter:title" content="{{ $blogDetails?->name }}">
    @endif

    {{-- Meta description (tiered fallback: CMS > blog content > auto-generated) --}}
    @if($metaContentData?->description)
        <meta name="description" content="{{ Str::limit(strip_tags($metaContentData?->description), 160) }}">
        <meta property="og:description" content="{{ Str::limit(strip_tags($metaContentData?->description), 160) }}">
        <meta name="twitter:description" content="{{ Str::limit(strip_tags($metaContentData?->description), 160) }}">
    @else
        @php
            $blogDesc = $blogDetails->description ?? '';
            $fallbackDesc = strip_tags($blogDesc);
            if (strlen($fallbackDesc) < 50) {
                $fallbackDesc = $blogDetails->name . ' — read our guide and get expert advice. Free delivery in Kampala. Shop at Yoola.ug.';
            }
        @endphp
        <meta name="description" content="{{ Str::limit($fallbackDesc, 160) }}">
        <meta property="og:description" content="{{ Str::limit($fallbackDesc, 160) }}">
        <meta name="twitter:description" content="{{ Str::limit($fallbackDesc, 160) }}">
    @endif

    {{-- Canonical + OG/Twitter URL --}}
    <link rel="canonical" href="{{ url('/blog/' . $blogDetails->slug) }}">
    <meta property="og:url" content="{{ url('/blog/' . $blogDetails->slug) }}">
    <meta name="twitter:url" content="{{ url('/blog/' . $blogDetails->slug) }}">

    {{-- OG type + twitter card --}}
    <meta property="og:type" content="article">
    <meta name="twitter:card" content="summary_large_image">
    @if($blogDetails->created_at)
    <meta property="article:published_time" content="{{ $blogDetails->created_at->toIso8601String() }}">
    @endif
    @if($blogDetails->updated_at)
    <meta property="article:modified_time" content="{{ $blogDetails->updated_at->toIso8601String() }}">
    @endif

    {{-- Author --}}
    @if($blogDetails->added_by == 'seller')
        <meta name="author" content="{{ $blogDetails->seller->shop?$blogDetails->seller->shop->name:$blogDetails->seller->f_name }}">
    @elseif($blogDetails->added_by == 'admin')
        <meta name="author" content="{{ $web_config['company_name'] }}">
    @endif

    {{-- OG image with fallback --}}
    @php
        $blogOgImage = $metaContentData?->image_full_url['path']
            ?? $blogDetails->thumbnail_full_url['path']
            ?? '';
    @endphp
    @if($blogOgImage)
    <meta property="og:image" content="{{ $blogOgImage }}">
    <meta name="twitter:image" content="{{ $blogOgImage }}">
    @endif

    {{-- SINGLE combined robots tag --}}
    @php
        $blogRobots = [];
        $isIndexed = (!isset($metaContentData->index) || $metaContentData->index != 'noindex');
        $blogRobots[] = $isIndexed ? 'index' : 'noindex';
        $blogRobots[] = ($metaContentData?->no_follow ?? false) ? 'nofollow' : 'follow';
        if ($metaContentData?->no_image_index ?? false) $blogRobots[] = 'noimageindex';
        if ($metaContentData?->no_archive ?? false) $blogRobots[] = 'noarchive';
        if ($metaContentData?->no_snippet ?? false) $blogRobots[] = 'nosnippet';
        if ($metaContentData?->meta_max_snippet) {
            $blogRobots[] = 'max-snippet' . ($metaContentData?->max_snippet_value ? ': ' . $metaContentData?->max_snippet_value : '');
        }
        if ($metaContentData?->max_video_preview) {
            $blogRobots[] = 'max-video-preview' . ($metaContentData?->max_video_preview_value ? ': ' . $metaContentData?->max_video_preview_value : '');
        }
        if ($metaContentData?->max_image_preview) {
            $blogRobots[] = 'max-image-preview' . ($metaContentData?->max_image_preview_value ? ': ' . $metaContentData?->max_image_preview_value : '');
        }
    @endphp
    <meta name="robots" content="{{ implode(', ', $blogRobots) }}">

    {{-- BlogPosting Schema (JSON-LD) --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BlogPosting",
        "headline": "{{ $metaContentData?->title ?? $blogDetails?->name }}",
        "description": "{{ Str::limit(strip_tags($metaContentData?->description ?? $blogDetails->description ?? ''), 200) }}",
        "image": "{{ $metaContentData?->image_full_url['path'] ?? $blogDetails->thumbnail_full_url['path'] ?? '' }}",
        "author": {
            "@type": "Person",
            "name": "{{ $blogDetails->added_by == 'seller' ? ($blogDetails->seller->shop->name ?? 'Yoola') : 'Yoola' }}"
        },
        "publisher": {
            "@type": "Organization",
            "name": "{{ $web_config['company_name'] ?? 'Yoola' }}"
        },
        "datePublished": "{{ $blogDetails->created_at ? $blogDetails->created_at->toIso8601String() : date('c') }}",
        "dateModified": "{{ $blogDetails->updated_at ? $blogDetails->updated_at->toIso8601String() : date('c') }}",
        "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": "{{ url('/blog/' . $blogDetails->slug) }}"
        }
    }
    </script>

    {{-- hreflang alternates --}}
    @foreach($blogDetails->translations->unique('locale') as $translation)
        <link rel="alternate" type="text/html" hreflang="{{ getLanguageCode(country_code: $translation->locale) }}"
              href="{{ route('frontend.blog.details', ['slug' => $blogDetails->slug, 'locale' => $translation->locale]) }}" title="{{ $blogDetails->title }}"/>
    @endforeach
@endif
