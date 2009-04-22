CREATE TABLE %dbpre%gitems
(
   gi_match int NOT NULL default 0,
   gi_item smallint NOT NULL default 0,
   gi_plr smallint NOT NULL default 0,
   gi_pickups smallint NOT NULL default 0
);

CREATE INDEX gi_gnumit ON %dbpre%gitems (gi_match, gi_item);
