CREATE TABLE %dbpre%config (
  num INTEGER PRIMARY KEY,
  conf varchar(20) NOT NULL default '',
  type varchar(120) NOT NULL default '',
  value varchar(60) NOT NULL default '',
  name varchar(30) NOT NULL default '',
  descr varchar(100) NOT NULL default ''
);

INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('Version','h0','3.03','Version','UTStatsDB version.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('AdminPass','p30','admin','Admin Pass','Administrative password.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('UpdatePass','p30','pass','Update Pass','Password used for running log parser.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('title','s50','UTStatsDB Server Stats','Title Bar','Title displayed on browser window.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('php_timelimit','i8','90','PHP Time Limit','Time limit for PHP to time out while processing logs.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('maxmatches','i8','0','Max. Matches','Maximum matches to keep (0 = keep all).');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('lockname','s30','UTStatsDB','Lock Name','Set to unique name if running multiple stats systems on one server. Blank to disable.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('rpgini','s200','','UTRPG Ini','UTRPG .ini file location. Only one per stats server.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('demodir','s200','','Demorec Path','Path to locate or store demorecs into.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('demoext','s10','','Demorec Extension','Extension of demorec files.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('playerspage','i5','30','Players Per Page','Number of players to list per page.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('serverspage','i5','30','Servers Per Page','Number of servers to list per page.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('mapspage','i5','30','Maps Per Page','Number of maps to list per page.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('matchespage','i5','35','Matches Per Page','Number of matches to list per page.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('navbar','b2|Side|Top','0','Navigation Bar','Use standard side or top navigation bar.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('layout','i2','1','Layout','Layout to use: 1=Default ngStats, 2=Smaller fonts, 3=Dark colors/small fonts, 4=Small/Dark/No bold');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('dateformat','b3|US-12hr|US-24hr|Europe','0','Date Format','Date Format');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('minchmatches','i5','5','Min. CH Matches','Minimum matches for player to appear on career highs.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('minchtime','i8','10','Min. CH Time','Minimum time in minutes for player to appear on career highs.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('minrankmatches','i5','0','Min. Rank Matches','Minimum matches for player to be ranked.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('minranktime','i8','0','Min. Rank Time','Minimum time in minutes for player to be ranked.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('useshortname','b2|Disabled|Enabled','0','Use Short Name','Use short player names from logs (special logging required).');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('ranksystem','b2|Disabled|Enabled','1','Rank System','Enable ranking system.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('rankbots','b2|Disabled|Enabled','0','Rank Bots','Include bots in ranking system.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('savesingle','b3|Two Humans|Two Humans/Bots|One','2','Minimum Players','Save matches with single players.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('allowincomplete','b4|None|Map Change|Unknown/Map Change|Server Quit/Unknown/Map Change','1','Allow Incomplete','Allow incomplete matches.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('discardscoreless','b2|Disabled|Enabled','1','Discard Scoreless','Discard matches without positive scores.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('skipinsession','b2|Disabled|Enabled','0','Skip In-Session','Enable if matches in session do not use temporary extensions.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('ignorelogtype','b2|Disabled|Enabled','1','Ignore Log Type','Enable to remove &#39;Log&#39; from beginning of game types.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('usestatsname','b2|Disabled|Enabled','1','Use Stats Name','Enable to track users by global stats name and password instead of player name.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('playersearch','b2|Disabled|Enabled','1','Player Search','Enable player search function in player list.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('mapsearch','b3|Disabled|Enabled|Multiple Pages Only','1','Map Search','Enable map search function in map list.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('showbots','b2|Disabled|Enabled','1','Show Bots','Enable to display bots in player list.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('plistall','b2|Disabled|Enabled','0','Player List All','Enable to include bots in player list by default.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('fullvehiclestats','b2|Disabled|Enabled','1','Full Vehicle Stats','Enable if using OLStats to display full vehicle/turret stats.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('allowswitches','b2|Disabled|Enabled','1','Allow Switches','Enable to allow debug switches log parser.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('serverlist','b2|Disabled|Enabled','1','Server List','Server list option shown on main menu.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('invasiontotals','b2|Disabled|Enabled','0','Invasion Totals','Enable to include Invasion match info in totals and player totals.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('bothighs','b2|Disabled|Enabled','0','Bot Highs','Enable to include bots in totals, match highs, and career highs.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('criticalfix','b2|Disabled|Enabled','1','Critical Fix','Fix for UT2004 bug with critical frags not granting enough points.');
INSERT INTO %dbpre%config (conf,type,value,name,descr) VALUES('ut99weapons','b2|Disabled|Enabled','0','UT99 Weap Prefix','Add prefix to UT99 weapons to separate from non-UT99 weapons.');
