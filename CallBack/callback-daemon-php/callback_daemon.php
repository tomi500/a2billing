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

function reasondesc($reason) {
	switch($reason)
	{
	    case  0: return "CHANUNAVAIL";
	    case  1: return "BUSY";
	    case  3: return "NOANSWER";
	    case  4: return "ANSWER";
	    case  8: return "CHANUNAVAIL";
	    default: return "CONGESTION";
	}
}

function originateresponse($e, $parameters, $server, $port, &$ast) {

    if ($parameters['ActionID'] == $ast->actionid) {
	$ansnum = $parameters['Reason'];
	$ansalpha = reasondesc($ansnum);
//	write_log(LOGFILE_API_CALLBACK, "OriginateResponse{$ansalpha}: ".var_export($parameters, true));
	return array($ansnum,$ansalpha);
    }
    return false;
}

function newstateresponse($e, $parameters, $server, $port, &$ast) {

    if ($e == 'newstate') {
	$res = $ast->GetVar($parameters['Channel'],'ACTIONID');
	if ($res['Response'] == "Success" && $res['Value'] == $ast->actionid && !isset($parameters['Value'])) {
//		write_log(LOGFILE_API_CALLBACK, "NewState: ".var_export($parameters, true));
//		write_log(LOGFILE_API_CALLBACK, var_export($ast->actionid, true) . " GetVar: " . var_export($res, true));
		unset($ast->event_handlers[$e]);
		$ast->channel = $parameters['Channel'];
		$res = $parameters['ChannelStateDesc'];
		if ($res =="Up")	$res = "ANSWER";
		return $res;
	}
    }
    if ($e == 'originateresponse' && $parameters['ActionID'] == $ast->actionid) {
//	write_log(LOGFILE_API_CALLBACK, "OriginateResponse: ".var_export($parameters, true));
	return reasondesc($parameters['Reason']);
    }
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

function ringup_engine(&$A2B, $server, $username, $secret, $AmiVars, $destination, $tariff, $trunk) {

	$A2B -> cardnumber = $AmiVars[4];

	$removeprefix	= explode(",",$trunk[4]);
	$prefix 	= $trunk[1];
	$ipaddress	= $trunk[3];
	$tech		= $trunk[2];
	if (is_array($removeprefix) && count($removeprefix)>0) {
	    foreach ($removeprefix as $testprefix) {
		if (substr($destination,0,strlen($testprefix))==$testprefix) {
		    $destination = substr($destination,strlen($testprefix));
		    break;
		}
	    }
	}
	$pos_dialingnumber = max(strpos($ipaddress, '%dialingnumber%'),strpos($ipaddress, '%none%'));
	$ipaddress = str_replace("%none%", '', $ipaddress);
	if (strncmp($destination, $prefix, strlen($prefix)) == 0 && strlen($prefix) > 1)	$prefix="";
	$ipaddress = str_replace("%dialingnumber%", $prefix.$destination, $ipaddress);
	if ($pos_dialingnumber !== false) {
	    $channel = "$tech/$ipaddress";
	} elseif ($A2B->agiconfig['switchdialcommand'] == 1) {
	    $channel = "$tech/$prefix$destination@$ipaddress";
	} else {
	    $channel = "$tech/$ipaddress/$prefix$destination";
	}
	$ast = new AGI_AsteriskManager();
	$res = $ast -> connect($server, $username, $secret);
	if (!$res) return -4;
	$ast -> actionid = $AmiVars[5];
	$ast -> add_event_handler('Newstate', 'newstateresponse');
	$ast -> add_event_handler('OriginateResponse', 'newstateresponse');
//	MAKE THE CALL
	$res = $ast -> Originate($channel,NULL,NULL,NULL,NULL,NULL,$A2B->config['callback']['timeout']*1000,$AmiVars[2],$AmiVars[3],NULL,true,$ast->actionid);
	$response = $ast -> wait_response(true);
	if ($ast->channel)	$ast -> Hangup ($ast->channel);
	$ast -> disconnect();
	if (is_array($response)) {
		write_log(LOGFILE_API_CALLBACK, "!!!!!!!!!!!!!!!!!!!!!=================== ARRAY ==================: ".var_export($response, true));
		$response = 'ERROR';
	}
	return $response;
//	if ($res !== false) return $res;
//	else return -2; // not enough free trunk for make call
}


$FG_DEBUG = 0;
$verbose_level = 1;

$A2B = new A2Billing();
$A2B->load_conf($agi);

if (!defined('PID'))
    define("PID", $A2B->config["daemon-info"]['pidfile']);

// CHECK IF THE DAEMON IS ALREADY RUNNING
$pH = new ProcessHandler();
if ($pH->isActive()) {
    die("Already running!");
} else {
    $pH->activate();
}

write_log(LOGFILE_API_CALLBACK, basename(__FILE__) . ' line:' . __LINE__ . "[#### CALLBACK ENGINE START ####]");

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
    $query="SELECT `id`,`status`,`exten_leg_a`,`account`,`callerid`,`exten`,`context`,`priority`,`variable`,`timeout`,`reason`,`num_attempts_unavailable`,`num_attempts_busy`,`num_attempts_noanswer`,TIMEDIFF(now(),`callback_time`),`id_server_group`, `surveillance`";
    $query.=" FROM `cc_callback_spool` WHERE `status`='PENDING' AND (`next_attempt_time`<=now() OR ISNULL(`next_attempt_time`))";
    $result=$instance_table->SQLExec($A2B->DBHandle, $query);
    foreach ($result as $value) {
	list($cc_id,$cc_status,$cc_exten_leg_a,$cc_account,$cc_callerid,$cc_exten,$cc_context,$cc_priority,$cc_variable,$cc_timeout,$cc_reason,$cc_num_attempts_unavailable,$cc_num_attempts_busy,$cc_num_attempts_noanswer,$cc_timediff,$id_server_group,$duration)=$value;
	if ($duration > 0) {
		$cc_timediff = 0;
	}
	$query="SELECT `id`,`manager_host`,`manager_username`,`manager_secret` FROM `cc_server_manager` WHERE `id_group`=$id_server_group LIMIT 1";
	$result1=$instance_table->SQLExec($A2B->DBHandle, $query);
	if (!(is_array($result1) && count($result1)>0)) {
	    print("id_server_group $id_server_group does not exist\n");
	    exit(1);
	}
	list($manager_id,$manager_host,$manager_username,$manager_secret)=$result1[0];
	$query="UPDATE `cc_server_manager` SET `lasttime_used`=now()";
	if (!$A2B->DBHandle->Execute($query)) die("Can't execute query '$query'\n");

	$query="SELECT `tariff`,`cbtimeoutunavailable`,`cbattemptunavailable`,`cbtimeoutbusy`,`cbattemptbusy`,`cbtimeoutnoanswer`,`cbattemptnoanswer`,`cbtimeoutmax`,TIME_TO_SEC(TIMEDIFF(SEC_TO_TIME(`cbtimeoutmax`),'$cc_timediff')) FROM `cc_card` WHERE `username`='$cc_account' LIMIT 1";
	$result1=$instance_table->SQLExec($A2B->DBHandle, $query);
	if (!(is_array($result1) && count($result1)>0)) die("Can't execute query '$query'\n");
	list($acc_tariff,$acc_to_unav,$acc_max_unav,$acc_to_busy,$acc_max_busy,$acc_to_noansw,$acc_max_noansw,$acc_max_timeout,$acc_timeout_res)=$result1[0];
	if ($duration > 0) {
		$acc_to_unav=$acc_to_busy=$acc_to_noansw=$cc_num_attempts_unavailable=$cc_num_attempts_busy=$cc_num_attempts_noanswer=0;
	}
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
		if($fatal && ($duration == 0 || $return == 4)) {
			$status=$last_status;
		} else	$status='PENDING';
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
// while (false) {
    $query = "SELECT `cc_ringup`.`id`,`simult`-`cc_ringup`.`inuse`,`trunks`,`id_server_group`,`username`,`tariff` FROM `cc_ringup`
    LEFT JOIN `cc_card` ON `cc_card`.`id`=`account_id`
    LEFT JOIN `cc_sheduler_ratecard` ON `id_ringup`=`cc_ringup`.`id`
    WHERE `cc_ringup`.`status`='1' AND `cc_ringup`.`inuse`<`simult`
	AND (`cc_sheduler_ratecard`.`id_ringup` IS NULL OR (`weekdays` LIKE CONCAT('%',WEEKDAY(NOW()),'%') AND (CURTIME() BETWEEN `timefrom` AND `timetill`
	OR (`timetill`<=`timefrom` AND (CURTIME()<`timetill` OR CURTIME()>=`timefrom`)))))";
    $result1 = $instance_table->SQLExec($A2B->DBHandle, $query);
    foreach ($result1 as $value) {
	$query = "SELECT `id_trunk`,`trunkprefix`,`providertech`,`providerip`,`removeprefix`,`outbound_cidgroup_id` FROM `cc_trunk` WHERE `id_trunk` IN ({$value[2]}) AND `status`='1' AND (`maxuse`='-1' OR `inuse`<`maxuse`) ORDER BY RAND() LIMIT 1";
	$trunk = $instance_table->SQLExec($A2B->DBHandle, $query);
	if (is_array($trunk) && count($trunk)>0) {
	    $query="SELECT `id`,`manager_host`,`manager_username`,`manager_secret` FROM `cc_server_manager` WHERE `id_group`='{$value[3]}' LIMIT 1";
	    $result=$instance_table->SQLExec($A2B->DBHandle, $query);
	    if (!(is_array($result) && count($result)>0)) {
		print("id_server_group $id_server_group does not exist\n");
		exit(1);
	    }
	    list($manager_id,$manager_host,$manager_username,$manager_secret)=$result[0];
	    $query="UPDATE `cc_server_manager` SET `lasttime_used`=now()";
	    if (!$A2B->DBHandle->Execute($query)) die("Can't execute query '$query'\n");
	    $query = "SELECT `id`,`try`,`tonum` FROM `cc_ringup_list` WHERE `id_ringup`='{$value[0]}' AND `inuse`='0' AND `passed`='0' LIMIT {$value[1]}";
	    $result = $instance_table->SQLExec($A2B->DBHandle, $query);
	    if (is_array($result) && count($result)>0) {
//	    	$ringupcount = count($result);
//	    	$trunks = explode(";",$value[2]);
		foreach ($result as $valnum) {
//		    $trunk = $trunk[0][0];
		    $A2B->DbDisconnect();
		    $pid=pcntl_fork();
		    if($pid==-1) {
			print("Can't fork!\n");
			exit(2);
		    } elseif($pid) {
			pcntl_wait($status, WNOHANG);
			$A2B -> DbConnect($agi);
			$A2B -> set_instance_table ($instance_table);
		    } else {
			ob_start();
			register_shutdown_function(create_function('$pars', 'ob_end_clean();posix_kill(getmypid(), SIGKILL);'), array());
			$A2B -> DbConnect($agi);
			$A2B -> set_instance_table ($instance_table);

			$query = "UPDATE `cc_ringup`,`cc_ringup_list`,`cc_trunk` SET `cc_ringup`.`inuse`=`cc_ringup`.`inuse`+1,`cc_ringup_list`.`inuse`='1',`cc_trunk`.`inuse`=`cc_trunk`.`inuse`+1 WHERE `cc_ringup`.`id`='{$value[0]}' AND `cc_ringup_list`.`id`='{$valnum[0]}' AND `id_trunk`='{$trunk[0][0]}'";
			if (!$A2B->DBHandle->Execute($query)) die("Can't execute query '$query'\n");
			$destination	= $valnum[2];
			$query = "SELECT cid FROM cc_outbound_cid_list WHERE activated = 1 AND outbound_cid_group = {$trunk[0][5]} AND cid NOT LIKE '{$destination}' ORDER BY RAND() LIMIT 1";
			$cidresult = $instance_table->SQLExec($A2B->DBHandle, $query);
			$outcid = (is_array($cidresult) && count($cidresult) > 0) ? $cidresult[0][0] : NULL;
			$return = ringup_engine($A2B, $manager_host.":5038", $manager_username, $manager_secret, array('1234567890',1,$outcid,'ACTIONID=RingUp-'.$valnum[0],$value[4],'RingUp-'.$valnum[0]), $destination, $value[5], $trunk[0]);
			$query = "UPDATE `cc_ringup`,`cc_ringup_list`,`cc_trunk` SET `cc_ringup`.`status`=IF(`lefte`='1',2,`cc_ringup`.`status`),`cc_ringup`.`inuse`=`cc_ringup`.`inuse`-1,`processed`=`processed`+1,`lefte`=`lefte`-1,`cc_ringup_list`.`inuse`='0',`channelstatedesc`='$return',`try`=`try`+1,`attempt`=NOW(),`passed`=1,`cc_trunk`.`inuse`=`cc_trunk`.`inuse`-1 WHERE `cc_ringup`.`id`='{$value[0]}' AND `cc_ringup_list`.`id`='{$valnum[0]}' AND `id_trunk`='{$trunk[0][0]}'";
			if (!$A2B->DBHandle->Execute($query)) die("Can't execute query '$query'\n");

			$A2B->DbDisconnect();
			exit(0);
		    }
		}
	    }
	}
    }
// break;
// }
    sleep(1);
}
?>
