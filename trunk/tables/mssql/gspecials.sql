CREATE TABLE %dbpre%gspecials
(
  gs_match int NOT NULL,
  gs_player smallint NOT NULL,
  gs_stype smallint NOT NULL,
  gs_total int NOT NULL default 0
);

CREATE INDEX gs_mps ON %dbpre%gspecials (gs_match,gs_player,gs_stype);
