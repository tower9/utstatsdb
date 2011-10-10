CREATE TABLE %dbpre%special (
  se_num smallint(5) unsigned NOT NULL auto_increment,
  se_title varchar(30) NOT NULL default '',
  se_desc varchar(175) NOT NULL default '',
  se_trigtype tinyint(3) unsigned NOT NULL default 0,
  se_trignum tinyint(3) unsigned NOT NULL default 0,
  se_total mediumint(8) unsigned NOT NULL default 0,
  UNIQUE KEY se_num (se_num),
  KEY se_title (se_title)
) Engine=MyISAM;

INSERT INTO %dbpre%special (se_title,se_desc,se_trignum) VALUES('Jackhammer','15 kills with the Impact Hammer',15);
INSERT INTO %dbpre%special (se_title,se_desc,se_trignum) VALUES('Gunslinger','15 kills with the Enforcer',15);
INSERT INTO %dbpre%special (se_title,se_desc,se_trignum) VALUES('Bio Hazard','15 kills with the Bio-Rifle',15);
INSERT INTO %dbpre%special (se_title,se_desc,se_trignum) VALUES('Combo King','15 kills with the Shock Rifle\'s combo',15);
INSERT INTO %dbpre%special (se_title,se_desc,se_trignum) VALUES('Shaftmaster','15 kills with the Link Gun\'s alt fire',15);
INSERT INTO %dbpre%special (se_title,se_desc,se_trignum) VALUES('Blue Streak','15 kills with the Stinger Minigun',15);
INSERT INTO %dbpre%special (se_title,se_desc,se_trignum) VALUES('Flak Master','15 kills with the Flak Cannon',15);
INSERT INTO %dbpre%special (se_title,se_desc,se_trignum) VALUES('Rocket Scientist','15 kills with the Rocket Launcher',15);
INSERT INTO %dbpre%special (se_title,se_desc,se_trignum) VALUES('Headhunter','15 Headshots',15);
INSERT INTO %dbpre%special (se_title,se_desc,se_trignum) VALUES('Big Game Hunter','15 kills with the Longbow AVRiL',15);
INSERT INTO %dbpre%special (se_title,se_desc,se_trigtype,se_trignum) VALUES('Road Kill','Running someone over with a vehicle.',2,1);
INSERT INTO %dbpre%special (se_title,se_desc,se_trigtype,se_trignum) VALUES('Road Rampage','Running over 15 people with a vehicle.',3,15);
INSERT INTO %dbpre%special (se_title,se_desc,se_trigtype,se_trignum) VALUES('TransGib','Gibbing someone with a translocator.',4,1);
INSERT INTO %dbpre%special (se_title,se_desc,se_trigtype,se_trignum) VALUES('Headshot','Delivering a killing shot to the head.',5,1);
INSERT INTO %dbpre%special (se_title,se_desc,se_trignum) VALUES('Eagle Eye','Destroying a flying vehicle (Raptor, Cicada, Fury), a speeding Scorpion, or a Viper ready to self-destruct with the Goliath or Paladin.',1);
INSERT INTO %dbpre%special (se_title,se_desc,se_trignum) VALUES('Bullseye','Killing an enemy with the kamikaze feature of the Scorpion or Viper.',1);
INSERT INTO %dbpre%special (se_title,se_desc,se_trignum) VALUES('Top Gun','Destroying a flying vehicle using a Raptor\'s missiles.',1);
INSERT INTO %dbpre%special (se_title,se_desc,se_trignum) VALUES('Pancake','Using a vehicle to crush an enemy player.',1);
INSERT INTO %dbpre%special (se_title,se_desc,se_trignum) VALUES('Hijacked','Stealing an abandoned enemy vehicle.',1);
INSERT INTO %dbpre%special (se_title,se_desc,se_trignum) VALUES('Juggernaut','Having two powerups at the same time: Berserk, Double Damage, and Invulnerability, or when you become a Titan or a Behemoth.',1);
INSERT INTO %dbpre%special (se_title,se_desc,se_trignum) VALUES('Hat Trick','3 successful flag captures in a match. They do not need to be consecutive.',3);
INSERT INTO %dbpre%special (se_title,se_desc,se_trignum) VALUES('Denied','Destroying an enemy redeemer in flight, killing an enemy orb runner within close range of a powernode, or killing an enemy flag carrier within close range of their flag.',1);
INSERT INTO %dbpre%special (se_title,se_desc,se_trignum) VALUES('Assassin','Betray one of your teammates in Betrayal or kill a Titan.',1);
INSERT INTO %dbpre%special (se_title,se_desc,se_trignum) VALUES('Payback','Kill a rogue who betrayed your team in Betrayal.',1);
INSERT INTO %dbpre%special (se_title,se_desc,se_trignum) VALUES('Rejected','Kill an enemy skull carrier just before he captures skulls in Greed.',1);
