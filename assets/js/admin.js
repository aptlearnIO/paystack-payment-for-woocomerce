jQuery(document).ready(function($) {
    togglePaystackApiKeys();

    $('#woocommerce_aptlearn_paystack_enable_live_mode').change(function() {
        togglePaystackApiKeys();
    });

    function togglePaystackApiKeys() {
        var isLiveMode = $('#woocommerce_aptlearn_paystack_enable_live_mode').is(':checked');

        if (isLiveMode) {
            $('#woocommerce_aptlearn_paystack_test_public_key, #woocommerce_aptlearn_paystack_test_secret_key').closest('tr').hide();
            $('#woocommerce_aptlearn_paystack_live_public_key, #woocommerce_aptlearn_paystack_live_secret_key').closest('tr').show();
        } else {
            $('#woocommerce_aptlearn_paystack_test_public_key, #woocommerce_aptlearn_paystack_test_secret_key').closest('tr').show();
            $('#woocommerce_aptlearn_paystack_live_public_key, #woocommerce_aptlearn_paystack_live_secret_key').closest('tr').hide();
        }
    }
});
