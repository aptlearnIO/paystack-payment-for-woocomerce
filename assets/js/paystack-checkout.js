jQuery(function ($) {
    var paystackHandler = PaystackPop.setup({
        key: paystack_params.public_key,
        email: paystack_params.email,
        amount: paystack_params.amount,
        currency: paystack_params.currency,
        // Additional parameters...
        callback: function (response) {
            // Handle successful payment
        },
        onClose: function () {
            // Handle closing of the popup
        }
    });

    // Trigger the Paystack popup when needed (e.g., on a specific button click)
    $('#pay-now').on('click', function (e) {
        e.preventDefault();
        paystackHandler.openIframe();
    });

    $('form.checkout').on('change', 'input[name="payment_method"]', function () {
        setTimeout(function () {
            $(document.body).trigger('update_checkout');
        }, 250);
    });

    // Additional code...
});
