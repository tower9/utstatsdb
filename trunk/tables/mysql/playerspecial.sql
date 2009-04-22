CREATE TABLE %dbpre%playerspecial (
  ps_pnum mediumint(8) unsigned NOT NULL,
  ps_stype smallint(5) unsigned NOT NULL,
  ps_total mediumint(8) unsigned NOT NULL default 0,
  KEY ps_ptype (ps_pnum,ps_stype)
) Type=MyISAM;
