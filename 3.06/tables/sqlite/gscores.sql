CREATE TABLE %dbpre%gscores (
  gs_match int(10) NOT NULL default 0,
  gs_player smallint(6) NOT NULL default 0,
  gs_time int(10) NOT NULL default 0,
  gs_score float NOT NULL default 0,
  gs_team tinyint(4) NOT NULL default 0
);

CREATE INDEX gs_gnumtime ON %dbpre%gscores (gs_match,gs_time);
