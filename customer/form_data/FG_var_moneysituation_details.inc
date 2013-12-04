<?php

getpost_ifset(array('id', 'displayheader', 'type', 'popup_select', 'popup_formname', 'popup_fieldname', 'atmenu'));
/**
if ($form_action == "ask-add") {
//echo "form_action1=".$form_action.'<br>';
	$QUERY = "SELECT credit FROM cc_card WHERE username = '" . $_SESSION["pr_login"] . "' AND uipass = '" . $_SESSION["pr_password"] . "'";
	$resmax = $DBHandle->Execute($QUERY);
	if ($resmax) {
		$row = $resmax->fetchRow();
		$credit = $row[0];
	}
//echo "credit=".$credit."<br>";

//	$HD_Form = new FormHandler("cc_logrefill","Refill");

	$HD_Form -> FG_TABLE_ID = 'cc_logrefill.id';

	if (!empty($account)) {
		$id_val = "value='$account'";
	}

	$HD_Form ->FG_LIST_ADDING_BUTTON1 = true;

	$list_refill_type = Constants::getRefillType_List();
} else
**/
if ($type == 'payment') {
	$HD_Form = new FormHandler("cc_logpayment as t2 LEFT JOIN cc_card ON cc_card.id=t2.card_id ", gettext("Payments"));
	$HD_Form->FG_TABLE_CLAUSE = "t2.card_id = $id AND cc_card.id_diller = {$_SESSION['card_id']}";
	$HD_Form->FG_TABLE_ID = 't2.id';
	$HD_Form->FG_TABLE_DEFAULT_SENS = "DESC";
	$HD_Form->AddViewElement(gettext("DATE"), "date", "30%", "center", "sort", "30", "", "", "", "", "", "display_dateformat");
	$HD_Form->AddViewElement(gettext("PAYMENT INCL FEE"), "payment", "40%", "center", "sort", "", "", "", "", "", "", "display_2bill");
	$HD_Form->AddViewElement(gettext("DESCRIPTION"), "description", "30%", "center", "sort");

	$HD_Form->FieldViewElement("t2.date, t2.payment, t2.description");
	$HD_Form->CV_NO_FIELDS = gettext("THERE IS NO PAYMENTS CREATED!");

} elseif ($type == 'invoice') {
	$list_sql = "cc_invoice " .
	"LEFT JOIN cc_card ON cc_card.id=cc_invoice.id_card " .
	"LEFT JOIN ( SELECT st1.id_invoice, SUM(st1.price*(1+(st1.vat/100))) AS total_price FROM cc_invoice_item AS st1 GROUP BY st1.id_invoice) " .
	"AS items ON items.id_invoice=cc_invoice.id ";
	$HD_Form = new FormHandler($list_sql, gettext("INVOICES"));
	$HD_Form->FG_TABLE_ID = 'cc_invoice.id';
	$HD_Form->FG_TABLE_DEFAULT_SENS = "DESC";
	$HD_Form->FG_TABLE_CLAUSE = "cc_invoice.id_card = $id AND cc_card.id_diller = {$_SESSION['card_id']} ";
	$HD_Form->AddViewElement(gettext("DATE"), "date", "50%", "center", "sort", "30", "", "", "", "", "", "display_dateformat");
	$HD_Form->AddViewElement(gettext("AMOUNT INCL VAT"), "total_price", "50%", "center", "sort", "", "", "", "", "", "", "display_2bill");

	$HD_Form->FieldViewElement("cc_invoice.date, total_price");
	$HD_Form->CV_NO_FIELDS = gettext("THERE IS NO INVOICE CREATED!");

} elseif ($type == 'sipiax') {
	$atmenu = ($atmenu=='IAX')?$atmenu:'SIP';
	$HD_Form = new FormHandler("cc_".strtolower($atmenu)."_buddies AS t2 LEFT JOIN cc_card ON cc_card.id = id_cc_card ", gettext($atmenu . " Config"));
	$HD_Form -> FG_TABLE_ID = 't2.id';
	$HD_Form -> FG_TABLE_DEFAULT_ORDER = "regexten ";
	$HD_Form -> FG_TABLE_DEFAULT_SENS = "ASC";
	$HD_Form -> FG_TABLE_CLAUSE = "id_cc_card = $id AND cc_card.id_diller = {$_SESSION['card_id']} AND t2.external = 0 ";
	$HD_Form -> AddViewElement(gettext("EXTENSION"), "regexten", "6%", "center", "sort");
	$HD_Form -> AddViewElement(gettext("CALLERID NAME"), "callerid", "20%", "center", "sort");
	$HD_Form -> AddViewElement(gettext("LANGUAGE"), "language", "5%", "center", "sort");
	$HD_Form -> AddViewElement(gettext("HOST"), "host", "24%", "center", "sort");
	$HD_Form -> AddViewElement(gettext("REGNAME"), "name", "9%", "center", "sort");
	$HD_Form -> AddViewElement(gettext("SECRET"), "secret", "4%", "center", "sort");

	$HD_Form -> FieldViewElement ("IF(regexten='',t2.name,regexten), callerid, t2.language, IF(port>0 AND UNIX_TIMESTAMP()<regseconds,concat('<font color=green>',ipaddr,':',port,'</font> ',IF(lastms=-1,'UNREACHABLE',IF(lastms>0,concat('(',lastms,' ms)'),'')),'<br>',useragent),host), t2.name, t2.secret");
	$HD_Form -> CV_NO_FIELDS = gettext("THERE IS NO") . " " . strtoupper($HD_Form->FG_INSTANCE_NAME) . " " . gettext("CREATED!");

}

$HD_Form -> FG_DEBUG = 0;

$HD_Form->CV_DISPLAY_LINE_TITLE_ABOVE_TABLE = false;
$HD_Form->CV_TEXT_TITLE_ABOVE_TABLE = '';
$HD_Form->CV_DISPLAY_FILTER_ABOVE_TABLE = false;
$HD_Form->FG_VIEW_TABLE_WITDH = '95%';

$HD_Form->CV_FOLLOWPARAMETERS = "&popup_select=" . $popup_select . "&id=$id&type=$type";
$HD_Form->FG_EDITION = false;
$HD_Form->FG_DELETION = false;
$HD_Form->FG_ADDITION = false;
