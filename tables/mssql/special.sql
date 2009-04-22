CREATE TABLE %dbpre%special (
  se_num smallint NOT NULL identity(1, 1),
  se_title varchar(30) NOT NULL default '',
  se_desc varchar(175) NOT NULL default '',
  se_total int NOT NULL default 0,
  CONSTRAINT se_num primary key (se_num)
);

CREATE INDEX se_title ON %dbpre%special (se_title);

INSERT INTO %dbpre%special (se_title,se_desc) VALUES('Jackhammer','15 kills with the Impact Hammer');
INSERT INTO %dbpre%special (se_title,se_desc) VALUES('Gunslinger','15 kills with the Enforcer');
INSERT INTO %dbpre%special (se_title,se_desc) VALUES('Bio Hazard','15 kills with the Bio-Rifle');
INSERT INTO %dbpre%special (se_title,se_desc) VALUES('Combo King','15 kills with the Shock Rifle\'s combo');
INSERT INTO %dbpre%special (se_title,se_desc) VALUES('Shaftmaster','15 kills with the Link Gun\'s alt fire');
INSERT INTO %dbpre%special (se_title,se_desc) VALUES('Blue Streak','15 kills with the Stinger Minigun');
INSERT INTO %dbpre%special (se_title,se_desc) VALUES('Flak Master','15 kills with the Flak Cannon');
INSERT INTO %dbpre%special (se_title,se_desc) VALUES('Rocket Scientist','15 kills with the Rocket Launcher');
INSERT INTO %dbpre%special (se_title,se_desc) VALUES('Headhunter','15 Headshots');
INSERT INTO %dbpre%special (se_title,se_desc) VALUES('Big Game Hunter','15 kills with the Longbow AVRiL');
INSERT INTO %dbpre%special (se_title,se_desc) VALUES('Eagle Eye','Destroying a flying vehicle (Raptor, Cicada, Fury), a speeding Scorpion, or a Viper ready to self-destruct with the Goliath or Paladin.');
INSERT INTO %dbpre%special (se_title,se_desc) VALUES('Bullseye','Killing an enemy with the kamikaze feature of the Scorpion or Viper.');
INSERT INTO %dbpre%special (se_title,se_desc) VALUES('Top Gun','Destroying a flying vehicle using a Raptor\'s missiles.');
INSERT INTO %dbpre%special (se_title,se_desc) VALUES('Pancake','Using a vehicle to crush an enemy player.');
INSERT INTO %dbpre%special (se_title,se_desc) VALUES('Road Rampage','Running over 15 people with a vehicle.');
INSERT INTO %dbpre%special (se_title,se_desc) VALUES('Hijacked','Stealing an abandoned enemy vehicle.');
INSERT INTO %dbpre%special (se_title,se_desc) VALUES('Juggernaut','Having two powerups at the same time: Berserk, Double Damage, and Invulnerability, or when you become a Titan or a Behemoth.');
INSERT INTO %dbpre%special (se_title,se_desc) VALUES('Hat Trick','3 successful flag captures in a match. They do not need to be consecutive.');
INSERT INTO %dbpre%special (se_title,se_desc) VALUES('Denied','Destroying an enemy redeemer in flight, killing an enemy orb runner within close range of a powernode, or killing an enemy flag carrier within close range of their flag.');
INSERT INTO %dbpre%special (se_title,se_desc) VALUES('Assassin','Betray one of your teammates in Betrayal or kill a Titan.');
INSERT INTO %dbpre%special (se_title,se_desc) VALUES('Payback','Kill a rogue who betrayed your team in Betrayal.');
INSERT INTO %dbpre%special (se_title,se_desc) VALUES('Rejected','Kill an enemy skull carrier just before he captures skulls in Greed.');
