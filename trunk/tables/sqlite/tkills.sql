CREATE TABLE %dbpre%tkills (
  tk_match int(10) NOT NULL default 0,
  tk_team tinyint(3) NOT NULL default 0,
  tk_score smallint(6) NOT NULL default 0,
  tk_time int(10) NOT NULL default 0
);

CREATE INDEX tk_gnumteam ON %dbpre%tkills (tk_match,tk_team,tk_time);
