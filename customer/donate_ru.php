<?php
$mosConfig_live_site = 'https://my.domain/';
// Global
$lang='';
$moduleclass_sfx = '';
$pretext = 'Благодарность Автору';
$btntxt = 'Отправить';
// Privat24
$use_privat24 = 1;
$privat24_summ = ''; //тут вставить сумму для оплаты контрагентом
// Webmoney
$use_wm = 1;
$wmz = 'Z';
$wme = 'E';
$wmr = 'R';
$wmu = 'U';
$wmcur_type = 'WMZ'; //валюта по-умолчанию
$wm_summ = ''; //тут вставить сумму для оплаты контрагентом
$wm_successurl = $wm_errorurl = $mosConfig_live_site."donate_ru.php";
$wm_descpay = 'Donate Author';
// Yandex
$use_yandex = 0;
$yandex = '01234567891011';
$yandex_summ = '0';
$yandex_successurl = $mosConfig_live_site."donate_ru.php";
// PayPal
$use_paypal = 1;
$donate_email = 'abcdefghi3jklmn0p';
$paypalcur_on = 0;
$paypalcur_val = 'USD'; //валюта по-умолчанию
$paypalval_on = 0;
$paypalval_val = ''; //тут вставить сумму для оплаты контрагентом
$paypalvalleast_val = 5;
$donate_org = 'Donate Author';
$donate_len = 1;
$paypallen_val = 4;
$link_cancel = $link_return = $mosConfig_live_site."donate_ru.php";
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
$logowm          = $mosConfig_live_site.'templates/default/images/kicons/logowm.gif';
$logoyandex      = $mosConfig_live_site.'templates/default/images/kicons/logoyandex.gif';
$logopaypal      = $mosConfig_live_site.'templates/default/images/kicons/logopaypal.gif';
$logoprivat24    = $mosConfig_live_site.'templates/default/images/kicons/logoprivat24.gif';
$logowm_sm       = $mosConfig_live_site.'templates/default/images/kicons/logowm_small.gif';
$logoyandex_sm   = $mosConfig_live_site.'templates/default/images/kicons/logoyandex_small.gif';
$logopaypal_sm   = $mosConfig_live_site.'templates/default/images/kicons/logopaypal_small.gif';
$logoprivat24_sm = $mosConfig_live_site.'templates/default/images/kicons/logoprivat24_small.gif';
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Donate for Me</title>
<meta name="description" content="Development for Something" />
<meta name="keywords" content="something" />
<meta name="robots" content="index, follow" />
<base href="https://my.domain/" />
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
<script type="text/javascript" src="./javascript/jquery/jquery-1.7.2.min.js"></script>
<script type="text/javascript">
	function show_wm()
	{
		var $j = jQuery.noConflict();
		$j('#paypal').hide();
		$j('#privat24').hide();
		$j('#yandex').hide();
		$j('#wm').fadeIn(1500);
		return false;
	};
	function show_privat24()
	{
		var $j = jQuery.noConflict();
		$j('#paypal').hide();
		$j('#wm').hide();
		$j('#yandex').hide();
		$j('#privat24').fadeIn(1500);
		return false;
	};
	function show_yandex()
	{
		var $j = jQuery.noConflict();
		$j('#paypal').hide();
		$j('#privat24').hide();
		$j('#wm').hide();
		$j('#yandex').fadeIn(1500);
		return false;
	};
	function show_paypal()
	{
		var $j = jQuery.noConflict();
		$j('#privat24').hide();
		$j('#wm').hide();
		$j('#yandex').hide();
		$j('#paypal').fadeIn(1500);
		return false;
	};
	function hide_all()
	{
		var $j = jQuery.noConflict();
		$j('#paypal').hide();
		$j('#privat24').hide();
		$j('#wm').hide();
		$j('#yandex').hide();
		return false;
	}
</script>
</head>
<body>
<div id="moduletable_donate">
<!--------------------------- Panel --------------------------->
<div id="rulez" align="center">
<?php	if ($pretext != '')
	{?>
		<span style="font-weight:bold; cursor:pointer; border-bottom:1px solid #CCC; padding:3px;" onclick="hide_all()" title="Hide all">
			<?php echo $pretext;?>

		</span><br/><br/>
<?php
	}
	if ($use_paypal)
	{?>
		<a href="javascript:void(0);" onclick="show_paypal()" title="PayPal"><img src="<?php echo $logopaypal_sm;?>" alt="PayPal" border="0" /></a>
<?php	}
	if ($use_privat24)
	{?>
		<a href="javascript:void(0);" onclick="show_privat24()" title="Pivat24"><img src="<?php echo $logoprivat24_sm;?>" alt="Privat24" border="0" /></a>
<?php	}
	if ($use_wm)
	{?>
		<a href="javascript:void(0);" onclick="show_wm()" title="Webmoney"><img src="<?php echo $logowm_sm;?>" alt="Webmoney" border="0" /></a>
<?php	}
	if ($use_yandex)
	{?>
	<a href="javascript:void(0);" onclick="show_yandex()" title="Yandex" style="text-decoration: none"><img src="<?php echo $logoyandex_sm;?>" alt="Yandex" border="0" /></a>
<?php	}?>
</div>
<!--------------------------- Privat24 --------------------------->
<?php $style = ($privat24_summ)?"":" style=\"display:none;\"";?>
<div id="privat24" align="center"<?php echo $style;?>>
	<form id="privat24_pay" name="privat24_pay" method="GET" action="https://sendmoney.privatbank.ua/ru/">
		<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td width="100%" align="center">
					<img src="<?php echo $logoprivat24;?>" alt="" title="" border="0" />
				</td>
			</tr>
			<tr>
				<td width="100%" align="center">
					Сумма&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				</td>
			</tr>
			<tr>
				<td width="100%" align="center">
					<input type="hidden" name="hash" value="1234567890">
					<input name="placeholder" type="text" size="4" value="<?php echo $privat24_summ;?>"> грн.
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
<!--------------------------- Webmoney --------------------------->
<?php $style = ($wm_summ)?"":" style=\"display:none;\"";?>
<div id="wm" align="center"<?php echo $style;?>>
	<form id="pay" name="pay" method="POST" action="https://merchant.webmoney.ru/lmi/payment.asp">
		<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td width="100%" align="center">
					<img src="<?php echo $logowm;?>" alt="" title="" border="0" />
				</td>
			</tr>
			<tr>
				<td width="100%" align="center">
					Сумма&nbsp;&nbsp;Знаки&nbsp;&nbsp;&nbsp;
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
						<option <?php if ($wmtype1==$wmcur_type) echo "selected=\"selected\" ";?>value="<?php echo $wmnum1;?>"><?php echo $wmtype1;?></option>
						<option <?php if ($wmtype2==$wmcur_type) echo "selected=\"selected\" ";?>value="<?php echo $wmnum2;?>"><?php echo $wmtype2;?></option>
						<option <?php if ($wmtype3==$wmcur_type) echo "selected=\"selected\" ";?>value="<?php echo $wmnum3;?>"><?php echo $wmtype3;?></option>
						<option <?php if ($wmtype4==$wmcur_type) echo "selected=\"selected\" ";?>value="<?php echo $wmnum4;?>"><?php echo $wmtype4;?></option>
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
<?php $style = ($paypalval_val)?"":" style=\"display:none;\"";?>
<div id="paypal" align="center"<?php echo $style;?>>
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
  header("Location: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=".$donate_email."&item_name=".$donate_org."&amount=".$amount."&lc=".$lang."&no_shipping=0&no_note=1&tax=0&currency_code=".$currency_code."&bn=PP%2dDonationsBF&charset=UTF%2d8&return=".$link_return."&cancel=".$link_cancel);
}
else if ($length == 1) {
  header("Location: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick-subscriptions&business=".$donate_email."&item_name=".$donate_org."&lc=".$lang."&no_shipping=1&no_note=1&currency_code=".$currency_code."&bn=PP%2dSubscriptionsBF&charset=UTF%2d8&a3=".$amount."%2e00&p3=1&t3=W&src=1&sra=1&return=".$link_return."&cancel=".$link_cancel);
}
elseif ($length == 2) {
  header("Location: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick-subscriptions&business=".$donate_email."&item_name=".$donate_org."&lc=".$lang."&no_shipping=1&no_note=1&currency_code=".$currency_code."&bn=PP%2dSubscriptionsBF&charset=UTF%2d8&a3=".$amount."%2e00&p3=1&t3=M&src=1&sra=1&return=".$link_return."&cancel=".$link_cancel);
}
elseif ($length == 3) {
  header("Location: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick-subscriptions&business=".$donate_email."&item_name=".$donate_org."&lc=".$lang."&no_shipping=1&no_note=1&currency_code=".$currency_code."&bn=PP%2dSubscriptionsBF&charset=UTF%2d8&a3=".$amount."%2e00&p3=1&t3=Y&src=1&sra=1&return=".$link_return."&cancel=".$link_cancel);
}
*/
//$currencies = array( 'USD' => '$ ', 'EUR' => '&euro; ' );
$currencies = array( 'USD' => '$ ', 'GBP' => '&pound; ', 'EUR' => '&euro; ' );//, 'THB' => '&#x0E3F ' );
?>
<div id="paypal_logo">
<img src="<?php echo $logopaypal;?>" alt="PayPal" />
</div>
<form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
    <input type="hidden" name="cmd" value="_donations">
    <input type="hidden" name="business" value="<?php echo $donate_email;?>">
    <input type="hidden" name="charset" value="UTF-8">
    <input type="hidden" name="lc" value="<?php echo $lang;?>">
    <input type="hidden" name="no_shipping" value="1">
    <input type="hidden" name="no_note" value="1">
    <input type="hidden" name="notify_url" value="">
    <input type="hidden" name="return" value="<?php echo $link_return;?>">
    <input type="hidden" name="cancel_return" value="<?php echo $link_cancel;?>">
    <input type="hidden" name="item_name" value="<?php echo $donate_org;?>">
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
      var currencySymbols = { 'USD': '$ ', 'GBP': '&pound; ', 'EUR': '&euro; ', 'THB': '&#x0E3F ' };
      var currencySymbol = currencySymbols[ selection ];
      currencyObj.innerHTML = currencySymbol;
    }
  }
</script>
JAVASCRIPT;
  $symbol = $currencies[ $paypalcur_val ];
  echo "$javaScript &nbsp;&nbsp;Сумма&nbsp;&nbsp;Валюта<br><span id=\"donate_symbol_currency\">".$symbol."</span><input type=\"text\" name=\"amount\" size=\"3\"  value=\"".$paypalval_val."\" class=\"inputbox\"> ";
} elseif ($paypalval_on == 1) {
    echo "<input type=\"hidden\" value=\"".$amount."\" name=\"amount\">";
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
  <?php
}
elseif ($donate_len == 1) {
  ?>
  <input type="hidden" name="paypallength" value="<?php echo $paypallen_val; ?>" />
  <?php
}
?>
<br>
<br>
<input type="submit" class="button" name="paypalsubmit" value="<?php echo $btntxt?>" />
</form>
</div>
</div>
<script type="text/javascript">
    donateChangeCurrency();
</script>
</body>
</html>