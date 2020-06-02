<?php
/*
Result :
    Get the Customer's Balance

Parameters :
    u : Customer's SIP name
    p : Customer's SIP secret
    html=1 : to display with <pre> tag
    html=xml : to return format for the balance checker is a simple XML

Usage :
    https://localhost/customer/balance.php?u=XXXXXXXXXXX&p=XXXXXXXXXXX&html=1
    https://localhost/customer/balance.php?u=XXXXXXXXXXX&p=XXXXXXXXXXX&html=xml
    https://localhost/customer/balance.php?u=XXXXXXXXXXX           <<== Will have returned balance in xml with authenticate by request ip instead password
    In Acrobits apps:
    https://localhost/customer/balance.php?u=%account[username]%&p=%account[password]%   <<== Will have returned balance in xml with authenticate by login+password
    https://localhost/customer/balance.php?u=%account[username]%                         <<== Will have returned balance in xml with authenticate by request ip instead password
*/

include ("lib/customer.defines.php");

getpost_ifset(array('u', 'p', 'html'));

if (!isset($u)) $u='';
if (!isset($p)) $p='';

$balance = Service_Get_Balance($u, $p);

$A2B -> DbDisconnect();

header ( "Content-type: text/xml" );
if (isset($html) && $html=="1") {
    echo "<pre>";
    echo $balance[0];
    echo "</pre>";
} else {
    echo "<response>\n";
    echo "<error>0</error>\n";
    echo "<balanceString>";
    echo $balance[1];
    if (strpos($balance[1],"ERROR")===false && $balance[0]!='')
	display_money_nocur($balance[0]);
    else
	echo $balance[0];
    echo "</balanceString>\n";
    echo "</response>\n";
}

function Service_Get_Balance($accountnumber, $password)
{
global $A2B;
    $A2B -> DBHandle = DbConnect();
    if (!$A2B -> DBHandle) {
        write_log(LOGFILE_API_CALLBACK, basename(__FILE__).' line:'.__LINE__." ERROR CONNECT DB");
        return array('500', 'ERROR - CONNECT DB ');
    }
    $card_id = 0;
    $instance_table = new Table();
    $A2B->set_instance_table($instance_table);

    if ($password=='')
	$QUERY = "SELECT cc.username, cc.credit, cc.status, cc.id, cc.currency FROM cc_card cc LEFT JOIN cc_sip_buddies ON cc.id=id_cc_card WHERE name LIKE '$accountnumber' AND ipaddr LIKE '".$_SERVER['REMOTE_ADDR']."' LIMIT 1";
    else
	$QUERY = "SELECT cc.username, cc.credit, cc.status, cc.id, cc.currency FROM cc_card cc LEFT JOIN cc_sip_buddies ON cc.id=id_cc_card WHERE name LIKE '$accountnumber' AND (secret LIKE '$password' OR ipaddr LIKE '".$_SERVER['REMOTE_ADDR']."') LIMIT 1";
    for ($i=10;$i>=0;$i--) {
	$res = $A2B->instance_table->ExecuteQuery($A2B->DBHandle, $QUERY);
	if ($res===false) {
	    return array('400', 'ERROR - AUTHENTICATE CODE ');
	} else {
	    $num = $res -> RecordCount();
	    if ($num>0) {
		$row [] = $res -> fetchRow();
		$card_id = $row[0][3];
		break;
	    }
	    if ($i>0) sleep(2);
	}
    }

    if (!$card_id || $card_id <= 0) {
	if ($password=='')
	    return array('', 'Your ip: '.$_SERVER['REMOTE_ADDR']);
	else
	    return array('400', 'ERROR - TIMEOUT ');
    }

    $balance  = $row[0][1];
    $currency = $row[0][4]." ";

    return array($balance, $currency);
}
