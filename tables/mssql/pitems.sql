CREATE TABLE %dbpre%pitems
(
   pi_plr int NOT NULL default 0,
   pi_item smallint NOT NULL default 0,
   pi_pickups int NOT NULL default 0,
   CONSTRAINT pi_plritm primary key (pi_plr, pi_item)
);
