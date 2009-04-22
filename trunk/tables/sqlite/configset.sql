CREATE TABLE %dbpre%configset (
  cnfs_num smallint(5) NOT NULL default 0,
  title_msg text NOT NULL default '',
  title_msgDesc varchar(150) NOT NULL
);

CREATE INDEX cnfs_num ON %dbpre%configset (cnfs_num);

INSERT INTO %dbpre%configset VALUES(0,'Welcome to UTStatsDB.<br /><br />This site is running the Unreal Tournament local stats database program.<br />For more information on UTStatsDB visit the homepage at <a href="http://www.utstatsdb.com">http://www.utstatsdb.com</a>.','Main page text.');
