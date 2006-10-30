CREATE TABLE %dbpre%mwkills (
  mwk_num INTEGER PRIMARY KEY,
  mwk_map mediumint(8) NOT NULL default 0,
  mwk_weapon smallint(5) NOT NULL default 0,
  mwk_kills mediumint(8) NOT NULL default 0,
  mwk_deaths mediumint(8) NOT NULL default 0,
  mwk_held mediumint(8) NOT NULL default 0,
  mwk_suicides mediumint(8) NOT NULL default 0
);

CREATE INDEX mwk_mapwp ON %dbpre%mwkills (mwk_map,mwk_weapon);
