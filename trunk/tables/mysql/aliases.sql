CREATE TABLE %dbpre%aliases (
  al_pnum mediumint(8) unsigned NOT NULL default 0,
  al_key varchar(32) NOT NULL default '',
  KEY al_numkey (al_pnum,al_key)
) Engine=MyISAM;