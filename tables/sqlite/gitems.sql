CREATE TABLE %dbpre%gitems (
  gi_match int(8) NOT NULL default 0,
  gi_item smallint(5) NOT NULL default 0,
  gi_plr smallint(6) NOT NULL default 0,
  gi_pickups smallint(5) NOT NULL default 0
);

CREATE INDEX gi_gnumit ON %dbpre%gitems (gi_match,gi_item);
