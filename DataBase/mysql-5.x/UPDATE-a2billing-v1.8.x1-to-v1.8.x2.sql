ALTER TABLE cc_trunk ADD (
  `dialprefixmain` char(30) COLLATE utf8_bin NOT NULL,
  `outbound_cidgroup_id` int(11) DEFAULT '-1',
  `dialprefixa` char(30) COLLATE utf8_bin NOT NULL,
  `periodcounta` int(11) DEFAULT '0',
  `periodexpirya` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timelefta` int(11) NOT NULL DEFAULT '60',
  `perioda` int(11) DEFAULT '0',
  `startdatea` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `stopdatea` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `billblockseca` int(11) NOT NULL DEFAULT '1',
  `maxsecperperioda` int(11) DEFAULT '-1',
  `lastcallstoptimea` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `failover_trunka` int(11) DEFAULT NULL,
  `dialprefixb` char(30) COLLATE utf8_bin NOT NULL,
  `periodcountb` int(11) DEFAULT '0',
  `periodexpiryb` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timeleftb` int(11) NOT NULL DEFAULT '60',
  `periodb` int(11) DEFAULT '0',
  `startdateb` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `stopdateb` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `billblocksecb` int(11) NOT NULL DEFAULT '1',
  `maxsecperperiodb` int(11) DEFAULT '-1',
  `lastcallstoptimeb` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `failover_trunkb` int(11) DEFAULT NULL
);

ALTER TABLE cc_card ADD (
  monitor int(11) DEFAULT '0',
  recalldays int(11) NOT NULL DEFAULT '10',
  recalltime int(11) NOT NULL DEFAULT '7200'
);

CREATE TABLE IF NOT EXISTS `cc_trunk_rand` (
  `trunk_id` int(11) NOT NULL DEFAULT '0',
  `trunk_dependa` int(11) NOT NULL DEFAULT '0',
  `trunk_dependb` int(11) NOT NULL DEFAULT '0',
  `trunkpercentage` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`trunk_id`,`trunk_dependa`,`trunk_dependb`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE cc_invoice_conf CHANGE value value VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;
INSERT INTO cc_invoice_conf (key_val) VALUES ('comments');
