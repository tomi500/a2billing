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

if (isset($idcust)) {
	if (!empty($idcust) && $idcust > 0) {
		$table_agent_security = new Table("cc_card ", " id_diller, username");
		$clause_agent_security = "id= " . $idcust;
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

$HD_Form -> FG_EDITION_LINK = $_SERVER['PHP_SELF']."?form_action=ask-edit&popup_select=$popup_select&idcust=";

if ($idcust != "" || !is_null($idcust)) {
	$HD_Form->FG_EDITION_CLAUSE = str_replace("%id", "$idcust", $HD_Form->FG_EDITION_CLAUSE);
}

if (!isset($form_action))
	$form_action = "list"; //ask-add
if (!isset ($action))
	$action = $form_action;
//echo "form_action2=".$form_action.'<br>'
//    ."account=".$account.'<br>';

if ($message != "success")
	$list = $HD_Form->perform_action($form_action);

// #### HEADER SECTION
$smarty->display('main.tpl');

	$QUERY = "SELECT lastname, firstname FROM cc_card WHERE id = $idcust AND id_diller = " . $_SESSION["card_id"];
	$resmax = $DBHandle->Execute($QUERY);
	if ($resmax) {
		$row = $resmax->fetchRow();
		$HD_Form -> FG_INTRO_TEXT_EDITION = "<u>".$row[0]." ".$row[1]."</u>";
		if ($form_action != "ask-edit") {
			?><center><?php
			echo $HD_Form -> FG_INTRO_TEXT_EDITION;
		}
		$HD_Form -> FG_INTRO_TEXT_EDITION = "<B>".$HD_Form -> FG_INTRO_TEXT_EDITION."</B>";
	}

// #### TOP SECTION PAGE
$HD_Form->create_toppage($form_action);

if ($message == "success") {
?>
<table width="95%" align="center">
<tr height="100px">
<td align="center"><b><?php echo gettext("Information has successfully been updated")?></b></td>
</tr>
</table>
<br>
<form runat="server">
    <div>
        <input id="button1" onclick="self.close()" class="form_input_button" type="button" value="" />
    </div>
</form>
<script type="text/javascript">
    objbutton=document.getElementById('button1');
    objbutton.focus();
    timeleft=10;
    function buttontimer(){
	timeleft--;
	if(timeleft==0) {
		objbutton.click();
	}
	objbutton.value = '<?php echo gettext("Close Window")?> ('+timeleft+')';
    }
    buttontimer();
    window.opener.location.reload();
    window.resizeBy(-100, -450);
    setInterval(function() {buttontimer()}, 1000);
</script>

<?php
} else {
	$HD_Form -> create_form ($form_action, $list, $id=null) ;
}
