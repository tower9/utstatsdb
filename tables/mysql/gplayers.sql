CREATE TABLE %dbpre%gplayers (
  gp_match int(10) unsigned NOT NULL default 0,
  gp_num smallint(5) unsigned NOT NULL default 0,
  gp_bot tinyint(3) unsigned NOT NULL default 0,
  gp_pnum mediumint(8) unsigned NOT NULL default 0,
  gp_ip varchar(21) NOT NULL default '',
  gp_netspeed mediumint(8) unsigned NOT NULL default 0,
  gp_ping smallint(5) unsigned NOT NULL default 0,
  gp_packetloss int(10) unsigned NOT NULL default 0,
  gp_tscore0 smallint(6) NOT NULL default 0,
  gp_tscore1 smallint(6) NOT NULL default 0,
  gp_tscore2 smallint(6) NOT NULL default 0,
  gp_tscore3 smallint(6) NOT NULL default 0,
  gp_kills0 smallint(5) unsigned NOT NULL default 0,
  gp_kills1 smallint(5) unsigned NOT NULL default 0,
  gp_kills2 smallint(5) unsigned NOT NULL default 0,
  gp_kills3 smallint(5) unsigned NOT NULL default 0,
  gp_deaths0 smallint(5) unsigned NOT NULL default 0,
  gp_deaths1 smallint(5) unsigned NOT NULL default 0,
  gp_deaths2 smallint(5) unsigned NOT NULL default 0,
  gp_deaths3 smallint(5) unsigned NOT NULL default 0,
  gp_suicides0 smallint(5) unsigned NOT NULL default 0,
  gp_suicides1 smallint(5) unsigned NOT NULL default 0,
  gp_suicides2 smallint(5) unsigned NOT NULL default 0,
  gp_suicides3 smallint(5) unsigned NOT NULL default 0,
  gp_time0 int(10) unsigned NOT NULL default 0,
  gp_time1 int(10) unsigned NOT NULL default 0,
  gp_time2 int(10) unsigned NOT NULL default 0,
  gp_time3 int(10) unsigned NOT NULL default 0,
  gp_rstart decimal(14,8) NOT NULL default 0,
  gp_rchange decimal(14,8) NOT NULL default 0,
  gp_firstblood tinyint(3) unsigned NOT NULL default 0,
  gp_teamkills0 smallint(5) unsigned NOT NULL default 0,
  gp_teamkills1 smallint(5) unsigned NOT NULL default 0,
  gp_teamkills2 smallint(5) unsigned NOT NULL default 0,
  gp_teamkills3 smallint(5) unsigned NOT NULL default 0,
  gp_teamdeaths0 smallint(5) unsigned NOT NULL default 0,
  gp_teamdeaths1 smallint(5) unsigned NOT NULL default 0,
  gp_teamdeaths2 smallint(5) unsigned NOT NULL default 0,
  gp_teamdeaths3 smallint(5) unsigned NOT NULL default 0,
  gp_capcarry0 smallint(5) unsigned NOT NULL default 0,
  gp_capcarry1 smallint(5) unsigned NOT NULL default 0,
  gp_capcarry2 smallint(5) unsigned NOT NULL default 0,
  gp_capcarry3 smallint(5) unsigned NOT NULL default 0,
  gp_tossed0 smallint(5) unsigned NOT NULL default 0,
  gp_tossed1 smallint(5) unsigned NOT NULL default 0,
  gp_tossed2 smallint(5) unsigned NOT NULL default 0,
  gp_tossed3 smallint(5) unsigned NOT NULL default 0,
  gp_drop0 smallint(5) unsigned NOT NULL default 0,
  gp_drop1 smallint(5) unsigned NOT NULL default 0,
  gp_drop2 smallint(5) unsigned NOT NULL default 0,
  gp_drop3 smallint(5) unsigned NOT NULL default 0,
  gp_pickup0 smallint(5) unsigned NOT NULL default 0,
  gp_pickup1 smallint(5) unsigned NOT NULL default 0,
  gp_pickup2 smallint(5) unsigned NOT NULL default 0,
  gp_pickup3 smallint(5) unsigned NOT NULL default 0,
  gp_return0 smallint(5) unsigned NOT NULL default 0,
  gp_return1 smallint(5) unsigned NOT NULL default 0,
  gp_return2 smallint(5) unsigned NOT NULL default 0,
  gp_return3 smallint(5) unsigned NOT NULL default 0,
  gp_taken0 smallint(5) unsigned NOT NULL default 0,
  gp_taken1 smallint(5) unsigned NOT NULL default 0,
  gp_taken2 smallint(5) unsigned NOT NULL default 0,
  gp_taken3 smallint(5) unsigned NOT NULL default 0,
  gp_typekill0 smallint(5) unsigned NOT NULL default 0,
  gp_typekill1 smallint(5) unsigned NOT NULL default 0,
  gp_typekill2 smallint(5) unsigned NOT NULL default 0,
  gp_typekill3 smallint(5) unsigned NOT NULL default 0,
  gp_assist0 smallint(5) unsigned NOT NULL default 0,
  gp_assist1 smallint(5) unsigned NOT NULL default 0,
  gp_assist2 smallint(5) unsigned NOT NULL default 0,
  gp_assist3 smallint(5) unsigned NOT NULL default 0,
  gp_holdtime0 int(10) unsigned NOT NULL default 0,
  gp_holdtime1 int(10) unsigned NOT NULL default 0,
  gp_holdtime2 int(10) unsigned NOT NULL default 0,
  gp_holdtime3 int(10) unsigned NOT NULL default 0,
  gp_extraa0 smallint(5) unsigned NOT NULL default 0,
  gp_extraa1 smallint(5) unsigned NOT NULL default 0,
  gp_extraa2 smallint(5) unsigned NOT NULL default 0,
  gp_extraa3 smallint(5) unsigned NOT NULL default 0,
  gp_extrab0 smallint(5) unsigned NOT NULL default 0,
  gp_extrab1 smallint(5) unsigned NOT NULL default 0,
  gp_extrab2 smallint(5) unsigned NOT NULL default 0,
  gp_extrab3 smallint(5) unsigned NOT NULL default 0,
  gp_extrac0 smallint(5) unsigned NOT NULL default 0,
  gp_extrac1 smallint(5) unsigned NOT NULL default 0,
  gp_extrac2 smallint(5) unsigned NOT NULL default 0,
  gp_extrac3 smallint(5) unsigned NOT NULL default 0,
  gp_multi1 smallint(5) unsigned NOT NULL default 0,
  gp_multi2 smallint(5) unsigned NOT NULL default 0,
  gp_multi3 smallint(5) unsigned NOT NULL default 0,
  gp_multi4 smallint(5) unsigned NOT NULL default 0,
  gp_multi5 smallint(5) unsigned NOT NULL default 0,
  gp_multi6 smallint(5) unsigned NOT NULL default 0,
  gp_multi7 smallint(5) unsigned NOT NULL default 0,
  gp_spree1 smallint(5) unsigned NOT NULL default 0,
  gp_spree2 smallint(5) unsigned NOT NULL default 0,
  gp_spree3 smallint(5) unsigned NOT NULL default 0,
  gp_spree4 smallint(5) unsigned NOT NULL default 0,
  gp_spree5 smallint(5) unsigned NOT NULL default 0,
  gp_spree6 smallint(5) unsigned NOT NULL default 0,
  gp_combo1 smallint(5) unsigned NOT NULL default 0,
  gp_combo2 smallint(5) unsigned NOT NULL default 0,
  gp_combo3 smallint(5) unsigned NOT NULL default 0,
  gp_combo4 smallint(5) unsigned NOT NULL default 0,
  gp_rank tinyint(3) unsigned NOT NULL default 0,
  gp_team tinyint(3) NOT NULL default 0,
  KEY gp_match (gp_match),
  KEY gp_pnum (gp_pnum),
  KEY gp_plrgame (gp_pnum,gp_match),
  KEY gp_gnumrank (gp_match,gp_rank)
) Type=MyISAM;
