CREATE TABLE %dbpre%pitems (
  pi_plr mediumint(8) unsigned NOT NULL default 0,
  pi_item smallint(5) unsigned NOT NULL default 0,
  pi_pickups mediumint(8) unsigned NOT NULL default 0,
  KEY pi_plritm(pi_plr,pi_item)
) Type=MyISAM;
