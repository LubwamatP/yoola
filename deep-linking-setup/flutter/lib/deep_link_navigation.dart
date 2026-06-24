import 'package:flutter/material.dart';
import 'deep_link_handler.dart';

/// =============================================================================
/// DEEP LINK NAVIGATION INTEGRATION
/// =============================================================================
///
/// This file shows how to integrate deep linking with your app's navigation.
/// Choose the approach that matches your navigation setup.
///
/// =============================================================================

/// Option 1: Using go_router (Recommended)
/// =============================================================================
///
/// Add to pubspec.yaml:
/// ```yaml
/// dependencies:
///   go_router: ^13.0.0
/// ```
///
/// ```dart
/// import 'package:go_router/go_router.dart';
///
/// final GoRouter router = GoRouter(
///   initialLocation: '/',
///   routes: [
///     GoRoute(
///       path: '/',
///       builder: (context, state) => HomeScreen(),
///     ),
///     GoRoute(
///       path: '/product/:id',
///       builder: (context, state) {
///         final productId = state.pathParameters['id'];
///         return ProductDetailScreen(productId: productId);
///       },
///     ),
///     GoRoute(
///       path: '/category/:slug',
///       builder: (context, state) {
///         final slug = state.pathParameters['slug'];
///         return CategoryScreen(slug: slug);
///       },
///     ),
///     GoRoute(
///       path: '/search',
///       builder: (context, state) {
///         final query = state.uri.queryParameters['q'];
///         return SearchScreen(query: query);
///       },
///     ),
///   ],
/// );
///
/// // In your app, use the router:
/// MaterialApp.router(
///   routerConfig: router,
/// )
/// ```

/// Option 2: Navigator 2.0 with DeepLinkHandler
/// =============================================================================

class DeepLinkNavigator extends StatefulWidget {
  final Widget child;
  final GlobalKey<NavigatorState> navigatorKey;

  const DeepLinkNavigator({
    Key? key,
    required this.child,
    required this.navigatorKey,
  }) : super(key: key);

  @override
  State<DeepLinkNavigator> createState() => _DeepLinkNavigatorState();
}

class _DeepLinkNavigatorState extends State<DeepLinkNavigator> {
  @override
  void initState() {
    super.initState();
    _initDeepLinks();
  }

  Future<void> _initDeepLinks() async {
    await DeepLinkHandler().init();

    DeepLinkHandler().deepLinkStream.listen((data) {
      _navigateToDeepLink(data);
    });
  }

  void _navigateToDeepLink(DeepLinkData data) {
    final navigator = widget.navigatorKey.currentState;
    if (navigator == null) return;

    switch (data.type) {
      case DeepLinkType.product:
        navigator.pushNamed(
          '/product-details',
          arguments: {'productId': data.id, 'slug': data.slug},
        );
        break;

      case DeepLinkType.category:
        navigator.pushNamed(
          '/category',
          arguments: {'slug': data.slug},
        );
        break;

      case DeepLinkType.brand:
        navigator.pushNamed(
          '/brand',
          arguments: {'slug': data.slug},
        );
        break;

      case DeepLinkType.shop:
        navigator.pushNamed(
          '/shop',
          arguments: {'slug': data.slug},
        );
        break;

      case DeepLinkType.search:
        navigator.pushNamed(
          '/search',
          arguments: {'query': data.query},
        );
        break;

      case DeepLinkType.flashDeal:
        navigator.pushNamed(
          '/flash-deal',
          arguments: {'id': data.id},
        );
        break;

      case DeepLinkType.orderTracking:
        navigator.pushNamed(
          '/order-tracking',
          arguments: {'orderId': data.id},
        );
        break;

      case DeepLinkType.home:
      default:
        navigator.pushNamedAndRemoveUntil('/', (route) => false);
        break;
    }
  }

  @override
  Widget build(BuildContext context) {
    return widget.child;
  }
}


/// =============================================================================
/// MAIN.DART EXAMPLE
/// =============================================================================
///
/// ```dart
/// import 'package:flutter/material.dart';
/// import 'deep_link_handler.dart';
/// import 'deep_link_navigation.dart';
///
/// final GlobalKey<NavigatorState> navigatorKey = GlobalKey<NavigatorState>();
///
/// void main() async {
///   WidgetsFlutterBinding.ensureInitialized();
///   runApp(MyApp());
/// }
///
/// class MyApp extends StatelessWidget {
///   @override
///   Widget build(BuildContext context) {
///     return DeepLinkNavigator(
///       navigatorKey: navigatorKey,
///       child: MaterialApp(
///         navigatorKey: navigatorKey,
///         title: 'Yoola',
///         initialRoute: '/',
///         routes: {
///           '/': (context) => HomeScreen(),
///           '/product-details': (context) => ProductDetailScreen(),
///           '/category': (context) => CategoryScreen(),
///           '/brand': (context) => BrandScreen(),
///           '/shop': (context) => ShopScreen(),
///           '/search': (context) => SearchScreen(),
///           '/flash-deal': (context) => FlashDealScreen(),
///           '/order-tracking': (context) => OrderTrackingScreen(),
///         },
///       ),
///     );
///   }
/// }
/// ```
/// =============================================================================


/// =============================================================================
/// API SERVICE FOR FETCHING DATA BY DEEP LINK
/// =============================================================================

class DeepLinkApiService {
  final String baseUrl;

  DeepLinkApiService({this.baseUrl = 'https://yoola.ug/api/v1'});

  /// Get product by slug or ID
  /// The deep link URL format is: /product/{slug}-{id}
  /// Example: /product/samsung-260l-stabiliser-coolpack-rt26har2dsa-IwYJWR
  Future<Map<String, dynamic>?> getProductBySlug(String slug) async {
    // Extract the ID from the slug (last segment after hyphen)
    final id = _extractIdFromSlug(slug);

    // Option 1: Fetch by ID (if your API supports it)
    // return await _fetchProduct('/products/details/$id');

    // Option 2: Fetch by slug (if your API supports it)
    // return await _fetchProduct('/products/by-slug/$slug');

    // For now, return a placeholder
    return {
      'id': id,
      'slug': slug,
      'name': 'Product from deep link',
    };
  }

  String _extractIdFromSlug(String slug) {
    final parts = slug.split('-');
    return parts.isNotEmpty ? parts.last : slug;
  }
}
