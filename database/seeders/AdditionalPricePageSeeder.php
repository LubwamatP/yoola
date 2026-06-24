<?php

namespace Database\Seeders;

use App\Models\PricePage;
use Illuminate\Database\Seeder;

/**
 * Additional High-Value Price Pages
 * Created: 2026-02-15
 * 
 * Targeting more Uganda electronics search queries
 */
class AdditionalPricePageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            // Laptop - High search volume
            [
                'slug' => 'laptop-price-in-uganda',
                'title' => 'Laptop Price in Uganda 2026 | HP, Dell, Lenovo | Best Deals | Yoola',
                'meta_description' => 'Laptop prices in Uganda from 800,000 UGX. HP, Dell, Lenovo, ASUS. Students, business, gaming laptops. ✓ Genuine warranty ✓ Free delivery Kampala. Shop Yoola.',
                'h1' => 'Laptop Price in Uganda (Updated February 2026)',
                'intro_text' => "Looking for a laptop in Uganda? Whether you need a budget laptop for students, a powerful business machine, or a gaming laptop, Yoola has you covered. We stock genuine laptops from HP, Dell, Lenovo, and ASUS with local warranty and after-sales support in Kampala.",
                'buying_guide' => '<h3>Laptop Buying Guide for Uganda</h3>
<p><strong>Choose by Purpose:</strong></p>
<ul>
<li><strong>Students:</strong> 4-8GB RAM, 256GB SSD, Intel i3/i5 - from 800,000 UGX</li>
<li><strong>Business:</strong> 8-16GB RAM, 512GB SSD, Intel i5/i7 - from 1,500,000 UGX</li>
<li><strong>Gaming:</strong> 16GB+ RAM, dedicated GPU, SSD - from 2,500,000 UGX</li>
<li><strong>Basic Use:</strong> Chromebooks and budget laptops from 500,000 UGX</li>
</ul>
<p><strong>Key Specs to Consider:</strong></p>
<ul>
<li><strong>Processor:</strong> Intel i5/i7 or AMD Ryzen 5/7 for good performance</li>
<li><strong>RAM:</strong> Minimum 8GB for smooth multitasking</li>
<li><strong>Storage:</strong> SSD is much faster than HDD - 256GB minimum</li>
<li><strong>Screen:</strong> 14-15.6" most popular, Full HD (1920x1080) recommended</li>
<li><strong>Battery:</strong> 6+ hours important for Uganda power situations</li>
</ul>',
                'faqs' => [
                    ['question' => 'How much is a good laptop in Uganda?', 'answer' => 'A good laptop for everyday use costs 1,200,000-1,800,000 UGX in Uganda. Budget options start from 800,000 UGX. Business and gaming laptops range from 2,000,000-4,500,000 UGX.'],
                    ['question' => 'Which laptop brand is best in Uganda?', 'answer' => 'HP, Dell, and Lenovo are the most trusted brands in Uganda with good after-sales support. All brands we stock come with genuine warranty.'],
                    ['question' => 'Do laptops come with warranty in Uganda?', 'answer' => 'Yes! All laptops from Yoola come with manufacturer warranty. We handle warranty claims locally in Kampala.'],
                    ['question' => 'Can I buy laptop on installments in Uganda?', 'answer' => 'Contact us on WhatsApp +256780221421 to discuss payment options. We may offer flexible payment for select customers.']
                ],
                'feature_filter' => 'laptop',
                'is_active' => true,
                'is_indexed' => true,
                'priority' => 92,
            ],

            // Deep Freezer - Uganda demand
            [
                'slug' => 'deep-freezer-price-in-uganda',
                'title' => 'Deep Freezer Price in Uganda 2026 | Chest Freezer | 100L-500L | Yoola',
                'meta_description' => 'Deep freezer prices in Uganda from 650,000 UGX. Chest freezers 100L-500L. Hisense, Samsung, Bruhm. For home and business. ✓ Free delivery Kampala. Shop Yoola.',
                'h1' => 'Deep Freezer Price in Uganda (Updated February 2026)',
                'intro_text' => "Deep freezers are essential in Uganda for preserving meat, fish, and bulk purchases. At Yoola, we stock quality chest freezers from 100L for small homes to 500L+ for butcheries and restaurants. Available from Hisense, Samsung, and other trusted brands with genuine warranty.",
                'buying_guide' => '<h3>Deep Freezer Buying Guide for Uganda</h3>
<p><strong>Capacity Guide:</strong></p>
<ul>
<li><strong>100-150L:</strong> Small families, basic storage - from 650,000 UGX</li>
<li><strong>200-300L:</strong> Medium families, bulk shopping - from 900,000 UGX</li>
<li><strong>300-400L:</strong> Large families, small businesses - from 1,200,000 UGX</li>
<li><strong>400L+:</strong> Butcheries, restaurants, commercial - from 1,500,000 UGX</li>
</ul>
<p><strong>Features to Look For:</strong></p>
<ul>
<li><strong>Fast Freeze:</strong> Quick-freezes fresh meat and fish</li>
<li><strong>Thick Insulation:</strong> Keeps food frozen longer during power cuts</li>
<li><strong>Lock:</strong> Security for businesses</li>
<li><strong>Wheels:</strong> Easy to move for cleaning</li>
</ul>
<p><strong>Power Considerations:</strong> Look for energy-efficient models with A+ or A++ rating. Important given UMEME costs and frequent outages.</p>',
                'faqs' => [
                    ['question' => 'How much is a deep freezer in Uganda?', 'answer' => 'Deep freezer prices at Yoola start from 650,000 UGX for 100L models. Large 400L+ commercial freezers range from 1,500,000-2,500,000 UGX.'],
                    ['question' => 'How long does deep freezer keep food during power cut?', 'answer' => 'A good deep freezer with thick insulation keeps food frozen for 24-48 hours during power outage, if you keep it closed. Some models last even longer.'],
                    ['question' => 'Which deep freezer brand is best in Uganda?', 'answer' => 'Hisense offers great value. Samsung is premium but durable. Bruhm is popular for businesses. All brands we stock come with warranty.'],
                    ['question' => 'Can deep freezer work with solar?', 'answer' => 'Yes, DC deep freezers are available for solar systems. Contact us for solar-compatible models and sizing advice.']
                ],
                'feature_filter' => 'freezer',
                'is_active' => true,
                'is_indexed' => true,
                'priority' => 88,
            ],

            // Electric Cooker
            [
                'slug' => 'electric-cooker-price-in-uganda',
                'title' => 'Electric Cooker Price in Uganda 2026 | Hotpoint, Bruhm | Free Oven | Yoola',
                'meta_description' => 'Electric cooker prices in Uganda from 550,000 UGX. 4-burner, 6-burner with oven. Hotpoint, Bruhm. Ceramic & coil. ✓ Free delivery Kampala. Shop at Yoola.',
                'h1' => 'Electric Cooker Price in Uganda (Updated February 2026)',
                'intro_text' => "Electric cookers offer clean, smoke-free cooking and precise temperature control. At Yoola, we stock electric standing cookers with ovens from Hotpoint, Bruhm, and Von. Available with ceramic (vitro) or coil (hot plate) cooking surfaces in 4 and 6 burner configurations.",
                'buying_guide' => '<h3>Electric Cooker Guide for Uganda</h3>
<p><strong>Types of Electric Cookers:</strong></p>
<ul>
<li><strong>Ceramic/Vitro Top:</strong> Smooth glass surface, easy to clean, heats faster</li>
<li><strong>Coil/Hot Plate:</strong> More affordable, replaceable elements</li>
<li><strong>Induction:</strong> Most efficient but needs special cookware</li>
</ul>
<p><strong>Oven Options:</strong></p>
<ul>
<li><strong>Electric Oven:</strong> Precise temperature, even baking</li>
<li><strong>Fan-Assisted:</strong> Faster cooking, no hot spots</li>
<li><strong>Rotisserie:</strong> For roasting chicken and meat</li>
</ul>
<p><strong>Power Requirements:</strong> Electric cookers need dedicated 30A socket and proper wiring. Ensure your electrical installation can handle the load.</p>',
                'faqs' => [
                    ['question' => 'How much is an electric cooker in Uganda?', 'answer' => 'Electric cooker prices at Yoola start from 550,000 UGX for basic 4-burner models. Premium ceramic cookers with fan ovens range from 1,200,000-2,500,000 UGX.'],
                    ['question' => 'Is electric cooker cheaper than gas in Uganda?', 'answer' => 'Gas is generally cheaper to run given UMEME electricity rates. However, electric cookers are cleaner, safer, and easier to maintain.'],
                    ['question' => 'Do electric cookers work during load shedding?', 'answer' => 'No, electric cookers need power. If outages are frequent in your area, consider a gas cooker or hybrid model as backup.'],
                    ['question' => 'What size electric cooker do I need?', 'answer' => '4-burner is sufficient for most homes. 6-burner is better for large families or if you cook multiple dishes simultaneously.']
                ],
                'product_type' => 'cooker',
                'feature_filter' => 'electric',
                'is_active' => true,
                'is_indexed' => true,
                'priority' => 85,
            ],

            // Water Dispenser
            [
                'slug' => 'water-dispenser-price-in-uganda',
                'title' => 'Water Dispenser Price in Uganda 2026 | Hot & Cold | Floor Standing | Yoola',
                'meta_description' => 'Water dispenser prices in Uganda from 280,000 UGX. Hot & cold, floor standing, table top. Geepas, Premier. ✓ Free delivery Kampala. Shop at Yoola.',
                'h1' => 'Water Dispenser Price in Uganda (Updated February 2026)',
                'intro_text' => "Water dispensers provide convenient access to hot and cold drinking water - perfect for homes and offices in Uganda. At Yoola, we stock floor standing and table top dispensers from Geepas, Premier, and other quality brands. Available with hot, cold, and normal temperature options.",
                'buying_guide' => '<h3>Water Dispenser Guide for Uganda</h3>
<p><strong>Types Available:</strong></p>
<ul>
<li><strong>Floor Standing:</strong> Full-size, holds standard 20L bottles, premium look</li>
<li><strong>Table Top:</strong> Compact, ideal for small spaces</li>
<li><strong>Bottom Loading:</strong> No heavy lifting - bottle goes at bottom</li>
</ul>
<p><strong>Temperature Options:</strong></p>
<ul>
<li><strong>Hot & Cold:</strong> Most popular, instant hot water for tea</li>
<li><strong>Cold & Normal:</strong> No heating, energy-saving</li>
<li><strong>Hot, Cold & Normal:</strong> Three temperature choices</li>
</ul>
<p><strong>Features to Consider:</strong></p>
<ul>
<li>Child safety lock on hot water tap</li>
<li>Cabinet/storage space below</li>
<li>LED indicators for temperature</li>
<li>Removable drip tray for easy cleaning</li>
</ul>',
                'faqs' => [
                    ['question' => 'How much is a water dispenser in Uganda?', 'answer' => 'Water dispenser prices at Yoola start from 280,000 UGX for table top models. Floor standing hot & cold dispensers range from 380,000-650,000 UGX.'],
                    ['question' => 'Which water dispenser brand is best in Uganda?', 'answer' => 'Geepas and Premier are popular for their reliability and affordability. All brands we stock come with warranty.'],
                    ['question' => 'Does water dispenser use a lot of electricity?', 'answer' => 'Typical dispenser uses 500-700W when heating, but heating is intermittent. Monthly power cost is around 15,000-25,000 UGX depending on usage.'],
                    ['question' => 'What size water bottle fits dispenser?', 'answer' => 'Standard 18.9L (20L) water bottles fit all our dispensers. These are the common refillable bottles sold at water shops across Uganda.']
                ],
                'feature_filter' => 'dispenser',
                'is_active' => true,
                'is_indexed' => true,
                'priority' => 82,
            ],

            // Sound System / Home Theatre
            [
                'slug' => 'home-theatre-price-in-uganda',
                'title' => 'Home Theatre Price in Uganda 2026 | Sony, LG, Samsung | Yoola',
                'meta_description' => 'Home theatre prices in Uganda from 450,000 UGX. 5.1 and 2.1 channel systems. Sony, LG, Samsung soundbars. ✓ Warranty ✓ Free delivery Kampala. Shop Yoola.',
                'h1' => 'Home Theatre Price in Uganda (Updated February 2026)',
                'intro_text' => "Transform your living room into a cinema with a quality home theatre system from Yoola. We stock 2.1 and 5.1 channel systems from Sony, LG, and Samsung - from compact soundbars to full surround sound setups. Perfect for movies, music, and connecting to your TV.",
                'buying_guide' => '<h3>Home Theatre Guide for Uganda</h3>
<p><strong>Types of Systems:</strong></p>
<ul>
<li><strong>2.1 Channel:</strong> Soundbar + subwoofer, simple setup, great for most rooms</li>
<li><strong>5.1 Channel:</strong> Full surround sound, immersive movie experience</li>
<li><strong>Soundbar Only:</strong> Space-saving, still better than TV speakers</li>
</ul>
<p><strong>Features to Look For:</strong></p>
<ul>
<li><strong>Bluetooth:</strong> Stream music from your phone</li>
<li><strong>HDMI ARC:</strong> Single cable connection to TV</li>
<li><strong>USB Playback:</strong> Play music from flash drive</li>
<li><strong>FM Radio:</strong> Listen to local stations</li>
<li><strong>Karaoke:</strong> Mic inputs for parties</li>
</ul>
<p><strong>Wattage Guide:</strong></p>
<ul>
<li><strong>300-500W:</strong> Small to medium rooms</li>
<li><strong>500-1000W:</strong> Large rooms, outdoor use</li>
<li><strong>1000W+:</strong> Events, commercial venues</li>
</ul>',
                'faqs' => [
                    ['question' => 'How much is a home theatre in Uganda?', 'answer' => 'Home theatre prices at Yoola start from 450,000 UGX for 2.1 soundbars. Premium 5.1 channel systems range from 1,200,000-3,500,000 UGX.'],
                    ['question' => 'Which home theatre brand is best in Uganda?', 'answer' => 'Sony offers premium sound quality. LG provides great value. Samsung integrates well with Samsung TVs. All brands we stock come with warranty.'],
                    ['question' => 'Can home theatre connect to any TV?', 'answer' => 'Yes, modern home theatres connect via HDMI ARC, optical cable, or Bluetooth. We can advise on compatibility with your TV.'],
                    ['question' => 'Is soundbar better than 5.1 system?', 'answer' => 'Soundbars are easier to set up and space-efficient. 5.1 systems provide true surround sound. Choose based on your room size and priorities.']
                ],
                'feature_filter' => 'theatre',
                'is_active' => true,
                'is_indexed' => true,
                'priority' => 80,
            ],

            // Microwave
            [
                'slug' => 'microwave-price-in-uganda',
                'title' => 'Microwave Price in Uganda 2026 | Samsung, LG | With Grill | Yoola',
                'meta_description' => 'Microwave oven prices in Uganda from 250,000 UGX. Solo, grill, convection. Samsung, LG, Geepas. 20-40L capacity. ✓ Warranty ✓ Free delivery. Shop Yoola.',
                'h1' => 'Microwave Price in Uganda (Updated February 2026)',
                'intro_text' => "Microwaves save time in Uganda's busy kitchens - reheat food in minutes, defrost meat quickly, and even cook simple meals. At Yoola, we stock microwaves from Samsung, LG, and Geepas in various sizes. Available in solo (basic), grill, and convection (full oven capability) models.",
                'buying_guide' => '<h3>Microwave Buying Guide for Uganda</h3>
<p><strong>Types of Microwaves:</strong></p>
<ul>
<li><strong>Solo:</strong> Basic reheating and defrosting - from 250,000 UGX</li>
<li><strong>Grill:</strong> Adds browning/grilling capability - from 380,000 UGX</li>
<li><strong>Convection:</strong> Full oven functions, baking possible - from 550,000 UGX</li>
</ul>
<p><strong>Capacity Guide:</strong></p>
<ul>
<li><strong>20-23L:</strong> Singles, couples, small portions</li>
<li><strong>25-30L:</strong> Small families, standard plates</li>
<li><strong>32-40L:</strong> Large families, bigger dishes</li>
</ul>
<p><strong>Features:</strong></p>
<ul>
<li>Auto cook menus (popcorn, pizza, etc.)</li>
<li>Defrost by weight or time</li>
<li>Child lock</li>
<li>Easy-clean interior coating</li>
</ul>',
                'faqs' => [
                    ['question' => 'How much is a microwave in Uganda?', 'answer' => 'Microwave prices at Yoola start from 250,000 UGX for basic 20L models. Larger grill microwaves range from 380,000-650,000 UGX.'],
                    ['question' => 'Which microwave brand is best in Uganda?', 'answer' => 'Samsung and LG are premium choices with advanced features. Geepas offers great value for basic needs. All come with warranty.'],
                    ['question' => 'Can microwave replace oven in Uganda?', 'answer' => 'Convection microwaves can bake cakes and roast. For serious baking, a dedicated oven is better. Convection microwaves are good for occasional baking.'],
                    ['question' => 'Is microwave safe to use?', 'answer' => 'Yes, microwaves are safe when used correctly. Never put metal inside. Use microwave-safe containers. Our staff can explain safe usage.']
                ],
                'feature_filter' => 'microwave',
                'is_active' => true,
                'is_indexed' => true,
                'priority' => 78,
            ],

            // Iron Box
            [
                'slug' => 'iron-box-price-in-uganda',
                'title' => 'Iron Box Price in Uganda 2026 | Steam Iron | Philips, Geepas | Yoola',
                'meta_description' => 'Iron box prices in Uganda from 45,000 UGX. Steam irons, dry irons. Philips, Geepas, Black+Decker. ✓ Warranty ✓ Free delivery Kampala. Shop at Yoola.',
                'h1' => 'Iron Box Price in Uganda (Updated February 2026)',
                'intro_text' => "A quality iron box keeps your clothes looking sharp. At Yoola, we stock steam irons and dry irons from Philips, Geepas, and Black+Decker. Steam irons remove wrinkles faster with less effort - perfect for busy professionals and large families.",
                'buying_guide' => '<h3>Iron Box Guide for Uganda</h3>
<p><strong>Types of Irons:</strong></p>
<ul>
<li><strong>Dry Iron:</strong> Basic, affordable, good for simple fabrics - from 45,000 UGX</li>
<li><strong>Steam Iron:</strong> Uses steam to remove wrinkles faster - from 85,000 UGX</li>
<li><strong>Steam Generator:</strong> Continuous steam, professional results - from 350,000 UGX</li>
</ul>
<p><strong>Features to Look For:</strong></p>
<ul>
<li><strong>Wattage:</strong> 1800-2400W heats faster and maintains temperature</li>
<li><strong>Soleplate:</strong> Ceramic glides smoothly, stainless steel is durable</li>
<li><strong>Anti-drip:</strong> Prevents water spots on fabric</li>
<li><strong>Auto shut-off:</strong> Safety feature when left unattended</li>
<li><strong>Self-clean:</strong> Removes mineral buildup</li>
</ul>',
                'faqs' => [
                    ['question' => 'How much is an iron box in Uganda?', 'answer' => 'Iron box prices at Yoola start from 45,000 UGX for dry irons. Quality steam irons range from 85,000-180,000 UGX.'],
                    ['question' => 'Is steam iron better than dry iron?', 'answer' => 'Steam irons remove wrinkles faster and easier, especially on cotton and linen. Worth the extra cost for heavy ironing.'],
                    ['question' => 'Which iron box brand is best in Uganda?', 'answer' => 'Philips is the gold standard for irons. Geepas offers great value. All brands we stock come with warranty.'],
                    ['question' => 'How long does iron box last?', 'answer' => 'A quality iron lasts 3-5+ years with proper care. Use clean water (preferably filtered) and clean the soleplate regularly.']
                ],
                'feature_filter' => 'iron',
                'is_active' => true,
                'is_indexed' => true,
                'priority' => 75,
            ],

            // Fan
            [
                'slug' => 'fan-price-in-uganda',
                'title' => 'Fan Price in Uganda 2026 | Standing Fan, Ceiling Fan | Geepas | Yoola',
                'meta_description' => 'Fan prices in Uganda from 65,000 UGX. Standing fans, ceiling fans, table fans. Geepas, Lontor, rechargeable. ✓ Warranty ✓ Free delivery Kampala. Shop Yoola.',
                'h1' => 'Fan Price in Uganda (Updated February 2026)',
                'intro_text' => "Beat Uganda's heat with quality fans from Yoola. We stock standing fans, ceiling fans, table fans, and rechargeable fans. Brands include Geepas, Lontor, and Premier. Rechargeable fans are perfect for areas with frequent power outages.",
                'buying_guide' => '<h3>Fan Buying Guide for Uganda</h3>
<p><strong>Types of Fans:</strong></p>
<ul>
<li><strong>Standing Fan:</strong> Adjustable height, oscillates, most popular - from 85,000 UGX</li>
<li><strong>Ceiling Fan:</strong> Permanent installation, cools large areas - from 120,000 UGX</li>
<li><strong>Table Fan:</strong> Compact, portable, for desks - from 65,000 UGX</li>
<li><strong>Rechargeable Fan:</strong> Works during power outage - from 95,000 UGX</li>
</ul>
<p><strong>Features:</strong></p>
<ul>
<li><strong>Speed Settings:</strong> 3-5 speeds for different needs</li>
<li><strong>Oscillation:</strong> Swings side to side for wider coverage</li>
<li><strong>Timer:</strong> Auto shut-off at night</li>
<li><strong>Remote Control:</strong> Convenience for ceiling fans</li>
</ul>
<p><strong>Rechargeable Fans:</strong> Essential in Uganda! Look for 6+ hour battery life and ability to charge while using.</p>',
                'faqs' => [
                    ['question' => 'How much is a fan in Uganda?', 'answer' => 'Fan prices at Yoola start from 65,000 UGX for table fans. Standing fans range from 85,000-180,000 UGX. Rechargeable fans start from 95,000 UGX.'],
                    ['question' => 'Which fan brand is best in Uganda?', 'answer' => 'Geepas and Lontor are popular for reliability. Rechargeable fans from Lontor are especially popular due to power outages.'],
                    ['question' => 'How long does rechargeable fan last?', 'answer' => 'Most rechargeable fans run 4-8 hours on full charge at medium speed. Higher speeds drain battery faster.'],
                    ['question' => 'Is ceiling fan better than standing fan?', 'answer' => 'Ceiling fans cool larger areas and save floor space. Standing fans are portable and don\'t need installation. Choose based on your needs.']
                ],
                'feature_filter' => 'fan',
                'is_active' => true,
                'is_indexed' => true,
                'priority' => 72,
            ],
        ];

        foreach ($pages as $page) {
            PricePage::updateOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }

        $this->command->info('Created ' . count($pages) . ' additional price pages!');
    }
}
