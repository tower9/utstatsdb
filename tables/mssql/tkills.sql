CREATE TABLE %dbpre%tkills
(
  tk_match int NOT NULL default 0,
  tk_team tinyint NOT NULL default 0,
  tk_score smallint NOT NULL default 0,
  tk_time int NOT NULL default 0
);

CREATE INDEX tk_gnumteam ON %dbpre%tkills (tk_match, tk_team, tk_time);
