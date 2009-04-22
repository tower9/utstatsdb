CREATE TABLE %dbpre%pitems (
  pi_plr mediumint(8) NOT NULL default 0,
  pi_item smallint(5) NOT NULL default 0,
  pi_pickups mediumint(8) NOT NULL default 0
);

CREATE INDEX pi_plritm ON %dbpre%pitems (pi_plr,pi_item);
