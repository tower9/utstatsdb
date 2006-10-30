CREATE TABLE %dbpre%mwkills (
  mwk_num mediumint(8) unsigned NOT NULL auto_increment,
  mwk_map mediumint(8) unsigned NOT NULL default 0,
  mwk_weapon smallint(5) unsigned NOT NULL default 0,
  mwk_kills mediumint(8) unsigned NOT NULL default 0,
  mwk_deaths mediumint(8) unsigned NOT NULL default 0,
  mwk_held mediumint(8) unsigned NOT NULL default 0,
  mwk_suicides mediumint(8) unsigned NOT NULL default 0,
  UNIQUE KEY mwk_num (mwk_num),
  KEY mwk_mapwp (mwk_map,mwk_weapon)
) Type=MyISAM;
