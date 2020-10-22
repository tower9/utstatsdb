CREATE TABLE %dbpre%maps (
  mp_num mediumint(8) unsigned NOT NULL auto_increment,
  mp_name varchar(32) NOT NULL default '',
  mp_desc varchar(32) NOT NULL default '',
  mp_author varchar(32) NOT NULL default '',
  mp_matches mediumint(8) unsigned NOT NULL default 0,
  mp_score mediumint(9) NOT NULL default 0,
  mp_kills mediumint(8) unsigned NOT NULL default 0,
  mp_deaths mediumint(8) unsigned NOT NULL default 0,
  mp_suicides mediumint(8) unsigned NOT NULL default 0,
  mp_time bigint(19) unsigned NOT NULL default 0,
  mp_lastmatch datetime NOT NULL default '2004-03-16 00:00:00',
  UNIQUE KEY mp_mapnum (mp_num),
  KEY mp_nameauth (mp_name,mp_author)
) Engine=MyISAM;

#INSERT INTO %dbpre%maps (mp_num,mp_name,mp_desc,mp_author) VALUES(1,'DM-Rankin','Rankin','Sjoerd De Jong');
#INSERT INTO %dbpre%maps (mp_num,mp_name,mp_desc,mp_author) VALUES(2,'CTF-BridgeOfFate','Bridge of Fate','Bastiaan (Checker) Frank');
#INSERT INTO %dbpre%maps (mp_num,mp_name,mp_desc,mp_author) VALUES(3,'BR-Colossus','Colossus','Dave Ewing');
#INSERT INTO %dbpre%maps (mp_num,mp_name,mp_desc,mp_author) VALUES(4,'AS-Convoy','Convoy','Phil Cole');
#INSERT INTO %dbpre%maps (mp_num,mp_name,mp_desc,mp_author) VALUES(5,'ONS-Torlan','Torlan','Streamline Studios/Hourences');
#INSERT INTO %dbpre%maps (mp_num,mp_name,mp_desc,mp_author) VALUES(6,'AS-FallenCity','Fallen City','Rogelio Olguin');
#INSERT INTO %dbpre%maps (mp_num,mp_name,mp_desc,mp_author) VALUES(7,'AS-Glacier','Glacier','Peter Respondek');
#INSERT INTO %dbpre%maps (mp_num,mp_name,mp_desc,mp_author) VALUES(8,'AS-Junkyard','Junkyard Escape','Phil Cole');
#INSERT INTO %dbpre%maps (mp_num,mp_name,mp_desc,mp_author) VALUES(9,'AS-MotherShip','MotherShip Assault','Rogelio Olguin');
#INSERT INTO %dbpre%maps (mp_num,mp_name,mp_desc,mp_author) VALUES(10,'AS-RobotFactory','Robot Factory','Chris Blundel');
