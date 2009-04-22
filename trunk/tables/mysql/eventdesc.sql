CREATE TABLE %dbpre%eventdesc (
  ed_num int(10) unsigned NOT NULL auto_increment,
  ed_desc varchar(32) NOT NULL default '',
  UNIQUE KEY ed_num (ed_num),
  KEY ed_desc (ed_desc)
) Type=MyISAM;
