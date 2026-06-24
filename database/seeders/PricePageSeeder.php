<?php

namespace Database\Seeders;

use App\Models\PricePage;
use Illuminate\Database\Seeder;

class PricePageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            // TVs
            [
                'slug' => 'hisense-tv-price-in-uganda',
                'title' => 'Hisense TV Price in Uganda 2026 | From 490,000 UGX | Yoola',
                'meta_description' => 'Compare Hisense TV prices in Uganda. Starting from 490,000 UGX. ✓ Genuine warranty ✓ Free Kampala delivery ✓ Best prices guaranteed. Shop at Yoola.',
                'h1' => 'Hisense TV Price in Uganda (Updated February 2026)',
                'intro_text' => 'Looking for Hisense TV prices in Uganda? Hisense offers the best value for Smart TVs, with prices starting from 490,000 UGX for a 32-inch model. At Yoola, we stock genuine Hisense TVs with official warranty - not grey market imports. Whether you want a basic digital TV or a feature-packed Android Smart TV, we have options for every budget.',
                'buying_guide' => '<h3>How to Choose the Right Hisense TV</h3>
<p><strong>Size Matters:</strong> For bedrooms, 32-43 inch is ideal. Living rooms work best with 50-65 inch screens. Measure your space before buying.</p>
<p><strong>Smart vs Digital:</strong> Smart TVs connect to WiFi and run apps like YouTube and Netflix. Digital TVs only receive broadcast channels. For streaming, go smart.</p>
<p><strong>Resolution:</strong> Full HD (1080p) is fine for 32-43 inch. For 50 inch and above, consider 4K for sharper images.</p>
<p><strong>What to Look For:</strong></p>
<ul>
<li>At least 2 HDMI ports (for decoder, gaming console)</li>
<li>USB port for playing media from flash drives</li>
<li>Built-in decoder saves you money on a separate decoder</li>
</ul>',
                'faqs' => [
                    ['question' => 'What is the cheapest Hisense TV in Uganda?', 'answer' => 'The cheapest Hisense TV at Yoola starts from 490,000 UGX for a 32-inch Digital TV. Smart TV models start from around 650,000 UGX.'],
                    ['question' => 'Does Hisense TV come with warranty in Uganda?', 'answer' => 'Yes, all Hisense TVs from Yoola come with genuine manufacturer warranty. We only sell authentic products, not grey market imports.'],
                    ['question' => 'Where can I buy Hisense TV in Kampala?', 'answer' => 'Visit Yoola at Burton Street, Aponye City Mall, Kampala. We offer same-day delivery within Kampala. Call or WhatsApp +256780221421.'],
                    ['question' => 'Is Hisense a good TV brand?', 'answer' => 'Yes, Hisense is one of the top 5 TV brands globally. They offer excellent value - premium features at affordable prices. Very popular in Uganda.']
                ],
                'product_type' => 'tv',
                'brand_filter' => 'hisense',
                'category_id' => null,
                'brand_id' => null,
                'is_active' => true,
            ],
            [
                'slug' => 'samsung-tv-price-in-uganda',
                'title' => 'Samsung TV Price in Uganda 2026 | Premium Quality | Yoola',
                'meta_description' => 'Samsung TV prices in Uganda starting from 750,000 UGX. Crystal UHD, QLED & Smart TVs. ✓ Official warranty ✓ Free delivery in Kampala. Shop at Yoola.',
                'h1' => 'Samsung TV Price in Uganda (Updated February 2026)',
                'intro_text' => 'Samsung TVs represent premium quality in Uganda\'s electronics market. Known for stunning picture quality and reliability, Samsung offers everything from affordable Crystal UHD TVs to flagship QLED models. At Yoola, we stock genuine Samsung TVs with full manufacturer warranty.',
                'buying_guide' => '<h3>Samsung TV Buying Guide for Uganda</h3>
<p><strong>Samsung TV Lineup:</strong></p>
<ul>
<li><strong>Crystal UHD:</strong> Entry-level 4K TVs, great value</li>
<li><strong>QLED:</strong> Premium picture quality, brighter colors</li>
<li><strong>Neo QLED:</strong> Top-of-the-line, mini-LED technology</li>
</ul>
<p><strong>Smart Features:</strong> All Samsung Smart TVs run Tizen OS with apps like Netflix, YouTube, and Apple TV built in.</p>
<p><strong>Gaming:</strong> Samsung TVs support Game Mode with low input lag - perfect for PS5 or Xbox gamers.</p>',
                'faqs' => [
                    ['question' => 'What is the price of Samsung TV in Uganda?', 'answer' => 'Samsung TV prices in Uganda range from 750,000 UGX for 32-inch models to over 5,000,000 UGX for large QLED TVs at Yoola.'],
                    ['question' => 'Is Samsung better than Hisense?', 'answer' => 'Samsung offers premium build quality and picture processing. Hisense offers better value for money. Both are excellent - choose based on your budget.'],
                    ['question' => 'Do Samsung TVs work with Ugandan decoders?', 'answer' => 'Yes, Samsung TVs have multiple HDMI inputs that work with all Ugandan decoders including StarTimes, GOtv, and DStv.']
                ],
                'product_type' => 'tv',
                'brand_filter' => 'samsung',
                'category_id' => null,
                'brand_id' => null,
                'is_active' => true,
            ],
            [
                'slug' => '32-inch-tv-price-in-uganda',
                'title' => '32 Inch TV Price in Uganda 2026 | Budget-Friendly | Yoola',
                'meta_description' => '32 inch TV prices in Uganda from 450,000 UGX. Hisense, Samsung, LG & more. Perfect for bedrooms. ✓ Free Kampala delivery. Shop at Yoola.',
                'h1' => '32 Inch TV Price in Uganda (Updated February 2026)',
                'intro_text' => '32 inch TVs are Uganda\'s most popular size - perfect for bedrooms, small living rooms, or as a secondary TV. At these prices, you can afford quality without breaking the bank. We stock 32-inch TVs from top brands including Hisense, Samsung, and LG.',
                'buying_guide' => '<h3>Is 32 Inch the Right Size for You?</h3>
<p><strong>Perfect For:</strong></p>
<ul>
<li>Bedrooms and guest rooms</li>
<li>Small apartments or hostels</li>
<li>Kitchen TVs</li>
<li>Secondary/backup TVs</li>
</ul>
<p><strong>Viewing Distance:</strong> 32 inch TVs are ideal when sitting 1.2 to 2 meters from the screen.</p>
<p><strong>Resolution:</strong> At 32 inches, HD (720p) looks fine. Full HD (1080p) is better if available.</p>',
                'faqs' => [
                    ['question' => 'What is the cheapest 32 inch TV in Uganda?', 'answer' => 'The cheapest 32 inch TVs at Yoola start from around 450,000 UGX for basic digital models. Smart TV versions start from 550,000 UGX.'],
                    ['question' => 'Which brand makes the best 32 inch TV?', 'answer' => 'Hisense offers the best value in 32 inch TVs. For premium quality, consider Samsung. Both brands are reliable and popular in Uganda.'],
                    ['question' => 'Can I get a Smart TV in 32 inch size?', 'answer' => 'Yes! Most brands offer 32 inch Smart TVs. They cost slightly more than digital-only models but give you YouTube, Netflix, and app access.']
                ],
                'product_type' => 'tv',
                'size_filter' => '32',
                'brand_filter' => null,
                'category_id' => null,
                'brand_id' => null,
                'is_active' => true,
            ],
            // Fridges
            [
                'slug' => 'samsung-fridge-price-in-uganda',
                'title' => 'Samsung Fridge Price in Uganda 2026 | Premium Refrigerators | Yoola',
                'meta_description' => 'Samsung fridge prices in Uganda from 1,200,000 UGX. Double door, French door & more. ✓ Energy efficient ✓ Warranty ✓ Free delivery. Shop at Yoola.',
                'h1' => 'Samsung Fridge Price in Uganda (Updated February 2026)',
                'intro_text' => 'Samsung refrigerators are known for durability, energy efficiency, and innovative features like Twin Cooling and Digital Inverter technology. At Yoola, we offer genuine Samsung fridges with full warranty. Whether you need a compact single door or a spacious French door refrigerator, we have options for every home.',
                'buying_guide' => '<h3>Choosing the Right Samsung Fridge</h3>
<p><strong>Types Available:</strong></p>
<ul>
<li><strong>Single Door:</strong> Compact, budget-friendly, 150-250L</li>
<li><strong>Double Door (Top Freezer):</strong> Most popular, 250-400L</li>
<li><strong>Bottom Freezer:</strong> Fridge at eye level, convenient</li>
<li><strong>French Door:</strong> Premium, 500L+, family-sized</li>
</ul>
<p><strong>Key Features:</strong></p>
<ul>
<li>Digital Inverter Compressor - saves electricity, runs quietly</li>
<li>Twin Cooling - keeps food fresh longer</li>
<li>No Frost - never defrost again</li>
</ul>',
                'faqs' => [
                    ['question' => 'How much is a Samsung fridge in Uganda?', 'answer' => 'Samsung fridge prices at Yoola range from 1,200,000 UGX for single door models to over 4,000,000 UGX for large French door refrigerators.'],
                    ['question' => 'Are Samsung fridges energy efficient?', 'answer' => 'Yes, Samsung fridges with Digital Inverter technology use up to 50% less electricity than conventional compressors. Great for Uganda power bills.'],
                    ['question' => 'Does Samsung fridge work with unstable power?', 'answer' => 'Samsung fridges handle voltage fluctuations well. However, we recommend using a voltage stabilizer or surge protector for best results.']
                ],
                'product_type' => 'fridge',
                'brand_filter' => 'samsung',
                'category_id' => null,
                'brand_id' => null,
                'is_active' => true,
            ],
            [
                'slug' => 'double-door-fridge-price-in-uganda',
                'title' => 'Double Door Fridge Price in Uganda 2026 | All Brands | Yoola',
                'meta_description' => 'Double door fridge prices in Uganda from 800,000 UGX. Samsung, LG, Hisense & more. Top freezer design. ✓ Free Kampala delivery. Shop at Yoola.',
                'h1' => 'Double Door Fridge Price in Uganda (Updated February 2026)',
                'intro_text' => 'Double door fridges (top freezer refrigerators) are Uganda\'s most popular choice for families. With separate compartments for freezing and cooling, they offer the perfect balance of capacity and efficiency. We stock double door fridges from Samsung, LG, Hisense, and other trusted brands.',
                'buying_guide' => '<h3>Double Door Fridge Buying Guide</h3>
<p><strong>Capacity Guide:</strong></p>
<ul>
<li>250-300L: 2-3 person household</li>
<li>300-400L: 4-5 person family</li>
<li>400L+: Large families or bulk storage</li>
</ul>
<p><strong>Must-Have Features:</strong></p>
<ul>
<li>No Frost technology (never defrost manually)</li>
<li>Adjustable shelves for flexibility</li>
<li>Energy rating (look for inverter models)</li>
</ul>',
                'faqs' => [
                    ['question' => 'What is the price of a double door fridge in Uganda?', 'answer' => 'Double door fridge prices at Yoola range from 800,000 UGX for budget brands to over 2,500,000 UGX for premium Samsung and LG models.'],
                    ['question' => 'Which double door fridge brand is best in Uganda?', 'answer' => 'Samsung and LG lead in quality and durability. Hisense offers excellent value for money. All brands we stock come with warranty.'],
                    ['question' => 'How much electricity does a double door fridge use?', 'answer' => 'Modern inverter fridges use about 1-1.5 units (kWh) per day. Non-inverter models use 2-3 units. Inverter fridges save money long-term.']
                ],
                'product_type' => 'fridge',
                'feature_filter' => 'double door',
                'brand_filter' => null,
                'category_id' => null,
                'brand_id' => null,
                'is_active' => true,
            ],
            // Cookers
            [
                'slug' => 'gas-cooker-price-in-uganda',
                'title' => 'Gas Cooker Price in Uganda 2026 | 4 & 6 Burner | Yoola',
                'meta_description' => 'Gas cooker prices in Uganda from 350,000 UGX. 4-burner, 6-burner with oven. Hotpoint, Geepas & more. ✓ Free delivery. Shop at Yoola.',
                'h1' => 'Gas Cooker Price in Uganda (Updated February 2026)',
                'intro_text' => 'Gas cookers remain Uganda\'s preferred cooking solution - faster than electric, more reliable during load shedding, and cheaper to run. At Yoola, we stock quality gas cookers from Hotpoint, Geepas, Mika, and other trusted brands. From compact 4-burner models to professional 6-burner cookers with ovens.',
                'buying_guide' => '<h3>Gas Cooker Buying Guide for Uganda</h3>
<p><strong>Burner Options:</strong></p>
<ul>
<li><strong>4 Burner:</strong> Standard for most homes, compact</li>
<li><strong>5 Burner:</strong> Extra burner, same footprint</li>
<li><strong>6 Burner:</strong> For serious cooking, large families</li>
</ul>
<p><strong>With or Without Oven?</strong></p>
<ul>
<li>Standing cookers include oven - great for baking</li>
<li>Table-top cookers (no oven) - cheaper, space-saving</li>
</ul>
<p><strong>Safety Features:</strong></p>
<ul>
<li>Auto-ignition (no matches needed)</li>
<li>Flame failure device (gas cuts off if flame dies)</li>
<li>Glass lid (protects burners, doubles as workspace)</li>
</ul>',
                'faqs' => [
                    ['question' => 'How much is a gas cooker in Uganda?', 'answer' => 'Gas cooker prices at Yoola start from 350,000 UGX for 4-burner table-top models. Standing cookers with oven range from 600,000 to 1,500,000 UGX.'],
                    ['question' => 'Which gas cooker brand is best in Uganda?', 'answer' => 'Hotpoint is the most trusted brand in Uganda. Geepas offers good value. Mika is affordable and reliable. All available at Yoola.'],
                    ['question' => 'Is gas cooking cheaper than electric in Uganda?', 'answer' => 'Yes, gas is significantly cheaper than electricity for cooking in Uganda, especially with current UMEME rates. A 6kg gas cylinder lasts most families 2-4 weeks.']
                ],
                'product_type' => 'cooker',
                'feature_filter' => 'gas',
                'brand_filter' => null,
                'category_id' => null,
                'brand_id' => null,
                'is_active' => true,
            ],
            // Small Appliances
            [
                'slug' => 'blender-price-in-uganda',
                'title' => 'Blender Price in Uganda 2026 | All Types & Brands | Yoola',
                'meta_description' => 'Blender prices in Uganda from 85,000 UGX. Geepas, Sayona, Philips. Smoothie blenders, food processors. ✓ Free delivery. Shop at Yoola.',
                'h1' => 'Blender Price in Uganda (Updated February 2026)',
                'intro_text' => 'From morning smoothies to g-nut paste and fresh juice, a good blender is essential in every Ugandan kitchen. We stock blenders from trusted brands like Geepas, Sayona, and Philips - ranging from basic models to powerful food processors.',
                'buying_guide' => '<h3>Blender Buying Guide</h3>
<p><strong>Types of Blenders:</strong></p>
<ul>
<li><strong>Standard Blender:</strong> All-purpose, glass or plastic jar</li>
<li><strong>Food Processor:</strong> Chops, slices, blends - more versatile</li>
<li><strong>Personal/Bullet Blender:</strong> Single-serve, portable</li>
<li><strong>Commercial Blender:</strong> Heavy-duty for juice bars</li>
</ul>
<p><strong>Power Matters:</strong></p>
<ul>
<li>300-500W: Basic blending, soft fruits</li>
<li>500-800W: Nuts, ice, tougher ingredients</li>
<li>1000W+: Professional power, crush anything</li>
</ul>',
                'faqs' => [
                    ['question' => 'What is the price of a blender in Uganda?', 'answer' => 'Blender prices at Yoola start from 85,000 UGX for basic models. Quality blenders range from 150,000 to 400,000 UGX.'],
                    ['question' => 'Which blender can grind g-nuts?', 'answer' => 'For grinding g-nuts (groundnuts), you need at least 500W power and strong blades. Geepas and Sayona commercial models handle this well.'],
                    ['question' => 'Glass or plastic jar - which is better?', 'answer' => 'Glass jars are more durable and don\'t stain or absorb odors. Plastic is lighter and won\'t shatter. Glass is worth the extra cost.']
                ],
                'product_type' => null,
                'feature_filter' => 'blender',
                'brand_filter' => null,
                'category_id' => null,
                'brand_id' => null,
                'is_active' => true,
            ],
            [
                'slug' => 'microwave-price-in-uganda',
                'title' => 'Microwave Price in Uganda 2026 | All Sizes | Yoola',
                'meta_description' => 'Microwave oven prices in Uganda from 280,000 UGX. 20L, 25L, 30L. Samsung, LG, Geepas. ✓ Free Kampala delivery. Shop at Yoola.',
                'h1' => 'Microwave Price in Uganda (Updated February 2026)',
                'intro_text' => 'Microwaves make reheating, defrosting, and quick cooking effortless. Essential for busy Ugandan households and offices. At Yoola, we stock microwaves from Samsung, LG, Geepas, and more - from compact 20L models to large 30L+ family sizes.',
                'buying_guide' => '<h3>Microwave Buying Guide</h3>
<p><strong>Size Guide:</strong></p>
<ul>
<li><strong>20L:</strong> Singles, couples, small spaces</li>
<li><strong>23-25L:</strong> Small families, most popular size</li>
<li><strong>28-32L:</strong> Large families, big dishes</li>
</ul>
<p><strong>Types:</strong></p>
<ul>
<li><strong>Solo:</strong> Basic reheating and defrosting</li>
<li><strong>Grill:</strong> Can brown and crisp food</li>
<li><strong>Convection:</strong> Full oven capabilities, baking</li>
</ul>',
                'faqs' => [
                    ['question' => 'How much is a microwave in Uganda?', 'answer' => 'Microwave prices at Yoola range from 280,000 UGX for basic 20L models to 700,000+ UGX for large convection microwaves.'],
                    ['question' => 'What size microwave do I need?', 'answer' => 'For 1-2 people, 20L is enough. Families of 3-5 should get 25L. Large families or those heating big dishes need 30L+.'],
                    ['question' => 'Can microwave use stabilizer?', 'answer' => 'Yes, using a stabilizer protects your microwave from Uganda\'s voltage fluctuations. We recommend it for all electronics.']
                ],
                'product_type' => null,
                'feature_filter' => 'microwave',
                'brand_filter' => null,
                'category_id' => null,
                'brand_id' => null,
                'is_active' => true,
            ],
            // Washing Machines
            [
                'slug' => 'washing-machine-price-in-uganda',
                'title' => 'Washing Machine Price in Uganda 2026 | All Types | Yoola',
                'meta_description' => 'Washing machine prices in Uganda from 450,000 UGX. Twin tub, automatic, front load. Samsung, LG, Hisense. ✓ Free delivery. Shop at Yoola.',
                'h1' => 'Washing Machine Price in Uganda (Updated February 2026)',
                'intro_text' => 'Save time and effort with a quality washing machine. Uganda\'s busy families are increasingly choosing machine washing over hand washing. At Yoola, we stock twin tub, semi-automatic, and fully automatic washing machines from top brands like Samsung, LG, and Hisense.',
                'buying_guide' => '<h3>Washing Machine Types Explained</h3>
<p><strong>Twin Tub:</strong></p>
<ul>
<li>Most affordable option</li>
<li>Manual water filling and draining</li>
<li>Separate wash and spin compartments</li>
<li>Best for: Budget-conscious buyers</li>
</ul>
<p><strong>Top Load Automatic:</strong></p>
<ul>
<li>Automatic water filling and washing</li>
<li>More convenient than twin tub</li>
<li>Uses more water than front load</li>
<li>Best for: Convenience seekers</li>
</ul>
<p><strong>Front Load:</strong></p>
<ul>
<li>Most water and energy efficient</li>
<li>Gentler on clothes</li>
<li>Higher price point</li>
<li>Best for: Quality and efficiency</li>
</ul>',
                'faqs' => [
                    ['question' => 'What is the cheapest washing machine in Uganda?', 'answer' => 'Twin tub washing machines are cheapest, starting from 450,000 UGX at Yoola. Automatic machines start from around 800,000 UGX.'],
                    ['question' => 'Which washing machine is best for Uganda?', 'answer' => 'For most Ugandan homes, a 7-8kg top load automatic offers the best balance of convenience and price. Front loaders save water but cost more.'],
                    ['question' => 'Does washing machine need special installation?', 'answer' => 'Twin tubs just need a power outlet and water source. Automatic machines may need plumbing connection. We can advise on setup.']
                ],
                'product_type' => 'washing',
                'brand_filter' => null,
                'category_id' => null,
                'brand_id' => null,
                'is_active' => true,
            ],
            [
                'slug' => 'smart-tv-price-in-uganda',
                'title' => 'Smart TV Price in Uganda 2026 | Android & Web OS | Yoola',
                'meta_description' => 'Smart TV prices in Uganda from 550,000 UGX. Netflix, YouTube, apps built-in. All sizes. ✓ Genuine warranty ✓ Free delivery. Shop at Yoola.',
                'h1' => 'Smart TV Price in Uganda (Updated February 2026)',
                'intro_text' => 'Smart TVs connect to WiFi and run apps like Netflix, YouTube, and Showmax directly - no need for separate streaming devices. Uganda\'s internet speeds have improved dramatically, making Smart TVs practical and popular. We stock Android TV, Google TV, and Web OS Smart TVs from all major brands.',
                'buying_guide' => '<h3>Smart TV Buying Guide for Uganda</h3>
<p><strong>Operating Systems:</strong></p>
<ul>
<li><strong>Android TV / Google TV:</strong> Most apps, Play Store access (Hisense, TCL, Sony)</li>
<li><strong>Web OS:</strong> Smooth, simple interface (LG)</li>
<li><strong>Tizen:</strong> Reliable, good app selection (Samsung)</li>
</ul>
<p><strong>Internet Requirements:</strong></p>
<ul>
<li>YouTube: 5 Mbps minimum</li>
<li>Netflix HD: 5 Mbps</li>
<li>Netflix 4K: 25 Mbps</li>
</ul>
<p><strong>WiFi vs Ethernet:</strong> For stable streaming, use ethernet cable if possible. WiFi works but may buffer on slow connections.</p>',
                'faqs' => [
                    ['question' => 'What is the cheapest Smart TV in Uganda?', 'answer' => 'The cheapest Smart TVs at Yoola start from 550,000 UGX for 32-inch models. 43-inch Smart TVs start from around 750,000 UGX.'],
                    ['question' => 'Do Smart TVs work with Uganda internet?', 'answer' => 'Yes! With MTN, Airtel, or Liquid fiber, Smart TVs work great. You need at least 5 Mbps for smooth YouTube and Netflix streaming.'],
                    ['question' => 'Can I use Smart TV without internet?', 'answer' => 'Yes, Smart TVs work as regular TVs without internet. You just won\'t access streaming apps. Decoder and local channels work normally.']
                ],
                'product_type' => 'tv',
                'feature_filter' => 'smart',
                'brand_filter' => null,
                'category_id' => null,
                'brand_id' => null,
                'is_active' => true,
            ],
        ];

        foreach ($pages as $page) {
            PricePage::updateOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }
    }
}


