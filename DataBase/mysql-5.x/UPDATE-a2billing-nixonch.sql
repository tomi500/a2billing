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
ALTER TABLE cc_card ADD cbtimeoutunavailable int(11) NOT NULL DEFAULT '5';
ALTER TABLE cc_card ADD cbattemptunavailable int(11) NOT NULL DEFAULT '20';
ALTER TABLE cc_card ADD cbtimeoutbusy int(11) NOT NULL DEFAULT '20';
ALTER TABLE cc_card ADD cbattemptbusy int(11) NOT NULL DEFAULT '3';
ALTER TABLE cc_card ADD cbtimeoutnoanswer int(11) NOT NULL DEFAULT '10';
ALTER TABLE cc_card ADD cbattemptnoanswer int(11) NOT NULL DEFAULT '3';
ALTER TABLE cc_card ADD cbtimeoutmax int(11) NOT NULL DEFAULT '600';
ALTER TABLE cc_card ADD paypal INT( 1 ) NOT NULL DEFAULT '0';

CREATE TABLE IF NOT EXISTS `cc_trunk_rand` (
  `trunk_id` int(11) NOT NULL DEFAULT '0',
  `trunk_dependa` int(11) NOT NULL DEFAULT '0',
  `trunk_dependb` int(11) NOT NULL DEFAULT '0',
  `trunkpercentage` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`trunk_id`,`trunk_dependa`,`trunk_dependb`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE cc_invoice_conf CHANGE value value VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;
INSERT IGNORE INTO cc_invoice_conf (key_val) VALUES ('comments');

ALTER TABLE cc_sip_buddies ADD `callbackextension` varchar( 40 ) DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `directmedia` enum('yes','no','nonat','update','update,nonat') NULL DEFAULT 'update,nonat';
ALTER TABLE cc_sip_buddies ADD `encryption` varchar( 20 ) COLLATE utf8_bin DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `ignorecryptolifetime` enum( 'yes', 'no' ) DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `transport` enum( 'tls', 'udp', 'tcp', 'udp,tcp', 'tcp,udp' ) DEFAULT NULL;

ALTER TABLE cc_sip_buddies
  CHANGE `canreinvite` `canreinvite` varchar( 20 ) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
  CHANGE `nat` `nat` enum( 'yes', 'no', 'force_rport', 'comedia', 'never', 'route' ) NULL DEFAULT 'yes' ,
  CHANGE `qualify` `qualify` varchar( 40 ) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
  CHANGE `regseconds` `regseconds` int( 11 ) NULL DEFAULT NULL ,
  CHANGE `rtpholdtimeout` `rtpholdtimeout` int( 11 ) NULL DEFAULT NULL ,
  CHANGE `rtpkeepalive` `rtpkeepalive` int( 11 ) NULL DEFAULT NULL ,
  CHANGE `outboundproxy` `outboundproxy` varchar( 40 ) NULL DEFAULT NULL ,
  CHANGE `callbackextension` `callbackextension` varchar( 40 ) NULL DEFAULT NULL ,
  CHANGE `directmedia` `directmedia` enum( 'yes', 'no', 'nonat', 'update', 'update,nonat' ) NULL DEFAULT 'update,nonat',
  CHANGE `encryption` `encryption` varchar( 20 ) COLLATE utf8_bin NULL DEFAULT NULL ,
  CHANGE `transport` `transport` enum( 'tls', 'udp', 'tcp', 'udp,tcp', 'tcp,udp' ) NULL DEFAULT NULL ,
  CHANGE `callgroup` `callgroup` varchar( 40 ) NULL DEFAULT NULL ,
  CHANGE `pickupgroup` `pickupgroup` varchar( 40 ) NULL DEFAULT NULL ,
  CHANGE `ignorecryptolifetime` `ignorecryptolifetime` enum( 'yes', 'no' ) NULL DEFAULT NULL;

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
ALTER TABLE cc_did ADD continuewithdid INT( 11 ) NOT NULL DEFAULT '0';

drop procedure if exists a2b_trf_check;

delimiter //

create procedure a2b_trf_check()
begin
    declare a int;
    select count(*) into a from cc_config where config_key='date_timezone';
    if a=0 then
	INSERT INTO cc_config (id, config_title, config_key, config_value, config_description, config_valuetype, config_listvalues, config_group_title)
	VALUES (NULL , 'Time Zone', 'date_timezone', '0', 'Defines the default timezone used by the date functions, eg Europe/Kiev', '0', NULL , 'global');
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

ALTER TABLE cc_callback_spool ADD `next_attempt_time` timestamp NULL DEFAULT NULL;
ALTER TABLE cc_callback_spool ADD `reason` int(11) DEFAULT NULL;
ALTER TABLE cc_callback_spool ADD `num_attempts_unavailable` int(11) NOT NULL DEFAULT '0';
ALTER TABLE cc_callback_spool ADD `num_attempts_busy` int(11) NOT NULL DEFAULT '0';
ALTER TABLE cc_callback_spool ADD `num_attempts_noanswer` int(11) NOT NULL DEFAULT '0';
ALTER TABLE cc_callback_spool ADD `exten_leg_a` varchar(60) COLLATE utf8_bin NOT NULL;
ALTER TABLE cc_callback_spool ADD `last_status` varchar(80) COLLATE utf8_bin DEFAULT NULL;

ALTER TABLE cc_payment_methods ADD UNIQUE `SECONDARY` ( `payment_method` );
INSERT IGNORE INTO cc_payment_methods (`payment_method`, `payment_filename`) VALUES ('WebMoney', 'webmoney.php');

delimiter //

create procedure a2b_trf_check()
begin
    declare a int;
    select count(*) into a from cc_payment_methods where payment_method='WebMoney';
    if a>1 then
	select id into a from cc_payment_methods where payment_method='WebMoney' order by id limit 0,1;
	delete from cc_payment_methods where payment_method='WebMoney' and id>a;
    end if;
end //

delimiter ;

call a2b_trf_check;

drop procedure if exists a2b_trf_check;

ALTER TABLE cc_configuration ADD UNIQUE `SECONDARY` ( `configuration_key` );
INSERT IGNORE INTO `cc_configuration` (`configuration_title`, `configuration_key`, `configuration_value`, `configuration_description`, `configuration_type`, `use_function`, `set_function`) VALUES
	('Enable webmoney Module', 'MODULE_PAYMENT_WM_STATUS', 'False', 'Do you want to accept webmoney payments?', 0, NULL, 'tep_cfg_select_option(array(''True'', ''False''),'),
	('Provider WMID', 'MODULE_PAYMENT_WM_WMID', '111111111111', '', 0, NULL, NULL),
	('WME Purse', 'MODULE_PAYMENT_WM_PURSE_WME', 'E222222222222', 'Euro (EUR)', 0, NULL, NULL),
	('WMR purse', 'MODULE_PAYMENT_WM_PURSE_WMR', 'R333333333333', 'Russian Rouble (RUB)', 0, NULL, NULL),
	('WMZ purse', 'MODULE_PAYMENT_WM_PURSE_WMZ', 'Z444444444444', 'U.S. Dollar (USD)', 0, NULL, NULL),
	('WMU Purse', 'MODULE_PAYMENT_WM_PURSE_WMU', 'U555555555555', 'Ukraine Hryvnia (UAH)', 0, NULL, NULL),
	('WebMoney', 'MODULE_PAYMENT_WM_CACERT', './WebMoneyTransferRootCA.crt', 'root certificate path, in PEM-format', 0, NULL, NULL),
	('Secret Key', 'MODULE_PAYMENT_WM_LMI_SECRET_KEY', 'Secret Key', 'Known to seller and WM Merchant Interface service on', 0, NULL, NULL),
	('Extra field for testmode', 'MODULE_PAYMENT_WM_LMI_SIM_MODE', '0', '0 - simulate success; 1 - simulate fail; 2 - simulate 80% sucsess, 20% fail', 0, NULL, NULL),
	('Hash method', 'MODULE_PAYMENT_WM_LMI_HASH_METHOD', 'MD 5', 'Method of forming control signature', 0, NULL, 'tep_cfg_select_option(array(''MD 5'', ''SIGN''),');

delimiter //

create procedure a2b_trf_check()
begin
    declare a int;
    select count(*) into a from cc_configuration where configuration_key='MODULE_PAYMENT_WM_STATUS';
    if a>1 then
	select id into a from cc_configuration where configuration_key='MODULE_PAYMENT_WM_LMI_HASH_METHOD' order by id limit 0,1;
	delete from cc_configuration where configuration_key='MODULE_PAYMENT_WM_LMI_HASH_METHOD' and id>a;
	delete from cc_configuration where configuration_key='MODULE_PAYMENT_WM_LMI_SIM_MODE' and id>a;
	delete from cc_configuration where configuration_key='MODULE_PAYMENT_WM_LMI_SECRET_KEY' and id>a;
	delete from cc_configuration where configuration_key='MODULE_PAYMENT_WM_CACERT' and id>a;
	delete from cc_configuration where configuration_key='MODULE_PAYMENT_WM_PURSE_WMU' and id>a;
	delete from cc_configuration where configuration_key='MODULE_PAYMENT_WM_PURSE_WMZ' and id>a;
	delete from cc_configuration where configuration_key='MODULE_PAYMENT_WM_PURSE_WMR' and id>a;
	delete from cc_configuration where configuration_key='MODULE_PAYMENT_WM_PURSE_WME' and id>a;
	delete from cc_configuration where configuration_key='MODULE_PAYMENT_WM_WMID' and id>a;
	delete from cc_configuration where configuration_key='MODULE_PAYMENT_WM_STATUS' and id>a;
    end if;
end //

delimiter ;

call a2b_trf_check;

drop procedure if exists a2b_trf_check;
