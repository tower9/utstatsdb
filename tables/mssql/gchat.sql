CREATE TABLE %dbpre%gchat
(
   gc_num bigint NOT NULL identity(1, 1),
   gc_match int NOT NULL default 0,
   gc_plr smallint NOT NULL default 0,
   gc_team tinyint NOT NULL default 0,
   gc_time int NOT NULL default 0,
   gc_text varchar(255) NOT NULL default '',
   CONSTRAINT gc_cnum primary key (gc_num)
);

CREATE INDEX gc_matchtime ON %dbpre%gchat (gc_match, gc_time);
