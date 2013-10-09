<?php
$mosConfig_live_site = 'https://customer.sipde.net';
// Global
$moduleclass_sfx = '';
$pretext = 'Благодарность Автору';
$btntxt = 'Donate';
// Webmoney
$use_wm = 1;
$wmz = 'Z419639909455';
$wme = 'E241616677453';
$wmr = 'R367908670622';
$wmu = 'U188972105659';
$wm_summ = '10';
$wm_successurl = $mosConfig_live_site;
$wm_errorurl = $mosConfig_live_site;
$wm_descpay = 'Donate Author';
// Yandex
$use_yandex = 0;
$yandex = '01234567891011';
$yandex_summ = '50';
$yandex_successurl = $mosConfig_live_site;
// PayPal
$use_paypal = 1;
//$donate_email = '4935XK7M8RJQY';
$donate_email = 'VBP4BCSYDMWD6';
$paypalcur_on = 0;
$paypalcur_val = 'EUR';
$paypalval_on = 0;
$paypalval_val = 10;
$paypalvalleast_val = 5;
$donate_org = 'Donate Author';
$donate_len = 1;
$paypallen_val = 4;
$link_cancel = $mosConfig_live_site;
$link_return = $mosConfig_live_site;
//////////////////////////////////////////////////////////////////////////////
if ($wmz != '') {
    $wmtype1 = 'WMZ';
    $wmnum1 = $wmz;
}
if ($wme != '') {
    $wmtype2 = 'WME';
    $wmnum2 = $wme;
}
if ($wmr != '') {
    $wmtype3 = 'WMR';
    $wmnum3 = $wmr;
	 }
if ($wmu != '') {
    $wmtype4 = 'WMU';
    $wmnum4 = $wmu;
	 }
$logowm = $mosConfig_live_site.'/templates/default/images/kicons/logowm.gif';
$logoyandex = $mosConfig_live_site.'/templates/default/images/kicons/logoyandex.gif';
$logopaypal = $mosConfig_live_site.'/templates/default/images/kicons/logopaypal.gif';
$logowm_sm = $mosConfig_live_site.'/templates/default/images/kicons/logowm_small.gif';
$logoyandex_sm = $mosConfig_live_site.'/templates/default/images/kicons/logoyandex_small.gif';
$logopaypal_sm = $mosConfig_live_site.'/templates/default/images/kicons/logopaypal_small.gif';
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Donate for Nixon</title>
<meta name="description" content="Development for A2Billing" />
<meta name="keywords" content="A2Billing,API,Asterisk,Termination,IP-PBX,PBX" />
<meta name="robots" content="index, follow" />
<base href="https://customer.sipde.net/" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<style type="text/css">
#moduletable_donate {
	position: absolute;
	left: 50%;
	top: 50%;
	height: 220px;
	width: 250px;
	margin-top: -110px;
	margin-left: -125px;
	border:1px solid #CCC;
	z-index: 1;
}
#wm, #yandex, #paypal {
	width:97%;
	padding:3px;
	margin:3px;
}
#rules {
	width:100%;
	text-decoration: none;
}
#wm, #yandex, #paypal {
	height:150px;
	height:150px !important;
}
#poweredby {
	text-align:center;
	font-family:Verdana, Arial, Helvetica, sans-serif;
	font-size:9px;
	margin:3px;
}
.money {
	padding:2px;
	margin:1px;
}
</style>
<script type="text/javascript" src="./javascript/jquery/jquery-1.2.6.min.js"></script>
<script type="text/javascript">
	function show_wm()
	{
		var $j = jQuery.noConflict();
		$j('#yandex').hide();
		$j('#paypal').hide();
		$j('#wm').fadeIn(1500);
		return false;
	};
	function show_yandex()
	{
		var $j = jQuery.noConflict();
		$j('#wm').hide();
		$j('#paypal').hide();
		$j('#yandex').fadeIn(1500);
		return false;
	};
	function show_paypal()
	{
		var $j = jQuery.noConflict();
		$j('#wm').hide();
		$j('#yandex').hide();
		$j('#paypal').fadeIn(1500);
		return false;
	};
	function hide_all()
	{
		var $j = jQuery.noConflict();
		$j('#wm').hide();
		$j('#yandex').hide();
		$j('#paypal').hide();
		return false;
	}
</script>
</head>
<body>
<div id="moduletable_donate">
<!--------------------------- Panel --------------------------->
<div id="rulez" align="center">
	<?php
	if ($pretext != '')
	{?>
		<span style="font-weight:bold; cursor:pointer; border-bottom:1px solid #CCC; padding:3px;" onclick="hide_all()" title="Hide all">
			<?php echo $pretext;?>
		</span><br/><br/>
	<?php
	}?>
	<?php
	if ($use_wm)
	{?>
		<a href="javascript:void(0);" onclick="show_wm()" title="Webmoney">
			<img src="<?php echo $logowm_sm;?>" alt="Webmoney" border="0" />
		</a>
	<?php
	}?>
	<?php
	if ($use_yandex)
	{?>
		<a href="javascript:void(0);" onclick="show_yandex()" title="Yandex">
			<img src="<?php echo $logoyandex_sm;?>" alt="Yandex" border="0" />
		</a>
	<?php
	}?>
	<?php
	if ($use_paypal)
	{?>
		<a href="javascript:void(0);" onclick="show_paypal()" title="PayPal">
			<img src="<?php echo $logopaypal_sm;?>" alt="PayPal" border="0" />
		</a>
	<?php
	}?>
</div>
<!--------------------------- Webmoney --------------------------->
<div id="wm" align="center" style="display:none;">
	<form id="pay" name="pay" method="POST" action="https://merchant.webmoney.ru/lmi/payment.asp">
		<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td width="100%" align="center">
					<img src="<?php echo $logowm;?>" alt="" title="" border="0" />
				</td>
			</tr>
			<tr>
				<td width="100%" align="center">
					Currency/amount:
				</td>
			</tr>
			<tr>
				<td width="100%" align="center">
					<input name="LMI_PAYMENT_AMOUNT" type="text" size="3" value="<?php echo $wm_summ;?>">
					<input type="hidden" name="LMI_PAYMENT_DESC" value="<?php echo $wm_descpay;?>">
					<input type="hidden" name="LMI_SIM_MODE" value="0">
					<input type="hidden" name="LMI_SUCCESS_URL" value="<?php echo $wm_successurl;?>">
					<input type="hidden" name="LMI_SUCCESS_METHOD" value="2">
					<input type="hidden" name="LMI_FAIL_URL" value="<?php echo $wm_errorurl;?>">
					<input type="hidden" name="LMI_FAIL_METHOD" value="2">
					<select name="LMI_PAYEE_PURSE" style="min-width:30px;">
						<option value="<?php echo $wmnum1;?>"><?php echo $wmtype1;?></option>
						<option value="<?php echo $wmnum2;?>"><?php echo $wmtype2;?></option>
						<option value="<?php echo $wmnum3;?>"><?php echo $wmtype3;?></option>
						<option value="<?php echo $wmnum4;?>"><?php echo $wmtype4;?></option>
					</select>
					<input type="hidden" name="sess_id" value="1">
					<input type="hidden" name="transactionID" value="1">
				</td>
			</tr>
			<tr>
				<td width="100%" align="center">
					<br>
					<input type="submit" class="button" value="<?php echo $btntxt;?>">
				</td>
			</tr>
		</table>
	</form>
</div>
<!--------------------------- Yandex --------------------------->
<div id="yandex" align="center" style="display:none;">
	<form id="yandex_pay" name="yandex_pay" method="POST" action="https://money.yandex.ru/charity.xml">
		<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td width="100%" align="center">
					<img src="<?php echo $logoyandex;?>" alt="" title="" border="0" />
				</td>
			</tr>
			<tr>
				<td width="100%" align="center">
					Amount: &nbsp;
					<input type="text" id="CompanySum" name="CompanySum" value="<?php echo $yandex_summ;?>" size="8" />&nbsp;Rub.
					<input type="hidden" name="to" value="<?php echo $yandex;?>"/>
					<input type="hidden" name="CompanyName" value="<?php echo $wm_descpay;?> "/>
					<input type="hidden" name="CompanyLink" value="<?php echo $yandex_successurl;?>"/>
				</td>
			</tr>
			<tr>
				<td width="100%" align="center">
					ID <?php echo $yandex;?>
				</td>
			</tr>
			<tr>
				<td width="100%" align="center">
					<br/>
					<input type="submit" class="button" value="<?php echo $btntxt;?>">
				</td>
			</tr>
		</table>
	</form>
</div>
<!--------------------------- PayPal --------------------------->
<div id="paypal" align="center" style="display:none;">
<?php
/**
$length = isset( $_POST[ 'paypallength' ] ) ? (int) $_POST[ 'paypallength' ] : "";
$amount = isset( $_POST[ 'paypalamount' ] ) ? trim( $_POST[ 'paypalamount' ] ) : "";
$amount = str_replace( ',', '.', $amount );
if( 1 <= $length && $length <= 3 )
{
  $amount = (int) round( $amount, 0 );
}
if( $amount < $paypalvalleast_val )
{
  $amount = $paypalvalleast_val;
}
$currency_code = isset( $_POST[ 'paypalcurrency_code' ] ) ? trim( $_POST[ 'paypalcurrency_code' ] ) : 0;
if ($length == 4) {
  header("Location: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=".$donate_email."&item_name=".$donate_org."&amount=".$amount."&no_shipping=0&no_note=1&tax=0&currency_code=".$currency_code."&bn=PP%2dDonationsBF&charset=UTF%2d8&return=".$link_return."&cancel=".$link_cancel);
}
else if ($length == 1) {
  header("Location: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick-subscriptions&business=".$donate_email."&item_name=".$donate_org."&no_shipping=1&no_note=1&currency_code=".$currency_code."&bn=PP%2dSubscriptionsBF&charset=UTF%2d8&a3=".$amount."%2e00&p3=1&t3=W&src=1&sra=1&return=".$link_return."&cancel=".$link_cancel);
}
elseif ($length == 2) {
  header("Location: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick-subscriptions&business=".$donate_email."&item_name=".$donate_org."&no_shipping=1&no_note=1&currency_code=".$currency_code."&bn=PP%2dSubscriptionsBF&charset=UTF%2d8&a3=".$amount."%2e00&p3=1&t3=M&src=1&sra=1&return=".$link_return."&cancel=".$link_cancel);
}
elseif ($length == 3) {
  header("Location: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick-subscriptions&business=".$donate_email."&item_name=".$donate_org."&no_shipping=1&no_note=1&currency_code=".$currency_code."&bn=PP%2dSubscriptionsBF&charset=UTF%2d8&a3=".$amount."%2e00&p3=1&t3=Y&src=1&sra=1&return=".$link_return."&cancel=".$link_cancel);
}
*/
$currencies = array( 'USD' => '$ ', 'EUR' => '&euro; ' );
//$currencies = array( 'USD' => '$ ', 'GBP' => '&pound; ', 'EUR' => '&euro; ' );
?>
<div id="paypal_logo">
<img src="<?php echo $logopaypal?>" alt="PayPal" />
</div>
<form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
    <input type="hidden" name="cmd" value="_donations">
    <input type="hidden" name="business" value="<?php echo $donate_email?>">
    <input type="hidden" name="charset" value="UTF-8">
    <input type="hidden" name="lc" value="RU">
    <input type="hidden" name="no_shipping" value="1">
    <input type="hidden" name="no_note" value="1">
    <input type="hidden" name="notify_url" value="<?php echo $link?>">
    <input type="hidden" name="return" value="<?php echo $link_return?>">
    <input type="hidden" name="cancel_return" value="<?php echo $link_cancel?>">
    <input type="hidden" name="item_name" value="<?php echo $donate_org?>">
<?php
if ($paypalval_on == 0) {
  $javaScript = <<<'JAVASCRIPT'
<script type="text/javascript">
  function donateChangeCurrency( )
  {
    var selectionObj = document.getElementById( 'donate_currency_code' );
    var selection = selectionObj.value;
    var currencyObj = document.getElementById( 'donate_symbol_currency' );
    if( currencyObj )
    {
      var currencySymbols = { 'USD': '$ ', 'GBP': '&pound; ', 'EUR': '&euro; ' };
      var currencySymbol = currencySymbols[ selection ];
      currencyObj.innerHTML = currencySymbol;
    }
  }
</script>
JAVASCRIPT;
  $symbol = $currencies[ $paypalcur_val ];
  echo "$javaScript Enter Amount:<br><span id=\"donate_symbol_currency\">".$symbol."</span><input type=\"text\" name=\"amount\" size=\"3\"  value=\"".$paypalval_val."\" class=\"inputbox\">";
}
elseif ($paypalval_on == 1) {
  echo "<input type=\"hidden\" value=\"".$paypalval_val."\" name=\"amount\">";
}
if ($paypalcur_on == 0) {
  print( "<select name=\"currency_code\" id=\"donate_currency_code\" class=\"inputbox\" onchange=\"donateChangeCurrency();\">" );
  foreach( $currencies as $currency => $dummy )
  {
    $selected = ( $currency == $paypalcur_val ) ? " selected=\"selected\"" : "";
    print( "<option value=\"$currency\"$selected>$currency</option>\n" );
  }
  print( "</select>\n" );
}
elseif ($paypalcur_on == 1) {
  echo "<input type=\"hidden\" name=\"currency_code\" value=\"".$paypalcur_val."\">";
}
if ($donate_len == 0) {
  ?>
  <select name="paypallength" class="inputbox">
    <option value="4">One Time</option>
    <option value="1">Weekly</option>
    <option value="2">Monthly</option>
    <option value="3">Annual</option>
  </select>
  <?
}
elseif ($donate_len == 1) {
  ?>
  <input type="hidden" name="paypallength" value="<? echo $paypallen_val; ?>" />
  <?
}
?>
<br>
<br>
<input type="submit" class="button" name="paypalsubmit" alt="Make payments with PayPal !" value="<?php echo $btntxt?>" />
</form>
</div>
</div>
</body>
</html>