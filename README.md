# Paystation payment plugin for WooCommerce

This integration is currently tested up to WordPress 6.5.3 with WooCommerce 8.8.3

## Requirements
* An account with [Paystation](https://paystation.co.nz/pricing/)
* An HMAC key for your Paystation account, contact our support team [via email](mailto:support@paystation.co.nz) if you do not already have this.

## Installation

These instructions will guide you through installing the module.

We recommend this plugin be installed through Wordpress's plugin manager. 

You can install this plugin [here](https://wordpress.org/plugins/paystation-woocommerce-payment-gateway/).

1. From the WooCommerce menu on the admin menu, select the `Settings` link.
2. Select `Payments` from the top menu bar.
3. Scroll down to the Paystation payment method and click `Manage` for using Paystation Payment Gateway.
4. Click `Enable Paystation Payment Module` checkbox to turn on plugin.
5. Enter Paystation Id as provided by Paystation.
6. Enter Gateway Id as provided by Paystation.
7. Enter HMAC key as provided by Paystation
8. Select the `Enable test mode` box if required.
9. Click `Save changes` button.
10. Email our support team [via email](mailto:support@paystation.co.nz) if you have any issues with the below details:
    a) Your Paystation ID, 
    b) Gateway ID,
    c) confirming that you are using the Paystation WooCommerce plugin, 
    d) the website link.

## Testing Payments

1. Ensure your Paystation settings have `Enable test mode` box ticked as above.
2. Make sure that you have at least one product added to your store.
3. Set up a product with any amount .00 cents or 1.00 dollar to check successful or unsuccessful transaction testing respectively.
4. Add product to cart and proceed to the checkout screen.
5. Select Paystation credit card payments as payment method and continue.
6. Fill the test card details of hosted payment form with one of our VISA or Mastercard [test cards](https://paystation.co.nz/developers/test-cards/)
7. Upon successful transaction orders will be shown at your website backend store.

## Taking Live Credit Payments

Once the site is working as expected you will need to fill in the [Go live](https://paystation.co.nz/golive) form so that Paystation can test and set your account into Production Mode.

Your account will be confirmed by Paystation when it's live, and after that you need to go back to the Woocommerce checkout settings, and uncheck the 'Enable test mode' box in the Paystation method settings.

Congratulations - you're now setup to take credit card Payments!