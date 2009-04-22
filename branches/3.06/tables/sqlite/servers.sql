CREATE TABLE %dbpre%servers (
  sv_num INTEGER PRIMARY KEY,
  sv_name varchar(45) NOT NULL default '',
  sv_shortname varchar(30) NOT NULL default '',
  sv_addr varchar(60) NOT NULL default '',
  sv_matches int(10) NOT NULL default 0,
  sv_frags mediumint(11) NOT NULL default 0,
  sv_score mediumint(11) NOT NULL default 0,
  sv_time bigint(19) NOT NULL default 0,
  sv_lastmatch datetime NOT NULL default '0000-00-00 00:00:00',
  sv_admin varchar(35) NOT NULL default '',
  sv_email varchar(45) NOT NULL default '',
  sv_address varchar(21) NOT NULL default ''
);

CREATE INDEX sv_name ON %dbpre%servers (sv_name);
CREATE INDEX sv_shortname ON %dbpre%servers (sv_shortname);
