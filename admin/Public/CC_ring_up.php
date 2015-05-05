<?php

include ("../lib/admin.defines.php");
include ("../lib/admin.module.access.php");
include ("../lib/admin.smarty.php");

if (!has_rights(ACX_MAINTENANCE)) {
	Header("HTTP/1.0 401 Unauthorized");
	Header("Location: PP_error.php?c=accessdenied");
	die();
}

check_demo_mode();
$json_data = $_POST["json_data"];
if (isset($json_data))	extract(json_decode($json_data,true,512));
else getpost_ifset(array ('method', 'file', 'to', 'ringuptag', 'trunks', 'simult', 'weekdays', 'timefrom', 'timetill', 'id'));

# individual file size limit - in bytes (102400 bytes = 100KB)
$file_size_ind = (int) MY_MAX_FILE_SIZE_IMPORT;

# PHP.INI
# ; Maximum allowed size for uploaded files.
# upload_max_filesize = 8M

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
	$nb_imported = 0;
	$ringupexist = false;
	$fp = fopen($the_file, "r");
	if (!$fp) {
		$_SESSION['message'] .=  ". </font><font color=\"red\">".gettext('Error: Failed to open the file.');
	} else {
	    while (!feof($fp)) {
		$ligneoriginal = fgets($fp, 4096);
		$ligneoriginal = trim($ligneoriginal);
		$ligne = str_replace(array ( '"', "'" ), '', $ligneoriginal);
		$val = preg_split('/[;,]/', $ligne);
		if (strlen($val[0]) > 0)
		{
			if (!$ringupexist) {
				$trunks = implode(",",$trunks);
				$QUERY = "INSERT INTO cc_ringup (tag,trunks,simult) values ('$ringuptag','$trunks','$simult')";
				$result_query = @ $DBHandle->Execute($QUERY);
				$QUERY = "SELECT id FROM cc_ringup WHERE tag LIKE '$ringuptag'";
				$result_query = $instance_table -> SQLExec ($DBHandle, $QUERY);
				$id_ringup = $result_query[0][0];
				$ringupexist = true;
			}
			$QUERY = "INSERT INTO cc_ringup_list (id_ringup, tonum) values ('" . $id_ringup . "', '" . $val[0] . "')";
			$result_query = $DBHandle->Execute($QUERY);
			if ($result_query) {
				$nb_imported++;
			} else {
				$_SESSION['message'] .= '<br/>' . $ligneoriginal;
			}
		}
	    } // END WHILE EOF
	    if ($nb_imported) {
		$QUERY = "UPDATE cc_ringup SET lefte='$nb_imported' WHERE id='" . $id_ringup . "'";
		$result_query = @ $DBHandle->Execute($QUERY);
		$_SESSION['message'] .=  ". ".gettext('All data was import successfully.');
		for ($i=0;$i<count($weekdays);$i++) {
			if ($weekdays[$i] != '') {
			    $QUERY = "INSERT INTO `cc_sheduler_ratecard` (`id_ringup`,`weekdays`,`timefrom`,`timetill`) VALUES ('$id_ringup','{$weekdays[$i]}',SEC_TO_TIME('{$timefrom[$i]}'),SEC_TO_TIME('{$timetill[$i]}'))";
			    $result_query = @ $DBHandle->Execute($QUERY);
			}
		}
	    } else {
		$_SESSION['message'] .=  ". </font><font color=\"red\">".gettext('Data was absent.');
		$QUERY = "DELETE FROM cc_ringup WHERE id = '" . $id_ringup . "'";
		$result_query = @ $DBHandle->Execute($QUERY);
	    }
	}
    }

	//Delete the Ring-up List
} elseif ($method == "delete" && is_numeric($id)) {
	$_SESSION['message'] = "<img src=\"$dir_img/error.gif\" width=\"15\" height=\"15\">&nbsp;<b><font size=\"3\" color=\"red\">";
	$QUERY = "DELETE FROM cc_ringup WHERE id = ".$id;
	$result_query = @ $DBHandle->Execute($QUERY);
	if ($result_query) {
	    $QUERY = "DELETE FROM `cc_sheduler_ratecard` WHERE `id_ringup`='$id'";
	    $result_query = @ $DBHandle->Execute($QUERY);
	    $QUERY = "DELETE FROM `cc_ringup_list` WHERE `id_ringup`='$id'";
	    $result_query = @ $DBHandle->Execute($QUERY);
	    if ($result_query)
		$_SESSION['message'] = "<font size=\"3\" color=\"green\">" . "Ring-Up list deleted";
	    else
		$_SESSION['message'] .= "Ring-Up num list not deleted.";
	} else	$_SESSION['message'] .= "Ring-Up not deleted.";

	//StartStop ring-up
} elseif ($method == "startstop" && is_numeric($id)) {
	$QUERY = "UPDATE cc_ringup SET status=IF(status=0,1,0) WHERE id='" . $id . "'";
	$result_query = @ $DBHandle->Execute($QUERY);
}

$QUERY = "SELECT id_trunk, trunkcode FROM cc_trunk ORDER BY id_trunk";
$result = $instance_table -> SQLExec ($DBHandle, $QUERY);
$coltrunks = count($result);
$smarty->display('main.tpl');

$weekdays = '';
$WeekDaysList = Constants::getWeekDays();
foreach ($WeekDaysList as $key => $val) {
	$weekdays .= '"'.$val[0].'"';
	$weekdays .= ',';
}
$weekdays = substr($weekdays,0,-1);
?>
<script language="JavaScript" type="text/JavaScript">
	var WEEKDAYS = {"dows": [<?php echo $weekdays;?>]};
	var TIMEINTERVALLIST = {"format": "%HH%:%MM%",
                                "empt": "00:00",
                                "DaysText": "<?php echo gettext("Days")?>",
                                "HoursText": "<?php echo gettext("Hours")?>",
                                "MinText": "<?php echo gettext("Minutes")?>",
                                "daylabel": "Дн.",
                                "hourlabel": "час.",
                                "minlabel": "мин.",
                                "ResetTxt": "<?php echo gettext("Clear")?>",
                                "CancelTxt": "<?php echo gettext("Cancel")?>",
                                "OkTxt": " <?php echo gettext("Ok")?> ",
                                "hidedays": true};
</script>
<!-- Init jQuery, jQuery UI and jQuery PI -->
<link href="./javascript/jquery/jquery.pi_ctl.min.css" type="text/css" rel="stylesheet">
<script language="JavaScript" src="./javascript/jquery/jquery.pi_timeInterval.min.js"></script>
<script language="JavaScript" src="./javascript/jquery/jquery.pi_dowselect.min.js"></script>
<script language="JavaScript" src="./javascript/jquery/jquery-ui-1.8.20.custom.min.js"></script>
<link href="./javascript/jquery/jquery-ui-1.8.20.custom.css" media="screen" type="text/css" rel="stylesheet">

<!-- Init jqDynaForm -->
<link href="./javascript/jquery/jqDynaForm.css" media="screen" type="text/css" rel="stylesheet">
<script language="JavaScript" src="./javascript/jquery/jqDynaForm.js"></script>
<script language="JavaScript" src="./javascript/jquery/fakeData.js"></script>
<div id="popup"></div>
<div style="display:none">
    <div data-name="shedule">
	<input name="weekdays[]" />
	<style type="text/css">.span12{padding:3px 10px;}</style>
	&nbsp;&nbsp;&nbsp;
	<?php echo gettext("From")?>: <b><input name="timefrom[]" value="0" /></b>&nbsp;
	<?php echo gettext("To")?>: <b><input name="timetill[]" value="0" /></b>
    </div>
</div>





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
	if (form.ringuptag.value.length < 1){
		alert ('<?php echo addslashes(gettext("Please, you must first enter TAG !")); ?>');
		form.ringuptag.focus ();
		return (false);
	}
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
	if ((form.simult.value.length < 1) || (!IsNumeric(form.simult.value))){
		alert ('<?php echo addslashes(gettext("Please, you must first insert quantity of simultaneous channels !")); ?>');
		form.simult.focus ();
		return (false);
	}
	document.myForm.submit();
}
// -->
</script>

<center>
<table width="560" cellspacing="0" cellpadding="0" border="0" align="center">
  <tr>
    <td><font size="3"><b><i><?php echo gettext("Ring-Up Upload");?></i></b></font>&nbsp;</td>
  </tr>
</table>


<form name="myForm" method="POST" id="myForm" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF'];?>">
<input type="hidden" value="upload" name="method"/>
<table width="560" cellspacing="5" cellpadding="2" border="0" style="padding-top:5px;padding-left:5px;padding-bottom:5px;padding-right:5px">
         <tr>
           <td>TAG:</td><td><input class="form_input_text" id="ringuptag" name="ringuptag" size="30" maxlength="30"></td>
         </tr>
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
           <td nowrap>Simultaneous channels:</td><td><input class="form_input_text" id="simult" name="simult" size="10" maxlength="2"></td>
         </tr>
         <tr>
           <td>Sheduler:</td>
           <td>
		<div class="blockDyna">
		    <div data-holder-for="shedule"></div>
		</div>
		<div class="clear"></div>
           </td>
         </tr>
  <tr>
  </tr>
  <tr>
    <td colspan="2"><p align="center">
<!--    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $file_size_ind?>">
    <input type="hidden" name="task" value="upload">
    <input type="SUBMIT" value="<?php echo gettext("Upload");?>" class="form_input_button">&nbsp;
-->
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
<br><table cellspacing="1" cellpadding="1" border="0">
  <tr class="form_head">
    <td class="tableBody" style="padding: 2px;" align="center" width="22%"><?php echo gettext("TAG");?></td>
    <td class="tableBody" style="padding: 2px;" align="center" width="12%"><?php echo gettext("TRUNK");?></td>
    <td class="tableBody" style="padding: 2px;" align="center" width="10%"><?php echo gettext("CHANNELS");?></td>
    <td class="tableBody" style="padding: 2px;" align="center" width="11%"><?php echo gettext("PASSED");?></td>
    <td class="tableBody" style="padding: 2px;" align="center" width="11%"><?php echo gettext("LEFT");?></td>
    <td class="tableBody" style="padding: 2px;" align="center" width="16%"><?php echo gettext("STATUS");?></td>
    <td class="tableBody" style="padding: 2px;" align="center" width="18%" colspan="2"><?php echo gettext("ACTION");?></td>
  </tr>
  
  <?php

	$QUERY = "SELECT id, tag, trunks, simult, processed, lefte,
	IF(`id_ringup` IS NULL OR `status`!='1' OR (`weekdays` LIKE CONCAT('%',WEEKDAY(NOW()),'%') AND (CURTIME() BETWEEN `timefrom` AND `timetill`
	OR (`timetill`<=`timefrom` AND (CURTIME()<`timetill` OR CURTIME()>=`timefrom`)))),`status`,3), action FROM cc_ringup
	LEFT JOIN `cc_sheduler_ratecard` ON `id_ringup`=`id`
	ORDER BY `id` DESC";
	$result_query = $instance_table -> SQLExec ($DBHandle, $QUERY);
	if ($result_query) {
	    // EXPORT
	    $FG_EXPORT_SESSION_VAR = "pr_export_entity_ringup";
	    
	    // Query Preparation for the Export Functionality
	    $_SESSION [$FG_EXPORT_SESSION_VAR] = "SELECT tonum, channelstatedesc, attempt, try FROM cc_ringup_list WHERE id_ringup=";
	    
	    if (! is_null ( $order ) && ($order != '') && ! is_null ( $sens ) && ($sens != '')) {
		$_SESSION [$FG_EXPORT_SESSION_VAR] .= " ORDER BY id";
	    }
	    
	    for ($i = 0; $i < count($result_query); $i++) {
    ?>
		<tr>
			<td>&nbsp;<?php echo $result_query[$i][1];?></td>
			<td align="center"><?php echo $result_query[$i][2];?></td>
			<td align="center"><?php echo $result_query[$i][3];?></td>
			<td align="center"><?php echo $result_query[$i][4];?></td>
			<td align="center"><?php echo $result_query[$i][5];?></td>
			<td align="center"><?php if($result_query[$i][6]==1) echo gettext('IN PROGRESS'); elseif($result_query[$i][6]==0) echo gettext('STOPPED'); elseif($result_query[$i][6]==2) echo gettext('FINISHED'); elseif($result_query[$i][6]==3) echo gettext('TIME-OUT');?></td>
			<td align="center"><?php if($result_query[$i][6]!=2) {?>
				<A href="<?php echo $_SERVER['PHP_SELF'];?>?method=startstop&amp;id=<?php echo $result_query[$i][0];?>&<?php echo $pass_param?>"><div class="upload_button">&nbsp;<?php if($result_query[$i][6]==1 || $result_query[$i][6]==3) echo gettext('Stop'); elseif($result_query[$i][6]==0) echo gettext('Start');?>&nbsp;</div></a>
			<?php }?></td>
			<td align="left" nowrap>
				<A href="javascript:if(confirm('<?php echo gettext("Are you sure to delete ");?> <?php echo $entry;?>?')) location.href='<?php echo $_SERVER['PHP_SELF'];?>?method=delete&amp;id=<?php echo $result_query[$i][0];?>&<?php echo $pass_param?>';"><img src='<?php echo $dir_img?>/cross.gif' title='Delete <?php echo $entry;?>' alt='Delete <?php echo $entry;?>' border=0></a>
				<A href="export_csv.php?var_export=<?php echo $FG_EXPORT_SESSION_VAR?>&var_export_type=type_csv&filename=<?php echo str_replace(" ","_",$result_query[$i][1]);?>&id=<?php echo $result_query[$i][0];?>" target="_blank"><img src="<?php echo $dir_img?>/dl.gif" title="<?php echo gettext("Export CSV");?>" alt="<?php echo gettext("Export CSV");?>" border="0"></a>
<!--				<A href="javascript: var inserttext = ''; if(inserttext = prompt('Rename <?php echo $entry;?>. Fill in the new name for the file.','<?php echo $entry;?>')) location.href='<?php echo $_SERVER['PHP_SELF'];?>?method=rename&<?php echo $pass_param?>&amp;file=<?php echo $entry;?>&amp;to='+inserttext; "><img src='<?php echo $dir_img?>/edit.gif' alt='Rename <?php echo $entry;?>' border=0></a>
-->
			</td>
		</tr>
    <?php
       }   }
    ?>

</table></center>
<br><br>
<?php

$smarty->display('footer.tpl');
