<?php
/*
	Plugin Name: Paystation WooCommerce Payment Gateway
	Description: Take credit card payments via Paystation's hosted payment pages.
	Version: 1.1.2
	WC tested up to: 3.3.0
	Author: Paystation Limited
	Author URI: http://www.paystation.co.nz
	License: GPL-2.0+
 	License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

if (!defined('ABSPATH')) {
	exit;
}

add_action('plugins_loaded', 'woocommerce_paystation_init', 0);

function woocommerce_paystation_init() {

	if (!class_exists('WC_Payment_Gateway')) {
		return;
	}

	class WC_Paystation_Threeparty extends WC_Payment_Gateway {

		public function __construct() {
			$this->id = 'threeparty';
			$this->method_title = __('Paystation', 'paystation');
			$this->method_description = __('Paystation allows you to accept credit card payments on your WooCommerce store.', 'paystation');
			$this->order_button_text = __('Proceed to Paystation', 'paystation');
			$this->icon = plugins_url('assets/logo.jpg', __FILE__);
			$this->has_fields = false;

			$this->init_form_fields();
			$this->init_settings();

			$this->title = $this->settings['title'];
			$this->description = $this->settings['description'];
			$this->merchant_id = $this->settings['merchant_id'];
			$this->working_key = $this->settings['working_key'];
			$this->addess_code = $this->settings['addess_code'];
			$this->redirect_page_id = $this->settings['redirect_page_id'];

			$this->return_url = str_replace('https:', 'http:', add_query_arg('wc-api', 'WC_Gateway_Paystation', home_url('/')));
			$this->supports = array('products', 'refunds');

			//WC-api hook for PostBack
			add_action('woocommerce_api_wc_paystation_threeparty', array($this, 'check_threeparty_response'));

			//Hook to call function when the 'Thank you page' is generated - checks the response code
			add_action('woocommerce_thankyou_threeparty', array($this, 'thankyou_page'));

			//Hook called when the checkout page is generated - used to display error message if any
			add_action('before_woocommerce_pay', array($this, 'checkout_page'));
			if (version_compare(WOOCOMMERCE_VERSION, '2.0.0', '>=')) {
				add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
			}
			else {
				add_action('woocommerce_update_options_payment_gateways', array(&$this, 'process_admin_options'));
			}
		}

		function checkout_page($param) {
			//Display paystation error message on the checkout page if the
			//appropriate query string is set
			if (isset($_GET['ec']) && !empty($_GET['ec']) && isset($_GET['em']) && !empty($_GET['em']) && $_GET['ec'] != '0') {
				?>
				<div class='error' style="color: red; font-weight: 700; border-color: red;">
					Your payment failed with the following error message from Paystation Payment Gateway:<br>
					Reason: <?= esc_html($_GET['em']) ?><br>
					Transaction ID: <?= esc_html($_GET['ti']) ?> <br/>
					<br>
				</div>
				<?php
			}
		}

		function thankyou_page($order_id) {
			//This redirects from the 'Thank you page' to the checkout page
			//if the payment failed, adding parameters to the query string so
			//an error message will display on the cart

			$order = wc_get_order($order_id);
			if (strlen($_GET['ec']) < 4 && $_GET['ec'] != '0') {
				wp_redirect($order->get_checkout_payment_url(false) . "&ec=" . urlencode($_GET['ec']) . "&em=" . urlencode($_GET['em']) . "&ti=" . urlencode($_GET['ti']));
				exit();
			}
		}

		function init_form_fields() {
			//Fields to display in admin checkout settings
			$this->form_fields = array(
				'enabled' => array(
					'title' => __('Enable/Disable', 'paystation'),
					'type' => 'checkbox',
					'label' => __('Enable Paystation Payment Module.', 'paystation'),
					'default' => 'no'),
				'title' => array(
					'title' => __('Title:', 'paystation'),
					'type' => 'text',
					'description' => __('This controls the title which the user sees during checkout.', 'paystation'),
					'default' => __('Credit card using Paystation Payment Gateway', 'paystation')),
				'description' => array(
					'title' => __('Description:', 'paystation'),
					'type' => 'textarea',
					'description' => __('This controls the description which the user sees during checkout.', 'paystation'),
					'default' => __('You will be redirected to Paystation Payment Gateway to complete your transaction.', 'paystation')),
				'paystation_id' => array(
					'title' => __('Paystation ID', 'paystation'),
					'type' => 'text',
					'description' => __('Paystation merchant ID given you by Paystation')),
				'gateway_id' => array(
					'title' => __('Gateway ID', 'paystation'),
					'type' => 'text',
					'description' => __('Gateway ID given you by Paystation ', 'paystation')),
				'HMAC_key' => array(
					'title' => __('HMAC key', 'paystation'),
					'type' => 'text',
					'description' => __('HMAC key given you by Paystation ', 'paystation')),
				'test_mode' => array(
					'title' => __('Test mode', 'paystation'),
					'type' => 'checkbox',
					'label' => __('Enable test mode', 'paystation'),
					'default' => 'yes'),
			);
		}

		/**
		 * Admin Panel Options
		 * - Options for bits like 'title' and availability on a country-by-country basis
		 * */
		public function admin_options() {
			echo '<h3>' . __('Paystation Payment Gateway', 'paystation') . '</h3>';
			echo '<p>' . __('New Zealand\'s premier online payment gateway') . '</p>';
			echo '<table class="form-table">';
			$this->generate_settings_html();
			echo '</table>';
		}

		/**
		 *  There are no payment fields for Paystation, but we want to show the description if set.
		 * */
		function payment_fields() {
			if ($this->description) {
				echo wpautop(wptexturize($this->description));
			}
		}

		/**
		 * Receipt Page
		 * */
		function receipt_page($order) {
			echo $this->generate_threeparty_form($order);
		}

		/**
		 * This function is called when the "Place Order" button is clicked
		 * */
		function process_payment($order_id) {
			$order = wc_get_order($order_id);
			$redirect_url = $this->initiate_paystation($order_id, $order);

			return array('result' => 'success', 'redirect' => $redirect_url);
		}

		function process_refund($order_id, $amount = null, $reason = '') {
			$order = wc_get_order($order_id);
			$transactionId = $order->get_transaction_id();

			if ($amount == null) {
				$amount = $order->get_total();
			}

			$amount = $amount * 100;

			$result = $this->processRefund($order_id, $transactionId, $amount);
			if ($result !== true) {
				error_log('An error occurred doing process_refund');
				return false;
			}
			else {
				$refund_message = sprintf(__('Refunded %s via Paystation, on Order: %s', 'p4m'), wc_price($amount / 100), $order_id);
				if ($reason) {
					$refund_message .= 'because ' . $reason;
				}
				$order->add_order_note($refund_message);
				return true;
			}
		}

		/**
		 * Process postback response
		 *
		 */
		function check_threeparty_response() {
			$xml = file_get_contents('php://input');
			$xml = simplexml_load_string($xml);

			if (!empty($xml)) {
				$errorCode = (int) $xml->ec;
				$transactionId = $xml->ti;
				$merchantReference = $xml->merchant_ref;

				$order = wc_get_order((int) wc_clean($merchantReference));

				if (!($order instanceof WC_Order) || !$order->needs_payment()) {
					exit();
				}

				if ($errorCode == 0) {
					echo "payment successful";
					$order->payment_complete((string) esc_html($transactionId));
				}
				else {
					echo "payment failed";
					$order->update_status('failed');
				}
			}

			exit();
		}

		/**
		 * Generate Paystation button link
		 * */
		public function generate_threeparty_form($order_id) {


		}

		/**
		 * @param string $prefix Prepended to the merchant session.
		 * @return string A new and unique merchant session value.
		 */
		public function makePaystationSessionID($prefix = 'woo') {
			return $prefix . '_' . $this->generateRandomString(5) . time();
		}

		public function generateRandomString($length) {
			$token = "";
			$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			$codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
			$codeAlphabet .= "0123456789";
			for ($i = 0; $i < $length; $i++) {
				$token .= $codeAlphabet[rand(0, strlen($codeAlphabet) - 1)];
			}
			return $token;
		}

		function directTransaction($url, $params) {
			$body = $params;

			$args = array(
				'body' => $body,
				'timeout' => '5',
				'redirection' => '5',
				'httpversion' => '1.1',
				'blocking' => true
			);

			$response = wp_remote_post($url, $args);
			return $response['body'];
		}

		function initiate_paystation($order_id, $order) {
			$paystationURL = "https://www.paystation.co.nz/direct/paystation.dll";
			$amount = $order->get_total() * 100;
			$testMode = $this->settings['test_mode'] == 'yes';
			$pstn_pi = trim($this->settings['paystation_id']);
			$pstn_gi = trim($this->settings['gateway_id']);

			$site = ''; // site can be used to differentiate transactions from different websites in admin.
			$pstn_mr = urlencode($order_id);
			$merchantSession = urlencode(time() . '-' . $this->makePaystationSessionID()); // max length of ms is 64 char

			$returnURL = $order->get_checkout_order_received_url();
			$pstn_du = urlencode($returnURL);

			$pstn_cu = $order->get_currency() ? : get_woocommerce_currency();
			$time=time();
			$paystationParams = [
				'paystation' => '_empty',
				'pstn_nr' => 't',
				'pstn_du' => $pstn_du,
				'pstn_dp' => site_url() . '/?wc-api=wc_paystation_threeparty',
				'pstn_pi' => $pstn_pi,
				'pstn_gi' => $pstn_gi,
				'pstn_ms' => $merchantSession,
				'pstn_am' => $amount,
				'pstn_mr' => $pstn_mr,
				'pstn_cu' => $pstn_cu,
				'pstn_fp' => 't',
				'pstn_ft' => "{$pstn_mr}_$time"
			];

			if ($testMode) {
				$paystationParams['pstn_tm'] = 't';
			}

			$paystationParams = http_build_query($paystationParams);

			$hmacGetParams = $this->constructHMACParams($paystationParams);

			$paystationURL .= $hmacGetParams;
			$initiationResult = $this->directTransaction($paystationURL, $paystationParams);

			$retarr = $this->parseXML($initiationResult);

			$url = $retarr['DigitalOrder'];

			return $url;
		}

		function processRefund($order_id, $transactionId, $amount) {
			$paystationURL = "https://www.paystation.co.nz/direct/paystation.dll";
			$testMode = $this->settings['test_mode'] == 'yes';
			$pstn_pi = trim($this->settings['paystation_id']);
			$pstn_gi = trim($this->settings['gateway_id']);

			$pstn_mr = urlencode($order_id);
			$merchantSession = urlencode(time() . '-' . $this->makePaystationSessionID());

			$paystationParams = [
				'paystation' => '_empty',
				'pstn_2p' => 't',
				'pstn_rc' => 't',
				'pstn_nr' => 't',
				'pstn_pi' => $pstn_pi,
				'pstn_gi' => $pstn_gi,
				'pstn_ms' => $merchantSession,
				'pstn_am' => $amount,
				'pstn_mr' => $pstn_mr,
				'pstn_rt' => $transactionId
			];

			if ($testMode) {
				$paystationParams['pstn_tm'] = 't';
			}

			$paystationParams = http_build_query($paystationParams);

			$hmacGetParams = $this->constructHMACParams($paystationParams);

			$result = $this->directTransaction($paystationURL, $paystationParams);

			$retarr = $this->parseXML($result);

			$errorCode = $retarr['PaystationErrorCode'];

			return $errorCode == '0';
		}

		function constructHMACParams($params) {
			$authenticationKey = trim($this->settings['HMAC_key']);
			$hmacWebserviceName = 'paystation';
			$pstn_HMACTimestamp = time();

			$hmacBody = pack('a*', $pstn_HMACTimestamp) . pack('a*', $hmacWebserviceName) . pack('a*', $params);
			$hmacHash = hash_hmac('sha512', $hmacBody, $authenticationKey);

			return '?pstn_HMACTimestamp=' . $pstn_HMACTimestamp . '&pstn_HMAC=' . $hmacHash;
		}

		function parseXML($xml) {
			preg_match_all("/<(.*?)>(.*?)\</", $xml, $outarr, PREG_SET_ORDER);
			$n = 0;
			while (isset($outarr[$n])) {
				$retarr[$outarr[$n][1]] = strip_tags($outarr[$n][0]);
				$n++;
			}

			return $retarr;
		}
	}

	function tbz_paystation_message() {
		$order_id = absint(get_query_var('order-received'));
		$order = wc_get_order($order_id);
		$payment_method = $order->payment_method;

		if (is_order_received_page() && ('tbz_paystation_gateway' == $payment_method)) {
			$paystation_message = get_post_meta($order_id, '_tbz_paystation_message', true);
			$message = $paystation_message['message'];
			$message_type = $paystation_message['message_type'];

			delete_post_meta($order_id, '_tbz_paystation_message');

			if (!empty($paystation_message)) {
				wc_add_notice($message, $message_type);
			}
		}
	}

	add_action('wp', 'tbz_paystation_message');


	/**
	 * Add Paystation Gateway to WC
	 **/
	function woocommerce_add_paystation_gateway($methods) {
		$methods[] = 'WC_Paystation_ThreeParty';
		return $methods;
	}

	add_filter('woocommerce_payment_gateways', 'woocommerce_add_paystation_gateway');

	function tbz_paystation_plugin_action_links($links, $file) {
		static $this_plugin;

		if (!$this_plugin) {
			$this_plugin = plugin_basename(__FILE__);
		}
		$settings_link = '<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout&section=threeparty') . '">Settings</a>';
		if ($file == $this_plugin) {
			array_unshift($links, $settings_link);
		}
		return $links;
	}

	add_filter('plugin_action_links', 'tbz_paystation_plugin_action_links', 10, 2);
}