CREATE TABLE %dbpre%pwkills (
  pwk_num INTEGER PRIMARY KEY,
  pwk_player mediumint(8) NOT NULL default 0,
  pwk_weapon smallint(5) NOT NULL default 0,
  pwk_frags mediumint(8) NOT NULL default 0,
  pwk_kills mediumint(8) NOT NULL default 0,
  pwk_deaths mediumint(8) NOT NULL default 0,
  pwk_held mediumint(8) NOT NULL default 0,
  pwk_suicides mediumint(8) NOT NULL default 0,
  pwk_fired int(10) NOT NULL default 0,
  pwk_hits int(10) NOT NULL default 0,
  pwk_damage int(10) NOT NULL default 0
);

CREATE INDEX pwk_plrwp ON %dbpre%pwkills (pwk_player,pwk_weapon);
