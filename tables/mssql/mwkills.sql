CREATE TABLE %dbpre%mwkills
(
   mwk_num int NOT NULL identity(1, 1),
   mwk_map int NOT NULL default 0,
   mwk_weapon smallint NOT NULL default 0,
   mwk_kills int NOT NULL default 0,
   mwk_deaths int NOT NULL default 0,
   mwk_held int NOT NULL default 0,
   mwk_suicides int NOT NULL default 0,
   CONSTRAINT mwk_num primary key (mwk_num)
);

CREATE INDEX mwk_mapwp ON %dbpre%mwkills (mwk_map, mwk_weapon);
