CREATE TABLE %dbpre%gchat (
  gc_num INTEGER PRIMARY KEY,
  gc_match int(10) NOT NULL default 0,
  gc_plr smallint(6) NOT NULL default 0,
  gc_team tinyint(3) NOT NULL default 0,
  gc_time int(10) NOT NULL default 0,
  gc_text varchar(255) NOT NULL default ''
);

CREATE INDEX gc_matchtime ON %dbpre%gchat (gc_match,gc_time);
