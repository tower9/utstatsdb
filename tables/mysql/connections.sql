CREATE TABLE %dbpre%connections (
  cn_match int(10) unsigned NOT NULL default 0,
  cn_pnum smallint(5) unsigned NOT NULL default 0,
  cn_ctime TIMESTAMP NOT NULL default CURRENT_TIMESTAMP,
  cn_dtime TIMESTAMP NOT NULL default CURRENT_TIMESTAMP,
  KEY cn_matchplr (cn_match,cn_pnum),
  KEY cn_matchdt (cn_match,cn_dtime),
  KEY cn_times (cn_ctime,cn_dtime)
) Engine=MyISAM;