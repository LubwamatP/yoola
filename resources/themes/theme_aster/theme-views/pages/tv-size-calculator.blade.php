@extends('theme-views.layouts.app')

@section('title', 'TV Size Calculator Uganda | What Size TV Do I Need? | Yoola')

@section('content')
<style>
    .calc-hero { background: linear-gradient(135deg, #1a1a2e 0%, #2d2d44 100%); padding: 50px 20px; text-align: center; }
    .calc-hero h1 { color: #fff; font-size: 2.2rem; font-weight: 700; margin-bottom: 15px; }
    .calc-hero p { color: #ccc; font-size: 1.1rem; max-width: 600px; margin: 0 auto; }
    .calculator-section { padding: 50px 20px; max-width: 800px; margin: 0 auto; }
    .calc-card { background: #fff; border-radius: 16px; padding: 40px; box-shadow: 0 4px 25px rgba(0,0,0,0.1); }
    .calc-card h2 { text-align: center; margin-bottom: 30px; }
    .input-group-calc { margin-bottom: 25px; }
    .input-group-calc label { display: block; font-weight: 600; margin-bottom: 10px; font-size: 1.1rem; }
    .input-group-calc input, .input-group-calc select { width: 100%; padding: 15px; border: 2px solid #e0e0e0; border-radius: 10px; font-size: 1.1rem; }
    .input-group-calc input:focus, .input-group-calc select:focus { border-color: #C41E3A; outline: none; }
    .calc-btn { width: 100%; padding: 18px; background: #C41E3A; color: #fff; border: none; border-radius: 10px; font-size: 1.2rem; font-weight: 600; cursor: pointer; transition: all 0.3s; }
    .calc-btn:hover { background: #a01830; transform: translateY(-2px); }
    .result-box { margin-top: 30px; padding: 30px; background: linear-gradient(135deg, #C41E3A 0%, #a01830 100%); border-radius: 12px; text-align: center; display: none; }
    .result-box.show { display: block; }
    .result-box h3 { color: #fff; font-size: 1.3rem; margin-bottom: 10px; }
    .result-box .size { color: #fff; font-size: 3.5rem; font-weight: 700; }
    .result-box .size span { font-size: 1.5rem; }
    .result-box p { color: #ffcdd2; margin-top: 15px; }
    .size-guide { margin-top: 40px; }
    .size-guide h3 { margin-bottom: 20px; color: #C41E3A; }
    .size-table { width: 100%; border-collapse: collapse; }
    .size-table th, .size-table td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
    .size-table th { background: #fef5f5; color: #C41E3A; }
    .tips-section { background: #fef5f5; padding: 50px 20px; margin-top: 50px; }
    .tips-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; max-width: 1000px; margin: 0 auto; }
    .tip-card { background: #fff; padding: 25px; border-radius: 12px; border-left: 4px solid #C41E3A; }
    .tip-card h4 { margin-bottom: 12px; color: #333; }
    .tip-card p { color: #666; line-height: 1.6; }
    .cta-section { background: #1a1a2e; padding: 50px 20px; text-align: center; }
    .cta-section h2 { color: #fff; margin-bottom: 15px; }
    .cta-section p { color: #ccc; margin-bottom: 25px; }
    .cta-btn { display: inline-block; background: #C41E3A; color: #fff; padding: 15px 40px; border-radius: 8px; font-weight: 600; text-decoration: none; margin: 5px; }
    .cta-btn:hover { background: #a01830; color: #fff; }
    .embed-box { margin-top: 50px; padding: 20px; background: #f8f8f8; border-radius: 8px; border: 1px solid #eee; }
    .embed-box h4 { margin-bottom: 10px; color: #C41E3A; }
    .embed-box code { display: block; padding: 15px; background: #1a1a2e; color: #fff; border-radius: 5px; font-size: 0.85rem; word-break: break-all; }
</style>

<div class="calc-hero">
    <h1>📺 TV Size Calculator</h1>
    <p>Find the perfect TV size for your room. Enter your viewing distance and get an instant recommendation.</p>
</div>

<div class="calculator-section">
    <div class="calc-card">
        <h2>Calculate Your Ideal TV Size</h2>
        
        <div class="input-group-calc">
            <label>📏 How far will you sit from the TV?</label>
            <div style="display:flex; gap:10px;">
                <input type="number" id="distance" placeholder="Enter distance" min="1" max="20" step="0.1" style="flex:1;">
                <select id="unit" style="width:120px;">
                    <option value="meters">Meters</option>
                    <option value="feet">Feet</option>
                </select>
            </div>
        </div>
        
        <div class="input-group-calc">
            <label>🎬 What will you mainly use it for?</label>
            <select id="usage">
                <option value="mixed">Mixed (Movies, TV, Sports)</option>
                <option value="movies">Mostly Movies & Gaming</option>
                <option value="news">Mostly News & Talk Shows</option>
            </select>
        </div>
        
        <button class="calc-btn" onclick="calculateSize()">Calculate My TV Size →</button>
        
        <div class="result-box" id="result">
            <h3>Your Recommended TV Size</h3>
            <div class="size"><span id="recommended-size">55</span>"</div>
            <p id="result-text">Perfect for your viewing distance of 2.5 meters</p>
            <a href="{{ route('products') }}?category=entertainment" class="cta-btn" style="margin-top:20px; background:#fff; color:#C41E3A;">Shop <span id="shop-size">55</span>" TVs</a>
        </div>
        
        <div class="size-guide">
            <h3>Quick Reference Guide</h3>
            <table class="size-table">
                <tr>
                    <th>Viewing Distance</th>
                    <th>Recommended Size</th>
                    <th>Best For</th>
                </tr>
                <tr>
                    <td>1.0 - 1.5m (3-5 ft)</td>
                    <td><strong>32"</strong></td>
                    <td>Bedroom, Kitchen</td>
                </tr>
                <tr>
                    <td>1.5 - 2.0m (5-7 ft)</td>
                    <td><strong>43"</strong></td>
                    <td>Small living room</td>
                </tr>
                <tr>
                    <td>2.0 - 2.5m (7-8 ft)</td>
                    <td><strong>50"</strong></td>
                    <td>Medium room</td>
                </tr>
                <tr>
                    <td>2.5 - 3.0m (8-10 ft)</td>
                    <td><strong>55"</strong></td>
                    <td>Large living room</td>
                </tr>
                <tr>
                    <td>3.0 - 4.0m (10-13 ft)</td>
                    <td><strong>65"</strong></td>
                    <td>Home cinema</td>
                </tr>
                <tr>
                    <td>4.0m+ (13+ ft)</td>
                    <td><strong>75"+</strong></td>
                    <td>Large hall</td>
                </tr>
            </table>
        </div>
    </div>
    
    <div class="embed-box">
        <h4>📎 Embed This Calculator</h4>
        <p style="color:#666; margin-bottom:10px;">Bloggers & websites: Feel free to embed this tool on your site!</p>
        <code>&lt;iframe src="https://yoola.ug/tools/tv-size-calculator" width="100%" height="600" frameborder="0"&gt;&lt;/iframe&gt;</code>
    </div>
</div>

<div class="tips-section">
    <h2 style="text-align:center; margin-bottom:30px; color:#333;">TV Buying Tips</h2>
    <div class="tips-grid">
        <div class="tip-card">
            <h4>🎯 The 1.5x Rule</h4>
            <p>For comfortable viewing, sit at a distance of about 1.5 times the screen's diagonal size. A 55" TV = sit about 2.1 meters away.</p>
        </div>
        <div class="tip-card">
            <h4>📐 Measure Your Wall</h4>
            <p>Make sure your TV fits! A 55" TV is about 122cm wide. Leave some space on each side for aesthetics.</p>
        </div>
        <div class="tip-card">
            <h4>🎮 Bigger for Gaming</h4>
            <p>Gamers and movie lovers often prefer slightly larger screens. If you're between sizes, go bigger!</p>
        </div>
        <div class="tip-card">
            <h4>💡 Room Brightness</h4>
            <p>Bright rooms need TVs with good brightness (300+ nits). Samsung QLED and Hisense ULED excel here.</p>
        </div>
    </div>
</div>

<div class="cta-section">
    <h2>Ready to Buy?</h2>
    <p>We have TVs from 32" to 85" at Uganda's best prices</p>
    <a href="{{ url('/buy/tv-uganda') }}" class="cta-btn">Browse All TVs</a>
    <a href="https://wa.me/256704229768?text=Hi%20Yoola,%20I%20need%20help%20choosing%20a%20TV" class="cta-btn" style="background:#25d366;">WhatsApp Us</a>
</div>

<script>
function calculateSize() {
    const distance = parseFloat(document.getElementById('distance').value);
    const unit = document.getElementById('unit').value;
    const usage = document.getElementById('usage').value;
    
    if (!distance || distance <= 0) {
        alert('Please enter your viewing distance');
        return;
    }
    
    let distanceM = unit === 'feet' ? distance * 0.3048 : distance;
    
    let multiplier = 1.0;
    if (usage === 'movies') multiplier = 1.15;
    if (usage === 'news') multiplier = 0.9;
    
    let sizeRaw = (distanceM * 100 / 4.2) * multiplier;
    
    const sizes = [32, 43, 50, 55, 65, 75, 85];
    let recommended = sizes.reduce((prev, curr) => 
        Math.abs(curr - sizeRaw) < Math.abs(prev - sizeRaw) ? curr : prev
    );
    
    document.getElementById('recommended-size').textContent = recommended;
    document.getElementById('shop-size').textContent = recommended;
    
    let distanceText = unit === 'feet' ? distance + ' feet' : distance + ' meters';
    document.getElementById('result-text').textContent = 'Perfect for your viewing distance of ' + distanceText;
    
    document.getElementById('result').classList.add('show');
    document.getElementById('result').scrollIntoView({ behavior: 'smooth', block: 'center' });
}
</script>
@endsection
