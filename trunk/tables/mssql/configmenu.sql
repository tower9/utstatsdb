CREATE TABLE %dbpre%configmenu
(
   num smallint NOT NULL identity(1, 1),
   url varchar(200) NOT NULL default '',
   descr varchar(30) NOT NULL default '',
   CONSTRAINT numcm primary key (num)
);
