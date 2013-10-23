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



getpost_ifset(array('IDCust', 'id', 'idcust', 'id_cc_card', 'cid', 'blacklist', 'activated', 'atmenub'));

if (strlen($HD_Form -> FG_TABLE_CLAUSE)>0)
	$HD_Form -> FG_TABLE_CLAUSE.=" AND ";

//$idcard = (has_rights(ACX_DISTRIBUTION) && ($popup_select>=1)) ? $idcust : $_SESSION["card_id"];
$idcard = (!isset($idcust) || $idcust == "") ? $_SESSION["card_id"] : $idcust ;

$atmenub = ($atmenub=='')?'ALL':$atmenub;
switch($atmenub) {
	case 'ALL'	: $listclause = '';break;
	case 'WHITE'	: $listclause = ' AND blacklist = 0';break;
	case 'BLACK'	: $listclause = ' AND blacklist = 1';break;
}

if (has_rights(ACX_DISTRIBUTION) && $popup_select>=1 && (!isset($form_action) || $form_action=="list")) {
	$HD_Form = new FormHandler("cc_callerid LEFT JOIN cc_card AS t2 ON t2.id=id_cc_card", gettext("CallerID"));
	$HD_Form -> FG_TABLE_CLAUSE = "t2.id = $idcust AND t2.id_diller = {$_SESSION['card_id']}".$listclause;
	$HD_Form -> FG_TABLE_ID = 'cc_callerid.id';
} elseif (!isset($form_action) || $form_action=="list") {
	$HD_Form = new FormHandler("cc_callerid", gettext("CallerID"));
	$HD_Form -> FG_TABLE_CLAUSE .= "id_cc_card = ".$_SESSION['card_id'].$listclause;
} else {
	$HD_Form = new FormHandler("cc_callerid", gettext("CallerID"));
	$HD_Form -> FG_TABLE_CLAUSE .= "id_cc_card = ".$idcard.$listclause;
}

$HD_Form -> FG_DEBUG = 0;

$actived_list = Constants::getActivationTrueFalseList();
$yesno = Constants::getYesNoList();


$HD_Form -> AddViewElement(gettext("CALLERID"), "cid", "25%", "center", "sort");
$HD_Form -> AddViewElement(gettext("ACTIVATED"), "activated", "13%", "center", "sort", "26", "list", $actived_list);
$HD_Form -> AddViewElement(gettext("CALLBACK"), "callback", "13%", "center", "SORT", "", "list", $yesno);
$HD_Form -> AddViewElement(gettext("PHONE FORWARD"), "phonenumber", "25%", "center", "sort", "26");
$HD_Form -> AddViewElement(gettext("VERIFY"), "verify", "13%", "center", "SORT");


$HD_Form -> FieldViewElement ("cid, cc_callerid.activated, callback, phonenumber, IF(verify=0,'<font color=red>".gettext('No')."</font>','<font color=green>".gettext('Yes')."</font>')");

$HD_Form -> CV_TITLE_TEXT = gettext("CallerID list");
$HD_Form -> CV_NO_FIELDS  = gettext("There are no Caller IDs created");
$HD_Form -> CV_DISPLAY_LINE_TITLE_ABOVE_TABLE = false;
$HD_Form -> CV_TEXT_TITLE_ABOVE_TABLE = '';
$HD_Form -> CV_DISPLAY_FILTER_ABOVE_TABLE = false;
$HD_Form -> FG_LIMITE_DISPLAY = 10;
$HD_Form -> FG_VIEW_TABLE_WITDH = '70%';

$HD_Form -> FG_DELETION_CONDITION = '"|col4|" == "<font color=red>'.gettext('No').'</font>"';

$HD_Form -> FG_ADDITION = false;
$HD_Form -> FG_EDITION = true;
$HD_Form -> FG_DELETION = true;

if (isset($form_action) && ($form_action=='ask-edit' || $form_action=='edit')) {

	$QUERY = "SELECT status, currency FROM cc_card  WHERE id = $idcard";
	$DBHandle  = DbConnect();
	$numrow = 0;
	$resmax = $DBHandle -> Execute($QUERY);
	if ($resmax)
		$numrow = $resmax -> RecordCount();

	if ($numrow == 0)
		exit();
	$customer_info =$resmax -> fetchRow();

	$currencies_list = get_currencies();
	$two_currency = false;

	if (!isset($currencies_list[strtoupper($customer_info [1])][2]) || !is_numeric($currencies_list[strtoupper($customer_info [1])][2]))
		$mycur = 1;
	else {
		$mycur = $currencies_list[strtoupper($customer_info [1])][2];
		if (strtoupper($_SESSION['currency'])!=strtoupper(BASE_CURRENCY)) $two_currency=true;
	}

	// Possible value to notify the user
	$instance_config_table = new Table("cc_config", "id, config_value");
	$QUERY = " config_key = 'values_notifications' ";
	$return = null;
	$return = $instance_config_table -> Get_list($DBHandle, $QUERY, 0);
	$values = explode(":",$return[0]["config_value"]);
	$limits_notify = array();
	$idx =0;
	$limits_notify [$idx] = array(gettext("NEVER").'&nbsp;', -2);
	foreach ($values as $val) {
		$idx++;
		$val_display = round($val,2);
		if ($two_currency)
			$val_display .= " ".strtoupper(BASE_CURRENCY)." - ".round($val/$mycur,2)." ".strtoupper($_SESSION['currency']);
		$limits_notify [$idx] = array($val_display,$val);
	}

//--------------------------------------------------------------------------------------

	$cidRO = "";
	$cli_access = 'disabled="true" style="color: gray;" ';
	$cli_prefix_access = "readonly";
	$DBHandle_max = DbConnect();
	$resmax = $DBHandle_max -> Execute("SELECT * FROM cc_callerid WHERE id='$id' AND verify=1 LIMIT 1");
	if ( $resmax ) {
		$numrow = $resmax -> RecordCount( );
		if ( $numrow == 1 ) {
			$cidRO = $cli_prefix_access;
			$cli_access = $cli_prefix_access = "";
		}
	}
}


$HD_Form -> AddEditElement (gettext("CALLERID"),
			   "cid",
			   '$value',
			   "INPUT",
			   "size=20 maxlength=30 ".$cidRO,
			   "18",
			   gettext("Insert the CallerID"));

$HD_Form -> AddEditElement (gettext("ACTIVATED"),
			   "activated",
			   't',
			   "RADIOBUTTON",
			   "",
			   "",
			   "",
			   "", "", "", gettext("Yes").":t, - ".gettext("No").":f", "", "", "",
			   gettext("... for incoming calls from this number"));

$HD_Form -> AddEditElement (gettext("BLACKLIST"),
			    "blacklist",
			    '0',
			    "RADIOBUTTON",
			    "",
			    "",
			    "",
			    "", "", "", gettext("Yes").":1, - ".gettext("No").":0", "", "", "",
			    gettext("Choose 'Yes' if you want to place this CallerID to Black List. This will break any autorization attempts from this CallerID."));

$HD_Form -> AddEditElement (gettext("PHONE NUMBER"),
			    "phonenumber",
			    '$value',
			    "INPUT",
			    "size=50 maxlength=50",
			    "18",
			    gettext("Enter the phone"),
			    "", "", "", "", "", "", "NO", gettext("Enter the phone number for immediatly dial instead prompt to dial"));

$HD_Form -> AddEditElement (gettext("CALLBACK"),
			   "callback",
			   '0',
			   "SELECT",
			   "",
			   "",
			   gettext("Choose 'Yes' if you want to callback to this CallerID"),
			   "list", "", "", "", $yesno, "%1", "",
			   gettext("Choose 'Yes' if you want to callback to this CallerID"));

$showcur = (!$two_currency)?gettext('currency : ').strtoupper(BASE_CURRENCY):'';
$HD_Form -> AddEditElement (gettext("SAY BALANCE AFTER AUTH"),
			   "warning_threshold",
			   '',
			   "SELECT",
			   "",
			   "",
			   "",
			   "list", "", "", "", $limits_notify , "%1", "", $showcur." (".gettext("with the warning threshold").")", "", '<OPTION  value="-1" selected>'.gettext("ALWAYS").'&nbsp;</OPTION>');

$HD_Form -> AddEditElement (gettext("SAY RATE BEFORE CALL"),
			   "say_rateinitial",
			   '0',
			   "SELECT",
			   "",
			   "",
			   "",
			   "list", "", "", "", $yesno, "%1");

$HD_Form -> AddEditElement (gettext("SAY BALANCE AFTER CALL"),
			   "say_balance_after_call",
			   '0',
			   "SELECT",
			   "",
			   "",
			   "",
			   "list", "", "", "", $yesno, "%1");

$HD_Form -> AddEditElement (gettext("CONSPIRATION"),
			   "cli_replace",
			   '0',
			   "SELECT",
			   "",
			   "",
			   "",
			   "list", "", "", "", $yesno, "%1", "",
			   gettext("Choose 'Yes' if you want hide CLI and replace its in a general manner for calls from this CallerID. Choose 'No' if you want set a veto for replacement CLI for calls from this CallerID. Choose 'Local' to replace this CallerID when calling to countries for which there exist a local number(s) for replacement."),
			   "", '<OPTION  value="2">'.gettext("Local").'&nbsp;</OPTION>',
			   gettext("If your CallerID number was verified you can use the remaining options..."));

$HD_Form -> AddEditElement (gettext("LOCAL REPLACE"),
			   "cli_localreplace",
			   '0',
			   "SELECT",
			   $cli_access,
			   "",
			   "",
			   "list", "", "", "", $yesno, "%1", "",
			   gettext("Present this CallerID, when calls are made to a country relevant to this CallerID."),
			   null, null, gettext("Settings for general Caller Line Identity (CLI)"));

$HD_Form -> AddEditElement (gettext("OTHER REPLACE"),
			   "cli_otherreplace",
			   '0',
			   "SELECT",
			   $cli_access,
			   "",
			   "",
			   "list", "", "", "", $yesno, "%1", "",
			   gettext("Present this CallerID, when calls are made to countries not relevant to this CallerID."));

$HD_Form -> AddEditElement (gettext("REPLACE BY PREFIX"),
			    "cli_prefixreplace",
			    "$value",
			    "INPUT",
			    "size=30 maxlength=30 ".$cli_prefix_access,
			    "28",
			    "",
			    "" , "", "", "", "" , "" , "NO", gettext("Present this CallerID, when calls are made to countries from listed prefixes. Insert the replaceable prefixes separated by comma, e.g. for UK,IT,RU,UA are '44,39,7,380'."));

$HD_Form -> FieldEditElement ('cid, activated, blacklist, phonenumber, callback, warning_threshold, say_rateinitial, say_balance_after_call, cli_replace, cli_localreplace, cli_otherreplace, cli_prefixreplace');

$HD_Form->FG_ADITION_HIDDEN_PARAM = $HD_Form->FG_EDITION_HIDDEN_PARAM = 'popup_select, idcust';
$HD_Form->FG_ADITION_HIDDEN_PARAM = $HD_Form->FG_EDITION_HIDDEN_PARAM_VALUE = "$popup_select, $idcust";

if (isset($filterprefix)  &&  (strlen($filterprefix)>0)) {
    if (strlen($HD_Form -> FG_TABLE_CLAUSE)>0)
        $HD_Form -> FG_TABLE_CLAUSE.=" AND ";

    $HD_Form -> FG_TABLE_CLAUSE.="cid like '$filterprefix%'";
}

$HD_Form -> CV_FOLLOWPARAMETERS = "&popup_select=$popup_select&idcust=$idcust&displayheader=0";

$HD_Form -> FG_INTRO_TEXT_EDITION= gettext("You can modify, through the following form, the different properties of your CallerID");
$HD_Form -> FG_INTRO_TEXT_ASK_DELETION = gettext("If you really want remove this")." ".$HD_Form->FG_INSTANCE_NAME.", ".gettext("click on the delete button.");
$HD_Form -> FG_INTRO_TEXT_ADD = gettext("you can add easily a new")." ".$HD_Form->FG_INSTANCE_NAME.".<br>".gettext("Fill the following fields and confirm by clicking on the button add.");

if (!has_rights(ACX_DISTRIBUTION) || !($popup_select>=1)) {
	$HD_Form -> FG_INTRO_TEXT_ADITION = gettext("You can add your home phone or cellphone number below to allow you to call in, be automatically recognized, and use your telephony services without having to enter your PIN.");
} else
	$HD_Form -> FG_INTRO_TEXT_ADITION = "";
$HD_Form -> FG_TEXT_ADITION_CONFIRMATION = gettext("Your new")." ".$HD_Form->FG_INSTANCE_NAME." ".gettext("has been inserted.")."<br>";

$HD_Form -> FG_BUTTON_EDITION_SRC = $HD_Form -> FG_BUTTON_ADITION_SRC  = Images_Path."/cormfirmboton.gif";
$HD_Form -> FG_BUTTON_EDITION_BOTTOM_TEXT = $HD_Form -> FG_BUTTON_ADITION_BOTTOM_TEXT = gettext("Made your change to edit this callerid.");

$HD_Form -> FG_GO_LINK_AFTER_ACTION_ADD 	= $_SERVER['PHP_SELF']."?atmenu=document&stitle=Document&wh=AC&popup_select=$popup_select&idcust=$idcust&id=";
$HD_Form -> FG_GO_LINK_AFTER_ACTION_EDIT	= $_SERVER['PHP_SELF']."?atmenu=document&stitle=Document&wh=AC&popup_select=$popup_select&idcust=$idcust&id=";
$HD_Form -> FG_GO_LINK_AFTER_ACTION_DELETE	= $_SERVER['PHP_SELF']."?atmenu=document&stitle=Document&wh=AC&popup_select=$popup_select&idcust=$idcust&id=";
