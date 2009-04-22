CREATE TABLE %dbpre%gscores
(
   gs_match int NOT NULL default 0,
   gs_player smallint NOT NULL default 0,
   gs_time int NOT NULL default 0,
   gs_score float NOT NULL default 0,
   gs_team tinyint NOT NULL default 0
);

CREATE INDEX gs_gnumtime ON %dbpre%gscores (gs_match, gs_time);
