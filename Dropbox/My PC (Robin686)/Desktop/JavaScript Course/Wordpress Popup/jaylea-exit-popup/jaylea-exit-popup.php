<?php
/**
 * Plugin Name: Jaylea Exit Popup
 * Plugin URI: https://yourwebsite.com
 * Description: Shows a customizable popup when users try to leave blog posts. Perfect for capturing leads and increasing engagement.
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL v2 or later
 * Text Domain: jaylea-exit-popup
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('JAYLEA_POPUP_URL', plugin_dir_url(__FILE__));
define('JAYLEA_POPUP_PATH', plugin_dir_path(__FILE__));

class JayleaExitPopup {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        register_activation_hook(__FILE__, array($this, 'activate'));
    }
    
    public function init() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_footer', array($this, 'add_popup_to_posts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        
        // Add AJAX handlers for analytics
        add_action('wp_ajax_jaylea_track_popup_display', array($this, 'track_popup_display'));
        add_action('wp_ajax_nopriv_jaylea_track_popup_display', array($this, 'track_popup_display'));
        add_action('wp_ajax_jaylea_track_popup_click', array($this, 'track_popup_click'));
        add_action('wp_ajax_nopriv_jaylea_track_popup_click', array($this, 'track_popup_click'));
        add_action('wp_ajax_jaylea_track_topbar_display', array($this, 'track_topbar_display'));
        add_action('wp_ajax_nopriv_jaylea_track_topbar_display', array($this, 'track_topbar_display'));
        add_action('wp_ajax_jaylea_track_topbar_click', array($this, 'track_topbar_click'));
        add_action('wp_ajax_nopriv_jaylea_track_topbar_click', array($this, 'track_topbar_click'));
        add_action('wp_ajax_jaylea_reset_analytics', array($this, 'reset_analytics'));
    }
    
    public function activate() {
        // Set default options when plugin is activated
        add_option('jaylea_popup_enabled', 1);
        add_option('jaylea_popup_title', 'Wait! Don\'t Leave Yet!');
        add_option('jaylea_popup_message', 'Subscribe to our newsletter for the latest updates and exclusive content!');
        add_option('jaylea_popup_button_text', 'Subscribe Now');
        add_option('jaylea_popup_button_url', '#');
        add_option('jaylea_popup_background_color', '#ffffff');
        add_option('jaylea_popup_text_color', '#333333');
        add_option('jaylea_popup_show_desktop', 1);
        add_option('jaylea_popup_show_tablet', 1);
        add_option('jaylea_popup_show_mobile', 1);
        add_option('jaylea_popup_features', '');
        
        // Add top bar options
        add_option('jaylea_topbar_enabled', 0);
        add_option('jaylea_topbar_message', '🎉 Special Offer: Get 20% off your first order! Limited time only.');
        add_option('jaylea_topbar_button_text', 'Get Offer');
        add_option('jaylea_topbar_button_url', '#');
        add_option('jaylea_topbar_background_color', '#ff6b35');
        add_option('jaylea_topbar_text_color', '#ffffff');
        add_option('jaylea_topbar_show_desktop', 1);
        add_option('jaylea_topbar_show_tablet', 1);
        add_option('jaylea_topbar_show_mobile', 1);
        add_option('jaylea_topbar_position', 'top');
        add_option('jaylea_topbar_closeable', 1);
        
        // Add analytics options
        add_option('jaylea_popup_displays', 0);
        add_option('jaylea_popup_clicks', 0);
        add_option('jaylea_topbar_displays', 0);
        add_option('jaylea_topbar_clicks', 0);
        add_option('jaylea_popup_analytics_start_date', current_time('mysql'));
    }
    
    public function add_admin_menu() {
        add_options_page(
            'Jaylea Popup Settings',
            'Jaylea Popup',
            'manage_options',
            'jaylea-popup-settings',
            array($this, 'settings_page')
        );
    }
    
    public function register_settings() {
        register_setting('jaylea_popup_settings', 'jaylea_popup_enabled');
        register_setting('jaylea_popup_settings', 'jaylea_popup_title');
        register_setting('jaylea_popup_settings', 'jaylea_popup_message');
        register_setting('jaylea_popup_settings', 'jaylea_popup_image');
        register_setting('jaylea_popup_settings', 'jaylea_popup_button_text');
        register_setting('jaylea_popup_settings', 'jaylea_popup_button_url');
        register_setting('jaylea_popup_settings', 'jaylea_popup_background_color');
        register_setting('jaylea_popup_settings', 'jaylea_popup_text_color');
        register_setting('jaylea_popup_settings', 'jaylea_popup_delay');
        register_setting('jaylea_popup_settings', 'jaylea_popup_show_desktop');
        register_setting('jaylea_popup_settings', 'jaylea_popup_show_tablet');
        register_setting('jaylea_popup_settings', 'jaylea_popup_show_mobile');
        register_setting('jaylea_popup_settings', 'jaylea_popup_features');
        
        // Top bar settings
        register_setting('jaylea_popup_settings', 'jaylea_topbar_enabled');
        register_setting('jaylea_popup_settings', 'jaylea_topbar_message');
        register_setting('jaylea_popup_settings', 'jaylea_topbar_button_text');
        register_setting('jaylea_popup_settings', 'jaylea_topbar_button_url');
        register_setting('jaylea_popup_settings', 'jaylea_topbar_background_color');
        register_setting('jaylea_popup_settings', 'jaylea_topbar_text_color');
        register_setting('jaylea_popup_settings', 'jaylea_topbar_show_desktop');
        register_setting('jaylea_popup_settings', 'jaylea_topbar_show_tablet');
        register_setting('jaylea_popup_settings', 'jaylea_topbar_show_mobile');
        register_setting('jaylea_popup_settings', 'jaylea_topbar_position');
        register_setting('jaylea_popup_settings', 'jaylea_topbar_closeable');
    }
    
    public function track_popup_display() {
        check_ajax_referer('jaylea_popup_nonce', 'nonce');
        
        $current_displays = get_option('jaylea_popup_displays', 0);
        update_option('jaylea_popup_displays', $current_displays + 1);
        
        wp_die('success');
    }
    
    public function track_popup_click() {
        check_ajax_referer('jaylea_popup_nonce', 'nonce');
        
        $current_clicks = get_option('jaylea_popup_clicks', 0);
        update_option('jaylea_popup_clicks', $current_clicks + 1);
        
        wp_die('success');
    }
    
    public function track_topbar_display() {
        check_ajax_referer('jaylea_popup_nonce', 'nonce');
        
        $current_displays = get_option('jaylea_topbar_displays', 0);
        update_option('jaylea_topbar_displays', $current_displays + 1);
        
        wp_die('success');
    }
    
    public function track_topbar_click() {
        check_ajax_referer('jaylea_popup_nonce', 'nonce');
        
        $current_clicks = get_option('jaylea_topbar_clicks', 0);
        update_option('jaylea_topbar_clicks', $current_clicks + 1);
        
        wp_die('success');
    }
    
    public function reset_analytics() {
        check_ajax_referer('jaylea_popup_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        update_option('jaylea_popup_displays', 0);
        update_option('jaylea_popup_clicks', 0);
        update_option('jaylea_topbar_displays', 0);
        update_option('jaylea_topbar_clicks', 0);
        update_option('jaylea_popup_analytics_start_date', current_time('mysql'));
        
        wp_die('success');
    }
    
    public function enqueue_admin_scripts($hook) {
        if ($hook !== 'settings_page_jaylea-popup-settings') {
            return;
        }
        wp_enqueue_media();
        wp_enqueue_script('jaylea-popup-admin', JAYLEA_POPUP_URL . 'assets/admin.js', array('jquery'), '1.0.0', true);
        wp_localize_script('jaylea-popup-admin', 'jaylea_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('jaylea_popup_nonce')
        ));
    }
    
    public function settings_page() {
        ?>
        <div class="wrap">
            <h1><span class="dashicons dashicons-external" style="font-size: 30px; margin-right: 10px;"></span>Jaylea Exit Popup Settings</h1>
            <p>Configure your exit intent popup to capture visitors before they leave your blog posts.</p>
            
            <?php $this->display_analytics_section(); ?>
            
            <?php $this->display_topbar_section(); ?>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('jaylea_popup_settings');
                do_settings_sections('jaylea_popup_settings');
                ?>
                
                <div style="display: flex; gap: 20px;">
                    <div style="flex: 2;">
                        <div class="postbox" style="border-left: 4px solid <?php echo get_option('jaylea_popup_enabled') ? '#46b450' : '#dc3232'; ?>;">
                            <h2 class="hndle" style="padding: 15px; background: <?php echo get_option('jaylea_popup_enabled') ? '#f0f9f0' : '#fdf2f2'; ?>;">
                                <span class="dashicons <?php echo get_option('jaylea_popup_enabled') ? 'dashicons-yes-alt' : 'dashicons-dismiss'; ?>" style="color: <?php echo get_option('jaylea_popup_enabled') ? '#46b450' : '#dc3232'; ?>; margin-right: 8px;"></span>
                                <span>Popup Status: <?php echo get_option('jaylea_popup_enabled') ? 'ACTIVE' : 'INACTIVE'; ?></span>
                            </h2>
                            <div class="inside" style="padding: 0 15px 15px;">
                                <table class="form-table">
                                    <tr>
                                        <th scope="row">Popup Status</th>
                                        <td>
                                            <fieldset>
                                                <label style="display: block; margin-bottom: 10px;">
                                                    <input type="radio" name="jaylea_popup_enabled" value="1" <?php checked(1, get_option('jaylea_popup_enabled'), true); ?> />
                                                    <span class="dashicons dashicons-yes-alt" style="color: #46b450; margin-right: 5px;"></span>
                                                    <strong>Active</strong> - Show the exit intent popup on blog posts
                                                </label>
                                                <label style="display: block;">
                                                    <input type="radio" name="jaylea_popup_enabled" value="0" <?php checked(0, get_option('jaylea_popup_enabled'), true); ?> />
                                                    <span class="dashicons dashicons-dismiss" style="color: #dc3232; margin-right: 5px;"></span>
                                                    <strong>Inactive</strong> - Hide the popup (but keep all settings)
                                                </label>
                                            </fieldset>
                                            <p class="description">
                                                <?php if (get_option('jaylea_popup_enabled')): ?>
                                                    <span style="color: #46b450; font-weight: 600;">✓ Popup is currently ACTIVE and will show to visitors</span>
                                                <?php else: ?>
                                                    <span style="color: #dc3232; font-weight: 600;">⚠ Popup is currently INACTIVE and will not show to visitors</span>
                                                <?php endif; ?>
                                            </p>
                                        </td>
                                    </tr>
                                    
                                </table>
                            </div>
                        </div>
                        
                        <div class="postbox">
                            <h2 class="hndle" style="padding: 15px;"><span>Popup Configuration</span></h2>
                            <div class="inside" style="padding: 0 15px 15px;">
                                <table class="form-table">
                                    <tr>
                                        <th scope="row">Popup Title</th>
                                        <td>
                                            <input type="text" name="jaylea_popup_title" value="<?php echo esc_attr(get_option('jaylea_popup_title')); ?>" class="large-text" />
                                            <p class="description">Main headline for your popup</p>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <th scope="row">Popup Message</th>
                                        <td>
                                            <textarea name="jaylea_popup_message" rows="4" class="large-text"><?php echo esc_textarea(get_option('jaylea_popup_message')); ?></textarea>
                                            <p class="description">Your compelling message to keep visitors engaged</p>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <th scope="row">Popup Image</th>
                                        <td>
                                            <input type="text" name="jaylea_popup_image" id="jaylea_popup_image" value="<?php echo esc_attr(get_option('jaylea_popup_image')); ?>" class="large-text" readonly />
                                            <br><br>
                                            <input type="button" class="button button-secondary" id="upload_image_button" value="Upload Image" />
                                            <input type="button" class="button button-secondary" id="remove_image_button" value="Remove Image" style="<?php echo get_option('jaylea_popup_image') ? '' : 'display:none;'; ?>" />
                                            <p class="description">Optional: Add an eye-catching image to your popup</p>
                                            <div id="image_preview" style="margin-top: 10px;">
                                                <?php if (get_option('jaylea_popup_image')): ?>
                                                    <img src="<?php echo esc_url(get_option('jaylea_popup_image')); ?>" style="max-width: 200px; height: auto; border: 1px solid #ddd; border-radius: 4px;" />
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class="postbox">
                            <h2 class="hndle" style="padding: 15px;"><span>Device Targeting</span></h2>
                            <div class="inside" style="padding: 0 15px 15px;">
                                <table class="form-table">
                                    <tr>
                                        <th scope="row">Show Popup On</th>
                                        <td>
                                            <fieldset>
                                                <label>
                                                    <input type="checkbox" name="jaylea_popup_show_desktop" value="1" <?php checked(1, get_option('jaylea_popup_show_desktop'), true); ?> />
                                                    <span class="dashicons dashicons-desktop" style="margin-right: 5px;"></span>Desktop
                                                </label><br><br>
                                                <label>
                                                    <input type="checkbox" name="jaylea_popup_show_tablet" value="1" <?php checked(1, get_option('jaylea_popup_show_tablet'), true); ?> />
                                                    <span class="dashicons dashicons-tablet" style="margin-right: 5px;"></span>Tablet
                                                </label><br><br>
                                                <label>
                                                    <input type="checkbox" name="jaylea_popup_show_mobile" value="1" <?php checked(1, get_option('jaylea_popup_show_mobile'), true); ?> />
                                                    <span class="dashicons dashicons-smartphone" style="margin-right: 5px;"></span>Mobile
                                                </label>
                                            </fieldset>
                                            <p class="description">Choose which devices should display the popup</p>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class="postbox">
                            <h2 class="hndle" style="padding: 15px;"><span>Product Features</span></h2>
                            <div class="inside" style="padding: 0 15px 15px;">
                                <table class="form-table">
                                    <tr>
                                        <th scope="row">Features List</th>
                                        <td>
                                            <textarea name="jaylea_popup_features" rows="6" class="large-text" placeholder="Enter each feature on a new line&#10;✓ Feature 1&#10;✓ Feature 2&#10;✓ Feature 3"><?php echo esc_textarea(get_option('jaylea_popup_features')); ?></textarea>
                                            <p class="description">Add product features that will appear above the CTA button. Each feature should be on a new line. Use ✓ or • for bullet points.</p>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class="postbox">
                            <h2 class="hndle" style="padding: 15px;"><span>Call-to-Action Button</span></h2>
                            <div class="inside" style="padding: 0 15px 15px;">
                                <table class="form-table">
                                    <tr>
                                        <th scope="row">Button Text</th>
                                        <td>
                                            <input type="text" name="jaylea_popup_button_text" value="<?php echo esc_attr(get_option('jaylea_popup_button_text')); ?>" class="regular-text" />
                                            <p class="description">Text that appears on your CTA button</p>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <th scope="row">Button URL</th>
                                        <td>
                                            <input type="url" name="jaylea_popup_button_url" value="<?php echo esc_attr(get_option('jaylea_popup_button_url')); ?>" class="large-text" />
                                            <p class="description">Where should the button take visitors? (newsletter signup, contact page, etc.)</p>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class="postbox">
                            <h2 class="hndle" style="padding: 15px;"><span>Appearance</span></h2>
                            <div class="inside" style="padding: 0 15px 15px;">
                                <table class="form-table">
                                    <tr>
                                        <th scope="row">Background Color</th>
                                        <td>
                                            <input type="color" name="jaylea_popup_background_color" value="<?php echo esc_attr(get_option('jaylea_popup_background_color')); ?>" />
                                            <p class="description">Background color of the popup</p>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <th scope="row">Text Color</th>
                                        <td>
                                            <input type="color" name="jaylea_popup_text_color" value="<?php echo esc_attr(get_option('jaylea_popup_text_color')); ?>" />
                                            <p class="description">Color of the text in the popup</p>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div style="flex: 1;">
                        <div class="postbox">
                            <h2 class="hndle" style="padding: 15px;"><span>Preview</span></h2>
                            <div class="inside" style="padding: 15px;">
                                <div id="popup-preview" style="border: 2px solid #ddd; border-radius: 8px; padding: 20px; text-align: center; background: white; position: relative;">
                                    <div style="position: absolute; top: 5px; right: 10px; font-size: 20px; color: #999;">&times;</div>
                                    <div id="preview-image"></div>
                                    <h3 id="preview-title" style="margin: 10px 0;"><?php echo esc_html(get_option('jaylea_popup_title')); ?></h3>
                                    <p id="preview-message" style="margin: 10px 0;"><?php echo esc_html(get_option('jaylea_popup_message')); ?></p>
                                    <div id="preview-features" style="margin: 15px 0; text-align: left; max-width: 300px; margin-left: auto; margin-right: auto;">
                                        <?php 
                                        $features = get_option('jaylea_popup_features');
                                        if ($features) {
                                            $feature_lines = explode("\n", $features);
                                            foreach ($feature_lines as $feature) {
                                                $feature = trim($feature);
                                                if (!empty($feature)) {
                                                    echo '<div style="margin: 5px 0; font-size: 14px;">' . esc_html($feature) . '</div>';
                                                }
                                            }
                                        }
                                        ?>
                                    </div>
                                    <button id="preview-button" style="padding: 12px 30px; background: linear-gradient(135deg, #ff9900 0%, #ff7700 100%); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 15px rgba(255, 153, 0, 0.3); cursor: pointer;"><?php echo esc_html(get_option('jaylea_popup_button_text')); ?></button>
                                </div>
                                <p style="margin-top: 10px; font-size: 12px; color: #666;">This is how your popup will look</p>
                            </div>
                        </div>
                        
                        <div class="postbox">
                            <h2 class="hndle" style="padding: 15px;"><span>Tips</span></h2>
                            <div class="inside" style="padding: 15px;">
                                <ul style="list-style: disc; margin-left: 20px;">
                                    <li><strong>Keep it simple:</strong> Clear, concise messaging works best</li>
                                    <li><strong>Strong CTA:</strong> Use action words like "Get", "Subscribe", "Download"</li>
                                    <li><strong>Value proposition:</strong> Tell visitors what they'll get</li>
                                    <li><strong>Test it:</strong> Try different messages to see what works</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php submit_button('Save Settings', 'primary large'); ?>
            </form>
        </div>
        <?php
    }
    
    public function display_analytics_section() {
        $popup_displays = get_option('jaylea_popup_displays', 0);
        $popup_clicks = get_option('jaylea_popup_clicks', 0);
        $topbar_displays = get_option('jaylea_topbar_displays', 0);
        $topbar_clicks = get_option('jaylea_topbar_clicks', 0);
        $start_date = get_option('jaylea_popup_analytics_start_date', current_time('mysql'));
        
        // Calculate conversion rates
        $popup_conversion_rate = 0;
        if ($popup_displays > 0) {
            $popup_conversion_rate = ($popup_clicks / $popup_displays) * 100;
        }
        
        $topbar_conversion_rate = 0;
        if ($topbar_displays > 0) {
            $topbar_conversion_rate = ($topbar_clicks / $topbar_displays) * 100;
        }
        
        // Calculate total stats
        $total_displays = $popup_displays + $topbar_displays;
        $total_clicks = $popup_clicks + $topbar_clicks;
        $total_conversion_rate = 0;
        if ($total_displays > 0) {
            $total_conversion_rate = ($total_clicks / $total_displays) * 100;
        }
        
        // Format start date
        $formatted_date = date('F j, Y', strtotime($start_date));
        ?>
        <div class="postbox" style="margin-bottom: 20px;">
            <h2 class="hndle" style="padding: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; margin: 0;">
                <span class="dashicons dashicons-chart-area" style="margin-right: 8px;"></span>
                <span>📊 Analytics Dashboard</span>
            </h2>
            <div class="inside" style="padding: 20px; background: #f8f9fa;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
                    
                    <!-- Total Displays -->
                    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-left: 4px solid #3498db;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <h3 style="margin: 0; color: #2c3e50; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">Total Displays</h3>
                                <p style="margin: 5px 0 0 0; font-size: 28px; font-weight: bold; color: #3498db;"><?php echo number_format($total_displays); ?></p>
                            </div>
                            <div style="font-size: 32px; color: #3498db; opacity: 0.7;">👁️</div>
                        </div>
                        <p style="margin: 10px 0 0 0; font-size: 11px; color: #7f8c8d;">Popup: <?php echo number_format($popup_displays); ?> | Top Bar: <?php echo number_format($topbar_displays); ?></p>
                    </div>
                    
                    <!-- Total Clicks -->
                    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-left: 4px solid #27ae60;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <h3 style="margin: 0; color: #2c3e50; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">Total Clicks</h3>
                                <p style="margin: 5px 0 0 0; font-size: 28px; font-weight: bold; color: #27ae60;"><?php echo number_format($total_clicks); ?></p>
                            </div>
                            <div style="font-size: 32px; color: #27ae60; opacity: 0.7;">🎯</div>
                        </div>
                        <p style="margin: 10px 0 0 0; font-size: 11px; color: #7f8c8d;">Popup: <?php echo number_format($popup_clicks); ?> | Top Bar: <?php echo number_format($topbar_clicks); ?></p>
                    </div>
                    
                    <!-- Overall Conversion Rate -->
                    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-left: 4px solid <?php echo $total_conversion_rate >= 5 ? '#e74c3c' : ($total_conversion_rate >= 2 ? '#f39c12' : '#95a5a6'); ?>;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <h3 style="margin: 0; color: #2c3e50; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">Overall Rate</h3>
                                <p style="margin: 5px 0 0 0; font-size: 28px; font-weight: bold; color: <?php echo $total_conversion_rate >= 5 ? '#e74c3c' : ($total_conversion_rate >= 2 ? '#f39c12' : '#95a5a6'); ?>;"><?php echo number_format($total_conversion_rate, 1); ?>%</p>
                            </div>
                            <div style="font-size: 32px; color: <?php echo $total_conversion_rate >= 5 ? '#e74c3c' : ($total_conversion_rate >= 2 ? '#f39c12' : '#95a5a6'); ?>; opacity: 0.7;">📈</div>
                        </div>
                        <p style="margin: 10px 0 0 0; font-size: 11px; color: #7f8c8d;">
                            <?php if ($total_conversion_rate >= 5): ?>
                                🔥 Excellent performance!
                            <?php elseif ($total_conversion_rate >= 2): ?>
                                👍 Good conversion rate
                            <?php elseif ($total_displays > 0): ?>
                                📊 Room for improvement
                            <?php else: ?>
                                📋 No data yet
                            <?php endif; ?>
                        </p>
                    </div>
                    
                    <!-- Popup Performance -->
                    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-left: 4px solid #9b59b6;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <h3 style="margin: 0; color: #2c3e50; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">Popup Rate</h3>
                                <p style="margin: 5px 0 0 0; font-size: 28px; font-weight: bold; color: #9b59b6;"><?php echo number_format($popup_conversion_rate, 1); ?>%</p>
                            </div>
                            <div style="font-size: 32px; color: #9b59b6; opacity: 0.7;">🚪</div>
                        </div>
                        <p style="margin: 10px 0 0 0; font-size: 11px; color: #7f8c8d;">Exit intent performance</p>
                    </div>
                    
                    <!-- Top Bar Performance -->
                    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-left: 4px solid #e67e22;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <h3 style="margin: 0; color: #2c3e50; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">Top Bar Rate</h3>
                                <p style="margin: 5px 0 0 0; font-size: 28px; font-weight: bold; color: #e67e22;"><?php echo number_format($topbar_conversion_rate, 1); ?>%</p>
                            </div>
                            <div style="font-size: 32px; color: #e67e22; opacity: 0.7;">📢</div>
                        </div>
                        <p style="margin: 10px 0 0 0; font-size: 11px; color: #7f8c8d;">Notification bar performance</p>
                    </div>
                    
                </div>
                
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 15px; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <div>
                        <p style="margin: 0; color: #7f8c8d; font-size: 14px;">
                            <span class="dashicons dashicons-calendar-alt" style="margin-right: 5px;"></span>
                            Analytics tracking since: <strong><?php echo $formatted_date; ?></strong>
                        </p>
                        <?php if ($total_displays > 0): ?>
                            <p style="margin: 5px 0 0 0; color: #7f8c8d; font-size: 12px;">
                                Average: <?php echo number_format($total_displays / max(1, (time() - strtotime($start_date)) / DAY_IN_SECONDS), 1); ?> displays per day
                            </p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <button type="button" id="reset-analytics" class="button button-secondary" style="color: #dc3545; border-color: #dc3545;">
                            <span class="dashicons dashicons-update" style="margin-right: 5px;"></span>
                            Reset Analytics
                        </button>
                    </div>
                </div>
                
                <?php if ($total_displays == 0): ?>
                    <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 15px; margin-top: 15px;">
                        <p style="margin: 0; color: #856404;">
                            <span class="dashicons dashicons-info" style="margin-right: 5px;"></span>
                            <strong>Getting Started:</strong> Your analytics will start tracking once visitors begin seeing your popup or top bar. Make sure at least one feature is enabled and test it on your website!
                        </p>
                    </div>
                <?php endif; ?>
                
            </div>
        </div>
        <?php
    }
    
    public function display_topbar_section() {
        ?>
        <div class="postbox" style="margin-bottom: 20px;">
            <h2 class="hndle" style="padding: 15px; background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%); color: white; margin: 0;">
                <span class="dashicons dashicons-admin-appearance" style="margin-right: 8px;"></span>
                <span>📢 Top Bar Notification</span>
            </h2>
            <div class="inside" style="padding: 20px; background: #f8f9fa;">
                <div style="display: flex; gap: 20px;">
                    <div style="flex: 2;">
                        <div class="postbox" style="border-left: 4px solid <?php echo get_option('jaylea_topbar_enabled') ? '#46b450' : '#dc3232'; ?>;">
                            <h3 class="hndle" style="padding: 15px; background: <?php echo get_option('jaylea_topbar_enabled') ? '#f0f9f0' : '#fdf2f2'; ?>; margin: 0;">
                                <span class="dashicons <?php echo get_option('jaylea_topbar_enabled') ? 'dashicons-yes-alt' : 'dashicons-dismiss'; ?>" style="color: <?php echo get_option('jaylea_topbar_enabled') ? '#46b450' : '#dc3232'; ?>; margin-right: 8px;"></span>
                                <span>Top Bar Status: <?php echo get_option('jaylea_topbar_enabled') ? 'ACTIVE' : 'INACTIVE'; ?></span>
                            </h3>
                            <div class="inside" style="padding: 15px;">
                                <table class="form-table">
                                    <tr>
                                        <th scope="row">Top Bar Status</th>
                                        <td>
                                            <fieldset>
                                                <label style="display: block; margin-bottom: 10px;">
                                                    <input type="radio" name="jaylea_topbar_enabled" value="1" <?php checked(1, get_option('jaylea_topbar_enabled'), true); ?> />
                                                    <span class="dashicons dashicons-yes-alt" style="color: #46b450; margin-right: 5px;"></span>
                                                    <strong>Active</strong> - Show the top bar notification
                                                </label>
                                                <label style="display: block;">
                                                    <input type="radio" name="jaylea_topbar_enabled" value="0" <?php checked(0, get_option('jaylea_topbar_enabled'), true); ?> />
                                                    <span class="dashicons dashicons-dismiss" style="color: #dc3232; margin-right: 5px;"></span>
                                                    <strong>Inactive</strong> - Hide the top bar notification
                                                </label>
                                            </fieldset>
                                            <p class="description">
                                                <?php if (get_option('jaylea_topbar_enabled')): ?>
                                                    <span style="color: #46b450; font-weight: 600;">✓ Top bar is currently ACTIVE and will show to visitors</span>
                                                <?php else: ?>
                                                    <span style="color: #dc3232; font-weight: 600;">⚠ Top bar is currently INACTIVE and will not show to visitors</span>
                                                <?php endif; ?>
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class="postbox">
                            <h3 class="hndle" style="padding: 15px; margin: 0;"><span>Top Bar Configuration</span></h3>
                            <div class="inside" style="padding: 15px;">
                                <table class="form-table">
                                    <tr>
                                        <th scope="row">Message</th>
                                        <td>
                                            <textarea name="jaylea_topbar_message" rows="2" class="large-text" placeholder="🎉 Special Offer: Get 20% off your first order! Limited time only."><?php echo esc_textarea(get_option('jaylea_topbar_message')); ?></textarea>
                                            <p class="description">Your notification message (emojis welcome!)</p>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <th scope="row">Button Text</th>
                                        <td>
                                            <input type="text" name="jaylea_topbar_button_text" value="<?php echo esc_attr(get_option('jaylea_topbar_button_text')); ?>" class="regular-text" />
                                            <p class="description">Text for the action button (leave empty to hide button)</p>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <th scope="row">Button URL</th>
                                        <td>
                                            <input type="url" name="jaylea_topbar_button_url" value="<?php echo esc_attr(get_option('jaylea_topbar_button_url')); ?>" class="large-text" />
                                            <p class="description">Where should the button take visitors?</p>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <th scope="row">Position</th>
                                        <td>
                                            <select name="jaylea_topbar_position">
                                                <option value="top" <?php selected('top', get_option('jaylea_topbar_position')); ?>>Top of page</option>
                                                <option value="bottom" <?php selected('bottom', get_option('jaylea_topbar_position')); ?>>Bottom of page</option>
                                            </select>
                                            <p class="description">Where to display the notification bar</p>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <th scope="row">Closeable</th>
                                        <td>
                                            <label>
                                                <input type="checkbox" name="jaylea_topbar_closeable" value="1" <?php checked(1, get_option('jaylea_topbar_closeable'), true); ?> />
                                                Allow users to close the notification bar
                                            </label>
                                            <p class="description">If enabled, users can dismiss the notification</p>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class="postbox">
                            <h3 class="hndle" style="padding: 15px; margin: 0;"><span>Device Targeting</span></h3>
                            <div class="inside" style="padding: 15px;">
                                <table class="form-table">
                                    <tr>
                                        <th scope="row">Show Top Bar On</th>
                                        <td>
                                            <fieldset>
                                                <label>
                                                    <input type="checkbox" name="jaylea_topbar_show_desktop" value="1" <?php checked(1, get_option('jaylea_topbar_show_desktop'), true); ?> />
                                                    <span class="dashicons dashicons-desktop" style="margin-right: 5px;"></span>Desktop
                                                </label><br><br>
                                                <label>
                                                    <input type="checkbox" name="jaylea_topbar_show_tablet" value="1" <?php checked(1, get_option('jaylea_topbar_show_tablet'), true); ?> />
                                                    <span class="dashicons dashicons-tablet" style="margin-right: 5px;"></span>Tablet
                                                </label><br><br>
                                                <label>
                                                    <input type="checkbox" name="jaylea_topbar_show_mobile" value="1" <?php checked(1, get_option('jaylea_topbar_show_mobile'), true); ?> />
                                                    <span class="dashicons dashicons-smartphone" style="margin-right: 5px;"></span>Mobile
                                                </label>
                                            </fieldset>
                                            <p class="description">Choose which devices should display the top bar</p>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class="postbox">
                            <h3 class="hndle" style="padding: 15px; margin: 0;"><span>Appearance</span></h3>
                            <div class="inside" style="padding: 15px;">
                                <table class="form-table">
                                    <tr>
                                        <th scope="row">Background Color</th>
                                        <td>
                                            <input type="color" name="jaylea_topbar_background_color" value="<?php echo esc_attr(get_option('jaylea_topbar_background_color')); ?>" />
                                            <p class="description">Background color of the top bar</p>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <th scope="row">Text Color</th>
                                        <td>
                                            <input type="color" name="jaylea_topbar_text_color" value="<?php echo esc_attr(get_option('jaylea_topbar_text_color')); ?>" />
                                            <p class="description">Color of the text in the top bar</p>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div style="flex: 1;">
                        <div class="postbox">
                            <h3 class="hndle" style="padding: 15px; margin: 0;"><span>Top Bar Preview</span></h3>
                            <div class="inside" style="padding: 15px;">
                                <div id="topbar-preview" style="border: 2px solid #ddd; border-radius: 8px; padding: 15px; text-align: center; background: <?php echo esc_attr(get_option('jaylea_topbar_background_color')); ?>; color: <?php echo esc_attr(get_option('jaylea_topbar_text_color')); ?>; position: relative; font-size: 14px; line-height: 1.4;">
                                    <?php if (get_option('jaylea_topbar_closeable')): ?>
                                        <div style="position: absolute; top: 5px; right: 10px; font-size: 16px; opacity: 0.7;">&times;</div>
                                    <?php endif; ?>
                                    <div id="topbar-preview-message" style="margin-bottom: <?php echo get_option('jaylea_topbar_button_text') ? '10px' : '0'; ?>;">
                                        <?php echo esc_html(get_option('jaylea_topbar_message')); ?>
                                    </div>
                                    <?php if (get_option('jaylea_topbar_button_text')): ?>
                                        <button id="topbar-preview-button" style="padding: 8px 16px; background: rgba(255,255,255,0.2); color: <?php echo esc_attr(get_option('jaylea_topbar_text_color')); ?>; border: 1px solid rgba(255,255,255,0.3); border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer;">
                                            <?php echo esc_html(get_option('jaylea_topbar_button_text')); ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                                <p style="margin-top: 10px; font-size: 12px; color: #666;">This is how your top bar will look</p>
                            </div>
                        </div>
                        
                        <div class="postbox">
                            <h3 class="hndle" style="padding: 15px; margin: 0;"><span>Top Bar Tips</span></h3>
                            <div class="inside" style="padding: 15px;">
                                <ul style="list-style: disc; margin-left: 20px; font-size: 14px;">
                                    <li><strong>Keep it brief:</strong> Short messages work best</li>
                                    <li><strong>Use urgency:</strong> "Limited time" creates action</li>
                                    <li><strong>Bright colors:</strong> Stand out but don't overwhelm</li>
                                    <li><strong>Test placement:</strong> Try top vs bottom positioning</li>
                                    <li><strong>Mobile-friendly:</strong> Ensure it looks good on all devices</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    public function add_popup_to_posts() {
        $popup_enabled = get_option('jaylea_popup_enabled');
        $topbar_enabled = get_option('jaylea_topbar_enabled');
        
        if (!is_single() || (!$popup_enabled && !$topbar_enabled)) {
            return;
        }
        
        // Get popup settings
        $title = get_option('jaylea_popup_title');
        $message = get_option('jaylea_popup_message');
        $image = get_option('jaylea_popup_image');
        $button_text = get_option('jaylea_popup_button_text');
        $button_url = get_option('jaylea_popup_button_url');
        $bg_color = get_option('jaylea_popup_background_color');
        $text_color = get_option('jaylea_popup_text_color');
        $features = get_option('jaylea_popup_features');
        $show_desktop = get_option('jaylea_popup_show_desktop');
        $show_tablet = get_option('jaylea_popup_show_tablet');
        $show_mobile = get_option('jaylea_popup_show_mobile');
        
        // Create device targeting classes for popup
        $device_classes = array();
        if ($show_desktop) $device_classes[] = 'show-desktop';
        if ($show_tablet) $device_classes[] = 'show-tablet';
        if ($show_mobile) $device_classes[] = 'show-mobile';
        $device_class = implode(' ', $device_classes);
        
        // Get top bar settings
        $topbar_message = get_option('jaylea_topbar_message');
        $topbar_button_text = get_option('jaylea_topbar_button_text');
        $topbar_button_url = get_option('jaylea_topbar_button_url');
        $topbar_bg_color = get_option('jaylea_topbar_background_color');
        $topbar_text_color = get_option('jaylea_topbar_text_color');
        $topbar_show_desktop = get_option('jaylea_topbar_show_desktop');
        $topbar_show_tablet = get_option('jaylea_topbar_show_tablet');
        $topbar_show_mobile = get_option('jaylea_topbar_show_mobile');
        $topbar_position = get_option('jaylea_topbar_position');
        $topbar_closeable = get_option('jaylea_topbar_closeable');
        
        // Create device targeting classes for top bar
        $topbar_device_classes = array();
        if ($topbar_show_desktop) $topbar_device_classes[] = 'show-desktop';
        if ($topbar_show_tablet) $topbar_device_classes[] = 'show-tablet';
        if ($topbar_show_mobile) $topbar_device_classes[] = 'show-mobile';
        $topbar_device_class = implode(' ', $topbar_device_classes);
        ?>
        
        <?php if ($popup_enabled): ?>
        <div id="jaylea-exit-popup" class="<?php echo esc_attr($device_class); ?>" style="display: none;">
            <div class="popup-overlay"></div>
            <div class="popup-content" style="background-color: <?php echo esc_attr($bg_color); ?>; color: <?php echo esc_attr($text_color); ?>;">
                <button class="popup-close" aria-label="Close popup">&times;</button>
                
                <?php if ($image): ?>
                    <img src="<?php echo esc_url($image); ?>" alt="Popup Image" class="popup-image" />
                <?php endif; ?>
                
                <h2><?php echo esc_html($title); ?></h2>
                <p><?php echo esc_html($message); ?></p>
                
                <?php if ($features): ?>
                    <div class="popup-features">
                        <?php 
                        $feature_lines = explode("\n", $features);
                        foreach ($feature_lines as $feature) {
                            $feature = trim($feature);
                            if (!empty($feature)) {
                                echo '<div class="feature-item">' . esc_html($feature) . '</div>';
                            }
                        }
                        ?>
                    </div>
                <?php endif; ?>
                
                <a href="<?php echo esc_url($button_url); ?>" class="popup-button amazon-style" target="_blank" rel="noopener">
                    <?php echo esc_html($button_text); ?>
                </a>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if ($topbar_enabled): ?>
        <div id="jaylea-topbar" class="<?php echo esc_attr($topbar_device_class); ?> topbar-<?php echo esc_attr($topbar_position); ?>" style="display: none;">
            <div class="topbar-content" style="background-color: <?php echo esc_attr($topbar_bg_color); ?>; color: <?php echo esc_attr($topbar_text_color); ?>;">
                <div class="topbar-inner">
                    <div class="topbar-message">
                        <?php echo esc_html($topbar_message); ?>
                    </div>
                    <?php if ($topbar_button_text): ?>
                        <a href="<?php echo esc_url($topbar_button_url); ?>" class="topbar-button" target="_blank" rel="noopener">
                            <?php echo esc_html($topbar_button_text); ?>
                        </a>
                    <?php endif; ?>
                    <?php if ($topbar_closeable): ?>
                        <button class="topbar-close" aria-label="Close notification">&times;</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <link rel="stylesheet" type="text/css" href="<?php echo JAYLEA_POPUP_URL; ?>assets/popup.css">
        <script>
            window.jaylea_popup_ajax = {
                ajax_url: '<?php echo admin_url('admin-ajax.php'); ?>',
                nonce: '<?php echo wp_create_nonce('jaylea_popup_nonce'); ?>'
            };
        </script>
        <script src="<?php echo JAYLEA_POPUP_URL; ?>assets/popup.js"></script>
        <?php
    }
}

new JayleaExitPopup();
?>