ALTER TABLE cc_trunk ADD dialprefixmain char(30) COLLATE utf8_bin NOT NULL AFTER `creationdate`;
ALTER TABLE cc_trunk ADD cid_handover int(11) DEFAULT '0' AFTER `if_max_use`;
ALTER TABLE cc_trunk ADD outbound_cidgroup_id int(11) DEFAULT '-1';
ALTER TABLE cc_trunk ADD wrapuptime VARCHAR( 20 ) NOT NULL DEFAULT '0';
ALTER TABLE cc_trunk ADD wrapnexttime timestamp NOT NULL DEFAULT '0000-00-00 00:00:00';

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

ALTER TABLE cc_trunk ADD operator_credit DECIMAL( 15, 5 ) NULL DEFAULT NULL;
ALTER TABLE cc_trunk ADD ussd_credit VARCHAR( 10 ) NULL DEFAULT NULL;
ALTER TABLE cc_trunk ADD credit_posix_extract VARCHAR( 30 ) NULL DEFAULT NULL;
ALTER TABLE cc_trunk ADD ussd_rest_package VARCHAR( 10 ) NULL DEFAULT NULL;
ALTER TABLE cc_trunk ADD ussd_vaucher_prefix VARCHAR( 10 ) NULL DEFAULT NULL;
ALTER TABLE cc_trunk ADD ussd_check_time TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE cc_trunk
  CHANGE removeprefix removeprefix CHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL,
  CHANGE stopdatea    stopdatea    DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  CHANGE stopdateb    stopdateb    DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';

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
ALTER TABLE cc_card ADD extmin int(11) NOT NULL DEFAULT '701' AFTER `fax`;
ALTER TABLE cc_card ADD extmax int(11) NOT NULL DEFAULT '799' AFTER `extmin`;
ALTER TABLE cc_card ADD extquantity int(11) NOT NULL DEFAULT '24' AFTER `extmax`;
ALTER TABLE cc_card ADD paypal int(1) NOT NULL DEFAULT '0';
ALTER TABLE cc_card ADD id_diller bigint(20) NOT NULL DEFAULT 0 AFTER `id`;
ALTER TABLE cc_card ADD margin int(3) NOT NULL DEFAULT 0 AFTER `id_diller`;
ALTER TABLE cc_card ADD margin_diller int(3) NOT NULL DEFAULT 10 AFTER `margin`;
ALTER TABLE cc_card ADD margintotal DECIMAL( 15, 5 ) NOT NULL DEFAULT 1 AFTER `margin_diller`;
ALTER TABLE cc_card ADD commission DECIMAL( 15, 5 ) NOT NULL DEFAULT 0 AFTER `margintotal`;

ALTER TABLE cc_card CHANGE id_campaign id_campaign  INT( 11 ) NULL DEFAULT '-1';

ALTER TABLE cc_logrefill ADD diller_id bigint(20) NULL DEFAULT NULL;

ALTER TABLE cc_logpayment ADD fee DECIMAL( 15, 5 ) NOT NULL DEFAULT 0 AFTER `payment`;

ALTER TABLE cc_invoice_item ADD fee DECIMAL( 15, 5 ) NOT NULL DEFAULT 0 AFTER `price`;

CREATE TABLE IF NOT EXISTS `cc_trunk_rand` (
  `trunk_id` int(11) NOT NULL DEFAULT '0',
  `trunk_dependa` int(11) NOT NULL DEFAULT '0',
  `trunk_dependb` int(11) NOT NULL DEFAULT '0',
  `trunkpercentage` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`trunk_id`,`trunk_dependa`,`trunk_dependb`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE cc_invoice_conf CHANGE value value VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;
INSERT IGNORE INTO cc_invoice_conf (key_val) VALUES ('comments');

ALTER TABLE cc_sip_buddies ADD `external` INT( 11 ) NOT NULL DEFAULT '0' AFTER `id_cc_card`;
ALTER TABLE cc_sip_buddies ADD `callbackextension` varchar( 40 ) DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `directmedia` enum('yes', 'no', 'nonat', 'update', 'outgoing') NULL DEFAULT 'yes' AFTER `nat`;
ALTER TABLE cc_sip_buddies ADD `encryption` varchar( 20 ) COLLATE utf8_bin DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `encryption_taglen` enum('32','80') DEFAULT NULL AFTER `encryption`;
ALTER TABLE cc_sip_buddies ADD `ignorecryptolifetime` enum( 'yes', 'no' ) DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `transport` enum( 'tls', 'udp', 'tcp', 'udp,tcp', 'tcp,udp' ) DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `remotesecret` VARCHAR( 40 ) NULL DEFAULT NULL AFTER `secret`;
ALTER TABLE cc_sip_buddies ADD `trustrpid` enum('yes','no') DEFAULT NULL AFTER `insecure`;
ALTER TABLE cc_sip_buddies ADD `progressinband` enum('yes','no','never') DEFAULT NULL AFTER `trustrpid`;
ALTER TABLE cc_sip_buddies ADD `promiscredir` enum('yes','no') DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `useclientcode` enum('yes','no') DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `callcounter` enum('yes','no') DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `busylevel` int(11) DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `allowoverlap` enum('yes','no') DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `allowsubscribe` enum('yes','no') DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `videosupport` enum('yes','no') DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `rfc2833compensate` enum('yes','no') DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `session-timers` enum('accept','refuse','originate') DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `session-expires` int(11) DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `session-minse` int(11) DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `session-refresher` enum('uac','uas') DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `t38pt_udptl` enum('yes','yes,fec','yes,redundancy','yes,none','yes,fec,maxdatagram=400') DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `t38pt_usertpsource` varchar(40) DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `sendrpid` enum('yes','no') DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `timert1` int(11) DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `timerb` int(11) DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `qualifyfreq` int(11) DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `constantssrc` enum('yes','no') DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `contactpermit` varchar(40) DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `contactdeny` varchar(40) DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `textsupport` enum('yes','no') DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `faxdetect` enum('yes','no') DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `buggymwi` enum('yes','no') DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `fullname` varchar(40) DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `trunkname` varchar(40) DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `mohinterpret` varchar(40) DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `parkinglot` varchar(40) DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `hasvoicemail` enum('yes','no') DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `call-limit` int(11) DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `g726nonstandard` enum('yes','no') DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `ignoresdpversion` enum('yes','no') DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `dynamic` enum('yes','no') DEFAULT NULL;
ALTER TABLE cc_sip_buddies ADD `namedcallgroup` varchar(40) DEFAULT NULL AFTER `callgroup`;
ALTER TABLE cc_sip_buddies ADD `namedpickupgroup` varchar(40) DEFAULT NULL AFTER `namedcallgroup`;
ALTER TABLE cc_sip_buddies ADD warning_threshold INT( 11 ) NOT NULL DEFAULT '-2';
ALTER TABLE cc_sip_buddies ADD say_rateinitial SMALLINT( 6 ) NOT NULL DEFAULT '0';
ALTER TABLE cc_sip_buddies ADD say_balance_after_call SMALLINT( 6 ) NOT NULL DEFAULT '0';


ALTER TABLE cc_sip_buddies
  CHANGE `canreinvite` `canreinvite` varchar( 20 ) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
  CHANGE `nat` `nat` enum( 'force_rport,comedia','no','force_rport','comedia','auto_force_rport','auto_comedia','auto_force_rport,auto_comedia' ) NULL DEFAULT 'comedia' ,
  CHANGE `directmedia` `directmedia` enum( 'yes','no','nonat','update','update,nonat','outgoing' ) NULL DEFAULT 'update,nonat' ,
  CHANGE `qualify` `qualify` varchar( 40 ) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
  CHANGE `regseconds` `regseconds` int( 11 ) NULL DEFAULT NULL ,
  CHANGE `rtpholdtimeout` `rtpholdtimeout` int( 11 ) NULL DEFAULT NULL ,
  CHANGE `rtpkeepalive` `rtpkeepalive` int( 11 ) NULL DEFAULT NULL ,
  CHANGE `outboundproxy` `outboundproxy` varchar( 40 ) NULL DEFAULT NULL ,
  CHANGE `callbackextension` `callbackextension` varchar( 40 ) NULL DEFAULT NULL ,
  CHANGE `encryption` `encryption` varchar( 20 ) COLLATE utf8_bin NULL DEFAULT NULL ,
  CHANGE `transport` `transport` enum( 'tls', 'udp', 'tcp', 'udp,tcp', 'tcp,udp' ) NULL DEFAULT NULL ,
  CHANGE `callgroup` `callgroup` varchar( 40 ) NULL DEFAULT NULL ,
  CHANGE `pickupgroup` `pickupgroup` varchar( 40 ) NULL DEFAULT NULL ,
  CHANGE `ignorecryptolifetime` `ignorecryptolifetime` enum( 'yes', 'no' ) NULL DEFAULT NULL ,
  CHANGE `deny` `deny` varchar(40) DEFAULT NULL ,
  CHANGE `auth` `auth` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
  CHANGE `dtmfmode` `dtmfmode` ENUM( 'rfc2833', 'info', 'shortinfo', 'inband', 'auto' ) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT 'rfc2833' ,
  CHANGE `md5secret` `md5secret` VARCHAR( 40 ) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
  CHANGE `fullcontact` `fullcontact` VARCHAR( 80 ) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
  CHANGE `type` `type` ENUM( 'friend', 'user', 'peer' ) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT 'friend' ,
  CHANGE `callingpres` `callingpres` ENUM( 'allowed_not_screened', 'allowed_passed_screen', 'allowed_failed_screen', 'allowed', 'prohib_not_screened', 'prohib_passed_screen', 'prohib_failed_screen', 'prohib' ) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
  CHANGE `usereqphone` `usereqphone` ENUM('yes','no') CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL,
  CHANGE `DEFAULTip` `defaultip` CHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL,
  CHANGE `allowtransfer` `allowtransfer` ENUM('yes','no') CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL,
  CHANGE `subscribemwi` `subscribemwi` ENUM('yes','no') CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL;

ALTER TABLE cc_iax_buddies ADD `forceencryption` varchar(20) COLLATE utf8_bin NOT NULL;
ALTER TABLE cc_iax_buddies ADD `external` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE cc_iax_buddies CHANGE `DEFAULTip` `defaultip` CHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL;

ALTER TABLE cc_call ADD card_caller INT( 11 ) NOT NULL AFTER `card_id`;
ALTER TABLE cc_call ADD src_peername INT( 11 ) NULL DEFAULT NULL AFTER `src`;
ALTER TABLE cc_call ADD src_exten VARCHAR( 40 ) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL AFTER `src_peername`;
ALTER TABLE cc_call ADD calledexten INT( 11 ) NULL DEFAULT NULL AFTER `calledstation`;
ALTER TABLE cc_call ADD faxstatus SMALLINT( 6 ) NULL DEFAULT NULL;
ALTER TABLE cc_call ADD remotefaxid VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;
ALTER TABLE cc_call ADD faxpages SMALLINT( 6 ) NULL DEFAULT NULL;
ALTER TABLE cc_call ADD faxbitrate SMALLINT( 6 ) NULL DEFAULT NULL;
ALTER TABLE cc_call ADD faxresolution SMALLINT( 6 ) NULL DEFAULT NULL;
ALTER TABLE cc_call ADD margindillers DECIMAL( 15, 5 ) NOT NULL DEFAULT 0 AFTER `sessionbill`;
ALTER TABLE cc_call ADD margindiller DECIMAL( 15, 5 ) NOT NULL DEFAULT 0 AFTER `margindillers`;

ALTER TABLE cc_callerid ADD callback INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE cc_callerid ADD phonenumber VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;
ALTER TABLE cc_callerid ADD verify INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE cc_callerid ADD warning_threshold INT( 11 ) NOT NULL DEFAULT '-1';
ALTER TABLE cc_callerid ADD say_rateinitial SMALLINT( 6 ) NOT NULL DEFAULT '0';
ALTER TABLE cc_callerid ADD say_balance_after_call SMALLINT( 6 ) NOT NULL DEFAULT '0';
ALTER TABLE cc_callerid ADD cli_replace SMALLINT( 6 ) NOT NULL DEFAULT '0';
ALTER TABLE cc_callerid ADD cli_localreplace SMALLINT( 6 ) NOT NULL DEFAULT '0';
ALTER TABLE cc_callerid ADD cli_otherreplace SMALLINT( 6 ) NOT NULL DEFAULT '0';
ALTER TABLE cc_callerid ADD cli_prefixreplace CHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '';
ALTER TABLE cc_callerid ADD blacklist SMALLINT( 6 ) NOT NULL DEFAULT '0';

ALTER TABLE cc_did_destination ADD answer INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE cc_did_destination ADD playsound VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;
ALTER TABLE cc_did_destination ADD timeout VARCHAR( 3 ) NOT NULL ;

ALTER TABLE cc_did ADD id_trunk INT( 11 ) NOT NULL DEFAULT '-1';
ALTER TABLE cc_did ADD allciduse INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE cc_did ADD continuewithdid INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE cc_did CHANGE expirationdate expirationdate DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE cc_did ADD areaprefix SMALLINT( 6 ) NULL DEFAULT NULL;
ALTER TABLE cc_did ADD citylength SMALLINT( 6 ) NULL DEFAULT NULL;
ALTER TABLE cc_did ADD verify_callerid SMALLINT( 6 ) NOT NULL DEFAULT '0';
ALTER TABLE cc_did ADD voicebox VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL;

CREATE TABLE IF NOT EXISTS cc_voicemail_users (
	uniqueid BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	customer_id BIGINT(20) NOT NULL default '0',
	sip_buddy_id BIGINT(20) NOT NULL default '0',
	context CHAR(80) NOT NULL DEFAULT 'default',
	mailbox CHAR(80) NOT NULL,
	password CHAR(80) NOT NULL,
	fullname CHAR(80),
	email CHAR(80),
	pager CHAR(80),
	attach CHAR(3),
	attachfmt CHAR(10),
	serveremail CHAR(80),
	language CHAR(20),
	tz CHAR(30),
	deletevoicemail CHAR(3),
	saycid CHAR(3),
	sendvoicemail CHAR(3),
	review CHAR(3),
	tempgreetwarn CHAR(3),
	operator CHAR(3),
	envelope CHAR(3),
	sayduration CHAR(3),
	saydurationm INT(3),
	forcename CHAR(3),
	forcegreetings CHAR(3),
	callback CHAR(80),
	dialout CHAR(80),
	exitcontext CHAR(80),
	maxmsg INT(5),
	volgain DECIMAL(5,2),
	imapuser VARCHAR(80),
	imappassword VARCHAR(80),
	imapsever VARCHAR(80),
	imapport VARCHAR(8),
	imapflags VARCHAR(80),
	stamp timestamp,
	KEY `mailbox_context` (`mailbox`,`context`)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS cc_voicemail_data (
	filename CHAR(255) NOT NULL PRIMARY KEY,
	origmailbox CHAR(80),
	context CHAR(80),
	macrocontext CHAR(80),
	exten CHAR(80),
	priority CHAR(5),
	callerchan CHAR(80),
	callerid CHAR(80),
	origdate CHAR(30),
	origtime CHAR(11),
	category CHAR(30),
	duration CHAR(5)
);

drop procedure if exists a2b_trf_check;

delimiter //

create procedure a2b_trf_check()
begin
    declare a int;
    select count(*) into a from cc_voicemail_data;
    if a=0 then
	insert into cc_voicemail_users(customer_id, context, mailbox, password, fullname, email, language)
	select S.id_cc_card, S.accountcode, S.regexten, '0000', concat(C.lastname,' ',C.firstname) fullname, C.email, S.language
	from cc_sip_buddies S, cc_card C
	where S.regexten IS NOT NULL AND S.regexten<>'' AND S.id_cc_card=C.id;
    end if;
end //

delimiter ;

call a2b_trf_check;

drop procedure if exists a2b_trf_check;

delimiter //

create procedure a2b_trf_check()
begin
    declare a int;
    select count(*) into a from cc_config where config_key='fax_path';
    if a=0 then
	INSERT INTO cc_config (id, config_title, config_key, config_value, config_description, config_valuetype, config_listvalues, config_group_title)
	VALUES (NULL , 'Fax Path', 'fax_path', '/var/spool/asterisk/fax', 'Path to link the stored fax files.', '0', NULL , 'webui');
    elseif a>1 then
	select id into a from cc_config where config_key='fax_path' order by id limit 0,1;
	delete from cc_config where config_key='fax_path' and id>a;
    end if;
end //

delimiter ;

call a2b_trf_check;

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
    select count(*) into a from cc_config where config_key='main_title';
    if a=0 then
	INSERT INTO cc_config (id, config_title, config_key, config_value, config_description, config_valuetype, config_listvalues, config_group_title)
	VALUES (NULL , 'Main Title', 'main_title', '..:: A2Billing Portal ::..', 'HTML title Attribute', '0', NULL , 'webui');
    elseif a>1 then
	select id into a from cc_config where config_key='main_title' order by id limit 0,1;
	delete from cc_config where config_key='main_title' and id>a;
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

delimiter //

create procedure a2b_trf_check()
begin
    declare a int;
    select count(*) into a from cc_config where config_key='context_surveillance';
    if a=0 then
	INSERT INTO `cc_config` (`id`, `config_title`, `config_key`, `config_value`, `config_description`, `config_valuetype`, `config_listvalues`, `config_group_title`)
	VALUES (NULL, 'Context Surveillance', 'context_surveillance', 'surveillance', 'Context to use in Surveillance', '0', NULL, 'callback');
    elseif a>1 then
	select id into a from cc_config where config_key='context_surveillance' order by id limit 0,1;
	delete from cc_config where config_key='context_surveillance' and id>a;
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
ALTER TABLE cc_callback_spool ADD `surveillance` INT( 11 ) NOT NULL DEFAULT '0';

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
	('Extra field for testmode', 'MODULE_PAYMENT_WM_LMI_SIM_MODE', '0', '0 - simulate success; 1 - simulate fail; 2 - simulate 80% success, 20% fail', 0, NULL, NULL),
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

CREATE TABLE IF NOT EXISTS `cc_card_concat` (
  `concat_id` int(11) NOT NULL DEFAULT '0',
  `concat_card_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`concat_id`,`concat_card_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `cc_fax` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_cc_card` int(11) NOT NULL DEFAULT '0',
  `ext_num` varchar(20) COLLATE utf8_bin NOT NULL,
  `localstationid` varchar(20) COLLATE utf8_bin NOT NULL,
  `localheaderinfo` varchar(50) COLLATE utf8_bin NOT NULL,
  `store` smallint(6) NOT NULL DEFAULT '1',
  `email` varchar(70) COLLATE utf8_bin NOT NULL,
  `notify_email` smallint(6) NOT NULL DEFAULT '1',
  `printer_type` varchar(60) COLLATE utf8_bin NOT NULL,
  `printer_ip` varchar(60) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`,`ext_num`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT IGNORE INTO cc_prefix (`prefix`, `destination`) VALUES ('0', 'Internal Call');
INSERT IGNORE INTO cc_prefix (`prefix`, `destination`) VALUES ('-1', '');
INSERT IGNORE INTO cc_prefix (`prefix`, `destination`) VALUES ('-2', '<b>FAX</b>');

INSERT IGNORE INTO cc_templatemail (`id_language`, `mailtype`, `fromemail`, `fromname`, `subject`, `messagetext`, `messagehtml`) VALUES
('en', 'fax_success', 'fax_robot@sipde.net', 'Fax Robot', 'FAX received from $cid_number$', 'You have just received a $count$ page fax from $cid_number$ $cid_name$, at phone number $dest_exten$, on $datetime$.\r\nThe original fax document is attached in $format$ format.\r\n\r\nYou may also download copy of fax from your personal web page <a href="https://customer.sipde.net/fax-history.php">https://customer.sipde.net/fax-history.php</a>\r\n\r\nKind regards,\r\nTeam <a href="http://www.sipde.net/">SIPDE.NET</a>', NULL),
('en', 'fax_failed', 'fax_robot@sipde.net', 'Fax Robot', 'Problem receiving the fax from $cid_number$', 'You have just received a fax attempt from $cid_number$ $cid_name$, at phone number $dest_exten$, on $datetime$.\r\nThere was a problem receiving the fax:  $fax_result$.\r\n\r\nYou may download copy of your faxes from your personal web page <a href="https://customer.sipde.net/fax-history.php">https://customer.sipde.net/fax-history.php</a>\r\n\r\nKind regards,\r\nTeam <a href="http://www.sipde.net/">SIPDE.NET</a>', NULL),
('ru', 'fax_success', 'fax_robot@sipde.net', 'Fax Robot', 'Факс от $cid_number$', 'Получен факс из $count$ страниц/ы от $cid_number$ $cid_name$, на номер $dest_exten$.\r\n Время получения $datetime$.\r\nФакс в формате $format$ прикреплен к этому письму как обычный файл.\r\n\r\nВы так же можете загрузить копию этого факса с вашей персональной страницы <a href="https://customer.sipde.net/fax-history.php">кликнув на эту ссылку</a>.\r\n\r\nСпасибо за Ваш интерес к сервису <a href="http://www.sipde.net/">SIPDE.NET</a> !', NULL),
('ru', 'fax_failed', 'fax_robot@sipde.net', 'Fax Robot', 'Попытка получения факса от $cid_number$', 'Факс от $cid_number$ $cid_name$ для $dest_exten$ не был получен по причине: $fax_result$.\r\n\r\nВы можете загружать копии полученных факсов с вашей персональной страницы <a href="https://customer.sipde.net/fax-history.php">кликнув на эту ссылку</a>.\r\nНастроить получение факсов можно в разделе МОЯ АТС => ФАКС МАШИНА.\r\n\r\nСпасибо за Ваш интерес к сервису <a href="http://www.sipde.net/">SIPDE.NET</a> !', NULL),
('ru', 'signup', 'info@sipde.net', 'No Reply', 'АКТИВАЦИЯ УЧЁТНОЙ ЗАПИСИ', 'Спасибо за регистрацию в сервисе SIPDE.NET\r\n\r\nЧтобы активировать Вашу учётную запись кликните по ссылке:\r\n<a href="https://customer.sipde.net/activate.php?key=$loginkey$">https://customer.sipde.net/activate.php?key=$loginkey$</a>\r\n\r\nЧтобы начать пользоваться сервисами пополните после активации балланс Вашего аккаунта с помощью кредитной карты, систем PayPal или WebMoney со <a href="https://customer.sipde.net/checkout_payment.php?ui_language=russian">страницы управления</a> Вашим аккаунтом.\r\n\r\n--\r\nС уважением.\r\nКоманда <a href="http://www.sipde.net/">SIPDE.NET</a>', NULL),
('ru', 'signupconfirmed', 'info@sipde.net', 'No Reply', 'ПОДТВЕРЖДЕНИЕ РЕГИСТРАЦИИ', 'Благодарим за регистрацию!\r\n\r\nНачинайте экономить на связи уже сейчас и упрощайте свою жизнь с помощью нашего сервиса. Ведь теперь, Вы сам себе оператор!\r\n\r\nИнформация для доступа к управлению <u>Вашей учётной записью</u>:\r\nНомер учётной записи: $cardnumber$\r\nЛогин для входа: $login$\r\nПароль: $password$\r\nСсылка для входа: <a href="https://customer.sipde.net/?ui_language=russian">https://customer.sipde.net/</a>\r\n\r\nРекомендуется удалить данное письмо после прочтения.\r\n\r\n--\r\nСпасибо за ваш интерес к сервису <a href="http://www.sipde.net/">SIPDE.NET</a> !', NULL),
('ru', 'did_paid', 'info@sipde.net', 'SIPDE.NET', 'DID уведомление - ($did$)', '\r\nОСТАТОК НА БАЛАНСЕ ПОСЛЕ ОПЛАТЫ: $balance_remaining$ $base_currency$\r\n\r\nАвтоматически списано $did_cost$ $base_currency$ с Вашего счёта чтобы оплатить ежемесячный взнос за Ваш DID номер ($did$).\r\n\r\nЕжемесячный взнос за DID номер : $did_cost$ $base_currency$\r\n\r\n--\r\n<a href="http://www.sipde.net/">SIPDE.NET</a>', NULL),
('ru', 'did_unpaid', 'info@sipde.net', 'SIPDE.NET', 'DID уведомление - ($did$)', '\r\nОСТАТОК НА БАЛАНСЕ: $balance_remaining$ $base_currency$\r\n\r\nУ Вас не хватает средств чтобы оплатить Ваш DID номер ($did$), ежемесячный платёж составляет: $did_cost$ $base_currency$\r\n\r\nПроизведено резервирование номера на дополнительные $days_remaining$ дней чтобы Вы могли оплатить выставленный счёт (REF: $invoice_ref$). После истечения этого срока DID номер станет свободен для заказа в обычном порядке.\r\n\r\n--\r\n<a href="http://www.sipde.net/">SIPDE.NET</a>', NULL),
('ru', 'did_released', 'info@sipde.net', 'SIPDE.NET', 'DID уведомление - ($did$)', '\r\nУ Вас не хватило средств чтобы оплатить Ваш DID номер ($did$), ежемесячный платёж составлял: $did_cost$ $base_currency$\r\n\r\nDID номер $did$ был автоматически отключен и переведен в свободную продажу!\r\nВы можете заказать его вновь в обычном порядке.\r\n\r\n--\r\n<a href="http://www.sipde.net/">SIPDE.NET</a>', NULL);

CREATE TABLE IF NOT EXISTS `cc_trunk_credit` (
  `trunk_id` int(11) NOT NULL,
  `checktime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `operator_credit` DECIMAL( 15, 5 ) NOT NULL,
  PRIMARY KEY (`trunk_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS `cc_callplan_lcr`;
DROP VIEW `cc_callplan_lcr`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `cc_callplan_lcr` AS SELECT
`cc_ratecard`.`id` AS `id`,
`cc_prefix`.`destination` AS `destination`,
`cc_ratecard`.`dialprefix` AS `dialprefix`,
`cc_ratecard`.`buyrate` AS `buyrate`,
`cc_ratecard`.`rateinitial` AS `rateinitial`,
`cc_ratecard`.`startdate` AS `startdate`,
`cc_ratecard`.`stopdate` AS `stopdate`,
`cc_ratecard`.`initblock` AS `initblock`,
`cc_ratecard`.`connectcharge` AS `connectcharge`,
`cc_ratecard`.`id_trunk` AS `id_trunk`,
`cc_ratecard`.`idtariffplan` AS `idtariffplan`,
`cc_ratecard`.`id` AS `ratecard_id`,
`cc_tariffgroup`.`id` AS `tariffgroup_id`,
`cc_tariffplan`.`tariffname` AS `tariffname`
FROM `cc_tariffgroup_plan`
left join `cc_tariffgroup` on `cc_tariffgroup_plan`.`idtariffgroup` = `cc_tariffgroup`.`id`
join `cc_tariffplan` on `cc_tariffplan`.`id` = `cc_tariffgroup_plan`.`idtariffplan`
left join `cc_ratecard` on `cc_ratecard`.`idtariffplan` = `cc_tariffplan`.`id`
left join `cc_prefix` on `cc_prefix`.`prefix` = `cc_ratecard`.`destination`
where (`cc_ratecard`.`id` is not null);
