<?php
/**
 * Plugin Name: Plant Grow Plugin
 * Plugin URI: https://yourwebsite.com/plant-grow-plugin
 * Description: A scroll-triggered plant growth widget that encourages users to read through your content. The plant grows as users scroll down the page.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: plant-grow-plugin
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('PLANT_GROW_VERSION', '1.0.0');
define('PLANT_GROW_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PLANT_GROW_PLUGIN_URL', plugin_dir_url(__FILE__));
define('PLANT_GROW_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Include required files
require_once PLANT_GROW_PLUGIN_DIR . 'includes/class-plant-grow-admin.php';
require_once PLANT_GROW_PLUGIN_DIR . 'includes/class-plant-grow-frontend.php';

/**
 * Main plugin class
 */
class Plant_Grow_Plugin {
    
    /**
     * Instance of this class
     */
    private static $instance = null;
    
    /**
     * Get instance of this class
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init();
    }
    
    /**
     * Initialize plugin
     */
    private function init() {
        // Initialize admin
        if (is_admin()) {
            new Plant_Grow_Admin();
        }
        
        // Initialize frontend
        new Plant_Grow_Frontend();
    }
}

/**
 * Initialize the plugin
 */
function plant_grow_plugin_init() {
    Plant_Grow_Plugin::get_instance();
}
add_action('plugins_loaded', 'plant_grow_plugin_init');

/**
 * Activation hook
 */
register_activation_hook(__FILE__, 'plant_grow_plugin_activate');
function plant_grow_plugin_activate() {
    // Set default options
    $defaults = array(
        'enabled' => true,
        'position' => 'bottom-left',
        'bottom_offset' => 110,
        'left_offset' => -10,
        'widget_width' => 180,
        'progress_bar_color' => '#d131c9',
        'progress_text_color' => '#3b3b3b',
        'progress_text_size' => 9,
        'mobile_background' => '#add7ff94',
        'use_media_library' => false, // Use plugin directory images by default
    );
    
    // Set default image paths (plugin directory)
    for ($i = 1; $i <= 14; $i++) {
        $defaults['stage_' . $i . '_image'] = PLANT_GROW_PLUGIN_URL . 'assets/images/plant-stage-' . $i . '.png';
    }
    
    add_option('plant_grow_settings', $defaults);
}

/**
 * Deactivation hook
 */
register_deactivation_hook(__FILE__, 'plant_grow_plugin_deactivate');
function plant_grow_plugin_deactivate() {
    // Clean up if needed
}

