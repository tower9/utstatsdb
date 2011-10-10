CREATE TABLE %dbpre%configquery (
  num smallint(5) unsigned NOT NULL auto_increment,
  server varchar(200) NOT NULL default '',
  port smallint(5) unsigned NOT NULL default 7777,
  type tinyint(1) NOT NULL default 0,
  password varchar(40) NOT NULL default '',
  link varchar(200) NOT NULL default '',
  spectators tinyint(1) NOT NULL default 1,
  bots tinyint(1) NOT NULL default 1,
  UNIQUE KEY num (num)
) Engine=MyISAM;
