#!/usr/bin/php -q
<?php

/***************************************************************************
 *            a2billing_invoice_cront.php
 *
 *  13 April 2007
 *  Purpose: To generate invoices and for Each User.
 *  Copyright  2007  User : Belaid Arezqui
 *  ADD THIS SCRIPT IN A CRONTAB JOB
 *
 *  The sample above will run the script every day of each month at 6AM
	crontab -e
	0 6 1 * * php /usr/local/a2billing/Cronjobs/a2billing_invoice_cront.php
	
	
	field	 allowed values
	-----	 --------------
	minute	 0-59
	hour		 0-23
	day of month	 1-31
	month	 1-12 (or names, see below)
	day of week	 0-7 (0 or 7 is Sun, or use names)
	
****************************************************************************/

set_time_limit(0);
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
//dl("pgsql.so"); // remove "extension= pgsql.so !

include (dirname(__FILE__) . "/lib/admin.defines.php");
include (dirname(__FILE__) . "/lib/ProcessHandler.php");

if (!defined('PID')) {
	define("PID", "/tmp/a2billing_batch_billing_pid.php");
}

// CHECK IF THE CRONT PROCESS IS ALREADY RUNNING
if (ProcessHandler :: isActive()) {
	// Already running!
	die();
} else {
	ProcessHandler :: activate();
	// run the cront
}

//Flag to show the debuging information
$verbose_level = 1;

$groupcard = 5000;

$A2B = new A2Billing();
$A2B->load_conf($agi, NULL, 0, $idconfig);

write_log(LOGFILE_CRONT_INVOICE, basename(__FILE__) . ' line:' . __LINE__ . "[#### CRONT BILLING BEGIN ####]");

if (!$A2B->DbConnect()) {
	echo "[Cannot connect to the database]\n";
	write_log(LOGFILE_CRONT_INVOICE, basename(__FILE__) . ' line:' . __LINE__ . "[Cannot connect to the database]");
	exit;
}

$instance_table = new Table();

// CHECK COUNT OF CARD ON WHICH APPLY THE SERVICE
$QUERY = 'SELECT count(*) FROM cc_card';

$result = $instance_table->SQLExec($A2B->DBHandle, $QUERY);
$nb_card = $result[0][0];
$nbpagemax = (ceil($nb_card / $groupcard));

if ($verbose_level >= 1)
	echo "===> NB_CARD : $nb_card - NBPAGEMAX:$nbpagemax\n";

if (!($nb_card > 0)) {
	if ($verbose_level >= 1)
		echo "[No card to run the Invoice Billing Service]\n";
	write_log(LOGFILE_CRONT_INVOICE, basename(__FILE__) . ' line:' . __LINE__ . "[No card to run the Invoice Billing service]");
	exit ();
}

if ($verbose_level >= 1)
	echo ("[Invoice Billing Service analyze cards on which to apply service]");
write_log(LOGFILE_CRONT_INVOICE, basename(__FILE__) . ' line:' . __LINE__ . "[Invoice Billing Service analyze cards on which to apply service]");

for ($page = 0; $page < $nbpagemax; $page++) {
	if ($verbose_level >= 1)
		echo "$page <= $nbpagemax \n";
	$Query_Customers = "SELECT id, vat, invoiceday ,typepaid,credit FROM cc_card ";

	if ($A2B->config["database"]['dbtype'] == "postgres") {
		$Query_Customers .= " LIMIT $groupcard OFFSET " . $page * $groupcard;
	} else {
		$Query_Customers .= " LIMIT " . $page * $groupcard . ", $groupcard";
	}

	$resmax = $instance_table->SQLExec($A2B->DBHandle, $Query_Customers);

	if (is_array($resmax)) {
		$numrow = count($resmax);
		if ($verbose_level >= 2)
			print_r($resmax[0]);
	} else {
		$numrow = 0;
	}

	if ($numrow == 0) {
		if ($verbose_level >= 1)
			echo "\n[No card to run the Invoice Billing Service]\n";
		write_log(LOGFILE_CRONT_INVOICE, basename(__FILE__) . ' line:' . __LINE__ . "[No card to run the Invoice Billing service]");
		exit ();

	} else {

		foreach ($resmax as $Customer) {
			$invoiceday = (is_numeric($Customer['invoiceday']) && $Customer['invoiceday'] >= 1 && $Customer['invoiceday'] <= 28) ? $Customer['invoiceday'] : 1;
			if ($verbose_level >= 1)
				echo "\n Invoiceday = $invoiceday  -  Invoiceday db = " . $Customer['invoiceday'];

			// the value of invoiceday is between 1..28, dont make sense to bill customer on 29, 30, 31
			if (date("j", time()) != $invoiceday) {
				if ($verbose_level >= 1)
					echo "\n We dont create an invoice today for this customer : " . $Customer['invoiceday'];
				continue;
			}
			
			//find the last billing 
			$card_id = $Customer['id'];
			$date_now = date("Y-m-d");
			if(empty($Customer['vat'])||!is_numeric($Customer['vat'])) $vat=0;
			else $vat = $Customer['vat'];
			
			
			
			// FIND THE LAST BILLING
			$billing_table = new Table('cc_billing_customer','id,date');
			$clause_last_billing = "id_card = ".$card_id;
			$result = $billing_table -> Get_list($A2B->DBHandle, $clause_last_billing,"date","desc");
			$call_table = new Table('cc_call',' COALESCE(SUM(sessionbill),0)' );
			$clause_call_billing ="card_id = ".$card_id." AND ";
			$clause_charge = "id_cc_card = ".$card_id." AND ";
			$desc_billing="";
			$desc_billing_postpaid="";
			$start_date =null;
			if(is_array($result) && !empty($result[0][0])){
				$clause_call_billing .= "stoptime >= '" .$result[0][1]."' AND "; 
				$clause_charge .= "creationdate >= '".$result[0][1]."' AND  ";
				$desc_billing = "Calls cost between the ".$result[0][1]." and ".$date_now ;
				$desc_billing_postpaid="Amount for periode between the ".date("Y-m-d",strptime($result[0][1]))." and ".$date_now;
				$start_date = $result[0][1];
			}else{
				$desc_billing = "Calls cost before the ".$date_now ;
				$desc_billing_postpaid="Amount for periode before the ".$date_now ;
			}
			//insert billing
			$field_insert = "id_card";
			$value_insert = " '$card_id'";
			if(!empty($start_date)){
				$field_insert.= ", start_date";
				$value_insert .= ", '$start_date'";
			}
			$instance_table = new Table("cc_billing_customer", $field_insert);
			$id_billing = $instance_table -> Add_table ($A2B->DBHandle, $value_insert, null, null,"id");
			
			$clause_call_billing .= "stoptime < '".$date_now."' ";
			$clause_charge .= "creationdate < '".$date_now."' ";
			$result =  $call_table -> Get_list($A2B->DBHandle, $clause_call_billing);
			// COMMON BEHAVIOUR FOR PREPAID AND POSTPAID ... GENERATE A RECEIPT FOR THE CALLS OF THE MONTH
			if(is_array($result) && is_numeric($result[0][0])){
				$amount_calls = $result[0][0];
				$amount_calls = ceil($amount_calls*100)/100;
				/// create receipt
				$field_insert = "id_card, title, description,status";
				$title = gettext("SUMMARY OF CALLS");
				$description = gettext("Summary of the calls charged since the last billing");
				$value_insert = "  '$card_id', '$title','$description',1";
				$instance_table = new Table("cc_receipt", $field_insert);
				$id_receipt = $instance_table -> Add_table ($A2B->DBHandle, $value_insert, null, null,"id");
				if(!empty($id_receipt)&& is_numeric($id_receipt)){
					$description = $desc_billing;
					$field_insert = " id_receipt,price,description,id_ext,type_ext";
					$instance_table = new Table("cc_receipt_item", $field_insert);
					$value_insert = " '$id_receipt', '$amount_calls','$description','".$id_billing."','CALLS'";
					$instance_table -> Add_table ($A2B->DBHandle, $value_insert, null, null,"id");
				}
				
			}	
			// GENERATE RECEIPT FOR CHARGE ALREADY CHARGED 
			$table_charge = new Table("cc_charge", "*");
			$result =  $table_charge -> Get_list($A2B->DBHandle, $clause_charge." AND charged_status = 1");
			if(is_array($result)){
				$field_insert = " id_card, title, description,status";
				$title = gettext("SUMMARY OF CHARGE");
				$description = gettext("Summary of the charge charged since the last billing.");
				$value_insert = " '$card_id', '$title','$description',1";
				$instance_table = new Table("cc_receipt", $field_insert);
				$id_receipt = $instance_table -> Add_table ($A2B->DBHandle, $value_insert, null, null,"id");
				if(!empty($id_receipt)&& is_numeric($id_receipt)){
					foreach ($result as $charge) {
						$description = gettext("CHARGE :").$charge['description'];
						$amount = $charge['amount'];
						$field_insert = "date, id_receipt,price,description,id_ext,type_ext";
						$instance_table = new Table("cc_receipt_item", $field_insert);
						$value_insert = " '".$charge['creationdate']."' , '$id_receipt', '$amount','$description','".$charge['id']."','CHARGE'";
						$instance_table -> Add_table ($A2B->DBHandle, $value_insert, null, null,"id");
					}
				}
			}
			// GENERATE INVOICE FOR CHARGE NOT YET CHARGED
			$table_charge = new Table("cc_charge", "*");
			$result =  $table_charge -> Get_list($A2B->DBHandle, $clause_charge." AND charged_status = 0 AND invoiced_status = 0");
			if(is_array($result) && sizeof($result)>0){
				$reference = generate_invoice_reference();
				$field_insert = "id_card, title ,reference, description,status,paid_status";
				$title = gettext("BILLING CHARGES");
				$description = gettext("This invoice is for some charges unpaid since the last billing.")." ".$desc_billing_postpaid;
				$value_insert = " '$card_id', '$title','$reference','$description',1,0";
				$instance_table = new Table("cc_invoice", $field_insert);
				$id_invoice = $instance_table -> Add_table ($A2B->DBHandle, $value_insert, null, null,"id");
				if(!empty($id_invoice)&& is_numeric($id_invoice)){
					foreach ($result as $charge) {
						$description = gettext("CHARGE :").$charge['description'];
						$amount = $charge['amount'];
						$field_insert = "date, id_invoice,price,vat,description,id_ext,type_ext";
						$instance_table = new Table("cc_invoice_item", $field_insert);
						$value_insert = " '".$charge['creationdate']."' , '$id_invoice', '$amount','$vat','$description','".$charge['id']."','CHARGE'";
						$instance_table -> Add_table ($A2B->DBHandle, $value_insert, null, null,"id");
					}
				}
			
			}
			
		// behaviour postpaid
		if($Customer['typepaid']==1 && is_numeric($Customer['credit']) && $Customer['credit']<0){
			//GENERATE AN INVOICE TO COMPLETE THE BALANCE
			$reference = generate_invoice_reference();
			$field_insert = " id_card, title ,reference, description,status,paid_status";
			$title = gettext("BILLING POSTPAID");
			$description = gettext("Invoice for POSTPAID");
			$value_insert = " '$card_id', '$title','$reference','$description',1,0";
			$instance_table = new Table("cc_invoice", $field_insert);
			$id_invoice = $instance_table -> Add_table ($A2B->DBHandle, $value_insert, null, null,"id");
			if(!empty($id_invoice)&& is_numeric($id_invoice)){
				$description = $desc_billing_postpaid;
				$amount = abs($Customer['credit']);
				$field_insert = " id_invoice,price,vat,description,id_ext,type_ext";
				$instance_table = new Table("cc_invoice_item", $field_insert);
				$value_insert = " '$id_invoice', '$amount','$vat','$description','".$id_billing."','POSTPAID'";
				$instance_table -> Add_table ($A2B->DBHandle, $value_insert, null, null,"id");
			}
		}
		
		} // END foreach($resmax as $Customer)
	}
}