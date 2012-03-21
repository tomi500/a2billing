<HTML>
<HEAD>
	<link rel="shortcut icon" href="templates/{$SKIN_NAME}/images/a2billing-icon-32x32.ico">
	<title>..:: {$CCMAINTITLE} ::..</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		{if ($CSS_NAME!="" && $CSS_NAME!="default")}
			   <link href="templates/default/css/{$CSS_NAME}.css" rel="stylesheet" type="text/css">
		{else}
			   <link href="templates/default/css/main.css" rel="stylesheet" type="text/css">
			   <link href="templates/default/css/menu.css" rel="stylesheet" type="text/css">
			   <link href="templates/default/css/style-def.css" rel="stylesheet" type="text/css">
		{/if}
         <script type="text/javascript" src="./javascript/jquery/jquery-1.2.6.min.js"></script>
</HEAD>

<BODY leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

	<form name="form" method="POST" action="userinfo.php" onsubmit="return test()">
	<input type="hidden" name="done" value="submit_log">

    <div id="login-wrapper" class="login-border-up">
	<div class="login-border-down">
	<div class="login-border-center">
	<center>
	<table border="0" cellpadding="3" cellspacing="12">
	<tr>
		<td class="login-title" colspan="2">
    {if ($error == 1)}
		{php} echo gettext("AUTHENTICATION REFUSED :<br>please check your user/password!");{/php}
    {elseif ($error==2)}
		{php} echo gettext("INACTIVE ACCOUNT :<br>Your account need to be activated!");{/php}
    {elseif ($error==3)}
		{php} echo gettext("BLOCKED ACCOUNT :<br>Please contact the administrator!");{/php}
    {elseif ($error==4)}
		{php} echo gettext("NEW ACCOUNT :<br>Your account has not been validate yet!");{/php}
    {else}
		{php} echo gettext("AUTHENTICATION");{/php}
    {/if}
		</td>
	</tr>
	<tr>
		<td ><img src="templates/{$SKIN_NAME}/images/kicons/lock_bg.png"></td>
		<td align="center" style="padding-right: 10px">
			<table width="90%">
			<tr align="center">
				<td align="left"><font size="2" face="Arial, Helvetica, Sans-Serif"><b>{php} echo gettext("User");{/php}:</b></font></td>
				<td><input class="form_input_text" type="text" name="pr_login" size="15" value="{$username}"></td>
			</tr>
			<tr align="center">
				<td align="left"><font face="Arial, Helvetica, Sans-Serif" size="2"><b>{php} echo gettext("Password");{/php}:</b></font></td>
				<td><input class="form_input_text" type="password" name="pr_password" size="15" value="{$password}"></td>
			</tr>
			</tr><tr >
                <td colspan="2"> &nbsp;</td>
            </tr>
			<tr align="right" >
                <td>
                    <select name="ui_language"  id="ui_language" class="icon-menu form_input_select">
                        <option style="background-image:url(templates/{$SKIN_NAME}/images/flags/gb.gif);" value="english" {php} if(LANGUAGE=="english") echo "selected";{/php} >English</option>
<!--
                        <option style="background-image:url(templates/{$SKIN_NAME}/images/flags/ua.gif);" value="ukrainian" {php} if(LANGUAGE=="ukrainian") echo "selected";{/php} >Ukrainian</option>
-->
                        <option style="background-image:url(templates/{$SKIN_NAME}/images/flags/ru.gif);" value="russian" {php} if(LANGUAGE=="russian") echo "selected";{/php} >Russian</option>
                        <option style="background-image:url(templates/{$SKIN_NAME}/images/flags/es.gif);" value="spanish" {php} if(LANGUAGE=="spanish") echo "selected";{/php} >Spanish</option>Român
                        <option style="background-image:url(templates/{$SKIN_NAME}/images/flags/fr.gif);" value="french" {php} if(LANGUAGE=="french") echo "selected";{/php} >French</option>
                        <option style="background-image:url(templates/{$SKIN_NAME}/images/flags/de.gif);" value="german" {php} if(LANGUAGE=="german") echo "selected";{/php} >German</option>
                        <option style="background-image:url(templates/{$SKIN_NAME}/images/flags/fi.gif);" value="finnish" {php} if(LANGUAGE=="finnish") echo "selected";{/php} >Finnish</option>
                        <option style="background-image:url(templates/{$SKIN_NAME}/images/flags/pt.gif);" value="portuguese" {php} if(LANGUAGE=="portuguese") echo "selected";{/php} >Portuguese</option>
                        <option style="background-image:url(templates/{$SKIN_NAME}/images/flags/br.gif);" value="brazilian" {php} if(LANGUAGE=="brazilian") echo "selected";{/php}>Brazilian</option>
                        <option style="background-image:url(templates/{$SKIN_NAME}/images/flags/it.gif);" value="italian" {php} if(LANGUAGE=="italian") echo "selected";{/php} >Italian</option>
                        <option style="background-image:url(templates/{$SKIN_NAME}/images/flags/cn.gif);" value="chinese" {php} if(LANGUAGE=="chinese") echo "selected";{/php} >Chinese</option>
                        <option style="background-image:url(templates/{$SKIN_NAME}/images/flags/ro.gif);" value="romanian" {php} if(LANGUAGE=="romanian") echo "selected";{/php} >Romanian</option>
                        <option style="background-image:url(templates/{$SKIN_NAME}/images/flags/pl.gif);" value="polish" {php} if(LANGUAGE=="polish") echo "selected";{/php} >Polish</option>
                        <option style="background-image:url(templates/{$SKIN_NAME}/images/flags/tr.gif);" value="turkish" {php} if(LANGUAGE=="turkish") echo "selected";{/php} >Turkish</option>
                        <option style="background-image:url(templates/{$SKIN_NAME}/images/flags/pk.gif);" value="urdu" {php} if(LANGUAGE=="urdu") echo "selected";{/php} >Urdu</option>
                        <option style="background-image:url(templates/{$SKIN_NAME}/images/flags/gr.gif);" value="greek" {php} if(LANGUAGE=="greek") echo "selected";{/php} >Greek</option>
                        <option style="background-image:url(templates/{$SKIN_NAME}/images/flags/id.gif);" value="indonesian" {php} if(LANGUAGE=="indonesian") echo "selected";{/php} >Indonesian</option>
                    </select>
                </td>
				<td><input type="submit" name="submit" value="{php} echo gettext("LOGIN");{/php}" class="form_input_button"></td>
			</tr>
			
			</table>
		</td>
	</tr>
	<tr align="center">
		<td colspan="2"><font class="fontstyle_007">{php} echo gettext("Forgot your password ?");{/php} <a href="forgotpassword.php">{php} echo gettext("Click here");{/php}</a></font>.</td>
    </tr>
	<tr align="center">
        <td colspan="2"><font class="fontstyle_007">{php} echo gettext("To sign up");{/php} <a href="signup.php">{php} echo gettext("Click here");{/php}</a></font>.</td>
    </tr>    
  	</table>
  	</center>
  	</div>
  	</div>
  	
    </div>
    <div id="footer">
<!--    <div style="color: #BD2A15; font-size: 11px; text-align:center;">{$COPYRIGHT}</div>
-->
    {php}
    $DBHandle = DbConnect();
    $instance_table = new Table();
    $QUERY = "SELECT configuration_key FROM cc_configuration where configuration_key in ('MODULE_PAYMENT_AUTHORIZENET_STATUS','MODULE_PAYMENT_PAYPAL_STATUS','MODULE_PAYMENT_MONEYBOOKERS_STATUS','MODULE_PAYMENT_WORLDPAY_STATUS','MODULE_PAYMENT_PLUGNPAY_STATUS','MODULE_PAYMENT_WM_STATUS') AND configuration_value='True'";
    $payment_methods = $instance_table->SQLExec($DBHandle, $QUERY);
    $show_logo = '';
    for ($index = 0; $index < sizeof($payment_methods); $index++) {
	if ($payment_methods[$index][0] == "MODULE_PAYMENT_PAYPAL_STATUS") {
	    $show_logo .= '<a href="https://www.paypal.com/en/mrb/pal=PGSJEXAEXKTBU" target="_blank"><img src="' . KICON_PATH . '/payments_paypal.gif" alt="Paypal"/></a>' . ' &nbsp; ';
	} elseif ($payment_methods[$index][0] == "MODULE_PAYMENT_MONEYBOOKERS_STATUS") {
	    $show_logo .= '<a href="https://www.moneybookers.com/app/?rid=811621" target="_blank"><img src="' . KICON_PATH . '/moneybookers.gif" alt="Moneybookers"/></a>' . ' &nbsp; ';
	} elseif ($payment_methods[$index][0] == "MODULE_PAYMENT_PLUGNPAY_STATUS") {
	    $show_logo .= '<a href="http://www.plugnpay.com/" target="_blank"><img src="' . KICON_PATH . '/plugnpay.png" alt="plugnpay.com"/></a>' . ' &nbsp; ';
	} elseif ($payment_methods[$index][0] == "MODULE_PAYMENT_WM_STATUS") {
	    $show_logo .= '<a href="http://www.wmtransfer.com/" target="_blank"><img src="' . KICON_PATH . '/webmoney_virified.png" alt="WebMoney"/></a>' . ' &nbsp; ';
	}
    }
    $show_logo .= <<<'EOD'
<!--LiveInternet counter-->
<script type="text/javascript">
document.write("<a href='http://www.liveinternet.ru/click' "+
"target=_blank><img src='//counter.yadro.ru/hit?t39.2;r"+
escape(document.referrer)+((typeof(screen)=="undefined")?"":
";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
";"+Math.random()+
"' alt='' title='LiveInternet' "+
"border='0' width='31' height='31'><\/a>")
</script>
<!--/LiveInternet-->
EOD;
    echo '<table style="width:100%;margin:0 auto;" align="center">
    <tr><td valign="top" align="center" class="tableBodyRight">' . $show_logo . '
    </td></tr></table>';
{/php}

    </div>
	</form>
{literal}
<script LANGUAGE="JavaScript">
	//document.form.pr_login.focus();
        $("#ui_language").change(function () {
          self.location.href= "index.php?ui_language="+$("#ui_language option:selected").val();
        });
</script>
{/literal}
