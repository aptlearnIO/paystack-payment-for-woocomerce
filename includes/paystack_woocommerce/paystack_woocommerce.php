<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Aptlearn_WC_Gateway_Paystack extends WC_Payment_Gateway
{
    public function __construct()
    {
        $this->id = 'aptlearn_paystack';
        $this->icon = apply_filters('woocommerce_gateway_icon', '');
        $this->has_fields = false;
        $this->method_title = __('Paystack', 'woocommerce');
        $this->method_description = __('Paystack Gateway Plugin for WooCommerce', 'woocommerce');
        $this->supports = array('products');

        // Load the settings.
        $this->init_form_fields();
        $this->init_settings();

        // Define user set variables
        $this->enabled = $this->get_option('enabled');
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->testmode = 'yes' === $this->get_option('testmode', 'no');
        $this->enable_live_mode = 'yes' === $this->get_option('enable_live_mode', 'no');
        $this->test_public_key = $this->get_option('test_public_key');
        $this->test_secret_key = $this->get_option('test_secret_key');
        $this->live_public_key = $this->get_option('live_public_key');
        $this->live_secret_key = $this->get_option('live_secret_key');
        $this->success_url = $this->get_option('success_url');
        $this->failure_url = $this->get_option('failure_url');
        $this->enable_transaction_fee = 'yes' === $this->get_option('enable_transaction_fee', 'no');
        $this->transaction_fee = $this->get_option('transaction_fee');
        $this->order_status = $this->get_option('order_status', 'completed');
        $this->public_key = $this->enable_live_mode ? $this->live_public_key : $this->test_public_key;
        $this->secret_key = $this->enable_live_mode ? $this->live_secret_key : $this->test_secret_key;

        // Actions
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        add_action('woocommerce_api_aptlearn_wc_gateway_paystack', array($this, 'verify_payment'));
        add_action('woocommerce_cart_calculate_fees', array($this, 'add_paystack_fee'), 20, 1);
        add_action('wp_footer', array($this, 'add_checkout_script'));
    }
public function add_checkout_script() {
    if (is_checkout()) {
        // Localize the Paystack parameters if Paystack is the chosen method
        if ($this->id === WC()->session->get('chosen_payment_method')) {
            wp_localize_script('aptlearn_paystack_custom', 'paystack_params', array(
                'public_key' => $this->public_key,
                'email' => WC()->customer->get_billing_email(),
                'amount' => WC()->cart->get_total('edit') * 100, // Amount in kobo
                'currency' => get_woocommerce_currency(),
                // Additional parameters...
            ));
        }
        ?>
        <script type="text/javascript">
            jQuery(function($) {
                $('form.checkout').on('change', 'input[name="payment_method"]', function() {
                    setTimeout(function() {
                        $(document.body).trigger('update_checkout');
                    }, 250);
                });
            });
        </script>
        <?php
    }
}





    // Initialize Gateway Settings Form Fields
    public function init_form_fields()
    {
        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Enable/Disable', 'woocommerce'),
                'type' => 'checkbox',
                'label' => __('Enable Paystack Payment', 'woocommerce'),
                'default' => 'no'
            ),
            'title' => array(
                'title' => __('Title', 'woocommerce'),
                'type' => 'text',
                'description' => __('This controls the title which the user sees during checkout.', 'woocommerce'),
                'default' => __('Paystack', 'woocommerce'),
                'desc_tip' => true,
            ),
            'description' => array(
                'title' => __('Description', 'woocommerce'),
                'type' => 'textarea',
                'description' => __('This controls the description which the user sees during checkout.', 'woocommerce'),
                'default' => __('Pay with your credit card via Paystack.', 'woocommerce')
            ),
            'enable_live_mode' => array(
                'title' => __('Enable Live Mode', 'woocommerce'),
                'type' => 'checkbox',
                'label' => __('Enable Live Mode', 'woocommerce'),
                'default' => 'no',
                'description' => __('If checked, use the live API keys. If unchecked, use the test API keys. You can get your API keys from the <a href="https://dashboard.paystack.com/#/settings/developers" target="_blank">Paystack settings page</a>.', 'woocommerce'),
            ),
            'test_public_key' => array(
                'title' => __('Test Public Key', 'woocommerce'),
                'type' => 'text',
                'default' => '',
            ),
            'test_secret_key' => array(
                'title' => __('Test Secret Key', 'woocommerce'),
                'type' => 'password',
                'default' => '',
            ),
            'live_public_key' => array(
                'title' => __('Live Public Key', 'woocommerce'),
                'type' => 'text',
                'default' => '',
            ),
            'live_secret_key' => array(
                'title' => __('Live Secret Key', 'woocommerce'),
                'type' => 'password',
                'default' => '',
            ),
           
            'success_url' => array(
                'title' => __('Success', 'woocommerce'),
                'type' => 'text',
                'description' => __('URL to redirect the customer to after a successful payment. For example: https://example.com/payment-success', 'woocommerce'),
                'default' => site_url('/'),
            ),
            'failure_url' => array(
                'title' => __('Failure URL', 'woocommerce'),
                'type' => 'text',
                'description' => __('URL to redirect the customer to after a failed payment. For example: https://example.com/payment-failure', 'woocommerce'),
                'default' => site_url('/'),
            ),
            'order_status' => array(
                'title' => __('Order Status After Payment', 'woocommerce'),
                'type' => 'select',
                'description' => __('Choose what status you want the order to have after the payment is completed.', 'woocommerce'),
                'default' => 'completed',
                'desc_tip' => true,
                'options' => array(
                    'completed' => __('Completed', 'woocommerce'),
                    'processing' => __('Processing', 'woocommerce'),
                ),
            ),
            'enable_transaction_fee' => array(
                'title' => __('Enable Transaction Fee', 'woocommerce'),
                'type' => 'checkbox',
                'label' => __('Enable Transaction Fee', 'woocommerce'),
                'default' => 'no',
                'description' => __('If checked, an additional fee will be added to the order total when the customer checks out with Paystack.', 'woocommerce'),
            ),
            'transaction_fee' => array(
                'title' => __('Transaction Fee', 'woocommerce'),
                'type' => 'number',
                'description' => __('This is the additional fee that will be added to the order total. Enter the amount in NGN.', 'woocommerce'),
                'default' => '100',
                'desc_tip' => true,
            ),
            'callback_url' => array(
                'title' => __('Callback Url (Important)', 'woocommerce'),
                'type' => 'title',
                'description' => __('Callback is used by Paystack to verify and process your order payments: <strong><code>' . add_query_arg('wc-api', 'Aptlearn_WC_Gateway_Paystack', home_url('/')) . '</code></strong>. <a href="https://dashboard.paystack.com/#/settings/developers" target="_blank">You can set it up here</a>.', 'woocommerce'),
            ),
          
            'delete_data' => array(
                'title' => __('Delete Data on Deactivation', 'woocommerce'),
                'type' => 'checkbox',
                'label' => __('Delete Data', 'woocommerce'),
                'default' => 'no',
                'description' => __('If checked, all plugin data (including the payments table) will be deleted when the plugin is deactivated.', 'woocommerce'),
            ),
            'footers_text' => array(
                'title' => __('Plugin Developer', 'woocommerce'),
                'type' => 'title',
                'description' => __('Made with love by <a href="https://akinolaakeem.com" target="_blank">Agba Akin</a>. If you love this plugin, follow or give me a shout on <a href="https://twitter.com/kynsofficial" target="_blank">Twitter</a>.', 'woocommerce'),
            ),
        );
    }

    public function add_paystack_fee()
    {
        global $woocommerce;

        if (is_admin() && !defined('DOING_AJAX')) {
            return;
        }

        // Only when Paystack is selected and we are on the checkout page
        if ($this->id != $woocommerce->session->get('chosen_payment_method') || !is_checkout()) {
            return;
        }

        // Add the fee
        if ($this->enable_transaction_fee) {
            $fee = $this->transaction_fee;
            $woocommerce->cart->add_fee(__('Paystack Processing Fee', 'woocommerce'), $fee);
        }
    }

   public function process_payment($order_id)
    {
        global $woocommerce;

        // Get the order
        $order = wc_get_order($order_id);

        // Add the transaction fee to the total order amount if the feature is enabled
        $total_amount = $order->get_total();
        if ($this->enable_transaction_fee) {
            $total_amount += $this->transaction_fee;
        }

        // Initialize the Paystack object
        $paystack = new Yabacon\Paystack($this->secret_key);

        // Callback URL for verification
        $callback_url = add_query_arg('wc-api', 'Aptlearn_WC_Gateway_Paystack', home_url('/'));

        // Create a new transaction
        try {
            $transaction = $paystack->transaction->initialize([
                'amount' => $total_amount * 100, // Amount in kobo
                'email' => $order->get_billing_email(),
                'callback_url' => $callback_url, // Set the callback URL here
            ]);

            // Save the transaction reference in the order meta
            $order->update_meta_data('paystack_transaction_ref', $transaction->data->reference);
            $order->save();

            // Reduce stock levels
            wc_reduce_stock_levels($order_id);

            // Remove cart
            $woocommerce->cart->empty_cart();

            // Redirect to Paystack for payment (always redirect to Paystack, no popup option)
            return array(
                'result' => 'success',
                'redirect' => $transaction->data->authorization_url
            );
        } catch (Exception $e) {
            wc_add_notice('Payment error: ' . $e->getMessage(), 'error');
            return;
        }
    }

    public function verify_payment() {
        // Check if the request contains the reference
        if (!isset($_GET['reference'])) {
            return;
        }

        $reference = $_GET['reference'];
        $orders = wc_get_orders(['paystack_transaction_ref' => $reference]);
        if (count($orders) == 0) {
            return;
        }
        $order = $orders[0];

        // Initialize the Paystack object
        $paystack = new Yabacon\Paystack($this->secret_key);

        // Verify the transaction
        try {
            $transaction = $paystack->transaction->verify(['reference' => $reference]);

            // Redirect to the success or failure page after verification
            if ($transaction->data->status == 'success') {
                $order->update_status($this->order_status, __('Payment received.', 'woocommerce'));
                wp_redirect($this->success_url); // Redirect to the user-defined success page
            } else {
                $order->update_status('failed', __('Payment failed.', 'woocommerce'));
                wp_redirect($this->failure_url); // Redirect to the user-defined failure page
            }
            exit; // Important to prevent further execution
        } catch (Exception $e) {
            $order->update_status('cancelled', __('Payment error.', 'woocommerce'));
            wp_redirect($this->failure_url); // Redirect to the user-defined failure page
            exit; // Important to prevent further execution
        }
    }

    public function handle_webhook() {
        // Implementation specific to handling bad network issues
        // You may need to consult the Paystack documentation to tailor this to your needs

        // Check if the request contains the reference
        if (!isset($_POST['reference'])) {
            return;
        }

        $reference = $_POST['reference'];
        $orders = wc_get_orders(['paystack_transaction_ref' => $reference]);
        if (count($orders) == 0) {
            return;
        }
        $order = $orders[0];

        // Initialize the Paystack object
        $paystack = new Yabacon\Paystack($this->secret_key);

        // Verify the transaction
        try {
            $transaction = $paystack->transaction->verify(['reference' => $reference]);

            // Perform specific actions for webhook, such as logging, retrying, etc.
            // ...

        } catch (Exception $e) {
            // Handle the exception as needed
        }

        exit; // Important to prevent further execution
    }


}

