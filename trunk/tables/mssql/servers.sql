CREATE TABLE %dbpre%servers
(
   sv_num int NOT NULL identity(1, 1),
   sv_name varchar(45) NOT NULL default '',
   sv_shortname varchar(30) NOT NULL default '',
   sv_addr varchar(60) NOT NULL default '',
   sv_matches int NOT NULL default 0,
   sv_frags int NOT NULL default 0,
   sv_score int NOT NULL default 0,
   sv_time bigint NOT NULL default 0,
   sv_lastmatch datetime NOT NULL default '1900-01-01 00:00:00',
   sv_admin varchar(35) NOT NULL default '',
   sv_email varchar(45) NOT NULL default '',
   sv_address varchar(21) NOT NULL default '',
   CONSTRAINT sv_num primary key (sv_num)
);

CREATE INDEX sv_name ON %dbpre%servers (sv_name);
CREATE INDEX sv_shortname ON %dbpre%servers (sv_shortname);
