<?php
/**
 * Admin settings class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Plant_Grow_Admin {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            __('Plant Grow Settings', 'plant-grow-plugin'),
            __('Plant Grow', 'plant-grow-plugin'),
            'manage_options',
            'plant-grow-settings',
            array($this, 'render_settings_page')
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('plant_grow_settings_group', 'plant_grow_settings', array($this, 'sanitize_settings'));
        
        // General settings section
        add_settings_section(
            'plant_grow_general_section',
            __('General Settings', 'plant-grow-plugin'),
            null,
            'plant-grow-settings'
        );
        
        // Enable/Disable
        add_settings_field(
            'enabled',
            __('Enable Widget', 'plant-grow-plugin'),
            array($this, 'render_checkbox_field'),
            'plant-grow-settings',
            'plant_grow_general_section',
            array('field' => 'enabled', 'label' => __('Show plant growth widget on frontend', 'plant-grow-plugin'))
        );
        
        // Position settings section
        add_settings_section(
            'plant_grow_position_section',
            __('Position Settings', 'plant-grow-plugin'),
            null,
            'plant-grow-settings'
        );
        
        add_settings_field(
            'bottom_offset',
            __('Bottom Offset (px)', 'plant-grow-plugin'),
            array($this, 'render_number_field'),
            'plant-grow-settings',
            'plant_grow_position_section',
            array('field' => 'bottom_offset', 'min' => 0, 'max' => 500)
        );
        
        add_settings_field(
            'left_offset',
            __('Left Offset (px)', 'plant-grow-plugin'),
            array($this, 'render_number_field'),
            'plant-grow-settings',
            'plant_grow_position_section',
            array('field' => 'left_offset', 'min' => -100, 'max' => 100)
        );
        
        add_settings_field(
            'widget_width',
            __('Widget Width (px)', 'plant-grow-plugin'),
            array($this, 'render_number_field'),
            'plant-grow-settings',
            'plant_grow_position_section',
            array('field' => 'widget_width', 'min' => 50, 'max' => 500)
        );
        
        // Appearance settings section
        add_settings_section(
            'plant_grow_appearance_section',
            __('Appearance Settings', 'plant-grow-plugin'),
            null,
            'plant-grow-settings'
        );
        
        add_settings_field(
            'progress_bar_color',
            __('Progress Bar Color', 'plant-grow-plugin'),
            array($this, 'render_color_field'),
            'plant-grow-settings',
            'plant_grow_appearance_section',
            array('field' => 'progress_bar_color')
        );
        
        add_settings_field(
            'progress_text_color',
            __('Progress Text Color', 'plant-grow-plugin'),
            array($this, 'render_color_field'),
            'plant-grow-settings',
            'plant_grow_appearance_section',
            array('field' => 'progress_text_color')
        );
        
        add_settings_field(
            'progress_text_size',
            __('Progress Text Size (px)', 'plant-grow-plugin'),
            array($this, 'render_number_field'),
            'plant-grow-settings',
            'plant_grow_appearance_section',
            array('field' => 'progress_text_size', 'min' => 5, 'max' => 20)
        );
        
        add_settings_field(
            'mobile_background',
            __('Mobile Background Color', 'plant-grow-plugin'),
            array($this, 'render_color_field'),
            'plant-grow-settings',
            'plant_grow_appearance_section',
            array('field' => 'mobile_background')
        );
        
        // Image settings section
        add_settings_section(
            'plant_grow_images_section',
            __('Plant Stage Images', 'plant-grow-plugin'),
            null,
            'plant-grow-settings'
        );
        
        add_settings_field(
            'use_media_library',
            __('Use WordPress Media Library', 'plant-grow-plugin'),
            array($this, 'render_checkbox_field'),
            'plant-grow-settings',
            'plant_grow_images_section',
            array('field' => 'use_media_library', 'label' => __('Use uploaded images from media library instead of plugin default images', 'plant-grow-plugin'))
        );
        
        // Add image upload fields for each stage
        for ($i = 1; $i <= 14; $i++) {
            add_settings_field(
                'stage_' . $i . '_image',
                sprintf(__('Stage %d Image', 'plant-grow-plugin'), $i),
                array($this, 'render_image_upload_field'),
                'plant-grow-settings',
                'plant_grow_images_section',
                array('field' => 'stage_' . $i . '_image', 'stage' => $i)
            );
        }
    }
    
    /**
     * Sanitize settings
     */
    public function sanitize_settings($input) {
        $sanitized = array();
        
        $sanitized['enabled'] = isset($input['enabled']) ? 1 : 0;
        $sanitized['use_media_library'] = isset($input['use_media_library']) ? 1 : 0;
        $sanitized['bottom_offset'] = intval($input['bottom_offset']);
        $sanitized['left_offset'] = intval($input['left_offset']);
        $sanitized['widget_width'] = intval($input['widget_width']);
        $sanitized['progress_bar_color'] = sanitize_hex_color($input['progress_bar_color']);
        $sanitized['progress_text_color'] = sanitize_hex_color($input['progress_text_color']);
        $sanitized['progress_text_size'] = intval($input['progress_text_size']);
        $sanitized['mobile_background'] = sanitize_hex_color($input['mobile_background']);
        
        // Sanitize image URLs
        for ($i = 1; $i <= 14; $i++) {
            $sanitized['stage_' . $i . '_image'] = esc_url_raw($input['stage_' . $i . '_image']);
        }
        
        return $sanitized;
    }
    
    /**
     * Render checkbox field
     */
    public function render_checkbox_field($args) {
        $options = get_option('plant_grow_settings', array());
        $value = isset($options[$args['field']]) ? $options[$args['field']] : 0;
        $label = isset($args['label']) ? $args['label'] : '';
        ?>
        <label>
            <input type="checkbox" name="plant_grow_settings[<?php echo esc_attr($args['field']); ?>]" value="1" <?php checked(1, $value); ?>>
            <?php echo esc_html($label); ?>
        </label>
        <?php
    }
    
    /**
     * Render number field
     */
    public function render_number_field($args) {
        $options = get_option('plant_grow_settings', array());
        $value = isset($options[$args['field']]) ? $options[$args['field']] : '';
        $min = isset($args['min']) ? $args['min'] : '';
        $max = isset($args['max']) ? $args['max'] : '';
        ?>
        <input type="number" name="plant_grow_settings[<?php echo esc_attr($args['field']); ?>]" value="<?php echo esc_attr($value); ?>" min="<?php echo esc_attr($min); ?>" max="<?php echo esc_attr($max); ?>" class="small-text">
        <?php
    }
    
    /**
     * Render color field
     */
    public function render_color_field($args) {
        $options = get_option('plant_grow_settings', array());
        $value = isset($options[$args['field']]) ? $options[$args['field']] : '#000000';
        ?>
        <input type="color" name="plant_grow_settings[<?php echo esc_attr($args['field']); ?>]" value="<?php echo esc_attr($value); ?>">
        <?php
    }
    
    /**
     * Render image upload field
     */
    public function render_image_upload_field($args) {
        $options = get_option('plant_grow_settings', array());
        $field_name = $args['field'];
        $value = isset($options[$field_name]) ? $options[$field_name] : '';
        $stage = $args['stage'];
        ?>
        <div class="plant-grow-image-upload">
            <input type="text" name="plant_grow_settings[<?php echo esc_attr($field_name); ?>]" value="<?php echo esc_url($value); ?>" class="regular-text plant-grow-image-url" id="plant-grow-image-<?php echo esc_attr($stage); ?>">
            <button type="button" class="button plant-grow-upload-image" data-stage="<?php echo esc_attr($stage); ?>"><?php _e('Upload Image', 'plant-grow-plugin'); ?></button>
            <button type="button" class="button plant-grow-remove-image" data-stage="<?php echo esc_attr($stage); ?>"><?php _e('Remove', 'plant-grow-plugin'); ?></button>
            <?php if ($value): ?>
                <div class="plant-grow-image-preview" style="margin-top: 10px;">
                    <img src="<?php echo esc_url($value); ?>" style="max-width: 150px; height: auto;">
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        if ($hook !== 'settings_page_plant-grow-settings') {
            return;
        }
        
        wp_enqueue_media();
        wp_enqueue_script(
            'plant-grow-admin',
            PLANT_GROW_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            PLANT_GROW_VERSION,
            true
        );
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('plant_grow_settings_group');
                do_settings_sections('plant-grow-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}

