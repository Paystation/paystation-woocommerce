# Paystation payment plugin for WooCommerce

This integration is currently only tested up to Wordpress 5.3.0 with WooCommerce 3.8.1

## Requirements
* An account with [Paystation](https://www2.paystation.co.nz/)
* An HMAC key for your Paystation account, contact our support team if you do not already have this <support@paystation.co.nz>

## Installation

These instructions will guide you through installing the module.

We recommend this plugin be installed through Wordpress's plugin manager. 

You can install this plugin by following this [guide](https://wordpress.org/support/article/managing-plugins/#automatic-plugin-installation) and searching for `Paystation WooCommerce Payment Gateway`

1. From the WooCommerce menu on the admin menu, select the `Settings` link.
2. Select `Checkout` from the tab accross the top menu bar.
3. Scroll down to the Payment Gateways section, and click the Paystation payment method titled `Credit card using Paystation Payment Gateway`.
4. Click `Enable Paystation Payment Module` checkbox to turn on plugin.
5. Enter Paystation Id as provided by Paystation.
6. Enter Gateway Id as provided by Paystation.
7. Enter HMAC key as provided by Paystation
8. Ensure the `Enable test mode` box is checked
9. Click the `Save changes` button at the bottom of the screen. The message `Your settings have been saved` should appear near the top of the screen.
10. Email support@paystation.co.nz letting us know the following: Your Paystation ID, Gateway ID, and that you are using the Paystation WooCommerce plugin and the URL to your website.

## Testing Payments

1. Ensure your Paystation settings have `Enable test mode` box ticked as above.
2. Navigate to your storefront.
3. Set up a product with a a cent value of of .00 or .54 cents for successful or unsuccessful transaction testing respectively.
4. Add product to cart and proceed to the checkout screen.
5. Select Paystation credit card payments as payment method and continue.
6. Follow through hosted payment form with one of our VISA or Mastercard [test cards](https://www2.paystation.co.nz/for-developers/test-cards/)
7. Upon completion you'll be redirected back to your store with an successful/unsuccessful payment message.

## Taking Live Credit Payments

Once the site is working as expected you will need to fill in the [Go live] (http://www.paystation.co.nz/golive) form so that Paystation can test and set your account into Production Mode.

After Paystation have confirmed the Paystation account is live, go back to the Woocommerce checkout settings, and uncheck the 'Enable test mode' box in the Paystation method settings

Congratulations - you're now setup to take credit card Payments!
