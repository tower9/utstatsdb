CREATE TABLE %dbpre%connections (
  cn_match int(10) NOT NULL default 0,
  cn_pnum smallint(5) NOT NULL default 0,
  cn_ctime TIMESTAMP NOT NULL default 0,
  cn_dtime TIMESTAMP NOT NULL default 0
);

CREATE INDEX cn_matchplr ON %dbpre%connections (cn_match,cn_pnum);
CREATE INDEX cn_matchdt ON %dbpre%connections (cn_match,cn_dtime);
CREATE INDEX cn_times ON %dbpre%connections (cn_ctime,cn_dtime);
