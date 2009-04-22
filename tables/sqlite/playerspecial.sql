CREATE TABLE %dbpre%playerspecial (
  ps_pnum mediumint(8) NOT NULL,
  ps_stype smallint(5) NOT NULL,
  ps_total mediumint(8) NOT NULL default 0
);

CREATE INDEX ps_ptype ON %dbpre%playerspecial (ps_pnum,ps_stype);
