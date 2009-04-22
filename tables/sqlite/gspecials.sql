CREATE TABLE %dbpre%gspecials (
  gs_match int(10) NOT NULL,
  gs_player smallint(5) NOT NULL,
  gs_stype smallint(5) NOT NULL,
  gs_total mediumint(8) NOT NULL default 0
);

CREATE INDEX gs_mps ON %dbpre%gspecials (gs_match,gs_player,gs_stype);
