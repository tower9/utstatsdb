CREATE TABLE %dbpre%gwaccuracy
(
   gwa_num int NOT NULL identity(1, 1),
   gwa_match int NOT NULL default 0,
   gwa_player int NOT NULL default 0,
   gwa_weapon smallint NOT NULL default 0,
   gwa_fired int NOT NULL default 0,
   gwa_hits int NOT NULL default 0,
   gwa_damage int NOT NULL default 0,
   CONSTRAINT gwa_num primary key (gwa_num)
);

CREATE INDEX gwa_mplrwp ON %dbpre%gwaccuracy (gwa_match, gwa_player, gwa_weapon);
