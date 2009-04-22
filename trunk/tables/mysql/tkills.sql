CREATE TABLE %dbpre%tkills (
  tk_match int(10) unsigned NOT NULL default 0,
  tk_team tinyint(3) unsigned NOT NULL default 0,
  tk_score smallint(6) NOT NULL default 0,
  tk_time int(10) unsigned NOT NULL default 0,
  KEY tk_gnumteam (tk_match,tk_team,tk_time)
) Type=MyISAM;
