CREATE TABLE %dbpre%playersgt (
  gt_num int(10) unsigned NOT NULL auto_increment,
  gt_pnum mediumint(8) unsigned NOT NULL,
  gt_tnum smallint(5) unsigned NOT NULL default 0,
  gt_type tinyint(3) unsigned NOT NULL default 0,
  gt_score int(10) NOT NULL default 0,
  gt_frags mediumint(9) NOT NULL default 0,
  gt_kills mediumint(8) unsigned NOT NULL default 0,
  gt_deaths mediumint(8) unsigned NOT NULL default 0,
  gt_suicides mediumint(8) unsigned NOT NULL default 0,
  gt_teamkills mediumint(8) unsigned NOT NULL default 0,
  gt_teamdeaths mediumint(8) unsigned NOT NULL default 0,
  gt_sph float NOT NULL default 0,
  gt_eff float NOT NULL default 0,
  gt_wins mediumint(8) unsigned NOT NULL default 0,
  gt_losses mediumint(8) unsigned NOT NULL default 0,
  gt_matches mediumint(8) unsigned NOT NULL default 0,
  gt_time bigint(19) unsigned NOT NULL default 0,
  gt_rank decimal(14,8) NOT NULL default 0,
  gt_capcarry mediumint(8) unsigned NOT NULL default 0,
  gt_tossed mediumint(8) unsigned NOT NULL default 0,
  gt_drop mediumint(8) unsigned NOT NULL default 0,
  gt_pickup mediumint(8) unsigned NOT NULL default 0,
  gt_return mediumint(8) unsigned NOT NULL default 0,
  gt_taken mediumint(8) unsigned NOT NULL default 0,
  gt_typekill mediumint(8) unsigned NOT NULL default 0,
  gt_assist mediumint(8) unsigned NOT NULL default 0,
  gt_holdtime bigint(19) unsigned NOT NULL default 0,
  gt_extraa mediumint(8) unsigned NOT NULL default 0,
  gt_extrab mediumint(8) unsigned NOT NULL default 0,
  gt_extrac mediumint(8) unsigned NOT NULL default 0,
  UNIQUE KEY gt_num (gt_num),
  KEY gt_pnumt (gt_pnum,gt_type),
  KEY gt_rank (gt_rank)
) Type=MyISAM;