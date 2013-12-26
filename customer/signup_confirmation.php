<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This file is part of A2Billing (http://www.a2billing.net/)
 *
 * A2Billing, Commercial Open Source Telecom Billing platform,   
 * powered by Star2billing S.L. <http://www.star2billing.com/>
 * 
 * @copyright   Copyright (C) 2004-2009 - Star2billing S.L. 
 * @author      Belaid Arezqui <areski@gmail.com>
 * @license     http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @package     A2Billing
 *
 * Software License Agreement (GNU Affero General Public License)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * 
**/


include ("./lib/customer.defines.php");
include ("./lib/customer.module.access.php");
include ("./lib/customer.smarty.php");

if (!$A2B->config["signup"]['enable_signup']) {
	exit ();
}

if (!isset ($_SESSION["date_mail"]) || (time() - $_SESSION["date_mail"]) > 60) {
	$_SESSION["date_mail"] = time();
} else {
	sleep(3);
	echo gettext("Sorry the confirmation email has been sent already, multi-signup are not authorized! Please wait 2 minutes before making any other signup!");
	exit ();
}

if (!isset ($_SESSION["cardnumber_signup"]) || strlen($_SESSION["cardnumber_signup"]) <= 1) {
	echo gettext("Error : No User Created.");
	exit ();
}

$FG_DEBUG = 0;
$DBHandle = DbConnect();

//echo "activatedbyuser = ".$activatedbyuser."<br>";
$lang_code = $_SESSION["language_code"];
//if (has_rights(ACX_DISTRIBUTION)) $activatedbyuser = true;
//else $activatedbyuser = $A2B->config['signup']['activatedbyuser'];
$activatedbyuser = has_rights(ACX_DISTRIBUTION) ? true : $A2B->config['signup']['activatedbyuser'];
if (!$activatedbyuser) {
	$mailtype = Mail :: $TYPE_SIGNUP;
} else {
	$mailtype = Mail :: $TYPE_SIGNUPCONFIRM;
}
//echo "id_signup       = ".$_SESSION["id_signup"]."<br>";
//echo "language_code   = ".$_SESSION["language_code"]."<br>";
//echo "language        = ".$language."<br>";
//echo "=========================<br>";
try {
	$mail = new Mail($mailtype, $_SESSION["id_signup"], $lang_code);
} catch (A2bMailException $e) {
	echo "<br>" . gettext("Error : No email Template Found");
	exit ();
}

$QUERY = "SELECT username, lastname, firstname, email, uipass, credit, useralias, loginkey FROM cc_card WHERE id=" . $_SESSION["id_signup"];

$res = $DBHandle->Execute($QUERY);
$num = 0;
if ($res)
	$num = $res->RecordCount();

if (!$num) {
	echo "<br>" . gettext("Error : No such user found in database");
	exit ();
}

for ($i = 0; $i < $num; $i++) {
	$list[] = $res->fetchRow();
}

if ($FG_DEBUG == 1)
	echo "</br><b>BELOW THE CARD PROPERTIES </b><hr></br>";

list ($username, $lastname, $firstname, $email, $uipass, $credit, $cardalias, $loginkey) = $list[0];
if ($FG_DEBUG == 1)
	echo "<br># $username, $lastname, $firstname, $email, $uipass, $credit, $cardalias #</br>";

try {
	$mail->send();
} catch (A2bMailException $e) {
    $error_msg = $e->getMessage();
}

$smarty->display('signup_header.tpl');
?>

<blockquote>
    <div align="center"><br><br>
	<font color="#FF0000"><b><?php echo gettext("SIGNUP CONFIRMATION"); ?></b></font><br>
	<br/><br/>
	<?php if (!$activatedbyuser){
			echo $list[0][2]; ?> <?php echo $list[0][1]; ?>, <?php echo gettext("thank you for registering with us!");?><br>
		  <?php echo gettext("An activation email has been sent to"); ?> <b><?php echo $list[0][3]; ?></b><br><br>	  
	<?php }elseif (has_rights(ACX_DISTRIBUTION)){
			echo gettext("Your new customer was registered successfully");?><br>
		  <?php echo gettext("An email confirming registration information has been sent to customer to"); ?> <b><?php echo $list[0][3]; ?></b><br><br>
			<h3>
			  <?php echo gettext("Cardnumber of ").$list[0][2]; ?> <?php echo $list[0][1] . gettext(" is "); ?> <b><font color="#00AA00"><?php echo $list[0][0]; ?></font></b><br><br><br><u>
			  <?php echo gettext("To login to account :"); ?></u><br>
			  <?php echo gettext("Card alias (login) is "); ?> <b><font color="#00AA00"><?php echo $list[0][6]; ?></font></b><br>
			  <?php echo gettext("Password is "); ?> <b><font color="#00AA00"><?php echo $list[0][4]; ?></font></b><br>
			</h3>
			<script type="text/javascript">window.opener.location.reload();</script>
	<?php }else {
			echo $list[0][2]; ?> <?php echo $list[0][1]; ?>, <?php echo gettext("Thank you for registering with us !");?><br>
		  <?php echo gettext("An email confirming your information has been sent to"); ?> <b><?php echo $list[0][3]; ?></b><br><br>
			<h3>
			  <?php echo gettext("Your cardnumber is "); ?> <b><font color="#00AA00"><?php echo $list[0][0]; ?></font></b><br><br><br><u>
			  <?php echo gettext("To login to your account :"); ?></u><br>
			  <?php echo gettext("Your card alias (login) is "); ?> <b><font color="#00AA00"><?php echo $list[0][6]; ?></font></b><br>
			  <?php echo gettext("Your password is "); ?> <b><font color="#00AA00"><?php echo $list[0][4]; ?></font></b><br>
			</h3>
	<?php } ?>
		
</div>
</blockquote>      

<br><br><br>
<br><br><br>


<?php

$smarty->display('signup_footer.tpl');
