<?php

namespace Database\Seeders;

use App\Models\PricePage;
use Illuminate\Database\Seeder;

/**
 * Critical Price Pages - Exploiting Jumia's 404 gaps
 * Created: 2026-02-15
 * 
 * CONTEXT: Jumia has had 404 errors on /air-conditioners/ and /gas-cookers/
 * for 2+ weeks. This is a massive SEO opportunity.
 */
class CriticalPricePageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            // #1 PRIORITY - Air Conditioner (Jumia 404 for 2+ weeks!)
            [
                'slug' => 'air-conditioner-price-in-uganda',
                'title' => 'Air Conditioner Price in Uganda 2026 | Samsung AC | Free Delivery | Yoola',
                'meta_description' => 'Air conditioner prices in Uganda from 800,000 UGX. Samsung Split AC, Cassette, Floor Standing. 12K-48K BTU. ✓ Professional installation ✓ Warranty. Shop at Yoola.',
                'h1' => 'Air Conditioner Price in Uganda (Updated February 2026)',
                'intro_text' => "Beat Uganda's heat with quality air conditioning from Yoola. We stock Samsung ACs from compact 12,000 BTU split units perfect for bedrooms to powerful 48,000 BTU commercial systems for offices and shops. All our air conditioners come with genuine manufacturer warranty and we can arrange professional installation in Kampala.",
                'buying_guide' => '<h3>Air Conditioner Buying Guide for Uganda</h3>
<p><strong>Choosing the Right BTU:</strong></p>
<ul>
<li><strong>12,000 BTU:</strong> Bedrooms, small offices (up to 20 sqm)</li>
<li><strong>18,000 BTU:</strong> Living rooms, medium offices (20-35 sqm)</li>
<li><strong>24,000 BTU:</strong> Large rooms, shops (35-50 sqm)</li>
<li><strong>36,000-48,000 BTU:</strong> Commercial spaces, halls (50+ sqm)</li>
</ul>
<p><strong>Types of Air Conditioners:</strong></p>
<ul>
<li><strong>Split AC:</strong> Most popular for homes - quiet indoor unit, outdoor compressor</li>
<li><strong>Cassette AC:</strong> Ceiling-mounted, 360° cooling, perfect for offices</li>
<li><strong>Floor Standing:</strong> High capacity, no installation needed</li>
</ul>
<p><strong>Samsung Windfree Technology:</strong> Disperses air through thousands of micro-holes - you feel cool without the direct cold blast. Ideal for bedrooms and offices where drafts cause discomfort.</p>
<p><strong>Running Costs in Uganda:</strong> A 12,000 BTU inverter AC uses about 1-1.5 kWh per hour. At UMEME rates (~750 UGX/unit), 8 hours of cooling costs 6,000-9,000 UGX daily. Inverter models save 30-50% vs non-inverter.</p>',
                'faqs' => [
                    ['question' => 'How much does an air conditioner cost in Uganda?', 'answer' => 'Air conditioner prices at Yoola range from 800,000 UGX for 12,000 BTU units to over 24,000,000 UGX for large commercial systems. Samsung Windfree split ACs start around 4,000,000 UGX.'],
                    ['question' => 'Does AC installation cost extra?', 'answer' => 'Installation is quoted separately based on your location and requirements. We partner with certified technicians in Kampala. WhatsApp us for a quote: +256780221421'],
                    ['question' => 'Which AC brand is best in Uganda?', 'answer' => 'Samsung leads in reliability and efficiency. Their Windfree technology is popular for homes and offices. We stock genuine Samsung ACs with full manufacturer warranty.'],
                    ['question' => 'How much electricity does AC use in Uganda?', 'answer' => 'A 12,000 BTU inverter AC uses about 1-1.5 units (kWh) per hour. At UMEME rates around 750 UGX/unit, running AC 8 hours costs roughly 6,000-9,000 UGX daily. Inverter models save 30-50%.'],
                    ['question' => 'Can AC work with Uganda power fluctuations?', 'answer' => 'Samsung ACs have built-in voltage protection. We still recommend a stabilizer for added protection. Never connect AC directly to a generator without proper voltage regulation.'],
                    ['question' => 'What size AC do I need for my room?', 'answer' => 'Measure your room in square meters. Up to 20 sqm = 12,000 BTU, 20-35 sqm = 18,000 BTU, 35-50 sqm = 24,000 BTU, 50+ sqm = 36,000-48,000 BTU. Higher ceilings or sunny rooms may need more.']
                ],
                'product_type' => 'ac',
                'feature_filter' => 'conditioner',
                'is_active' => true,
                'is_indexed' => true,
                'priority' => 100,
            ],

            // #2 PRIORITY - Gas Cooker (Jumia 404 for 2+ weeks!)
            [
                'slug' => 'gas-cooker-price-in-uganda',
                'title' => 'Gas Cooker Price in Uganda 2026 | 4 & 6 Burner with Oven | Yoola',
                'meta_description' => 'Gas cooker prices in Uganda from 450,000 UGX. Full standing cookers with oven. 4-burner, 6-burner. Hotpoint, Bruhm, Mika. ✓ Free delivery Kampala. Shop at Yoola.',
                'h1' => 'Gas Cooker Price in Uganda (Updated February 2026)',
                'intro_text' => "Gas cookers remain Uganda's preferred cooking solution - faster than electric, reliable during load shedding, and cheaper to run than electricity. At Yoola, we stock quality standing gas cookers with ovens from Hotpoint, Bruhm, Mika, and other trusted brands. From compact 4-burner models to professional 6-burner cookers.",
                'buying_guide' => '<h3>Gas Cooker Buying Guide for Uganda</h3>
<p><strong>Why Choose Gas Over Electric?</strong></p>
<ul>
<li>Works during load shedding (UMEME outages)</li>
<li>Cheaper to run than electric cookers</li>
<li>Faster cooking with instant heat control</li>
<li>Preferred by professional cooks</li>
</ul>
<p><strong>Burner Options:</strong></p>
<ul>
<li><strong>4 Burner:</strong> Standard for most homes, space-efficient</li>
<li><strong>5 Burner:</strong> Extra burner for busy kitchens</li>
<li><strong>6 Burner:</strong> For large families, serious cooking</li>
</ul>
<p><strong>Oven Features to Look For:</strong></p>
<ul>
<li>Gas oven vs Electric oven (gas is more economical)</li>
<li>Rotisserie function for roasting</li>
<li>Double glass door (heat retention, safety)</li>
<li>Auto-ignition (no matches needed)</li>
</ul>
<p><strong>Safety Features:</strong></p>
<ul>
<li>Flame failure device (gas cuts off if flame dies)</li>
<li>Auto-ignition on all burners</li>
<li>Sturdy pan supports</li>
</ul>
<p><strong>Gas Consumption:</strong> A 6kg gas cylinder typically lasts 2-4 weeks for a family of 4, depending on cooking frequency. Much cheaper than running an electric cooker.</p>',
                'faqs' => [
                    ['question' => 'How much is a gas cooker in Uganda?', 'answer' => 'Gas cooker prices at Yoola start from 450,000 UGX for 4-burner models. 6-burner professional cookers range from 800,000 to 1,800,000 UGX depending on brand and features.'],
                    ['question' => 'Which gas cooker brand is best in Uganda?', 'answer' => 'Hotpoint is the most trusted brand in Uganda with excellent after-sales support. Bruhm offers great value. Mika is reliable and affordable. All brands we stock come with warranty.'],
                    ['question' => 'Is gas cooking cheaper than electric in Uganda?', 'answer' => 'Yes, significantly cheaper! A 6kg gas cylinder (around 35,000 UGX) lasts 2-4 weeks. Electric cooking at UMEME rates costs 2-3x more for the same amount of cooking.'],
                    ['question' => 'Does gas cooker work with 6kg and 13kg cylinders?', 'answer' => 'Yes, all our gas cookers work with standard Ugandan gas cylinders (6kg, 13kg). You just need the correct regulator and hose, which we can supply.'],
                    ['question' => 'Do you deliver gas cookers in Kampala?', 'answer' => 'Yes! Free delivery within Kampala for orders above 100,000 UGX. We deliver to all areas including Ntinda, Kisaasi, Naalya, Kira, Makindye, and more.'],
                    ['question' => 'Can I bake with a gas cooker oven?', 'answer' => 'Absolutely! Gas ovens are excellent for baking cakes, bread, and roasting. Many professional bakers prefer gas for its even heat distribution and temperature control.']
                ],
                'product_type' => 'cooker',
                'feature_filter' => 'gas',
                'is_active' => true,
                'is_indexed' => true,
                'priority' => 99,
            ],

            // #3 PRIORITY - Samsung Washing Machine (Jumia has NO Samsung!)
            [
                'slug' => 'samsung-washing-machine-price-in-uganda',
                'title' => 'Samsung Washing Machine Price in Uganda 2026 | Twin Tub & Front Load | Yoola',
                'meta_description' => 'Samsung washing machine prices in Uganda from 990,000 UGX. Twin tub, front load, top load. 6-14kg capacity. ✓ Genuine warranty ✓ Free delivery. Shop at Yoola.',
                'h1' => 'Samsung Washing Machine Price in Uganda (Updated February 2026)',
                'intro_text' => "Samsung washing machines combine Korean engineering with features designed for African conditions - from Wobble technology that protects fabrics to Digital Inverter motors that save electricity. At Yoola, we're one of the few retailers in Uganda with genuine Samsung washing machines. Available in twin tub, top load, and front load configurations.",
                'buying_guide' => '<h3>Samsung Washing Machine Guide for Uganda</h3>
<p><strong>Why Samsung?</strong></p>
<ul>
<li><strong>Digital Inverter Motor:</strong> 20-year warranty, saves energy, runs quietly</li>
<li><strong>Wobble Technology:</strong> Gentle on clothes, removes tough stains</li>
<li><strong>Smart Check:</strong> App diagnoses problems before they become expensive</li>
<li><strong>StayClean Drawer:</strong> Detergent drawer stays residue-free</li>
</ul>
<p><strong>Types Available:</strong></p>
<ul>
<li><strong>Twin Tub:</strong> Budget-friendly, manual operation, great for beginners</li>
<li><strong>Top Load Automatic:</strong> Convenient, good capacity, water-efficient</li>
<li><strong>Front Load:</strong> Most efficient, gentler on clothes, premium features</li>
</ul>
<p><strong>Capacity Guide:</strong></p>
<ul>
<li><strong>6-7kg:</strong> Singles, couples, or small families (1-3 people)</li>
<li><strong>8-9kg:</strong> Medium families (3-5 people)</li>
<li><strong>10-14kg:</strong> Large families, heavy bedding, commercial use</li>
</ul>
<p><strong>Water & Electricity in Uganda:</strong> Samsung inverter machines use 30-50% less electricity than conventional motors. Important given UMEME rates. Front loaders use less water than top loaders.</p>',
                'faqs' => [
                    ['question' => 'How much is a Samsung washing machine in Uganda?', 'answer' => 'Samsung washing machine prices at Yoola start from 990,000 UGX for 6kg twin tub models. Front load automatic machines range from 1,500,000 to 3,500,000 UGX depending on capacity and features.'],
                    ['question' => 'Is Samsung better than other washing machine brands?', 'answer' => 'Samsung leads in technology, durability, and energy efficiency. Their Digital Inverter motor comes with a 20-year warranty. For long-term value, Samsung is worth the investment.'],
                    ['question' => 'Where can I buy genuine Samsung washing machine in Uganda?', 'answer' => 'Yoola stocks genuine Samsung washing machines with full manufacturer warranty. Visit us at Burton St, Aponye Mall, Kampala, or WhatsApp +256780221421 for prices and availability.'],
                    ['question' => 'Which Samsung washing machine is best for Uganda?', 'answer' => 'For most Ugandan homes, the Samsung 8kg top load automatic offers the best balance of capacity, features, and price. Twin tubs are great for budget-conscious buyers.'],
                    ['question' => 'Does Samsung washing machine need special installation?', 'answer' => 'Twin tubs just need power and water access. Automatic machines may need plumbing. We can advise on setup and recommend technicians if needed.'],
                    ['question' => 'How much water does Samsung washing machine use?', 'answer' => 'Samsung front loaders use about 40-50 liters per wash. Top loaders use 80-120 liters. Front loaders are better for water-scarce areas or high water bills.']
                ],
                'product_type' => 'washing',
                'brand_filter' => 'samsung',
                'is_active' => true,
                'is_indexed' => true,
                'priority' => 98,
            ],

            // #4 - Samsung Fridge (Strong Yoola advantage)
            [
                'slug' => 'samsung-fridge-price-in-uganda',
                'title' => 'Samsung Fridge Price in Uganda 2026 | French Door & Double Door | Yoola',
                'meta_description' => 'Samsung fridge prices in Uganda from 1,200,000 UGX. French door, double door, side-by-side. Twin Cooling, Digital Inverter. ✓ Warranty ✓ Free delivery. Shop Yoola.',
                'h1' => 'Samsung Fridge Price in Uganda (Updated February 2026)',
                'intro_text' => "Samsung refrigerators represent premium quality in Uganda's appliance market. Known for innovative features like Twin Cooling (keeping food fresher longer) and Digital Inverter compressors (saving electricity), Samsung fridges are built for African conditions. At Yoola, we stock genuine Samsung refrigerators with full manufacturer warranty.",
                'buying_guide' => '<h3>Samsung Fridge Guide for Uganda</h3>
<p><strong>Samsung Technologies:</strong></p>
<ul>
<li><strong>Twin Cooling Plus:</strong> Separate cooling systems for fridge and freezer - food stays fresh 2x longer, no odor mixing</li>
<li><strong>Digital Inverter:</strong> Saves up to 50% electricity, runs quietly, 10-year warranty</li>
<li><strong>All-Around Cooling:</strong> Cold air flows from every shelf level</li>
<li><strong>Power Freeze/Cool:</strong> Rapid cooling when you need it</li>
</ul>
<p><strong>Types Available:</strong></p>
<ul>
<li><strong>Double Door (Top Freezer):</strong> Classic design, 250-400L, most affordable</li>
<li><strong>Bottom Freezer:</strong> Fridge at eye level, convenient access</li>
<li><strong>French Door:</strong> Premium, 450-650L, family-sized, multiple compartments</li>
<li><strong>Side-by-Side:</strong> Maximum capacity, water/ice dispenser options</li>
</ul>
<p><strong>Capacity Guide:</strong></p>
<ul>
<li><strong>200-300L:</strong> Singles, couples (1-2 people)</li>
<li><strong>300-450L:</strong> Small to medium families (3-5 people)</li>
<li><strong>450L+:</strong> Large families, bulk storage, entertaining</li>
</ul>',
                'faqs' => [
                    ['question' => 'How much is a Samsung fridge in Uganda?', 'answer' => 'Samsung fridge prices at Yoola range from 1,200,000 UGX for basic double door models to over 4,500,000 UGX for large French door refrigerators with premium features.'],
                    ['question' => 'Are Samsung fridges good for Uganda power?', 'answer' => 'Yes! Samsung Digital Inverter fridges handle voltage fluctuations well and use less electricity. We still recommend a stabilizer for maximum protection.'],
                    ['question' => 'What size Samsung fridge do I need?', 'answer' => 'For 1-2 people, 200-300L is enough. Families of 3-5 need 300-450L. Large families or those who shop in bulk should get 450L or larger.'],
                    ['question' => 'Does Samsung fridge come with warranty in Uganda?', 'answer' => 'Yes! All Samsung fridges from Yoola come with genuine manufacturer warranty. The Digital Inverter compressor has a 10-year warranty.'],
                    ['question' => 'Where to buy Samsung fridge in Kampala?', 'answer' => 'Visit Yoola at Burton St, Aponye Mall, Kampala. We offer free delivery within Kampala. WhatsApp us at +256780221421 for current stock and prices.']
                ],
                'product_type' => 'fridge',
                'brand_filter' => 'samsung',
                'is_active' => true,
                'is_indexed' => true,
                'priority' => 95,
            ],

            // #5 - Geepas Uganda (Low competition, have products)
            [
                'slug' => 'geepas-products-uganda',
                'title' => 'Geepas Products in Uganda 2026 | Kitchen Appliances | Best Prices | Yoola',
                'meta_description' => 'Geepas appliances in Uganda. Blenders, mixers, air fryers, kettles & more. UAE quality at affordable prices. ✓ Warranty ✓ Free Kampala delivery. Shop at Yoola.',
                'h1' => 'Geepas Products in Uganda (Updated February 2026)',
                'intro_text' => "Geepas is a UAE-based brand that's become hugely popular in Uganda for affordable yet reliable kitchen appliances. From powerful blenders that handle g-nuts to versatile air fryers and food processors, Geepas delivers quality without the premium price tag. At Yoola, we stock a wide range of genuine Geepas products with local warranty.",
                'buying_guide' => '<h3>Popular Geepas Products in Uganda</h3>
<p><strong>Kitchen Appliances:</strong></p>
<ul>
<li><strong>Blenders:</strong> 1.5L to 2L capacity, 400-1500W power, glass and plastic jars</li>
<li><strong>Food Processors:</strong> Chop, slice, blend, knead - all-in-one kitchen helpers</li>
<li><strong>Air Fryers:</strong> Healthy cooking with little to no oil, 3-8L capacity</li>
<li><strong>Electric Kettles:</strong> 1.5-2L capacity, auto shut-off, stainless steel</li>
<li><strong>Sandwich Makers:</strong> Quick breakfast, easy to clean</li>
</ul>
<p><strong>Why Geepas is Popular in Uganda:</strong></p>
<ul>
<li>Affordable prices - quality without breaking the bank</li>
<li>Durable construction - built for African conditions</li>
<li>Wide product range - from simple to professional</li>
<li>Available spare parts and service</li>
</ul>
<p><strong>Power Considerations:</strong> Geepas appliances work on 220-240V which is standard in Uganda. Most include surge protection but a stabilizer is recommended for expensive models.</p>',
                'faqs' => [
                    ['question' => 'Is Geepas a good brand in Uganda?', 'answer' => 'Yes! Geepas offers excellent value - UAE quality at African prices. Very popular in Uganda for blenders, kettles, and air fryers. All Geepas products at Yoola come with warranty.'],
                    ['question' => 'Where to buy Geepas products in Kampala?', 'answer' => 'Yoola stocks genuine Geepas appliances at Burton St, Aponye Mall, Kampala. Free delivery within Kampala. WhatsApp us at +256780221421.'],
                    ['question' => 'How much is Geepas blender in Uganda?', 'answer' => 'Geepas blender prices at Yoola range from 85,000 UGX for basic models to 350,000 UGX for professional food processors with multiple attachments.'],
                    ['question' => 'Can Geepas blender grind g-nuts?', 'answer' => 'Yes! Geepas blenders with 500W+ power handle g-nuts (groundnuts) well. Look for models with stainless steel blades and glass jars for best results.'],
                    ['question' => 'Does Geepas have warranty in Uganda?', 'answer' => 'Yes, all Geepas products from Yoola come with warranty. We handle warranty claims locally - no need to ship products abroad.']
                ],
                'product_type' => null,
                'brand_filter' => 'geepas',
                'is_active' => true,
                'is_indexed' => true,
                'priority' => 90,
            ],
        ];

        foreach ($pages as $page) {
            PricePage::updateOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }

        $this->command->info('Created ' . count($pages) . ' critical price pages!');
    }
}
