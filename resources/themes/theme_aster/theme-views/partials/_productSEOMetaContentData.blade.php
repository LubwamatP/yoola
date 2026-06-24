@if(isset($productDetails) && isset($metaContentData))
    @php
        // Build brand, category, final price for meta tags
        $brandName = $productDetails->brand->name ?? 'Yoola';
        $catName = $productDetails->category->name ?? 'Electronics';
        $basePrice = $productDetails->unit_price ?? 0;
        $finalPrice = $basePrice;
        if (($productDetails->discount ?? 0) > 0) {
            if (($productDetails->discount_type ?? '') == 'percent') {
                $finalPrice = $basePrice - ($basePrice * $productDetails->discount / 100);
            } else {
                $finalPrice = max(0, $basePrice - $productDetails->discount);
            }
        }
    @endphp

    {{-- Title (from CMS or product name) --}}
    @if($metaContentData?->title)
        <title>{{ $metaContentData?->title }}</title>
        <meta name="title" content="{{ $metaContentData?->title }}">
        <meta property="og:title" content="{{ $metaContentData?->title }}">
        <meta name="twitter:title" content="{{ $metaContentData?->title }}">
    @else
        <title>{{ $productDetails?->name }}</title>
        <meta name="title" content="{{ $productDetails?->name }}">
        <meta property="og:title" content="{{ $productDetails?->name }}">
        <meta name="twitter:title" content="{{ $productDetails?->name }}">
    @endif

    {{-- Meta description (tiered fallback: CMS > product details > auto-generated) --}}
    @if($metaContentData?->description)
        <meta name="description" content="{{ Str::limit(strip_tags($metaContentData?->description), 160) }}">
        <meta property="og:description" content="{{ Str::limit(strip_tags($metaContentData?->description), 160) }}">
        <meta name="twitter:description" content="{{ Str::limit(strip_tags($metaContentData?->description), 160) }}">
    @else
        @php
            $prodDetails = strip_tags($productDetails->details ?? '');
            if (strlen($prodDetails) >= 50) {
                $fallbackProductDesc = 'Buy ' . $productDetails->name . ' in Uganda. ' . Str::limit($prodDetails, 120) . '. Genuine with warranty. Free delivery Kampala.';
            } else {
                $fallbackProductDesc = 'Buy ' . $productDetails->name . ' in Uganda. Genuine ' . $brandName . ' ' . $catName . '. Best price with warranty. Free delivery Kampala. Shop at Yoola.ug';
            }
        @endphp
        <meta name="description" content="{{ Str::limit($fallbackProductDesc, 160) }}">
        <meta property="og:description" content="{{ Str::limit($fallbackProductDesc, 160) }}">
        <meta name="twitter:description" content="{{ Str::limit($fallbackProductDesc, 160) }}">
    @endif

    {{-- Canonical + OG/Twitter URL --}}
    <link rel="canonical" href="{{ url('/product/' . $productDetails->slug) }}">
    <meta property="og:url" content="{{ url('/product/' . $productDetails->slug) }}">
    <meta name="twitter:url" content="{{ url('/product/' . $productDetails->slug) }}">

    {{-- OG type + product metadata + twitter card --}}
    <meta property="og:type" content="product">
    <meta name="twitter:card" content="summary_large_image">
    <meta property="product:price:amount" content="{{ round($finalPrice) }}">
    <meta property="product:price:currency" content="UGX">
    <meta property="product:brand" content="{{ $brandName }}">
    <meta property="product:availability" content="{{ ($productDetails->current_stock ?? 0) > 0 ? 'in stock' : 'out of stock' }}">

    {{-- OG image with fallback --}}
    @php
        $productOgImage = $metaContentData?->image_full_url['path']
            ?? $productDetails->thumbnail_full_url['path']
            ?? '';
    @endphp
    @if($productOgImage)
    <meta property="og:image" content="{{ $productOgImage }}">
    <meta name="twitter:image" content="{{ $productOgImage }}">
    @endif

    {{-- Author --}}
    @if($productDetails->added_by == 'seller')
        <meta name="author" content="{{ $productDetails->seller->shop?$productDetails->seller->shop->name:$productDetails->seller->f_name }}">
    @elseif($productDetails->added_by == 'admin')
        <meta name="author" content="{{ $web_config['company_name'] }}">
    @endif

    {{-- SINGLE combined robots tag --}}
    @php
        $productRobots = [];
        $isIndexed = (!isset($metaContentData->index) || $metaContentData->index != 'noindex');
        $productRobots[] = $isIndexed ? 'index' : 'noindex';
        $productRobots[] = ($metaContentData?->no_follow ?? false) ? 'nofollow' : 'follow';
        if ($metaContentData?->no_image_index ?? false) $productRobots[] = 'noimageindex';
        if ($metaContentData?->no_archive ?? false) $productRobots[] = 'noarchive';
        if ($metaContentData?->no_snippet ?? false) $productRobots[] = 'nosnippet';
        if ($metaContentData?->meta_max_snippet) {
            $productRobots[] = 'max-snippet' . ($metaContentData?->max_snippet_value ? ': ' . $metaContentData?->max_snippet_value : '');
        }
        if ($metaContentData?->max_video_preview) {
            $productRobots[] = 'max-video-preview' . ($metaContentData?->max_video_preview_value ? ': ' . $metaContentData?->max_video_preview_value : '');
        }
        if ($metaContentData?->max_image_preview) {
            $productRobots[] = 'max-image-preview' . ($metaContentData?->max_image_preview_value ? ': ' . $metaContentData?->max_image_preview_value : '');
        }
    @endphp
    <meta name="robots" content="{{ implode(', ', $productRobots) }}">
@endif
