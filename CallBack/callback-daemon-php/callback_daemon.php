#!/usr/bin/php -q
<?php

declare(ticks = 1);
if (function_exists('pcntl_signal')) {
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

	$A2B -> DBHandle = DbConnect();
		$instance_table = new Table();
	$A2B -> set_instance_table ($instance_table);
	$A2B -> cardnumber = $AmiVars[4];
		
	if ($A2B -> callingcard_ivr_authenticate_light ($error_msg)) {
	
		$RateEngine = new RateEngine();
		$RateEngine -> webui = 0;
		// LOOKUP RATE : FIND A RATE FOR THIS DESTINATION
		
		$A2B -> agiconfig['accountcode'] = $A2B -> cardnumber;
		$A2B -> agiconfig['use_dnid'] = 1;
		$A2B -> agiconfig['say_timetocall'] = 0;
		$A2B -> extension = $A2B -> dnid = $A2B -> destination = $destination;
		
		$resfindrate = $RateEngine->rate_engine_findrates($A2B, $destination, $tariff);
		
		// IF FIND RATE
		if ($resfindrate!=0) {				
			$res_all_calcultimeout = $RateEngine->rate_engine_all_calcultimeout($A2B, $A2B->credit);
			if ($res_all_calcultimeout) {						
			    $ast = new AGI_AsteriskManager();
			    $res = $ast -> connect($server, $username, $secret);
			    if (!$res) return -4;
			    // MAKE THE CALL
			    $res = $RateEngine->rate_engine_performcall(false, $destination, $A2B, 8, $AmiVars, $ast);
			    $ast -> disconnect();
			    if ($res !== false) return $res;
			    else return -2; // not enough free trunk for make call
			} else return -3; // not have enough credit to call you back
		} else return -1; // no route to call back your phonenumber
	} else return -1; // ERROR MESSAGE IS CONFIGURE BY THE callingcard_ivr_authenticate_light
}


if (!defined('PID')) {
	define("PID", "/var/run/a2billing/a2b-callback-daemon.pid");
}

// CHECK IF THE DAEMON IS ALREADY RUNNING
if (ProcessHandler :: isActive()) {
	die("Already running!");
} else {
	ProcessHandler :: activate();
}

$FG_DEBUG = 0;
$verbose_level = 1;

$A2B = new A2Billing();
$A2B->load_conf($agi);

write_log(LOGFILE_API_CALLBACK, basename(__FILE__) . ' line:' . __LINE__ . "[#### CALLBACK BEGIN ####]");

if (!$A2B->DbConnect()) {
	echo "[Cannot connect to the database]\n";
	write_log(LOGFILE_API_CALLBACK, basename(__FILE__) . ' line:' . __LINE__ . "[Cannot connect to the database]");
	exit;
}

if ($A2B->config["database"]['dbtype'] == "postgres") {
	$UNIX_TIMESTAMP = "date_part('epoch',";
} else {
	$UNIX_TIMESTAMP = "UNIX_TIMESTAMP(";
}


    $id_server_group=1;

    $dbh=@mysql_connect($A2B->config['database']['hostname'],$A2B->config['database']['user'],$A2B->config['database']['password']) or die("Can't connect to DB server: ".": ".mysql_error()."\n");
    @mysql_select_db($A2B->config['database']['dbname'],$dbh) or die("Can't use database ".$A2B->config['database']['dbname'].": ".mysql_error($dbh)."\n");

    $query="SELECT `manager_host`,`manager_username`,`manager_secret` FROM `cc_server_manager` WHERE `id_group`=$id_server_group";
    $result=@mysql_query($query,$dbh) or die("Can't execute query '$query': ".mysql_error($dbh)."\n");
    $num_rows=@mysql_num_rows($result);
    if($num_rows!=1)
    {
	print("Query to cc_server_manager return incorrect result: number of rows is $num_rows\n");
	exit(1);
    }
    list($manager_host,$manager_username,$manager_secret)=@mysql_fetch_row($result) or die("Can't fetch row: ".mysql_error($dbh)."\n");

    $query="UPDATE `cc_server_manager` SET `lasttime_used`=now()";
    @mysql_query($query,$dbh) or die("Can't execute query '$query': ".mysql_error($dbh)."\n");

    while(true)
    {
	pcntl_wait($status, WNOHANG);
	$query="SELECT `id`,`entry_time`,`status`,`exten_leg_a`,`account`,`callerid`,`exten`,`context`,`priority`,`variable`,`timeout`,`reason`,`num_attempts_unavailable`,`num_attempts_busy`,`num_attempts_noanswer`,TIMEDIFF(now(),`entry_time`)";
	$query.=" FROM `cc_callback_spool` WHERE `id_server_group`=$id_server_group AND `status`='PENDING' AND (`next_attempt_time`<=now() OR ISNULL(`next_attempt_time`))";
	$result=@mysql_query($query,$dbh) or die("Can't execute query '$query': ".mysql_error($dbh)."\n");
	while((list($cc_id,$cc_entry_time,$cc_status,$cc_exten_leg_a,$cc_account,$cc_callerid,$cc_exten,$cc_context,$cc_priority,$cc_variable,$cc_timeout,$cc_reason,$cc_num_attempts_unavailable,$cc_num_attempts_busy,$cc_num_attempts_noanswer,$cc_timediff)=@mysql_fetch_row($result)))
	{
	    $query2="SELECT `tariff`,`cbtimeoutunavailable`,`cbattemptunavailable`,`cbtimeoutbusy`,`cbattemptbusy`,`cbtimeoutnoanswer`,`cbattemptnoanswer`,`cbtimeoutmax`,TIME_TO_SEC(TIMEDIFF(`cbtimeoutmax`,'$cc_timediff')) FROM `cc_card` WHERE `username`=$cc_account";
	    $result2=@mysql_query($query2,$dbh) or die("Can't execute query '$query2': ".mysql_error($dbh)."\n");
	    $num_rows2=@mysql_num_rows($result2);
	    if($num_rows2!=1)
	    {
		print("Query to cc_card return incorrect result: number of rows is $num_rows2 for user_name='$cc_account'\n");
		exit(1);
	    }
	    list($acc_tariff,$acc_to_unav,$acc_max_unav,$acc_to_busy,$acc_max_busy,$acc_to_noansw,$acc_max_noansw,$acc_max_timeout,$acc_timeout_res)=@mysql_fetch_row($result2);
	    if($acc_timeout_res<0)
	    {
		$query3="UPDATE `cc_callback_spool` SET `status`='ERROR_TIMEOUT' WHERE `id`=$cc_id";
		@mysql_query($query3,$dbh) or die("Can't execute query '$query3': ".mysql_error($dbh)."\n");
	    }
	    elseif($acc_max_unav<=$cc_num_attempts_unavailable)
	    {
		$query3="UPDATE `cc_callback_spool` SET `status`='ERROR_UNAVAILABLE' WHERE `id`=$cc_id";
		@mysql_query($query3,$dbh) or die("Can't execute query '$query3': ".mysql_error($dbh)."\n");
	    }
	    elseif($acc_max_busy<=$cc_num_attempts_busy)
	    {
		$query3="UPDATE `cc_callback_spool` SET `status`='ERROR_BUSY' WHERE `id`=$cc_id";
		@mysql_query($query3,$dbh) or die("Can't execute query '$query3': ".mysql_error($dbh)."\n");
	    }
	    elseif($acc_max_noansw<=$cc_num_attempts_noanswer)
	    {
		$query3="UPDATE `cc_callback_spool` SET `status`='ERROR_NO-ANSWER' WHERE `id`=$cc_id";
		@mysql_query($query3,$dbh) or die("Can't execute query '$query3': ".mysql_error($dbh)."\n");
	    }
	    else
	    {
		$pid=pcntl_fork();
		if($pid==-1)
		{
		    print("Can't fork!\n");
		    exit(2);
		}
		elseif($pid)
		{
		    pcntl_wait($status, WNOHANG);
		}
		else
		{
		    $query3="UPDATE `cc_callback_spool` SET `status`='PROCESSING',`num_attempt`=`num_attempt`+1,`last_attempt_time`=now() WHERE `id`=$cc_id";
		    @mysql_query($query3,$dbh) or die("Can't execute query '$query3': ".mysql_error($dbh)."\n");
		    ob_start();
		    register_shutdown_function(create_function('$pars', 'ob_end_clean();posix_kill(getmypid(), SIGKILL);'), array());

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
			case  1: $last_status="ERROR_BUSY";$timeout=$acc_to_busy;break;
			case  3: $last_status="ERROR_NO-ANSWER";$timeout=$acc_to_noansw;break;
			case  4: $last_status="SENT";$fatal=1;break;
			case  5: $last_status="ERROR_CONGESTION";$fatal=1;break;
			default: $last_status="ERROR_UNKNOWN (#$return)";$fatal=1;break;
		    }
		    if($fatal) $status=$last_status;
			  else $status='PENDING';
		    $query3="UPDATE `cc_callback_spool` SET `status`='$status',`last_status`='$last_status'";
		    if($return==-2 || $return==0) $query3.=",`num_attempts_unavailable`=`num_attempts_unavailable`+1";
		    if($return==1) $query3.=",`num_attempts_busy`=`num_attempts_busy`+1";
		    if($return==3) $query3.=",`num_attempts_noanswer`=`num_attempts_noanswer`+1";
		    if($timeout>=0) $query3.=",`next_attempt_time`=ADDTIME(now(),SEC_TO_TIME($timeout))";
		    $query3.=" WHERE `id`=$cc_id";
		    @mysql_query($query3,$dbh) or die("Can't execute query '$query3': ".mysql_error($dbh)."\n");

		    exit(0);
		}
	    }
	}
	sleep(1);
    }

    @mysql_close($dbh);
?>
