CREATE TABLE %dbpre%gevents (
  ge_num bigint(12) unsigned NOT NULL auto_increment,
  ge_match int(10) unsigned NOT NULL default 0,
  ge_plr smallint(6) NOT NULL default 0,
  ge_event tinyint(3) unsigned NOT NULL default 0,
  ge_time int(10) unsigned NOT NULL default 0,
  ge_length int(10) unsigned NOT NULL default 0,
  ge_quant mediumint(9) NOT NULL default 0,
  ge_reason tinyint(3) unsigned NOT NULL default 0,
  ge_opponent smallint(6) NOT NULL default 0,
  ge_item smallint(5) unsigned NOT NULL default 0,
  UNIQUE KEY ge_num (ge_num),
  KEY ge_gnumev (ge_match,ge_event,ge_num),
  KEY ge_kstype (ge_event,ge_match,ge_plr,ge_time)
) Engine=MyISAM;
