CREATE TABLE %dbpre%gkills
(
   gk_match int NOT NULL default 0,
   gk_killer smallint NOT NULL default 0,
   gk_victim smallint NOT NULL default 0,
   gk_time int NOT NULL default 0,
   gk_kweapon smallint NOT NULL default 0,
   gk_vweapon smallint NOT NULL default 0,
   gk_kteam smallint NOT NULL default 0,
   gk_vteam smallint NOT NULL default 0
);

CREATE INDEX gk_gnum ON %dbpre%gkills (gk_match);
