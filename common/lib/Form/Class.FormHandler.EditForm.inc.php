<?php

$processed = $this->getProcessed();
$dynaform = $multform = false;
foreach ($this->FG_TABLE_EDITION as $sts) {
	if (stripos($sts[3],"POPUPDAYTIME")===0) {
		$WeekDaysList = Constants::getWeekDays();
		foreach ($WeekDaysList as $key => $val) {
			$weekdays .= '"'.$val[0].'"';
			$weekdays .= ',';
		}
		$weekdays = substr($weekdays,0,-1);
		if (!$dynaform) {
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
<?php
			$dynaform = true;
		}
?>
<div style="display:none">
    <div data-name="<?php echo $sts[1];?>">
	<input name="weekdays[]" />
	<style type="text/css">.span12{padding:3px 10px;}</style>
	&nbsp;&nbsp;&nbsp;
		<?php echo gettext("From")?>: <b><input name="timefrom[]" value="0" /></b>&nbsp;
		<?php echo gettext("To")?>: <b><input name="timetill[]" value="0" /></b>&nbsp;
		<?php if (substr($sts[3], -1) == "3") { ?>&nbsp;
		<?php echo gettext("Action every")?> <b><input class="form_input_text" name="inputa[]" value="10" size="4" maxlength="3" /></b>&nbsp;<font style="color:#BC2222"><?php echo gettext("sec")?></font>&nbsp;&nbsp;
		<?php echo gettext("Num calls per action")?>: <b><input class="form_input_text" name="inputb[]" value="1" size="4" maxlength="1" /></b>&nbsp;&nbsp;
		<?php echo gettext("Max duration, sec")?>: <b><input class="form_input_text" name="inputc[]" value="60" size="4" maxlength="3" /></b><?php
		      } ?> 
    </div>
</div>
<?php
	} elseif ($sts[3]=='MULTSELECT' && !$multform) {
?>
<script language="JavaScript" type="text/javascript">
<!--
function deselectHeaders(search_sources)
{
    sss = document.myForm["selected_" + search_sources];
    uss = document.myForm["unselected_" + search_sources];
    uss[0].selected = false;
    sss[0].selected = false;
}

function resetHidden(search_sources,sss)
{
    var tmp = '';
    for (i = 1; i < sss.length; i++) {
        tmp += sss[i].value;
        if (i < sss.length - 1)
            tmp += ",";
    }

    document.myForm[search_sources].value = tmp;
}

function addSource(search_sources)
{
    sss = document.myForm["selected_" + search_sources];
    uss = document.myForm["unselected_" + search_sources];
    for (i = 1; i < uss.length; i++) {
        if ((uss[i].selected) && (uss[i].style.display != 'none')) {
            ll = sss.length;
            sss[ll] = new Option(uss[i].text, uss[i].value);
            sss[ll].idx = i;
            uss[i].style.display = 'none';
        }
    }

    resetHidden(search_sources,sss);
}

function removeSource(search_sources)
{
    sss = document.myForm["selected_" + search_sources];
    uss = document.myForm["unselected_" + search_sources];
    for (i = 1; i < sss.length; i++) {
        if (sss[i].selected) {
            uss[sss[i].idx] = new Option(sss[i].text, sss[i].value);
            sss[i] = null;
            i--;
        }
    }

    resetHidden(search_sources,sss);
}

function moveSourceUp(search_sources)
{
    sss = document.myForm["selected_" + search_sources];
    var sel = sss.selectedIndex;

    if (sel == -1 || sss.length <= 2) return;

    // deselect everything but the first selected item
    sss.selectedIndex = sel;

    if (sel == 1) {
        tmp = sss[sel];
        sss[sel] = null;
        sss[sss.length] = tmp;
        sss.selectedIndex = sss.length - 1;
    } else {
        tmp = new Array();

        for (i = 1; i < sss.length; i++) {
            tmp[i - 1] = new Option(sss[i].text, sss[i].value);
            tmp[i - 1].idx = sss[i].idx;
        }

        for (i = 0; i < tmp.length; i++) {
            if (i + 1 == sel - 1) {
                sss[i + 1] = tmp[i + 1];
            } else if (i + 1 == sel) {
                sss[i + 1] = tmp[i - 1];
            } else {
                sss[i + 1] = tmp[i];
            }
        }

        sss.selectedIndex = sel - 1;
    }

    resetHidden(search_sources,sss);
}

function moveSourceDown(search_sources)
{
    sss = document.myForm["selected_" + search_sources];
    var sel = sss.selectedIndex;

    if (sel == -1 || sss.length <= 2) return;

    // deselect everything but the first selected item
    sss.selectedIndex = sel;

    if (sel == sss.length - 1) {
        tmp = new Array();

        for (i = 1; i < sss.length; i++) {
            tmp[i - 1] = new Option(sss[i].text, sss[i].value);
            tmp[i - 1].idx = sss[i].idx;
        }

        sss[1] = tmp[tmp.length - 1];
        for (i = 0; i < tmp.length - 1; i++) {
            sss[i + 2] = tmp[i];
        }

        sss.selectedIndex = 1;
    } else {
        tmp = new Array();

        for (i = 1; i < sss.length; i++) {
            tmp[i - 1] = new Option(sss[i].text, sss[i].value)
            tmp[i - 1].idx = sss[i].idx;
        }

        for (i = 0; i < tmp.length; i++) {
            if (i + 1 == sel) {
                sss[i + 1] = tmp[i + 1];
            } else if (i + 1 == sel + 1) {
                sss[i + 1] = tmp[i - 1];
            } else {
                sss[i + 1] = tmp[i];
            }
        }

        sss.selectedIndex = sel + 1;
    }

    resetHidden(search_sources,sss);
}


// -->
</script>
<?php
	    $multform = true;
	}
}
?>
<script language="JavaScript" src="./javascript/krutilke.js"></script>
<script language="JavaScript" src="./javascript/calonlydays.js"></script>
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

function sendto(action, record, field_inst, instance){
  //alert ("action:" + action);
  document.myForm.form_action.value = action;
  document.myForm.sub_action.value = record;
  if (field_inst != null) document.myForm.elements[field_inst].value = instance;
  document.myForm.submit();
}

function sendtolittle(direction){
  document.myForm.action=direction;
  document.myForm.submit();
}

function keyDownNumber(e,id_el,record,field_inst,instance)
{
 if(e.keyCode==13) {
   updatecontent(id_el,record,field_inst,instance)
   return false
 }
 var key = (typeof e.charCode == 'undefined' ? e.keyCode : e.charCode);
   if (e.ctrlKey || e.altKey || key < 58)  {
     document.getElementById(id_el).style.color = "blue";
     return true;
   }
     else  return false;
}

function updatecontent(id_el, record, field_inst, instance)
{
//  alert (document.getElementById(id_el).value + " is not valid.");
  document.myForm.percentage.value = document.getElementById(id_el).value;
  document.myForm.form_action.value = "update-content";
  document.myForm.sub_action.value = record;
  if (field_inst != null) document.myForm.elements[field_inst].value = instance;
  document.myForm.submit();
  document.getElementById(id_el).style.color = "#BC2222";
  return false;
}

//-->
</script>
<FORM action=<?php echo $_SERVER['PHP_SELF']?> method=post name="myForm" id="myForm">
	<INPUT type="hidden" name="id" value="<?php echo $id?>">
	<INPUT type="hidden" name="form_action" value="edit">
	<INPUT type="hidden" name="sub_action" value="">
	<INPUT type="hidden" name="atmenu" value="<?php echo $atmenu?>">
	<INPUT type="hidden" name="stitle" value="<?php echo $stitle?>">
	<INPUT type="hidden" name="current_page" value="<?php echo $processed['current_page'];?>">
	<INPUT type="hidden" name="order" value="<?php echo $processed['order'];?>">
	<INPUT type="hidden" name="sens" value="<?php echo $processed['sens'];?>">
	<INPUT type="hidden" name="percentage" value="">
<?php
	if (!empty($this->FG_QUERY_EDITION_HIDDEN_FIELDS)){
		$split_hidden_fields = preg_split("/,/",trim($this->FG_QUERY_EDITION_HIDDEN_FIELDS));
		$split_hidden_fields_value = preg_split("/,/",trim($this->FG_QUERY_EDITION_HIDDEN_VALUE));
		
		for ($cur_hidden=0;$cur_hidden<count($split_hidden_fields);$cur_hidden++){
			echo "	<INPUT type=\"hidden\" name=\"".trim($split_hidden_fields[$cur_hidden])."\" value=\"".trim($split_hidden_fields_value[$cur_hidden])."\">\n";
		}
	}
	
	if (!empty($this->FG_EDITION_HIDDEN_PARAM)){
		$split_hidden_fields = preg_split("/,/",trim($this->FG_EDITION_HIDDEN_PARAM));
		$split_hidden_fields_value = preg_split("/,/",trim($this->FG_EDITION_HIDDEN_PARAM_VALUE));
		
		for ($cur_hidden=0;$cur_hidden<count($split_hidden_fields);$cur_hidden++){
			echo "	<INPUT type=\"hidden\" name=\"".trim($split_hidden_fields[$cur_hidden])."\" value=\"".trim($split_hidden_fields_value[$cur_hidden])."\">\n";
		}
	}
?>
<table class="editform_table1" cellspacing="2">
<?php
	for($i=0;$i<$this->FG_NB_TABLE_EDITION;$i++) { 
		$pos = strpos($this->FG_TABLE_EDITION[$i][14], ':'); // SQL CUSTOM QUERY		
		if (strlen($this->FG_TABLE_EDITION[$i][16])>1) {
			echo '<TR><TD valign="top" bgcolor="#FEFEEE" colspan="2" class="tableBodyRight" ><i>';				
			echo $this->FG_TABLE_EDITION[$i][16];
			echo '</i></TD></TR>';
		}
		if (!$pos) {
?>		<TR>
<?php		if (!$this-> FG_fit_expression[$i]  &&  isset($this-> FG_fit_expression[$i]) ){
?>			<TD valign="middle" class="form_head_red">		<?php echo $this->FG_TABLE_EDITION[$i][0]?>		</TD>
			<TD valign="top" class="tableBodyRight" background="<?php echo Images_Path;?>/background_cells_red.gif" >
<?php		} else { ?>
			<TD valign="middle" class="form_head">		<?php echo $this->FG_TABLE_EDITION[$i][0]?>		</TD>
			<TD valign="top" class="tableBodyRight" background="<?php echo Images_Path;?>/background_cells.gif" >
<?php		}

			if ($this->FG_DEBUG == 1) print($this->FG_TABLE_EDITION[$i][3]);
				if(($this->FG_DISPLAY_SELECT == true) && (strlen($this->FG_SELECT_FIELDNAME)>0) && (strlen($list[0][$this->FG_SELECT_FIELDNAME])>0) && ($this->FG_CONF_VALUE_FIELDNAME == $this->FG_TABLE_EDITION[$i][1]))
				{
				$valuelist = explode(",", $list[0][$this->FG_SELECT_FIELDNAME]);
				
				?>
					<SELECT name=<?php echo "'{$this->FG_TABLE_EDITION[$i][1]}'"?> class="form_input_select">
					<?php 
					foreach($valuelist as $listval) {
					?>
					<option value=<?php echo "\"$listval\"";?> <?php  if($listval == $list[0][$i]) echo " selected";?>><?php echo $listval;?></option>
					<?php }?>
					</select>
				<?php
				}
		  		elseif (strtoupper ($this->FG_TABLE_EDITION[$i][3])==strtoupper ("INPUT"))
				{								
					if (isset ($this->FG_TABLE_EDITION[$i][15]) && strlen($this->FG_TABLE_EDITION[$i][15])>1){				
						$list[0][$i] = call_user_func($this->FG_TABLE_EDITION[$i][15], $list[0][$i]);
					}			
			  ?>
                        <INPUT 	
						class="form_input_text" 
						 <?php if(substr_count($this->FG_TABLE_EDITION[$i][4], "readonly") > 0){?>
						 style="background-color: #CCCCCC;" 
						 <?php }?> 
						name=<?php echo $this->FG_TABLE_EDITION[$i][1]?>  <?php echo $this->FG_TABLE_EDITION[$i][4]?> value="<?php if($this->VALID_SQL_REG_EXP){ echo stripslashes($list[0][$i]); }else{ echo $processed[$this->FG_TABLE_ADITION[$i][1]];  }?>"> 
                        <?php 
				}elseif (strtoupper ($this->FG_TABLE_EDITION[$i][3])==strtoupper ("LABEL"))
				{
					if (isset ($this->FG_TABLE_EDITION[$i][15]) && strlen($this->FG_TABLE_EDITION[$i][15])>1){
						$list[0][$i] = call_user_func($this->FG_TABLE_EDITION[$i][15], $list[0][$i]);
					}			
			  ?>  
                         <?php if($this->VALID_SQL_REG_EXP){ echo stripslashes($list[0][$i]); }else{ echo $processed[$this->FG_TABLE_ADITION[$i][1]];  }?>
                        <?php 
				}
				elseif (strtoupper ($this->FG_TABLE_EDITION[$i][3])=="POPUPVALUE") {
			?>
				<INPUT class="form_enter" name=<?php echo $this->FG_TABLE_EDITION[$i][1]?>  <?php echo $this->FG_TABLE_EDITION[$i][4]?> value="<?php
					if($this->VALID_SQL_REG_EXP && is_array($list)){
						echo stripslashes($list[0][$i]);
					}else{ echo $processed[$this->FG_TABLE_ADITION[$i][1]]; }?>">
                                	<a href="#" onclick="window.open('<?php echo $this->FG_TABLE_EDITION[$i][12]?>popup_formname=myForm&popup_fieldname=<?php echo $this->FG_TABLE_EDITION[$i][1]?>' <?php echo $this->FG_TABLE_EDITION[$i][13]?>);"><img src="<?php echo Images_Path_Main;?>/icon_arrow_orange.gif"/></a>
			 <?php
				}elseif (strtoupper ($this -> FG_TABLE_EDITION[$i][3])=="POPUPVALUETIME") {
                        ?>
                        <INPUT class="form_enter" name=<?php echo $this->FG_TABLE_EDITION[$i][1]?>  <?php echo $this->FG_TABLE_EDITION[$i][4]?> value="<?php if($this->VALID_SQL_REG_EXP){ echo stripslashes($list[0][$i]); }else{ echo $processed[$this->FG_TABLE_ADITION[$i][1]]; }?>">
                         <a href="#" onclick="window.open('<?php echo $this->FG_TABLE_EDITION[$i][12]?>formname=myForm&fieldname=<?php echo $this->FG_TABLE_EDITION[$i][1]?>' <?php echo $this->FG_TABLE_EDITION[$i][14]?>);"><img src="<?php echo Images_Path_Main;?>/icon_arrow_orange.gif"/></a>
                        <?php
				} elseif (strtoupper ($this->FG_TABLE_EDITION[$i][3])=="POPUPDATETIME") {
                        ?>
                         <INPUT class="form_enter" name=<?php echo $this->FG_TABLE_EDITION[$i][1]?>  <?php echo $this->FG_TABLE_EDITION[$i][4]?> value="<?php if($this->VALID_SQL_REG_EXP){ echo stripslashes($list[0][$i]); }else{ echo $processed[$this->FG_TABLE_ADITION[$i][1]]; }?>">
                          <a href="javascript:cal<?php echo $this->FG_TABLE_EDITION[$i][1]?>.popup();"><img src="<?php echo Images_Path_Main;?>/cal.gif" width="16" height="16" border="0" title="Click Here to Pick up the date" alt="Click Here to Pick up the date"></a>
                          <script language="JavaScript">
                         <!-- // create calendar object(s) just after form tag closed
                             // specify form element as the only parameter (document.forms['formname'].elements['inputname']);
                             // note: you can have as many calendar objects as you need for your application
                          var cal<?php echo $this->FG_TABLE_EDITION[$i][1]?> = new calendaronlyminutes(document.forms['myForm'].elements['<?php echo $this->FG_TABLE_EDITION[$i][1]?>']);
                          cal<?php echo $this->FG_TABLE_EDITION[$i][1]?>.year_scroll = false;
                          cal<?php echo $this->FG_TABLE_EDITION[$i][1]?>.time_comp = true;
                          cal<?php echo $this->FG_TABLE_EDITION[$i][1]?>.formatpgsql = true;
                          //-->
                          </script>
			<?php
				} elseif (stripos($this->FG_TABLE_EDITION[$i][3],"POPUPDAYTIME")===0) {
					$k = substr($this->FG_TABLE_EDITION[$i][3], -1);
					$instance_sub_table = new Table($this->FG_TABLE_EDITION[$i][8], $this->FG_TABLE_EDITION[$i][9]);
					$select_list = $instance_sub_table -> Get_list ($this->DBHandle, str_replace("%id", "$id", $this->FG_TABLE_EDITION[$i][10]), "ids", "DESC", null, null, null, null);
					if ($this->FG_DEBUG >= 2) { echo "<br>"; print_r($select_list);}
					//print_r($select_list); echo "<br>";
					if($this->VALID_SQL_REG_EXP) {
						if ($select_list !== 0) {
							$select_list = array_reverse($select_list);
							$json_list = array();
							if ($k=="3") {
								foreach ($select_list as $value) {
									$json_list[] = array('weekdays[]'=>$value[0],'timefrom[]'=>$value[1],'timetill[]'=>$value[2],'inputa[]'=>$value[3],'inputb[]'=>$value[4],'inputc[]'=>$value[5]);
								}
							} else {
								foreach ($select_list as $value) {
									$json_list[] = array('weekdays[]'=>$value[0],'timefrom[]'=>$value[1],'timetill[]'=>$value[2]);
								}
							}
							$json_str = json_encode($json_list);
							?>
							<script language="JavaScript"> PopUpDayTimeJson={"<?php echo $this->FG_TABLE_EDITION[$i][1];?>":<?php echo $json_str;?>}; </script>
							<?php
						} else {
						
						}
					} else {
						$json_str = json_encode($processed[$this->FG_TABLE_ADITION[$i][1]]);?>
						<script language="JavaScript"> PopUpDayTimeJson={"<?php echo $this->FG_TABLE_EDITION[$i][1]?>":<?php echo $json_str;?>}; </script><?php
						}?>
				    <div class="blockDyna">
				    <div data-holder-for="<?php echo $this->FG_TABLE_EDITION[$i][1];?>"></div>
				    </div>
				    <div class="clear"></div>
			<?php
				} elseif (strtoupper ($this->FG_TABLE_EDITION[$i][3])=="TEXTAREA") {
			  ?>
                     <textarea class="form_input_textarea" 
					 <?php if(substr_count($this->FG_TABLE_EDITION[$i][4], "readonly") > 0){?>
						 style="background-color: #CCCCCC;" 
						 <?php }?> 
					 name=<?php echo $this->FG_TABLE_EDITION[$i][1]?>  <?php echo $this->FG_TABLE_EDITION[$i][4]?>><?php if($this->VALID_SQL_REG_EXP){ echo stripslashes($list[0][$i]); }else{ echo $processed[$this->FG_TABLE_ADITION[$i][1]];  }?></textarea> 
				<?php	
		  		}elseif (strtoupper ($this->FG_TABLE_EDITION[$i][3])=="SPAN")
				{
			  ?>
                     <span name=<?php echo $this->FG_TABLE_EDITION[$i][1]?>  <?php echo $this->FG_TABLE_EDITION[$i][4]?>><?php if($this->VALID_SQL_REG_EXP){ echo stripslashes($list[0][$i]); }else{ echo $processed[$this->FG_TABLE_ADITION[$i][1]];  }?></span> 	 
                        <?php 	
				} elseif (strtoupper ($this->FG_TABLE_EDITION[$i][3])=="SELECT")	{
					if (strtoupper ($this->FG_TABLE_EDITION[$i][7])=="SQL") {
						$instance_sub_table = new Table($this->FG_TABLE_EDITION[$i][8], $this->FG_TABLE_EDITION[$i][9]);
						$select_list = $instance_sub_table -> Get_list ($this->DBHandle, str_replace("%id", "$id", $this->FG_TABLE_EDITION[$i][10]), null, null, null, null, null, null);
						if ($this->FG_DEBUG >= 2) { echo "<br>"; print_r($select_list);}
											
					} elseif (strtoupper ($this->FG_TABLE_EDITION[$i][7])=="LIST")
					{
						$select_list = $this->FG_TABLE_EDITION[$i][11];
						if ($this->FG_DEBUG >= 2) { echo "<br>"; print_r($select_list);}
					}
					 if ($this->FG_DEBUG >= 2) print_r ($list);			 
					 if ($this->FG_DEBUG >= 2) echo "<br>#$i<br>::>".$this->VALID_SQL_REG_EXP;
					 if ($this->FG_DEBUG >= 2) echo "<br><br>::>".$list[0][$i];
					 if ($this->FG_DEBUG >= 2) echo "<br><br>::>".$this->FG_TABLE_ADITION[$i][1];
			  		 ?>
						<SELECT name='<?php echo $this->FG_TABLE_EDITION[$i][1]?><?php if (strpos($this->FG_TABLE_EDITION[$i][4], "multiple")) echo "[]";?>' class="form_input_select" <?php echo $this->FG_TABLE_EDITION[$i][4]?>>
                        <?php
						echo ($this->FG_TABLE_EDITION[$i][15]);
						
						if (count($select_list)>0) {
							$select_number=0;
							foreach ($select_list as $select_recordset){
								$select_number++;
								$recordshow = "<OPTION  value='";
									if ($this->VALID_SQL_REG_EXP) {
										if (strpos($this->FG_TABLE_EDITION[$i][4], "multiple")) {
											$recordshow .= $select_recordset[1]."' ";
											if (intval($select_recordset[1]) & intval($list[0][$i]))
												$recordshow .= "selected";
										} else {
											$temprecord = explode(';', $list[0][$i]);
											if (strcmp($temprecord[0],$select_recordset[1])==0) {
												$recordshow .= $list[0][$i]."' selected";
											} else {
												$recordshow .= $select_recordset[1]."' ";
											}
										}
									} else {
										if (strpos($this->FG_TABLE_EDITION[$i][4], "multiple")) {
											$recordshow .= $select_recordset[1]."' ";
											if (is_array($processed[$this->FG_TABLE_EDITION[$i][1]]) && (intval($select_recordset[1]) & array_sum($processed[$this->FG_TABLE_EDITION[$i][1]])))
												$recordshow .= "selected";
										} else {
											$temprecord = explode(';', $processed[$this->FG_TABLE_EDITION[$i][1]]);
											if (strcmp($temprecord[0],$select_recordset[1])==0) {
												$recordshow .= $processed[$this->FG_TABLE_EDITION[$i][1]]."' selected";
											} else {
												$recordshow .= $select_recordset[1]."' ";
											}
										}
									}
									echo $recordshow.'> ';
									
									// CLOSE THE <OPTION
									if ($this->FG_TABLE_EDITION[$i][12] != "") {
										$value_display = $this->FG_TABLE_EDITION[$i][12];
										$nb_recor_k = count($select_recordset);
										for ($k=1;$k<=$nb_recor_k;$k++) {
											$value_display  = str_replace("%$k", $select_recordset[$k-1], $value_display );
										}
									} else {
										$value_display = $select_recordset[0];	
									}
									
									// DISPLAY THE VALUE
									echo $value_display;
									?>
								</OPTION>
<?php
			  				}// END_FOREACH
						}else{
							echo gettext("No data found !!!");
						}//END_IF
?>			</SELECT>
<?php
				} elseif (strtoupper ($this->FG_TABLE_EDITION[$i][3])=="DATALIST")	{
					if (strtoupper ($this->FG_TABLE_EDITION[$i][7])=="SQL") {
						$instance_sub_table = new Table($this->FG_TABLE_EDITION[$i][8], $this->FG_TABLE_EDITION[$i][9]);
						$select_list = $instance_sub_table -> Get_list ($this->DBHandle, str_replace("%id", "$id", $this->FG_TABLE_EDITION[$i][10]), null, null, null, null, null, null);
						if ($this->FG_DEBUG >= 2) { echo "<br>"; print_r($select_list);}
											
					} elseif (strtoupper ($this->FG_TABLE_EDITION[$i][7])=="LIST")
					{
						$select_list = $this->FG_TABLE_EDITION[$i][11];
						if ($this->FG_DEBUG >= 2) { echo "<br>"; print_r($select_list);}
					}
					 if ($this->FG_DEBUG >= 2) print_r ($list);			 
					 if ($this->FG_DEBUG >= 2) echo "<br>#$i<br>::>".$this->VALID_SQL_REG_EXP;
					 if ($this->FG_DEBUG >= 2) echo "<br><br>::>".$list[0][$i];
					 if ($this->FG_DEBUG >= 2) echo "<br><br>::>".$this->FG_TABLE_ADITION[$i][1];
					 $myname = $this->FG_TABLE_EDITION[$i][1];
					 if (strpos($this->FG_TABLE_EDITION[$i][4], "multiple"))
						$myname.= "[]";
					 ?>
					<INPUT type="text" list="list_<?php echo $myname;?>" name="<?php echo $myname;?>" class="form_input_text" <?php echo $this->FG_TABLE_EDITION[$i][4];?> value="<?php if($this->VALID_SQL_REG_EXP){ echo stripslashes($list[0][$i]); }else{ echo $processed[$this->FG_TABLE_ADITION[$i][1]];  }?>">
					<datalist id="list_<?php echo $myname;?>" autocomplete="off">
						<?php if ($this->FG_TABLE_EDITION[$i][15]) echo ($this->FG_TABLE_EDITION[$i][15]);
						elseif (array_search($list[0][$i],array_column($select_list,0))===false)
							array_unshift($select_list,array($list[0][$i],$list[0][$i]));
						if (count($select_list)>0) {
							$select_number=0;
							foreach ($select_list as $select_recordset){
								$select_number++;?>

						<OPTION value="<?php
								$recordshow = "";
								if ($this->VALID_SQL_REG_EXP) {
									if (strpos($this->FG_TABLE_EDITION[$i][4], "multiple")) {
										$recordshow .= $select_recordset[1]."\"";
									} else {
										$temprecord = explode(';', $list[0][$i]);
										if (strcmp($temprecord[0],$select_recordset[1])==0) {
											$recordshow .= $list[0][$i]."\"";
										} else {
											$recordshow .= $select_recordset[1]."\"";
										}
									}
								} else {
									if (strpos($this->FG_TABLE_EDITION[$i][4], "multiple")) {
										$recordshow .= $select_recordset[1]."\"";
									} else {
										$temprecord = explode(';', $processed[$this->FG_TABLE_EDITION[$i][1]]);
										if (strcmp($temprecord[0],$select_recordset[1])==0) {
											$recordshow .= $processed[$this->FG_TABLE_EDITION[$i][1]]."\"";
										} else {
											$recordshow .= $select_recordset[1]."\"";
										}
									}
								}
								echo $recordshow.'>';
								
								if ($this->FG_TABLE_EDITION[$i][12] != "") {
									$value_display = $this->FG_TABLE_EDITION[$i][12];
									$nb_recor_k = count($select_recordset);
									for ($k=1;$k<=$nb_recor_k;$k++) {
										$value_display  = str_replace("%$k", $select_recordset[$k-1], $value_display );
									}
								} else {
									$value_display = $select_recordset[0];	
								}
								// DISPLAY THE VALUE
								echo $value_display;
								// CLOSE THE <OPTION
?></OPTION><?php
							}// END_FOREACH
							preg_match('/"([^"]+)"/', $this->FG_TABLE_EDITION[$i][12], $m);
							if ($m[1]!="") $value_display = " value=\"".$m[1]."\"";
							else $value_display = "";
						}else{
							echo gettext("No data found !!!");
						}//END_IF
?>

					</datalist>
<?php
				} elseif (strtoupper ($this->FG_TABLE_EDITION[$i][3])=="RADIOBUTTON"){
						$radio_table = preg_split("/,/",trim($this->FG_TABLE_EDITION[$i][10]));
						foreach ($radio_table as $radio_instance){
							$radio_composant = preg_split("/:/",$radio_instance);
							echo $radio_composant[0];
							echo ' <input class="form_enter" type="radio" name="'.$this->FG_TABLE_EDITION[$i][1].'" value="'.$radio_composant[1].'" ';
							if($this->VALID_SQL_REG_EXP){ 
								$know_is_checked = stripslashes($list[0][$i]); 
							}else{ 
								$know_is_checked = $processed[$this->FG_TABLE_EDITION[$i][1]];  
							}
													
							if ($know_is_checked==$radio_composant[1]){
								echo "checked";
							}
							echo ">";
													
						}								
						//  Yes <input type="radio" name="digitalized" value="t" checked>
						//  No<input type="radio" name="digitalized" value="f">
						
				}//END_IF (RADIOBUTTON)
				elseif (strtoupper ($this->FG_TABLE_EDITION[$i][3])=="MULTSELECT"){
					if (strtoupper ($this->FG_TABLE_EDITION[$i][7])=="SQL") {
						$instance_sub_table = new Table($this->FG_TABLE_EDITION[$i][8], $this->FG_TABLE_EDITION[$i][9]);
						$unselect_list = $instance_sub_table -> Get_list ($this->DBHandle, str_replace("%id", "$id", $this->FG_TABLE_EDITION[$i][10]), null, null, null, null, null, null);
					} elseif (strtoupper ($this->FG_TABLE_EDITION[$i][7])=="LIST") {
						$unselect_list = $this->FG_TABLE_EDITION[$i][11];
					}
					$count_usl = count($unselect_list);
?>
		<input name="<?php echo $this->FG_TABLE_EDITION[$i][1];?>" value="<?php if($this->VALID_SQL_REG_EXP){ echo $list[0][$i]; }else{ echo $processed[$this->FG_TABLE_EDITION[$i][1]];  }?>" type="hidden">
<?php					if ($count_usl>0) {
?>		<table>
			<tr>
			<td>
			<SELECT name="unselected_<?php echo $this->FG_TABLE_EDITION[$i][1];?>" multiple="multiple" size="<?php if($count_usl<8){echo $count_usl+1;}else{echo 9;}?>" width="50" onchange="deselectHeaders(<?php echo "'{$this->FG_TABLE_EDITION[$i][1]}'";?>)" class="form_input_select">
				<OPTION value=""><?php echo gettext("Unselected Fields...");?></OPTION>
				<script language="JavaScript" type="text/JavaScript">
				<!--
				document.myForm.unselected_<?php echo $this->FG_TABLE_EDITION[$i][1];?>[0].style.color="#505050";
				//-->
				</script>
				<?php		$select_number=0;
						$select_list = Array();
						if ($this->VALID_SQL_REG_EXP) {
							$select_recordset = explode(',', $list[0][$i]);
						} else {
							$select_recordset = explode(',', $processed[$this->FG_TABLE_EDITION[$i][1]]);
						}
						foreach ($unselect_list as $unselect_recordset){
							$select_number++;
							$selected_item = $recordshow = "<OPTION  value=\"{$unselect_recordset[0]}\"";
							if ($this->FG_TABLE_EDITION[$i][12] != "") {
								$value_display = $this->FG_TABLE_EDITION[$i][12];
								$nb_recor_k = count($unselect_recordset);
								for ($k=1;$k<=$nb_recor_k;$k++) {
									$value_display  = str_replace("%$k", $unselect_recordset[$k-1], $value_display );
								}
							} else {
								$value_display = $unselect_recordset[0];
							}
							if (array_search($unselect_recordset[0], $select_recordset) !== false) {
								$selected_item .= " idx=\"{$select_number}\">".$value_display;
								$recordshow .= " style=\"display: none\"";
								$select_list[] = Array(array_search($unselect_recordset[0], $select_recordset), $selected_item, $select_number);
							}
							echo $recordshow.'> '.$value_display;
							?></OPTION>
				<?php		}
		      ?></SELECT>
			</td>
			<td>
			<a href="" onclick="addSource('<?php echo $this->FG_TABLE_EDITION[$i][1];?>'); return false;"><img src="<?php echo Images_Path;?>/forward.png" alt="add source" title="add source" border="0"></a>
			<br>
			<a href="" onclick="removeSource('<?php echo $this->FG_TABLE_EDITION[$i][1];?>'); return false;"><img src="<?php echo Images_Path;?>/back.png" alt="remove source" title="remove source" border="0"></a>
			</td>
			<td>
			<SELECT name="selected_<?php echo $this->FG_TABLE_EDITION[$i][1];?>" multiple="multiple" size="<?php if($count_usl<8){echo $count_usl+1;}else{echo 9;}?>" width="50" onchange="deselectHeaders('<?php echo $this->FG_TABLE_EDITION[$i][1];?>');" class="form_input_select">
				<OPTION value=""><?php echo gettext("Selected Fields...");?></OPTION>
				<script language="JavaScript" type="text/JavaScript">
				<!--
				document.myForm.selected_<?php echo $this->FG_TABLE_EDITION[$i][1];?>[0].style.color="#505050";
				//-->
				</script>
				<?php		$select_number=0;
						sort($select_list);
						foreach ($select_list as $selected_item){
							$select_number++;
							echo $selected_item[1];?></OPTION>
				<script language="JavaScript" type="text/JavaScript">
				<!--
				document.myForm.selected_<?php echo $this->FG_TABLE_EDITION[$i][1];?>[<?php echo $select_number;?>].idx=<?php echo $selected_item[2];?>;
				//-->
				</script>
				<?php		}
		      ?></SELECT>
			</td>
	
			<td>
			<a href="" onclick="moveSourceUp('<?php echo $this->FG_TABLE_EDITION[$i][1];?>'); return false;"><img src="<?php echo Images_Path;?>/up_black.png" alt="move up" title="move up" border="0"></a>
			<br>
			<a href="" onclick="moveSourceDown('<?php echo $this->FG_TABLE_EDITION[$i][1];?>'); return false;"><img src="<?php echo Images_Path;?>/down_black.png" alt="move down" title="move down" border="0"></a>
			</td>
			</tr>
		</table>
<?php					} else {
						echo gettext("No data found !!!");
					}//END_IF
?><?php
				}
				if (!$this-> FG_fit_expression[$i]  &&  isset($this-> FG_fit_expression[$i]) ){?>
			<span class="liens">
<?php
echo "		<br>".$this->FG_TABLE_EDITION[$i][6]." - ".$this->FG_regular[$this->FG_TABLE_EDITION[$i][5]][1];?>
			</span>
<?php					}
					if (strlen($this->FG_TABLE_COMMENT[$i])>1){
						if (strtoupper ($this->FG_TABLE_EDITION[$i][3])!="MULTSELECT")
echo "			<br/>";
echo $this->FG_TABLE_COMMENT[$i]?>&nbsp;<?php
					} ?>
			
			</TD>
		</TR>
<?php
					} else {
						
						if (strtoupper ($this->FG_TABLE_EDITION[$i][3])=="SELECT" || strtoupper ($this->FG_TABLE_EDITION[$i][3])=="DATALIST") {
							$table_split = preg_split("/:/",$this->FG_TABLE_EDITION[$i][14]);
?>		<TR>
						<!-- ******************** PARTIE EXTERN : SELECT ***************** -->
			<TD width="122" class="form_head" valign="top"><br><?php echo $this->FG_TABLE_EDITION[$i][0]?></TD>
			<TD align="center" valign="top" class="editform_table1_td1">
				<br>

						 	<!-- Table with list instance already inserted -->
				<table cellspacing="0" align=left class="editform_table2" <?php if (is_numeric($table_split[13])) {?> style=width:auto <?php }?> >
								<TR class="editform_table2_td1"> 
								<TD height=16 style="PADDING-LEFT: 5px; PADDING-RIGHT: 3px" class="form_head"> 
								  <TABLE border=0 cellPadding=0 cellSpacing=0 style="width: auto">
									  <TR> 
										<TD class="form_head"><?php echo $this->FG_TABLE_EDITION[$i][0]?> <?php echo gettext("LIST");?></TD>
									  </TR>
								  </TABLE></TD>
							  </TR>
							  <TR> 
								<TD> 
								<TABLE class="editform_table3" cellSpacing=0>
								<?php
								$SPLIT_CLAUSE = str_replace("%id", "$id", $table_split[4]);
	
								$instance_sub_table = new Table($table_split[2], $table_split[3]);
								$split_select_list = $instance_sub_table -> Get_list ($this->DBHandle, $SPLIT_CLAUSE, null, null, null, null, null, null);			
				
								if (!is_array($split_select_list)){	
									$num = 0;
								}else{	
									$num = count($split_select_list);
								}
		
								if($num>0) {
									for($j=0;$j<$num;$j++) {
										if (is_numeric($table_split[13])) $splitcurrent = $table_split[13] - 1;
										if (is_numeric($table_split[7])) {
										    $splitcurrent=$table_split[7];
										    $instance_sub_sub_table = new Table($table_split[8], $table_split[9]);
										    $SUB_TABLE_SPLIT_CLAUSE = str_replace("%1", $split_select_list[$j][$table_split[7]], $table_split[11] );
										    $sub_table_split_select_list = $instance_sub_sub_table -> Get_list ($this->DBHandle, $SUB_TABLE_SPLIT_CLAUSE, null, null, null, null, null, null);
										    if (isset($sub_table_split_select_list[0][0])) $split_select_list[$j][$table_split[7]] = $sub_table_split_select_list[0][0];
										}
								?>
								
                                  <TR class="" bgcolor="<?php echo $this->FG_TABLE_ALTERNATE_ROW_COLOR[$j%2]?>"  onMouseOver="bgColor='#C4FFD7'" onMouseOut="bgColor='<?php echo $this->FG_TABLE_ALTERNATE_ROW_COLOR[$j%2]?>'"> 
                                  <?php if (isset($split_select_list[$j][$splitcurrent])){?>
                                    <TD align="center" vAlign=top class=tableBody>
                                      <INPUT onClick="return updatecontent('<?php echo $table_split[1].$split_select_list[$j][1]?>','<?php echo $i?>','<?php echo $table_split[1]?>_hidden','<?php echo $split_select_list[$j][1]?>')" title="Save this <?php echo $this->FG_TABLE_EDITION[$i][0]?>" alt="Save this <?php echo $this->FG_TABLE_EDITION[$i][0]?>" border=0 height=11 hspace=2 src="<?php echo Images_Path_Main;?>/icon-save.gif" type=image width=33 value="Done" id=submit55 name=submit55\>
                                    </TD>
                                    <TD vAlign=top class=tableBody>
                                      <INPUT TYPE="TEXT" id='<?php echo $table_split[1].$split_select_list[$j][1]?>' class="form_input_text" style=font-weight:bold onkeypress="return keyDownNumber(event,id,'<?php echo $i?>','<?php echo $table_split[1]?>_hidden','<?php echo $split_select_list[$j][1]?>');" size=1 maxlength=3 value='<?php echo $split_select_list[$j][$splitcurrent]?>'\>
                                      <b><?php echo $table_split[12]?></b>
                                      &nbsp;
                                    </TD>				<?php }?>
                                    <TD vAlign=top class=tableBody>
                                      <font face="Verdana" size="2">
						<?php echo $split_select_list[$j][0]?>
                                      </font> </TD>
                                    <TD align="center" vAlign=top class=tableBodyRight> 
                                      <input onClick="sendto('del-content','<?php echo $i?>','<?php echo $table_split[1]?>_hidden','<?php echo $split_select_list[$j][1]?>');" title="Remove this <?php echo $this->FG_TABLE_EDITION[$i][0]?>" alt="Remove this <?php echo $this->FG_TABLE_EDITION[$i][0]?>" border=0 height=11 hspace=2 id=submit33 name=submit33 src="<?php echo Images_Path_Main;?>/icon-del.gif" type=image width=33 value="add-split"\>
                                    </TD>
                                  </TR>
                                  <?php  
                                  
                                  }//end_for
								} else { ?>
									
                                  <TR bgcolor="<?php echo $this->FG_TABLE_ALTERNATE_ROW_COLOR[$j%2]?>"  onMouseOver="bgColor='#C4FFD7'" onMouseOut="bgColor='<?php echo $this->FG_TABLE_ALTERNATE_ROW_COLOR[$j%2]?>'"> 
                                    <TD colspan="2" align="<?php echo $this->FG_TABLE_COL[$i][3]?>" vAlign=top class=tableBody> 
                                      <div align="center" class="liens"><?php echo gettext("No");?> <?php echo $this->FG_TABLE_EDITION[$i][0]?></div></TD>
                                  </TR>
                                  <?php } ?>
                              </TABLE></td>
                          </tr>
                          <TR class="bgcolor_016"> 
                            <TD class="editform_table3_td2" height="4"></TD>
                          </TR>
                        </table><br>
						</TD>
                    </TR>
                    <TR>
					  <!-- *******************   Select to ADD new instances  ****************************** -->					  					  
                      <TD class="form_head">&nbsp;</TD>
                      <TD align="center" valign="top" background="<?php echo Images_Path;?>/background_cells.gif" class="text">
                        <TABLE width="300" height=50 border=0 align="left" cellPadding=0 cellSpacing=0>
                            <TR> 
                            	<TD bgColor=#7f99cc colspan=3 height=16 style="PADDING-LEFT: 5px; PADDING-RIGHT: 5px" class="form_head">
									<TABLE border=0 cellPadding=0 cellSpacing=0 width="100%">
										<TR> 
											<TD class="form_head"><?php echo gettext("Add a new");?> <?php echo $this->FG_TABLE_EDITION[$i][0]?></TD>
										</TR>
									</TABLE>
								</TD>
                            </TR>
							
                            <TR> 
								<TD class="form_head"> <IMG height=1 src="<?php echo Images_Path_Main;?>/clear.gif" width=1>
								</TD>
								<TD class="editform_table4_td1"> 
                                
								<TABLE width="97%" border=0 align="center" cellPadding=0 cellSpacing=0>
									<TR> 
										<TD width="122" class="tableBody"><?php echo $this->FG_TABLE_EDITION[$i][0]?></TD>
										<TD width="516"><div align="center">
							 				<input name="<?php echo $table_split[1]?>_hidden" type="hidden" value="" />
                                          <SELECT name="<?php echo $table_split[1]?>[]" <?php echo $this->FG_TABLE_EDITION[$i][4]?> class="form_input_select">
                                            <?php
											 $SPLIT_CLAUSE = $split_select_list[0][1];
											 if($num>0) {
											   for($j=1;$j<$num;$j++) {
											     $SPLIT_CLAUSE .= ','.$split_select_list[$j][1];
														  }
												    } else $SPLIT_CLAUSE = 0;
											 $SUB_TABLE_SPLIT_CLAUSE = str_replace("%2", $SPLIT_CLAUSE, $table_split[15] );
											 $split_select_list = $instance_sub_table -> Get_list ($this->DBHandle, $SUB_TABLE_SPLIT_CLAUSE, $table_split[13], $table_split[14], null, null, null, null);
											 if (count($split_select_list)>0) {	
												 $select_number=0;
												 foreach ($split_select_list as $select_recordset) {
													 $select_number++;
													 if ($table_split[6]!="" && !is_null($table_split[6])) {
													 	if (is_numeric($table_split[7])) {
															$instance_sub_sub_table = new Table($table_split[8], $table_split[9]); 
															$SUB_TABLE_SPLIT_CLAUSE = str_replace("%1", $select_recordset[$table_split[7]], $table_split[11] );
															$sub_table_split_select_list = $instance_sub_sub_table -> Get_list ($this->DBHandle, $SUB_TABLE_SPLIT_CLAUSE, null,null, null, null, null, null);
															$select_recordset[$table_split[7]] = $sub_table_split_select_list[0][0];
														}
														
														$value_display = $table_split[6];
														$nb_recor_k = count($select_recordset);
														for ($k=1;$k<=$nb_recor_k;$k++) {
															$value_display  = str_replace("%$k", $select_recordset[$k-1], $value_display );
														}
													 } else {
													 	$value_display  = $select_recordset[0];
													 } ?>
						                            <OPTION  value='<?php echo $select_recordset[1]?>'>
						                            <?php echo $value_display?>
						                            </OPTION>
						                            <?php
												}// END_FOREACH
											}else{
												echo gettext("No data found !!!");
											}//END_IF
											?>
                                          </SELECT>
                                        </div>
										</TD>
                                    </TR>
									<TR>
                                      <TD colspan=2 height=4></TD>
                                    </TR>
                                    <TR>
                                    	<TD colspan="2" align="center" vAlign="middle">
											<a href="#" onClick="sendto('add-content','<?php echo $i?>');"> 
											<span class="cssbutton">ADD <?php echo $this->FG_TABLE_EDITION[$i][0]?></span></a>
										</TD>
                                    </TR>
                                </TABLE>
							</TD>
                            <TD class="form_head"><IMG height=1 src="<?php echo Images_Path_Main;?>/clear.gif" width=1></TD>
                            </TR>
                            <TR>
                            	<TD colspan=3 class="form_head"><IMG height=1 src="<?php echo Images_Path_Main;?>/clear.gif" width=1></TD>
                            </TR>
                        </TABLE>
                        </TD>
                    </TR>
					<?php 
						} elseif (strtoupper ($this->FG_TABLE_EDITION[$i][3])=="INSERT") {
							$table_split = preg_split("/:/",$this->FG_TABLE_EDITION[$i][14]);
					?>
					<TR>
					  <!-- ******************** PARTIE EXTERN : INSERT ***************** -->
						<TD width="122" class="form_head"><?php echo $this->FG_TABLE_EDITION[$i][0]?></TD>
                      	<TD align="center" valign="top" background="<?php echo Images_Path;?>/background_cells.gif" class="text"><br>
						
                        <!-- Table with list instance already inserted -->
                        <table cellspacing="0" class="editform_table2">
                          <TR bgcolor="#ffffff">
                            <TD height=16 style="PADDING-LEFT: 5px; PADDING-RIGHT: 3px" class="form_head">
                            	<TABLE border=0 cellPadding=0 cellSpacing=0 width="100%">
                                	<TR>
                                		<TD class="form_head"><?php echo $this->FG_TABLE_EDITION[$i][0]?>&nbsp;<?php echo gettext("LIST");?> </TD>
                                	</TR>
                            	</TABLE>
							</TD>
                          </TR>
                          <TR>
                            <TD>
								<TABLE border=0 cellPadding=0 cellSpacing=0 width="100%">
                                <?php
								$SPLIT_CLAUSE = str_replace("%id", "$id", $table_split[4]);
					
								$instance_sub_table = new Table($table_split[2], $table_split[3]);
								$split_select_list = $instance_sub_table -> Get_list ($this->DBHandle, $SPLIT_CLAUSE, null, null, null, null, null, null);
								
								if (!is_array($split_select_list)) {
									$num=0;
								} else {
									$num = count($split_select_list);
								}
								
								if($num>0) {
								for($j=0;$j<$num;$j++) {
								?>
                                  <TR bgcolor="<?php echo $this->FG_TABLE_ALTERNATE_ROW_COLOR[$j%2]?>"  onMouseOver="bgColor='#C4FFD7'" onMouseOut="bgColor='<?php echo $this->FG_TABLE_ALTERNATE_ROW_COLOR[$j%2]?>'">
                                    <TD vAlign="top" align="<?php echo $this->FG_TABLE_COL[$i][3]?>" class="tableBody">
                                      <font face="Verdana" size="2">
                                      <?php if(!empty($split_select_list[$j][$table_split[7]]))
                                      {
                                      ?>
                                      <b><?php echo $split_select_list[$j][$table_split[7]]?></b> : 
                                      <?php }?>
                                      <?php echo $split_select_list[$j][0]?>
                                      </font> </TD>
                                    <TD align="center" vAlign="top2" class="tableBodyRight">
                                      <input onClick="sendto('del-content','<?php echo $i?>','<?php echo $table_split[1]?>','<?php echo $split_select_list[$j][1]?>');" alt="Remove this <?php echo $this->FG_TABLE_EDITION[$i][0]?>" border=0 height=11 hspace=2 id=submit33 name=submit33 src="<?php echo Images_Path_Main;?>/icon-del.gif" type=image width=33 value="add-split">
                                    </TD>
                                  </TR>
                                <?php
								}//end_for
								}else{
								?>
                                  <TR bgcolor="<?php echo $this->FG_TABLE_ALTERNATE_ROW_COLOR[$j%2]?>"  onMouseOver="bgColor='#C4FFD7'" onMouseOut="bgColor='<?php echo $this->FG_TABLE_ALTERNATE_ROW_COLOR[$j%2]?>'">
                                    <TD colspan="2" align="<?php echo $this->FG_TABLE_COL[$i][3]?>" vAlign="top" class="tableBody">
                                      <div align="center" class="liens">No <?php echo $this->FG_TABLE_EDITION[$i][0]?></div></TD>
                                  </TR>
                                <?php
								}
								?>
                              </TABLE></td>
                          </tr>
                          <TR class="bgcolor_016"> 
                            <TD class="editform_table3_td2"> 
                            	<TABLE border=0 cellPadding=0 cellSpacing=0 width="100%">
                                	<TR><TD height="4" align="right"></TD></TR>
                              	</TABLE>
							</TD>
                          </TR>
                        </table><br>
						</TD>
                    </TR>
                    <TR>
					  <!-- *******************   Select to ADD new instances  ****************************** -->					  
                      <TD class="form_head">&nbsp;</TD>
                      <TD align="center" valign="top" background="<?php echo Images_Path;?>/background_cells.gif" class="text"><br>
                        <TABLE width="300" height=50 border=0 align="center" cellPadding=0 cellSpacing=0>
                            <TR> 
                            	<TD bgColor=#7f99cc colspan=3 height=16 style="PADDING-LEFT: 5px; PADDING-RIGHT: 5px" class="form_head">
									<TABLE border=0 cellPadding=0 cellSpacing=0 width="100%">
										<TR> 
											<TD class="form_head"><?php echo gettext("Add a new");?> <?php echo $this->FG_TABLE_EDITION[$i][0]?></TD>
										</TR>
									</TABLE>
								</TD>
                            </TR>
							
                            <TR> 
								<TD class="form_head"> <IMG height=1 src="<?php echo Images_Path_Main;?>/clear.gif" width=1>
								</TD>
								<TD class="editform_table4_td1">
								<TABLE width="97%" border=0 align="center" cellPadding=0 cellSpacing=0>
									<TR> 
										<TD width="122" class="tableBody"><?php echo $this->FG_TABLE_EDITION[$i][0]?></TD>
										<TD width="516"><div align="left"> 	
										<?php if($this->FG_TABLE_EDITION[$i][4] == "multiline"){?>
							  				<textarea name=<?php echo $table_split[1]?> class="form_input_text"  cols="40" rows="5"></textarea>
										<?php }else{?>
											<INPUT TYPE="TEXT" name=<?php echo $table_split[1]?> class="form_input_text"  size="20" maxlength="20">
										<?php }?>
										</TD>
                                    </TR>
                                    <TR> 
										<TD colspan="2" align="center">
											<a href="#" onClick="sendto('add-content','<?php echo $i?>');"> 
											<span class="cssbutton">ADD <?php echo $this->FG_TABLE_EDITION[$i][0]?></span></a>
										</TD>
                                    </TR>
                                    <TR> 
                                      <TD colspan=2 height=4></TD>
                                    </TR>
                                    <TR> 
                                      <TD colspan=2> <div align="right"></div></TD>
                                    </TR>
                                </TABLE>
								</TD>
								<TD class="form_head"><IMG height=1 src="<?php echo Images_Path_Main;?>/clear.gif" width=1>
								</TD>
                            </TR>
                            <TR> 
                              <TD colspan=3 class="form_head"><IMG height=1 src="<?php echo Images_Path_Main;?>/clear.gif" width=1></TD>
                            </TR>
                        </TABLE>
                        <br></TD>
                    </TR>					
					<?php  } elseif (strtoupper ($this->FG_TABLE_EDITION[$i][3])=="HAS_MANY") {
							$table_split = preg_split("/:/",$this->FG_TABLE_EDITION[$i][14]);
							$table_col = preg_split("/,/", $table_split[2]);
					?>
					<TR>
					  <!-- ******************** PARTIE EXTERN : HAS_MANY ***************** -->
                      	<TD width="122" class="form_head"><?php echo $this->FG_TABLE_EDITION[$i][0]?></TD>
                      	<TD align="center" valign="top" background="<?php echo Images_Path;?>/background_cells.gif" class="text"><br>
                        <!-- Table with list instance already inserted -->
                        <table cellspacing="0" class="editform_table2">
                          <TR bgcolor="#ffffff">
                            <TD height=16 style="PADDING-LEFT: 5px; PADDING-RIGHT: 3px" class="form_head">
                            	<TABLE border=0 cellPadding=0 cellSpacing=0 width="100%">
                                	<TR>
                                		<TD class="form_head"><?php echo $this->FG_TABLE_EDITION[$i][0]?>&nbsp;<?php echo gettext("LIST");?> </TD>
                                	</TR>
                            	</TABLE>
							</TD>
                          </TR>
                          <TR>
                            <TD>
								<TABLE border=0 cellPadding=0 cellSpacing=0 width="100%">
                                <?php
								$SPLIT_CLAUSE = str_replace("%id", "$id", $table_split[3]);
								$instance_sub_table = new Table($table_split[0], $table_split[2]);
								$split_select_list = $instance_sub_table -> Get_list ($this->DBHandle, $SPLIT_CLAUSE, null, null, null, null, null, null);
								if (!is_array($split_select_list)) {
									$num=0;
								} else {
									$num = count($split_select_list);
								}
								if($num>0) {
								for ($j=0;$j<$num;$j++) {
								?>
                                  <TR bgcolor="<?php echo $this->FG_TABLE_ALTERNATE_ROW_COLOR[$j%2]?>"  onMouseOver="bgColor='#C4FFD7'" onMouseOut="bgColor='<?php echo $this->FG_TABLE_ALTERNATE_ROW_COLOR[$j%2]?>'">
                                    <TD vAlign="top" align="<?php echo $this->FG_TABLE_COL[$i][3]?>" class="tableBody">
                                       <?php if(!empty($split_select_list[$j][$table_split[7]]))
                                      {
                                      ?>
                                      (
                                      <?php echo $split_select_list[$j][$table_split[7]]?> )
                                      <?php }?>
                                       <font face="Verdana" size="2">
                                      <?php echo $split_select_list[$j][0]?>
                                      </font> </TD>
                                    <TD align="center" vAlign="top2" class="tableBodyRight">
                                      <img onClick="sendto('del-content','<?php echo $i?>','<?php echo $table_col[0]?>','<?php echo $split_select_list[$j][0]?>');" alt="Remove this <?php echo $this->FG_TABLE_EDITION[$i][0]?>" border=0 height=11 hspace=2 id="del" name="del" src="<?php echo Images_Path_Main;?>/icon-del.gif" width=33 value="add-split">
                                    </TD>
                                  </TR>
                                  <?php
								  }//end_for
								}else{
								?>
                                  <TR bgcolor="<?php echo $this->FG_TABLE_ALTERNATE_ROW_COLOR[$j%2]?>"  onMouseOver="bgColor='#C4FFD7'" onMouseOut="bgColor='<?php echo $this->FG_TABLE_ALTERNATE_ROW_COLOR[$j%2]?>'">
                                    <TD colspan="2" align="<?php echo $this->FG_TABLE_COL[$i][3]?>" vAlign="top" class="tableBody">
                                      <div align="center" class="liens">No <?php echo $this->FG_TABLE_EDITION[$i][0]?></div></TD>
                                  </TR>
                               <?php }?>
                              </TABLE></td>
                          </tr>
                          <TR class="bgcolor_016"> 
                            <TD class="editform_table3_td2"> 
                            	<TABLE border=0 cellPadding=0 cellSpacing=0 width="100%">
                                	<TR><TD height="4" align="right"></TD></TR>
                              	</TABLE>
							</TD>
                          </TR>
                        </table><br>
						</TD>
                    </TR>
                    <TR>
					  <!-- *******************   Select to ADD new instances  ****************************** -->					  
                      <TD class="form_head">&nbsp;</TD>
                      <TD align="center" valign="top" background="<?php echo Images_Path;?>/background_cells.gif" class="text"><br>
                        <TABLE width="300" height=50 border=0 align="center" cellPadding=0 cellSpacing=0>
                            <TR> 
                            	<TD bgColor=#7f99cc colspan=3 height=16 style="PADDING-LEFT: 5px; PADDING-RIGHT: 5px" class="form_head">
									<TABLE border=0 cellPadding=0 cellSpacing=0 width="100%">
										<TR> 
											<TD class="form_head"><?php echo gettext("Add a new");?> <?php echo $this->FG_TABLE_EDITION[$i][0]?></TD>
										</TR>
									</TABLE>
								</TD>
                            </TR>
							
                            <TR> 
								<TD class="form_head"> <IMG height=1 src="<?php echo Images_Path_Main;?>/clear.gif" width=1>
								</TD>
								<TD class="editform_table4_td1"> 
                                
								<TABLE width="97%" border=0 align="center" cellPadding=0 cellSpacing=0>
									<TR> 
										<TD width="122" class="tableBody"><?php echo $this->FG_TABLE_EDITION[$i][0]?></TD>
										<TD width="516"><div align="left"> 	
										<?php if($this->FG_TABLE_EDITION[$i][4] == "multiline"){?>
							  				<textarea name=<?php echo $table_col[0]?> class="form_input_text"  cols="40" rows="5"></textarea>
										<?php }else{?>
											<INPUT TYPE="TEXT" name=<?php echo $table_col[0]?> class="form_input_text"  size="20" maxlength="20">
										<?php }?>
										</TD>
                                    </TR>
                                    <TR> 
										<TD colspan="2" align="center">									  	
											<a href="#" onClick="sendto('add-content','<?php echo $i; ?>');"> <span class="cssbutton">ADD <?php echo $this->FG_TABLE_EDITION[$i][0]?></span></a>
										</TD>
                                    </TR>
                                    <TR>
                                      <TD colspan=2 height=4></TD>
                                    </TR>
                                    <TR> 
                                      <TD colspan=2> <div align="right"></div></TD>
                                    </TR>
                                </TABLE>
								</TD>
								<TD class="form_head"><IMG height=1 src="<?php echo Images_Path_Main;?>/clear.gif" width=1>
								</TD>
                            </TR>
                            <TR> 
                              <TD colspan=3 class="form_head"><IMG height=1 src="<?php echo Images_Path_Main;?>/clear.gif" width=1></TD>
                            </TR>
                        </TABLE>
                        <br></TD>
                    </TR>					
					<?php  } elseif (strtoupper ($this->FG_TABLE_EDITION[$i][3])=="CHECKBOX") {
							
							$table_split = preg_split("/:/",$this->FG_TABLE_EDITION[$i][14]);
					?>
					<TR> 
					 <!-- ******************** PARTIE EXTERN : CHECKBOX ***************** -->
                     
 					 <td class="editform_table5_td1">
					 	<table width="100%" border="0" cellpadding="2" cellspacing="0" class="form_text">
                   		<tr>
                        	<td width="122"><?php echo $this->FG_TABLE_EDITION[$i][0]?></td>
                        </tr>
						</table>
					</td>
					<td valign="top" class="editform_table5_td2">
					    
	<?php 
	$SPLIT_CLAUSE = str_replace("%id", "$id", $table_split[4]);

	$instance_sub_table = new Table($table_split[2], $table_split[3]);
	$split_select_list = $instance_sub_table -> Get_list ($this->DBHandle, $SPLIT_CLAUSE, null, null, null, null, null, null);			
	if (!is_array($split_select_list)) {	
		$num=0;
	} else {
		$num = count($split_select_list);
	}
	
	 ////////////////////////////////////////////////////////////////////////////////////////////////////////

	 $table_split[12] = str_replace("%id", "$id", $table_split[12]);
	 $split_select_list_tariff = $instance_sub_table -> Get_list ($this->DBHandle, $table_split[12], null, null, null, null, null, null);
	 if (count($split_select_list_tariff)>0) {
			 $select_number=0;
			  ?>				
			  <TABLE class="editform_table6" cellSpacing=0>
				<TR> 
                	<TD colspan=3 class="editform_table6_td1">
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td class="editform_table7_td1"><font > <?php echo $this->FG_TABLE_COMMENT[$i]?></font></td>
							</tr>
                        </table>
					</TD>
				</TR>
                <TR> 
                	<TD class="form_head"> <IMG height=1 src="<?php echo Images_Path_Main;?>/clear.gif" width=1>
                    </TD>
                    <TD class="editform_table4_td1"> 
						<TABLE width="97%" border=0 align="center" cellPadding=0 cellSpacing=0>
                        
 <?php 
 	foreach ($split_select_list_tariff as $select_recordset){ 
		$select_number++;
		
		if ($table_split[6]!="" && !is_null($table_split[6])) {
			
			if (is_numeric($table_split[7])){
				$instance_sub_sub_table = new Table($table_split[8], $table_split[9]);
				$SUB_TABLE_SPLIT_CLAUSE = str_replace("%1", $select_recordset[$table_split[7]], $table_split[11] );
				$sub_table_split_select_list_tariff = $instance_sub_sub_table -> Get_list ($this->DBHandle, $SUB_TABLE_SPLIT_CLAUSE, null, null, null, null, null, null);
				$select_recordset[$table_split[7]] = $sub_table_split_select_list_tariff[0][0];
			}													 
			$value_display = $table_split[6];
			$nb_recor_k = count($select_recordset);
			for ($k=1;$k<=$nb_recor_k;$k++){
				$value_display  = str_replace("%$k", $select_recordset[$k-1], $value_display );
			}
		} else {
			$value_display  = $select_recordset[0];
		}
		
		$checked_tariff=false;
		if($num>0) {
		for($j=0;$j<$num;$j++)
		{
			if ($select_recordset[1]==$split_select_list[$j][1]) $checked_tariff=true;
		}
	}
?>
			<TR>
				<TD class="tableBody"><input type="checkbox" name="<?php echo $table_split[0]?>[]" value="<?php echo $select_recordset[1]?>" <?php if ($checked_tariff) echo"checked";?>></TD>
				<TD class="text_azul">&nbsp; <?php echo $value_display?></TD>
			</TR>
<?php }// END_FOREACH?>
                         <TR><TD colspan=2 height=4>
				<span class="liens">
					<?php
				if (!$this-> FG_fit_expression[$i]  &&  isset($this-> FG_fit_expression[$i]) ){
					echo "<br>".$this->FG_TABLE_EDITION[$i][6];
				}
		  ?>
					</span>
				</TD></TR>
                                </TABLE></TD>
                              <TD class="form_head"><IMG height=1 src="<?php echo Images_Path_Main;?>/clear.gif" width=1>
                              </TD>
                            </TR>
                            <TR>
                              <TD colspan="3" class="form_head"><IMG height=1 src="<?php echo Images_Path_Main;?>/clear.gif" width=1></TD>
                            </TR>
                        </TABLE>

			  <?php
	  		} else {
				echo gettext("No data found !!!");
	  }?>

					 </TD>
                    </TR>
                    <?php   	  }// end if if (strtoupper ($this->FG_TABLE_EDITION[$i][3])=="SELECT")
							}// end if pos
			}//END_FOR ?>
				               
	<TR><TD colspan="2" nowrap>
	  <TABLE cellspacing="0" class="editform_table8">
		<tr>
			<td width="50%"><span class="tableBodyRight"><?php echo $this->FG_BUTTON_EDITION_BOTTOM_TEXT?></span></td>
            <td width="50%" align="right" valign="top" class="text">
				<input value=" <?php echo $this->FG_EDIT_PAGE_CONFIRM_BUTTON; ?> " class="form_input_button" type="SUBMIT" id="submit4">
			</td>
		</tr>
	  </TABLE>
	 </TD></TR>
              </TABLE>
</FORM>
