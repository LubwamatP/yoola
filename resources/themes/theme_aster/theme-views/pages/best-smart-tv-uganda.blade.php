@extends('theme-views.layouts.app')

@section('title', 'Best Smart TV in Uganda 2026 | Top Picks | Yoola')

@section('content')
<style>
    .hero-section { background: linear-gradient(135deg, #1a1a2e 0%, #2d2d44 100%); padding: 50px 20px; text-align: center; }
    .hero-section h1 { color: #fff; font-size: 2.2rem; font-weight: 700; margin-bottom: 15px; }
    .hero-section p { color: #ddd; font-size: 1.1rem; max-width: 650px; margin: 0 auto; }
    .picks-section { padding: 50px 20px; }
    .pick-card { background: #fff; border-radius: 12px; padding: 30px; margin-bottom: 25px; box-shadow: 0 2px 15px rgba(0,0,0,0.08); border-left: 5px solid #C41E3A; }
    .pick-badge { display: inline-block; background: #C41E3A; color: #fff; padding: 5px 15px; border-radius: 20px; font-size: 0.85rem; margin-bottom: 15px; }
    .pick-card h3 { font-size: 1.4rem; margin-bottom: 10px; }
    .pick-card .price { font-size: 1.3rem; color: #C41E3A; font-weight: 700; margin: 15px 0; }
    .pick-card ul { color: #666; margin: 15px 0; padding-left: 20px; }
    .pick-card li { margin-bottom: 8px; }
    .pick-card .btn-danger { background: #C41E3A; border-color: #C41E3A; }
    .section-title { text-align: center; padding: 40px 20px 20px; }
    .section-title h2 { font-size: 1.8rem; font-weight: 700; }
    .comparison-table { width: 100%; border-collapse: collapse; margin: 30px 0; }
    .comparison-table th, .comparison-table td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
    .comparison-table th { background: #fef5f5; color: #C41E3A; }
    .guide-section { background: #fef5f5; padding: 50px 20px; }
    .guide-content { max-width: 800px; margin: 0 auto; line-height: 1.8; }
    .guide-content h3 { margin: 25px 0 15px; color: #C41E3A; }
    .cta-section { background: #C41E3A; padding: 40px 20px; text-align: center; }
    .cta-section h2 { color: #fff; margin-bottom: 20px; }
    .cta-btn { display: inline-block; background: #fff; color: #C41E3A; padding: 15px 35px; border-radius: 8px; font-weight: 600; text-decoration: none; margin: 5px; }
    .cta-btn:hover { background: #f0f0f0; color: #C41E3A; }
</style>

<div class="hero-section">
    <h1>Best Smart TVs in Uganda 2026</h1>
    <p>Our expert picks for the best smart TVs you can buy in Uganda right now. Tested and recommended.</p>
</div>

<div class="picks-section container">
    <div class="pick-card">
        <span class="pick-badge">🏆 Best Overall</span>
        <h3>Samsung Crystal UHD 55"</h3>
        <p>The best all-round smart TV for most Ugandan homes. Crystal-clear 4K picture, Tizen smart platform with all major apps, and Samsung's legendary reliability.</p>
        <div class="price">From UGX 1,850,000</div>
        <ul>
            <li>✓ 4K Crystal UHD display</li>
            <li>✓ Tizen OS — Netflix, YouTube, Prime built-in</li>
            <li>✓ HDR10+ for vivid colors</li>
            <li>✓ 2-year warranty</li>
        </ul>
        <a href="{{ route('samsung.hub') }}" class="btn btn-danger">View Samsung TVs</a>
    </div>
    
    <div class="pick-card">
        <span class="pick-badge">💰 Best Value</span>
        <h3>Hisense 50" VIDAA Smart TV</h3>
        <p>The best smart TV for the money. Hisense delivers excellent picture quality and a smooth smart TV experience at a fraction of premium prices.</p>
        <div class="price">From UGX 950,000</div>
        <ul>
            <li>✓ 4K UHD resolution</li>
            <li>✓ VIDAA U smart system</li>
            <li>✓ Built-in Chromecast</li>
            <li>✓ Dolby Vision HDR</li>
        </ul>
        <a href="{{ route('hisense.hub') }}" class="btn btn-danger">View Hisense TVs</a>
    </div>
    
    <div class="pick-card">
        <span class="pick-badge">🎮 Best for Streaming</span>
        <h3>TCL 55" Google TV</h3>
        <p>Perfect for Netflix addicts. Google TV brings all your streaming apps together with voice search. Great picture, great price.</p>
        <div class="price">From UGX 1,100,000</div>
        <ul>
            <li>✓ Google TV built-in</li>
            <li>✓ Hands-free Google Assistant</li>
            <li>✓ 4K HDR Pro</li>
            <li>✓ Game mode for low latency</li>
        </ul>
        <a href="{{ route('products') }}?brand=tcl" class="btn btn-danger">View TCL TVs</a>
    </div>
    
    <div class="pick-card">
        <span class="pick-badge">🏠 Best Budget</span>
        <h3>CHiQ 43" Android TV</h3>
        <p>The cheapest way to get a proper smart TV. Android TV means access to thousands of apps from the Play Store.</p>
        <div class="price">From UGX 580,000</div>
        <ul>
            <li>✓ Full Android TV OS</li>
            <li>✓ Google Play Store access</li>
            <li>✓ Full HD resolution</li>
            <li>✓ Built-in Chromecast</li>
        </ul>
        <a href="{{ route('chiq.hub') }}" class="btn btn-danger">View CHiQ TVs</a>
    </div>
</div>

<div class="section-title">
    <h2>Quick Comparison</h2>
</div>

<div class="container" style="overflow-x: auto;">
    <table class="comparison-table">
        <tr>
            <th>Model</th>
            <th>Best For</th>
            <th>Smart Platform</th>
            <th>Price Range</th>
        </tr>
        <tr>
            <td><strong>Samsung Crystal UHD</strong></td>
            <td>Best overall quality</td>
            <td>Tizen</td>
            <td>UGX 1.8M - 3.5M</td>
        </tr>
        <tr>
            <td><strong>Hisense VIDAA</strong></td>
            <td>Value for money</td>
            <td>VIDAA U</td>
            <td>UGX 450K - 1.5M</td>
        </tr>
        <tr>
            <td><strong>TCL Google TV</strong></td>
            <td>Streaming & apps</td>
            <td>Google TV</td>
            <td>UGX 600K - 2M</td>
        </tr>
        <tr>
            <td><strong>CHiQ Android TV</strong></td>
            <td>Budget buyers</td>
            <td>Android TV</td>
            <td>UGX 380K - 900K</td>
        </tr>
    </table>
</div>

<div class="guide-section">
    <div class="guide-content">
        <h2 style="text-align:center; margin-bottom:30px;">Smart TV Buying Guide for Uganda</h2>
        
        <h3>What makes a TV "smart"?</h3>
        <p>A smart TV connects to your WiFi and lets you stream apps like Netflix, YouTube, and Showmax directly — no extra devices needed.</p>
        
        <h3>Which smart TV platform is best?</h3>
        <p><strong>Google TV / Android TV</strong> has the most apps. <strong>Samsung Tizen</strong> is the smoothest. <strong>VIDAA</strong> (Hisense) is simple and fast.</p>
        
        <h3>Do I need 4K?</h3>
        <p>For TVs 43" and larger, yes — you'll notice the sharper picture. For 32" bedroom TVs, Full HD is fine.</p>
    </div>
</div>

<div class="cta-section">
    <h2>Ready to Buy?</h2>
    <a href="{{ route('products') }}?category=entertainment" class="cta-btn">Shop All Smart TVs</a>
    <a href="https://wa.me/256704229768?text=Hi%20Yoola,%20I%20need%20help%20choosing%20a%20smart%20TV" class="cta-btn" style="background:#25d366; color:#fff;">Get Expert Advice</a>
</div>
@endsection
