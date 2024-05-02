 === Paystation WooCommerce Payment Gateway ===
Contributors: paystationNZ
Tags: credit card, paystation, woocommerce, payment gateway, payment, gateway, credit, card, e-commerce, ecommerce, new zealand, nz, 
Requires at least: 4.1
Tested up to: 6.1
Stable tag: 1.2.3
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Take credit card payments on your store via Paystation.

== Description ==

Accept credit card payments with [Paystation](http://www.paystation.co.nz)

With our secure hosted payment pages you can take payments via multiple card types 

* VISA
* Mastercard
* American Express
* Diners Club
* Q Card
* POLi
* MasterPass
* UnionPay

= Requirements: =

* An account with [Paystation](http://www.paystation.co.nz/signup).

= Installation =

1. From the WooCommerce menu on the admin menu, select the 'Settings' link.
2. Select 'Checkout' from the tab accross the top menu bar.
3. Scroll down to the Payment Gateways section, and click the Paystation payment method titled 'Credit card using Paystation Payment Gateway'.
4. Click 'Enable Paystation Payment Module' checkbox to turn on plugin.
5. Enter Paystation Id as provided by Paystation.
6. Enter Gateway Id as provided by Paystation.
7. Enter HMAC key as provided by Paystation
8. Ensure the 'Enable test mode' box is checked
9. Click the 'Save changes' button at the bottom of the screen. The message "Your settings have been saved" should appear near the top of the screen..
10. Email support@paystation.co.nz letting us know the following: Your Paystation ID, Gateway ID, and that you are using the Paystation WooCommerce plugin and the URL to your website.

= Testing Payments =

1. Ensure your Paystation settings have 'Enable test mode' box ticked as above.
2. Navigate to your storefront.
3. Set up a product with a a cent value of of .00 or .54 cents for successful or unsuccessful transaction testing respectively.
4. Add product to cart and proceed to the checkout screen.
5. Select Paystation credit card payments as payment method and continue.
6. Follow through hosted payment form with one of our VISA or Mastercard [test cards](http://www.paystation.co.nz/for-developers/test-cards/)
7. Upon completion you'll be redirected back to your store with an successful/unsuccessful payment message.

= Taking Live Credit Payments =

Once the site is working as expected you will need to fill in the [Go live] (http://www.paystation.co.nz/golive) form so that Paystation can test and set your account into Production Mode.

After Paystation have confirmed the Paystation account is live, go back to the Woocommerce checkout settings, and uncheck the 'Enable test mode' box in the Paystation method settings

Congratulations - you're now setup to take credit card Payments!

== Frequently Asked Questions ==

= What is Paystation (3 party hosted)? =

Paystation (3 party hosted) is a Credit Card payment gateway. Paystation is one of New Zealand's leading online payments solution providers.

= Where do I find the Paystation transaction number? =

Successful transaction details including the Paystation transaction number are shown at the top of the screen when you view the details of a Woocommerce order in WordPress admin.
Details of the payment also appear in the Paystation Admin website, which you as Paystation customer have access to.

=  Where can I find dummy Credit Card details for testing purposes? =

[Visit the Test Card Number page](http://www.paystation.co.nz/for-developers/test-cards/)

== Changelog ==
= 1.2.3 [2022-11-01] =
* Update supported Wordpress version

= 1.2.2 [2022-10-25] =
* Fixed typecasting of error code for postback

= 1.2.1 [2022-06-03] =
* Improved error logging

= 1.2 [2022-06-03] =
* Update supported Wordpress version
* Fixed error on successful refund
* Added: Order notes on failed refund containing error details with error code

= 1.1.7 [2022-11-04] =
* Change to fixed width logo

= 1.1.6 [2021-16-06] =
* Support for Sequential order numbers

= 1.1.5 [2021-30-04] =
* Update supported Wordpress version

= 1.1.4 [2020-01-07] =
* Fixed image asset path 

= 1.1.3 [2019-11-12] =
* Fixed image directory path not loading logo

= 1.1.2 [2018-24-04] =
* Fixed image directory path not loading logo

= 1.1.1 [2018-22-02] =
* Fixed paystation logo image on the checkout page
* Fixed 'Not tested with the active version of WooCommerce' message

= 1.1.0 [2018-01-02] =
* Added multi currency support
* Fixed merchant reference on refunds

= 1.0.0 [2017-29-11] =
* Original Version.