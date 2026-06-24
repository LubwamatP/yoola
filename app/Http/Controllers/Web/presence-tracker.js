/**
 * Real-time Presence Tracker
 * Sends heartbeat to server every 10 seconds to track active users
 * Automatically detects current page and product being viewed
 */
(function() {
    'use strict';

    const HEARTBEAT_INTERVAL = 10000; // 10 seconds
    let heartbeatTimer = null;
    let isPageVisible = true;
    let currentProductId = null;
    let currentPage = null;

    /**
     * Detect current page type from URL
     */
    function detectCurrentPage() {
        const path = window.location.pathname;

        if (path === '/' || path === '') {
            return 'home';
        }
        if (path.includes('/product/')) {
            return 'product';
        }
        if (path.includes('/category/') || path.includes('/categories')) {
            return 'category';
        }
        if (path.includes('/search')) {
            return 'search';
        }
        if (path.includes('/cart') || path.includes('/shop-cart')) {
            return 'cart';
        }
        if (path.includes('/checkout')) {
            return 'checkout';
        }
        if (path.includes('/vendor-shop') || path.includes('/shop/')) {
            return 'shop';
        }
        if (path.includes('/user-') || path.includes('/account-')) {
            return 'account';
        }
        if (path.includes('/brand/')) {
            return 'brand';
        }

        return 'other';
    }

    /**
     * Detect product ID from page (if viewing a product)
     */
    function detectProductId() {
        // Check for product ID in data attribute
        const productElement = document.querySelector('[data-product-id]');
        if (productElement) {
            return parseInt(productElement.dataset.productId, 10) || null;
        }

        // Check for hidden input with product ID
        const productInput = document.querySelector('input[name="product_id"]');
        if (productInput) {
            return parseInt(productInput.value, 10) || null;
        }

        // Check meta tag
        const metaProductId = document.querySelector('meta[property="product:id"]');
        if (metaProductId) {
            return parseInt(metaProductId.content, 10) || null;
        }

        return null;
    }

    /**
     * Send heartbeat to server
     */
    function sendHeartbeat() {
        if (!isPageVisible) {
            return;
        }

        currentPage = detectCurrentPage();
        currentProductId = detectProductId();

        const csrfToken = document.querySelector('meta[name="csrf-token"]');

        fetch('/presence/heartbeat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken ? csrfToken.content : '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: currentProductId,
                page: currentPage
            }),
            keepalive: true
        }).catch(function(error) {
            // Silently fail - don't disrupt user experience
            console.debug('Presence heartbeat failed:', error);
        });
    }

    /**
     * Notify server that user is leaving
     */
    function sendLeave() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');

        // Use sendBeacon for reliable delivery on page unload
        if (navigator.sendBeacon) {
            const data = new FormData();
            data.append('_token', csrfToken ? csrfToken.content : '');

            navigator.sendBeacon('/presence/leave', data);
        } else {
            // Fallback for older browsers
            fetch('/presence/leave', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken ? csrfToken.content : ''
                },
                body: JSON.stringify({}),
                keepalive: true
            }).catch(function() {});
        }
    }

    /**
     * Handle visibility change (tab switching)
     */
    function handleVisibilityChange() {
        if (document.hidden) {
            isPageVisible = false;
        } else {
            isPageVisible = true;
            // Send immediate heartbeat when returning to page
            sendHeartbeat();
        }
    }

    /**
     * Start the heartbeat timer
     */
    function startHeartbeat() {
        // Send initial heartbeat immediately
        sendHeartbeat();

        // Set up interval for continuous heartbeats
        heartbeatTimer = setInterval(sendHeartbeat, HEARTBEAT_INTERVAL);
    }

    /**
     * Stop the heartbeat timer
     */
    function stopHeartbeat() {
        if (heartbeatTimer) {
            clearInterval(heartbeatTimer);
            heartbeatTimer = null;
        }
    }

    /**
     * Initialize presence tracking
     */
    function init() {
        // Start heartbeat
        startHeartbeat();

        // Listen for visibility changes
        document.addEventListener('visibilitychange', handleVisibilityChange);

        // Send leave notification when page unloads
        window.addEventListener('beforeunload', sendLeave);
        window.addEventListener('pagehide', sendLeave);

        // Handle page navigation in SPAs
        window.addEventListener('popstate', function() {
            sendHeartbeat();
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose API for manual updates (e.g., when viewing a product via AJAX modal)
    window.PresenceTracker = {
        setProductId: function(productId) {
            currentProductId = productId;
            sendHeartbeat();
        },
        setPage: function(page) {
            currentPage = page;
            sendHeartbeat();
        },
        sendHeartbeat: sendHeartbeat
    };
})();
