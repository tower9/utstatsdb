CREATE TABLE %dbpre%gkills (
  gk_match int(10) unsigned NOT NULL default 0,
  gk_killer smallint(6) NOT NULL default 0,
  gk_victim smallint(6) NOT NULL default 0,
  gk_time int(10) unsigned NOT NULL default 0,
  gk_kweapon smallint(6) NOT NULL default 0,
  gk_vweapon smallint(6) NOT NULL default 0,
  gk_kteam tinyint(4) NOT NULL default 0,
  gk_vteam tinyint(4) NOT NULL default 0,
  KEY gk_gnum (gk_match)
) Engine=MyISAM;
