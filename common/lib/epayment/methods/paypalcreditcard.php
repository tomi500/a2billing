<?php
include(dirname(__FILE__).'/../includes/methods/paypal.php');

class paypalcreditcard {
    var $code, $title, $description, $enabled;
    var $paypal_allowed_currencies;

	// class constructorform_action_url
    public function __construct() {
		global $user_paypal;

		$this->title = MODULE_PAYMENT_PAYPAL_TEXT_TITLE;
		$this->description = MODULE_PAYMENT_PAYPAL_TEXT_DESCRIPTION;
		$this->code = 'paypalcreditcard';
		$this->sort_order = 1;
		$this->enabled = ((MODULE_PAYMENT_PAYPAL_STATUS == 'True' && $user_paypal) ? true : false);
		//$this->enabled = true;

		$this->form_action_url = PAYPAL_PAYMENT_URL;
		$this->paypal_allowed_currencies = explode(', ', MODULE_PAYMENT_PAYPAL_CURRENCY);
    }

    public function keys() {
		return array(
			'MODULE_PAYMENT_PAYPAL_STATUS', 	'MODULE_PAYMENT_PAYPAL_ID',
			'MODULE_PAYMENT_PAYPAL_USER',		'MODULE_PAYMENT_PAYPAL_PWD',
			'MODULE_PAYMENT_PAYPAL_SIGNATURE',	'MODULE_PAYMENT_PAYPAL_CURRENCY'
		);
    }
	// class methods
    public function update_status() {
		global $order;

		if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_PAYPAL_ZONE > 0) ) {
			$check_flag = false;
			$check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_PAYPAL_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
			while ($check = tep_db_fetch_array($check_query)) {
				if ($check['zone_id'] < 1) {
					$check_flag = true;
					break;
				} elseif ($check['zone_id'] == $order->billing['zone_id']) {
					$check_flag = true;
					break;
				}
			}

			if ($check_flag == false) {
				$this->enabled = false;
			}
		}
    }

    public function javascript_validation() {
		return false;
    }

    public function selection() {
		foreach ($this->paypal_allowed_currencies as $curselected) {
			$purse_type[] = array('id' => $curselected, 'text' => $curselected);
		}
		$selection = array(
		   'id' => $this->code,
		   'module' => $this->title,
		   'fields' => array(array('title' => 'Choose type', 'field' => tep_draw_pull_down_menu('wm_purse_type', $purse_type)))
		   );
		return $selection;
    }

    public function pre_confirmation_check() {
		return false;
    }

    public function confirmation() {
		return false;
    }

    public function process_button($transactionID = 0, $key= "") {
		global $order, $currencies, $currency;

		$my_currency = $_POST['wm_purse_type'];

		if (!in_array($my_currency, $this->paypal_allowed_currencies)) {
			$my_currency = BASE_CURRENCY;
		}
		$currencyObject = new currencies();
		$process_button_string =
					 tep_draw_hidden_field('METHOD', 'SetExpressCheckout') .
					 tep_draw_hidden_field('PAYMENTREQUEST_0_CURRENCYCODE', $my_currency) .
					 tep_draw_hidden_field('PAYMENTREQUEST_0_ITEMAMT', number_format($order->info['total'], $currencyObject->get_decimal_places($my_currency))) .
					 tep_draw_hidden_field('PAYMENTREQUEST_0_AMT', number_format($order->info['total'], $currencyObject->get_decimal_places($my_currency))) .
					 tep_draw_hidden_field('RETURNURL', tep_href_link("userinfo", '', 'SSL')) .
					 tep_draw_hidden_field('CANCELURL', tep_href_link("checkout_payment", '', 'SSL')) .
					 tep_draw_hidden_field('SOLUTIONTYPE', 'Sole') .
					 tep_draw_hidden_field('LANDINGPAGE', 'Billing') .
					 tep_draw_hidden_field('NO_SHIPPING', '1') .
					 tep_draw_hidden_field('ALLOWNOTE', '0') .
					 tep_draw_hidden_field('LOCALECODE', LANG) .
					 tep_draw_hidden_field('ADDROVERRIDE', '0');
/**					 tep_draw_hidden_field('cmd', '_xclick') .
					 tep_draw_hidden_field('business', MODULE_PAYMENT_PAYPAL_ID) .
					 tep_draw_hidden_field('item_name', gettext('Payment for ').STORE_NAME) .
					 tep_draw_hidden_field('rm', '2') .
//					 tep_draw_hidden_field('bn', 'Credits_BuyNow_WPS_'.substr($order->customer['country'],0,2)) .
//					 tep_draw_hidden_field('country', substr($order->customer['country'],0,2)) .
					 tep_draw_hidden_field('lc', LANG) .
					 tep_draw_hidden_field('charset', 'UTF-8') .
					 tep_draw_hidden_field('email', $order->customer['email_address']) .
					 tep_draw_hidden_field('no_shipping', '1') .
					 tep_draw_hidden_field('PHPSESSID', session_id()) .
					 tep_draw_hidden_field('amount', number_format($order->info['total'], $currencyObject->get_decimal_places($my_currency))) .
//					 tep_draw_hidden_field('shipping', number_format($order->info['shipping_cost'] * $currencyObject->get_value($my_currency), $currencyObject->get_decimal_places($my_currency))) .
					 tep_draw_hidden_field('currency_code', $my_currency) .
					 tep_draw_hidden_field('notify_url', tep_href_link("checkout_process.php?transactionID=".$transactionID."&sess_id=".session_id()."&key=".$key, '', 'SSL')) .
					 tep_draw_hidden_field('return', tep_href_link("userinfo", '', 'SSL')) .
					 tep_draw_hidden_field('cbt', gettext('Return to ').STORE_NAME) .
					 tep_draw_hidden_field('cancel_return', tep_href_link("userinfo", '', 'SSL'));
**/

		return $process_button_string;
    }
    public function get_CurrentCurrency() {
		$getcur = $_POST['wm_purse_type'];
		if (!in_array($getcur, $this->paypal_allowed_currencies))
			$getcur = BASE_CURRENCY;
		return $getcur;
    }
    public function before_process() {
		return false;
    }

    public function get_OrderStatus()
    {
        if ($_POST['payment_status']=="")
        {
            return -2;
        }
        switch ($_POST['payment_status']) {
            case "Failed":
                return -2;
            break;
            case "Denied":
                return -1;
            break;
            case "Pending":
                return -0;
            break;
            case "In-Progress":
                return 1;
            break;
            case "Completed":
                return 2;
            break;
            case "Processed":
                return 3;
            break;
            case "Refunded":
                return 4;
            break;
            default:
                return 5;
        }
    }
    public function after_process() {
		return false;
    }

    public function output_error() {
		return false;
    }

}
