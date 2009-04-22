CREATE TABLE %dbpre%connections
(
   cn_match int NOT NULL default 0,
   cn_pnum smallint NOT NULL default 0,
   cn_ctime datetime NOT NULL default '1900-01-01 00:00:00',
   cn_dtime datetime NOT NULL default '1900-01-01 00:00:00'
);

CREATE INDEX cn_matchplr ON %dbpre%connections (cn_match, cn_pnum);
CREATE INDEX cn_matchdt ON %dbpre%connections (cn_match, cn_dtime);
CREATE INDEX cn_times ON %dbpre%connections (cn_ctime, cn_dtime);
