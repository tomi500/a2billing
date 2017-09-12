<?php

//require_once('./lib/epayment/includes/methods/webmoney.php');
define('MODULE_PAYMENT_WM_TEXT_TITLE', 'WebMoney');
define('MODULE_PAYMENT_WM_TEXT_DESCRIPTION', 'WebMoney');
define('MODULE_PAYMENT_WM_LMI_PAYMENT_DESC', 'Connection service');

class webmoney {
	var $code, $title, $description, $enabled, $current_wm_currency;

	function webmoney() {
		global $order;

		$this->code = 'webmoney';
		$this->title = MODULE_PAYMENT_WM_TEXT_TITLE;
		$this->description = MODULE_PAYMENT_WM_TEXT_DESCRIPTION;
		$this->sort_order = 1;
		$this->enabled = ((MODULE_PAYMENT_WM_STATUS == 'True') ? true : false);

		$this->form_action_url = 'https://merchant.webmoney.ru/lmi/payment.asp';
	}

	function keys() {
		return array(
				  'MODULE_PAYMENT_WM_STATUS',		'MODULE_PAYMENT_WM_WMID'
				, 'MODULE_PAYMENT_WM_PURSE_WMU',	'MODULE_PAYMENT_WM_PURSE_WME'
				, 'MODULE_PAYMENT_WM_PURSE_WMR',	'MODULE_PAYMENT_WM_PURSE_WMZ'
				, 'MODULE_PAYMENT_WM_CACERT',		'MODULE_PAYMENT_WM_LMI_SECRET_KEY'
				, 'MODULE_PAYMENT_WM_LMI_SIM_MODE',	'MODULE_PAYMENT_WM_LMI_HASH_METHOD'
//				, 'MODULE_PAYMENT_WM_WMSIGNER_PATH'
				);
	}

	function javascript_validation() {
		return false;
	}

	function selection() {

		
		   global $order;
		   if (MODULE_PAYMENT_WM_PURSE_WME) $purse_type[] = array('id' => 'WME', 'text' => 'WME Euro (EUR)');
		   if (MODULE_PAYMENT_WM_PURSE_WMZ) $purse_type[] = array('id' => 'WMZ', 'text' => 'WMZ U.S. Dollar (USD)');
		   if (MODULE_PAYMENT_WM_PURSE_WMU) $purse_type[] = array('id' => 'WMU', 'text' => 'WMU Ukraine Hryvnia (UAH)');
		   if (MODULE_PAYMENT_WM_PURSE_WMR) $purse_type[] = array('id' => 'WMR', 'text' => 'WMR Russian Rouble (RUB)');

		   $selection = array(
		   'id' => $this->code,
		   'module' => $this->title,
		   'fields' => array(array('title' => 'Choose type', 'field' => tep_draw_pull_down_menu('wm_purse_type', $purse_type)))
		   );

		   return $selection;
		
		//return array('id' => $this->code, 'module' => $this->title);
	}

	function get_CurrentCurrency()
	{
		switch($_POST['wm_purse_type'])
		{
			case 'WMU': $getcur = "UAH"; break;
			case 'WMZ': $getcur = "USD"; break;
			case 'WME': $getcur = "EUR"; break;
			case 'WMR': $getcur = "RUB"; break;
			default   : $getcur = ""; break;
		}
		   return $getcur;
	}

	function pre_confirmation_check() {
		return false;
	}

	function confirmation() {
		return false;
	}

	function process_button($transactionID = 0, $key= "") {
		global $order, $currencies, $currency;

		$process_button_string = tep_draw_hidden_field('LMI_PAYMENT_AMOUNT', $order->info['total']);
		$process_button_string .= tep_draw_hidden_field('LMI_PAYMENT_DESC', MODULE_PAYMENT_WM_LMI_PAYMENT_DESC);
//		$process_button_string .= tep_draw_hidden_field('LMI_PAYMENT_NO', '1');

		if ($_POST['wm_purse_type'] == 'WMU')
		   $process_button_string .= tep_draw_hidden_field('LMI_PAYEE_PURSE', MODULE_PAYMENT_WM_PURSE_WMU);
		elseif ($_POST['wm_purse_type'] == 'WMZ')
		   $process_button_string .= tep_draw_hidden_field('LMI_PAYEE_PURSE', MODULE_PAYMENT_WM_PURSE_WMZ);
		elseif ($_POST['wm_purse_type'] == 'WME')
		   $process_button_string .= tep_draw_hidden_field('LMI_PAYEE_PURSE', MODULE_PAYMENT_WM_PURSE_WME);
		elseif ($_POST['wm_purse_type'] == 'WMR')
		   $process_button_string .= tep_draw_hidden_field('LMI_PAYEE_PURSE', MODULE_PAYMENT_WM_PURSE_WMR);

		$process_button_string .= tep_draw_hidden_field('LMI_SIM_MODE', MODULE_PAYMENT_WM_LMI_SIM_MODE);

		$process_button_string .= tep_draw_hidden_field('LMI_SUCCESS_URL', tep_href_link("userinfo.php", '', 'SSL'));
//		$process_button_string .= tep_draw_hidden_field('LMI_SUCCESS_URL', tep_href_link("checkout_success.php", '', 'SSL'));
		$process_button_string .= tep_draw_hidden_field('LMI_FAIL_URL', tep_href_link("checkout_payment.php", '', 'SSL'));

		$process_button_string .= tep_draw_hidden_field('LMI_RESULT_URL', tep_href_link("checkout_process.php", '', 'SSL'));

		$process_button_string .= tep_draw_hidden_field('transactionID',$transactionID);
		$process_button_string .= tep_draw_hidden_field('sess_id', session_id());
		$process_button_string .= tep_draw_hidden_field('key', $key);

		/*For debug only*/
		//write_log(LOGFILE_EPAYMENT, basename(__FILE__).' - WM URL'.$process_button_string);
		return $process_button_string;
	}

	function get_OrderStatus()
	{
		//Если есть номер платежа в системе WebMoney Transfer, выполненный в процессе обработки

		if ($_POST['LMI_SYS_TRANS_NO'] != ""){
			return 2; //то возвращаем статус завершенного успешно
		}
		else { 
			return -2; //иначе статус Failed
		}
	}

	function before_process() {
		return false;
	}

	function after_process() {
		return false;
	}

}


?>

