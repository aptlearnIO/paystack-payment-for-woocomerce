<?php
/*
Plugin Name: Paystack Gateway for WooCommerce 
Plugin URI: https://aptlearn.com
Description: Extends WooCommerce by Adding the Paystack Gateway, Easy to customize
Version: 1.0
Author: Agba Akin
Author URI: https://akinolaakeem.com
*/

// Include the Paystack library
require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

function aptlearn_is_woocommerce_active() {
    if (class_exists('WooCommerce')) {
        return true;
    }
    return false;
}

function aptlearn_woocommerce_required_notice() {
    $install_url = wp_nonce_url(
        add_query_arg(
            [
                'action' => 'install-plugin',
                'plugin' => 'woocommerce',
            ],
            admin_url('update.php')
        ),
        'install-plugin_woocommerce'
    );

    $activate_url = wp_nonce_url(
        add_query_arg(
            [
                'action' => 'activate',
                'plugin' => 'woocommerce/woocommerce.php',
            ],
            admin_url('plugins.php')
        ),
        'activate-plugin_woocommerce/woocommerce.php'
    );

    if (file_exists(WP_PLUGIN_DIR . '/woocommerce/woocommerce.php')) {
        // WooCommerce is installed but not activated
        $message = "Paystack Gateway for WooCommerce requires WooCommerce to be activated. Please <a href='{$activate_url}'>activate WooCommerce</a> first.";
    } else {
        // WooCommerce is not installed
        $message = "Paystack Gateway for WooCommerce requires WooCommerce to be installed. Please <a href='{$install_url}'>install WooCommerce</a> first.";
    }

    ?>
    <div class="notice notice-error">
        <p><?php _e($message, 'aptlearn-paystack'); ?></p>
    </div>
    <?php
}

function initialize_aptlearn_paystack_plugin() {
    if (!aptlearn_is_woocommerce_active()) {
        add_action('admin_notices', 'aptlearn_woocommerce_required_notice');
        return;
    }

    // Include the main plugin class
    require_once plugin_dir_path(__FILE__) . 'includes/paystack_woocommerce/paystack_woocommerce.php';

    // Add the gateway to WooCommerce
    function aptlearn_add_paystack_gateway_class($methods) {
        $methods[] = 'Aptlearn_WC_Gateway_Paystack';
        return $methods;
    }
    add_filter('woocommerce_payment_gateways', 'aptlearn_add_paystack_gateway_class');

    // Add settings link to the plugin page
      function aptlearn_paystack_plugin_action_links($links) {
    $settings_link = '<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout&section=aptlearn_paystack') . '">' . __('Settings', 'woocommerce') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}

    add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'aptlearn_paystack_plugin_action_links');

    // Add footer link to the plugin page
    function aptlearn_paystack_plugin_meta_links($links, $file) {
        $plugin = plugin_basename(__FILE__);
        if ($file == $plugin) {
            $links[] = 'Made with love by <a href="https://akinolaakeem.com" target="_blank">Agba Akin</a>. If you love this plugin, follow or give me a shout on <a href="https://twitter.com/kynsofficial" target="_blank">Twitter</a>.';
        }
        return $links;
    }
    add_filter('plugin_row_meta', 'aptlearn_paystack_plugin_meta_links', 10, 2);
}

add_action('plugins_loaded', 'initialize_aptlearn_paystack_plugin');

// Create the Paystack payments table on plugin activation
function aptlearn_create_paystack_payments_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'paystack_payments';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        order_id mediumint(9) NOT NULL,
        transaction_ref varchar(255) NOT NULL,
        status varchar(255) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'aptlearn_create_paystack_payments_table');

// Delete the Paystack payments table on plugin deactivation if the option is enabled
function aptlearn_delete_paystack_payments_table() {
    if (get_option('woocommerce_paystack_settings')['delete_table'] === 'yes') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'paystack_payments';
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }
}
register_deactivation_hook(__FILE__, 'aptlearn_delete_paystack_payments_table');

// Existing JavaScript enqueue and admin scripts code...

function aptlearn_paystack_admin_scripts($hook) {
    global $post;

    if ($hook == 'woocommerce_page_wc-settings') {
        wp_enqueue_script('aptlearn_paystack_admin_js', plugins_url('assets/js/admin.js', __FILE__), array('jquery'), '1.0', true);
    }
}

add_action('admin_enqueue_scripts', 'aptlearn_paystack_admin_scripts');

