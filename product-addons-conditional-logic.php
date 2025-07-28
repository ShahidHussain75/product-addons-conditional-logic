<?php
/*
Plugin Name: Product Add-Ons Conditional Logic for WooCommerce
Description: Adds conditional logic to WooCommerce Product Add-Ons fields. Show/hide fields based on other field values.
Version: 1.0.0
Author: Shahid
Text Domain: product-addons-conditional-logic
Domain Path: /languages
*/


if ( ! defined( 'ABSPATH' ) ) exit;
// Ensure required functions are available
if ( ! function_exists( 'is_plugin_active' ) ) {
    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
}

// Check if WooCommerce Product Add-Ons is active
add_action( 'admin_init', function() {
    if ( ! is_plugin_active( 'woocommerce-product-addons/woocommerce-product-addons.php' ) ) {
        add_action( 'admin_notices', function() {
            echo '<div class="notice notice-error"><p><strong>Product Add-Ons Conditional Logic for WooCommerce</strong> requires <strong>WooCommerce Product Add-Ons</strong> to be active.</p></div>';
        });
    }
});

// Load includes
foreach ([
    'includes/class-pacl-admin.php',
    'includes/class-pacl-frontend.php',
    'includes/class-pacl-save.php',
] as $file) {
    $path = plugin_dir_path(__FILE__) . $file;
    if (file_exists($path)) require_once $path;
}

// Load assets
add_action('wp_enqueue_scripts', function() {
    if (is_product()) {
        wp_enqueue_script(
            'pacl-conditional-logic',
            plugins_url('assets/js/conditional-logic.js', __FILE__),
            ['jquery'],
            '1.0.0',
            true
        );
        // Try to get conditions from filter, fallback to empty array
        $conds = apply_filters('pacl_get_conditions_json', []);
        wp_localize_script('pacl-conditional-logic', 'pacl_conditions', [
            'conditions' => $conds
        ]);
    }
});
