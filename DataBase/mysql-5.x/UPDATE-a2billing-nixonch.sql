ALTER TABLE cc_trunk ADD dialprefixmain char(30) COLLATE utf8_bin NOT NULL;
ALTER TABLE cc_trunk ADD outbound_cidgroup_id int(11) DEFAULT '-1';

ALTER TABLE cc_trunk ADD dialprefixa char(30) COLLATE utf8_bin NOT NULL;
ALTER TABLE cc_trunk ADD periodcounta int(11) DEFAULT '0';
ALTER TABLE cc_trunk ADD periodexpirya timestamp NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE cc_trunk ADD timelefta int(11) NOT NULL DEFAULT '60';
ALTER TABLE cc_trunk ADD perioda int(11) DEFAULT '0';
ALTER TABLE cc_trunk ADD startdatea timestamp NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE cc_trunk ADD stopdatea timestamp NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE cc_trunk ADD billblockseca int(11) NOT NULL DEFAULT '1';
ALTER TABLE cc_trunk ADD maxsecperperioda int(11) DEFAULT '-1';
ALTER TABLE cc_trunk ADD lastcallstoptimea timestamp NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE cc_trunk ADD failover_trunka int(11) NULL DEFAULT NULL;

ALTER TABLE cc_trunk ADD dialprefixb char(30) COLLATE utf8_bin NOT NULL;
ALTER TABLE cc_trunk ADD periodcountb int(11) DEFAULT '0';
ALTER TABLE cc_trunk ADD periodexpiryb timestamp NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE cc_trunk ADD timeleftb int(11) NOT NULL DEFAULT '60';
ALTER TABLE cc_trunk ADD periodb int(11) DEFAULT '0';
ALTER TABLE cc_trunk ADD startdateb timestamp NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE cc_trunk ADD stopdateb timestamp NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE cc_trunk ADD billblocksecb int(11) NOT NULL DEFAULT '1';
ALTER TABLE cc_trunk ADD maxsecperperiodb int(11) DEFAULT '-1';
ALTER TABLE cc_trunk ADD lastcallstoptimeb timestamp NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE cc_trunk ADD failover_trunkb int(11) NULL DEFAULT NULL;

ALTER TABLE cc_card ADD monitor int(11) DEFAULT '0';
ALTER TABLE cc_card ADD recalldays int(11) NOT NULL DEFAULT '10';
ALTER TABLE cc_card ADD recalltime int(11) NOT NULL DEFAULT '7200';

CREATE TABLE IF NOT EXISTS `cc_trunk_rand` (
  `trunk_id` int(11) NOT NULL DEFAULT '0',
  `trunk_dependa` int(11) NOT NULL DEFAULT '0',
  `trunk_dependb` int(11) NOT NULL DEFAULT '0',
  `trunkpercentage` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`trunk_id`,`trunk_dependa`,`trunk_dependb`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE cc_invoice_conf CHANGE value value VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;
INSERT INTO cc_invoice_conf (key_val) VALUES ('comments');

ALTER TABLE cc_sip_buddies ADD callbackextension varchar(15) NOT NULL DEFAULT '';
ALTER TABLE cc_sip_buddies ADD directmedia varchar(15) NOT NULL DEFAULT 'update,nonat';
ALTER TABLE cc_sip_buddies ADD encryption varchar(20) COLLATE utf8_bin NOT NULL;
ALTER TABLE cc_sip_buddies ADD transport varchar(20) COLLATE utf8_bin NOT NULL;

ALTER TABLE cc_sip_buddies
  CHANGE canreinvite canreinvite VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  CHANGE nat nat CHAR( 3 ) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL,
  CHANGE qualify qualify CHAR( 7 ) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL,
  CHANGE regseconds regseconds VARCHAR( 15 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  CHANGE rtpkeepalive rtpkeepalive VARCHAR( 15 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;

ALTER TABLE cc_trunk CHANGE removeprefix removeprefix CHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL;

ALTER TABLE cc_iax_buddies ADD forceencryption varchar(20) COLLATE utf8_bin NOT NULL;

ALTER TABLE cc_call ADD card_caller BIGINT( 20 ) NOT NULL AFTER `card_id`;

ALTER TABLE cc_callerid ADD callback INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE cc_callerid ADD phonenumber VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;

ALTER TABLE cc_did_destination ADD answer INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE cc_did_destination ADD playsound VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;
ALTER TABLE cc_did_destination ADD timeout VARCHAR( 3 ) NOT NULL ;

ALTER TABLE cc_did ADD id_trunk INT( 11 ) NOT NULL DEFAULT '-1';
ALTER TABLE cc_did ADD allciduse INT( 11 ) NOT NULL DEFAULT '0';

drop procedure if exists a2b_trf_check;

delimiter //

create procedure a2b_trf_check()
begin
    declare a int;
    select count(*) into a from cc_config where config_key='date_timezone';
    if a=0 then
	INSERT INTO cc_config (id, config_title, config_key, config_value, config_description, config_valuetype, config_listvalues, config_group_title)
	VALUES (NULL , 'Time Zone', 'date_timezone', 'Europe/Moscow', 'Defines the default timezone used by the date functions, eg Europe/Kiev', '0', NULL , 'global');
    elseif a>1 then
	select id into a from cc_config where config_key='date_timezone' order by id limit 0,1;
	delete from cc_config where config_key='date_timezone' and id>a;
    end if;
end //

delimiter ;

call a2b_trf_check;

drop procedure if exists a2b_trf_check;

delimiter //

create procedure a2b_trf_check()
begin
    declare a int;
    select count(*) into a from cc_config where config_key='monitor_conversion';
    if a=0 then
	INSERT INTO cc_config (id, config_title, config_key, config_value, config_description, config_valuetype, config_listvalues, config_group_title)
	VALUES (NULL, 'Conversion MixMonitor to Monitor', 'monitor_conversion', '0', 'Use Asterisk command Monitor instead MixMonitor to right sync in/out channels', '1', 'yes,no', 'webui');
    elseif a>1 then
	select id into a from cc_config where config_key='monitor_conversion' order by id limit 0,1;
	delete from cc_config where config_key='monitor_conversion' and id>a;
    end if;
end //

delimiter ;

call a2b_trf_check;

drop procedure if exists a2b_trf_check;

delimiter //

create procedure a2b_trf_check()
begin
    declare a int;
    select count(*) into a from cc_config where config_key='startup_time';
    if a=0 then
	INSERT INTO cc_config (id, config_title, config_key, config_value, config_description, config_valuetype, config_listvalues, config_group_title)
	VALUES (NULL, 'System startup time', 'startup_time', '0', 'Numbers in seconds since 1970-01-01 (Unix epoch)', 0, NULL, 'global');
    elseif a>1 then
	select id into a from cc_config where config_key='startup_time' order by id limit 0,1;
	delete from cc_config where config_key='startup_time' and id>a;
    end if;
end //

delimiter ;

call a2b_trf_check;

drop procedure if exists a2b_trf_check;

ALTER TABLE cc_callback_spool ADD `next_attempt_time` timestamp DEFAULT NULL;
ALTER TABLE cc_callback_spool ADD `reason` int(11) DEFAULT NULL;
ALTER TABLE cc_callback_spool ADD `num_attempts_unavailable` int(11) NOT NULL DEFAULT '0';
ALTER TABLE cc_callback_spool ADD `num_attempts_busy` int(11) NOT NULL DEFAULT '0';
ALTER TABLE cc_callback_spool ADD `num_attempts_noanswer` int(11) NOT NULL DEFAULT '0';
ALTER TABLE cc_callback_spool ADD `exten_leg_a` varchar(60) COLLATE utf8_bin NOT NULL;
ALTER TABLE cc_callback_spool ADD `last_status` varchar(80) COLLATE utf8_bin DEFAULT NULL;

ALTER TABLE cc_card ADD `cbtimeoutunavailable` int(11) NOT NULL DEFAULT '5';
ALTER TABLE cc_card ADD `cbattemptunavailable` int(11) NOT NULL DEFAULT '20';
ALTER TABLE cc_card ADD `cbtimeoutbusy` int(11) NOT NULL DEFAULT '20';
ALTER TABLE cc_card ADD `cbattemptbusy` int(11) NOT NULL DEFAULT '3';
ALTER TABLE cc_card ADD `cbtimeoutnoanswer` int(11) NOT NULL DEFAULT '10';
ALTER TABLE cc_card ADD `cbattemptnoanswer` int(11) NOT NULL DEFAULT '3';
ALTER TABLE cc_card ADD `cbtimeoutmax` int(11) NOT NULL DEFAULT '600';
