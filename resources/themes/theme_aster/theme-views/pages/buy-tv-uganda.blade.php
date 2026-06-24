@extends('theme-views.layouts.app')

@section('title', 'Buy TV in Uganda | Best Prices on Smart TVs | Yoola')

@section('content')
<style>
    .hero-section { background: linear-gradient(135deg, #1a1a2e 0%, #2d2d44 100%); padding: 50px 20px; text-align: center; }
    .hero-section h1 { color: #fff; font-size: 2.2rem; font-weight: 700; margin-bottom: 15px; }
    .hero-section p { color: #ccc; font-size: 1.1rem; max-width: 650px; margin: 0 auto 25px; }
    .hero-section .btn-danger { background: #C41E3A; border-color: #C41E3A; }
    .size-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; padding: 40px 20px; max-width: 900px; margin: 0 auto; }
    .size-card { background: #fff; border-radius: 10px; padding: 25px 15px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.08); transition: all 0.3s; text-decoration: none; color: inherit; border-bottom: 3px solid #C41E3A; }
    .size-card:hover { transform: translateY(-5px); box-shadow: 0 5px 20px rgba(0,0,0,0.15); }
    .size-card .size { font-size: 2rem; font-weight: 700; color: #C41E3A; }
    .size-card span { display: block; color: #666; margin-top: 5px; }
    .brand-section { background: #fef5f5; padding: 50px 20px; }
    .brand-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; max-width: 1000px; margin: 0 auto; }
    .brand-card { background: #fff; border-radius: 10px; padding: 25px; text-align: center; text-decoration: none; color: inherit; border-left: 4px solid #C41E3A; }
    .brand-card:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    .brand-card h3 { margin-bottom: 10px; color: #C41E3A; }
    .brand-card p { color: #666; font-size: 0.9rem; }
    .section-title { text-align: center; padding: 40px 20px 20px; }
    .section-title h2 { font-size: 1.8rem; font-weight: 700; }
    .faq-section { padding: 50px 20px; max-width: 800px; margin: 0 auto; }
    .faq-item { margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
    .faq-item h3 { font-size: 1.1rem; margin-bottom: 10px; }
    .faq-item p { color: #666; line-height: 1.6; }
    .cta-bar { background: #C41E3A; padding: 25px; text-align: center; }
    .cta-bar span { color: #fff; }
    .cta-bar a { color: #fff; font-weight: 600; margin: 0 15px; text-decoration: underline; }
</style>

<div class="hero-section">
    <h1>Buy TV in Uganda</h1>
    <p>Find the perfect television for your home. Smart TVs, 4K QLED, LED screens from trusted brands. Best prices with free delivery in Kampala.</p>
    <a href="{{ route('products') }}?category=entertainment" class="btn btn-danger btn-lg">Shop All TVs</a>
</div>

<div class="section-title">
    <h2>Shop by Screen Size</h2>
</div>

<div class="size-grid">
    <a href="{{ url('/prices/32-inch-tv-uganda') }}" class="size-card">
        <div class="size">32"</div>
        <span>Bedroom / Kitchen</span>
    </a>
    <a href="{{ url('/prices/43-inch-tv-uganda') }}" class="size-card">
        <div class="size">43"</div>
        <span>Small Living Room</span>
    </a>
    <a href="{{ url('/prices/50-inch-tv-uganda') }}" class="size-card">
        <div class="size">50"</div>
        <span>Medium Room</span>
    </a>
    <a href="{{ url('/prices/55-inch-tv-uganda') }}" class="size-card">
        <div class="size">55"</div>
        <span>Large Living Room</span>
    </a>
    <a href="{{ url('/prices/65-inch-tv-uganda') }}" class="size-card">
        <div class="size">65"</div>
        <span>Home Cinema</span>
    </a>
</div>

<div class="brand-section">
    <div class="section-title">
        <h2>Shop by Brand</h2>
    </div>
    <div class="brand-grid">
        <a href="{{ route('samsung.hub') }}" class="brand-card">
            <h3>Samsung</h3>
            <p>QLED, Crystal UHD, Smart TVs. Premium quality.</p>
        </a>
        <a href="{{ route('hisense.hub') }}" class="brand-card">
            <h3>Hisense</h3>
            <p>VIDAA Smart TVs. Best value for money.</p>
        </a>
        <a href="{{ url('/prices/chiq-tv-uganda') }}" class="brand-card">
            <h3>CHiQ</h3>
            <p>Android TV. Affordable smart features.</p>
        </a>
        <a href="{{ route('products') }}?brand=tcl" class="brand-card">
            <h3>TCL</h3>
            <p>Google TV & Roku. Great picture quality.</p>
        </a>
    </div>
</div>

<div class="faq-section">
    <h2 style="text-align:center; margin-bottom:30px;">TV Buying Guide</h2>
    
    <div class="faq-item">
        <h3>What size TV should I buy for my room?</h3>
        <p>For bedrooms, 32-43 inch is ideal. For living rooms, go for 50-55 inch. For a home cinema experience, 65 inch or larger. Measure your viewing distance — you should sit about 1.5x the screen size away.</p>
    </div>
    
    <div class="faq-item">
        <h3>What is the best TV brand in Uganda?</h3>
        <p>Samsung offers premium quality with QLED technology. Hisense provides excellent value with VIDAA smart features. CHiQ and TCL are great budget options with Android TV built-in.</p>
    </div>
    
    <div class="faq-item">
        <h3>Do I need a Smart TV?</h3>
        <p>Smart TVs let you stream Netflix, YouTube, and apps directly without extra devices. In Uganda, most new TVs are smart TVs. We recommend getting one for the convenience.</p>
    </div>
    
    <div class="faq-item">
        <h3>How much does a TV cost in Uganda?</h3>
        <p>32" TVs start from around UGX 350,000. 43" Smart TVs from UGX 600,000. 55" 4K TVs from UGX 1,200,000. Premium Samsung QLED TVs from UGX 2,500,000.</p>
    </div>
</div>

<div class="cta-bar">
    <span>Need help choosing? </span>
    <a href="https://wa.me/256704229768?text=Hi%20Yoola,%20I%20want%20to%20buy%20a%20TV">WhatsApp Us</a>
    <a href="tel:+256704229768">Call: 0704 229 768</a>
</div>
@endsection
