CREATE TABLE %dbpre%aliases (
  al_pnum mediumint(8) NOT NULL default 0,
  al_key varchar(32) NOT NULL default ''
);

CREATE INDEX al_numkey ON %dbpre%aliases (al_pnum,al_key);
