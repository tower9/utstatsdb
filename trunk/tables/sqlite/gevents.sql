CREATE TABLE %dbpre%gevents (
  ge_num INTEGER PRIMARY KEY,
  ge_match int(10) NOT NULL default 0,
  ge_plr smallint(6) NOT NULL default 0,
  ge_event tinyint(3) NOT NULL default 0,
  ge_time int(10) NOT NULL default 0,
  ge_length int(10) NOT NULL default 0,
  ge_quant mediumint(9) NOT NULL default 0,
  ge_reason tinyint(3) NOT NULL default 0,
  ge_opponent smallint(6) NOT NULL default 0,
  ge_item smallint(5) NOT NULL default 0
);

CREATE INDEX ge_gnumev ON %dbpre%gevents (ge_match,ge_event,ge_num);
CREATE INDEX ge_kstype ON %dbpre%gevents (ge_event,ge_match,ge_plr,ge_time);
