@extends('theme-views.layouts.app')

@section('title', 'Hisense vs TCL TV Uganda | Which is Better? | Yoola')

@section('content')
<style>
    .hero-section { background: linear-gradient(135deg, #1a1a2e 0%, #2d2d44 100%); padding: 50px 20px; text-align: center; }
    .hero-section h1 { color: #fff; font-size: 2rem; font-weight: 700; margin-bottom: 15px; }
    .hero-section p { color: #ddd; font-size: 1.1rem; max-width: 650px; margin: 0 auto; }
    .vs-section { display: flex; justify-content: center; align-items: center; gap: 30px; padding: 40px 20px; flex-wrap: wrap; }
    .vs-card { background: #fff; border-radius: 12px; padding: 30px; width: 280px; text-align: center; box-shadow: 0 2px 15px rgba(0,0,0,0.08); }
    .vs-card h2 { font-size: 1.8rem; margin-bottom: 10px; }
    .vs-card .tagline { color: #666; font-size: 0.95rem; }
    .vs-divider { font-size: 2.5rem; font-weight: 700; color: #C41E3A; }
    .verdict-box { background: #C41E3A; color: #fff; padding: 30px; text-align: center; margin: 0 20px; border-radius: 12px; }
    .verdict-box h2 { margin-bottom: 10px; }
    .comparison-section { padding: 50px 20px; }
    .comparison-table { width: 100%; max-width: 900px; margin: 0 auto; border-collapse: collapse; }
    .comparison-table th, .comparison-table td { padding: 15px; text-align: center; border-bottom: 1px solid #eee; }
    .comparison-table th { background: #fef5f5; color: #C41E3A; }
    .winner { color: #C41E3A; font-weight: 600; }
    .category-section { padding: 40px 20px; max-width: 900px; margin: 0 auto; }
    .category-section h3 { margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #C41E3A; color: #C41E3A; }
    .category-section p { color: #555; line-height: 1.7; margin-bottom: 20px; }
    .score-bar { display: flex; align-items: center; gap: 10px; margin-bottom: 15px; }
    .score-label { width: 80px; font-weight: 500; }
    .score-track { flex: 1; height: 12px; background: #eee; border-radius: 6px; overflow: hidden; }
    .score-fill { height: 100%; border-radius: 6px; }
    .hisense-fill { background: linear-gradient(90deg, #00a0e9, #0076bf); }
    .tcl-fill { background: linear-gradient(90deg, #e31937, #b01030); }
    .cta-section { background: #fef5f5; padding: 50px 20px; text-align: center; }
    .cta-section h2 { margin-bottom: 25px; }
    .cta-btn { display: inline-block; padding: 15px 30px; border-radius: 8px; font-weight: 600; text-decoration: none; margin: 10px; color: #fff; }
    .btn-hisense { background: #00a0e9; }
    .btn-tcl { background: #e31937; }
</style>

<div class="hero-section">
    <h1>Hisense vs TCL: Which TV Brand is Better in Uganda?</h1>
    <p>An honest comparison to help you decide. Both are excellent value brands — but which one is right for you?</p>
</div>

<div class="vs-section">
    <div class="vs-card">
        <h2 style="color: #00a0e9;">Hisense</h2>
        <p class="tagline">"See the Incredible"</p>
        <p style="margin-top:15px;">Chinese brand, #2 TV seller globally. Known for VIDAA smart platform and great value.</p>
    </div>
    <div class="vs-divider">VS</div>
    <div class="vs-card">
        <h2 style="color: #e31937;">TCL</h2>
        <p class="tagline">"Inspire Greatness"</p>
        <p style="margin-top:15px;">Chinese brand, #3 globally. Known for Google TV integration and aggressive pricing.</p>
    </div>
</div>

<div class="verdict-box">
    <h2>Quick Verdict</h2>
    <p><strong>Choose Hisense</strong> if you want proven reliability, good local support, and the VIDAA smart system.<br>
    <strong>Choose TCL</strong> if you want Google TV with more apps, or you're on a tighter budget.</p>
</div>

<div class="comparison-section">
    <table class="comparison-table">
        <tr>
            <th>Category</th>
            <th style="color: #00a0e9;">Hisense</th>
            <th style="color: #e31937;">TCL</th>
            <th>Winner</th>
        </tr>
        <tr><td><strong>Picture Quality</strong></td><td>Excellent</td><td>Very Good</td><td class="winner">Hisense</td></tr>
        <tr><td><strong>Smart Platform</strong></td><td>VIDAA U</td><td>Google TV</td><td class="winner">TCL</td></tr>
        <tr><td><strong>Price (43")</strong></td><td>~UGX 650K</td><td>~UGX 580K</td><td class="winner">TCL</td></tr>
        <tr><td><strong>Build Quality</strong></td><td>Solid</td><td>Good</td><td class="winner">Hisense</td></tr>
        <tr><td><strong>Uganda Availability</strong></td><td>Excellent</td><td>Good</td><td class="winner">Hisense</td></tr>
        <tr><td><strong>Warranty Support</strong></td><td>Good</td><td>Average</td><td class="winner">Hisense</td></tr>
    </table>
</div>

<div class="category-section">
    <h3>Picture Quality</h3>
    <div class="score-bar">
        <span class="score-label">Hisense</span>
        <div class="score-track"><div class="score-fill hisense-fill" style="width: 85%;"></div></div>
        <span>8.5/10</span>
    </div>
    <div class="score-bar">
        <span class="score-label">TCL</span>
        <div class="score-track"><div class="score-fill tcl-fill" style="width: 80%;"></div></div>
        <span>8.0/10</span>
    </div>
    <p>Both brands deliver excellent picture quality for the price. Hisense edges ahead with slightly better color accuracy and contrast.</p>
    
    <h3>Smart Features</h3>
    <div class="score-bar">
        <span class="score-label">Hisense</span>
        <div class="score-track"><div class="score-fill hisense-fill" style="width: 75%;"></div></div>
        <span>7.5/10</span>
    </div>
    <div class="score-bar">
        <span class="score-label">TCL</span>
        <div class="score-track"><div class="score-fill tcl-fill" style="width: 88%;"></div></div>
        <span>8.8/10</span>
    </div>
    <p>TCL wins here with Google TV — you get the full Play Store with thousands of apps and excellent voice search.</p>
</div>

<div class="cta-section">
    <h2>Ready to Choose?</h2>
    <p style="color:#666; margin-bottom:25px;">View our full range of both brands</p>
    <a href="{{ route('hisense.hub') }}" class="cta-btn btn-hisense">Shop Hisense TVs</a>
    <a href="{{ route('products') }}?brand=tcl" class="cta-btn btn-tcl">Shop TCL TVs</a>
    <br><br>
    <a href="https://wa.me/256704229768?text=Hi%20Yoola,%20I%20need%20help%20choosing%20between%20Hisense%20and%20TCL" style="color:#25d366; font-weight:600;">💬 Still unsure? WhatsApp us for advice</a>
</div>
@endsection
