CREATE TABLE %dbpre%aliases
(
   al_pnum int NOT NULL default 0,
   al_key varchar(32) NOT NULL default ''
);

CREATE INDEX %dbpre%aliasesIdx1 ON %dbpre%aliases (al_pnum, al_key);
