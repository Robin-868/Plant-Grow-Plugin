(function() {
    'use strict';
    
    var popup = document.getElementById('jaylea-exit-popup');
    var closeBtn = document.querySelector('.popup-close');
    var overlay = document.querySelector('.popup-overlay');
    var hasShown = sessionStorage.getItem('exit_popup_shown') === 'true';
    var isExiting = false;
    
    if (!popup) return;
    
    // Analytics tracking functions
    function trackPopupDisplay() {
        if (typeof window.jaylea_popup_ajax !== 'undefined') {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', window.jaylea_popup_ajax.ajax_url, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send('action=jaylea_track_popup_display&nonce=' + window.jaylea_popup_ajax.nonce);
        }
    }
    
    function trackPopupClick() {
        if (typeof window.jaylea_popup_ajax !== 'undefined') {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', window.jaylea_popup_ajax.ajax_url, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send('action=jaylea_track_popup_click&nonce=' + window.jaylea_popup_ajax.nonce);
        }
    }
    
    // Device detection
    function getDeviceType() {
        var width = window.innerWidth;
        if (width >= 1024) return 'desktop';
        if (width >= 768) return 'tablet';
        return 'mobile';
    }
    
    function shouldShowOnDevice() {
        var deviceType = getDeviceType();
        var classes = popup.className.split(' ');
        
        return classes.includes('show-' + deviceType);
    }
    
    function showPopup() {
        if (!hasShown && !isExiting && shouldShowOnDevice()) {
            popup.style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevent scrolling
            hasShown = true;
            sessionStorage.setItem('exit_popup_shown', 'true');
            
            // Track popup display
            trackPopupDisplay();
            
            // Track popup display with Google Analytics (if available)
            if (typeof gtag !== 'undefined') {
                gtag('event', 'popup_displayed', {
                    'event_category': 'engagement',
                    'event_label': 'exit_intent',
                    'custom_parameters': {
                        'device_type': getDeviceType()
                    }
                });
            }
        }
    }
    
    function hidePopup() {
        popup.style.display = 'none';
        document.body.style.overflow = ''; // Restore scrolling
        isExiting = false;
        
        // Track popup close with Google Analytics (if available)
        if (typeof gtag !== 'undefined') {
            gtag('event', 'popup_closed', {
                'event_category': 'engagement',
                'event_label': 'exit_intent'
            });
        }
    }
    
    // Exit intent detection - mouse leaving viewport
    document.addEventListener('mouseleave', function(e) {
        if (e.clientY <= 0 && !hasShown) {
            showPopup();
        }
    });
    
    // Mobile scroll-up detection (alternative to mouse leave)
    var lastScrollTop = 0;
    var scrollUpCount = 0;
    
    window.addEventListener('scroll', function() {
        var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        if (scrollTop < lastScrollTop && scrollTop < 100) {
            scrollUpCount++;
            if (scrollUpCount >= 3 && !hasShown) {
                showPopup();
            }
        } else {
            scrollUpCount = 0;
        }
        
        lastScrollTop = scrollTop;
    });
    
    // Beforeunload backup (some browsers block this)
    window.addEventListener('beforeunload', function(e) {
        if (!hasShown) {
            isExiting = true;
            setTimeout(showPopup, 100);
        }
    });
    
    // Close popup events
    if (closeBtn) {
        closeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            hidePopup();
        });
    }
    
    if (overlay) {
        overlay.addEventListener('click', hidePopup);
    }
    
    // ESC key to close
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && popup.style.display === 'block') {
            hidePopup();
        }
    });
    
    // Track button clicks
    var popupButton = document.querySelector('.popup-button');
    if (popupButton) {
        popupButton.addEventListener('click', function() {
            // Track CTA click
            trackPopupClick();
            
            // Track with Google Analytics (if available)
            if (typeof gtag !== 'undefined') {
                gtag('event', 'popup_cta_clicked', {
                    'event_category': 'conversion',
                    'event_label': 'exit_intent'
                });
            }
        });
    }
    
    // Auto-hide after 30 seconds (optional)
    setTimeout(function() {
        if (popup.style.display === 'block') {
            hidePopup();
        }
    }, 30000);
    
    // Handle device orientation/resize changes
    window.addEventListener('resize', function() {
        if (popup.style.display === 'block' && !shouldShowOnDevice()) {
            hidePopup();
        }
    });

})();

// ===== TOP BAR FUNCTIONALITY =====
(function() {
    'use strict';
    
    var topbar = document.getElementById('jaylea-topbar');
    var topbarClose = document.querySelector('.topbar-close');
    var topbarButton = document.querySelector('.topbar-button');
    var hasTopbarShown = sessionStorage.getItem('topbar_shown') === 'true';
    var isTopbarClosed = sessionStorage.getItem('topbar_closed') === 'true';
    
    if (!topbar) return;
    
    // Analytics tracking functions for top bar
    function trackTopbarDisplay() {
        if (typeof window.jaylea_popup_ajax !== 'undefined') {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', window.jaylea_popup_ajax.ajax_url, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send('action=jaylea_track_topbar_display&nonce=' + window.jaylea_popup_ajax.nonce);
        }
    }
    
    function trackTopbarClick() {
        if (typeof window.jaylea_popup_ajax !== 'undefined') {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', window.jaylea_popup_ajax.ajax_url, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send('action=jaylea_track_topbar_click&nonce=' + window.jaylea_popup_ajax.nonce);
        }
    }
    
    // Device detection for top bar
    function getDeviceType() {
        var width = window.innerWidth;
        if (width >= 1024) return 'desktop';
        if (width >= 768) return 'tablet';
        return 'mobile';
    }
    
    function shouldShowTopbarOnDevice() {
        var deviceType = getDeviceType();
        var classes = topbar.className.split(' ');
        
        return classes.includes('show-' + deviceType);
    }
    
    function showTopbar() {
        if (!hasTopbarShown && !isTopbarClosed && shouldShowTopbarOnDevice()) {
            topbar.style.display = 'block';
            
            // Add body class for padding adjustment
            var position = topbar.classList.contains('topbar-top') ? 'topbar-top' : 'topbar-bottom';
            document.body.classList.add('jaylea-topbar-active', position);
            
            hasTopbarShown = true;
            sessionStorage.setItem('topbar_shown', 'true');
            
            // Track top bar display
            trackTopbarDisplay();
            
            // Track with Google Analytics (if available)
            if (typeof gtag !== 'undefined') {
                gtag('event', 'topbar_displayed', {
                    'event_category': 'engagement',
                    'event_label': 'notification_bar',
                    'custom_parameters': {
                        'device_type': getDeviceType()
                    }
                });
            }
        }
    }
    
    function hideTopbar() {
        topbar.style.display = 'none';
        
        // Remove body classes
        document.body.classList.remove('jaylea-topbar-active', 'topbar-top', 'topbar-bottom');
        
        // Remember that user closed it
        sessionStorage.setItem('topbar_closed', 'true');
        
        // Track with Google Analytics (if available)
        if (typeof gtag !== 'undefined') {
            gtag('event', 'topbar_closed', {
                'event_category': 'engagement',
                'event_label': 'notification_bar'
            });
        }
    }
    
    // Show top bar immediately when page loads (if not already closed)
    if (!isTopbarClosed) {
        // Small delay to ensure page is ready
        setTimeout(showTopbar, 500);
    }
    
    // Close top bar events
    if (topbarClose) {
        topbarClose.addEventListener('click', function(e) {
            e.preventDefault();
            hideTopbar();
        });
    }
    
    // Track button clicks
    if (topbarButton) {
        topbarButton.addEventListener('click', function() {
            // Track CTA click
            trackTopbarClick();
            
            // Track with Google Analytics (if available)
            if (typeof gtag !== 'undefined') {
                gtag('event', 'topbar_cta_clicked', {
                    'event_category': 'conversion',
                    'event_label': 'notification_bar'
                });
            }
        });
    }
    
    // Handle device orientation/resize changes for top bar
    window.addEventListener('resize', function() {
        if (topbar.style.display === 'block' && !shouldShowTopbarOnDevice()) {
            hideTopbar();
        }
    });
    
})();