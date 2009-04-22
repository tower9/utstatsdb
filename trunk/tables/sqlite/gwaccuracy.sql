CREATE TABLE %dbpre%gwaccuracy (
  gwa_num INTEGER PRIMARY KEY,
  gwa_match mediumint(8) NOT NULL default 0,
  gwa_player mediumint(8) NOT NULL default 0,
  gwa_weapon smallint(5) NOT NULL default 0,
  gwa_fired int(10) NOT NULL default 0,
  gwa_hits int(10) NOT NULL default 0,
  gwa_damage int(10) NOT NULL default 0
);

CREATE INDEX gwa_mplrwp ON %dbpre%gwaccuracy (gwa_match,gwa_player,gwa_weapon);
