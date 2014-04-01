<?php

getpost_ifset(array('id', 'card_id', 'date', 'credit', 'description', 'added_invoice', 'refill_type', 'idcust', 'popup_select', 'message'));

$list_refill_type = Constants::getRefillType_List();
$list_refill_type[] = array( gettext("Connection charge for DID setup"), "4");
$list_refill_type[] = array( gettext("Monthly Charge for DID use"), "5");
$list_refill_type[] = array( gettext("Subscription fee"), "6");
$list_refill_type[] = array( gettext("Extra charge"), "7");

$QUERY_ADD = $QUERY_CLAUSE = "";
if (!isset($form_action) || $form_action=="list") {
	if (has_rights(ACX_DISTRIBUTION))
		$QUERY_CLAUSE = " OR diller_id = ".$_SESSION["card_id"];
	$QUERY = "((SELECT cc_charge.id, cc_charge.creationdate `date`, -amount credit, lastname, firstname, company_name, id_cc_card card_id, chargetype+3 refill_type, IFNULL(description,0) description FROM cc_charge
LEFT JOIN cc_card ON cc_card.id=id_cc_card WHERE id_cc_card = ".$_SESSION["card_id"]." AND charged_status = 1)
UNION (
SELECT cc_logrefill.id, `date`, cc_logrefill.credit, lastname, firstname, company_name, card_id, refill_type, description FROM cc_logrefill
LEFT JOIN cc_card ON cc_card.id=card_id WHERE cc_card.id = ".$_SESSION["card_id"].$QUERY_CLAUSE.")) aa";
	$HD_Form = new FormHandler($QUERY,gettext("Refill"));
//	if (strlen($HD_Form -> FG_TABLE_CLAUSE)>0)
//		$HD_Form -> FG_TABLE_CLAUSE.=" AND ";
//	$HD_Form -> FG_TABLE_CLAUSE.="(";
//	$HD_Form -> FG_TABLE_CLAUSE.="cc_card.id = ".$_SESSION["card_id"].") AND id_cc_card = ".$_SESSION["card_id"];
	$HD_Form -> FG_TABLE_ID = 'aa.id';
	$HD_Form -> FG_TABLE_DEFAULT_ORDER = 'date';
} else {
	$HD_Form = new FormHandler("cc_logrefill",gettext("Refill"));
	unset($list_refill_type[0]);
	$HD_Form -> FG_TABLE_ID = 'cc_logrefill.id';
}

$HD_Form -> FG_DEBUG = 0;
$HD_Form -> FG_TABLE_DEFAULT_SENS = "DESC";
/**
$HD_Form ->FG_LIST_ADDING_BUTTON1 = true;
$HD_Form ->FG_LIST_ADDING_BUTTON_LINK1 = "A2B_entity_logrefill.php?form_action=ask-add&section=".$_SESSION["menu_section"];
$HD_Form ->FG_LIST_ADDING_BUTTON_ALT1 = $HD_Form ->FG_LIST_ADDING_BUTTON_MSG1 = gettext("Add Refill");
$HD_Form ->FG_LIST_ADDING_BUTTON_IMG1 = Images_Path . "/money.png";
**/

//$HD_Form -> AddViewElement(gettext("ID"), "cc_logrefill.id", "4%", "center", "sort");
$HD_Form -> AddViewElement(gettext("REFILL DATE"), "date", "17%", "center", "sort", "30", "", "", "", "", "", "display_dateformat");
if (has_rights(ACX_DISTRIBUTION)) {
	$HD_Form -> AddViewElement(gettext("PARTNER"), "name", "17%", "center", "sort", "", "link2cust", "cc_card", "IF(id = ".$_SESSION['card_id'].",'',CONCAT_WS(' ',lastname,firstname,IF(company_name='','',CONCAT('(',company_name,')')))), id", "id='%id'", "%1", "", "javascript:;\" onClick=\"MM_openBrWindow('A2B_entity_moneysituation_details.php?type=card&form_action=ask-edit&atmenu=card&displayheader=0&popup_select=1&idcust=%2','','scrollbars=yes,resizable=yes,width=660,height=810,left=600,top=50,screenX=430,screenY=50')\"", gettext("USER DELETED"));
	$QUERY_ADD = "card_id, ";
}
$HD_Form -> AddViewElement(gettext("REFILL AMOUNT"), "credit", "15%", "center", "sort","", "", "", "", "", "", "display_refill_money");
$HD_Form -> AddViewElement(gettext("REFILL TYPE"),"refill_type", "13%", "center", "sort", "", "list", $list_refill_type);
$HD_Form -> AddViewElement(gettext("DESCRIPTION"), "description", "30%", "center", "sort");

$HD_Form -> FieldViewElement ("date, ".$QUERY_ADD."IF(card_id = ".$_SESSION['card_id'].", credit, -credit) AS credit, refill_type, IFNULL(description,IF(credit > 0,IF(card_id = ".$_SESSION['card_id'].",'".gettext('REFILL FROM YOUR DILLER')."','".gettext('REFILL TO YOUR CUSTOMER')."'),IF(card_id = ".$_SESSION['card_id'].",'".gettext('REFUND TO YOUR DILLER')."','".gettext('REFUND FROM YOUR CUSTOMER')."')))");

/**
if (!($popup_select>=1))	 $HD_Form -> FG_FILTER_SEARCH_FORM = true;
$HD_Form -> FG_FILTER_SEARCH_SESSION_NAME = 'refill_cust_selection';
$HD_Form -> FG_FILTER_SEARCH_TOP_TEXT = gettext('Define specific criteria to search for refills created.');
$HD_Form -> FG_FILTER_SEARCH_1_TIME = true;
$HD_Form -> FG_FILTER_SEARCH_1_TIME_TEXT = gettext('DATE');
$HD_Form -> FG_FILTER_SEARCH_1_TIME_FIELD = 'date';


$HD_Form -> AddSearchElement_C1(gettext("ACCOUNT NUMBER"), 'username','usernametype');
$HD_Form -> AddSearchElement_C1(gettext("LASTNAME"),'lastname','lastnametype');
$HD_Form -> AddSearchElement_C1(gettext("FIRSTNAME"),'firstname','firstnametype');
$HD_Form -> FG_FILTER_SEARCH_DELETE_ALL = false;
**/
$HD_Form -> CV_NO_FIELDS  = gettext("THERE IS NO ".strtoupper($HD_Form->FG_INSTANCE_NAME)." CREATED!"); 
$HD_Form -> CV_DISPLAY_LINE_TITLE_ABOVE_TABLE = false;
$HD_Form -> CV_TEXT_TITLE_ABOVE_TABLE = '';
$HD_Form -> CV_DISPLAY_FILTER_ABOVE_TABLE = false;
if (has_rights(ACX_DISTRIBUTION))	$HD_Form -> FG_FILTER_APPLY = true;
$HD_Form -> FG_FILTERFIELD = 'lastname';
$HD_Form -> FG_FILTERFIELDNAME = gettext('LASTNAME').gettext(' OR ').gettext('COMPANY');
$HD_Form -> FG_FILTER_FORM_ACTION = 'list';
$HD_Form -> FG_LIMITE_DISPLAY = 13;

if (isset($filterprefix)  &&  (strlen($filterprefix)>0)) {
	if (strlen($HD_Form -> FG_TABLE_CLAUSE)>0) $HD_Form -> FG_TABLE_CLAUSE.=" AND";
	$HD_Form -> FG_TABLE_CLAUSE.=" (upper(lastname) like upper('$filterprefix%') OR upper(company_name) like upper('$filterprefix%'))";
}

$HD_Form -> FG_ADDITION = true;
if ($form_action=="ask-add") {
//	$begin_date  = date("Y");
//	$end_date    = date("-m-d H:i:s");
//	$comp_date   = $begin_date.$end_date;
//	$comp_date   = "value='$comp_date'";
	$refill_type = 3;
}
/**
$HD_Form -> AddEditElement (gettext("REFILL DATE"),
			   "date",
			   '$value',
			   "INPUT",
			   "size=52 maxlength=40 $comp_date",
			   "10",
			   gettext("Insert the current date"),
			   "", "", "", "", "", "", "", "" );
**/
$QUERYpop = "SELECT credit FROM cc_card WHERE username = '" . $_SESSION["pr_login"] . "' AND uipass = '" . $_SESSION["pr_password"] . "'";
$resmax = $DBHandle->Execute($QUERYpop);
if ($resmax) {
	$row = $resmax->fetchRow();
	$credit = $row[0];
}

$HD_Form -> AddEditElement (gettext("REFILL AMOUNT "),
			   "credit",
			   '$value',
			   "INPUT",
			   "id=credit size=52 maxlength=10",
			   "12",
			   gettext("Insert the credit amount"),
			   "", "", "", "", "", "", "", gettext("Enter the amount in the currency base: ").BASE_CURRENCY.".<BR>".gettext("<b>Available ").$credit." ".BASE_CURRENCY.".</b>" );

$HD_Form -> AddEditElement (gettext("DESCRIPTION"),
			    "description",
			    '',
			    "TEXTAREA",
			    "cols=50 rows=4",
			    "",
			    gettext("Insert the description"),
			    "", "", "", "", "", "", "", "");

$HD_Form -> AddEditElement (gettext("REFILL TYPE"),
			    "refill_type",
			    $refill_type,
			    "SELECT",
			    "",
			    "",
			    "",
			    "list", "", "", "", $list_refill_type, "%1", "NO-NULL");

	$HD_Form -> FieldEditElement ('credit, description, refill_type');
	$HD_Form -> FG_QUERY_ADITION_HIDDEN_FIELDS = "card_id, diller_id";
	$HD_Form -> FG_QUERY_ADITION_HIDDEN_VALUE = "$card_id, " . $_SESSION["card_id"];

	$HD_Form -> FG_INTRO_TEXT_EDITION= gettext("You can modify, through the following form, the different properties of your ".$HD_Form->FG_INSTANCE_NAME);
	$HD_Form -> FG_INTRO_TEXT_ASK_DELETION = gettext("If you really want remove this ".$HD_Form->FG_INSTANCE_NAME.", click on the delete button.");
	$HD_Form -> FG_INTRO_TEXT_ADD = gettext("you can add easily a new ".$HD_Form->FG_INSTANCE_NAME.".<br>Fill the following fields and confirm by clicking on the button add.");

if ($popup_select>=1) {
	$QUERYpop = "SELECT lastname, firstname FROM cc_card WHERE id = $card_id AND id_diller = " . $_SESSION["card_id"];
	$resmax = $DBHandle->Execute($QUERYpop);
	if ($resmax) {
		$row = $resmax->fetchRow();
//		?><center><?php
//		echo gettext("To: ").$row[0]." ".$row[1];
		$HD_Form -> FG_INTRO_TEXT_ADITION = gettext("To: ").$row[0]." ".$row[1];
	}
}
	$HD_Form -> FG_TEXT_ADITION_CONFIRMATION = gettext("Your new ".$HD_Form->FG_INSTANCE_NAME." has been inserted.<br>");


//	$HD_Form -> FG_BUTTON_EDITION_SRC = $HD_Form -> FG_BUTTON_ADITION_SRC  = Images_Path . "/cormfirmboton.gif";
	$HD_Form -> FG_BUTTON_EDITION_BOTTOM_TEXT = $HD_Form -> FG_BUTTON_ADITION_BOTTOM_TEXT = gettext("Click 'Confirm Data' to continue");

	$HD_Form -> FG_ADDITIONAL_FUNCTION_BEFORE_ADD = 'check_add_card_refill_diller';
	$HD_Form -> FG_ADDITIONAL_FUNCTION_AFTER_ADD = 'processing_diller_refill_add';

	$HD_Form -> FG_ADITION_HIDDEN_PARAM = "popup_select";
	$HD_Form -> FG_ADITION_HIDDEN_PARAM_VALUE = 1;

//$HD_Form->FG_ADITION_HIDDEN_PARAM = $HD_Form->FG_EDITION_HIDDEN_PARAM = 'popup_select, idcust';
//$HD_Form->FG_ADITION_HIDDEN_PARAM = $HD_Form->FG_EDITION_HIDDEN_PARAM_VALUE = "$popup_select, $idcust";

$HD_Form -> FG_GO_LINK_AFTER_ACTION_ADD = $_SERVER['PHP_SELF']."?idcust=$card_id&message=success&popup_select=$popup_select&card_id=$card_id&id=";

//	$HD_Form -> FG_GO_LINK_AFTER_ACTION_ADD = $_SERVER['PHP_SELF']."?atmenu=document&stitle=Document&wh=AC&id=";
	$HD_Form -> FG_GO_LINK_AFTER_ACTION_EDIT = $_SERVER['PHP_SELF']."?atmenu=document&stitle=Document&wh=AC&id=";
	$HD_Form -> FG_GO_LINK_AFTER_ACTION_DELETE = $_SERVER['PHP_SELF']."?atmenu=document&stitle=Document&wh=AC&id=";
