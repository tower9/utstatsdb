CREATE TABLE %dbpre%gitems (
  gi_match int(8) unsigned NOT NULL default 0,
  gi_item smallint(5) unsigned NOT NULL default 0,
  gi_plr smallint(6) NOT NULL default 0,
  gi_pickups smallint(5) unsigned NOT NULL default 0,
  KEY gi_gnumit (gi_match,gi_item)
) Type=MyISAM;
