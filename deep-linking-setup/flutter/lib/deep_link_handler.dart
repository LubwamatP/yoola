import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:uni_links/uni_links.dart';

/// =============================================================================
/// YOOLA DEEP LINK HANDLER
/// =============================================================================
///
/// This class handles deep links for the Yoola app.
///
/// Supported URL patterns:
/// - https://yoola.ug/product/{slug}-{id}
/// - https://yoola.ug/category/{slug}
/// - https://yoola.ug/brand/{slug}
/// - https://yoola.ug/shop/{slug}
/// - https://yoola.ug/search?q={query}
/// - yoola://open/product/{id}
///
/// =============================================================================

class DeepLinkHandler {
  static final DeepLinkHandler _instance = DeepLinkHandler._internal();
  factory DeepLinkHandler() => _instance;
  DeepLinkHandler._internal();

  StreamSubscription? _linkSubscription;
  final StreamController<DeepLinkData> _deepLinkController =
      StreamController<DeepLinkData>.broadcast();

  /// Stream of deep link events
  Stream<DeepLinkData> get deepLinkStream => _deepLinkController.stream;

  /// Initialize deep link handling
  Future<void> init() async {
    // Handle app opened from terminated state via deep link
    await _handleInitialLink();

    // Handle deep links while app is running
    _linkSubscription = uriLinkStream.listen(
      (Uri? uri) {
        if (uri != null) {
          _handleDeepLink(uri);
        }
      },
      onError: (err) {
        debugPrint('Deep link error: $err');
      },
    );
  }

  /// Handle the initial link if app was opened via deep link
  Future<void> _handleInitialLink() async {
    try {
      final Uri? initialUri = await getInitialUri();
      if (initialUri != null) {
        _handleDeepLink(initialUri);
      }
    } on PlatformException catch (e) {
      debugPrint('Failed to get initial link: $e');
    }
  }

  /// Parse and handle the deep link
  void _handleDeepLink(Uri uri) {
    debugPrint('Received deep link: $uri');

    final DeepLinkData? data = _parseDeepLink(uri);
    if (data != null) {
      _deepLinkController.add(data);
    }
  }

  /// Parse deep link URI into structured data
  DeepLinkData? _parseDeepLink(Uri uri) {
    final String path = uri.path;
    final List<String> segments = uri.pathSegments;

    // Handle product links: /product/{slug}-{id}
    if (path.startsWith('/product/') && segments.length >= 2) {
      final String slug = segments[1];
      final String? productId = _extractIdFromSlug(slug);

      return DeepLinkData(
        type: DeepLinkType.product,
        id: productId,
        slug: slug,
        rawUri: uri,
      );
    }

    // Handle category links: /category/{slug}
    if (path.startsWith('/category/') && segments.length >= 2) {
      return DeepLinkData(
        type: DeepLinkType.category,
        slug: segments[1],
        rawUri: uri,
      );
    }

    // Handle brand links: /brand/{slug}
    if (path.startsWith('/brand/') && segments.length >= 2) {
      return DeepLinkData(
        type: DeepLinkType.brand,
        slug: segments[1],
        rawUri: uri,
      );
    }

    // Handle shop links: /shop/{slug}
    if (path.startsWith('/shop/') && segments.length >= 2) {
      return DeepLinkData(
        type: DeepLinkType.shop,
        slug: segments[1],
        rawUri: uri,
      );
    }

    // Handle search links: /search?q={query}
    if (path.startsWith('/search')) {
      final String? query = uri.queryParameters['q'];
      return DeepLinkData(
        type: DeepLinkType.search,
        query: query,
        rawUri: uri,
      );
    }

    // Handle flash deals: /flash-deals/{id}
    if (path.startsWith('/flash-deals/') && segments.length >= 2) {
      return DeepLinkData(
        type: DeepLinkType.flashDeal,
        id: segments[1],
        rawUri: uri,
      );
    }

    // Handle order tracking: /order-tracking/{id}
    if (path.startsWith('/order-tracking/') && segments.length >= 2) {
      return DeepLinkData(
        type: DeepLinkType.orderTracking,
        id: segments[1],
        rawUri: uri,
      );
    }

    // Unknown link type - open home
    return DeepLinkData(
      type: DeepLinkType.home,
      rawUri: uri,
    );
  }

  /// Extract product ID from slug
  /// URL format: /product/{name}-{name}-{id}
  /// Example: samsung-260l-stabiliser-coolpack-rt26har2dsa-IwYJWR
  /// Returns: IwYJWR
  String? _extractIdFromSlug(String slug) {
    final parts = slug.split('-');
    if (parts.isNotEmpty) {
      return parts.last;
    }
    return null;
  }

  /// Dispose resources
  void dispose() {
    _linkSubscription?.cancel();
    _deepLinkController.close();
  }
}

/// Types of deep links supported
enum DeepLinkType {
  home,
  product,
  category,
  brand,
  shop,
  search,
  flashDeal,
  orderTracking,
}

/// Structured deep link data
class DeepLinkData {
  final DeepLinkType type;
  final String? id;
  final String? slug;
  final String? query;
  final Uri rawUri;

  DeepLinkData({
    required this.type,
    this.id,
    this.slug,
    this.query,
    required this.rawUri,
  });

  @override
  String toString() {
    return 'DeepLinkData(type: $type, id: $id, slug: $slug, query: $query)';
  }
}


/// =============================================================================
/// USAGE EXAMPLE
/// =============================================================================
///
/// 1. Initialize in main.dart:
///
/// ```dart
/// void main() async {
///   WidgetsFlutterBinding.ensureInitialized();
///   await DeepLinkHandler().init();
///   runApp(MyApp());
/// }
/// ```
///
/// 2. Listen to deep links in your navigation:
///
/// ```dart
/// class _MyAppState extends State<MyApp> {
///   late StreamSubscription<DeepLinkData> _deepLinkSub;
///
///   @override
///   void initState() {
///     super.initState();
///     _deepLinkSub = DeepLinkHandler().deepLinkStream.listen(_handleDeepLink);
///   }
///
///   void _handleDeepLink(DeepLinkData data) {
///     switch (data.type) {
///       case DeepLinkType.product:
///         Navigator.pushNamed(context, '/product', arguments: data.id);
///         break;
///       case DeepLinkType.category:
///         Navigator.pushNamed(context, '/category', arguments: data.slug);
///         break;
///       case DeepLinkType.search:
///         Navigator.pushNamed(context, '/search', arguments: data.query);
///         break;
///       default:
///         Navigator.pushNamed(context, '/home');
///     }
///   }
///
///   @override
///   void dispose() {
///     _deepLinkSub.cancel();
///     super.dispose();
///   }
/// }
/// ```
///
/// =============================================================================
