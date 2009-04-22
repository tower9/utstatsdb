CREATE TABLE %dbpre%players
(
   pnum int NOT NULL identity(1, 1),
   plr_name varchar(61) NOT NULL default '',
   plr_bot tinyint NOT NULL default 0,
   plr_frags int NOT NULL default 0,
   plr_score int NOT NULL default 0,
   plr_kills int NOT NULL default 0,
   plr_deaths int NOT NULL default 0,
   plr_suicides int NOT NULL default 0,
   plr_headshots int NOT NULL default 0,
   plr_firstblood int NOT NULL default 0,
   plr_transgib int NOT NULL default 0,
   plr_headhunter int NOT NULL default 0,
   plr_flakmonkey int NOT NULL default 0,
   plr_combowhore int NOT NULL default 0,
   plr_roadrampage int NOT NULL default 0,
   plr_carjack int NOT NULL default 0,
   plr_roadkills int NOT NULL default 0,
   plr_user varchar(35) NOT NULL default '',
   plr_id varchar(32) NOT NULL default '',
   plr_key varchar(32) NOT NULL default '',
   plr_ip varchar(21) NOT NULL default '',
   plr_netspeed int NOT NULL default 0,
   plr_rpg tinyint NOT NULL default 0,
   plr_matches int NOT NULL default 0,
   plr_time bigint NOT NULL default 0,
   plr_fph float NOT NULL default 0,
   plr_sph float NOT NULL default 0,
   plr_eff float NOT NULL default 0,
   plr_wins int NOT NULL default 0,
   plr_teamwins int NOT NULL default 0,
   plr_losses int NOT NULL default 0,
   plr_multi1 int NOT NULL default 0,
   plr_multi2 int NOT NULL default 0,
   plr_multi3 int NOT NULL default 0,
   plr_multi4 int NOT NULL default 0,
   plr_multi5 int NOT NULL default 0,
   plr_multi6 int NOT NULL default 0,
   plr_multi7 int NOT NULL default 0,
   plr_spree1 int NOT NULL default 0,
   plr_spreet1 int NOT NULL default 0,
   plr_spreek1 int NOT NULL default 0,
   plr_spree2 int NOT NULL default 0,
   plr_spreet2 int NOT NULL default 0,
   plr_spreek2 int NOT NULL default 0,
   plr_spree3 int NOT NULL default 0,
   plr_spreet3 int NOT NULL default 0,
   plr_spreek3 int NOT NULL default 0,
   plr_spree4 int NOT NULL default 0,
   plr_spreet4 int NOT NULL default 0,
   plr_spreek4 int NOT NULL default 0,
   plr_spree5 int NOT NULL default 0,
   plr_spreet5 int NOT NULL default 0,
   plr_spreek5 int NOT NULL default 0,
   plr_spree6 int NOT NULL default 0,
   plr_spreet6 int NOT NULL default 0,
   plr_spreek6 int NOT NULL default 0,
   plr_combo1 int NOT NULL default 0,
   plr_combo2 int NOT NULL default 0,
   plr_combo3 int NOT NULL default 0,
   plr_combo4 int NOT NULL default 0,
   plr_flagcapture int NOT NULL default 0,
   plr_flagreturn int NOT NULL default 0,
   plr_flagkill int NOT NULL default 0,
   plr_cpcapture int NOT NULL default 0,
   plr_bombcarried int NOT NULL default 0,
   plr_bombtossed int NOT NULL default 0,
   plr_bombkill int NOT NULL default 0,
   plr_nodeconstructed int NOT NULL default 0,
   plr_nodedestroyed int NOT NULL default 0,
   plr_nodeconstdestroyed int NOT NULL default 0,
   CONSTRAINT pnum primary key (pnum)
);

CREATE INDEX plr_name ON %dbpre%players (plr_name);
CREATE INDEX plr_bot ON %dbpre%players (plr_bot, plr_name);
CREATE INDEX plr_usrid ON %dbpre%players (plr_user, plr_id);
CREATE INDEX plr_sscore ON %dbpre%players (plr_bot, plr_score, plr_frags, plr_deaths);
CREATE INDEX plr_skills ON %dbpre%players (plr_bot, plr_kills, plr_frags, plr_deaths);
CREATE INDEX plr_sdeaths ON %dbpre%players (plr_bot, plr_deaths, plr_frags);
CREATE INDEX plr_ssuicides ON %dbpre%players (plr_bot, plr_suicides, plr_frags, plr_deaths);
CREATE INDEX plr_seff ON %dbpre%players (plr_bot, plr_eff, plr_kills, plr_frags, plr_deaths);
CREATE INDEX plr_sfph ON %dbpre%players (plr_bot, plr_fph, plr_kills, plr_frags, plr_deaths);
CREATE INDEX plr_ssph ON %dbpre%players (plr_bot, plr_sph, plr_kills, plr_frags, plr_deaths);
CREATE INDEX plr_smatches ON %dbpre%players (plr_bot, plr_matches, plr_kills, plr_frags, plr_deaths);
CREATE INDEX plr_stime ON %dbpre%players (plr_bot, plr_time, plr_kills, plr_frags, plr_deaths);
CREATE INDEX plr_sfrags ON %dbpre%players (plr_bot, plr_frags, plr_deaths);
CREATE INDEX plr_swins ON %dbpre%players (plr_bot, plr_wins, plr_teamwins, plr_matches, plr_kills, plr_frags, plr_deaths);
