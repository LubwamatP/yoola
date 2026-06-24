<?php

use App\Enums\ViewPaths\Web\ProductCompare;
use App\Enums\ViewPaths\Web\ShopFollower;
use App\Http\Controllers\Customer\Auth\CustomerAuthController;
use App\Http\Controllers\Customer\Auth\ForgotPasswordController;
use App\Http\Controllers\Customer\Auth\LoginController;
use App\Http\Controllers\Customer\Auth\RegisterController;
use App\Http\Controllers\Customer\Auth\SocialAuthController;
use App\Http\Controllers\Customer\PaymentController;
use App\Http\Controllers\Customer\RewardPointController;
use App\Http\Controllers\Customer\SystemController;
use App\Http\Controllers\Payment_Methods\BkashPaymentController;
use App\Http\Controllers\Payment_Methods\FlutterwaveV3Controller;
use App\Http\Controllers\Payment_Methods\LiqPayController;
use App\Http\Controllers\Payment_Methods\MercadoPagoController;
use App\Http\Controllers\Payment_Methods\PaymobController;
use App\Http\Controllers\Payment_Methods\PaypalPaymentController;
use App\Http\Controllers\Payment_Methods\PaystackController;
use App\Http\Controllers\Payment_Methods\PaytabsController;
use App\Http\Controllers\Payment_Methods\PaytmController;
use App\Http\Controllers\Payment_Methods\RazorPayController;
use App\Http\Controllers\Payment_Methods\SenangPayController;
use App\Http\Controllers\Payment_Methods\SslCommerzPaymentController;
use App\Http\Controllers\Payment_Methods\StripePaymentController;
use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\Web\ChattingController;
use App\Http\Controllers\Web\CouponController;
use App\Http\Controllers\Web\CurrencyController;
use App\Http\Controllers\Web\DigitalProductDownloadController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\PageController;
use App\Http\Controllers\Web\ProductCompareController;
use App\Http\Controllers\Web\ProductDetailsController;
use App\Http\Controllers\Web\ProductListController;
use App\Http\Controllers\Web\ReviewController;
use App\Http\Controllers\Web\Shop\ShopFollowerController;
use App\Http\Controllers\Web\ShopViewController;
use App\Http\Controllers\Web\UserLoyaltyController;
use App\Http\Controllers\Web\UserProfileController;
use App\Http\Controllers\Web\UserWalletController;
use App\Http\Controllers\Web\WebController;
use App\Http\Controllers\Web\ProductTrackingController;
use App\Http\Controllers\Web\PresenceController;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\DeepLinkController;

/*
|--------------------------------------------------------------------------
// Web Routes
|--------------------------------------------------------------------------
|
// Here is where you can register web routes for your application. These
// routes are loaded by the RouteServiceProvider and all of them will
// be assigned to the "web" middleware group. Make something great!
|
*/

/*
|--------------------------------------------------------------------------
// Deep Link Verification Routes (App Links & Universal Links)
|--------------------------------------------------------------------------
|
// These routes serve verification files for Android App Links and iOS Universal Links.
// When a user clicks https://yoola.ug/product/xxx:
// - If App is Installed: Opens directly in the Yoola Flutter app
// - If App is NOT Installed: Opens in the mobile web browser
|
*/
Route::get('.well-known/assetlinks.json', [DeepLinkController::class, 'androidAssetLinks']);
Route::get('.well-known/apple-app-site-association', [DeepLinkController::class, 'appleAppSiteAssociation']);
Route::get('/image-proxy', function () {
    $url = request('url');
    if (!$url) {
        abort(400, 'Missing url parameter');
    }
    $response = Http::withHeaders(['User-Agent' => 'Laravel-Image-Proxy'])->get($url);
    return response($response->body(), $response->status())
        ->header('Content-Type', $response->header('Content-Type'))
        ->header('Access-Control-Allow-Origin', '*');
});

Route::get('/search', [SearchController::class, 'search'])->name('search');
Route::get('/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');
Route::get('/api/search', [SearchController::class, 'apiSearch'])->name('api.search');

// Product View Tracking & Recommendations API
Route::group(['prefix' => 'tracking', 'as' => 'tracking.'], function () {
    Route::controller(ProductTrackingController::class)->group(function () {
        Route::post('view', 'recordView')->name('view');
        Route::post('cart', 'markAddedToCart')->name('cart');
        Route::get('recently-viewed', 'getRecentlyViewed')->name('recently-viewed');
        Route::get('also-viewed', 'getAlsoViewed')->name('also-viewed');
        Route::get('trending', 'getTrending')->name('trending');
        Route::get('personalized', 'getPersonalized')->name('personalized');
    });
});

// Real-time Presence Tracking (Heartbeat)
Route::group(['prefix' => 'presence', 'as' => 'presence.'], function () {
    Route::controller(PresenceController::class)->group(function () {
        Route::post('heartbeat', 'heartbeat')->name('heartbeat');
        Route::post('leave', 'leave')->name('leave');
        Route::get('product-viewers', 'getProductViewers')->name('product-viewers');
    });
});

Route::get('/power-calculator', [\App\Http\Controllers\Web\PowerCalculatorController::class, 'index'])
    ->name('power-calculator.index');

Route::controller(WebController::class)->group(function () {
    Route::get('maintenance-mode', 'maintenance_mode')->name('maintenance-mode');
});

Route::group(['namespace' => 'Web', 'middleware' => ['maintenance_mode', 'guestCheck']], function () {
    Route::group(['prefix' => 'product-compare', 'as' => 'product-compare.'], function () {
        Route::controller(ProductCompareController::class)->group(function () {
            Route::get(ProductCompare::INDEX[URI], 'index')->name('index')->middleware('customer');
            Route::post(ProductCompare::INDEX[URI], 'add');
            Route::get(ProductCompare::DELETE[URI], 'delete')->name('delete');
            Route::get(ProductCompare::DELETE_ALL[URI], 'deleteAllCompareProduct')->name('delete-all');
        });
    });
    Route::post(ShopFollower::SHOP_FOLLOW[URI], [ShopFollowerController::class, 'followOrUnfollowShop'])->name('shop-follow');
});

Route::group(['namespace' => 'Web', 'middleware' => ['maintenance_mode', 'guestCheck']], function () {

    Route::controller(HomeController::class)->group(function () {
        Route::get('/', 'index')->name('home');
    });

    Route::controller(WebController::class)->group(function () {
        Route::get('quick-view', 'getQuickView')->name('quick-view');
        Route::get('searched-products', 'getSearchedProducts')->name('searched-products');
    });

    Route::group(['middleware' => ['customer']], function () {
        Route::controller(ReviewController::class)->group(function () {
            Route::post('review', 'add')->name('review.store');
            Route::post('submit-deliveryman-review', 'addDeliveryManReview')->name('submit-deliveryman-review');
            Route::post('review-delete-image', 'deleteReviewImage')->name('delete-review-image');
        });
    });

    Route::controller(WebController::class)->group(function () {
        Route::get('checkout-details', 'checkout_details')->name('checkout-details');
        Route::get('checkout-shipping', 'checkout_shipping')->name('checkout-shipping');
        Route::get('checkout-payment', 'checkout_payment')->name('checkout-payment');
        Route::get('checkout-review', 'checkout_review')->name('checkout-review');
        Route::get('checkout-complete', 'getCashOnDeliveryCheckoutComplete')->name('checkout-complete');
        Route::post('offline-payment-checkout-complete', 'getOfflinePaymentCheckoutComplete')->name('offline-payment-checkout-complete');
        Route::get('order-placed', 'order_placed')->name('order-placed');
        Route::get('order-placed-success', 'getOrderPlaceView')->name('order-placed-success');
        Route::get('shop-cart', 'shop_cart')->name('shop-cart');
        Route::post('order_note', 'order_note')->name('order_note');
        Route::get('digital-product-download/{id}', 'getDigitalProductDownload')->name('digital-product-download');
        Route::post('digital-product-download-otp-verify', 'getDigitalProductDownloadOtpVerify')->name('digital-product-download-otp-verify');
        Route::post('digital-product-download-otp-reset', 'getDigitalProductDownloadOtpReset')->name('digital-product-download-otp-reset');
        Route::get('pay-offline-method-list', 'pay_offline_method_list')->name('pay-offline-method-list')->middleware('guestCheck');
        Route::get('checkout-complete-wallet', 'checkout_complete_wallet')->name('checkout-complete-wallet');

        Route::post('subscription', 'subscription')->name('subscription');
        Route::get('search-shop', 'search_shop')->name('search-shop');

        Route::get('categories', 'getAllCategoriesView')->name('categories');
        Route::get('category-ajax/{id}', 'categories_by_category')->name('category-ajax');

        Route::get('brands', 'getAllBrandsView')->name('brands');
        Route::get('vendors', 'getAllVendorsView')->name('vendors');
        Route::get('seller-profile/{id}', 'seller_profile')->name('seller-profile');
    });

    Route::controller(PageController::class)->group(function () {
        Route::get('business-page/{slug}', 'getPageView')->name('business-page.view');
        Route::get('contacts', 'getContactView')->name('contacts');
        Route::get('helpTopic', 'getHelpTopicView')->name('helpTopic');
    });

    Route::controller(ProductDetailsController::class)->group(function () {
        Route::get('/product/{slug}', 'index')->name('product');
    });

    Route::controller(ProductListController::class)->group(function () {
        Route::get('products', 'products')->name('products');
        Route::get('flash-deals/{id}', 'getFlashDealsView')->name('flash-deals');
        Route::post('flash-deals/{id}', 'getFlashDealsProducts');

        Route::get('brand/{slug}', 'getBrandProductsView')->name('brand-products');
        
        // SEO Redirect: Old URLs to new format (fixes 404s from Google-indexed pages)
        Route::get('product-category/{slug}', function ($slug) {
            return redirect('/category/' . $slug, 301);
        });
        
        Route::get('category/{slug}', 'getCategoryProductsView')->name('category-products');
        Route::get('featured-products', 'getFeaturedProductsView')->name('featured-products');
        Route::get('featured-deal-products', 'getFeaturedDealProductsView')->name('featured-deal-products');
        Route::get('latest-products', 'getLatestProductsView')->name('latest-products');
        Route::get('best-selling-products', 'getBestSellingProductsView')->name('best-selling-products');
        Route::get('top-rated-products', 'getTopRatedProductsView')->name('top-rated-products');
        Route::get('most-favorite-products', 'getMostFavoriteProductsView')->name('most-favorite-products');
        Route::get('discounted-products', 'getDiscountedProductsView')->name('discounted-products');
        Route::get('clearance-sale-products', 'getClearanceSaleProductsView')->name('clearance-sale-products');
    });

    // ==================== BUNDLE ROUTES ====================
    // TEMPORARILY DISABLED - Views not created yet
    // Uncomment when bundle views are ready
    /*
    Route::group(['prefix' => 'bundles', 'as' => 'bundles.'], function () {
        Route::controller(\App\Http\Controllers\Web\BundleController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{slug}', 'show')->name('show');
            Route::post('/add-to-cart/{bundleId}', 'addToCart')->name('add-to-cart');
            Route::get('/quick-view/{bundleId}', 'quickView')->name('quick-view');
        });
    });
    Route::get('/bundles-homepage', [\App\Http\Controllers\Web\BundleController::class, 'homepageBundles'])->name('bundles.homepage');
    Route::get('/bundles-featured', [\App\Http\Controllers\Web\BundleController::class, 'featuredBundles'])->name('bundles.featured');
    */
    // ==================== END BUNDLE ROUTES ====================

    Route::controller(ShopViewController::class)->group(function () {
        Route::post('ajax-filter-products', 'filterProductsAjaxResponse')->name('ajax-filter-products');
    });

    Route::controller(WebController::class)->group(function () {
        Route::post('/products-view-style', 'product_view_style')->name('product_view_style');
        Route::post('review-list-product', 'review_list_product')->name('review-list-product');
        Route::post('review-list-shop', 'getShopReviewList')->name('review-list-shop'); // theme fashion
        Route::get('wishlists', 'viewWishlist')->name('wishlists')->middleware('customer');
        Route::post('store-wishlist', 'storeWishlist')->name('store-wishlist');
        Route::post('delete-wishlist', 'deleteWishlist')->name('delete-wishlist');
        Route::get('delete-wishlist-all', 'deleteAllWishListItems')->name('delete-wishlist-all')->middleware('customer');
        Route::get('searched-products-for-compare', 'getSearchedProductsForCompareList')->name('searched-products-compare'); // theme fashion compare list
    });

    Route::controller(CurrencyController::class)->group(function () {
        Route::post('/currency', 'changeCurrency')->name('currency.change');
    });

    Route::controller(UserProfileController::class)->group(function () {
        Route::group(['prefix' => 'support-ticket', 'as' => 'support-ticket.', 'middleware' => 'customer'], function () {
            Route::get('{id}', 'single_ticket')->name('index');
            Route::post('{id}', 'comment_submit')->name('comment');
            Route::get('delete/{id}', 'support_ticket_delete')->name('delete');
            Route::get('close/{id}', 'support_ticket_close')->name('close');
        });
    });

    Route::controller(UserProfileController::class)->group(function () {
        Route::group(['prefix' => 'track-order', 'as' => 'track-order.'], function () {
            Route::get('', 'track_order')->name('index');
            Route::get('result-view', 'track_order_result')->name('result-view');
            Route::get('last', 'track_last_order')->name('last');
            Route::any('result', 'track_order_result')->name('result');
            Route::get('order-wise-result-view', 'track_order_wise_result')->name('order-wise-result-view');
        });
    });

    Route::controller(UserProfileController::class)->group(function () {
        Route::get('user-profile', 'user_profile')->name('user-profile')->middleware('customer'); //theme_aster
        Route::get('user-account', 'user_account')->name('user-account')->middleware('customer');
        Route::post('user-account-update', 'getUserProfileUpdate')->name('user-update')->middleware('customer');
        Route::post('user-account-picture', 'user_picture')->name('user-picture');
        Route::get('account-address-add', 'account_address_add')->name('account-address-add');
        Route::get('account-address', 'account_address')->name('account-address');
        Route::post('account-address-store', 'address_store')->name('address-store');
        Route::get('account-address-delete', 'address_delete')->name('address-delete');
        ROute::get('account-address-edit/{id}', 'address_edit')->name('address-edit');
        Route::post('account-address-update', 'address_update')->name('address-update');
        Route::get('account-payment', 'account_payment')->name('account-payment');
        Route::get('account-oder', 'account_order')->name('account-oder')->middleware('customer');
        Route::get('account-order-details', 'account_order_details')->name('account-order-details')->middleware('customer');
        Route::get('account-order-details-vendor-info', 'account_order_details_seller_info')->name('account-order-details-vendor-info')->middleware('customer');
        Route::get('account-order-details-delivery-man-info', 'account_order_details_delivery_man_info')->name('account-order-details-delivery-man-info')->middleware('customer');
        Route::get('account-order-details-reviews', 'getAccountOrderDetailsReviewsView')->name('account-order-details-reviews')->middleware('customer');
        Route::get('generate-invoice/{id}', 'generate_invoice')->name('generate-invoice');
        Route::get('account-wishlist', 'account_wishlist')->name('account-wishlist'); //add to card not work
        Route::get('refund-request/{id}', 'refund_request')->name('refund-request');
        Route::get('refund-details/{id}', 'refund_details')->name('refund-details');
        Route::post('refund-store', 'store_refund')->name('refund-store');
        Route::get('account-tickets', 'account_tickets')->name('account-tickets');
        Route::get('order-cancel/{id}', 'order_cancel')->name('order-cancel');
        Route::post('ticket-submit', 'submitSupportTicket')->name('ticket-submit');
        Route::get('account-delete/{id}', 'account_delete')->name('account-delete');
        Route::get('refer-earn', 'refer_earn')->name('refer-earn')->middleware('customer');
        Route::get('user-coupons', 'user_coupons')->name('user-coupons')->middleware('customer');
        Route::get('user-restock-requests', 'restockRequestsView')->name('user-restock-requests')->middleware('customer');
        Route::get('user-restock-request-delete', 'deleteRestockRequest')->name('user-restock-request-delete')->middleware('customer');
        Route::get('user-all-restock-request-delete/{ids}', 'deleteAllRestockRequest')->name('user-all-restock-request-delete')->middleware('customer');
    });

    Route::controller(ChattingController::class)->group(function () {
        Route::get('chat/{type}', 'index')->name('chat')->middleware('customer');
        Route::get('message', 'getMessageByUser')->name('messages');
        Route::post('message', 'addMessage');
    });

    Route::controller(UserWalletController::class)->group(function () {
        Route::get('wallet-account', 'myWalletAccount')->name('wallet-account'); //theme fashion
        Route::get('wallet', 'index')->name('wallet')->middleware('customer');
    });

    Route::controller(UserLoyaltyController::class)->group(function () {
        Route::get('loyalty', 'index')->name('loyalty')->middleware('customer');
        Route::post('loyalty-exchange-currency', 'getLoyaltyExchangeCurrency')->name('loyalty-exchange-currency');
        Route::get('ajax-loyalty-currency-amount', 'getLoyaltyCurrencyAmount')->name('ajax-loyalty-currency-amount');
    });

    Route::controller(DigitalProductDownloadController::class)->group(function () {
        Route::group(['prefix' => 'digital-product-download-pos', 'as' => 'digital-product-download-pos.'], function () {
            Route::get('/', 'index')->name('index');
        });
    });

    Route::controller(ShopViewController::class)->group(function () {
        Route::get('vendor-shop/{slug}', 'seller_shop')->name('vendor-shop');
        Route::get('ajax-shop-vacation-check', 'ajax_shop_vacation_check')->name('ajax-shop-vacation-check');
    });

    Route::controller(WebController::class)->group(function () {
        Route::post('vendor-shop/{id}', 'seller_shop_product');
        Route::get('top-rated', 'top_rated')->name('topRated');
        Route::get('best-sell', 'best_sell')->name('bestSell');
        Route::get('new-product', 'new_product')->name('newProduct');
    });

    Route::group(['prefix' => 'contact', 'as' => 'contact.'], function () {
        Route::controller(WebController::class)->group(function () {
            Route::post('store', 'contact_store')->name('store');
            Route::get('/code/captcha/{tmp}', 'captcha')->name('default-captcha');
        });
    });
});

Route::group(['prefix' => 'cart', 'as' => 'cart.', 'namespace' => 'Web'], function () {
    Route::controller(CartController::class)->group(function () {
        Route::post('variant_price', 'getVariantPrice')->name('variant_price');
        Route::post('add', 'addToCart')->name('add');
        Route::post('add-all-to-cart', 'addAllToCartFromWishtList')->name('add-all-to-cart');
        Route::post('update-variation', 'update_variation')->name('update-variation'); //theme fashion
        Route::post('remove', 'removeFromCart')->name('remove');
        Route::get('remove-all', 'remove_all_cart')->name('remove-all'); //theme fashion
        Route::post('nav-cart-items', 'updateNavCart')->name('nav-cart');
        Route::post('floating-nav-cart-items', 'update_floating_nav')->name('floating-nav-cart-items'); // theme fashion floating nav
        Route::post('updateQuantity', 'updateQuantity')->name('updateQuantity');
        Route::post('updateQuantity-guest', 'updateQuantity_guest')->name('updateQuantity.guest');
        Route::post('order-again', 'orderAgain')->name('order-again')->middleware('customer');
        Route::post('select-cart-items', 'updateCheckedCartItems')->name('select-cart-items');
        Route::post('product-restock-request', 'addProductRestockRequest')->name('product-restock-request');
    });
});

Route::group(['prefix' => 'coupon', 'as' => 'coupon.', 'namespace' => 'Web'], function () {
    Route::controller(CouponController::class)->group(function () {
        Route::post('apply', 'apply')->name('apply');
        Route::get('remove', 'removeCoupon')->name('remove');
    });
});

Route::get('authentication-failed', function () {
    $errors = [];
    array_push($errors, ['code' => 'auth-001', 'message' => 'Unauthorized.']);
    return response()->json([
        'errors' => $errors
    ], 401);
})->name('authentication-failed');

Route::group(['namespace' => 'Customer', 'prefix' => 'customer', 'as' => 'customer.'], function () {

    Route::group(['namespace' => 'Auth', 'prefix' => 'auth', 'as' => 'auth.'], function () {

        Route::controller(CustomerAuthController::class)->group(function () {
            Route::get('login', 'loginView')->name('login');
            Route::post('login', 'loginSubmit');
            Route::get('login/verify-account', 'loginVerifyPhone')->name('login.verify-account');
            Route::post('login/verify-account/submit', 'verifyAccount')->name('login.verify-account.submit');
            Route::get('login/update-info', 'updateInfo')->name('login.update-info');
            Route::post('login/update-info', 'updateInfoSubmit');
            Route::post('login/resend-otp-code', 'resendOTPCode')->name('resend-otp-code');
        });

        Route::controller(LoginController::class)->group(function () {
            Route::get('logout', 'logout')->name('logout');
            Route::get('get-login-modal-data', 'getLoginModalView')->name('get-login-modal-data');
        });

        Route::controller(RegisterController::class)->group(function () {
            Route::get('sign-up', 'getRegisterView')->name('sign-up');
            Route::post('sign-up', 'submitRegisterData');
            Route::get('check-verification', 'verificationCheckView')->name('check-verification');
            Route::post('verify', 'verifyRegistration')->name('verify');
            Route::post('ajax-verify', 'ajax_verify')->name('ajax_verify');
            Route::post('resend-otp', 'resendOTPToCustomer')->name('resend_otp');
        });

        Route::controller(SocialAuthController::class)->group(function () {
            Route::get('login/{service}', 'redirectToProvider')->name('service-login');
            Route::get('login/{service}/callback', 'handleProviderCallback')->name('service-callback');
            Route::get('login/social/confirmation', 'socialLoginConfirmation')->name('social-login-confirmation');
            Route::post('login/social/confirmation/update', 'updateSocialLoginConfirmation')->name('social-login-confirmation.update');
            Route::post('login/social/verify-account', 'verifyAccount')->name('login.social.verify-account');
        });

        Route::controller(ForgotPasswordController::class)->group(function () {
            Route::get('recover-password', 'reset_password')->name('recover-password');
            Route::post('forgot-password', 'resetPasswordRequest')->name('forgot-password');
            Route::post('verify-recover-password', 'verifyRecoverPassword')->name('verify-recover-password');
            Route::get('otp-verification', 'otp_verification')->name('otp-verification');
            Route::post('otp-verification', 'otp_verification_submit');
            Route::get('reset-password', 'resetPasswordView')->name('reset-password');
            Route::post('reset-password', 'resetPasswordSubmit')->name('password-recovery');
            Route::post('resend-otp-reset-password', 'resendPhoneOTPRequest')->name('resend-otp-reset-password');
        });
    });

    Route::controller(SystemController::class)->group(function () {
        Route::get('set-payment-method/{name}', 'setPaymentMethod')->name('set-payment-method');
        Route::get('set-shipping-method', 'setShippingMethod')->name('set-shipping-method');
        Route::post('choose-shipping-address', 'getChooseShippingAddress')->name('choose-shipping-address');
        Route::post('choose-shipping-address-other', 'getChooseShippingAddressOther')->name('choose-shipping-address-other');
        Route::post('choose-billing-address', 'choose_billing_address')->name('choose-billing-address');
    });

    Route::group(['prefix' => 'reward-points', 'as' => 'reward-points.', 'middleware' => ['auth:customer']], function () {
        Route::get('convert', [RewardPointController::class, 'convert'])->name('convert');
    });
});

Route::group(['namespace' => 'Customer', 'prefix' => 'customer', 'as' => 'customer.'], function () {
    Route::controller(PaymentController::class)->group(function () {
        Route::post('/web-payment-request', 'payment')->name('web-payment-request');
        Route::post('/customer-add-fund-request', 'customer_add_to_fund_request')->name('add-fund-request');
    });
});

Route::controller(PaymentController::class)->group(function () {
    Route::get('web-payment', 'web_payment_success')->name('web-payment-success');
    Route::get('payment-success', 'success')->name('payment-success');
    Route::get('payment-fail', 'fail')->name('payment-fail');
});

$isGatewayPublished = 0;
try {
    if (file_exists(base_path('Modules/Gateways/Addon/info.php'))) {
        $gatewayInfoData = include(base_path('Modules/Gateways/Addon/info.php'));
        $isGatewayPublished = $gatewayInfoData['is_published'] == 1 ? 1 : 0;
    }
} catch (Exception $exception) {
}

if (!$isGatewayPublished) {
    Route::group(['prefix' => 'payment'], function () {

        //SSLCOMMERZ
        Route::group(['prefix' => 'sslcommerz', 'as' => 'sslcommerz.'], function () {
            Route::get('pay', [SslCommerzPaymentController::class, 'index'])->name('pay');
            Route::post('success', [SslCommerzPaymentController::class, 'success'])
                ->withoutMiddleware([VerifyCsrfToken::class]);
            Route::post('failed', [SslCommerzPaymentController::class, 'failed'])
                ->withoutMiddleware([VerifyCsrfToken::class]);
            Route::post('canceled', [SslCommerzPaymentController::class, 'canceled'])
                ->withoutMiddleware([VerifyCsrfToken::class]);
        });

        //STRIPE
        Route::group(['prefix' => 'stripe', 'as' => 'stripe.'], function () {
            Route::get('pay', [StripePaymentController::class, 'index'])->name('pay');
            Route::get('token', [StripePaymentController::class, 'payment_process_3d'])->name('token');
            Route::get('success', [StripePaymentController::class, 'success'])->name('success');
        });

        //RAZOR-PAY
        Route::group(['prefix' => 'razor-pay', 'as' => 'razor-pay.'], function () {
            Route::get('pay', [RazorPayController::class, 'index']);
            Route::post('payment', [RazorPayController::class, 'payment'])->name('payment')
                ->withoutMiddleware([VerifyCsrfToken::class]);
            Route::post('callback', [RazorPayController::class, 'callback'])->name('callback')
                ->withoutMiddleware([VerifyCsrfToken::class]);
            Route::any('cancel', [RazorPayController::class, 'cancel'])->name('cancel')
                ->withoutMiddleware([VerifyCsrfToken::class]);

            Route::any('create-order', [RazorPayController::class, 'createOrder'])->name('create-order')
                ->withoutMiddleware([VerifyCsrfToken::class]);
            Route::any('verify-payment', [RazorPayController::class, 'verifyPayment'])->name('verify-payment')
                ->withoutMiddleware([VerifyCsrfToken::class]);
        });

        //PAYPAL
        Route::group(['prefix' => 'paypal', 'as' => 'paypal.'], function () {
            Route::get('pay', [PaypalPaymentController::class, 'payment']);
            Route::any('success', [PaypalPaymentController::class, 'success'])->name('success')
                ->withoutMiddleware([VerifyCsrfToken::class]);
            Route::any('cancel', [PaypalPaymentController::class, 'cancel'])->name('cancel')
                ->withoutMiddleware([VerifyCsrfToken::class]);
        });

        //SENANG-PAY
        Route::group(['prefix' => 'senang-pay', 'as' => 'senang-pay.'], function () {
            Route::get('pay', [SenangPayController::class, 'index']);
            Route::any('callback', [SenangPayController::class, 'return_senang_pay']);
        });

        //PAYTM
        Route::group(['prefix' => 'paytm', 'as' => 'paytm.'], function () {
            Route::get('pay', [PaytmController::class, 'payment']);
            Route::any('response', [PaytmController::class, 'callback'])->name('response')
                ->withoutMiddleware([VerifyCsrfToken::class]);
        });

        //FLUTTERWAVE
        Route::group(['prefix' => 'flutterwave-v3', 'as' => 'flutterwave-v3.'], function () {
            Route::get('pay', [FlutterwaveV3Controller::class, 'initialize'])->name('pay');
            Route::get('callback', [FlutterwaveV3Controller::class, 'callback'])->name('callback');
        });

        //PAYSTACK
        Route::group(['prefix' => 'paystack', 'as' => 'paystack.'], function () {
            Route::get('pay', [PaystackController::class, 'index'])->name('pay');
            Route::get('callback', [PaystackController::class, 'handleGatewayCallback'])->name('callback');
            Route::get('cancel', [PaystackController::class, 'cancel'])->name('cancel');
        });

        //BKASH
        Route::group(['prefix' => 'bkash', 'as' => 'bkash.'], function () {
            // Payment Routes for bKash
            Route::get('make-payment', [BkashPaymentController::class, 'make_tokenize_payment'])->name('make-payment');
            Route::any('callback', [BkashPaymentController::class, 'callback'])->name('callback');
        });

        //Liqpay
        Route::group(['prefix' => 'liqpay', 'as' => 'liqpay.'], function () {
            Route::get('payment', [LiqPayController::class, 'payment'])->name('payment');
            Route::any('callback', [LiqPayController::class, 'callback'])->name('callback');
        });

        //MERCADOPAGO
        Route::group(['prefix' => 'mercadopago', 'as' => 'mercadopago.'], function () {
            Route::get('pay', [MercadoPagoController::class, 'index'])->name('index');
            Route::post('make-payment', [MercadoPagoController::class, 'make_payment'])->name('make_payment');
        });

        //PAYMOB
        Route::group(['prefix' => 'paymob', 'as' => 'paymob.'], function () {
            Route::any('pay', [PaymobController::class, 'credit'])->name('pay');
            Route::any('callback', [PaymobController::class, 'callback'])->name('callback');
        });

        //PAYTABS
        Route::group(['prefix' => 'paytabs', 'as' => 'paytabs.'], function () {
            Route::any('pay', [PaytabsController::class, 'payment'])->name('pay');
            Route::any('callback', [PaytabsController::class, 'callback'])->name('callback');
            Route::any('response', [PaytabsController::class, 'response'])->name('response');
        });
    });
}

// WhatsApp Webhook Routes (excluded from CSRF)
Route::prefix('api/whatsapp')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->group(function () {
    Route::get('webhook', [\App\Http\Controllers\Api\WhatsAppWebhookController::class, 'verify'])->name('whatsapp.webhook.verify');
    Route::post('webhook', [\App\Http\Controllers\Api\WhatsAppWebhookController::class, 'handle'])->name('whatsapp.webhook.handle');
    Route::post('send', [\App\Http\Controllers\Api\WhatsAppWebhookController::class, 'sendMessage'])->name('whatsapp.send');
});

// Include tools routes
require __DIR__ . '/tools.php';


// === SEO PAGES WITH PRODUCTS - March 8, 2026 ===

// CHiQ TV Prices
Route::get('/prices/chiq-tv-uganda', function () {
    $brand = \App\Models\Brand::where('name', 'like', '%chiq%')->first();
    $products = \App\Models\Product::where('brand_id', $brand->id ?? 0)
        ->where('name', 'like', '%TV%')
        ->where('status', 1)
        ->orderBy('unit_price', 'asc')
        ->take(12)
        ->get();
    return view('theme-views.pages.prices-chiq-tv-uganda', compact('products'));
})->name('prices.chiq-tv');

// Samsung Washing Machine
Route::get('/prices/samsung-washing-machine', function () {
    $brand = \App\Models\Brand::where('name', 'like', '%samsung%')->first();
    $products = \App\Models\Product::where('brand_id', $brand->id ?? 0)
        ->where(function($q) {
            $q->where('name', 'like', '%washing%')
              ->orWhere('name', 'like', '%washer%');
        })
        ->where('status', 1)
        ->orderBy('unit_price', 'asc')
        ->take(12)
        ->get();
    return view('theme-views.pages.prices-samsung-washing-machine', compact('products'));
})->name('prices.samsung-washer');

// TV Free Delivery Kampala
Route::get('/buy/tv-free-delivery-kampala', function () {
    $products = \App\Models\Product::where('name', 'like', '%TV%')
        ->where('status', 1)
        ->orderBy('unit_price', 'asc')
        ->take(12)
        ->get();
    return view('theme-views.pages.buy-tv-free-delivery-kampala', compact('products'));
})->name('buy.tv-free-delivery');

// Fridge Pay on Delivery
Route::get('/buy/fridge-pay-on-delivery', function () {
    $products = \App\Models\Product::where(function($q) {
            $q->where('name', 'like', '%fridge%')
              ->orWhere('name', 'like', '%refrigerator%')
              ->orWhere('name', 'like', '%freezer%');
        })
        ->where('status', 1)
        ->orderBy('unit_price', 'asc')
        ->take(12)
        ->get();
    return view('theme-views.pages.buy-fridge-pay-on-delivery', compact('products'));
})->name('buy.fridge-cod');

// Cheap TV Uganda
Route::get('/deals/cheap-tv-uganda', function () {
    $products = \App\Models\Product::where('name', 'like', '%TV%')
        ->where('status', 1)
        ->where('unit_price', '<', 1500000)
        ->orderBy('unit_price', 'asc')
        ->take(12)
        ->get();
    return view('theme-views.pages.deals-cheap-tv-uganda', compact('products'));
})->name('deals.cheap-tv');

// === MORE SEO PAGES - March 8, 2026 (Batch 2) ===

// Hisense TV Prices
Route::get('/prices/hisense-tv-uganda', function () {
    $brand = \App\Models\Brand::where('name', 'like', '%hisense%')->first();
    $products = \App\Models\Product::where('brand_id', $brand->id ?? 0)
        ->where('name', 'like', '%TV%')
        ->where('status', 1)
        ->orderBy('unit_price', 'asc')
        ->take(12)->get();
    return view('theme-views.pages.prices-hisense-tv-uganda', compact('products'));
})->name('prices.hisense-tv');

// Samsung TV Prices
Route::get('/prices/samsung-tv-uganda', function () {
    $brand = \App\Models\Brand::where('name', 'like', '%samsung%')->first();
    $products = \App\Models\Product::where('brand_id', $brand->id ?? 0)
        ->where('name', 'like', '%TV%')
        ->where('status', 1)
        ->orderBy('unit_price', 'asc')
        ->take(12)->get();
    return view('theme-views.pages.prices-samsung-tv-uganda', compact('products'));
})->name('prices.samsung-tv');

// Soundbar
Route::get('/buy/soundbar-uganda', function () {
    $products = \App\Models\Product::where(function($q) {
            $q->where('name', 'like', '%soundbar%')
              ->orWhere('name', 'like', '%sound bar%');
        })
        ->where('status', 1)
        ->orderBy('unit_price', 'asc')
        ->take(12)->get();
    return view('theme-views.pages.buy-soundbar-uganda', compact('products'));
})->name('buy.soundbar');

// Home Theatre
Route::get('/buy/home-theatre-uganda', function () {
    $products = \App\Models\Product::where(function($q) {
            $q->where('name', 'like', '%home theatre%')
              ->orWhere('name', 'like', '%home theater%')
              ->orWhere('name', 'like', '%speaker%');
        })
        ->where('status', 1)
        ->orderBy('unit_price', 'asc')
        ->take(12)->get();
    return view('theme-views.pages.buy-home-theatre-uganda', compact('products'));
})->name('buy.home-theatre');

// Gas Cooker
Route::get('/buy/gas-cooker-uganda', function () {
    $products = \App\Models\Product::where(function($q) {
            $q->where('name', 'like', '%gas cooker%')
              ->orWhere('name', 'like', '%stove%')
              ->orWhere('name', 'like', '%burner%');
        })
        ->where('status', 1)
        ->orderBy('unit_price', 'asc')
        ->take(12)->get();
    return view('theme-views.pages.buy-gas-cooker-uganda', compact('products'));
})->name('buy.gas-cooker');

// Microwave
Route::get('/buy/microwave-uganda', function () {
    $products = \App\Models\Product::where('name', 'like', '%microwave%')
        ->where('status', 1)
        ->orderBy('unit_price', 'asc')
        ->take(12)->get();
    return view('theme-views.pages.buy-microwave-uganda', compact('products'));
})->name('buy.microwave');
require __DIR__.'/tv-seo.php';
require __DIR__.'/fridge-seo.php';
require __DIR__.'/all-seo.php';
require __DIR__.'/seo-boost.php';
require __DIR__.'/faq-routes.php';
require __DIR__.'/tcl-seo.php';

// =====================================================
// YOOLA SEO LANDING PAGES - CATEGORY HUBS (March 2026)
// =====================================================

// Main TV Category Hub
Route::get('/buy/tvs-uganda', function () {
    $categoryIds = \App\Models\Category::where('name', 'like', '%TV%')
        ->orWhere('name', 'like', '%Television%')
        ->pluck('id');
    
    $products = \App\Models\Product::whereHas('category', function($q) use ($categoryIds) {
            $q->whereIn('id', $categoryIds);
        })
        ->orWhere('name', 'like', '%TV%')
        ->orWhere('name', 'like', '%Television%')
        ->where('status', 1)
        ->with('brand')
        ->orderBy('unit_price')
        ->get();
    
    // Calculate price ranges by TV size
    $sizeRanges = [
        '24-32' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => '24-32"', 'use' => 'Bedroom'],
        '40-43' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => '40-43"', 'use' => 'Small Room'],
        '50-55' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => '50-55"', 'use' => 'Living Room'],
        '65-75' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => '65-75"', 'use' => 'Large Room'],
        '85+' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => '85"+', 'use' => 'Theatre'],
    ];
    
    foreach ($products as $product) {
        if (preg_match('/(\d{2,3})\s*["\'\-]?\s*(inch|in)?/i', $product->name, $matches)) {
            $size = (int)$matches[1];
            $price = $product->unit_price;
            
            if ($size >= 24 && $size <= 32) $key = '24-32';
            elseif ($size >= 40 && $size <= 43) $key = '40-43';
            elseif ($size >= 50 && $size <= 55) $key = '50-55';
            elseif ($size >= 65 && $size <= 75) $key = '65-75';
            elseif ($size >= 85) $key = '85+';
            else continue;
            
            $sizeRanges[$key]['min'] = min($sizeRanges[$key]['min'], $price);
            $sizeRanges[$key]['max'] = max($sizeRanges[$key]['max'], $price);
        }
    }
    
    $tvSizes = [];
    foreach ($sizeRanges as $key => $range) {
        if ($range['min'] < PHP_INT_MAX) {
            $tvSizes[] = [
                'size' => $range['label'],
                'use' => $range['use'],
                'min' => $range['min'],
                'max' => $range['max'],
            ];
        }
    }
    
    return view('theme-views.pages.buy-tvs-uganda', compact('products', 'tvSizes'));
})->name('buy.tvs-uganda');


// Fridges Landing Page
Route::get('/buy/fridges-uganda', function () {
    $products = \App\Models\Product::where(function($q) {
            $q->where('name', 'like', '%Fridge%')
              ->orWhere('name', 'like', '%Refrigerator%')
              ->orWhere('name', 'like', '%Freezer%');
        })
        ->where('status', 1)
        ->with('brand')
        ->orderBy('unit_price')
        ->get();
    
    // Calculate price ranges by capacity (liters)
    $sizeRanges = [
        'mini' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => '50-120L', 'use' => 'Mini/Bar Fridge'],
        'single' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => '150-250L', 'use' => 'Single Door'],
        'double' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => '250-400L', 'use' => 'Double Door'],
        'large' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => '400L+', 'use' => 'Side-by-Side/French Door'],
        'chest' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => 'Chest', 'use' => 'Deep Freezer'],
    ];
    
    foreach ($products as $product) {
        $price = $product->unit_price;
        $name = strtolower($product->name);
        
        // Check for chest freezer first
        if (strpos($name, 'chest') !== false || strpos($name, 'deep freeze') !== false) {
            $sizeRanges['chest']['min'] = min($sizeRanges['chest']['min'], $price);
            $sizeRanges['chest']['max'] = max($sizeRanges['chest']['max'], $price);
            continue;
        }
        
        // Extract liters
        if (preg_match('/(\d{2,3})\s*(l|ltr|litre|liter)/i', $product->name, $matches)) {
            $liters = (int)$matches[1];
            
            if ($liters >= 50 && $liters < 150) $key = 'mini';
            elseif ($liters >= 150 && $liters < 250) $key = 'single';
            elseif ($liters >= 250 && $liters < 400) $key = 'double';
            elseif ($liters >= 400) $key = 'large';
            else continue;

            $sizeRanges[$key]['min'] = min($sizeRanges[$key]['min'], $price);
            $sizeRanges[$key]['max'] = max($sizeRanges[$key]['max'], $price);
        }
    }

    $fridgeSizes = [];
    foreach ($sizeRanges as $key => $range) {
        if ($range['min'] < PHP_INT_MAX) {
            $fridgeSizes[] = [
                'size' => $range['label'],
                'use' => $range['use'],
                'min' => $range['min'],
                'max' => $range['max'],
            ];
        }
    }
    
    return view('theme-views.pages.buy-fridges-uganda', compact('products', 'fridgeSizes'));
})->name('buy.fridges-uganda');

// Washing Machines Landing Page
Route::get('/buy/washing-machines-uganda', function () {
    $products = \App\Models\Product::where(function($q) {
            $q->where('name', 'like', '%Washing Machine%')
              ->orWhere('name', 'like', '%Washer%')
              ->orWhere('name', 'like', '%Laundry%');
        })
        ->where('status', 1)
        ->with('brand')
        ->orderBy('unit_price')
        ->get();
    
    // Calculate price ranges by capacity (kg)
    $sizeRanges = [
        'compact' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => '5-7 kg', 'use' => 'Singles/Couples'],
        'standard' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => '8-10 kg', 'use' => 'Small Family'],
        'large' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => '12+ kg', 'use' => 'Large Family'],
        'twin' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => 'Twin Tub', 'use' => 'Budget Option'],
    ];
    
    foreach ($products as $product) {
        $price = $product->unit_price;
        $name = strtolower($product->name);
        
        // Check for twin tub first
        if (strpos($name, 'twin') !== false) {
            $sizeRanges['twin']['min'] = min($sizeRanges['twin']['min'], $price);
            $sizeRanges['twin']['max'] = max($sizeRanges['twin']['max'], $price);
            continue;
        }
        
        // Extract kg capacity
        if (preg_match('/(\d+(?:\.\d+)?)\s*kg/i', $product->name, $matches)) {
            $kg = (float)$matches[1];
            
            if ($kg >= 5 && $kg <= 7) $key = 'compact';
            elseif ($kg >= 8 && $kg <= 10) $key = 'standard';
            elseif ($kg >= 12) $key = 'large';
            else continue;

            $sizeRanges[$key]['min'] = min($sizeRanges[$key]['min'], $price);
            $sizeRanges[$key]['max'] = max($sizeRanges[$key]['max'], $price);
        }
    }

    $washerSizes = [];
    foreach ($sizeRanges as $key => $range) {
        if ($range['min'] < PHP_INT_MAX) {
            $washerSizes[] = [
                'size' => $range['label'],
                'use' => $range['use'],
                'min' => $range['min'],
                'max' => $range['max'],
            ];
        }
    }
    
    return view('theme-views.pages.buy-washing-machines-uganda', compact('products', 'washerSizes'));
})->name('buy.washing-machines-uganda');

// Air Conditioners Landing Page
Route::get('/buy/air-conditioners-uganda', function () {
    $products = \App\Models\Product::where(function($q) {
            $q->where('name', 'like', '%Air Conditioner%')
              ->orWhere('name', 'like', '%AC %')
              ->orWhere('name', 'like', '% AC')
              ->orWhere('name', 'like', '%Split%')
              ->orWhere('name', 'like', '%BTU%');
        })
        ->where('status', 1)
        ->with('brand')
        ->orderBy('unit_price')
        ->get();
    
    $sizeRanges = [
        'small' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => '9,000-12,000 BTU', 'use' => 'Bedroom (15-25 sqm)'],
        'medium' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => '18,000-24,000 BTU', 'use' => 'Living Room (30-50 sqm)'],
        'large' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => '30,000+ BTU', 'use' => 'Large Room/Office'],
    ];
    
    foreach ($products as $product) {
        $price = $product->unit_price;
        if (preg_match('/(\d{1,2}),?(\d{3})\s*BTU/i', $product->name, $matches)) {
            $btu = (int)($matches[1] . $matches[2]);
            if ($btu >= 9000 && $btu <= 15000) $key = 'small';
            elseif ($btu >= 16000 && $btu <= 26000) $key = 'medium';
            elseif ($btu >= 27000) $key = 'large';
            else continue;
            $sizeRanges[$key]['min'] = min($sizeRanges[$key]['min'], $price);
            $sizeRanges[$key]['max'] = max($sizeRanges[$key]['max'], $price);
        }
    }

    $acSizes = [];
    foreach ($sizeRanges as $range) {
        if ($range['min'] < PHP_INT_MAX) {
            $acSizes[] = ['size' => $range['label'], 'use' => $range['use'], 'min' => $range['min'], 'max' => $range['max']];
        }
    }
    
    return view('theme-views.pages.buy-air-conditioners-uganda', compact('products', 'acSizes'));
})->name('buy.air-conditioners-uganda');

// Speakers Landing Page  
Route::get('/buy/speakers-uganda', function () {
    $products = \App\Models\Product::where(function($q) {
            $q->where('name', 'like', '%Speaker%')
              ->orWhere('name', 'like', '%Soundbar%')
              ->orWhere('name', 'like', '%Subwoofer%')
              ->orWhere('name', 'like', '%Home Theater%')
              ->orWhere('name', 'like', '%Woofer%');
        })
        ->where('status', 1)
        ->with('brand')
        ->orderBy('unit_price')
        ->get();
    
    $types = [
        'portable' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => 'Portable/Bluetooth', 'use' => 'On the go'],
        'soundbar' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => 'Soundbar', 'use' => 'TV audio upgrade'],
        'subwoofer' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => 'Subwoofer', 'use' => 'Party/Bass'],
        'theater' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => 'Home Theater', 'use' => 'Full room sound'],
    ];
    
    foreach ($products as $product) {
        $price = $product->unit_price;
        $name = strtolower($product->name);
        
        if (strpos($name, 'home theater') !== false || strpos($name, 'home theatre') !== false) $key = 'theater';
        elseif (strpos($name, 'soundbar') !== false || strpos($name, 'sound bar') !== false) $key = 'soundbar';
        elseif (strpos($name, 'subwoofer') !== false || strpos($name, 'woofer') !== false) $key = 'subwoofer';
        else $key = 'portable';
        
        $types[$key]['min'] = min($types[$key]['min'], $price);
        $types[$key]['max'] = max($types[$key]['max'], $price);
    }

    $speakerTypes = [];
    foreach ($types as $type) {
        if ($type['min'] < PHP_INT_MAX) {
            $speakerTypes[] = ['size' => $type['label'], 'use' => $type['use'], 'min' => $type['min'], 'max' => $type['max']];
        }
    }
    
    return view('theme-views.pages.buy-speakers-uganda', compact('products', 'speakerTypes'));
})->name('buy.speakers-uganda');

// Cookers Landing Page
Route::get('/buy/cookers-uganda', function () {
    $products = \App\Models\Product::where(function($q) {
            $q->where('name', 'like', '%Cooker%')
              ->orWhere('name', 'like', '%Oven%')
              ->orWhere('name', 'like', '%Stove%')
              ->orWhere('name', 'like', '%Hob%')
              ->orWhere('name', 'like', '%Range%');
        })
        ->where('status', 1)
        ->with('brand')
        ->orderBy('unit_price')
        ->get();
    
    $types = [
        'gas' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => 'Gas Cooker', 'use' => 'Traditional cooking'],
        'electric' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => 'Electric Cooker', 'use' => 'Modern/Clean'],
        'combined' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => 'Gas + Electric', 'use' => 'Versatile'],
        'builtin' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => 'Built-in Hob/Oven', 'use' => 'Modern kitchen'],
    ];
    
    foreach ($products as $product) {
        $price = $product->unit_price;
        $name = strtolower($product->name);
        
        if (strpos($name, 'built') !== false || strpos($name, 'hob') !== false) $key = 'builtin';
        elseif (strpos($name, 'gas') !== false && strpos($name, 'electric') !== false) $key = 'combined';
        elseif (strpos($name, 'electric') !== false || strpos($name, 'induction') !== false) $key = 'electric';
        else $key = 'gas';
        
        $types[$key]['min'] = min($types[$key]['min'], $price);
        $types[$key]['max'] = max($types[$key]['max'], $price);
    }

    $cookerTypes = [];
    foreach ($types as $type) {
        if ($type['min'] < PHP_INT_MAX) {
            $cookerTypes[] = ['size' => $type['label'], 'use' => $type['use'], 'min' => $type['min'], 'max' => $type['max']];
        }
    }
    
    return view('theme-views.pages.buy-cookers-uganda', compact('products', 'cookerTypes'));
})->name('buy.cookers-uganda');

// Microwaves Landing Page
Route::get('/buy/microwaves-uganda', function () {
    $products = \App\Models\Product::where(function($q) {
            $q->where('name', 'like', '%Microwave%');
        })
        ->where('status', 1)
        ->with('brand')
        ->orderBy('unit_price')
        ->get();
    
    $types = [
        'solo' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => 'Solo Microwave', 'use' => 'Reheating/Basic'],
        'grill' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => 'Grill Microwave', 'use' => 'Reheating + Grilling'],
        'convection' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => 'Convection', 'use' => 'Full cooking/Baking'],
    ];
    
    foreach ($products as $product) {
        $price = $product->unit_price;
        $name = strtolower($product->name);
        
        if (strpos($name, 'convection') !== false) $key = 'convection';
        elseif (strpos($name, 'grill') !== false) $key = 'grill';
        else $key = 'solo';
        
        $types[$key]['min'] = min($types[$key]['min'], $price);
        $types[$key]['max'] = max($types[$key]['max'], $price);
    }

    $microwaveTypes = [];
    foreach ($types as $type) {
        if ($type['min'] < PHP_INT_MAX) {
            $microwaveTypes[] = ['size' => $type['label'], 'use' => $type['use'], 'min' => $type['min'], 'max' => $type['max']];
        }
    }
    
    return view('theme-views.pages.buy-microwaves-uganda', compact('products', 'microwaveTypes'));
})->name('buy.microwaves-uganda');

// Kampala Location Page
Route::get('/buy/electronics-kampala', function () {
    $categories = [
        ['name' => 'TVs', 'url' => '/buy/tvs-uganda', 'icon' => '📺', 'count' => \App\Models\Product::where('name', 'like', '%TV%')->where('status', 1)->count()],
        ['name' => 'Fridges', 'url' => '/buy/fridges-uganda', 'icon' => '❄️', 'count' => \App\Models\Product::where('name', 'like', '%Fridge%')->orWhere('name', 'like', '%Refrigerator%')->where('status', 1)->count()],
        ['name' => 'Washing Machines', 'url' => '/buy/washing-machines-uganda', 'icon' => '🧺', 'count' => \App\Models\Product::where('name', 'like', '%Washing%')->where('status', 1)->count()],
        ['name' => 'Air Conditioners', 'url' => '/buy/air-conditioners-uganda', 'icon' => '❄️', 'count' => \App\Models\Product::where('name', 'like', '%Air Condition%')->orWhere('name', 'like', '%BTU%')->where('status', 1)->count()],
        ['name' => 'Speakers', 'url' => '/buy/speakers-uganda', 'icon' => '🔊', 'count' => \App\Models\Product::where('name', 'like', '%Speaker%')->orWhere('name', 'like', '%Soundbar%')->where('status', 1)->count()],
        ['name' => 'Cookers', 'url' => '/buy/cookers-uganda', 'icon' => '🍳', 'count' => \App\Models\Product::where('name', 'like', '%Cooker%')->orWhere('name', 'like', '%Oven%')->where('status', 1)->count()],
        ['name' => 'Microwaves', 'url' => '/buy/microwaves-uganda', 'icon' => '⚡', 'count' => \App\Models\Product::where('name', 'like', '%Microwave%')->where('status', 1)->count()],
    ];
    return view('theme-views.pages.buy-electronics-kampala', compact('categories'));
})->name('buy.electronics-kampala');

// Blenders Landing Page
Route::get('/buy/blenders-uganda', function () {
    $products = \App\Models\Product::where(function($q) {
            $q->where('name', 'like', '%Blender%')
              ->orWhere('name', 'like', '%Juicer%')
              ->orWhere('name', 'like', '%Food Processor%')
              ->orWhere('name', 'like', '%Mixer%');
        })
        ->where('status', 1)
        ->with('brand')
        ->orderBy('unit_price')
        ->get();
    
    $types = [
        'basic' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => 'Basic Blender', 'use' => 'Smoothies/Juices'],
        'commercial' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => 'Commercial', 'use' => 'Heavy duty'],
        'processor' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => 'Food Processor', 'use' => 'Chopping/Mixing'],
        'juicer' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => 'Juicer', 'use' => 'Fresh juice'],
    ];
    
    foreach ($products as $product) {
        $price = $product->unit_price;
        $name = strtolower($product->name);
        if (strpos($name, 'processor') !== false) $key = 'processor';
        elseif (strpos($name, 'juicer') !== false) $key = 'juicer';
        elseif (strpos($name, 'commercial') !== false || $price > 500000) $key = 'commercial';
        else $key = 'basic';
        $types[$key]['min'] = min($types[$key]['min'], $price);
        $types[$key]['max'] = max($types[$key]['max'], $price);
    }
    $blenderTypes = [];
    foreach ($types as $t) { if ($t['min'] < PHP_INT_MAX) $blenderTypes[] = ['size'=>$t['label'],'use'=>$t['use'],'min'=>$t['min'],'max'=>$t['max']]; }
    return view('theme-views.pages.buy-blenders-uganda', compact('products', 'blenderTypes'));
})->name('buy.blenders-uganda');

// Vacuum Cleaners Landing Page
Route::get('/buy/vacuum-cleaners-uganda', function () {
    $products = \App\Models\Product::where(function($q) {
            $q->where('name', 'like', '%Vacuum%')
              ->orWhere('name', 'like', '%Cleaner%');
        })
        ->where('status', 1)
        ->with('brand')
        ->orderBy('unit_price')
        ->get();
    
    $types = [
        'handheld' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => 'Handheld', 'use' => 'Quick cleanups'],
        'upright' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => 'Upright/Canister', 'use' => 'Full home'],
        'wet' => ['min' => PHP_INT_MAX, 'max' => 0, 'label' => 'Wet & Dry', 'use' => 'Versatile'],
    ];
    foreach ($products as $product) {
        $price = $product->unit_price;
        $name = strtolower($product->name);
        if (strpos($name, 'wet') !== false || strpos($name, 'dry') !== false) $key = 'wet';
        elseif (strpos($name, 'hand') !== false || strpos($name, 'portable') !== false) $key = 'handheld';
        else $key = 'upright';
        $types[$key]['min'] = min($types[$key]['min'], $price);
        $types[$key]['max'] = max($types[$key]['max'], $price);
    }
    $vacuumTypes = [];
    foreach ($types as $t) { if ($t['min'] < PHP_INT_MAX) $vacuumTypes[] = ['size'=>$t['label'],'use'=>$t['use'],'min'=>$t['min'],'max'=>$t['max']]; }
    return view('theme-views.pages.buy-vacuum-cleaners-uganda', compact('products', 'vacuumTypes'));
})->name('buy.vacuum-cleaners-uganda');

// Wakiso Location Page
Route::get('/buy/electronics-wakiso', function () {
    $categories = [
        ['name' => 'TVs', 'url' => '/buy/tvs-uganda', 'icon' => '📺'],
        ['name' => 'Fridges', 'url' => '/buy/fridges-uganda', 'icon' => '❄️'],
        ['name' => 'Washing Machines', 'url' => '/buy/washing-machines-uganda', 'icon' => '🧺'],
        ['name' => 'Air Conditioners', 'url' => '/buy/air-conditioners-uganda', 'icon' => '❄️'],
        ['name' => 'Speakers', 'url' => '/buy/speakers-uganda', 'icon' => '🔊'],
        ['name' => 'Cookers', 'url' => '/buy/cookers-uganda', 'icon' => '🍳'],
    ];
    return view('theme-views.pages.buy-electronics-wakiso', compact('categories'));
})->name('buy.electronics-wakiso');

// Brand TV Pages
Route::get('/buy/hisense-tvs-uganda', function () {
    $products = \App\Models\Product::where('name', 'like', '%Hisense%')->where(function($q) { $q->where('name', 'like', '%TV%')->orWhere('name', 'like', '%Television%'); })->where('status', 1)->orderBy('unit_price')->get();
    $brand = 'Hisense';
    $brandInfo = ['tagline' => 'Quality & Value', 'warranty' => '2 years', 'origin' => 'China (Global brand)'];
    return view('theme-views.pages.buy-brand-tvs', compact('products', 'brand', 'brandInfo'));
})->name('buy.hisense-tvs');

Route::get('/buy/samsung-tvs-uganda', function () {
    $products = \App\Models\Product::where('name', 'like', '%Samsung%')->where(function($q) { $q->where('name', 'like', '%TV%')->orWhere('name', 'like', '%Television%'); })->where('status', 1)->orderBy('unit_price')->get();
    $brand = 'Samsung';
    $brandInfo = ['tagline' => 'Premium Innovation', 'warranty' => '2 years', 'origin' => 'South Korea'];
    return view('theme-views.pages.buy-brand-tvs', compact('products', 'brand', 'brandInfo'));
})->name('buy.samsung-tvs');

Route::get('/buy/tcl-tvs-uganda', function () {
    $products = \App\Models\Product::where('name', 'like', '%TCL%')->where(function($q) { $q->where('name', 'like', '%TV%')->orWhere('name', 'like', '%Television%'); })->where('status', 1)->orderBy('unit_price')->get();
    $brand = 'TCL';
    $brandInfo = ['tagline' => 'Smart TV Leader', 'warranty' => '1 year', 'origin' => 'China'];
    return view('theme-views.pages.buy-brand-tvs', compact('products', 'brand', 'brandInfo'));
})->name('buy.tcl-tvs');

Route::get('/buy/chiq-tvs-uganda', function () {
    $products = \App\Models\Product::where('name', 'like', '%CHiQ%')->where(function($q) { $q->where('name', 'like', '%TV%')->orWhere('name', 'like', '%Television%'); })->where('status', 1)->orderBy('unit_price')->get();
    $brand = 'CHiQ';
    $brandInfo = ['tagline' => 'Budget Smart TVs', 'warranty' => '1 year', 'origin' => 'China'];
    return view('theme-views.pages.buy-brand-tvs', compact('products', 'brand', 'brandInfo'));
})->name('buy.chiq-tvs');

// Trust Pages
Route::get('/genuine-guarantee', function () {
    return view('theme-views.pages.genuine-guarantee');
})->name('genuine-guarantee');

Route::get('/cash-on-delivery', function () {
    return view('theme-views.pages.cash-on-delivery');
})->name('cash-on-delivery');

Route::get('/warranty-support', function () {
    return view('theme-views.pages.warranty-support');
})->name('warranty-support');

// Deep Freezers
Route::get('/buy/deep-freezers-uganda', function () {
    $products = \App\Models\Product::where(function($q) { $q->where('name', 'like', '%Freezer%')->orWhere('name', 'like', '%Chest%'); })->where('status', 1)->orderBy('unit_price')->get();
    return view('theme-views.pages.buy-deep-freezers-uganda', compact('products'));
})->name('buy.deep-freezers');

// Soundbars
Route::get('/buy/soundbars-uganda', function () {
    $products = \App\Models\Product::where(function($q) { $q->where('name', 'like', '%Soundbar%')->orWhere('name', 'like', '%Sound Bar%'); })->where('status', 1)->orderBy('unit_price')->get();
    return view('theme-views.pages.buy-soundbars-uganda', compact('products'));
})->name('buy.soundbars');

// Water Dispensers
Route::get('/buy/water-dispensers-uganda', function () {
    $products = \App\Models\Product::where(function($q) { $q->where('name', 'like', '%Dispenser%'); })->where('status', 1)->orderBy('unit_price')->get();
    return view('theme-views.pages.buy-water-dispensers-uganda', compact('products'));
})->name('buy.water-dispensers');

// Budget Filter Pages
Route::get('/buy/tvs-under-500k', function () {
    $products = \App\Models\Product::where(function($q) { $q->where('name', 'like', '%TV%')->orWhere('name', 'like', '%Television%'); })->where('status', 1)->where('unit_price', '<=', 500000)->orderBy('unit_price')->get();
    $budget = '500,000'; $category = 'TVs';
    return view('theme-views.pages.buy-budget', compact('products', 'budget', 'category'));
})->name('buy.tvs-under-500k');

Route::get('/buy/tvs-under-1m', function () {
    $products = \App\Models\Product::where(function($q) { $q->where('name', 'like', '%TV%')->orWhere('name', 'like', '%Television%'); })->where('status', 1)->where('unit_price', '<=', 1000000)->orderBy('unit_price')->get();
    $budget = '1,000,000'; $category = 'TVs';
    return view('theme-views.pages.buy-budget', compact('products', 'budget', 'category'));
})->name('buy.tvs-under-1m');

Route::get('/buy/fridges-under-1m', function () {
    $products = \App\Models\Product::where(function($q) { $q->where('name', 'like', '%Fridge%')->orWhere('name', 'like', '%Refrigerator%'); })->where('status', 1)->where('unit_price', '<=', 1000000)->orderBy('unit_price')->get();
    $budget = '1,000,000'; $category = 'Fridges';
    return view('theme-views.pages.buy-budget', compact('products', 'budget', 'category'));
})->name('buy.fridges-under-1m');

// Brand Fridge Pages
Route::get('/buy/hisense-fridges-uganda', function () {
    $products = \App\Models\Product::where('name', 'like', '%Hisense%')->where(function($q) { $q->where('name', 'like', '%Fridge%')->orWhere('name', 'like', '%Refrigerator%')->orWhere('name', 'like', '%Freezer%'); })->where('status', 1)->orderBy('unit_price')->get();
    $brand = 'Hisense'; $brandInfo = ['tagline' => 'Energy Efficient', 'warranty' => '2 years', 'origin' => 'China (Global)'];
    return view('theme-views.pages.buy-brand-fridges', compact('products', 'brand', 'brandInfo'));
})->name('buy.hisense-fridges');

Route::get('/buy/samsung-fridges-uganda', function () {
    $products = \App\Models\Product::where('name', 'like', '%Samsung%')->where(function($q) { $q->where('name', 'like', '%Fridge%')->orWhere('name', 'like', '%Refrigerator%'); })->where('status', 1)->orderBy('unit_price')->get();
    $brand = 'Samsung'; $brandInfo = ['tagline' => 'Premium Innovation', 'warranty' => '2 years', 'origin' => 'South Korea'];
    return view('theme-views.pages.buy-brand-fridges', compact('products', 'brand', 'brandInfo'));
})->name('buy.samsung-fridges');

Route::get('/buy/lg-fridges-uganda', function () {
    $products = \App\Models\Product::where('name', 'like', '%LG%')->where(function($q) { $q->where('name', 'like', '%Fridge%')->orWhere('name', 'like', '%Refrigerator%'); })->where('status', 1)->orderBy('unit_price')->get();
    $brand = 'LG'; $brandInfo = ['tagline' => 'Life is Good', 'warranty' => '2 years', 'origin' => 'South Korea'];
    return view('theme-views.pages.buy-brand-fridges', compact('products', 'brand', 'brandInfo'));
})->name('buy.lg-fridges');

// Neighborhood Delivery Pages
Route::get('/delivery/ntinda', function () {
    return view('theme-views.pages.delivery-neighborhood', ['area' => 'Ntinda', 'time' => 'Same-day', 'fee' => 'FREE']);
})->name('delivery.ntinda');

Route::get('/delivery/kololo', function () {
    return view('theme-views.pages.delivery-neighborhood', ['area' => 'Kololo', 'time' => 'Same-day', 'fee' => 'FREE']);
})->name('delivery.kololo');

Route::get('/delivery/muyenga', function () {
    return view('theme-views.pages.delivery-neighborhood', ['area' => 'Muyenga', 'time' => 'Same-day', 'fee' => 'FREE']);
})->name('delivery.muyenga');

Route::get('/delivery/bugolobi', function () {
    return view('theme-views.pages.delivery-neighborhood', ['area' => 'Bugolobi', 'time' => 'Same-day', 'fee' => 'FREE']);
})->name('delivery.bugolobi');

Route::get('/delivery/kira', function () {
    return view('theme-views.pages.delivery-neighborhood', ['area' => 'Kira', 'time' => 'Same-day', 'fee' => 'FREE']);
})->name('delivery.kira');

Route::get('/delivery/naalya', function () {
    return view('theme-views.pages.delivery-neighborhood', ['area' => 'Naalya', 'time' => 'Same-day', 'fee' => 'FREE']);
})->name('delivery.naalya');
