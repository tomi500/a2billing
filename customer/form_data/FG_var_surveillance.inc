<?php

getpost_ifset(array('id', 'exten_leg_a','surveillance'));

$HD_Form = new FormHandler("cc_callback_spool_temp","surveillance");

$HD_Form -> FG_DEBUG = 0;

$HD_Form -> FG_TABLE_DEFAULT_ORDER = "exten_leg_a";
$HD_Form -> FG_TABLE_DEFAULT_SENS = "ASC";

$HD_Form -> AddViewElement(gettext("PHONENUMBER"), "exten_leg_a", "20%", "center", "sort", "", "lie_link", "", "", "", "", "", "call-history.php?s=1&t=0&order=t1.starttime&sens=DESC&current_page=0&posted=1&Period=&frommonth=&fromstatsmonth=&tomonth=&tostatsmonth=&fromday=true&fromstatsday_sday=1&fromstatsmonth_sday=".date('Y')."-".date('m')."&today=&tostatsday_sday=".date('d')."&tostatsmonth_sday=".date('Y')."-".date('m')."&calleridtype=1&phonenumbertype=1&sourcetype=&clidtype=&channel=&resulttype=min&callerid=&phonenumber=%0&src=&clid=&terminatecauseid=ANSWER&choose_calltype=-1&choose_currency=");
$HD_Form -> AddViewElement(gettext("DURATION"), "surveillance", "20%", "center", "sort", "26", "", "", "", "", "", "display_minute");
$HD_Form -> AddViewElement(gettext("TIME LEFT"), "timeleft", "20%", "center", "sort", "", "timeleft", "%1");
$HD_Form -> FieldViewElement ('exten_leg_a, surveillance*60 surveillance, surveillance*60-UNIX_TIMESTAMP(now())+UNIX_TIMESTAMP(last_attempt_time) timeleft');


$HD_Form -> CV_NO_FIELDS  = gettext("There are no surveillance created.");
$HD_Form -> CV_DISPLAY_LINE_TITLE_ABOVE_TABLE = false;
$HD_Form -> CV_TEXT_TITLE_ABOVE_TABLE = '';
$HD_Form -> CV_DISPLAY_FILTER_ABOVE_TABLE = false;
$HD_Form -> FG_VIEW_TABLE_WITDH = '70%';

if (strlen($HD_Form -> FG_TABLE_CLAUSE)>0)
    $HD_Form -> FG_TABLE_CLAUSE.=" AND ";
	$HD_Form -> FG_TABLE_CLAUSE.="surveillance > 0 AND account = ".$_SESSION["pr_login"];
	
if (strlen($HD_Form -> FG_EDITION_CLAUSE)>0)
    $HD_Form -> FG_EDITION_CLAUSE.=" AND ";
	$HD_Form -> FG_EDITION_CLAUSE.="account = ".$_SESSION["pr_login"];
	

$HD_Form -> FG_EDITION = true;
$HD_Form -> FG_DELETION = true;

$HD_Form -> AddEditElement (gettext("PHONE"),
			   "exten_leg_a",
			   '$value',
			   "INPUT",
			   "size=30 maxlength=6",
			   "4",
			   gettext("Insert the phone"),
			   "", "", "", "", "", "", "", gettext("Enter here the phonenumber you want to call for this record") );

$HD_Form -> AddEditElement (gettext("DURATION"),
			   "surveillance",
			   '$value',
			   "INPUT",
			   "size=30 maxlength=2",
			   "",
			   "",
			   "", "", "", "", "", "", "", gettext("Enter here the duration in minutes from 1 to 99 for saving") );

$HD_Form -> FieldEditElement ('exten_leg_a, surveillance');
/**
if (isset($filterprefix)  &&  (strlen($filterprefix)>0)) {
	if (strlen($HD_Form -> FG_TABLE_CLAUSE)>0) $HD_Form -> FG_TABLE_CLAUSE.=" AND ";
	$HD_Form -> FG_TABLE_CLAUSE.="id_cc_card like '$filterprefix'";
}
**/
$HD_Form -> FG_INTRO_TEXT_EDITION= gettext("You can modify, through the following form, the different properties of your")." ".$HD_Form->FG_INSTANCE_NAME;
$HD_Form -> FG_INTRO_TEXT_ASK_DELETION = gettext("If you really want remove this")." ".$HD_Form->FG_INSTANCE_NAME.", ".gettext("click on the delete button.");
$HD_Form -> FG_INTRO_TEXT_ADD = gettext("you can add easily a new")." ".$HD_Form->FG_INSTANCE_NAME.".<br>".gettext("Fill the following fields and confirm by clicking on the button add.");

$HD_Form -> FG_INTRO_TEXT_ADITION = '';
$HD_Form -> FG_TEXT_ADITION_CONFIRMATION = gettext("Your new")." ".$HD_Form->FG_INSTANCE_NAME." ".gettext("has been inserted.")."<br>";


$HD_Form -> FG_BUTTON_EDITION_SRC = $HD_Form -> FG_BUTTON_ADITION_SRC  = Images_Path."/cormfirmboton.gif";
$HD_Form -> FG_BUTTON_EDITION_BOTTOM_TEXT = $HD_Form -> FG_BUTTON_ADITION_BOTTOM_TEXT = gettext("Once you have completed the form above, click on the CONTINUE button.");


$HD_Form -> FG_GO_LINK_AFTER_ACTION_ADD = $_SERVER['PHP_SELF']."?atmenu=document&stitle=Document&wh=AC&id=";
$HD_Form -> FG_GO_LINK_AFTER_ACTION_EDIT = $_SERVER['PHP_SELF']."?atmenu=document&stitle=Document&wh=AC&id=";
$HD_Form -> FG_GO_LINK_AFTER_ACTION_DELETE = $_SERVER['PHP_SELF']."?atmenu=document&stitle=Document&wh=AC&id=";

