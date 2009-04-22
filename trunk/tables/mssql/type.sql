CREATE TABLE %dbpre%type
(
   tp_num smallint NOT NULL identity(1, 1),
   tp_desc varchar(30) NOT NULL default '',
   tp_type tinyint NOT NULL default 0,
   tp_team tinyint NOT NULL default 0,
   tp_played int NOT NULL default 0,
   tp_gtime bigint NOT NULL default 0,
   tp_ptime bigint NOT NULL default 0,
   tp_score int NOT NULL default 0,
   tp_kills int NOT NULL default 0,
   tp_deaths int NOT NULL default 0,
   tp_suicides int NOT NULL default 0,
   tp_teamkills int NOT NULL default 0,
   CONSTRAINT tp_tnum primary key (tp_num)
);

CREATE INDEX tp_desc ON %dbpre%type (tp_desc);

INSERT INTO %dbpre%type (tp_desc,tp_type) VALUES('Deathmatch',1);
INSERT INTO %dbpre%type (tp_desc,tp_type,tp_team) VALUES('Capture the Flag',2,1);
INSERT INTO %dbpre%type (tp_desc,tp_type,tp_team) VALUES('Bombing Run',3,1);
INSERT INTO %dbpre%type (tp_desc,tp_type,tp_team) VALUES('Team Deathmatch',4,1);
INSERT INTO %dbpre%type (tp_desc,tp_type,tp_team) VALUES('Assault',5,1);
INSERT INTO %dbpre%type (tp_desc,tp_type,tp_team) VALUES('Onslaught',6,1);
INSERT INTO %dbpre%type (tp_desc,tp_type,tp_team) VALUES('Double Domination',7,1);
INSERT INTO %dbpre%type (tp_desc,tp_type) VALUES('Mutant',8);
INSERT INTO %dbpre%type (tp_desc,tp_type,tp_team) VALUES('Invasion',9,1);
INSERT INTO %dbpre%type (tp_desc,tp_type) VALUES('Last Man Standing',10);
INSERT INTO %dbpre%type (tp_desc,tp_type,tp_team) VALUES('Instagib CTF',2,1);
INSERT INTO %dbpre%type (tp_desc,tp_type,tp_team) VALUES('Vehicle CTF',2,1);
INSERT INTO %dbpre%type (tp_desc,tp_type) VALUES('Log Deathmatch',1);
INSERT INTO %dbpre%type (tp_desc,tp_type,tp_team) VALUES('Log Capture the Flag',2,1);
INSERT INTO %dbpre%type (tp_desc,tp_type,tp_team) VALUES('Log Bombing Run',3,1);
INSERT INTO %dbpre%type (tp_desc,tp_type,tp_team) VALUES('Log Team Deathmatch',4,1);
INSERT INTO %dbpre%type (tp_desc,tp_type,tp_team) VALUES('Log Assault',5,1);
INSERT INTO %dbpre%type (tp_desc,tp_type,tp_team) VALUES('Log Onslaught',6,1);
INSERT INTO %dbpre%type (tp_desc,tp_type,tp_team) VALUES('Log Double Domination',7,1);
INSERT INTO %dbpre%type (tp_desc,tp_type) VALUES('Log Mutant',8);
INSERT INTO %dbpre%type (tp_desc,tp_type,tp_team) VALUES('Log Invasion',9,1);
INSERT INTO %dbpre%type (tp_desc,tp_type) VALUES('Log Last Man Standing',10);
INSERT INTO %dbpre%type (tp_desc,tp_type,tp_team) VALUES('Log Vehicle CTF',2,1);
INSERT INTO %dbpre%type (tp_desc,tp_type) VALUES('Tournament DeathMatch',1);
INSERT INTO %dbpre%type (tp_desc,tp_type,tp_team) VALUES('Tournament Team Game',4,1);
INSERT INTO %dbpre%type (tp_desc,tp_type,tp_team) VALUES('Domination',7,1);
INSERT INTO %dbpre%type (tp_desc,tp_type,tp_team) VALUES('CTF4',2,1);
INSERT INTO %dbpre%type (tp_desc,tp_type,tp_team) VALUES('TeamLastManStanding',10,1);
INSERT INTO %dbpre%type (tp_desc,tp_type,tp_team) VALUES('InstaGib TeamLastManStanding',10,1);
INSERT INTO %dbpre%type (tp_desc,tp_type,tp_team) VALUES('Red Orchestra',4,1);
INSERT INTO %dbpre%type (tp_desc,tp_type,tp_team) VALUES('Neotokyo Engagement',7,1);
INSERT INTO %dbpre%type (tp_desc,tp_type,tp_team) VALUES('DeathBall',18,1);
INSERT INTO %dbpre%type (tp_desc,tp_type,tp_team) VALUES('Team ArenaMaster',19,1);
INSERT INTO %dbpre%type (tp_desc,tp_type,tp_team) VALUES('JailBreak',20,1);
INSERT INTO %dbpre%type (tp_desc,tp_type,tp_team) VALUES('Warfare',6,1);
INSERT INTO %dbpre%type (tp_desc,tp_type) VALUES('Duel',1);
INSERT INTO %dbpre%type (tp_desc,tp_type,tp_team) VALUES('Tactical Ops',21,1);
