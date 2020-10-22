CREATE TABLE %dbpre%totals (
  tl_totals char(6) NOT NULL default 'Totals',
  tl_score int(11) NOT NULL default 0,
  tl_kills int(10) unsigned NOT NULL default 0,
  tl_deaths int(10) unsigned NOT NULL default 0,
  tl_suicides mediumint(8) unsigned NOT NULL default 0,
  tl_teamkills mediumint(8) unsigned NOT NULL default 0,
  tl_teamdeaths mediumint(8) unsigned NOT NULL default 0,
  tl_players mediumint(8) unsigned NOT NULL default 0,
  tl_matches mediumint(8) unsigned NOT NULL default 0,
  tl_time bigint(19) unsigned NOT NULL default 0,
  tl_gametime bigint(19) unsigned NOT NULL default 0,
  tl_playertime bigint(19) unsigned NOT NULL default 0,
  tl_cpcapture mediumint(8) unsigned NOT NULL default 0,
  tl_flagcapture mediumint(8) unsigned NOT NULL default 0,
  tl_flagdrop mediumint(8) unsigned NOT NULL default 0,
  tl_flagpickup mediumint(8) unsigned NOT NULL default 0,
  tl_flagreturn mediumint(8) unsigned NOT NULL default 0,
  tl_flagtaken mediumint(8) unsigned NOT NULL default 0,
  tl_flagkill mediumint(8) unsigned NOT NULL default 0,
  tl_flagassist mediumint(8) unsigned NOT NULL default 0,
  tl_bombcarried mediumint(8) unsigned NOT NULL default 0,
  tl_bombtossed mediumint(8) unsigned NOT NULL default 0,
  tl_bombdrop mediumint(8) unsigned NOT NULL default 0,
  tl_bombpickup mediumint(8) unsigned NOT NULL default 0,
  tl_bombtaken mediumint(8) unsigned NOT NULL default 0,
  tl_bombkill mediumint(8) unsigned NOT NULL default 0,
  tl_bombassist mediumint(8) unsigned NOT NULL default 0,
  tl_nodeconstructed mediumint(8) unsigned NOT NULL default 0,
  tl_nodeconstdestroyed mediumint(8) unsigned NOT NULL default 0,
  tl_nodedestroyed mediumint(8) unsigned NOT NULL default 0,
  tl_coredestroyed mediumint(8) unsigned NOT NULL default 0,
  tl_spkills mediumint(8) unsigned NOT NULL default 0,
  tl_spdeaths mediumint(8) unsigned NOT NULL default 0,
  tl_spsuicides mediumint(8) unsigned NOT NULL default 0,
  tl_spteamkills mediumint(8) unsigned NOT NULL default 0,
  tl_spteamdeaths mediumint(8) unsigned NOT NULL default 0,
  tl_spmatches mediumint(8) unsigned NOT NULL default 0,
  tl_sptime bigint(19) unsigned NOT NULL default 0,
  tl_multi1 mediumint(8) unsigned NOT NULL default 0,
  tl_multi2 mediumint(8) unsigned NOT NULL default 0,
  tl_multi3 mediumint(8) unsigned NOT NULL default 0,
  tl_multi4 mediumint(8) unsigned NOT NULL default 0,
  tl_multi5 mediumint(8) unsigned NOT NULL default 0,
  tl_multi6 mediumint(8) unsigned NOT NULL default 0,
  tl_multi7 mediumint(8) unsigned NOT NULL default 0,
  tl_spree1 mediumint(8) unsigned NOT NULL default 0,
  tl_spreet1 int(10) unsigned NOT NULL default 0,
  tl_spreek1 mediumint(8) unsigned NOT NULL default 0,
  tl_spree2 mediumint(8) unsigned NOT NULL default 0,
  tl_spreet2 int(10) unsigned NOT NULL default 0,
  tl_spreek2 mediumint(8) unsigned NOT NULL default 0,
  tl_spree3 mediumint(8) unsigned NOT NULL default 0,
  tl_spreet3 int(10) unsigned NOT NULL default 0,
  tl_spreek3 mediumint(8) unsigned NOT NULL default 0,
  tl_spree4 mediumint(8) unsigned NOT NULL default 0,
  tl_spreet4 int(10) unsigned NOT NULL default 0,
  tl_spreek4 mediumint(8) unsigned NOT NULL default 0,
  tl_spree5 mediumint(8) unsigned NOT NULL default 0,
  tl_spreet5 int(10) unsigned NOT NULL default 0,
  tl_spreek5 mediumint(8) unsigned NOT NULL default 0,
  tl_spree6 mediumint(8) unsigned NOT NULL default 0,
  tl_spreet6 int(10) unsigned NOT NULL default 0,
  tl_spreek6 mediumint(8) unsigned NOT NULL default 0,
  tl_combo1 mediumint(8) unsigned NOT NULL default 0,
  tl_combo2 mediumint(8) unsigned NOT NULL default 0,
  tl_combo3 mediumint(8) unsigned NOT NULL default 0,
  tl_combo4 mediumint(8) unsigned NOT NULL default 0,
  tl_chfrags mediumint(8) unsigned NOT NULL default 0,
  tl_chfrags_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chfrags_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chfrags_tm bigint(19) unsigned NOT NULL default 0,
  tl_chkills mediumint(8) unsigned NOT NULL default 0,
  tl_chkills_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chkills_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chkills_tm bigint(19) unsigned NOT NULL default 0,
  tl_chdeaths mediumint(8) unsigned NOT NULL default 0,
  tl_chdeaths_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chdeaths_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chdeaths_tm bigint(19) unsigned NOT NULL default 0,
  tl_chsuicides mediumint(8) unsigned NOT NULL default 0,
  tl_chsuicides_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chsuicides_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chsuicides_tm bigint(19) unsigned NOT NULL default 0,
  tl_chfirstblood mediumint(8) unsigned NOT NULL default 0,
  tl_chfirstblood_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chfirstblood_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chfirstblood_tm bigint(19) unsigned NOT NULL default 0,
  tl_chheadshots mediumint(8) unsigned NOT NULL default 0,
  tl_chheadshots_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chheadshots_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chheadshots_tm bigint(19) unsigned NOT NULL default 0,
  tl_chcarjack mediumint(8) unsigned NOT NULL default 0,
  tl_chcarjack_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chcarjack_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chcarjack_tm bigint(19) unsigned NOT NULL default 0,
  tl_chroadkills mediumint(8) unsigned NOT NULL default 0,
  tl_chroadkills_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chroadkills_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chroadkills_tm bigint(19) unsigned NOT NULL default 0,
  tl_chmulti1 mediumint(8) unsigned NOT NULL default 0,
  tl_chmulti1_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chmulti1_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chmulti1_tm bigint(19) unsigned NOT NULL default 0,
  tl_chmulti2 mediumint(8) unsigned NOT NULL default 0,
  tl_chmulti2_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chmulti2_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chmulti2_tm bigint(19) unsigned NOT NULL default 0,
  tl_chmulti3 mediumint(8) unsigned NOT NULL default 0,
  tl_chmulti3_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chmulti3_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chmulti3_tm bigint(19) unsigned NOT NULL default 0,
  tl_chmulti4 mediumint(8) unsigned NOT NULL default 0,
  tl_chmulti4_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chmulti4_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chmulti4_tm bigint(19) unsigned NOT NULL default 0,
  tl_chmulti5 mediumint(8) unsigned NOT NULL default 0,
  tl_chmulti5_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chmulti5_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chmulti5_tm bigint(19) unsigned NOT NULL default 0,
  tl_chmulti6 mediumint(8) unsigned NOT NULL default 0,
  tl_chmulti6_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chmulti6_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chmulti6_tm bigint(19) unsigned NOT NULL default 0,
  tl_chmulti7 mediumint(8) unsigned NOT NULL default 0,
  tl_chmulti7_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chmulti7_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chmulti7_tm bigint(19) unsigned NOT NULL default 0,
  tl_chspree1 mediumint(8) unsigned NOT NULL default 0,
  tl_chspree1_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chspree1_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chspree1_tm bigint(19) unsigned NOT NULL default 0,
  tl_chspree2 mediumint(8) unsigned NOT NULL default 0,
  tl_chspree2_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chspree2_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chspree2_tm bigint(19) unsigned NOT NULL default 0,
  tl_chspree3 mediumint(8) unsigned NOT NULL default 0,
  tl_chspree3_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chspree3_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chspree3_tm bigint(19) unsigned NOT NULL default 0,
  tl_chspree4 mediumint(8) unsigned NOT NULL default 0,
  tl_chspree4_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chspree4_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chspree4_tm bigint(19) unsigned NOT NULL default 0,
  tl_chspree5 mediumint(8) unsigned NOT NULL default 0,
  tl_chspree5_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chspree5_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chspree5_tm bigint(19) unsigned NOT NULL default 0,
  tl_chspree6 mediumint(8) unsigned NOT NULL default 0,
  tl_chspree6_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chspree6_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chspree6_tm bigint(19) unsigned NOT NULL default 0,
  tl_chfph float unsigned NOT NULL default 0,
  tl_chfph_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chfph_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chfph_tm bigint(19) unsigned NOT NULL default 0,
  tl_chcpcapture mediumint(8) unsigned NOT NULL default 0,
  tl_chcpcapture_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chcpcapture_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chcpcapture_tm bigint(19) unsigned NOT NULL default 0,
  tl_chflagcapture mediumint(8) unsigned NOT NULL default 0,
  tl_chflagcapture_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chflagcapture_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chflagcapture_tm bigint(19) unsigned NOT NULL default 0,
  tl_chflagreturn mediumint(8) unsigned NOT NULL default 0,
  tl_chflagreturn_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chflagreturn_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chflagreturn_tm bigint(19) unsigned NOT NULL default 0,
  tl_chflagkill mediumint(8) unsigned NOT NULL default 0,
  tl_chflagkill_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chflagkill_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chflagkill_tm bigint(19) unsigned NOT NULL default 0,
  tl_chbombcarried mediumint(8) unsigned NOT NULL default 0,
  tl_chbombcarried_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chbombcarried_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chbombcarried_tm bigint(19) unsigned NOT NULL default 0,
  tl_chbombtossed mediumint(8) unsigned NOT NULL default 0,
  tl_chbombtossed_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chbombtossed_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chbombtossed_tm bigint(19) unsigned NOT NULL default 0,
  tl_chbombkill mediumint(8) unsigned NOT NULL default 0,
  tl_chbombkill_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chbombkill_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chbombkill_tm bigint(19) unsigned NOT NULL default 0,
  tl_chnodeconstructed mediumint(8) unsigned NOT NULL default 0,
  tl_chnodeconstructed_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chnodeconstructed_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chnodeconstructed_tm bigint(19) unsigned NOT NULL default 0,
  tl_chnodedestroyed mediumint(8) unsigned NOT NULL default 0,
  tl_chnodedestroyed_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chnodedestroyed_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chnodedestroyed_tm bigint(19) unsigned NOT NULL default 0,
  tl_chnodeconstdestroyed mediumint(8) unsigned NOT NULL default 0,
  tl_chnodeconstdestroyed_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chnodeconstdestroyed_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chnodeconstdestroyed_tm bigint(19) unsigned NOT NULL default 0,
  tl_chheadhunter mediumint(8) unsigned NOT NULL default 0,
  tl_chheadhunter_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chheadhunter_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chheadhunter_tm bigint(19) unsigned NOT NULL default 0,
  tl_chroadrampage mediumint(8) unsigned NOT NULL default 0,
  tl_chroadrampage_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chroadrampage_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chroadrampage_tm bigint(19) unsigned NOT NULL default 0,
  tl_chwins mediumint(8) unsigned NOT NULL default 0,
  tl_chwins_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chwins_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chwins_tm bigint(19) unsigned NOT NULL default 0,
  tl_chteamwins mediumint(8) unsigned NOT NULL default 0,
  tl_chteamwins_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chteamwins_gms mediumint(8) unsigned NOT NULL default 0,
  tl_chteamwins_tm bigint(19) unsigned NOT NULL default 0,
  tl_chfragssg mediumint(8) unsigned NOT NULL default 0,
  tl_chfragssg_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chfragssg_tm bigint(19) unsigned NOT NULL default 0,
  tl_chfragssg_map mediumint(8) unsigned NOT NULL default 0,
  tl_chfragssg_date datetime NOT NULL default '2004-03-16 00:00:00',
  tl_chkillssg mediumint(8) unsigned NOT NULL default 0,
  tl_chkillssg_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chkillssg_tm bigint(19) unsigned NOT NULL default 0,
  tl_chkillssg_map mediumint(8) unsigned NOT NULL default 0,
  tl_chkillssg_date datetime NOT NULL default '2004-03-16 00:00:00',
  tl_chdeathssg mediumint(8) unsigned NOT NULL default 0,
  tl_chdeathssg_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chdeathssg_tm bigint(19) unsigned NOT NULL default 0,
  tl_chdeathssg_map mediumint(8) unsigned NOT NULL default 0,
  tl_chdeathssg_date datetime NOT NULL default '2004-03-16 00:00:00',
  tl_chsuicidessg mediumint(8) unsigned NOT NULL default 0,
  tl_chsuicidessg_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chsuicidessg_tm bigint(19) unsigned NOT NULL default 0,
  tl_chsuicidessg_map mediumint(8) unsigned NOT NULL default 0,
  tl_chsuicidessg_date datetime NOT NULL default '2004-03-16 00:00:00',
  tl_chcarjacksg mediumint(8) unsigned NOT NULL default 0,
  tl_chcarjacksg_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chcarjacksg_tm bigint(19) unsigned NOT NULL default 0,
  tl_chcarjacksg_map mediumint(8) unsigned NOT NULL default 0,
  tl_chcarjacksg_date datetime NOT NULL default '2004-03-16 00:00:00',
  tl_chroadkillssg mediumint(8) unsigned NOT NULL default 0,
  tl_chroadkillssg_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chroadkillssg_tm bigint(19) unsigned NOT NULL default 0,
  tl_chroadkillssg_map mediumint(8) unsigned NOT NULL default 0,
  tl_chroadkillssg_date datetime NOT NULL default '2004-03-16 00:00:00',
  tl_chcpcapturesg mediumint(8) unsigned NOT NULL default 0,
  tl_chcpcapturesg_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chcpcapturesg_tm bigint(19) unsigned NOT NULL default 0,
  tl_chcpcapturesg_map mediumint(8) unsigned NOT NULL default 0,
  tl_chcpcapturesg_date datetime NOT NULL default '2004-03-16 00:00:00',
  tl_chflagcapturesg mediumint(8) unsigned NOT NULL default 0,
  tl_chflagcapturesg_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chflagcapturesg_tm bigint(19) unsigned NOT NULL default 0,
  tl_chflagcapturesg_map mediumint(8) unsigned NOT NULL default 0,
  tl_chflagcapturesg_date datetime NOT NULL default '2004-03-16 00:00:00',
  tl_chflagreturnsg mediumint(8) unsigned NOT NULL default 0,
  tl_chflagreturnsg_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chflagreturnsg_tm bigint(19) unsigned NOT NULL default 0,
  tl_chflagreturnsg_map mediumint(8) unsigned NOT NULL default 0,
  tl_chflagreturnsg_date datetime NOT NULL default '2004-03-16 00:00:00',
  tl_chflagkillsg mediumint(8) unsigned NOT NULL default 0,
  tl_chflagkillsg_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chflagkillsg_tm bigint(19) unsigned NOT NULL default 0,
  tl_chflagkillsg_map mediumint(8) unsigned NOT NULL default 0,
  tl_chflagkillsg_date datetime NOT NULL default '2004-03-16 00:00:00',
  tl_chbombcarriedsg mediumint(8) unsigned NOT NULL default 0,
  tl_chbombcarriedsg_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chbombcarriedsg_tm bigint(19) unsigned NOT NULL default 0,
  tl_chbombcarriedsg_map mediumint(8) unsigned NOT NULL default 0,
  tl_chbombcarriedsg_date datetime NOT NULL default '2004-03-16 00:00:00',
  tl_chbombtossedsg mediumint(8) unsigned NOT NULL default 0,
  tl_chbombtossedsg_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chbombtossedsg_tm bigint(19) unsigned NOT NULL default 0,
  tl_chbombtossedsg_map mediumint(8) unsigned NOT NULL default 0,
  tl_chbombtossedsg_date datetime NOT NULL default '2004-03-16 00:00:00',
  tl_chbombkillsg mediumint(8) unsigned NOT NULL default 0,
  tl_chbombkillsg_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chbombkillsg_tm bigint(19) unsigned NOT NULL default 0,
  tl_chbombkillsg_map mediumint(8) unsigned NOT NULL default 0,
  tl_chbombkillsg_date datetime NOT NULL default '2004-03-16 00:00:00',
  tl_chnodeconstructedsg mediumint(8) unsigned NOT NULL default 0,
  tl_chnodeconstructedsg_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chnodeconstructedsg_tm bigint(19) unsigned NOT NULL default 0,
  tl_chnodeconstructedsg_map mediumint(8) unsigned NOT NULL default 0,
  tl_chnodeconstructedsg_date datetime NOT NULL default '2004-03-16 00:00:00',
  tl_chnodeconstdestroyedsg mediumint(8) unsigned NOT NULL default 0,
  tl_chnodeconstdestroyedsg_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chnodeconstdestroyedsg_tm bigint(19) unsigned NOT NULL default 0,
  tl_chnodeconstdestroyedsg_map mediumint(8) unsigned NOT NULL default 0,
  tl_chnodeconstdestroyedsg_date datetime NOT NULL default '2004-03-16 00:00:00',
  tl_chnodedestroyedsg mediumint(8) unsigned NOT NULL default 0,
  tl_chnodedestroyedsg_plr mediumint(8) unsigned NOT NULL default 0,
  tl_chnodedestroyedsg_tm bigint(19) unsigned NOT NULL default 0,
  tl_chnodedestroyedsg_map mediumint(8) unsigned NOT NULL default 0,
  tl_chnodedestroyedsg_date datetime NOT NULL default '2004-03-16 00:00:00',
  UNIQUE KEY tl_tot (tl_totals)
) Engine=MyISAM;

INSERT INTO %dbpre%totals (tl_totals) VALUES('Totals');
