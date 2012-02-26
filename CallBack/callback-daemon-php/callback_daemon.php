#!/usr/bin/php -q
<?php

//$id_server_group=1;

declare(ticks = 1);
if (function_exists('pcntl_signal'))
{
    pcntl_signal(SIGHUP, SIG_IGN); // Указываем игнорировать сигнал требования перезапуска
}

set_time_limit(0);
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));


include ("lib/admin.defines.php");
include ("lib/Class.RateEngine.php");
include ("lib/ProcessHandler.php");
include ("lib/phpagi/phpagi-asmanager.php");

function originateresponse($e, $parameters, $server, $port, $actionid) {

    if ($parameters['ActionID'] == $actionid)	return $parameters['Reason'];
    return false;
}

function callback_engine(&$A2B, $server, $username, $secret, $AmiVars, $destination, $tariff) {

    $A2B -> cardnumber = $AmiVars[4];

    if ($A2B -> callingcard_ivr_authenticate_light ($error_msg))
    {
	$RateEngine = new RateEngine();
	$RateEngine -> webui = 0;

//	LOOKUP RATE : FIND A RATE FOR THIS DESTINATION
	$A2B -> agiconfig['accountcode'] = $A2B -> cardnumber;
	$A2B -> agiconfig['use_dnid'] = 1;
	$A2B -> agiconfig['say_timetocall'] = 0;
	$A2B -> extension = $A2B -> dnid = $A2B -> destination = $destination;

	$resfindrate = $RateEngine->rate_engine_findrates($A2B, $destination, $tariff);

//	IF FIND RATE
	if ($resfindrate!=0)
	{
	    $res_all_calcultimeout = $RateEngine->rate_engine_all_calcultimeout($A2B, $A2B->credit);
	    if ($res_all_calcultimeout)
	    {
		$ast = new AGI_AsteriskManager();
		$res = $ast -> connect($server, $username, $secret);
		if (!$res) return -4;
//		MAKE THE CALL
		$res = $RateEngine->rate_engine_performcall(false, $destination, $A2B, 8, $AmiVars, $ast);
		$ast -> disconnect();
		if ($res !== false) return $res;
		else return -2; // not enough free trunk for make call
	    }
	    else return -3; // not have enough credit to call you back
	}
	else return -1; // no route to call back your phonenumber
    }
    else return -1; // ERROR MESSAGE IS CONFIGURE BY THE callingcard_ivr_authenticate_light
}


$FG_DEBUG = 0;
$verbose_level = 1;

$A2B = new A2Billing();
$A2B->load_conf($agi);

if (!defined('PID'))
    define("PID", $A2B->config["daemon-info"]['pidfile']);

// CHECK IF THE DAEMON IS ALREADY RUNNING
if (ProcessHandler :: isActive())
    die("Already running!");
else
    ProcessHandler :: activate();

write_log(LOGFILE_API_CALLBACK, basename(__FILE__) . ' line:' . __LINE__ . "[#### CALLBACK BEGIN ####]");

if (!$A2B->DbConnect()) {
    echo "[Cannot connect to the database]\n";
    write_log(LOGFILE_API_CALLBACK, basename(__FILE__) . ' line:' . __LINE__ . "[Cannot connect to the database]");
    exit;
}

if ($A2B->config["database"]['dbtype'] == "postgres")
    $UNIX_TIMESTAMP = "date_part('epoch',";
else
    $UNIX_TIMESTAMP = "UNIX_TIMESTAMP(";

$instance_table = new Table();
$A2B -> set_instance_table ($instance_table);

while(true)
{
    pcntl_wait($status, WNOHANG);
    $query="SELECT `id`,`status`,`exten_leg_a`,`account`,`callerid`,`exten`,`context`,`priority`,`variable`,`timeout`,`reason`,`num_attempts_unavailable`,`num_attempts_busy`,`num_attempts_noanswer`,TIMEDIFF(now(),`callback_time`),`id_server_group`";
    $query.=" FROM `cc_callback_spool` WHERE `status`='PENDING' AND (`next_attempt_time`<=now() OR ISNULL(`next_attempt_time`))";
    $result=$instance_table->SQLExec($A2B->DBHandle, $query);
    foreach ($result as $value) {
	list($cc_id,$cc_status,$cc_exten_leg_a,$cc_account,$cc_callerid,$cc_exten,$cc_context,$cc_priority,$cc_variable,$cc_timeout,$cc_reason,$cc_num_attempts_unavailable,$cc_num_attempts_busy,$cc_num_attempts_noanswer,$cc_timediff,$id_server_group)=$value;
	$query="SELECT `id`,`manager_host`,`manager_username`,`manager_secret` FROM `cc_server_manager` WHERE `id_group`=$id_server_group LIMIT 1";
	$result1=$instance_table->SQLExec($A2B->DBHandle, $query);
	if (!(is_array($result1) && count($result1)>0)) {
	    print("id_server_group $id_server_group does not exist\n");
	    exit(1);
	}
	list($manager_id,$manager_host,$manager_username,$manager_secret)=$result1[0];
	$query="UPDATE `cc_server_manager` SET `lasttime_used`=now()";
	if (!$A2B->DBHandle->Execute($query)) die("Can't execute query '$query'\n");

	$query="SELECT `tariff`,`cbtimeoutunavailable`,`cbattemptunavailable`,`cbtimeoutbusy`,`cbattemptbusy`,`cbtimeoutnoanswer`,`cbattemptnoanswer`,`cbtimeoutmax`,TIME_TO_SEC(TIMEDIFF(`cbtimeoutmax`,'$cc_timediff')) FROM `cc_card` WHERE `username`='$cc_account' LIMIT 1";
	$result1=$instance_table->SQLExec($A2B->DBHandle, $query);
	if (!(is_array($result1) && count($result1)>0)) die("Can't execute query '$query'\n");
	list($acc_tariff,$acc_to_unav,$acc_max_unav,$acc_to_busy,$acc_max_busy,$acc_to_noansw,$acc_max_noansw,$acc_max_timeout,$acc_timeout_res)=$result1[0];
	if ($acc_timeout_res < 0)
	{
	    $query="UPDATE `cc_callback_spool` SET `status`='ERROR_TIMEOUT',`id_server`='$manager_id' WHERE `id`=$cc_id";
	    if (!$A2B->DBHandle->Execute($query)) die("Can't execute query '$query'\n");
	}
	elseif($acc_max_unav<=$cc_num_attempts_unavailable)
	{
	    $query="UPDATE `cc_callback_spool` SET `status`='ERROR_UNAVAILABLE',`id_server`='$manager_id' WHERE `id`=$cc_id";
	    if (!$A2B->DBHandle->Execute($query)) die("Can't execute query '$query'\n");
	}
	elseif($acc_max_busy<=$cc_num_attempts_busy)
	{
	    $query="UPDATE `cc_callback_spool` SET `status`='ERROR_BUSY',`id_server`='$manager_id' WHERE `id`=$cc_id";
	    if (!$A2B->DBHandle->Execute($query)) die("Can't execute query '$query'\n");
	}
	elseif($acc_max_noansw<=$cc_num_attempts_noanswer)
	{
	    $query="UPDATE `cc_callback_spool` SET `status`='ERROR_NO-ANSWER',`id_server`='$manager_id' WHERE `id`=$cc_id";
	    if (!$A2B->DBHandle->Execute($query)) die("Can't execute query '$query'\n");
	}
	else {
	    $A2B->DbDisconnect();
	    $pid=pcntl_fork();
	    if($pid==-1) {
		print("Can't fork!\n");
		exit(2);
	    }
	    elseif($pid) {
		pcntl_wait($status, WNOHANG);
		$A2B -> DbConnect($agi);
		$A2B -> set_instance_table ($instance_table);
	    }
	    else {
		ob_start();
		register_shutdown_function(create_function('$pars', 'ob_end_clean();posix_kill(getmypid(), SIGKILL);'), array());

		$A2B -> DbConnect($agi);
		$A2B -> set_instance_table ($instance_table);
		$query="UPDATE `cc_callback_spool` SET `status`='PROCESSING',`num_attempt`=`num_attempt`+1,`last_attempt_time`=now(),`id_server`='$manager_id' WHERE `id`=$cc_id";
		if (!$A2B->DBHandle->Execute($query)) die("Can't execute query '$query'\n");
		$return=callback_engine($A2B, $manager_host.":5038", $manager_username, $manager_secret, array($cc_exten,$cc_priority,$cc_callerid,$cc_variable,$cc_account,$cc_id), $cc_exten_leg_a, $acc_tariff);
		$timeout=-1;
		$fatal=0;
		switch($return)
		{
		    case -4: $last_status="ERROR_AMI";$fatal=1;break; // AMI not have connecting
		    case -3: $last_status="ERROR_NO-MONEY";$fatal=1;break; // not have enough credit to call you back
		    case -2: $last_status="ERROR_CHANNEL-UNAVAILABLE";$timeout=$acc_to_unav;break; // not enough free trunk for make call
		    case -1: $last_status="ERROR_NO-RATE-AVAILABLE";$fatal=1;break; // no route to call back your phonenumber or other fatal errors
		    case  0: $last_status="ERROR_CHANNEL-UNAVAILABLE";$timeout=$acc_to_unav;break;
		    case  1: $last_status="BUSY";$timeout=$acc_to_busy;break;
		    case  3: $last_status="NO-ANSWER";$timeout=$acc_to_noansw;break;
		    case  4: $last_status="SENT";$fatal=1;break;
		    case  5: $last_status="ERROR_CONGESTION";$fatal=1;break;
		    case  8: $last_status="ERROR_CONGESTION_OR_CHANNEL-UNAVAILABLE";$fatal=0;$timeout=$acc_to_unav;break;
		    default: $last_status="ERROR_UNKNOWN (#$return)";$fatal=1;break;
		}
		if($fatal) $status=$last_status;
		    else $status='PENDING';
		$query="UPDATE `cc_callback_spool` SET `status`='$status',`last_status`='$last_status',`manager_result`='$last_status'";
		if($return==-2 || $return==0 || $return==8) $query.=",`num_attempts_unavailable`=`num_attempts_unavailable`+1";
		if($return==1) $query.=",`num_attempts_busy`=`num_attempts_busy`+1";
		if($return==3) $query.=",`num_attempts_noanswer`=`num_attempts_noanswer`+1";
		if($timeout>=0) $query.=",`next_attempt_time`=ADDTIME(now(),SEC_TO_TIME($timeout))";
		$query.=" WHERE `id`=$cc_id";
		if (!$A2B->DBHandle->Execute($query)) die("Can't execute query '$query'\n");
		$A2B->DbDisconnect();
		exit(0);
	    }
	}
    }
    sleep(1);
}
?>
