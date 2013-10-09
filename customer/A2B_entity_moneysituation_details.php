<?php

include ("lib/customer.defines.php");
include ("lib/customer.module.access.php");
include ("lib/Form/Class.FormHandler.inc.php");
include ("./form_data/FG_var_moneysituation_details.inc");
include ("lib/customer.smarty.php");

if (!has_rights(ACX_DISTRIBUTION)) {
	Header("HTTP/1.0 401 Unauthorized");
	Header("Location: PP_error.php?c=accessdenied");
	die();
}

if ($atmenu == "") $atmenu = "SIP";

$DBHandle = DbConnect();

if (isset($id)) {
	if (!empty($id) && $id > 0) {
		$table_agent_security = new Table("cc_card ", " id_diller, username");
		$clause_agent_security = "id= " . $id;
		$result_security = $table_agent_security->Get_list($DBHandle, $clause_agent_security, null, null, null, null, null, null);
		if ($result_security[0][0] != $_SESSION['card_id']) {
			Header("Location: A2B_entity_moneysituation.php?section=14");
			die();
		}
		$account = $result_security[0][1];
	}
}

$HD_Form->setDBHandler($DBHandle);

$HD_Form->init();

if ($id != "" || !is_null($id)) {
	$HD_Form->FG_EDITION_CLAUSE = str_replace("%id", "$id", $HD_Form->FG_EDITION_CLAUSE);
}

if (!isset($form_action))
	$form_action = "list"; //ask-add
if (!isset ($action))
	$action = $form_action;
//echo "form_action2=".$form_action.'<br>'
//    ."account=".$account.'<br>';

$list = $HD_Form->perform_action($form_action);

// #### HEADER SECTION
$smarty->display('main.tpl');


// #### TOP SECTION PAGE
$HD_Form->create_toppage($form_action);

//if ($atmenu != 'card') {
	$QUERY = "SELECT lastname, firstname FROM cc_card WHERE id = $id AND id_diller = " . $_SESSION["card_id"];
	$resmax = $DBHandle->Execute($QUERY);
	if ($resmax) {
		$row = $resmax->fetchRow();
?><center><?php
		echo $row[0]." ".$row[1];
	}

//}

$HD_Form->create_form($form_action, $list, $id = null);
