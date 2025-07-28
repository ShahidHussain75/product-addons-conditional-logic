<?php
// Frontend logic for Product Add-Ons Conditional Logic
if ( ! defined( 'ABSPATH' ) ) exit;

class PACL_Frontend {
    public function __construct() {
        add_filter('pacl_get_conditions_json', [$this, 'get_conditions_json']);
        add_action('woocommerce_before_add_to_cart_button', [$this, 'output_conditions_json']);
        add_filter('woocommerce_add_cart_item_data', [$this, 'ignore_hidden_fields'], 10, 3);
    }

    public function get_conditions_json($json) {
        global $post;
        if (!is_product() || empty($post)) return $json;
        $conds = get_post_meta($post->ID, '_pacl_conditions', true);
        if (!is_array($conds)) $conds = [];
        return $conds;
    }

    public function output_conditions_json() {
        global $post;
        $conds = get_post_meta($post->ID, '_pacl_conditions', true);
        if (!is_array($conds)) $conds = [];
        echo '<script type="application/json" id="pacl-conditions-json">' . wp_json_encode($conds) . '</script>';
    }

    public function ignore_hidden_fields($cart_item_data, $product_id, $variation_id) {
        // Remove add-on fields that are hidden by logic (handled in JS, but double-check here)
        if (isset($_POST['addon']) && is_array($_POST['addon'])) {
            $conds = get_post_meta($product_id, '_pacl_conditions', true);
            if (is_array($conds)) {
                foreach ($conds as $field_id => $cond) {
                    if ($this->is_field_hidden($cond, $_POST['addon'])) {
                        unset($cart_item_data['addon'][$field_id]);
                    }
                }
            }
        }
        return $cart_item_data;
    }

    private function is_field_hidden($cond, $addons) {
        if (empty($cond['trigger']) || !isset($addons[$cond['trigger']])) return false;
        $trigger_val = $addons[$cond['trigger']];
        if ($cond['type'] === 'show') {
            return $trigger_val != $cond['value'];
        } else {
            return $trigger_val == $cond['value'];
        }
    }
}

new PACL_Frontend();
