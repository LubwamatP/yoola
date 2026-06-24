<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

/**
 * Deep Link Controller
 *
 * Handles serving verification files for Android App Links and iOS Universal Links
 * These files prove to Google and Apple that yoola.ug authorizes the app to open its links.
 *
 * When a user clicks a link like https://yoola.ug/product/samsung-260l-xxx:
 * - If App is Installed: Opens the Yoola Flutter app directly to the product page
 * - If App is NOT Installed: Opens the mobile web browser to the product page
 */
class DeepLinkController extends Controller
{
    /**
     * Android App Links verification file
     *
     * Location: /.well-known/assetlinks.json
     *
     * IMPORTANT: Update the sha256_cert_fingerprints with your actual release key fingerprint
     * To get your SHA256 fingerprint, run:
     * keytool -list -v -keystore your-release-key.keystore -alias your-key-alias
     *
     * @return Response
     */
    public function androidAssetLinks(): Response
    {
        $config = [
            [
                'relation' => ['delegate_permission/common.handle_all_urls'],
                'target' => [
                    'namespace' => 'android_app',
                    'package_name' => config('app.android_package_name', 'com.yoola.app'),
                    'sha256_cert_fingerprints' => [
                        config('app.android_sha256_fingerprint', 'YOUR_SHA256_FINGERPRINT_HERE')
                    ]
                ]
            ]
        ];

        return response()->json($config)
            ->header('Content-Type', 'application/json');
    }

    /**
     * iOS Universal Links verification file
     *
     * Location: /.well-known/apple-app-site-association
     *
     * IMPORTANT:
     * - This file must NOT have a .json extension
     * - Must be served with Content-Type: application/json
     * - Update TEAMID1234 with your actual Apple Team ID
     * - Update com.yoola.app with your actual Bundle ID
     *
     * To find your Team ID:
     * Apple Developer Portal > Membership > Team ID
     *
     * @return Response
     */
    public function appleAppSiteAssociation(): Response
    {
        $teamId = config('app.apple_team_id', 'TEAMID1234');
        $bundleId = config('app.ios_bundle_id', 'com.yoola.app');
        $appId = "{$teamId}.{$bundleId}";

        $config = [
            'applinks' => [
                'apps' => [], // Must be empty array
                'details' => [
                    [
                        'appID' => $appId,
                        'paths' => [
                            '/product/*',           // Product detail pages
                            '/products/*',          // Product listings
                            '/shop/*',              // Shop/store pages
                            '/category/*',          // Category pages
                            '/brand/*',             // Brand pages
                            '/flash-deals/*',       // Flash deals
                            '/search/*',            // Search results
                            '/seller/*',            // Seller pages
                            '/order-tracking/*',    // Order tracking
                        ]
                    ]
                ]
            ],
            'webcredentials' => [
                'apps' => [$appId]
            ]
        ];

        return response()->json($config)
            ->header('Content-Type', 'application/json');
    }

    /**
     * Parse product slug to extract product ID
     *
     * URL Format: /product/{slug}-{id}
     * Example: /product/samsung-260l-stabiliser-coolpack-rt26har2dsa-IwYJWR
     *
     * This extracts "IwYJWR" as the unique product identifier
     *
     * @param string $slug The full product slug from URL
     * @return string|null The extracted product ID
     */
    public static function extractProductIdFromSlug(string $slug): ?string
    {
        // The ID is typically the last segment after the final hyphen
        $parts = explode('-', $slug);

        if (count($parts) > 1) {
            return end($parts);
        }

        return $slug;
    }
}
