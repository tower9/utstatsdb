CREATE TABLE %dbpre%playersgts (
  gs_pnum mediumint(8) unsigned NOT NULL,
  gs_stype smallint(5) unsigned NOT NULL,
  gs_tnum smallint(5) unsigned NOT NULL,
  gs_total mediumint(8) unsigned NOT NULL default 0,
  KEY gs_ptn (gs_pnum,gs_stype,gs_tnum)
) Type=MyISAM;
