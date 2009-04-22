CREATE TABLE %dbpre%eventdesc (
  ed_num INTEGER PRIMARY KEY,
  ed_desc varchar(32) NOT NULL default ''
);

CREATE INDEX ed_desc ON %dbpre%eventdesc (ed_desc);
