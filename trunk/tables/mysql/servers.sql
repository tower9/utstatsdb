CREATE TABLE %dbpre%servers (
  sv_num mediumint(8) unsigned NOT NULL auto_increment,
  sv_name varchar(45) NOT NULL default '',
  sv_shortname varchar(30) NOT NULL default '',
  sv_addr varchar(60) NOT NULL default '',
  sv_matches int(10) unsigned NOT NULL default 0,
  sv_frags int(11) NOT NULL default 0,
  sv_score int(11) NOT NULL default 0,
  sv_time bigint(19) unsigned NOT NULL default 0,
  sv_lastmatch datetime NOT NULL default '0000-00-00 00:00:00',
  sv_admin varchar(35) NOT NULL default '',
  sv_email varchar(45) NOT NULL default '',
  sv_address varchar(21) NOT NULL default '',
  UNIQUE KEY sv_num (sv_num),
  KEY sv_name (sv_name),
  KEY sv_shortname (sv_shortname)
) Type=MyISAM;
