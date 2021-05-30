#!/usr/bin/php-cgi -q 
<?
    $id_server_group=1;
    $timeout=300;


    $config=parse_ini_file("/etc/a2billing.conf",true);

    $dbh=@mysqli_connect($config['database']['hostname'],$config['database']['user'],$config['database']['password'],$config['database']['dbname']) or die("Can't connect to DB server: ".": ".mysqli_error($dbh)."\n");

    $query="UPDATE `cc_callback_spool` SET `status`='ERROR_PROCESSING' WHERE `status`='PROCESSING' AND TIME_TO_SEC(TIMEDIFF(now(),`next_attempt_time`))>$timeout";
    @mysqli_query($dbh,$query) or die("Can't execute query '$query': ".mysqli_error($dbh)."\n");
    @mysqli_close($dbh);
?>
