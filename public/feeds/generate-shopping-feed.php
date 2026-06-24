<?php
/**
 * Google Shopping Product Feed Generator for Yoola.ug
 * Updated: 2026-03-10 - Added discount/sale_price support
 */

$db_host = 'localhost';
$db_name = 'zuuldqak_fireword';
$db_user = 'zuuldqak_kungu';
$db_pass = 'Q!6_CvdkVWx9MAta';
$site_url = 'https://yoola.ug';
$storage_url = 'https://yoola.ug/storage/product/thumbnail';
$output_file = __DIR__ . '/google-shopping-feed.xml';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "
        SELECT p.id, p.name, p.slug, p.unit_price, p.discount, p.discount_type,
               p.thumbnail, p.current_stock, p.details, b.name as brand_name
        FROM products p
        LEFT JOIN brands b ON p.brand_id = b.id
        WHERE p.status = 1 ORDER BY p.id ASC
    ";

    $products = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

    $xml = new XMLWriter();
    $xml->openMemory();
    $xml->setIndent(true);
    $xml->startDocument('1.0', 'UTF-8');
    $xml->startElement('feed');
    $xml->writeAttribute('xmlns', 'http://www.w3.org/2005/Atom');
    $xml->writeAttribute('xmlns:g', 'http://base.google.com/ns/1.0');
    $xml->writeElement('title', 'Yoola.ug Product Feed');
    $xml->writeElement('link', $site_url);
    $xml->writeElement('updated', date('c'));

    $product_count = 0;
    $discounted_count = 0;

    foreach ($products as $product) {
        if (empty($product['name']) || empty($product['slug']) || empty($product['unit_price'])) continue;

        $unit_price = (float)$product['unit_price'];
        $discount = (float)$product['discount'];
        $discount_type = $product['discount_type'];
        $has_discount = ($discount > 0);
        $sale_price = $unit_price;
        
        if ($has_discount) {
            if ($discount_type === 'percent') {
                $sale_price = $unit_price - ($unit_price * $discount / 100);
            } else {
                $sale_price = $unit_price - $discount;
            }
            if ($sale_price < 0) $sale_price = 0;
            $discounted_count++;
        }

        $availability = ($product['current_stock'] > 0) ? 'in_stock' : 'out_of_stock';
        $description = strip_tags($product['details'] ?? '');
        $description = html_entity_decode($description, ENT_QUOTES, 'UTF-8');
        $description = trim(preg_replace('/\s+/', ' ', $description));
        if (strlen($description) > 5000) $description = substr($description, 0, 4997) . '...';
        if (empty($description)) $description = $product['name'] . ' - Available at Yoola.ug Uganda';

        $image_url = !empty($product['thumbnail']) ? $storage_url . '/' . $product['thumbnail'] : '';
        $product_url = $site_url . '/product/' . $product['slug'];
        $price_formatted = number_format($unit_price, 2, '.', '') . ' UGX';
        $sale_price_formatted = number_format($sale_price, 2, '.', '') . ' UGX';
        $title = html_entity_decode($product['name'], ENT_QUOTES, 'UTF-8');
        $title = trim(preg_replace('/\s+/', ' ', $title));
        if (strlen($title) > 150) $title = substr($title, 0, 147) . '...';

        $xml->startElement('entry');
        $xml->writeElement('g:id', 'YOOLA-' . $product['id']);
        $xml->writeElement('g:title', $title);
        $xml->writeElement('g:description', $description);
        $xml->writeElement('g:link', $product_url);
        if (!empty($image_url)) $xml->writeElement('g:image_link', $image_url);
        $xml->writeElement('g:price', $price_formatted);
        if ($has_discount) $xml->writeElement('g:sale_price', $sale_price_formatted);
        $xml->writeElement('g:availability', $availability);
        $xml->writeElement('g:condition', 'new');
        if (!empty($product['brand_name'])) $xml->writeElement('g:brand', $product['brand_name']);
        $xml->writeElement('g:product_type', 'Electronics');
        $xml->writeElement('g:google_product_category', 'Electronics');
        $xml->writeElement('g:shipping_country', 'UG');
        $xml->endElement();
        $product_count++;
    }

    $xml->endElement();
    file_put_contents($output_file, $xml->outputMemory());

    echo "SUCCESS: Feed generated!\n";
    echo "Products: $product_count\n";
    echo "With discounts: $discounted_count\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>
