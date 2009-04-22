CREATE TABLE %dbpre%gspecials (
  gs_match int(10) unsigned NOT NULL,
  gs_player smallint(5) unsigned NOT NULL,
  gs_stype smallint(5) unsigned NOT NULL,
  gs_total mediumint(8) unsigned NOT NULL default 0,
  KEY gs_mps (gs_match,gs_player,gs_stype)
) Type=MyISAM;
