CREATE TABLE %dbpre%configquery
(
   num smallint NOT NULL identity(1, 1),
   server varchar(200) NOT NULL default '',
   port smallint NOT NULL default 7777,
   type tinyint NOT NULL default 0,
   password varchar(40) NOT NULL default '',
   link varchar(200) NOT NULL default '',
   spectators tinyint NOT NULL default 1,
   bots tinyint NOT NULL default 1,
   CONSTRAINT numcq primary key (num)
);
