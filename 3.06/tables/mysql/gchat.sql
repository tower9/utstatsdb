CREATE TABLE %dbpre%gchat (
  gc_num bigint(12) unsigned NOT NULL auto_increment,
  gc_match int(10) unsigned NOT NULL default 0,
  gc_plr smallint(6) NOT NULL default 0,
  gc_team tinyint(3) unsigned NOT NULL default 0,
  gc_time int(10) unsigned NOT NULL default 0,
  gc_text varchar(255) NOT NULL default '',
  UNIQUE KEY gc_cnum (gc_num),
  KEY gc_matchtime (gc_match,gc_time)
) Type=MyISAM;
