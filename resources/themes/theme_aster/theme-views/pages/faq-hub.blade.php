@extends('theme-views.layouts.app')

@section('title', 'Electronics FAQ Uganda | Common Questions Answered | Yoola')

@section('content')
<style>
    .hero-section { background: linear-gradient(135deg, #1a1a2e 0%, #2d2d44 100%); padding: 50px 20px; text-align: center; }
    .hero-section h1 { color: #fff; font-size: 2.2rem; font-weight: 700; margin-bottom: 15px; }
    .hero-section p { color: #eee; font-size: 1.1rem; max-width: 600px; margin: 0 auto; }
    .faq-categories { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; padding: 50px 20px; max-width: 1100px; margin: 0 auto; }
    .faq-cat-card { background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 2px 15px rgba(0,0,0,0.08); border-top: 4px solid #C41E3A; }
    .faq-cat-card h2 { font-size: 1.3rem; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #fef5f5; color: #C41E3A; }
    .faq-cat-card ul { list-style: none; padding: 0; margin: 0; }
    .faq-cat-card li { margin-bottom: 12px; }
    .faq-cat-card a { color: #333; text-decoration: none; display: flex; align-items: center; gap: 10px; }
    .faq-cat-card a:hover { color: #C41E3A; }
    .faq-cat-card a::before { content: "→"; color: #C41E3A; }
    .popular-section { background: #fef5f5; padding: 50px 20px; }
    .popular-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; max-width: 1000px; margin: 0 auto; }
    .popular-item { background: #fff; padding: 25px; border-radius: 10px; border-left: 4px solid #C41E3A; }
    .popular-item h3 { font-size: 1.1rem; margin-bottom: 12px; color: #333; }
    .popular-item p { color: #666; line-height: 1.6; margin-bottom: 15px; }
    .popular-item a { color: #C41E3A; font-weight: 500; }
    .section-title { text-align: center; padding: 40px 20px 20px; }
    .section-title h2 { font-size: 1.8rem; font-weight: 700; }
    .cta-bar { background: #C41E3A; padding: 40px 20px; text-align: center; }
    .cta-bar h2 { color: #fff; margin-bottom: 15px; }
    .cta-bar p { color: #ffcdd2; margin-bottom: 20px; }
    .cta-btn { display: inline-block; background: #fff; color: #C41E3A; padding: 15px 35px; border-radius: 8px; font-weight: 600; text-decoration: none; }
    .cta-btn:hover { background: #f0f0f0; color: #C41E3A; }
</style>

<div class="hero-section">
    <h1>Electronics FAQ</h1>
    <p>Get answers to the most common questions about buying electronics in Uganda</p>
</div>

<div class="faq-categories">
    <div class="faq-cat-card">
        <h2>📺 TV Questions</h2>
        <ul>
            <li><a href="#tv-size">What size TV should I buy?</a></li>
            <li><a href="#tv-brand">What is the best TV brand in Uganda?</a></li>
            <li><a href="#tv-price">How much does a TV cost in Uganda?</a></li>
            <li><a href="#smart-tv">Do I need a Smart TV?</a></li>
            <li><a href="{{ url('/compare/hisense-vs-tcl') }}">Hisense vs TCL — which is better?</a></li>
            <li><a href="{{ url('/best/smart-tv-uganda') }}">Best Smart TVs in Uganda 2026</a></li>
        </ul>
    </div>
    
    <div class="faq-cat-card">
        <h2>❄️ Fridge & Freezer Questions</h2>
        <ul>
            <li><a href="#fridge-size">What size fridge do I need?</a></li>
            <li><a href="#fridge-brand">Best fridge brands in Uganda?</a></li>
            <li><a href="#freezer-size">How to choose a chest freezer?</a></li>
            <li><a href="{{ url('/prices/fridge-uganda') }}">Fridge prices in Uganda</a></li>
            <li><a href="{{ url('/prices/chest-freezer-uganda') }}">Chest freezer prices</a></li>
        </ul>
    </div>
    
    <div class="faq-cat-card">
        <h2>🧺 Washing Machine Questions</h2>
        <ul>
            <li><a href="#washer-type">Twin tub vs automatic — which to buy?</a></li>
            <li><a href="#washer-size">What kg washing machine do I need?</a></li>
            <li><a href="{{ url('/prices/washing-machine-uganda') }}">Washing machine prices</a></li>
            <li><a href="{{ route('samsung.washing-machines') }}">Samsung washing machines</a></li>
        </ul>
    </div>
    
    <div class="faq-cat-card">
        <h2>🛒 Shopping & Delivery</h2>
        <ul>
            <li><a href="#delivery">Do you deliver outside Kampala?</a></li>
            <li><a href="#genuine">Are your products genuine?</a></li>
            <li><a href="#payment">What payment methods do you accept?</a></li>
            <li><a href="#warranty">Do products have warranty?</a></li>
        </ul>
    </div>
</div>

<div class="popular-section">
    <div class="section-title">
        <h2>Most Asked Questions</h2>
    </div>
    
    <div class="popular-grid">
        <div class="popular-item" id="tv-size">
            <h3>What size TV should I buy for my room?</h3>
            <p>For bedrooms: 32-43 inch. For living rooms: 50-55 inch. For home cinema: 65+ inch. Sit about 1.5x the screen size away for best viewing.</p>
            <a href="{{ url('/buy/tv-uganda') }}">View all TVs →</a>
        </div>
        
        <div class="popular-item" id="tv-brand">
            <h3>What is the best TV brand in Uganda?</h3>
            <p>Samsung for premium quality. Hisense for best value. TCL for smart features on a budget. CHiQ for the most affordable smart TVs.</p>
            <a href="{{ url('/best/smart-tv-uganda') }}">See our top picks →</a>
        </div>
        
        <div class="popular-item" id="fridge-size">
            <h3>What size fridge do I need?</h3>
            <p>1-2 people: 100-150L. Family of 4: 200-300L. Large family: 400L+. Double door fridges offer more space and better organization.</p>
            <a href="{{ url('/prices/fridge-uganda') }}">View fridges →</a>
        </div>
        
        <div class="popular-item" id="washer-type">
            <h3>Twin tub or automatic washing machine?</h3>
            <p>Twin tub is cheaper and uses less water — great if you have time. Automatic (top/front load) is convenient and better for clothes.</p>
            <a href="{{ url('/prices/washing-machine-uganda') }}">Compare washers →</a>
        </div>
        
        <div class="popular-item" id="delivery">
            <h3>Do you deliver outside Kampala?</h3>
            <p>Yes! We deliver nationwide in Uganda. Kampala deliveries are free. Outside Kampala, delivery fees depend on location.</p>
            <a href="https://wa.me/256704229768">Get delivery quote →</a>
        </div>
        
        <div class="popular-item" id="genuine">
            <h3>Are your products genuine?</h3>
            <p>100% yes. Every product on Yoola is genuine with manufacturer warranty. We work directly with authorized distributors. No counterfeits, ever.</p>
            <a href="{{ route('products') }}">Shop with confidence →</a>
        </div>
    </div>
</div>

<div class="cta-bar">
    <h2>Still Have Questions?</h2>
    <p>Our team is ready to help you find the perfect electronics</p>
    <a href="https://wa.me/256704229768?text=Hi%20Yoola,%20I%20have%20a%20question" class="cta-btn">WhatsApp Us Now</a>
</div>
@endsection
