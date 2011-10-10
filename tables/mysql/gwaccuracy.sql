CREATE TABLE %dbpre%gwaccuracy (
  gwa_num int(10) unsigned NOT NULL auto_increment,
  gwa_match mediumint(8) unsigned NOT NULL default 0,
  gwa_player mediumint(8) unsigned NOT NULL default 0,
  gwa_weapon smallint(5) unsigned NOT NULL default 0,
  gwa_fired int(10) unsigned NOT NULL default 0,
  gwa_hits int(10) unsigned NOT NULL default 0,
  gwa_damage int(10) unsigned NOT NULL default 0,
  UNIQUE KEY gwa_num (gwa_num),
  KEY gwa_mplrwp (gwa_match,gwa_player,gwa_weapon)
) Engine=MyISAM;
