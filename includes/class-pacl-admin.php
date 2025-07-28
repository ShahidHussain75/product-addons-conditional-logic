<?php
// Admin UI logic for Product Add-Ons Conditional Logic
if ( ! defined( 'ABSPATH' ) ) exit;


class PACL_Admin {
    public function __construct() {
        // Add fields to each add-on row in Product Add-Ons admin (try both hooks for compatibility)
        add_action('woocommerce_product_addons_panel_after_options', [$this, 'add_conditional_logic_fields'], 10, 2);
        add_action('woocommerce_product_addons_panel_option', [$this, 'add_conditional_logic_fields'], 99, 2);
        // Save logic
        add_action('save_post', [$this, 'save_meta'], 20, 2);
        // Optional: enqueue admin styles
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
    }

    // Add conditional logic fields to each add-on field in admin
    public function add_conditional_logic_fields($addon, $loop) {
        $field_id = isset($addon['field_id']) ? $addon['field_id'] : 'field_' . $loop;
        $cond = isset($_POST['pacl_conditions'][$field_id]) ? $_POST['pacl_conditions'][$field_id] : [];
        echo '<div class="pacl-conditional-logic" style="margin:10px 0;padding:10px;border:1px solid #eee;background:#fafafa;">';
        echo '<strong>' . __('Conditional Logic', 'product-addons-conditional-logic') . '</strong><br />';
        // Condition Type
        echo '<label>' . __('Type:', 'product-addons-conditional-logic') . '</label> ';
        echo '<select name="pacl_conditions['.$field_id.'][type]">';
        echo '<option value="">' . __('None', 'product-addons-conditional-logic') . '</option>';
        echo '<option value="show"' . (isset($cond['type']) && $cond['type']==='show' ? ' selected' : '') . '>' . __('Show if', 'product-addons-conditional-logic') . '</option>';
        echo '<option value="hide"' . (isset($cond['type']) && $cond['type']==='hide' ? ' selected' : '') . '>' . __('Hide if', 'product-addons-conditional-logic') . '</option>';
        echo '</select> ';
        // Trigger Field
        echo '<label>' . __('Trigger Field:', 'product-addons-conditional-logic') . '</label> ';
        echo '<input type="text" name="pacl_conditions['.$field_id.'][trigger]" value="' . (isset($cond['trigger']) ? esc_attr($cond['trigger']) : '') . '" placeholder="field_id" /> ';
        // Trigger Value
        echo '<label>' . __('Trigger Value:', 'product-addons-conditional-logic') . '</label> ';
        echo '<input type="text" name="pacl_conditions['.$field_id.'][value]" value="' . (isset($cond['value']) ? esc_attr($cond['value']) : '') . '" placeholder="value" />';
        echo '<p class="description">Set logic: e.g. Show if [Trigger Field] equals [Trigger Value].</p>';
        echo '</div>';
    }

    public function save_meta($post_id, $post) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if ($post->post_type !== 'product') return;
        if (isset($_POST['pacl_conditions']) && is_array($_POST['pacl_conditions'])) {
            update_post_meta($post_id, '_pacl_conditions', $_POST['pacl_conditions']);
        } else {
            delete_post_meta($post_id, '_pacl_conditions');
        }
    }

    public function enqueue_admin_scripts($hook) {
        if ($hook === 'post.php' || $hook === 'post-new.php') {
            global $post;
            $is_product = isset($post) && $post->post_type === 'product';
            if ($hook === 'post.php' || $hook === 'post-new.php' || $is_product) {
                wp_enqueue_style('pacl-admin', plugins_url('../assets/css/admin.css', __FILE__));
                wp_enqueue_script('pacl-admin-logic', plugins_url('../assets/js/admin-conditional-logic.js', __FILE__), ['jquery'], '1.0.0', true);
            }
        }
    }
}

new PACL_Admin();
