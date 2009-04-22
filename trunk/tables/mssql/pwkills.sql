CREATE TABLE %dbpre%pwkills
(
   pwk_num int NOT NULL identity(1, 1),
   pwk_player int NOT NULL default 0,
   pwk_weapon smallint NOT NULL default 0,
   pwk_frags int NOT NULL default 0,
   pwk_kills int NOT NULL default 0,
   pwk_deaths int NOT NULL default 0,
   pwk_held int NOT NULL default 0,
   pwk_suicides int NOT NULL default 0,
   pwk_fired int NOT NULL default 0,
   pwk_hits int NOT NULL default 0,
   pwk_damage int NOT NULL default 0,
   CONSTRAINT pwk_num primary key (pwk_num)
);

CREATE INDEX pwk_plrwp ON %dbpre%pwkills (pwk_player, pwk_weapon);
