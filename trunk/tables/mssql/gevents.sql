CREATE TABLE %dbpre%gevents
(
   ge_num bigint NOT NULL identity(1, 1),
   ge_match int NOT NULL default 0,
   ge_plr smallint NOT NULL default 0,
   ge_event tinyint NOT NULL default 0,
   ge_time int NOT NULL default 0,
   ge_length int NOT NULL default 0,
   ge_quant int NOT NULL default 0,
   ge_reason tinyint NOT NULL default 0,
   ge_opponent smallint NOT NULL default 0,
   ge_item smallint NOT NULL default 0,
   CONSTRAINT ge_num primary key (ge_num)
);

CREATE INDEX ge_gnumev ON %dbpre%gevents (ge_match, ge_event, ge_num);
CREATE INDEX ge_kstype ON %dbpre%gevents (ge_event, ge_match, ge_plr, ge_time);
