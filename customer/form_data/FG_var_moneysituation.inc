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
 * @author      Nick Mitin <nixon@sipde.net>
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



$HD_Form = new FormHandler("cc_card","Card");
$HD_Form -> FG_TABLE_CLAUSE = "id_diller=".$_SESSION["card_id"];
$HD_Form -> FG_TABLE_ID="cc_card.id";
$HD_Form -> FG_DEBUG = 0;
$HD_Form -> FG_LIMITE_DISPLAY = 10;
$HD_Form -> FG_TABLE_DEFAULT_SENS = "DESC";

if (has_rights (ACX_DISTRIBUTION) && !($popup_select>=1)) {
	$HD_Form -> FG_ADDITION = true;
	$HD_Form -> FG_LIST_ADDING_BUTTON1 = true;
	$HD_Form -> FG_LIST_ADDING_BUTTON_LINK1 = "javascript:;\" onClick=\"MM_openBrWindow('signup.php?form_action=ask-add&id_diller=".$_SESSION["pr_login"]."&margin=".$_SESSION["margin_diller"]."&popup_select=1&popup_formname=myForm&displayheader=0','','resizable=yes,width=800,height=810,left=600,top=50,screenX=600,screenY=50')\"";
//	$HD_Form -> FG_LIST_ADDING_BUTTON_LINK1 = "signup.php?form_action=ask-add&id_diller=".$_SESSION["pr_login"]."&margin=5&section=14";
	$HD_Form -> FG_LIST_ADDING_BUTTON_ALT1 = $HD_Form ->FG_LIST_ADDING_BUTTON_MSG1 = gettext("Add Customer");
	$HD_Form -> FG_LIST_ADDING_BUTTON_IMG1 = Images_Path ."/user_add.png" ;
}

//$HD_Form -> AddViewElement(gettext("ID"), "cc_card.id", "7%", "center", "sort", "");
//$HD_Form -> AddViewElement(gettext("CardNumber"), "username", "7%", "center", "sort", "", "30", "", "", "", "", "linktocustomer");
//$HD_Form -> AddViewElement(gettext("CUSTOMER"), "customer", "18%", "center", "sort");
$HD_Form -> AddViewElement(gettext("CUSTOMER"), "lastname", "25%", "center", "sort", "", "link2cust", "cc_card", "CONCAT_WS(' ',lastname,firstname,IF(company_name='','',CONCAT('(',company_name,')'))), id", "id='%id'", "%1", "", "javascript:;\" onClick=\"MM_openBrWindow('A2B_entity_moneysituation_details.php?form_action=ask-edit&displayheader=0&popup_select=1&idcust=%2','','status=no,toolbar=no,menubar=no,location=no,directories=no,scrollbars=yes,resizable=yes,width=675,height=680,left=500,top=40,screenX=500,screenY=40')\"");
$HD_Form -> AddViewElement(gettext("BALANCE"), "credit", "11%", "center", "sort", "", "", "", "", "", "", "display_refill_money");
$HD_Form -> AddViewElement(gettext("COMMISSION ACCRUED"), "commission", "17%", "center", "sort", "", "", "", "", "", "", "display_refill_money");
$HD_Form -> AddViewElement(gettext("MARGIN"), "margin", "7%", "center", "sort");
//$HD_Form -> AddViewElement(gettext("INVOICE "), "invoice", "5%", "center", "sort", "15", "lie", "cc_invoice LEFT JOIN cc_invoice_item ON cc_invoice_item.id_invoice=cc_invoice.id", "ROUND(SUM(CEIL(cc_invoice_item.price*(1+(cc_invoice_item.vat/100))*100)/100),2)", "cc_invoice.id_card='%id' GROUP BY cc_invoice.id_card", "%1");
$HD_Form -> AddViewElement(gettext("INVOICE "), "invoice", "5%", "center", "", "", "link2cust", "cc_invoice LEFT JOIN cc_invoice_item ON cc_invoice_item.id_invoice=cc_invoice.id", "SUM(ROUND(cc_invoice_item.price*(1+(cc_invoice_item.vat/100)),2)), cc_invoice.id_card", "cc_invoice.id_card='%id' GROUP BY cc_invoice.id_card", "%1", "", "javascript:;\" onClick=\"MM_openBrWindow('A2B_entity_moneysituation_details.php?type=invoice&form_action=list&atmenu=card&displayheader=0&popup_select=1&idcust=%2','','scrollbars=yes,resizable=yes,width=500,height=415,left=600,top=200,screenX=600,screenY=200')\"");
$HD_Form -> AddViewElement(gettext("AMOUNT "), "payment", "5%", "center", "", "", "link2cust", "cc_logpayment as t2", "SUM(ROUND(t2.payment-t2.fee,2)),t2.card_id", "t2.card_id='%id' GROUP BY t2.card_id", "%1", "", "javascript:;\" onClick=\"MM_openBrWindow('A2B_entity_moneysituation_details.php?type=payment&form_action=list&atmenu=card&displayheader=0&popup_select=1&idcust=%2','','scrollbars=yes,resizable=yes,width=550,height=415,left=600,top=200,screenX=600,screenY=200')\"");
$HD_Form -> AddViewElement(gettext("TO PAY"), "to_pay", "10%", "center", "", "", "eval", "number_format((%4-%5+abs(%5-%4))/2, 2, '.', ' ')");

//$HD_Form -> FieldViewElement ("CONCAT_WS(' ',lastname,firstname,IF(company_name='','',CONCAT('(',company_name,')'))) AS customer, credit, commission, CONCAT_WS(' ',margin,'%') margin, cc_card.id, cc_card.id, cc_card.id");
$HD_Form -> FieldViewElement ("cc_card.id, credit, commission, CONCAT_WS(' ',margin,'%'), cc_card.id, cc_card.id, cc_card.id");

$HD_Form -> FG_ACTION_SIZE_COLUMN = '10%';
$HD_Form -> CV_NO_FIELDS  = gettext("THERE IS NO").strtoupper($HD_Form->FG_INSTANCE_NAME)." ".gettext("CREATED!"); 
$HD_Form -> CV_DISPLAY_LINE_TITLE_ABOVE_TABLE = false;
$HD_Form -> CV_TEXT_TITLE_ABOVE_TABLE = '';
$HD_Form -> CV_DISPLAY_FILTER_ABOVE_TABLE = false;


$HD_Form -> FG_EDITION = false;
$HD_Form -> FG_DELETION = false;
$HD_Form -> FG_ADDITION = false;


if (has_rights (ACX_DISTRIBUTION) && !($popup_select>=1)) {
/**	$FG_COL_QUERY_FIELD = '';
	if (strlen($FG_COL_QUERY_FIELD)>2) {
		$FG_COL_QUERY_FIELD = substr( $FG_COL_QUERY_FIELD,2);
	}
	$FG_COL_QUERY_COUNT = count(preg_split('/,/',$FG_COL_QUERY_FIELD));
**/
//	$HD_Form -> FG_INFO = true;

	$HD_Form -> FG_OTHER_BUTTON1 = true;
	$HD_Form -> FG_OTHER_BUTTON2 = true;
	$HD_Form -> FG_OTHER_BUTTON3 = true;
	$HD_Form -> FG_OTHER_BUTTON4 = false;
	$HD_Form -> FG_OTHER_BUTTON5 = true;
	$HD_Form -> FG_OTHER_BUTTON1_LINK= "javascript:;\" onClick=\"MM_openBrWindow('A2B_entity_moneysituation_details.php?type=sipiax&form_action=list&atmenu=SIP&displayheader=0&popup_select=1&idcust=|param|','','scrollbars=yes,resizable=yes,width=650,height=415,left=550,top=200,screenX=550,screenY=200')\"";
	$HD_Form -> FG_OTHER_BUTTON2_LINK= "javascript:;\" onClick=\"MM_openBrWindow('A2B_entity_callerid.php?form_action=list&displayheader=0&popup_select=1&idcust=|param|','','scrollbars=yes,resizable=yes,width=600,height=450,left=600,top=200,screenX=600,screenY=200')\"";
	$HD_Form -> FG_OTHER_BUTTON3_LINK= "javascript:;\" onClick=\"MM_openBrWindow('call-history.php?popup_select=1&choose_callowner=4&id=|param|','','scrollbars=yes,resizable=yes,width=950,height=500,left=160,top=180,screenX=160,screenY=180')\"";
	$HD_Form -> FG_OTHER_BUTTON4_LINK= "javascript:;\" onClick=\"MM_openBrWindow('A2B_entity_moneysituation_details.php?type=payment&form_action=list&atmenu=card&displayheader=0&popup_select=1&idcust=|param|','','scrollbars=yes,resizable=yes,width=550,height=415,left=600,top=200,screenX=600,screenY=200')\"";
	$HD_Form -> FG_OTHER_BUTTON5_LINK= "javascript:;\" onClick=\"MM_openBrWindow('A2B_entity_logrefill.php?form_action=ask-add&popup_select=1&popup_formname=myForm&card_id=|param|&displayheader=0','','scrollbars=no,resizable=yes,width=570,height=300,left=700,top=200,screenX=700,screenY=200')\"";
	$HD_Form -> FG_OTHER_BUTTON1_ALT = '<font color="red">'.gettext('EXT').'</font>';
	$HD_Form -> FG_OTHER_BUTTON2_ALT = '<font color="red">'.gettext('CID').'</font>';
	$HD_Form -> FG_OTHER_BUTTON3_ALT = '<font color="red">'.gettext('LOG').'</font>';
	$HD_Form -> FG_OTHER_BUTTON4_ALT = '<font color="red">'.gettext('PAY').'</font>';
	$HD_Form -> FG_OTHER_BUTTON5_ALT = gettext('Add Refill to this Card');
//	$HD_Form -> FG_OTHER_BUTTON4_ALT = '<font color="red">'.gettext('REFILL').'</font>';

	$HD_Form -> FG_OTHER_BUTTON1_IMG = '';
	$HD_Form -> FG_OTHER_BUTTON2_IMG = '';
	$HD_Form -> FG_OTHER_BUTTON3_IMG = '';
	$HD_Form -> FG_OTHER_BUTTON4_IMG = '';
	$HD_Form -> FG_OTHER_BUTTON5_IMG = Images_Path . "/money.png";

}




$HD_Form -> FG_INTRO_TEXT_EDITION= gettext("You can modify, through the following form, the different properties of your")." ".$HD_Form->FG_INSTANCE_NAME;
$HD_Form -> FG_INTRO_TEXT_ASK_DELETION = gettext("If you really want remove this")." ".$HD_Form->FG_INSTANCE_NAME.", ".gettext("click on the delete button.");
$HD_Form -> FG_INTRO_TEXT_ADD = gettext("you can add easily a new")." ".$HD_Form->FG_INSTANCE_NAME.".<br>".gettext("Fill the following fields and confirm by clicking on the button add.");


$HD_Form -> FG_FILTER_APPLY = true;
//$HD_Form -> FG_FILTERFIELD = 'name';
$HD_Form -> FG_FILTERFIELDNAME = gettext("CUSTOMER");
$HD_Form -> FG_FILTER_FORM_ACTION = 'list';

if (isset($filterprefix)  &&  (strlen($filterprefix)>0)){
	if (strlen($HD_Form -> FG_TABLE_CLAUSE)>0) $HD_Form -> FG_TABLE_CLAUSE.=" AND";
	$HD_Form -> FG_TABLE_CLAUSE.=" (upper(lastname) like upper('$filterprefix%') OR upper(company_name) like upper('$filterprefix%'))";
}



$HD_Form -> FG_INTRO_TEXT_ADITION = gettext("Add a ".$HD_Form->FG_INSTANCE_NAME." now.");
$HD_Form -> FG_TEXT_ADITION_CONFIRMATION = gettext("Your new ".$HD_Form->FG_INSTANCE_NAME." has been inserted. <br>");


$HD_Form -> FG_BUTTON_EDITION_SRC = $HD_Form -> FG_BUTTON_ADITION_SRC  = Images_Path . "/cormfirmboton.gif";
$HD_Form -> FG_BUTTON_EDITION_BOTTOM_TEXT = $HD_Form -> FG_BUTTON_ADITION_BOTTOM_TEXT = gettext("Once you have completed the form above, click on the CONTINUE button.");



$HD_Form -> FG_GO_LINK_AFTER_ACTION_ADD = $_SERVER['PHP_SELF']."?atmenu=document&stitle=Document&wh=AC&id=";
$HD_Form -> FG_GO_LINK_AFTER_ACTION_EDIT = $_SERVER['PHP_SELF']."?atmenu=document&stitle=Document&wh=AC&id=";
$HD_Form -> FG_GO_LINK_AFTER_ACTION_DELETE = $_SERVER['PHP_SELF']."?atmenu=document&stitle=Document&wh=AC&id=";
