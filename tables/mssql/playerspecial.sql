CREATE TABLE %dbpre%playerspecial
(
  ps_pnum int NOT NULL,
  ps_stype smallint NOT NULL,
  ps_total int NOT NULL default 0
);

CREATE INDEX ps_ptype ON %dbpre%playerspecial (ps_pnum,ps_stype);
