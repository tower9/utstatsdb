CREATE TABLE %dbpre%configlogs (
  num smallint NOT NULL identity(1, 1),
  logpath varchar(200) NOT NULL default '',
  backuppath varchar(200) NOT NULL default '',
  prefix varchar(60) NOT NULL default '',
  chatprefix varchar(60) NOT NULL default '',
  chatrequire tinyint NOT NULL default 0,
  noport tinyint NOT NULL default 0,
  ftpserver varchar(100) NOT NULL default '',
  ftppath varchar(200) NOT NULL default '',
  passive tinyint NOT NULL default 1,
  alllogs tinyint NOT NULL default 1,
  ftpuser varchar(30) NOT NULL default '',
  ftppass varchar(30) NOT NULL default '',
  deftype tinyint NOT NULL default 0,
  defteam tinyint NOT NULL default 0,
  demoftppath varchar(200) NOT NULL default '',
  multicheck tinyint NOT NULL default 0,
  CONSTRAINT numcl primary key (num)
);

INSERT INTO %dbpre%configlogs (logpath,prefix) VALUES('./Logs/','Stats_');
#INSERT INTO %dbpre%configlogs (logpath,prefix) VALUES('./Logs/','Unreal.ngLog.');
