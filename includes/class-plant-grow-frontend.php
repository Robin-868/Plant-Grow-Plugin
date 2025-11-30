<?php
/**
 * Frontend display class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Plant_Grow_Frontend {
    
    private $settings;
    
    public function __construct() {
        $this->settings = get_option('plant_grow_settings', array());
        
        // Only load if enabled
        if (isset($this->settings['enabled']) && $this->settings['enabled']) {
            add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
            add_action('wp_footer', array($this, 'render_widget'));
        }
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        // Enqueue CSS
        wp_enqueue_style(
            'plant-grow-style',
            PLANT_GROW_PLUGIN_URL . 'assets/css/plant-grow.css',
            array(),
            PLANT_GROW_VERSION
        );
        
        // Enqueue JavaScript
        wp_enqueue_script(
            'plant-grow-script',
            PLANT_GROW_PLUGIN_URL . 'assets/js/plant-grow.js',
            array(),
            PLANT_GROW_VERSION,
            true
        );
        
        // Pass settings to JavaScript
        wp_localize_script('plant-grow-script', 'plantGrowSettings', array(
            'stages' => $this->get_stage_images(),
            'settings' => array(
                'bottom' => isset($this->settings['bottom_offset']) ? intval($this->settings['bottom_offset']) : 110,
                'left' => isset($this->settings['left_offset']) ? intval($this->settings['left_offset']) : -10,
                'width' => isset($this->settings['widget_width']) ? intval($this->settings['widget_width']) : 180,
                'progressBarColor' => isset($this->settings['progress_bar_color']) ? $this->settings['progress_bar_color'] : '#d131c9',
                'progressTextColor' => isset($this->settings['progress_text_color']) ? $this->settings['progress_text_color'] : '#3b3b3b',
                'progressTextSize' => isset($this->settings['progress_text_size']) ? intval($this->settings['progress_text_size']) : 9,
                'mobileBackground' => isset($this->settings['mobile_background']) ? $this->settings['mobile_background'] : '#add7ff94',
            )
        ));
    }
    
    /**
     * Get stage images (from media library or plugin directory)
     */
    private function get_stage_images() {
        $images = array();
        $use_media_library = isset($this->settings['use_media_library']) && $this->settings['use_media_library'];
        
        for ($i = 1; $i <= 14; $i++) {
            $field_name = 'stage_' . $i . '_image';
            
            if ($use_media_library && isset($this->settings[$field_name]) && !empty($this->settings[$field_name])) {
                // Use media library image
                $images[$i] = $this->settings[$field_name];
            } else {
                // Use plugin directory default image
                $images[$i] = PLANT_GROW_PLUGIN_URL . 'assets/images/plant-stage-' . $i . '.png';
            }
        }
        
        return $images;
    }
    
    /**
     * Render widget HTML
     */
    public function render_widget() {
        $settings = $this->settings;
        $stage_images = $this->get_stage_images();
        ?>
        <div class="plant-widget" id="plant-widget">
            <!-- Plant Container -->
            <div class="plant-container">
                <?php for ($i = 1; $i <= 14; $i++): ?>
                    <div class="plant-stage" id="stage<?php echo $i; ?>" data-stage="<?php echo $i; ?>">
                        <img src="<?php echo esc_url($stage_images[$i]); ?>" alt="<?php printf(__('Plant Stage %d', 'plant-grow-plugin'), $i); ?>" loading="lazy">
                    </div>
                <?php endfor; ?>
            </div>

            <!-- Bottom Section - Contains progress bar -->
            <div class="bottom-section">
                <div class="progress-section" id="progressSection">
                    <div class="progress-bar-container">
                        <div class="progress-bar" id="progressBar"></div>
                    </div>
                    <div class="progress-text" id="progressText">0%</div>
                </div>
            </div>
        </div>
        <?php
    }
}

