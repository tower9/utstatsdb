CREATE TABLE %dbpre%eventdesc
(
   ed_num int NOT NULL identity(1, 1),
   ed_desc varchar(32) NOT NULL default '',
   CONSTRAINT ed_num primary key (ed_num)
);

CREATE INDEX ed_desc ON %dbpre%eventdesc (ed_desc);
