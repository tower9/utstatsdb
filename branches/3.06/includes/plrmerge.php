<?php

/*
    UTStatsDB
    Copyright (C) 2002-2005  Patrick Contreras / Paul Gallier

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

function player_merge($src, $dst)
{
  global $dbpre;

  $link = sql_connect();

  // Lookup source player
  $result = sql_queryn($link, "SELECT * FROM {$dbpre}players WHERE pnum=$src LIMIT 1");
  if (!$result) {
    echo "Player database error!<br />\n";
    sql_close($link);
    return 0;
  }
  $row = sql_fetch_assoc($result);
  sql_free_result($result);
  if (!$row) {
    echo "Invalid source player ID #{$src}.<br />\n";
    sql_close($link);
    return 0;
  }
  while (list($key,$val) = each($row))
    ${"src_".$key} = $val;

  // Lookup destination player
  $result = sql_queryn($link, "SELECT * FROM {$dbpre}players WHERE pnum=$dst LIMIT 1");
  if (!$result) {
    echo "Player database error!<br />\n";
    sql_close($link);
    return 0;
  }
  $row2 = sql_fetch_assoc($result);
  sql_free_result($result);
  if (!$row2) {
    echo "Invalid destination player ID #{$dst}.<br />\n";
    sql_close($link);
    return 0;
  }
  while (list($key,$val) = each($row2))
    ${"dst_".$key} = $val;

  echo <<<EOF
<b>Merging players:</b><br />
<br />
<b>Source:</b> [$src] $src_plr_name<br />
<b>Destination:</b> [$dst] $dst_plr_name<br />
<br />

EOF;

  echo "Player stats....";
  $query = "UPDATE {$dbpre}players SET ";
  reset($row);
  while (list($key,$val) = each($row)) {
    if (is_numeric($val) && $val && $key != "pnum" && $key != "plr_name" && $key != "plr_bot" && $key != "plr_user" && $key != "plr_id" && $key != "plr_key" && $key != "plr_ip" && $key != "netspeed" && $key != "plr_fph" && $key != "plr_sph" && $key != "plr_eff")
      $query.="$key=$key+$val,";
  }
  if (strlen($query) > 23) {
    $query = substr($query, 0, -1);
    $query.=" WHERE pnum=$dst";
    $result = sql_queryn($link, $query);
  }
  $result = sql_queryn($link, "DELETE FROM {$dbpre}players WHERE pnum=$src LIMIT 1");
  echo "done.<br />";

  echo "Player type stats....";
  $result = sql_querynb($link, "SELECT gt_num,gt_type,gt_score,gt_frags,gt_kills,gt_deaths,gt_suicides,gt_teamkills,gt_teamdeaths,gt_wins,gt_losses,gt_matches,gt_time,gt_rank,gt_capcarry,gt_tossed,gt_drop,gt_pickup,gt_return,gt_taken,gt_typekill,gt_assist,gt_holdtime,gt_extraa,gt_extrab,gt_extrac FROM {$dbpre}playersgt WHERE gt_pnum=$src");
  if (!$result) {
    echo "Player type database error!<br />\n";
    sql_close($link);
    return 0;
  }
  while ($row3 = sql_fetch_row($result)) {
  	$gt_num = $row3[0];
    $gt_type = $row3[1];
    $gt_score = $row3[2];
    $gt_frags = $row3[3];
    $gt_kills = $row3[4];
    $gt_deaths = $row3[5];
    $gt_suicides = $row3[6];
    $gt_teamkills = $row3[7];
    $gt_teamdeaths = $row3[8];
    $gt_wins = $row3[9];
    $gt_losses = $row3[10];
    $gt_matches = $row3[11];
    $gt_time = $row3[12];
    $gt_rank = $row3[13];
    $gt_capcarry = $row3[14];
    $gt_tossed = $row3[15];
    $gt_drop = $row3[16];
    $gt_pickup = $row3[17];
    $gt_return = $row3[18];
    $gt_taken = $row3[19];
    $gt_typekill = $row3[20];
    $gt_assist = $row3[21];
    $gt_holdtime = $row3[22];
    $gt_extraa = $row3[23];
    $gt_extrab = $row3[24];
    $gt_extrac = $row3[25];
    $result2 = sql_queryn($link, "SELECT gt_num FROM {$dbpre}playersgt WHERE gt_pnum=$dst AND gt_type=$gt_type LIMIT 1");
    $row3 = sql_fetch_row($result2);
    sql_free_result($result2);
    if ($row3) {
      $num2 = $row3[0];
      $result2 = sql_queryn($link, "UPDATE {$dbpre}playersgt SET gt_score=gt_score+$gt_score,gt_frags=gt_frags+$gt_frags,gt_kills=gt_kills+$gt_kills,gt_deaths=gt_deaths+$gt_deaths,gt_suicides=gt_suicides+$gt_suicides,gt_teamkills=gt_teamkills+$gt_teamkills,gt_teamdeaths=gt_teamdeaths+$gt_teamdeaths,gt_wins=gt_wins+$gt_wins,gt_losses=gt_losses+$gt_losses,gt_matches=gt_matches+$gt_matches,gt_time=gt_time+$gt_time,gt_rank=(gt_rank+$gt_rank)/2.0,gt_capcarry=gt_capcarry+$gt_capcarry,gt_tossed=gt_tossed+$gt_tossed,gt_drop=gt_drop+$gt_drop,gt_pickup=gt_pickup+$gt_pickup,gt_return=gt_return+$gt_return,gt_taken=gt_taken+$gt_taken,gt_typekill=gt_typekill+$gt_typekill,gt_assist=gt_assist+$gt_assist,gt_holdtime=gt_holdtime+$gt_holdtime,gt_extraa=gt_extraa+$gt_extraa,gt_extrab=gt_extrab+$gt_extrab,gt_extrac=gt_extrac+$gt_extrac WHERE gt_num=$num2");
      $result2 = sql_queryn($link, "SELECT gt_score,gt_kills,gt_deaths,gt_suicides,gt_time FROM {$dbpre}playersgt WHERE gt_num=$num2 LIMIT 1");
      $row3 = sql_fetch_row($result2);
      sql_free_result($result2);
      $gt_score = $row3[0];
      $gt_kills = $row3[1];
      $gt_deaths = $row3[2];
      $gt_suicides = $row3[3];
      $gt_time = $row3[4];
      if ($gt_time <= 0)
        $gt_sph = 0.0;
      else
        $gt_sph = $gt_score / $gt_time;
      if (($gt_kills + $gt_deaths + $gt_suicides) <= 0)
        $gt_eff = 0.0;
      else
        $gt_eff = $gt_kills / ($gt_kills + $gt_deaths + $gt_suicides);
      $result2 = sql_queryn($link, "UPDATE {$dbpre}playersgt SET gt_sph=$gt_sph,gt_eff=$gt_eff WHERE gt_num=$num2");
      $result2 = sql_queryn($link, "DELETE FROM {$dbpre}playersgt WHERE gt_num=$gt_num");
    }
    else
      $result2 = sql_queryn($link, "UPDATE {$dbpre}playersgt SET gt_pnum=$dst WHERE gt_num=$gt_num");
  }
  sql_free_result($result);
  echo "done.<br />";

  echo "Weapon stats....";
  $result = sql_querynb($link, "SELECT pwk_num,pwk_weapon,pwk_frags,pwk_kills,pwk_deaths,pwk_held,pwk_suicides,pwk_fired,pwk_hits,pwk_damage FROM {$dbpre}pwkills WHERE pwk_player=$src");
  if (!$result) {
    echo "Player-Weapon database error!<br />\n";
    sql_close($link);
    return 0;
  }
  while ($row3 = sql_fetch_row($result)) {
    $num = $row3[0];
    $weap = $row3[1];
    $pwk_frags = $row3[2];
    $pwk_kills = $row3[3];
    $pwk_deaths = $row3[4];
    $pwk_held = $row3[5];
    $pwk_suicides = $row3[6];
    $pwk_fired = $row3[7];
    $pwk_hits = $row3[8];
    $pwk_damage = $row3[9];
    $result2 = sql_queryn($link, "SELECT pwk_num FROM {$dbpre}pwkills WHERE pwk_player=$dst AND pwk_weapon=$weap LIMIT 1");
    $row3 = sql_fetch_row($result2);
    sql_free_result($result2);
    if ($row3) {
      $num2 = $row3[0];
      $result2 = sql_queryn($link, "UPDATE {$dbpre}pwkills SET pwk_frags=pwk_frags+$pwk_frags,pwk_kills=pwk_kills+$pwk_kills,pwk_deaths=pwk_deaths+$pwk_deaths,pwk_held=pwk_held+$pwk_held,pwk_suicides=pwk_suicides+$pwk_suicides,pwk_fired=pwk_fired+$pwk_fired,pwk_hits=pwk_hits+$pwk_hits,pwk_damage=pwk_damage+$pwk_damage WHERE pwk_num=$num2");
      $result2 = sql_queryn($link, "DELETE FROM {$dbpre}pwkills WHERE pwk_num=$num");
    }
    else
      $result2 = sql_queryn($link, "UPDATE {$dbpre}pwkills SET pwk_player=$dst WHERE pwk_num=$num");
  }
  sql_free_result($result);
  echo "done.<br />";

  echo "Item pickups....";
  $result = sql_querynb($link, "SELECT pi_item,pi_pickups FROM {$dbpre}pitems WHERE pi_plr=$src");
  if (!$result) {
    echo "Player item database error!<br />\n";
    sql_close($link);
    return 0;
  }
  while ($row3 = sql_fetch_row($result)) {
    $pi_item = $row3[0];
    $pi_pickups = $row3[1];
    $result2 = sql_queryn($link, "SELECT pi_pickups FROM {$dbpre}pitems WHERE pi_plr=$dst AND pi_item=$pi_item LIMIT 1");
    $row3 = sql_fetch_row($result2);
    sql_free_result($result2);
    if ($row3) {
      $result2 = sql_queryn($link, "UPDATE {$dbpre}pitems SET pi_pickups=pi_pickups+$pi_pickups WHERE pi_plr=$dst AND pi_item=$pi_item");
      $result2 = sql_queryn($link, "DELETE FROM {$dbpre}pitems WHERE pi_plr=$src AND pi_item=$pi_item");
    }
    else
      $result2 = sql_queryn($link, "UPDATE {$dbpre}pitems SET pi_plr=$dst WHERE pi_plr=$src AND pi_item=$pi_item");
  }
  sql_free_result($result);
  echo "done.<br />";

  echo "Match stats....";
  $result = sql_queryn($link, "UPDATE {$dbpre}gplayers SET gp_pnum=$dst WHERE gp_pnum=$src");
  echo "done.<br />";

  echo "Career highs and player count....";
  $result = sql_queryn($link, "SELECT 
   tl_chfrags_plr,
   tl_chkills_plr,
   tl_chdeaths_plr,
   tl_chsuicides_plr,
   tl_chfirstblood_plr,
   tl_chheadshots_plr,
   tl_chcarjack_plr,
   tl_chroadkills_plr,
   tl_chmulti1_plr,
   tl_chmulti2_plr,
   tl_chmulti3_plr,
   tl_chmulti4_plr,
   tl_chmulti5_plr,
   tl_chmulti6_plr,
   tl_chmulti7_plr,
   tl_chspree1_plr,
   tl_chspree2_plr,
   tl_chspree3_plr,
   tl_chspree4_plr,
   tl_chspree5_plr,
   tl_chspree6_plr,
   tl_chfph_plr,
   tl_chcpcapture_plr,
   tl_chflagcapture_plr,
   tl_chflagreturn_plr,
   tl_chflagkill_plr,
   tl_chbombcarried_plr,
   tl_chbombtossed_plr,
   tl_chbombkill_plr,
   tl_chnodeconstructed_plr,
   tl_chnodedestroyed_plr,
   tl_chnodeconstdestroyed_plr,
   tl_chheadhunter_plr,
   tl_chflakmonkey_plr,
   tl_chcombowhore_plr,
   tl_chroadrampage_plr,
   tl_chwins_plr,
   tl_chteamwins_plr,
   tl_chfragssg_plr,
   tl_chkillssg_plr,
   tl_chdeathssg_plr,
   tl_chsuicidessg_plr,
   tl_chcarjacksg_plr,
   tl_chroadkillssg_plr,
   tl_chcpcapturesg_plr,
   tl_chflagcapturesg_plr,
   tl_chflagreturnsg_plr,
   tl_chflagkillsg_plr,
   tl_chbombcarriedsg_plr,
   tl_chbombtossedsg_plr,
   tl_chbombkillsg_plr,
   tl_chnodeconstructedsg_plr,
   tl_chnodeconstdestroyedsg_plr,
   tl_chnodedestroyedsg_plr
   FROM {$dbpre}totals WHERE tl_totals='Totals' LIMIT 1");
  $row3 = sql_fetch_assoc($result);
  sql_free_result($result);
  $query = "UPDATE {$dbpre}totals SET ";
  while (list($key,$val) = each($row3)) {
    if ($val == $src)
      $query.="$key=$dst,";
  }
  if (strlen($query) > 22) {
    $query.="tl_players=tl_players-1 WHERE tl_totals='Totals'";
    $result = sql_queryn($link, $query);
  }
  echo "done.<br />";

  echo "Career weapon highs....";
  $result = sql_queryn($link, "UPDATE {$dbpre}weapons SET wp_chkills_plr=$dst WHERE wp_chkills_plr=$src");
  $result = sql_queryn($link, "UPDATE {$dbpre}weapons SET wp_chdeaths_plr=$dst WHERE wp_chdeaths_plr=$src");
  $result = sql_queryn($link, "UPDATE {$dbpre}weapons SET wp_chdeathshld_plr=$dst WHERE wp_chdeathshld_plr=$src");
  $result = sql_queryn($link, "UPDATE {$dbpre}weapons SET wp_chsuicides_plr=$dst WHERE wp_chsuicides_plr=$src");
  $result = sql_queryn($link, "UPDATE {$dbpre}weapons SET wp_chkillssg_plr=$dst WHERE wp_chkillssg_plr=$src");
  $result = sql_queryn($link, "UPDATE {$dbpre}weapons SET wp_chdeathssg_plr=$dst WHERE wp_chdeathssg_plr=$src");
  $result = sql_queryn($link, "UPDATE {$dbpre}weapons SET wp_chdeathshldsg_plr=$dst WHERE wp_chdeathshldsg_plr=$src");
  $result = sql_queryn($link, "UPDATE {$dbpre}weapons SET wp_chsuicidessg_plr=$dst WHERE wp_chsuicidessg_plr=$src");
  echo "done.<br />";

  echo "Match weapon accuracy stats....";
  $result = sql_queryn($link, "UPDATE {$dbpre}gwaccuracy SET gwa_player=$dst WHERE gwa_player=$src");
  if (!$result) {
    echo "Match weapon accuracy database error!<br />\n";
    sql_close($link);
    return 0;
  }
  echo "done.<br />";

  echo "Updating player fph, sph, efficiency....";
  $result = sql_queryn($link, "SELECT plr_frags,plr_score,plr_kills,plr_deaths,plr_suicides,plr_time FROM {$dbpre}players WHERE pnum=$dst LIMIT 1");
  if (!$result) {
    echo "Player database error!<br />\n";
    sql_close($link);
    return 0;
  }
  $row = sql_fetch_row($result);
  sql_free_result($result);
  if (!$row) {
    echo "Error retrieving player data.<br />\n";
    sql_close($link);
    return 0;
  }
  $plr_frags = $row[0];
  $plr_score = $row[1];
  $plr_kills = $row[2];
  $plr_deaths = $row[3];
  $plr_suicides = $row[4];
  $plr_time = $row[5];

  if ($plr_time <= 0) {
    $plr_fph = 0.0;
    $plr_sph = 0.0;
  }
  else {
    $plr_fph = $plr_frags / $plr_time;
    $plr_sph = $plr_score / $plr_time;
  }
  if (($plr_kills + $plr_deaths + $plr_suicides) <= 0)
    $plr_eff = 0.0;
  else
    $plr_eff = $plr_kills / ($plr_kills + $plr_deaths + $plr_suicides);
  $result = sql_queryn($link, "UPDATE {$dbpre}players SET plr_fph=$plr_fph,plr_sph=$plr_sph,plr_eff=$plr_eff WHERE pnum=$dst LIMIT 1");
  echo "done.<br />";

  sql_close($link);

  echo "<br /><b>Player merge complete.</b><br />";
  return 1;
}

?>