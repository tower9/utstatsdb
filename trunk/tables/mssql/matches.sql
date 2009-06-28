CREATE TABLE %dbpre%matches (
  gm_num int NOT NULL identity(1, 1),
  gm_server smallint NOT NULL default 0,
  gm_serverversion varchar(10) NOT NULL default '',
  gm_map int NOT NULL default 0,
  gm_type tinyint NOT NULL default 0,
  gm_uttype tinyint NOT NULL default 0,
  gm_init datetime NOT NULL default '1900-01-01 00:00:00',
  gm_start datetime NOT NULL default '1900-01-01 00:00:00',
  gm_logger tinyint NOT NULL default 0,
  gm_logname varchar(45) NOT NULL default '',
  gm_timeoffset float NOT NULL default 100,
  gm_rpg tinyint NOT NULL default 0,
  gm_maxwave smallint NOT NULL default 0,
  gm_difficulty smallint NOT NULL default 0,
  gm_mutators varchar(255) default NULL,
  gm_mapvoting tinyint NOT NULL default 0,
  gm_kickvoting tinyint NOT NULL default 0,
  gm_fraglimit smallint NOT NULL default 0,
  gm_timelimit smallint NOT NULL default 0,
  gm_overtime smallint NOT NULL default 0,
  gm_minplayers tinyint NOT NULL default 0,
  gm_translocator tinyint NOT NULL default 0,
  gm_endtimedelay float NOT NULL default 0,
  gm_balanceteams smallint NOT NULL default 0,
  gm_playersbalanceteams smallint NOT NULL default 0,
  gm_friendlyfirescale varchar(10) NOT NULL default '',
  gm_linksetup varchar(26) NOT NULL default '',
  gm_gamespeed float NOT NULL default 0,
  gm_healthforkills tinyint NOT NULL default 0,
  gm_allowsuperweapons tinyint NOT NULL default 1,
  gm_camperalarm tinyint NOT NULL default 0,
  gm_allowpickups tinyint NOT NULL default 1,
  gm_allowadrenaline tinyint NOT NULL default 1,
  gm_fullammo tinyint NOT NULL default 0,
  gm_starttime bigint NOT NULL default 0,
  gm_length bigint NOT NULL default 0,
  gm_numplayers smallint NOT NULL default 0,
  gm_kills smallint NOT NULL default 0,
  gm_deaths smallint NOT NULL default 0,
  gm_suicides smallint NOT NULL default 0,
  gm_numteams tinyint NOT NULL default 0,
  gm_tscore0 smallint NOT NULL default 0,
  gm_tscore1 smallint NOT NULL default 0,
  gm_tscore2 smallint NOT NULL default 0,
  gm_tscore3 smallint NOT NULL default 0,
  gm_firstblood smallint NOT NULL default -1,
  gm_headshots smallint NOT NULL default 0,
  gm_status tinyint NOT NULL default 0,
  CONSTRAINT gm_gnum primary key (gm_num)
);

CREATE INDEX gm_gstart ON %dbpre%matches (gm_start);
CREATE INDEX gm_svnum ON %dbpre%matches (gm_server, gm_num);
CREATE INDEX gm_svmap ON %dbpre%matches (gm_server, gm_map);
CREATE INDEX gm_mapnum ON %dbpre%matches (gm_map, gm_num);