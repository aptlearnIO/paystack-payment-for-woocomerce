# Paystack Gateway for WooCommerce

Welcome to the GitHub repository for the **Paystack Gateway for WooCommerce** plugin! This plugin, developed by [aptLearn](https://aptlearn.io/), provides a simple, secure, and efficient way of integrating the Paystack payment gateway into your WooCommerce store. By using this plugin, you can offer your customers a seamless checkout experience, allowing them to pay with their credit cards via Paystack.

## :sparkles: Features

- **Easy Setup**: Configure your Paystack settings directly from your WooCommerce settings page.
- **Transaction Fees**: Optionally add a transaction fee for customers who choose to pay via Paystack.
- **Live and Test Modes**: Switch between live and test modes for your Paystack integration.
- **Order Status Selection**: Choose the order status after a successful transaction.
- **Redirect URLs**: Set custom URLs to redirect your customers to after successful or failed payments.

## :gear: Installation

1. Download the plugin from this repository.
2. Navigate to the **Plugins > Add New** section from your WordPress admin dashboard.
3. Click on the **Upload Plugin** button and choose the downloaded file, then click on **Install Now**.
4. Once the plugin is installed, click **Activate** to start using the Paystack Gateway for WooCommerce plugin.

## :wrench: Configuration

After activating the plugin, navigate to **WooCommerce > Settings > Payments > Paystack** to configure the plugin. Here you can enable/disable the plugin, enter your Paystack API keys, set the success and failure URLs, enable transaction fees, and more.

## :framed_picture: Screenshots

![Screenshot 1](https://aptlearn.io/wp-content/uploads/2023/08/Screenshot-2023-08-11-at-1.35.19-AM.png)
![Screenshot 2](https://aptlearn.io/wp-content/uploads/2023/08/Screenshot-2023-08-08-at-10.39.27-PM.png)
![Screenshot 3](https://aptlearn.io/wp-content/uploads/2023/08/Screenshot-2023-08-08-at-10.40.08-PM.png)

## :telephone_receiver: Support

For support, feature requests, or bug reports, please open an issue here on GitHub.

## :handshake: Contributions

Contributions are welcome from everyone. If you'd like to contribute, please fork the master branch, make your changes, and then submit a pull request.

## :scroll: License

This project is licensed under the GNU General Public License v2.0.

## :man_technologist: Developer Guide

This plugin is built in a modular fashion to promote readability, maintainability, and extensibility. It uses the [Paystack PHP library](https://github.com/Yabacon/paystack-php) to interact with the Paystack API.

### Key Classes and Functions

**Aptlearn_WC_Gateway_Paystack:** This is the main class of the plugin, which extends the `WC_Payment_Gateway` class from WooCommerce. This class handles the bulk of the plugin functionality, including initializing settings, processing payments, and verifying transactions.

**init_form_fields:** This function sets up the form fields that appear in the WooCommerce settings page under the Paystack section.

**process_payment:** This function is triggered when a customer chooses to pay via Paystack. It uses the Paystack PHP library to initialize a new transaction, then redirects the customer to the Paystack payment page.

**verify_payment:** This function is called after the customer completes the payment on the Paystack page. It verifies the transaction with Paystack and updates the order status accordingly.

## Changelog

### Version 1.0 (Initial Release)
- Initial release of the plugin.

### Version 1.1
- Properly handle fatal errors when WooCommerce is not activated.
- Fix issues with payment status updates.
- Implement admin options for payment completion on site or Paystack's.

### Extending the Plugin

You can extend the functionality of this plugin by hooking into various actions and filters provided by WooCommerce. For instance, you can add additional form fields to the settings page, modify the behavior of the payment process, or add new features like support for more payment methods.

Please note that any modifications to the code should be done in a child theme or a separate plugin to prevent your changes from being overwritten when updating the plugin.

### Contribution

Contributions are welcome from everyone. If you'd like to contribute, please fork the master branch, make your changes, and then submit a pull request.

For more detailed information about the code, please refer to the inline comments in the PHP files.

## :clap: Credits

This plugin is developed and maintained by [aptLearn](https://aptlearn.io/). If you love this plugin, follow or give our engineering lead a shout on [Twitter](https://twitter.com/kynsofficial).
