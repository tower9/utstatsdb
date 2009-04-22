CREATE TABLE %dbpre%pwkills (
  pwk_num int(10) unsigned NOT NULL auto_increment,
  pwk_player mediumint(8) unsigned NOT NULL default 0,
  pwk_weapon smallint(5) unsigned NOT NULL default 0,
  pwk_frags mediumint(8) NOT NULL default 0,
  pwk_kills mediumint(8) unsigned NOT NULL default 0,
  pwk_deaths mediumint(8) unsigned NOT NULL default 0,
  pwk_held mediumint(8) unsigned NOT NULL default 0,
  pwk_suicides mediumint(8) unsigned NOT NULL default 0,
  pwk_fired int(10) unsigned NOT NULL default 0,
  pwk_hits int(10) unsigned NOT NULL default 0,
  pwk_damage int(10) unsigned NOT NULL default 0,
  UNIQUE KEY pwk_num (pwk_num),
  KEY pwk_plrwp (pwk_player,pwk_weapon)
) Type=MyISAM;
