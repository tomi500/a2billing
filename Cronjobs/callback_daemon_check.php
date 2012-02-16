#!/usr/bin/php-cgi -q 
<?
    $id_server_group=1;
    $timeout=300;


    $config=parse_ini_file("/etc/a2billing.conf",true);

    $dbh=@mysql_connect($config['database']['hostname'],$config['database']['user'],$config['database']['password']) or die("Can't connect to DB server: ".": ".mysql_error()."\n");
    @mysql_select_db($config['database']['dbname'],$dbh) or die("Can't use database ".$config['database']['dbname'].": ".mysql_error($dbh)."\n");

    $query="UPDATE `cc_callback_spool` SET `status`='ERROR_PROCESSING' WHERE `status`='PROCESSING' AND TIME_TO_SEC(TIMEDIFF(now(),`next_attempt_time`))>$timeout";
    @mysql_query($query,$dbh) or die("Can't execute query '$query': ".mysql_error($dbh)."\n");
    @mysql_close($dbh);
?>
