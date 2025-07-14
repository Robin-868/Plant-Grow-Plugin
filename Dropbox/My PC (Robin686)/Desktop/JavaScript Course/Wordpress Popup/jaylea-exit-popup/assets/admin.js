jQuery(document).ready(function($) {
    // Add CSS for spinning animation
    $('<style>.spin { animation: spin 1s linear infinite; } @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }</style>').appendTo('head');
    // Popup status change functionality
    $('input[name="jaylea_popup_enabled"]').on('change', function() {
        updatePopupStatusDisplay();
    });
    
    function updatePopupStatusDisplay() {
        var isActive = $('input[name="jaylea_popup_enabled"]:checked').val() == '1';
        var statusBox = $('.postbox').first();
        var statusHeader = statusBox.find('.hndle');
        var statusDescription = statusBox.find('.description');
        
        // Update border color
        if (isActive) {
            statusBox.css('border-left', '4px solid #46b450');
            statusHeader.css('background', '#f0f9f0');
            statusHeader.find('.dashicons').removeClass('dashicons-dismiss').addClass('dashicons-yes-alt').css('color', '#46b450');
            statusHeader.find('span:last').text('Popup Status: ACTIVE');
            statusDescription.html('<span style="color: #46b450; font-weight: 600;">✓ Popup is currently ACTIVE and will show to visitors</span>');
        } else {
            statusBox.css('border-left', '4px solid #dc3232');
            statusHeader.css('background', '#fdf2f2');
            statusHeader.find('.dashicons').removeClass('dashicons-yes-alt').addClass('dashicons-dismiss').css('color', '#dc3232');
            statusHeader.find('span:last').text('Popup Status: INACTIVE');
            statusDescription.html('<span style="color: #dc3232; font-weight: 600;">⚠ Popup is currently INACTIVE and will not show to visitors</span>');
        }
        
        // Show/hide a notice about the status change
        $('.status-change-notice').remove();
        var noticeClass = isActive ? 'notice-success' : 'notice-warning';
        var noticeText = isActive ? 'Popup activated! Don\'t forget to save your settings.' : 'Popup deactivated! It will not show to visitors until reactivated.';
        var notice = '<div class="notice ' + noticeClass + ' status-change-notice" style="margin: 10px 0;"><p>' + noticeText + '</p></div>';
        statusBox.after(notice);
        
        // Auto-hide notice after 5 seconds
        setTimeout(function() {
            $('.status-change-notice').fadeOut();
        }, 5000);
    }
    
    // Analytics reset functionality
    $('#reset-analytics').on('click', function(e) {
        e.preventDefault();
        
        if (!confirm('Are you sure you want to reset all analytics data? This action cannot be undone.')) {
            return;
        }
        
        var button = $(this);
        var originalText = button.html();
        
        // Show loading state
        button.prop('disabled', true).html('<span class="dashicons dashicons-update spin" style="margin-right: 5px;"></span>Resetting...');
        
        $.ajax({
            url: jaylea_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'jaylea_reset_analytics',
                nonce: jaylea_ajax.nonce
            },
            success: function(response) {
                if (response === 'success') {
                    // Show success message
                    button.after('<span class="success-message" style="color: #46b450; margin-left: 10px; font-weight: 600;">✓ Analytics reset successfully!</span>');
                    
                    // Reload page after 2 seconds to show updated analytics
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    alert('Error resetting analytics. Please try again.');
                    button.prop('disabled', false).html(originalText);
                }
            },
            error: function() {
                alert('Error resetting analytics. Please try again.');
                button.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Image upload functionality
    $('#upload_image_button').click(function(e) {
        e.preventDefault();
        
        var image_uploader = wp.media({
            title: 'Select Image for Popup',
            button: {
                text: 'Use This Image'
            },
            multiple: false
        });
        
        image_uploader.on('select', function() {
            var attachment = image_uploader.state().get('selection').first().toJSON();
            $('#jaylea_popup_image').val(attachment.url);
            $('#image_preview').html('<img src="' + attachment.url + '" style="max-width: 200px; height: auto; border: 1px solid #ddd; border-radius: 4px;" />');
            $('#remove_image_button').show();
            updatePreview();
        });
        
        image_uploader.open();
    });
    
    // Remove image functionality
    $('#remove_image_button').click(function(e) {
        e.preventDefault();
        $('#jaylea_popup_image').val('');
        $('#image_preview').html('');
        $(this).hide();
        updatePreview();
    });
    
    // Live preview updates
    $('input[name="jaylea_popup_title"]').on('input', function() {
        $('#preview-title').text($(this).val());
    });
    
    $('textarea[name="jaylea_popup_message"]').on('input', function() {
        $('#preview-message').text($(this).val());
    });
    
    $('input[name="jaylea_popup_button_text"]').on('input', function() {
        $('#preview-button').text($(this).val());
    });
    
    $('input[name="jaylea_popup_background_color"]').on('change', function() {
        $('#popup-preview').css('background-color', $(this).val());
    });
    
    $('input[name="jaylea_popup_text_color"]').on('change', function() {
        $('#popup-preview').css('color', $(this).val());
    });
    
    // Features preview update
    $('textarea[name="jaylea_popup_features"]').on('input', function() {
        updateFeaturesPreview();
    });
    
    function updatePreview() {
        var imageUrl = $('#jaylea_popup_image').val();
        if (imageUrl) {
            $('#preview-image').html('<img src="' + imageUrl + '" style="max-width: 100px; height: auto; margin-bottom: 10px; border-radius: 4px;" />');
        } else {
            $('#preview-image').html('');
        }
    }
    
    function updateFeaturesPreview() {
        var features = $('textarea[name="jaylea_popup_features"]').val();
        var featuresHtml = '';
        
        if (features.trim()) {
            var featureLines = features.split('\n');
            featuresHtml = '<div style="margin: 15px 0; text-align: left; max-width: 300px; margin-left: auto; margin-right: auto;">';
            
            featureLines.forEach(function(feature) {
                feature = feature.trim();
                if (feature) {
                    featuresHtml += '<div style="margin: 5px 0; font-size: 14px;">' + feature + '</div>';
                }
            });
            
            featuresHtml += '</div>';
        }
        
        $('#preview-features').html(featuresHtml);
    }
    
    // Device targeting preview indicators
    function updateDevicePreview() {
        var desktop = $('input[name="jaylea_popup_show_desktop"]').is(':checked');
        var tablet = $('input[name="jaylea_popup_show_tablet"]').is(':checked');
        var mobile = $('input[name="jaylea_popup_show_mobile"]').is(':checked');
        
        var deviceInfo = '';
        var devices = [];
        if (desktop) devices.push('Desktop');
        if (tablet) devices.push('Tablet');
        if (mobile) devices.push('Mobile');
        
        if (devices.length > 0) {
            deviceInfo = '<p style="font-size: 12px; color: #666; margin-top: 5px;">Will show on: ' + devices.join(', ') + '</p>';
        } else {
            deviceInfo = '<p style="font-size: 12px; color: #d63638; margin-top: 5px;">⚠️ No devices selected - popup won\'t show</p>';
        }
        
        $('.postbox:has(#popup-preview) .inside').find('p:last').after(deviceInfo);
        $('.postbox:has(#popup-preview) .inside').find('p:last').prev().remove(); // Remove previous device info
    }
    
    $('input[name="jaylea_popup_show_desktop"], input[name="jaylea_popup_show_tablet"], input[name="jaylea_popup_show_mobile"]').on('change', function() {
        updateDevicePreview();
    });
    
    // Initialize device preview
    updateDevicePreview();
    
    // ===== TOP BAR FUNCTIONALITY =====
    
    // Top bar status change functionality
    $('input[name="jaylea_topbar_enabled"]').on('change', function() {
        updateTopbarStatusDisplay();
    });
    
    function updateTopbarStatusDisplay() {
        var isActive = $('input[name="jaylea_topbar_enabled"]:checked').val() == '1';
        var statusBox = $('.postbox').filter(function() {
            return $(this).find('h3:contains("Top Bar Status")').length > 0;
        });
        var statusHeader = statusBox.find('h3').first();
        var statusDescription = statusBox.find('.description');
        
        // Update border color
        if (isActive) {
            statusBox.css('border-left', '4px solid #46b450');
            statusHeader.css('background', '#f0f9f0');
            statusHeader.find('.dashicons').removeClass('dashicons-dismiss').addClass('dashicons-yes-alt').css('color', '#46b450');
            statusHeader.find('span:last').text('Top Bar Status: ACTIVE');
            statusDescription.html('<span style="color: #46b450; font-weight: 600;">✓ Top bar is currently ACTIVE and will show to visitors</span>');
        } else {
            statusBox.css('border-left', '4px solid #dc3232');
            statusHeader.css('background', '#fdf2f2');
            statusHeader.find('.dashicons').removeClass('dashicons-yes-alt').addClass('dashicons-dismiss').css('color', '#dc3232');
            statusHeader.find('span:last').text('Top Bar Status: INACTIVE');
            statusDescription.html('<span style="color: #dc3232; font-weight: 600;">⚠ Top bar is currently INACTIVE and will not show to visitors</span>');
        }
        
        // Show/hide a notice about the status change
        $('.topbar-status-change-notice').remove();
        var noticeClass = isActive ? 'notice-success' : 'notice-warning';
        var noticeText = isActive ? 'Top bar activated! Don\'t forget to save your settings.' : 'Top bar deactivated! It will not show to visitors until reactivated.';
        var notice = '<div class="notice ' + noticeClass + ' topbar-status-change-notice" style="margin: 10px 0;"><p>' + noticeText + '</p></div>';
        statusBox.after(notice);
        
        // Auto-hide notice after 5 seconds
        setTimeout(function() {
            $('.topbar-status-change-notice').fadeOut();
        }, 5000);
    }
    
    // Top bar live preview updates
    $('textarea[name="jaylea_topbar_message"]').on('input', function() {
        $('#topbar-preview-message').text($(this).val());
    });
    
    $('input[name="jaylea_topbar_button_text"]').on('input', function() {
        var buttonText = $(this).val();
        if (buttonText.trim()) {
            $('#topbar-preview-button').text(buttonText).show();
            $('#topbar-preview-message').css('margin-bottom', '10px');
        } else {
            $('#topbar-preview-button').hide();
            $('#topbar-preview-message').css('margin-bottom', '0');
        }
    });
    
    $('input[name="jaylea_topbar_background_color"]').on('change', function() {
        $('#topbar-preview').css('background-color', $(this).val());
    });
    
    $('input[name="jaylea_topbar_text_color"]').on('change', function() {
        var color = $(this).val();
        $('#topbar-preview').css('color', color);
        $('#topbar-preview-button').css('color', color);
    });
    
    $('input[name="jaylea_topbar_closeable"]').on('change', function() {
        var closeable = $(this).is(':checked');
        if (closeable) {
            $('#topbar-preview').find('.close-btn').show();
        } else {
            $('#topbar-preview').find('.close-btn').hide();
        }
    });
    
    // Top bar device targeting preview
    function updateTopbarDevicePreview() {
        var desktop = $('input[name="jaylea_topbar_show_desktop"]').is(':checked');
        var tablet = $('input[name="jaylea_topbar_show_tablet"]').is(':checked');
        var mobile = $('input[name="jaylea_topbar_show_mobile"]').is(':checked');
        
        var deviceInfo = '';
        var devices = [];
        if (desktop) devices.push('Desktop');
        if (tablet) devices.push('Tablet');
        if (mobile) devices.push('Mobile');
        
        if (devices.length > 0) {
            deviceInfo = '<p style="font-size: 12px; color: #666; margin-top: 5px;">Will show on: ' + devices.join(', ') + '</p>';
        } else {
            deviceInfo = '<p style="font-size: 12px; color: #d63638; margin-top: 5px;">⚠️ No devices selected - top bar won\'t show</p>';
        }
        
        // Find the top bar preview container and update device info
        var previewContainer = $('#topbar-preview').closest('.postbox');
        previewContainer.find('.topbar-device-info').remove();
        previewContainer.find('.inside').append('<div class="topbar-device-info">' + deviceInfo + '</div>');
    }
    
    $('input[name="jaylea_topbar_show_desktop"], input[name="jaylea_topbar_show_tablet"], input[name="jaylea_topbar_show_mobile"]').on('change', function() {
        updateTopbarDevicePreview();
    });
    
    // Initialize top bar device preview
    updateTopbarDevicePreview();
    
    // Position change handler
    $('select[name="jaylea_topbar_position"]').on('change', function() {
        var position = $(this).val();
        var previewContainer = $('#topbar-preview').closest('.postbox');
        var positionText = position === 'top' ? 'top of page' : 'bottom of page';
        
        previewContainer.find('.position-info').remove();
        previewContainer.find('.inside').append('<div class="position-info"><p style="font-size: 12px; color: #666; margin-top: 5px;">Position: ' + positionText + '</p></div>');
    });
    
    // Initialize position info
    var initialPosition = $('select[name="jaylea_topbar_position"]').val();
    var initialPositionText = initialPosition === 'top' ? 'top of page' : 'bottom of page';
    $('#topbar-preview').closest('.postbox').find('.inside').append('<div class="position-info"><p style="font-size: 12px; color: #666; margin-top: 5px;">Position: ' + initialPositionText + '</p></div>');
    
});