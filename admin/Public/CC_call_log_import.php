<?php

include ("../lib/admin.defines.php");
include ("../lib/admin.module.access.php");
include ("../lib/Class.RateEngine.php");
include ("../lib/admin.smarty.php");

if (!has_rights(ACX_MAINTENANCE)) {
	Header("HTTP/1.0 401 Unauthorized");
	Header("Location: PP_error.php?c=accessdenied");
	die();
}

check_demo_mode();
$json_data = $_POST["json_data"];
if (isset($json_data)) {
    $json_data = stripslashes($json_data);
    extract(json_decode($json_data,true,512));
} else {
    getpost_ifset(array ('method', 'file', 'to', 'ringuptag', 'trunks', 'prefix2import', 'weekdays', 'timefrom', 'timetill', 'id'));
}

# individual file size limit - in bytes (102400 bytes = 100KB)
$file_size_ind = (int) MY_MAX_FILE_SIZE_IMPORT;

# PHP.INI
# ; Maximum allowed size for uploaded files.
# upload_max_filesize = 64M

# the images directory
$dir_img = "templates/default/images";

// -------------------------------- //
//     SCRIPT UNDER THIS LINE!      //
// -------------------------------- //

function getlast($toget) {
	$pos = strrpos($toget, ".");
	$lastext = substr($toget, $pos +1);
	return $lastext;
}

$DBHandle = DbConnect();
$instance_table = new Table();

if ($method)
	$_SESSION['message'] = "";
if ($method == 'upload') {
	$_SESSION['message'] = "<img src=\"$dir_img/error.gif\" width=\"15\" height=\"15\">&nbsp;<b><font size=\"3\" color=\"red\">";
	$the_file_name = $_FILES['the_file']['name'];
	$the_file_type = $_FILES['the_file']['type'];
	$the_file = $_FILES['the_file']['tmp_name'];
//echo var_export($_FILES, true)."<br>";
//echo var_export($trunks, true)."<br>";
//echo var_export($weekdays, true)."<br>";
//exit ();
	$uploaded = false;
//	$_SESSION['message'] = '';
	if (count($_FILES) > 0) {
//		session_register('message');
		$errortext = validate_upload($the_file, $the_file_type);
		if ($errortext != "" || $errortext != false) {
			echo $errortext;
			exit;
		}
		if (stripos($the_file_type, 'csv') === false) {
			$_SESSION['message'] .= gettext("ERROR: your file type is not allowed") . " (" . getlast($the_file_name) . "), " . gettext("or you didn't specify a file to upload");
		} else {
			if ($_FILES['the_file']['size'] > $file_size_ind) {
				$_SESSION['message'] .= gettext("ERROR: please get the file size less than") . " " . $file_size_ind . " BYTES  (" . round(($file_size_ind / 1024), 2) . " KB)";
			} else {
				$new_filename = "/tmp/" . MDP(6) . ".csv";
				if (file_exists($new_filename)) {
					$_SESSION['message'] .=  $new_filename . " already exists. ";
				} else {
					if (!move_uploaded_file($the_file, $new_filename)) {
						$_SESSION['message'] .=  gettext("File Save Failed, FILE=" . $new_filename);
					} else {
						$_SESSION['message'] = "<font size=\"3\" color=\"green\">" . $the_file_name . " " . gettext("uploaded");
						$uploaded = true;
					}
				}
				$the_file = $new_filename;
			}
		}
	} else {
		$_SESSION['message'] .=  gettext("File Upload Failed");
	}
	if ($FG_DEBUG == 1)
		echo "<br> THE_FILE:$the_file <br>THE_FILE_TYPE:$the_file_type";
	
    if ($uploaded) {
	$nb_imported = $nb_failed = $nb_notfocused = 0;
	$fp = fopen($the_file, "r");
	if (!$fp) {
		$_SESSION['message'] .=  ". </font><font color=\"red\">".gettext('Error: Failed to open the file.');
	} else {
		$A2B -> DBHandle = DbConnect();
//		$instance_table = new Table();
		$A2B -> set_instance_table ($instance_table);
		$RateEngine = new RateEngine();
		$RateEngine -> webui			= 0;
		$RateEngine -> pos_dialingnumber	= false;
		$RateEngine -> dialstatus		= "ANSWER";

		$A2B->agiconfig['use_dnid']		= 1;
		$A2B->agiconfig['say_timetocall']	= 0;
		$A2B->agiconfig['lcr_mode']		= 1;
		$A2B->agiconfig['min_duration_2bill']	= 1;
		$A2B->recalltime			= false;
		$A2B->CallerIDext			= false;
		$A2B->hostname = $A2B->dnid		= "";
		$A2B->nbused				= 1;
		$A2B->cid_verify			= true;
		$removeprefix = explode(",",$prefix2import);
//		$trunks0 = implode(",",$trunks);
	    while (!feof($fp)) {
		$ligneoriginal = fgets($fp, 4096);
		$ligneoriginal = trim($ligneoriginal);
		$ligne = str_replace(array ( '"', "'" ), '', $ligneoriginal);
		$val = preg_split('/[;,]/', $ligne);
		if (strlen($val[3]) > 0)
		{
			
			$A2B->uniqueid = strtotime($val[0]);
			$A2B->channel = "SIP/".$A2B->uniqueid;
			$RateEngine -> real_answeredtime = $RateEngine -> answeredtime	= (int) $val[1];
			$A2B -> CallerID						= (string) $val[2];
			$destination							= (string) $val[3];
			$buycost							= (float) $val[4];

//			$sellerprefix = NULL; // QuickCom
			$sellerprefix = '00'; // VoipPro
			if (is_array($removeprefix) && count($removeprefix)>0) {
				foreach ($removeprefix as $testprefix) {
					if (substr($destination,0,strlen($testprefix))==$testprefix) {
						$destination  = substr($destination,strlen($testprefix));
						$sellerprefix = (string) $testprefix;
						break;
					}
				}
			}
			$A2B -> destination = $A2B->oldphonenumber = $destination;



			$QUERY = "SELECT id_cc_card  FROM cc_callerid WHERE cid LIKE '{$A2B->CallerID}' LIMIT 1";
			$result = $instance_table -> SQLExec ($DBHandle, $QUERY);
			if (!is_array($result) || count($result) == 0) {
				$QUERY = "SELECT card_id FROM cc_call WHERE calledstation LIKE '{$destination}' ORDER BY id DESC LIMIT 1";
				$result = $instance_table -> SQLExec ($DBHandle, $QUERY);
				if (is_array($result) && count($result) > 0) {
					$A2B -> id_card		= $result[0][0];
				} else	$A2B -> id_card		= 6;

			} else		$A2B -> id_card		= $result[0][0];
			
			$QUERY = "SELECT id_diller, username, credit, tariff, margin FROM cc_card WHERE id='{$A2B->id_card}' LIMIT 1";
			$result = $instance_table -> SQLExec ($DBHandle, $QUERY);
			$A2B -> id_diller				= $result[0][0];
			$A2B -> username = $A2B -> accountcode		= $result[0][1];
			$A2B -> credit					= $result[0][2];
			$A2B -> tariff					= $result[0][3];
			$A2B -> margin					= $result[0][4];
			$A2B -> card_caller = $A2B -> card_id		= $A2B -> id_card;

			$A2B->agiconfig['accountcode']=$A2B->cardnumber =  $A2B->accountcode;

//				if (!$A2B->callingcard_ivr_authenticate_light($error_msg)) {

//				}

					$A2B -> margintotal			= $A2B->margin_calculate($A2B->id_card);
					
					$A2B -> callingcard_ivr_authenticate_light ($error_msg);
					$resfindrate = $RateEngine->rate_engine_findrates($A2B, $destination, $A2B -> tariff);
		                        // IF FIND RATE
					if ($resfindrate != 0) {
//						$res_all_calcultimeout = $RateEngine->rate_engine_all_calcultimeout($A2B, $A2B->credit);
			                        $RateEngine -> usedratecard = 0;
			                        for ($k=0;$k<count($RateEngine -> ratecard_obj);$k++) {
			                                if ($RateEngine -> ratecard_obj[$k][74] == $sellerprefix && $RateEngine -> ratecard_obj[$k][29] == $trunks[0]) {
			                                        $RateEngine -> usedratecard = $k;
			                                        break;
			                                }
			                        }
						$RateEngine -> usedtrunk = $RateEngine -> ratecard_obj[$RateEngine->usedratecard][29];
//                            			$res_all_calcultimeout = $RateEngine->rate_engine_all_calcultimeout($A2B, $A2B->credit);
						$RateEngine -> rate_engine_updatesystem ($A2B, NULL, $destination, 1, 0, 0, $RateEngine -> usedtrunk, NULL, 0, $val[0], $buycost);
						if ($A2B -> id_card == 6)	$nb_notfocused++;
						else				$nb_imported++;
					} else $nb_failed++;
			
		} //else $nb_failed++;
	    } // END WHILE EOF
	    $_SESSION['message'] .=  ". ".$nb_imported." imported. ".$nb_notfocused." not focused. ".$nb_failed." failed."."trunk=".$RateEngine -> usedtrunk." k=".$RateEngine -> usedratecard." / Trunk0=".$trunks[0]." / Trunk1=".$trunks[1]." / Trunk2=".$trunks[2];
	}
    }

	
}

$QUERY = "SELECT id_trunk, trunkcode FROM cc_trunk ORDER BY id_trunk";
$result = $instance_table -> SQLExec ($DBHandle, $QUERY);
$coltrunks = count($result);
$smarty->display('main.tpl');

?>

<script language="JavaScript" type="text/javascript">
<!--
function IsNumeric(sText)
{
	var ValidChars = "0123456789";
	var IsNumber=true;
	var Char;
	var len=sText.length;

	if (len == 0) IsNumber=false;
	else
	for (i = 0; i < len && IsNumber == true; i++)
	{
		Char = sText.charAt(i);
		if (ValidChars.indexOf(Char) == -1)
		{
			IsNumber = false;
		}
	}
return IsNumber;
}

function sendtoupload(form){
	if (form.the_file.value.length < 2){
		alert ('<?php echo addslashes(gettext("Please, you must first select a file !")); ?>');
		form.the_file.focus ();
		return (false);
	}
	if (form.elements["trunks[]"].selectedIndex == -1){
		alert ('<?php echo addslashes(gettext("Please, you must first select atleast one Trunk !")); ?>');
		form.elements["trunks[]"].focus ();
		return (false);
	}
	if (form.prefix2import.value.length < 1){
		alert ('<?php echo addslashes(gettext("Please, you must first enter at least one Dialprefix !")); ?>');
		form.prefix2import.focus ();
		return (false);
	}
	document.myForm.submit();
}
// -->
</script>

<center>
<table width="560" cellspacing="0" cellpadding="0" border="0" align="center">
  <tr>
    <td><font size="3"><b><i><?php echo gettext("Call-Log (CDR) Upload");?></i></b></font>&nbsp;</td>
  </tr>
</table>


<form name="myForm" method="POST" id="myForm" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF'];?>">
<input type="hidden" value="upload" name="method"/>
<table width="560" cellspacing="5" cellpadding="2" border="0" style="padding-top:5px;padding-left:5px;padding-bottom:5px;padding-right:5px">
         <tr>
           <td>File Name:</td><td>
<!--           <input type='file' name='file[]' class="upload_textfield" size="30"> -->
           <input name="the_file" type="file" size="50" class="saisie1">
           </td>
         </tr>
  <tr>
    <td><?php echo gettext("File size limit");?>:</td>
	<td>
		<b><?php 
			if ($file_size_ind >= 1048576) {
				$file_size_ind_rnd = round(($file_size_ind/1024000),3) . " MB";
			} elseif ($file_size_ind >= 1024) {	
				$file_size_ind_rnd = round(($file_size_ind/1024),2) . " KB";
			} elseif ($file_size_ind >= 0) {
				$file_size_ind_rnd = $file_size_ind . " bytes";
			} else {
				$file_size_ind_rnd = "0 bytes";
			}
			
			echo "$file_size_ind_rnd";
		?></b>
	</td>
  </tr>
         <tr>
           <td>Trunks:</td>
           <td>
        	<select name="trunks[]" multiple="multiple" size="<?php if ($coltrunks<10) echo $coltrunks; else echo 10;?>" width="40" class="form_input_select">
 <?php for( $i = 0; $i < $coltrunks; $i++ ) { ?>
        	<option value="<?php echo $result[$i][0]?>"><?php echo $result[$i][0].'-'.$result[$i][1]?></option>
    <?php } ?>
        	</select>
           </td>
         </tr>
         <tr>
           <td nowrap>Dialprefixes to import:</td><td><input class="form_input_text" id="prefix2import" name="prefix2import" size="20" maxlength="20 value="<?php if(strlen($prefix2import)>0) echo $prefix2import;?>"></td>
         </tr>
  <tr>
  </tr>
  <tr>
    <td colspan="2"><p align="center">
    <input type="button" value="<?php echo gettext("Upload");?>" class="form_input_button" name="submit1" onClick="sendtoupload(this.form);">&nbsp;
    <input type="reset" value="<?php echo gettext("Clear");?>" class="form_input_button">
    </p></td>
  </tr>
</table>
</form>

      <?php
        //When there is a message, after an action, show it
        if($_SESSION['message'])
        {
          echo $_SESSION['message'] . "</font></b>";
        }
      ?>
</center>
<br><br>
<?php

$smarty->display('footer.tpl');
