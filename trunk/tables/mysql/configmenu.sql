CREATE TABLE %dbpre%configmenu (
  num smallint(5) unsigned NOT NULL auto_increment,
  url varchar(200) NOT NULL default '',
  descr varchar(30) NOT NULL default '',
  UNIQUE KEY num (num)
) Engine=MyISAM;
