CREATE TABLE %dbpre%gbots (
  gb_match int(10) NOT NULL default 0,
  gb_bot smallint(5) NOT NULL default 0,
  gb_skill tinyint(3) NOT NULL default 0,
  gb_alertness float NOT NULL default 0,
  gb_accuracy float NOT NULL default 0,
  gb_aggressive float NOT NULL default 0,
  gb_strafing float NOT NULL default 0,
  gb_style float NOT NULL default 0,
  gb_tactics float NOT NULL default 0,
  gb_transloc float NOT NULL default 0,
  gb_reaction float NOT NULL default 0,
  gb_jumpiness float NOT NULL default 0,
  gb_favorite smallint(5) NOT NULL default 0
);

CREATE INDEX gb_mbot ON %dbpre%gbots (gb_match,gb_bot);
