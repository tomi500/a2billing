<?php
/*
Result :
    Rate checker web service

Parameters :
    u : Customer's SIP name
    p : Customer's SIP secret
    t : Destination number, as it is dialed. This parameter will probably be always present.

Usage :
    https://localhost/customer/rate.php?u=XXXXXXXXXXX&p=XXXXXXXXXXX&t=1234567890
    https://localhost/customer/rate.php?u=XXXXXXXXXXX&t=1234567890                <<== Will have returned rate in xml with authenticate by request ip instead password
or in Acrobits apps:
    https://localhost/customer/rate.php?u=%account[username]%&p=%account[password]%&t=%targetNumber%
    https://localhost/customer/rate.php?u=%account[username]%&t=%targetNumber%    <<== Will have returned rate in xml with authenticate by request ip instead password
*/

include ("lib/customer.defines.php");
include ("lib/Class.RateEngine.php");

getpost_ifset(array('u', 'p', 't'));

if (!isset($u)) $u='';
if (!isset($p)) $p='';
if (!isset($t)) $t='';

$rate = Service_Get_RateInitial($u, $p, $t);

$A2B -> DbDisconnect();

echo "<response>\n";
echo "<error>0</error>\n";
echo "<callRateString>";
echo $rate[2]." ";
if (strpos($rate[2],"ERROR")===false) {
    display_money_nocur($rate[0]);
    if ($rate[1]>$rate[0]) {
	echo "-";
	display_money_nocur($rate[1]);
    }
    echo " / min";
} else {
    echo $rate[0];
}
echo "</callRateString>\n";
//echo "<messageRateString>Your Best Rate</messageRateString>\n";
echo "</response>\n";

function Service_Get_RateInitial($accountnumber, $password, $called)
{
global $A2B;
    $A2B -> DBHandle = DbConnect();
    if (!$A2B -> DBHandle) {
        write_log(LOGFILE_API_CALLBACK, basename(__FILE__).' line:'.__LINE__." ERROR CONNECT DB");
        return array('500', '500', 'ERROR - CONNECT DB');
    }
    $instance_table = new Table();
    $A2B->set_instance_table($instance_table);

    $res = $A2B->instance_table->SQLExec($A2B->DBHandle, "SELECT cc.id, tariff, credit, currency, cc.username, status, creditlimit FROM cc_card cc LEFT JOIN cc_sip_buddies ON cc.id=id_cc_card WHERE name LIKE '$accountnumber' AND (secret LIKE '$password' OR ipaddr LIKE '".$_SERVER['REMOTE_ADDR']."') LIMIT 1");

    if (!is_array($res) || count($res) == 0) {
        return array('400', '400', 'ERROR - AUTHENTICATE CODE');
    }
    $cardid		= $res[0][0];
    $tariffplan 	= $res[0][1];
    $balance		= $res[0][2];
    if ($balance<0)
	$balance       += $res[0][6];
    $currency		= $res[0][3];
    $A2B->cardnumber	= $res[0][4];
    $A2B->credit	= $balance;
    $status		= $res[0][5];

    if (!$cardid || $cardid <= 0 || !($status == 1 || $status == 8)) {
        return array('400', '400', 'ERROR - AUTHENTICATE CODE');
    }

    $rate1 = $rate2 = $destination = '';

    if ($called && $cardid) {
	if (strlen($called) > 2 && is_numeric($called)) {
		$currencies_list = get_currencies();
		if (!isset($currencies_list[strtoupper($currency)][2]) || !is_numeric($currencies_list[strtoupper($currency)][2])) {
			$mycur = 1;
		} else {
			$mycur = $currencies_list[strtoupper($currency)][2];
		}
		if ($A2B->callingcard_ivr_authenticate_light($error_msg)) {
			$RateEngine = new RateEngine();
			$RateEngine->webui = 0;
			$A2B->agiconfig['accountcode'] = $A2B->cardnumber;
			$A2B->agiconfig['use_dnid'] = 1;
			$A2B->agiconfig['say_timetocall'] = 0;
			$A2B->agiconfig['lcr_mode'] = 1;
			$A2B->dnid = $A2B->destination = $called;
			if ($A2B->removeinterprefix)
				$A2B->destination = $A2B->apply_rules($A2B->destination);
			$A2B->destination = $A2B->apply_add_countryprefixto($A2B->destination);
			$resfindrate = $RateEngine->rate_engine_findrates($A2B, $A2B->destination, $tariffplan);
			// IF FIND RATE
			if ($resfindrate != 0) {
			    $A2B->margintotal	= $A2B->margin_calculate($cardid);
			    for($j=0;$j<count($RateEngine->ratecard_obj);$j++){
				if ($RateEngine->ratecard_obj[$j][70]=="-INFOLINE-")	break;
				$rateinitial =  round($A2B->margintotal * $RateEngine->ratecard_obj[$j][12] / $mycur, 5);
				if ($rate1=='') {
				    $rate1 = $rate2 = $rateinitial;
				} else {
				    if ($rate1>$rateinitial) $rate1 = $rateinitial;
				    if ($rate2<$rateinitial) $rate2 = $rateinitial;
				}
				if ($RateEngine->ratecard_obj[$j][7] != '') {
				    $res = $A2B->instance_table -> SQLExec ($A2B -> DBHandle, "SELECT destination FROM cc_prefix where prefix='".$RateEngine->ratecard_obj[$j][5]."'");
				    if (is_array($res))
					$destination = $res[0][0];
				}
				if (strpos($RateEngine->ratecard_obj[$j][7],"_")===0 && ($j+1==count($RateEngine->ratecard_obj) || strpos($RateEngine->ratecard_obj[$j+1][7],"_")===false))
				    break;
			    }
			}
		}
	}
    }
    if ($destination) $currency = $destination." = ".$currency;
    return array($rate1, $rate2, $currency);
}
