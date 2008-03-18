<?php

/*
    UTStatsDB
    Copyright (C) 2002-2008  Patrick Contreras / Paul Gallier

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

if (preg_match("/logsave.php/i", $_SERVER["PHP_SELF"])) {
  echo "Access denied.\n";
  die();
}

require("logranks.php");

function findpwk($player, $weapon) // Player, Weapon
{
  global $nohtml, $link, $dbpre;

  if ($nohtml)
    $break = "";
  else
    $break = "<br />";

  $result = sql_queryn($link, "SELECT pwk_num FROM {$dbpre}pwkills WHERE pwk_player=$player AND pwk_weapon=$weapon LIMIT 1");
  if (!$result) {
    echo "Error accessing pwkills table.{$break}\n";
    exit;
  }
  $row = sql_fetch_row($result);
  sql_free_result($result);
  if ($row)
    $pwk = intval($row[0]);
  else {
    $result = sql_queryn($link, "INSERT INTO {$dbpre}pwkills VALUES (NULL,$player,$weapon,0,0,0,0,0,0,0,0)");
    if (!$result) {
      echo "Error adding pwkills table entry.{$break}\n";
      exit;
    }
    $pwk = sql_insert_id($link);
  }
  return $pwk;
}

function findmwk($map, $weapon) // Map, Weapon
{
  global $nohtml, $link, $dbpre;

  if ($nohtml)
    $break = "";
  else
    $break = "<br />";

  $result = sql_queryn($link, "SELECT mwk_num FROM {$dbpre}mwkills WHERE mwk_map=$map AND mwk_weapon=$weapon LIMIT 1");
  if (!$result) {
    echo "Error accessing mwkills table.{$break}\n";
    exit;
  }
  $row = sql_fetch_row($result);
  sql_free_result($result);
  if ($row)
    $mwk = intval($row[0]);
  else {
    $result = sql_queryn($link, "INSERT INTO {$dbpre}mwkills VALUES (NULL,$map,$weapon,0,0,0,0)");
    if (!$result) {
      echo "Error adding mwkills table entry.{$break}\n";
      exit;
    }
    $mwk = sql_insert_id($link);
  }
  return $mwk;
}

function findgwa($match, $player, $weapon) // Match, Player, Weapon
{
  global $nohtml, $link, $dbpre;

  if ($nohtml)
    $break = "";
  else
    $break = "<br />";

  $result = sql_queryn($link, "SELECT gwa_num FROM {$dbpre}gwaccuracy WHERE gwa_match=$match AND gwa_player=$player AND gwa_weapon=$weapon LIMIT 1");
  if (!$result) {
    echo "Error accessing gwaccuracy table.{$break}\n";
    exit;
  }
  $row = sql_fetch_row($result);
  sql_free_result($result);
  if ($row)
    $gwa = intval($row[0]);
  else {
    $result = sql_queryn($link, "INSERT INTO {$dbpre}gwaccuracy VALUES (NULL,$match,$player,$weapon,0,0,0)");
    if (!$result) {
      echo "Error adding gwaccuracy table entry.{$break}\n";
      exit;
    }
    $gwa = sql_insert_id($link);
  }
  return $gwa;
}

function storedata()
{
  global $link, $dbtype, $dbpre, $mysqlverh, $mysqlverl, $config, $server, $player, $match;
  global $events, $pickups, $gkills, $gscores, $tkills, $chatlog;
  global $spree, $multi, $tchange, $uselimit, $relog, $pwastats;
  global $stattype, $nohtml, $flagstatus, $killmatch, $mutantstat, $logname, $matchnum;

  if ($nohtml)
    $break = "";
  else
    $break = "<br />";

  if ($match->length <= 0)
    return 0;

  $md = date("Y-m-d H:i:s", $match->matchdate); // YYYY-MM-DD HH:MM:SS
  $sd = date("Y-m-d H:i:s", $match->startdate);
  $tot_ptime = 0;
  $match->tot_score = intval(floor($match->tot_score));

  // Update Map Data
  $result = sql_queryn($link, "UPDATE {$dbpre}maps SET mp_matches=mp_matches+1,mp_score=mp_score+{$match->tot_score},mp_kills=mp_kills+{$match->tot_kills},mp_deaths=mp_deaths+{$match->tot_deaths},mp_suicides=mp_suicides+{$match->tot_suicides},mp_time=mp_time+{$match->length},mp_lastmatch='$sd' WHERE mp_num={$match->mapnum}");
  if (!$result) {
    echo "Error updating map data in database.{$break}\n";
    exit;
  }

  // Update Server Data
  $result = sql_queryn($link, "UPDATE {$dbpre}servers SET sv_matches=sv_matches+1,sv_frags=sv_frags+{$match->tot_kills}-{$match->tot_suicides},sv_score=sv_score+{$match->tot_score},sv_time=sv_time+{$match->length},sv_lastmatch='$sd' WHERE sv_num={$match->servernum} LIMIT 1");
  if (!$result) {
    echo "Error updating server data in database.{$break}\n";
    exit;
  }

  // Check player data for empty players
  reset($player);
  $match->numplayers = 0;
  $playerc = current($player);
  while ($playerc !== FALSE) {
    if (isset($playerc->name) && $playerc->name != "")
      $match->numplayers++;
    $playerc = next($player);
  }

  // Save Match Data
  if (strlen($match->mutators) > 255)
    $match->mutators = substr($match->mutators, 0, 255);
  $result = sql_queryn($link, "INSERT INTO {$dbpre}matches VALUES (NULL,{$match->servernum},'{$match->serverversion}',{$match->mapnum},{$match->gametnum},{$match->uttype},'$md','$sd',{$match->logger},'$logname',{$match->rpg},{$match->maxwave},{$match->difficulty},'{$match->mutators}',{$match->mapvoting},{$match->kickvoting},{$match->fraglimit},{$match->timelimit},{$match->overtime},{$match->minplayers},{$match->translocator},{$match->endtimedelay},{$match->balanceteams},{$match->playersbalanceteams},'{$match->friendlyfirescale}','{$match->linksetup}',{$match->gamespeed},{$match->healthforkills},{$match->allowsuperweapons},{$match->camperalarm},{$match->allowpickups},{$match->allowadrenaline},{$match->fullammo},{$match->starttime},{$match->length},{$match->numplayers},{$match->tot_kills},{$match->tot_deaths},{$match->tot_suicides},{$match->numteams},{$match->team[0]},{$match->team[1]},{$match->team[2]},{$match->team[3]},{$match->firstblood},{$match->headshots},0)");
  if (!$result) {
    echo "Error saving match data in database.{$break}\n";
    exit;
  }
  $matchnum = sql_insert_id($link);

  // Read Totals Data
  $result = sql_queryn($link, "SELECT * FROM {$dbpre}totals LIMIT 1");
  $row = sql_fetch_assoc($result);
  sql_free_result($result);
  while (list($key,$val) = each($row))
    ${$key} = $val;

  // Player Data
  reset($player);
  $playerc = current($player);
  while ($playerc !== FALSE) {
    if (isset($playerc->name) && $playerc->name != "") { // Modified to prevent empty Player 0 appearing
      $i = $playerc->plr;
      for ($pf = 0; $pf < 4; $pf++)
        $player[$i]->frags[$pf] = $player[$i]->kills[$pf] - $player[$i]->suicides[$pf];
      if ($player[$i]->hash != "")
        $player[$i]->key = $player[$i]->hash;

      // Check for existing player
      $spname = sql_addslashes($player[$i]->name);
      $spuser = sql_addslashes($player[$i]->user);
      if ($config["usestatsname"] && $player[$i]->user && $player[$i]->id)
        $result = sql_queryn($link, "SELECT * FROM {$dbpre}players WHERE plr_user='$spuser' AND plr_id='{$player[$i]->id}' LIMIT 1");
      else
        $result = sql_queryn($link, "SELECT * FROM {$dbpre}players WHERE plr_name='$spname' AND plr_user='' AND plr_id='' LIMIT 1");
      if (!$result) {
        echo "Error accessing players database.{$break}\n";
        exit;
      }
      $row = sql_fetch_assoc($result);
      sql_free_result($result);
      if (!$row) { // Create new player
        $result = sql_queryn($link, "INSERT INTO {$dbpre}players (plr_name,plr_user,plr_id,plr_key) VALUES('$spname','$spuser','{$player[$i]->id}','{$player[$i]->key}')");
        if (!$result) {
          echo "Error creating new player in database.{$break}\n";
          exit;
        }
        $plrnum = sql_insert_id($link);
        $result = sql_queryn($link, "SELECT * FROM {$dbpre}players WHERE pnum=$plrnum LIMIT 1");
        $row = sql_fetch_assoc($result);
        sql_free_result($result);
        if (!$player[$i]->is_bot())
          $tl_players++;
      }
      while (list($key,$val) = each($row))
        ${$key} = $val;

      $player[$i]->num = $pnum;
      $plr_name = sql_addslashes($plr_name);
      $player[$i]->name = sql_addslashes($player[$i]->name);
      $plr_user = sql_addslashes($plr_user);
      $plr_key = $player[$i]->key;
      $plr_ip = $player[$i]->ip;
      $plr_netspeed = $player[$i]->netspeed;
      $plr_bot = $player[$i]->bot;
      $player[$i]->tscore[0] = intval(floor($player[$i]->tscore[0]));
      $player[$i]->tscore[1] = intval(floor($player[$i]->tscore[1]));
      $player[$i]->tscore[2] = intval(floor($player[$i]->tscore[2]));
      $player[$i]->tscore[3] = intval(floor($player[$i]->tscore[3]));
      $plr_score += array_sum($player[$i]->tscore);
      if ($match->rpg && !$plr_rpg)
        $plr_rpg = 1;
      // Invasion matches do not change most overall stats
      if ($match->gametype != 9 || $config["invasiontotals"]) {
        $plr_frags += array_sum($player[$i]->frags);
        $plr_kills += array_sum($player[$i]->kills);
        $plr_deaths += array_sum($player[$i]->deaths);
        $plr_suicides += array_sum($player[$i]->suicides);
        $plr_headshots += $player[$i]->headshots;
        $plr_firstblood += $player[$i]->firstblood;
        $plr_transgib += $player[$i]->transgib;
        if ($player[$i]->headhunter)
          $plr_headhunter++;
        if ($player[$i]->flakmonkey)
          $plr_flakmonkey++;
        if ($player[$i]->combowhore)
          $plr_combowhore++;
        if ($player[$i]->roadrampage)
          $plr_roadrampage++;
        $plr_carjack += $player[$i]->carjack;
        $plr_roadkills += $player[$i]->roadkills;
        $plr_multi1 += $player[$i]->multi[0];
        $plr_multi2 += $player[$i]->multi[1];
        $plr_multi3 += $player[$i]->multi[2];
        $plr_multi4 += $player[$i]->multi[3];
        $plr_multi5 += $player[$i]->multi[4];
        $plr_multi6 += $player[$i]->multi[5];
        $plr_multi7 += $player[$i]->multi[6];
        $plr_spree1 += $player[$i]->spree[0];
        $plr_spreet1 += $player[$i]->spreet[0];
        $plr_spreek1 += $player[$i]->spreek[0];
        $plr_spree2 += $player[$i]->spree[1];
        $plr_spreet2 += $player[$i]->spreet[1];
        $plr_spreek2 += $player[$i]->spreek[1];
        $plr_spree3 += $player[$i]->spree[2];
        $plr_spreet3 += $player[$i]->spreet[2];
        $plr_spreek3 += $player[$i]->spreek[2];
        $plr_spree4 += $player[$i]->spree[3];
        $plr_spreet4 += $player[$i]->spreet[3];
        $plr_spreek4 += $player[$i]->spreek[3];
        $plr_spree5 += $player[$i]->spree[4];
        $plr_spreet5 += $player[$i]->spreet[4];
        $plr_spreek5 += $player[$i]->spreek[4];
        $plr_spree6 += $player[$i]->spree[5];
        $plr_spreet6 += $player[$i]->spreet[5];
        $plr_spreek6 += $player[$i]->spreek[5];
        $plr_combo1 += $player[$i]->combo[0];
        $plr_combo2 += $player[$i]->combo[1];
        $plr_combo3 += $player[$i]->combo[2];
        $plr_combo4 += $player[$i]->combo[3];
      }
      $plr_matches++;
      $plr_time += array_sum($player[$i]->totaltime);

      // Store player's total matches and game time
      $player[$i]->globalmatches = $plr_matches;
      $player[$i]->globaltime = $plr_time;

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

      // Load gametype specific stats for player
      $result = sql_queryn($link, "SELECT * FROM {$dbpre}playersgt WHERE gt_pnum=$pnum AND gt_tnum={$match->gametnum} LIMIT 1");
      if (!$result) {
        echo "Error loading player gametype data.{$break}\n";
        exit;
      }
      $row = sql_fetch_assoc($result);
      sql_free_result($result);
      if (!$row) {
        $result = sql_queryn($link, "INSERT INTO {$dbpre}playersgt (gt_pnum,gt_tnum,gt_type) VALUES($pnum,{$match->gametnum},{$match->gametype})");
        if (!$result) {
          echo "Error creating player gametype data.{$break}\n";
          exit;
        }
        $gt_num = sql_insert_id($link);
        $result = sql_queryn($link, "SELECT * FROM {$dbpre}playersgt WHERE gt_num=$gt_num LIMIT 1");
        $row = sql_fetch_assoc($result);
        sql_free_result($result);
      }
      while (list($key,$val) = each($row))
        ${$key} = $val;

      $gt_score += array_sum($player[$i]->tscore);
      $gt_frags += array_sum($player[$i]->frags);
      $gt_kills += array_sum($player[$i]->kills);
      $gt_deaths += array_sum($player[$i]->deaths);
      $gt_suicides += array_sum($player[$i]->suicides);
      $gt_matches++;
      $gt_time += array_sum($player[$i]->totaltime);
      $gt_teamkills += array_sum($player[$i]->teamkills);
      $gt_teamdeaths += array_sum($player[$i]->teamdeaths);
      $gt_capcarry += array_sum($player[$i]->capcarry);
      $gt_tossed += array_sum($player[$i]->tossed);
      $gt_drop += array_sum($player[$i]->dropped);
      $gt_pickup += array_sum($player[$i]->pickup);
      $gt_return += array_sum($player[$i]->return);
      $gt_taken += array_sum($player[$i]->taken);
      $gt_typekill += array_sum($player[$i]->typekill);
      $gt_assist += array_sum($player[$i]->assist);
      $gt_holdtime += array_sum($player[$i]->holdtime);
      $gt_extraa += array_sum($player[$i]->extraa);
      $gt_extrab += array_sum($player[$i]->extrab);
      $gt_extrac += array_sum($player[$i]->extrac);

      if ($match->teamgame) {
        if ($player[$i]->team >= 0 && $player[$i]->team <= 3) {
          $mx = max($match->team[0], $match->team[1], $match->team[2], $match->team[3]);
          if ($match->team[$player[$i]->team] == $mx)
          {
            $plr_teamwins++;
            $gt_wins++;
          }
          else {
            $plr_losses++;
            $gt_losses++;
          }
        }
      }
      else {
        if ($player[$i]->rank == 1)
        {
          $plr_wins++;
          $gt_wins++;
        }
        else {
          $plr_losses++;
          $gt_losses++;
        }
      }

      // Fix pickup - should not be negative.  Why 0 and -5 anyway?
      if ($match->gametype == 10 || $match->gametype == 19) { // Last Man Standing
      	if ($player[$player[$i]->plr]->lives > 0)
          $player[$i]->pickup[0] = $player[$player[$i]->plr]->lives;
        else
          $player[$i]->pickup[0] = 0;
        if (array_sum($player[$i]->deaths) + array_sum($player[$i]->suicides) < $player[$player[$i]->plr]->lives)
          $player[$i]->pickup[1] = $player[$player[$i]->plr]->lives - (array_sum($player[$i]->deaths) + array_sum($player[$i]->suicides));
        else
          $player[$i]->pickup[1] = 0;
      }

      if ($gt_time <= 0) {
        $gt_fph = 0.0;
        $gt_sph = 0.0;
      }
      else {
        $gt_fph = $gt_frags / $gt_time;
        $gt_sph = $gt_score / $gt_time;
      }
      if (($gt_kills + $gt_deaths + $gt_suicides) <= 0)
        $gt_eff = 0.0;
      else
        $gt_eff = $gt_kills / ($gt_kills + $gt_deaths + $gt_suicides);

      $player[$player[$i]->plr]->ranks = $gt_rank; // Set existing rank

      switch ($match->gametype) {
        case 2: // Capture the Flag
          $plr_flagcapture += $gt_capcarry;
          $plr_flagreturn += $gt_return;
          $plr_flagkill += $gt_typekill;
          break;
        case 3: // Bombing Run
          $plr_bombcarried += $gt_capcarry;
          $plr_bombtossed += $gt_tossed;
          $plr_bombkill += $gt_typekill;
          break;
        case 6: // Onslaught
          $plr_nodeconstructed += $gt_pickup;
          $plr_nodedestroyed += $gt_taken;
          $plr_nodeconstdestroyed += $gt_drop;
          break;
        case 7: // Double Domination
          $plr_cpcapture += $gt_capcarry;
          break;
      }

      // Calculate player average ping time
      if ($player[$i]->pingcount)
        $player[$i]->avgping = intval(round(floatval($player[$i]->ping) / floatval($player[$i]->pingcount)));
      else
        $player[$i]->avgping = 0;

      // Check for name change
      if ($plr_name != $player[$i]->name)
        $plr_name = $player[$i]->name;

      // Save player stats
      $result = sql_queryn($link, "REPLACE INTO {$dbpre}players VALUES (
        $pnum,
        '$plr_name',
        $plr_bot,
        $plr_frags,$plr_score,$plr_kills,$plr_deaths,$plr_suicides,
        $plr_headshots,$plr_firstblood,
        $plr_transgib,$plr_headhunter,$plr_flakmonkey,$plr_combowhore,
        $plr_roadrampage,$plr_carjack,$plr_roadkills,
        '$plr_user',
        '$plr_id',
        '$plr_key',
        '$plr_ip',
        $plr_netspeed,
        $plr_rpg,
        $plr_matches,$plr_time,
        $plr_fph,$plr_sph,$plr_eff,
        $plr_wins,$plr_teamwins,$plr_losses,
        $plr_multi1,$plr_multi2,$plr_multi3,$plr_multi4,$plr_multi5,$plr_multi6,$plr_multi7,
        $plr_spree1,$plr_spreet1,$plr_spreek1,
        $plr_spree2,$plr_spreet2,$plr_spreek2,
        $plr_spree3,$plr_spreet3,$plr_spreek3,
        $plr_spree4,$plr_spreet4,$plr_spreek4,
        $plr_spree5,$plr_spreet5,$plr_spreek5,
        $plr_spree6,$plr_spreet6,$plr_spreek6,
        $plr_combo1,$plr_combo2,$plr_combo3,$plr_combo4,
        $plr_flagcapture,$plr_flagreturn,$plr_flagkill,
        $plr_cpcapture,
        $plr_bombcarried,$plr_bombtossed,$plr_bombkill,
        $plr_nodeconstructed,$plr_nodedestroyed,$plr_nodeconstdestroyed)");
      if (!$result) {
        echo "Error saving player data in database.{$break}\n";
        exit;
      }

      // Save player gametype data
      $result = sql_queryn($link, "REPLACE INTO {$dbpre}playersgt VALUES (
        $gt_num,
        $gt_pnum,
        $gt_tnum,
        $gt_type,
        $gt_score,$gt_frags,$gt_kills,$gt_deaths,$gt_suicides,
        $gt_teamkills,$gt_teamdeaths,
        $gt_sph,$gt_eff,
        $gt_wins,$gt_losses,$gt_matches,
        $gt_time,
        $gt_rank,
        $gt_capcarry,$gt_tossed,$gt_drop,$gt_pickup,$gt_return,$gt_taken,
        $gt_typekill,$gt_assist,$gt_holdtime,
        $gt_extraa,$gt_extrab,$gt_extrac)");
      if (!$result) {
        echo "Error saving player gametype data.{$break}\n";
        exit;
      }

      // Save player alias
      if ($player[$i]->key) {
        $result = sql_queryn($link, "SELECT COUNT(*) FROM {$dbpre}aliases WHERE al_pnum=$pnum AND al_key='$plr_key'");
        if (!$result) {
          echo "Error accessing alias database.{$break}\n";
          exit;
        }
        $row = sql_fetch_row($result);
        sql_free_result($result);
        if (!$row || !$row[0]) {
          $result = sql_queryn($link, "INSERT INTO {$dbpre}aliases VALUES ($pnum,'$plr_key')");
          if (!$result) {
            echo "Error updating alias database.{$break}\n";
            exit;
          }
        }
      }

      // Save Match Player Data
      $result = sql_queryn($link, "INSERT INTO {$dbpre}gplayers VALUES (
        $matchnum,
        $i,
        {$player[$i]->bot},
        $pnum,
        '{$player[$i]->ip}',{$player[$i]->netspeed},{$player[$i]->avgping},
        {$player[$i]->tscore[0]},{$player[$i]->tscore[1]},{$player[$i]->tscore[2]},{$player[$i]->tscore[3]},
        {$player[$i]->kills[0]},{$player[$i]->kills[1]},{$player[$i]->kills[2]},{$player[$i]->kills[3]},
        {$player[$i]->deaths[0]},{$player[$i]->deaths[1]},{$player[$i]->deaths[2]},{$player[$i]->deaths[3]},
        {$player[$i]->suicides[0]},{$player[$i]->suicides[1]},{$player[$i]->suicides[2]},{$player[$i]->suicides[3]},
        {$player[$i]->totaltime[0]},{$player[$i]->totaltime[1]},{$player[$i]->totaltime[2]},{$player[$i]->totaltime[3]},
        {$player[$i]->ranks},{$player[$i]->rankc},
        {$player[$i]->headshots},
        {$player[$i]->firstblood},
        {$player[$i]->carjack},
        {$player[$i]->roadkills},
        {$player[$i]->teamkills[0]},{$player[$i]->teamkills[1]},{$player[$i]->teamkills[2]},{$player[$i]->teamkills[3]},
        {$player[$i]->teamdeaths[0]},{$player[$i]->teamdeaths[1]},{$player[$i]->teamdeaths[2]},{$player[$i]->teamdeaths[3]},
        {$player[$i]->capcarry[0]},{$player[$i]->capcarry[1]},{$player[$i]->capcarry[2]},{$player[$i]->capcarry[3]},
        {$player[$i]->tossed[0]},{$player[$i]->tossed[1]},{$player[$i]->tossed[2]},{$player[$i]->tossed[3]},
        {$player[$i]->dropped[0]},{$player[$i]->dropped[1]},{$player[$i]->dropped[2]},{$player[$i]->dropped[3]},
        {$player[$i]->pickup[0]},{$player[$i]->pickup[1]},{$player[$i]->pickup[2]},{$player[$i]->pickup[3]},
        {$player[$i]->return[0]},{$player[$i]->return[1]},{$player[$i]->return[2]},{$player[$i]->return[3]},
        {$player[$i]->taken[0]},{$player[$i]->taken[1]},{$player[$i]->taken[2]},{$player[$i]->taken[3]},
        {$player[$i]->typekill[0]},{$player[$i]->typekill[1]},{$player[$i]->typekill[2]},{$player[$i]->typekill[3]},
        {$player[$i]->assist[0]},{$player[$i]->assist[1]},{$player[$i]->assist[2]},{$player[$i]->assist[3]},
        {$player[$i]->holdtime[0]},{$player[$i]->holdtime[1]},{$player[$i]->holdtime[2]},{$player[$i]->holdtime[3]},
        {$player[$i]->extraa[0]},{$player[$i]->extraa[1]},{$player[$i]->extraa[2]},{$player[$i]->extraa[3]},
        {$player[$i]->extrab[0]},{$player[$i]->extrab[1]},{$player[$i]->extrab[2]},{$player[$i]->extrab[3]},
        {$player[$i]->extrac[0]},{$player[$i]->extrac[1]},{$player[$i]->extrac[2]},{$player[$i]->extrac[3]},
        {$player[$i]->multi[0]},{$player[$i]->multi[1]},{$player[$i]->multi[2]},{$player[$i]->multi[3]},{$player[$i]->multi[4]},{$player[$i]->multi[5]},{$player[$i]->multi[6]},
        {$player[$i]->spree[0]},{$player[$i]->spree[1]},{$player[$i]->spree[2]},{$player[$i]->spree[3]},{$player[$i]->spree[4]},{$player[$i]->spree[5]},
        {$player[$i]->combo[0]},{$player[$i]->combo[1]},{$player[$i]->combo[2]},{$player[$i]->combo[3]},
        {$player[$i]->transgib},
        {$player[$i]->headhunter},{$player[$i]->flakmonkey},{$player[$i]->combowhore},{$player[$i]->roadrampage},
        {$player[$i]->rank},
        {$player[$i]->team})");
      if (!$result) {
        echo "Error saving match player data in database.{$break}\n";
        exit;
      }

      // Save bot stats
      if ($player[$i]->is_bot() && isset($botstats[$i])) {
        $result = sql_queryn($link, "INSERT INTO {$dbpre}gbots VALUES (
          $matchnum,
          $i,
          {$botstats[$i][0]},
          '{$botstats[$i][1]}',
          '{$botstats[$i][2]}',
          '{$botstats[$i][3]}',
          '{$botstats[$i][4]}',
          '{$botstats[$i][5]}',
          '{$botstats[$i][6]}',
          '{$botstats[$i][7]}',
          '{$botstats[$i][8]}',
          '{$botstats[$i][9]}',
          '{$botstats[$i][10]}')");
        if (!$result) {
          echo "Error saving bot stats in database.{$break}\n";
          exit;
        }
      }

      // Save Player Weapon Accuracy data
      if (array_key_exists($i, $pwastats)) {
        $pwaa = $pwastats[$i];
        $pwa = current($pwaa);
        if ($i == 0) // Weirdness fix
          $pwa = next($pwaa);
        while ($pwa) {
          $weap = key($pwaa);
          $fired = $pwa[0];
          $hits = $pwa[1];
          $damage = $pwa[2];
          $pwk = findpwk($pnum, $weap);

          // Store weapon accuracy data in pwkills
          $result = sql_queryn($link, "UPDATE {$dbpre}pwkills SET pwk_fired=pwk_fired+$fired,pwk_hits=pwk_hits+$hits,pwk_damage=pwk_damage+$damage WHERE pwk_num=$pwk LIMIT 1");
          if (!$result) {
            echo "Error updating pwkills table entry for pwa.{$break}\n";
            exit;
          }
          // Store weapon accuracy in gwaccuracy
          $pgwa = findgwa($matchnum, $pnum, $weap);
          $result = sql_queryn($link, "UPDATE {$dbpre}gwaccuracy SET gwa_fired=gwa_fired+$fired,gwa_hits=gwa_hits+$hits,gwa_damage=gwa_damage+$damage WHERE gwa_num=$pgwa LIMIT 1");
          if (!$result) {
            echo "Error updating gwaccuracy table entry.{$break}\n";
            exit;
          }
          // Store weapon accuracy in weapons
          $result = sql_queryn($link, "UPDATE {$dbpre}weapons SET wp_fired=wp_fired+$fired,wp_hits=wp_hits+$hits,wp_damage=wp_damage+$damage WHERE wp_num=$weap LIMIT 1");
          if (!$result) {
            echo "Error updating weapons table entry for pwa.{$break}\n";
            exit;
          }
          $pwa = next($pwaa);
        }
      }

      // Totals
      $tl_score += array_sum($player[$i]->tscore);

      // Invasion matches do not change most overall stats
      if ($match->gametype != 9 || $config["invasiontotals"]) {
        $tl_kills += array_sum($player[$i]->kills);
        $tl_deaths += array_sum($player[$i]->deaths);
        $tl_suicides += array_sum($player[$i]->suicides);
        $tl_headshots += $player[$i]->headshots;
        $tl_transgib += $player[$i]->transgib;
        if ($player[$i]->headhunter)
          $tl_headhunter++;
        if ($player[$i]->flakmonkey)
          $tl_flakmonkey++;
        if ($player[$i]->combowhore)
          $tl_combowhore++;
        if ($player[$i]->roadrampage)
          $tl_roadrampage++;
        $tl_carjack += $player[$i]->carjack;
        $tl_roadkills += $player[$i]->roadkills;
        $tl_multi1 += $player[$i]->multi[0];
        $tl_multi2 += $player[$i]->multi[1];
        $tl_multi3 += $player[$i]->multi[2];
        $tl_multi4 += $player[$i]->multi[3];
        $tl_multi5 += $player[$i]->multi[4];
        $tl_multi6 += $player[$i]->multi[5];
        $tl_multi7 += $player[$i]->multi[6];
        $tl_spree1 += $player[$i]->spree[0];
        $tl_spreet1 += $player[$i]->spreet[0];
        $tl_spreek1 += $player[$i]->spreek[0];
        $tl_spree2 += $player[$i]->spree[1];
        $tl_spreet2 += $player[$i]->spreet[1];
        $tl_spreek2 += $player[$i]->spreek[1];
        $tl_spree3 += $player[$i]->spree[2];
        $tl_spreet3 += $player[$i]->spreet[2];
        $tl_spreek3 += $player[$i]->spreek[2];
        $tl_spree4 += $player[$i]->spree[3];
        $tl_spreet4 += $player[$i]->spreet[3];
        $tl_spreek4 += $player[$i]->spreek[3];
        $tl_spree5 += $player[$i]->spree[4];
        $tl_spreet5 += $player[$i]->spreet[4];
        $tl_spreek5 += $player[$i]->spreek[4];
        $tl_spree6 += $player[$i]->spree[5];
        $tl_spreet6 += $player[$i]->spreet[5];
        $tl_spreek6 += $player[$i]->spreek[5];
        $tl_combo1 += $player[$i]->combo[0];
        $tl_combo2 += $player[$i]->combo[1];
        $tl_combo3 += $player[$i]->combo[2];
        $tl_combo4 += $player[$i]->combo[3];
      }

      $tl_teamkills += array_sum($player[$i]->teamkills);
      $tl_teamdeaths += array_sum($player[$i]->teamdeaths);
      $tl_time += array_sum($player[$i]->totaltime);
      if (!$player[$i]->is_bot()) {
        $tl_playertime += array_sum($player[$i]->totaltime);
        $tot_ptime += array_sum($player[$i]->totaltime);
      }

      if ($config["bothighs"] || !$player[$i]->is_bot()) {
        // Game Highs
        if (array_sum($player[$i]->frags) > $tl_chfragssg) {
          $tl_chfragssg = array_sum($player[$i]->frags);
          $tl_chfragssg_plr = $pnum;
          $tl_chfragssg_tm = array_sum($player[$i]->totaltime);
          $tl_chfragssg_map = $match->mapnum;
          $tl_chfragssg_date = $sd;
        }
        if (array_sum($player[$i]->kills) > $tl_chkillssg) {
          $tl_chkillssg = array_sum($player[$i]->kills);
          $tl_chkillssg_plr = $pnum;
          $tl_chkillssg_tm = array_sum($player[$i]->totaltime);
          $tl_chkillssg_map = $match->mapnum;
          $tl_chkillssg_date = $sd;
        }
        if (array_sum($player[$i]->deaths) > $tl_chdeathssg) {
          $tl_chdeathssg = array_sum($player[$i]->deaths);
          $tl_chdeathssg_plr = $pnum;
          $tl_chdeathssg_tm = array_sum($player[$i]->totaltime);
          $tl_chdeathssg_map = $match->mapnum;
          $tl_chdeathssg_date = $sd;
        }
        if (array_sum($player[$i]->suicides) > $tl_chsuicidessg) {
          $tl_chsuicidessg = array_sum($player[$i]->suicides);
          $tl_chsuicidessg_plr = $pnum;
          $tl_chsuicidessg_tm = array_sum($player[$i]->totaltime);
          $tl_chsuicidessg_map = $match->mapnum;
          $tl_chsuicidessg_date = $sd;
        }
        if ($player[$i]->carjack > $tl_chcarjacksg) {
          $tl_chcarjacksg = $player[$i]->carjack;
          $tl_chcarjacksg_plr = $pnum;
          $tl_chcarjacksg_tm = array_sum($player[$i]->totaltime);
          $tl_chcarjacksg_map = $match->mapnum;
          $tl_chcarjacksg_date = $sd;
        }
        if ($player[$i]->roadkills > $tl_chroadkillssg) {
          $tl_chroadkillssg = $player[$i]->roadkills;
          $tl_chroadkillssg_plr = $pnum;
          $tl_chroadkillssg_tm = array_sum($player[$i]->totaltime);
          $tl_chroadkillssg_map = $match->mapnum;
          $tl_chroadkillssg_date = $sd;
        }

        // Career Highs
        if ($plr_matches >= $config["minchmatches"] && $plr_time >= $config["minchtime"]) {
          if ($plr_time == 0)
            $plr_fph = "0.0";
          else
            $plr_fph = round($plr_frags * (360000 / $plr_time), 1);

          if ($plr_frags > $tl_chfrags) {
            $tl_chfrags = $plr_frags;
            $tl_chfrags_plr = $pnum;
            $tl_chfrags_gms = $plr_matches;
            $tl_chfrags_tm = $plr_time;
          }
          if ($plr_kills > $tl_chkills) {
            $tl_chkills = $plr_kills;
            $tl_chkills_plr = $pnum;
            $tl_chkills_gms = $plr_matches;
            $tl_chkills_tm = $plr_time;
          }
          if ($plr_deaths > $tl_chdeaths) {
            $tl_chdeaths = $plr_deaths;
            $tl_chdeaths_plr = $pnum;
            $tl_chdeaths_gms = $plr_matches;
            $tl_chdeaths_tm = $plr_time;
          }
          if ($plr_suicides > $tl_chsuicides) {
            $tl_chsuicides = $plr_suicides;
            $tl_chsuicides_plr = $pnum;
            $tl_chsuicides_gms = $plr_matches;
            $tl_chsuicides_tm = $plr_time;
          }
          if ($plr_firstblood > $tl_chfirstblood) {
            $tl_chfirstblood = $plr_firstblood;
            $tl_chfirstblood_plr = $pnum;
            $tl_chfirstblood_gms = $plr_matches;
            $tl_chfirstblood_tm = $plr_time;
          }
          if ($plr_headshots > $tl_chheadshots) {
            $tl_chheadshots = $plr_headshots;
            $tl_chheadshots_plr = $pnum;
            $tl_chheadshots_gms = $plr_matches;
            $tl_chheadshots_tm = $plr_time;
          }
          if ($plr_carjack > $tl_chcarjack) {
            $tl_chcarjack = $plr_carjack;
            $tl_chcarjack_plr = $pnum;
            $tl_chcarjack_gms = $plr_matches;
            $tl_chcarjack_tm = $plr_time;
          }
          if ($plr_roadkills > $tl_chroadkills) {
            $tl_chroadkills = $plr_roadkills;
            $tl_chroadkills_plr = $pnum;
            $tl_chroadkills_gms = $plr_matches;
            $tl_chroadkills_tm = $plr_time;
          }
          if ($plr_multi1 > $tl_chmulti1) {
            $tl_chmulti1 = $plr_multi1;
            $tl_chmulti1_plr = $pnum;
            $tl_chmulti1_gms = $plr_matches;
            $tl_chmulti1_tm = $plr_time;
          }
          if ($plr_multi2 > $tl_chmulti2) {
            $tl_chmulti2 = $plr_multi2;
            $tl_chmulti2_plr = $pnum;
            $tl_chmulti2_gms = $plr_matches;
            $tl_chmulti2_tm = $plr_time;
          }
          if ($plr_multi3 > $tl_chmulti3) {
            $tl_chmulti3 = $plr_multi3;
            $tl_chmulti3_plr = $pnum;
            $tl_chmulti3_gms = $plr_matches;
            $tl_chmulti3_tm = $plr_time;
          }
          if ($plr_multi4 > $tl_chmulti4) {
            $tl_chmulti4 = $plr_multi4;
            $tl_chmulti4_plr = $pnum;
            $tl_chmulti4_gms = $plr_matches;
            $tl_chmulti4_tm = $plr_time;
          }
          if ($plr_multi5 > $tl_chmulti5) {
            $tl_chmulti5 = $plr_multi5;
            $tl_chmulti5_plr = $pnum;
            $tl_chmulti5_gms = $plr_matches;
            $tl_chmulti5_tm = $plr_time;
          }
          if ($plr_multi6 > $tl_chmulti6) {
            $tl_chmulti6 = $plr_multi6;
            $tl_chmulti6_plr = $pnum;
            $tl_chmulti6_gms = $plr_matches;
            $tl_chmulti6_tm = $plr_time;
          }
          if ($plr_multi7 > $tl_chmulti7) {
            $tl_chmulti7 = $plr_multi7;
            $tl_chmulti7_plr = $pnum;
            $tl_chmulti7_gms = $plr_matches;
            $tl_chmulti7_tm = $plr_time;
          }
          if ($plr_spree1 > $tl_chspree1) {
            $tl_chspree1 = $plr_spree1;
            $tl_chspree1_plr = $pnum;
            $tl_chspree1_gms = $plr_matches;
            $tl_chspree1_tm = $plr_time;
          }
          if ($plr_spree2 > $tl_chspree2) {
            $tl_chspree2 = $plr_spree2;
            $tl_chspree2_plr = $pnum;
            $tl_chspree2_gms = $plr_matches;
            $tl_chspree2_tm = $plr_time;
          }
          if ($plr_spree3 > $tl_chspree3) {
            $tl_chspree3 = $plr_spree3;
            $tl_chspree3_plr = $pnum;
            $tl_chspree3_gms = $plr_matches;
            $tl_chspree3_tm = $plr_time;
          }
          if ($plr_spree4 > $tl_chspree4) {
            $tl_chspree4 = $plr_spree4;
            $tl_chspree4_plr = $pnum;
            $tl_chspree4_gms = $plr_matches;
            $tl_chspree4_tm = $plr_time;
          }
          if ($plr_spree5 > $tl_chspree5) {
            $tl_chspree5 = $plr_spree5;
            $tl_chspree5_plr = $pnum;
            $tl_chspree5_gms = $plr_matches;
            $tl_chspree5_tm = $plr_time;
          }
          if ($plr_spree6 > $tl_chspree6) {
            $tl_chspree6 = $plr_spree6;
            $tl_chspree6_plr = $pnum;
            $tl_chspree6_gms = $plr_matches;
            $tl_chspree6_tm = $plr_time;
          }
          if ($plr_fph > $tl_chfph) {
            $tl_chfph = $plr_fph;
            $tl_chfph_plr = $pnum;
            $tl_chfph_gms = $plr_matches;
            $tl_chfph_tm = $plr_time;
          }
          if ($plr_wins > $tl_chwins) {
            $tl_chwins = $plr_wins;
            $tl_chwins_plr = $pnum;
            $tl_chwins_gms = $plr_matches;
            $tl_chwins_tm = $plr_time;
          }
          if ($plr_teamwins > $tl_chteamwins) {
            $tl_chteamwins = $plr_teamwins;
            $tl_chteamwins_plr = $pnum;
            $tl_chteamwins_gms = $plr_matches;
            $tl_chteamwins_tm = $plr_time;
          }
          if ($plr_flagcapture > $tl_chflagcapture) {
            $tl_chflagcapture = $plr_flagcapture;
            $tl_chflagcapture_plr = $pnum;
            $tl_chflagcapture_gms = $plr_matches;
            $tl_chflagcapture_tm = $plr_time;
          }
          if ($plr_flagreturn > $tl_chflagreturn) {
            $tl_chflagreturn = $plr_flagreturn;
            $tl_chflagreturn_plr = $pnum;
            $tl_chflagreturn_gms = $plr_matches;
            $tl_chflagreturn_tm = $plr_time;
          }
          if ($plr_flagkill > $tl_chflagkill) {
            $tl_chflagkill = $plr_flagkill;
            $tl_chflagkill_plr = $pnum;
            $tl_chflagkill_gms = $plr_matches;
            $tl_chflagkill_tm = $plr_time;
          }
          if ($plr_cpcapture > $tl_chcpcapture) {
            $tl_chcpcapture = $plr_cpcapture;
            $tl_chcpcapture_plr = $pnum;
            $tl_chcpcapture_gms = $plr_matches;
            $tl_chcpcapture_tm = $plr_time;
          }
          if ($plr_bombcarried > $tl_chbombcarried) {
            $tl_chbombcarried = $plr_bombcarried;
            $tl_chbombcarried_plr = $pnum;
            $tl_chbombcarried_gms = $plr_matches;
            $tl_chbombcarried_tm = $plr_time;
          }
          if ($plr_bombtossed > $tl_chbombtossed) {
            $tl_chbombtossed = $plr_bombtossed;
            $tl_chbombtossed_plr = $pnum;
            $tl_chbombtossed_gms = $plr_matches;
            $tl_chbombtossed_tm = $plr_time;
          }
          if ($plr_bombkill > $tl_chbombkill) {
            $tl_chbombkill = $plr_bombkill;
            $tl_chbombkill_plr = $pnum;
            $tl_chbombkill_gms = $plr_matches;
            $tl_chbombkill_tm = $plr_time;
          }
          if ($plr_nodeconstructed > $tl_chnodeconstructed) {
            $tl_chnodeconstructed = $plr_nodeconstructed;
            $tl_chnodeconstructed_plr = $pnum;
            $tl_chnodeconstructed_gms = $plr_matches;
            $tl_chnodeconstructed_tm = $plr_time;
          }
          if ($plr_nodedestroyed > $tl_chnodedestroyed) {
            $tl_chnodedestroyed = $plr_nodedestroyed;
            $tl_chnodedestroyed_plr = $pnum;
            $tl_chnodedestroyed_gms = $plr_matches;
            $tl_chnodedestroyed_tm = $plr_time;
          }
          if ($plr_nodeconstdestroyed > $tl_chnodeconstdestroyed) {
            $tl_chnodeconstdestroyed = $plr_nodeconstdestroyed;
            $tl_chnodeconstdestroyed_plr = $pnum;
            $tl_chnodeconstdestroyed_gms = $plr_matches;
            $tl_chnodeconstdestroyed_tm = $plr_time;
          }
          if ($plr_headhunter > $tl_chheadhunter) {
            $tl_chheadhunter = $plr_headhunter;
            $tl_chheadhunter_plr = $pnum;
            $tl_chheadhunter_gms = $plr_matches;
            $tl_chheadhunter_tm = $plr_time;
          }
          if ($plr_flakmonkey > $tl_chflakmonkey) {
            $tl_chflakmonkey = $plr_flakmonkey;
            $tl_chflakmonkey_plr = $pnum;
            $tl_chflakmonkey_gms = $plr_matches;
            $tl_chflakmonkey_tm = $plr_time;
          }
          if ($plr_combowhore > $tl_chcombowhore) {
            $tl_chcombowhore = $plr_combowhore;
            $tl_chcombowhore_plr = $pnum;
            $tl_chcombowhore_gms = $plr_matches;
            $tl_chcombowhore_tm = $plr_time;
          }
          if ($plr_roadrampage > $tl_chroadrampage) {
            $tl_chroadrampage = $plr_roadrampage;
            $tl_chroadrampage_plr = $pnum;
            $tl_chroadrampage_gms = $plr_matches;
            $tl_chroadrampage_tm = $plr_time;
          }
        }
      }
      switch ($match->gametype) {
        case 1: // DeathMatch
          $tl_spkills += array_sum($player[$i]->kills);
          $tl_spdeaths += array_sum($player[$i]->deaths);
          $tl_spsuicides += array_sum($player[$i]->suicides);
          $tl_spteamkills += array_sum($player[$i]->teamkills);
          $tl_spteamdeaths += array_sum($player[$i]->teamdeaths);
          $tl_spmatches += 1;
          $tl_sptime += array_sum($player[$i]->totaltime);
          break;
        case 2: // Capture the Flag
          $tl_flagcapture += array_sum($player[$i]->capcarry);
          $tl_flagdrop += array_sum($player[$i]->dropped);
          $tl_flagpickup += array_sum($player[$i]->pickup);
          $tl_flagreturn += array_sum($player[$i]->return);
          $tl_flagtaken += array_sum($player[$i]->taken);
          $tl_flagkill += array_sum($player[$i]->typekill);
          $tl_flagassist += array_sum($player[$i]->assist);
          if (array_sum($player[$i]->capcarry) > $tl_chflagcapturesg && ($config["bothighs"] || !$player[$i]->is_bot())) {
            $tl_chflagcapturesg = array_sum($player[$i]->capcarry);
            $tl_chflagcapturesg_plr = $pnum;
            $tl_chflagcapturesg_tm = array_sum($player[$i]->totaltime);
            $tl_chflagcapturesg_map = $match->mapnum;
            $tl_chflagcapturesg_date = $sd;
          }
          if (array_sum($player[$i]->return) > $tl_chflagreturnsg && ($config["bothighs"] || !$player[$i]->is_bot())) {
            $tl_chflagreturnsg = array_sum($player[$i]->return);
            $tl_chflagreturnsg_plr = $pnum;
            $tl_chflagreturnsg_tm = array_sum($player[$i]->totaltime);
            $tl_chflagreturnsg_map = $match->mapnum;
            $tl_chflagreturnsg_date = $sd;
          }
          if (array_sum($player[$i]->typekill) > $tl_chflagkillsg && ($config["bothighs"] || !$player[$i]->is_bot())) {
            $tl_chflagkillsg = array_sum($player[$i]->typekill);
            $tl_chflagkillsg_plr = $pnum;
            $tl_chflagkillsg_tm = array_sum($player[$i]->totaltime);
            $tl_chflagkillsg_map = $match->mapnum;
            $tl_chflagkillsg_date = $sd;
          }
          break;
        case 3: // Bombing Run
          $tl_bombcarried += array_sum($player[$i]->capcarry);
          $tl_bombtossed += array_sum($player[$i]->tossed);
          $tl_bombdrop += array_sum($player[$i]->dropped);
          $tl_bombpickup += array_sum($player[$i]->pickup);
          $tl_bombtaken += array_sum($player[$i]->taken);
          $tl_bombkill += array_sum($player[$i]->typekill);
          $tl_bombassist += array_sum($player[$i]->assist);
          if (array_sum($player[$i]->capcarry) > $tl_chbombcarriedsg && ($config["bothighs"] || !$player[$i]->is_bot())) {
            $tl_chbombcarriedsg = array_sum($player[$i]->capcarry);
            $tl_chbombcarriedsg_plr = $pnum;
            $tl_chbombcarriedsg_tm = array_sum($player[$i]->totaltime);
            $tl_chbombcarriedsg_map = $match->mapnum;
            $tl_chbombcarriedsg_date = $sd;
          }
          if (array_sum($player[$i]->tossed) > $tl_chbombtossedsg && ($config["bothighs"] || !$player[$i]->is_bot())) {
            $tl_chbombtossedsg = array_sum($player[$i]->tossed);
            $tl_chbombtossedsg_plr = $pnum;
            $tl_chbombtossedsg_tm = array_sum($player[$i]->totaltime);
            $tl_chbombtossedsg_map = $match->mapnum;
            $tl_chbombtossedsg_date = $sd;
          }
          break;
        case 4: // Team DeathMatch
          break;
        case 5: // Assault
          break;
        case 6: // Onslaught
          $tl_nodeconstructed += array_sum($player[$i]->pickup);
          $tl_nodeconstdestroyed += array_sum($player[$i]->dropped);
          $tl_nodedestroyed += array_sum($player[$i]->taken);
          $tl_coredestroyed += array_sum($player[$i]->capcarry);
          if (array_sum($player[$i]->pickup) > $tl_chnodeconstructedsg && ($config["bothighs"] || !$player[$i]->is_bot())) {
            $tl_chnodeconstructedsg = array_sum($player[$i]->pickup);
            $tl_chnodeconstructedsg_plr = $pnum;
            $tl_chnodeconstructedsg_tm = array_sum($player[$i]->totaltime);
            $tl_chnodeconstructedsg_map = $match->mapnum;
            $tl_chnodeconstructedsg_date = $sd;
          }
          if (array_sum($player[$i]->dropped) > $tl_chnodeconstdestroyedsg && ($config["bothighs"] || !$player[$i]->is_bot())) {
            $tl_chnodeconstdestroyedsg = array_sum($player[$i]->dropped);
            $tl_chnodeconstdestroyedsg_plr = $pnum;
            $tl_chnodeconstdestroyedsg_tm = array_sum($player[$i]->totaltime);
            $tl_chnodeconstdestroyedsg_map = $match->mapnum;
            $tl_chnodeconstdestroyedsg_date = $sd;
          }
          if (array_sum($player[$i]->taken) > $tl_chnodedestroyedsg && ($config["bothighs"] || !$player[$i]->is_bot())) {
            $tl_chnodedestroyedsg = array_sum($player[$i]->taken);
            $tl_chnodedestroyedsg_plr = $pnum;
            $tl_chnodedestroyedsg_tm = array_sum($player[$i]->totaltime);
            $tl_chnodedestroyedsg_map = $match->mapnum;
            $tl_chnodedestroyedsg_date = $sd;
          }
          $break;
        case 7: // Double Domination
          $tl_cpcapture += $player[$i]->capcarry[0] + $player[$i]->capcarry[1];
          if ($player[$i]->capcarry[0] + $player[$i]->capcarry[1] > $tl_chcpcapturesg && ($config["bothighs"] || !$player[$i]->is_bot())) {
            $tl_chcpcapturesg = $player[$i]->capcarry[0] + $player[$i]->capcarry[1];
            $tl_chcpcapturesg_plr = $pnum;
            $tl_chcpcapturesg_tm = array_sum($player[$i]->totaltime);
            $tl_chcpcapturesg_map = $match->mapnum;
            $tl_chcpcapturesg_date = $sd;
          }
          break;
        default: // Other
      }
    }
    $playerc = next($player);
  }

  if ($config["ranksystem"])
    rankplayers();

  // Save Totals
  $tl_matches++;
  $tl_gametime += $match->length;

  $result = sql_queryn($link, "REPLACE INTO {$dbpre}totals VALUES (
    'Totals',
    $tl_score,$tl_kills,$tl_deaths,$tl_suicides,
    $tl_teamkills,$tl_teamdeaths,
    $tl_players,
    $tl_matches,$tl_time,$tl_gametime,$tl_playertime,
    $tl_cpcapture,
    $tl_flagcapture,$tl_flagdrop,$tl_flagpickup,$tl_flagreturn,$tl_flagtaken,$tl_flagkill,$tl_flagassist,
    $tl_bombcarried,$tl_bombtossed,$tl_bombdrop,$tl_bombpickup,$tl_bombtaken,$tl_bombkill,$tl_bombassist,
    $tl_nodeconstructed,$tl_nodeconstdestroyed,$tl_nodedestroyed,$tl_coredestroyed,
    $tl_spkills,$tl_spdeaths,$tl_spsuicides,$tl_spteamkills,$tl_spteamdeaths,$tl_spmatches,$tl_sptime,
    $tl_headshots,
    $tl_multi1,$tl_multi2,$tl_multi3,$tl_multi4,$tl_multi5,$tl_multi6,$tl_multi7,
    $tl_spree1,$tl_spreet1,$tl_spreek1,
    $tl_spree2,$tl_spreet2,$tl_spreek2,
    $tl_spree3,$tl_spreet3,$tl_spreek3,
    $tl_spree4,$tl_spreet4,$tl_spreek4,
    $tl_spree5,$tl_spreet5,$tl_spreek5,
    $tl_spree6,$tl_spreet6,$tl_spreek6,
    $tl_combo1,$tl_combo2,$tl_combo3,$tl_combo4,
    $tl_transgib,$tl_headhunter,$tl_flakmonkey,$tl_combowhore,
    $tl_roadrampage,$tl_carjack,$tl_roadkills,
    $tl_chfrags,$tl_chfrags_plr,$tl_chfrags_gms,$tl_chfrags_tm,
    $tl_chkills,$tl_chkills_plr,$tl_chkills_gms,$tl_chkills_tm,
    $tl_chdeaths,$tl_chdeaths_plr,$tl_chdeaths_gms,$tl_chdeaths_tm,
    $tl_chsuicides,$tl_chsuicides_plr,$tl_chsuicides_gms,$tl_chsuicides_tm,
    $tl_chfirstblood,$tl_chfirstblood_plr,$tl_chfirstblood_gms,$tl_chfirstblood_tm,
    $tl_chheadshots,$tl_chheadshots_plr,$tl_chheadshots_gms,$tl_chheadshots_tm,
    $tl_chcarjack,$tl_chcarjack_plr,$tl_chcarjack_gms,$tl_chcarjack_tm,
    $tl_chroadkills,$tl_chroadkills_plr,$tl_chroadkills_gms,$tl_chroadkills_tm,
    $tl_chmulti1,$tl_chmulti1_plr,$tl_chmulti1_gms,$tl_chmulti1_tm,
    $tl_chmulti2,$tl_chmulti2_plr,$tl_chmulti2_gms,$tl_chmulti2_tm,
    $tl_chmulti3,$tl_chmulti3_plr,$tl_chmulti3_gms,$tl_chmulti3_tm,
    $tl_chmulti4,$tl_chmulti4_plr,$tl_chmulti4_gms,$tl_chmulti4_tm,
    $tl_chmulti5,$tl_chmulti5_plr,$tl_chmulti5_gms,$tl_chmulti5_tm,
    $tl_chmulti6,$tl_chmulti6_plr,$tl_chmulti6_gms,$tl_chmulti6_tm,
    $tl_chmulti7,$tl_chmulti7_plr,$tl_chmulti7_gms,$tl_chmulti7_tm,
    $tl_chspree1,$tl_chspree1_plr,$tl_chspree1_gms,$tl_chspree1_tm,
    $tl_chspree2,$tl_chspree2_plr,$tl_chspree2_gms,$tl_chspree2_tm,
    $tl_chspree3,$tl_chspree3_plr,$tl_chspree3_gms,$tl_chspree3_tm,
    $tl_chspree4,$tl_chspree4_plr,$tl_chspree4_gms,$tl_chspree4_tm,
    $tl_chspree5,$tl_chspree5_plr,$tl_chspree5_gms,$tl_chspree5_tm,
    $tl_chspree6,$tl_chspree6_plr,$tl_chspree6_gms,$tl_chspree6_tm,
    $tl_chfph,$tl_chfph_plr,$tl_chfph_gms,$tl_chfph_tm,
    $tl_chcpcapture,$tl_chcpcapture_plr,$tl_chcpcapture_gms,$tl_chcpcapture_tm,
    $tl_chflagcapture,$tl_chflagcapture_plr,$tl_chflagcapture_gms,$tl_chflagcapture_tm,
    $tl_chflagreturn,$tl_chflagreturn_plr,$tl_chflagreturn_gms,$tl_chflagreturn_tm,
    $tl_chflagkill,$tl_chflagkill_plr,$tl_chflagkill_gms,$tl_chflagkill_tm,
    $tl_chbombcarried,$tl_chbombcarried_plr,$tl_chbombcarried_gms,$tl_chbombcarried_tm,
    $tl_chbombtossed,$tl_chbombtossed_plr,$tl_chbombtossed_gms,$tl_chbombtossed_tm,
    $tl_chbombkill,$tl_chbombkill_plr,$tl_chbombkill_gms,$tl_chbombkill_tm,
    $tl_chnodeconstructed,$tl_chnodeconstructed_plr,$tl_chnodeconstructed_gms,$tl_chnodeconstructed_tm,
    $tl_chnodedestroyed,$tl_chnodedestroyed_plr,$tl_chnodedestroyed_gms,$tl_chnodedestroyed_tm,
    $tl_chnodeconstdestroyed,$tl_chnodeconstdestroyed_plr,$tl_chnodeconstdestroyed_gms,$tl_chnodeconstdestroyed_tm,
    $tl_chheadhunter,$tl_chheadhunter_plr,$tl_chheadhunter_gms,$tl_chheadhunter_tm,
    $tl_chflakmonkey,$tl_chflakmonkey_plr,$tl_chflakmonkey_gms,$tl_chflakmonkey_tm,
    $tl_chcombowhore,$tl_chcombowhore_plr,$tl_chcombowhore_gms,$tl_chcombowhore_tm,
    $tl_chroadrampage,$tl_chroadrampage_plr,$tl_chroadrampage_gms,$tl_chroadrampage_tm,
    $tl_chwins,$tl_chwins_plr,$tl_chwins_gms,$tl_chwins_tm,
    $tl_chteamwins,$tl_chteamwins_plr,$tl_chteamwins_gms,$tl_chteamwins_tm,
    $tl_chfragssg,$tl_chfragssg_plr,$tl_chfragssg_tm,$tl_chfragssg_map,'$tl_chfragssg_date',
    $tl_chkillssg,$tl_chkillssg_plr,$tl_chkillssg_tm,$tl_chkillssg_map,'$tl_chkillssg_date',
    $tl_chdeathssg,$tl_chdeathssg_plr,$tl_chdeathssg_tm,$tl_chdeathssg_map,'$tl_chdeathssg_date',
    $tl_chsuicidessg,$tl_chsuicidessg_plr,$tl_chsuicidessg_tm,$tl_chsuicidessg_map,'$tl_chsuicidessg_date',
    $tl_chcarjacksg,$tl_chcarjacksg_plr,$tl_chcarjacksg_tm,$tl_chcarjacksg_map,'$tl_chcarjacksg_date',
    $tl_chroadkillssg,$tl_chroadkillssg_plr,$tl_chroadkillssg_tm,$tl_chroadkillssg_map,'$tl_chroadkillssg_date',
    $tl_chcpcapturesg,$tl_chcpcapturesg_plr,$tl_chcpcapturesg_tm,$tl_chcpcapturesg_map,'$tl_chcpcapturesg_date',
    $tl_chflagcapturesg,$tl_chflagcapturesg_plr,$tl_chflagcapturesg_tm,$tl_chflagcapturesg_map,'$tl_chflagcapturesg_date',
    $tl_chflagreturnsg,$tl_chflagreturnsg_plr,$tl_chflagreturnsg_tm,$tl_chflagreturnsg_map,'$tl_chflagreturnsg_date',
    $tl_chflagkillsg,$tl_chflagkillsg_plr,$tl_chflagkillsg_tm,$tl_chflagkillsg_map,'$tl_chflagkillsg_date',
    $tl_chbombcarriedsg,$tl_chbombcarriedsg_plr,$tl_chbombcarriedsg_tm,$tl_chbombcarriedsg_map,'$tl_chbombcarriedsg_date',
    $tl_chbombtossedsg,$tl_chbombtossedsg_plr,$tl_chbombtossedsg_tm,$tl_chbombtossedsg_map,'$tl_chbombtossedsg_date',
    $tl_chbombkillsg,$tl_chbombkillsg_plr,$tl_chbombkillsg_tm,$tl_chbombkillsg_map,'$tl_chbombkillsg_date',
    $tl_chnodeconstructedsg,$tl_chnodeconstructedsg_plr,$tl_chnodeconstructedsg_tm,$tl_chnodeconstructedsg_map,'$tl_chnodeconstructedsg_date',
    $tl_chnodeconstdestroyedsg,$tl_chnodeconstdestroyedsg_plr,$tl_chnodeconstdestroyedsg_tm,$tl_chnodeconstdestroyedsg_map,'$tl_chnodeconstdestroyedsg_date',
    $tl_chnodedestroyedsg,$tl_chnodedestroyedsg_plr,$tl_chnodedestroyedsg_tm,$tl_chnodedestroyedsg_map,'$tl_chnodedestroyedsg_date')");

  if (!$result) {
    echo "Error saving totals data.{$break}\n";
    exit;
  }

  // Load Weapon Highs
  $result = sql_queryn($link, "SELECT wp_num,wp_desc,
    wp_chkills,wp_chkills_plr,wp_chkills_gms,wp_chkills_tm,
    wp_chdeaths,wp_chdeaths_plr,wp_chdeaths_gms,wp_chdeaths_tm,
    wp_chdeathshld,wp_chdeathshld_plr,wp_chdeathshld_gms,wp_chdeathshld_tm,
    wp_chsuicides,wp_chsuicides_plr,wp_chsuicides_gms,wp_chsuicides_tm,
    wp_chkillssg,wp_chkillssg_plr,wp_chkillssg_tm,
    wp_chkillssg_map,wp_chkillssg_dt,wp_chdeathssg,wp_chdeathssg_plr,wp_chdeathssg_tm,
    wp_chdeathssg_map,wp_chdeathssg_dt,wp_chdeathshldsg,wp_chdeathshldsg_plr,
    wp_chdeathshldsg_tm,wp_chdeathshldsg_map,wp_chdeathshldsg_dt,wp_chsuicidessg,
    wp_chsuicidessg_plr,wp_chsuicidessg_tm,wp_chsuicidessg_map,wp_chsuicidessg_dt
    FROM {$dbpre}weapons");
  if (!$result) {
    echo "Error loading weapons data.{$break}\n";
    exit;
  }
  $maxweapon = 0;
  $weapsg = array();
  while($row = sql_fetch_assoc($result)) {
  	$num = $row["wp_num"];
    if ($num > $maxweapon)
      $maxweapon = $num;
    $weapsg[$num] = $row;
  }
  sql_free_result($result);

  // Create temporary weapon specific per player totals table
  if (strtolower($dbtype) == "sqlite") {
    $result = sql_queryn($link, "CREATE TEMPORARY TABLE temp_wtkills (wt_plr mediumint(8) NOT NULL, wt_desc varchar(35) NOT NULL default '', wt_num smallint(5) NOT NULL default 0, wt_intnum smallint(5) NOT NULL default 0, wt_kills int(10) NOT NULL default 0, wt_deaths int(10) NOT NULL default 0, wt_held int(10) NOT NULL default 0, wt_suicides int(10) NOT NULL default 0)");
    if (!$result) {
      echo "Error creating temp table for weapon specific per player totals.{$break}\n";
      exit;
    }
    $result = sql_queryn($link, "CREATE INDEX wt_plrdesc ON temp_wtkills (wt_plr,wt_desc)");
    if (!$result) {
      echo "Error creating temp table for weapon specific per player totals.{$break}\n";
      exit;
    }
  }
  else {
    sql_queryn($link, "DROP TABLE IF EXISTS temp_wtkills");
    $result = sql_queryn($link, "CREATE TEMPORARY TABLE temp_wtkills (wt_plr mediumint(8) unsigned NOT NULL, wt_desc varchar(35) NOT NULL default '', wt_num smallint(5) unsigned NOT NULL default 0, wt_intnum smallint(5) unsigned NOT NULL default 0, wt_kills int(10) unsigned NOT NULL default 0, wt_deaths int(10) unsigned NOT NULL default 0, wt_held int(10) unsigned NOT NULL default 0, wt_suicides int(10) unsigned NOT NULL default 0, UNIQUE KEY wt_plrdesc (wt_plr,wt_desc)) Type=HEAP");
    if (!$result) {
      $result = sql_queryn($link, "CREATE TABLE temp_wtkills (wt_plr mediumint(8) unsigned NOT NULL, wt_desc varchar(35) NOT NULL default '', wt_num smallint(5) unsigned NOT NULL default 0, wt_intnum smallint(5) unsigned NOT NULL default 0, wt_kills int(10) unsigned NOT NULL default 0, wt_deaths int(10) unsigned NOT NULL default 0, wt_held int(10) unsigned NOT NULL default 0, wt_suicides int(10) unsigned NOT NULL default 0, UNIQUE KEY wt_plrdesc (wt_plr,wt_desc)) Type=HEAP");
      if (!$result) {
        echo "Error creating temp table for weapon specific per player totals.{$break}\n";
        exit;
      }
    }
  }
  for ($wpn = 0; $wpn <= $maxweapon; $wpn++) {
    if (isset($weapsg[$wpn])) {
      for ($i = 0; $i <= $match->maxplayer; $i++) {
        if (isset($player[$i]) && $player[$i]->name != "") {
          $sweap = sql_addslashes($weapsg[$wpn]['wp_desc']);
          if (strtolower($dbtype) == "sqlite")
            $result = sql_queryn($link, "INSERT INTO temp_wtkills (wt_plr,wt_desc,wt_num,wt_intnum) VALUES ($i,'$sweap',{$weapsg[$wpn]['wp_num']},$wpn)");
          else
            $result = sql_queryn($link, "INSERT IGNORE INTO temp_wtkills (wt_plr,wt_desc,wt_num,wt_intnum) VALUES ($i,'$sweap',{$weapsg[$wpn]['wp_num']},$wpn)");
          if (!$result)
            echo "Error inserting into temp table for weapon specific per player totals.{$break}\n";
        }
      }
    }
  }

  // Save Individual Kill Log
  for ($i = 0; $i < $match->gkcount; $i++) {
    list($gkkiller, $gkvictim, $gktime, $gkkweapon, $gkvweapon, $gkkteam, $gkvteam, $gkkwtype, $gkvwtype) = $gkills[$i];
    if (($gkkiller < 0 || $player[$gkkiller]->name != "") && ($gkvictim < 0 || $player[$gkvictim]->name != "")) {
      $result = sql_queryn($link, "INSERT INTO {$dbpre}gkills VALUES ($matchnum,$gkkiller,$gkvictim,$gktime,$gkkweapon,$gkvweapon,$gkkteam,$gkvteam)");
      if (!$result) {
        echo "Error saving gkills data.{$break}\n";
        exit;
      }

      // Use actual player numbers for gkkiller and gkvictim
      if ($gkkiller >= 0)
        $killer = $player[$gkkiller]->num;
      else
        $killer = -1;
      if ($gkvictim >= 0)
        $victim = $player[$gkvictim]->num;
      else
        $victim = -1;

      if ($killer == $victim) { // Self-inflicted Suicide
        // Killer Weapon: frags-1 / suicides+1
        $pwk = findpwk($killer, $gkkweapon);
        $result = sql_queryn($link, "UPDATE {$dbpre}pwkills SET pwk_frags=pwk_frags-1,pwk_suicides=pwk_suicides+1 WHERE pwk_num=$pwk LIMIT 1");
        if (!$result) {
          echo "Error updating pwkills table entry [1].{$break}\n";
          exit;
        }
        $mwk = findmwk($match->mapnum, $gkkweapon);
        $result = sql_queryn($link, "UPDATE {$dbpre}mwkills SET mwk_suicides=mwk_suicides+1 WHERE mwk_num=$mwk LIMIT 1");
        if (!$result) {
          echo "Error updating mwkills table entry [1].{$break}\n";
          exit;
        }
        // Killer Weapon Totals: frags-1 / suicides+1
        $result = sql_queryn($link, "UPDATE {$dbpre}weapons SET wp_frags=wp_frags-1,wp_suicides=wp_suicides+1 WHERE wp_num=$gkkweapon LIMIT 1");
        if (!$result) {
          echo "Error updating weapons table entry [1].{$break}\n";
          exit;
        }
        // Weapon Specific for Game
        if (strtolower($dbtype) == "sqlite")
          $result = sql_queryn($link, "UPDATE temp_wtkills SET wt_suicides=wt_suicides+1 WHERE wt_plr=$gkkiller AND wt_desc='{$weapsg[$gkkweapon]['wp_desc']}'");
        else
          $result = sql_queryn($link, "UPDATE IGNORE temp_wtkills SET wt_suicides=wt_suicides+1 WHERE wt_plr=$gkkiller AND wt_desc='{$weapsg[$gkkweapon]['wp_desc']}'");
        if (!$result)
          echo "Error inserting into temp table for weapon specific per player totals (suicides).{$break}\n";
      }
      else if ($killer == -1) { // Environment Suicide
        if ($gkkwtype > 0 || $match->gametype == 9) { // Auto-turrents count as deaths
          // Killing Weapon: deaths+1
          $pwk = findpwk($victim, $gkkweapon);
          $result = sql_queryn($link, "UPDATE {$dbpre}pwkills SET pwk_deaths=pwk_deaths+1 WHERE pwk_num=$pwk LIMIT 1");
          if (!$result) {
            echo "Error updating pwkills table entry [2a].{$break}\n";
            exit;
          }
          // Victim Weapon: deaths+1
          $pwk = findpwk($victim, $gkvweapon);
          $result = sql_queryn($link, "UPDATE {$dbpre}pwkills SET pwk_held=pwk_held+1 WHERE pwk_num=$pwk LIMIT 1");
          if (!$result) {
            echo "Error updating pwkills table entry [2a].{$break}\n";
            exit;
          }
          if ($gkkwtype == 3 && $match->logger == 1) {
            $mwk = findmwk($match->mapnum, $gkkweapon);
            $result = sql_queryn($link, "UPDATE {$dbpre}mwkills SET mwk_kills=mwk_kills+1 WHERE mwk_num=$mwk LIMIT 1");
            if (!$result) {
              echo "Error updating mwkills table entry [2a].{$break}\n";
              exit;
            }
          }
          else {
            $mwk = findmwk($match->mapnum, $gkkweapon);
            $result = sql_queryn($link, "UPDATE {$dbpre}mwkills SET mwk_deaths=mwk_deaths+1 WHERE mwk_num=$mwk LIMIT 1");
            if (!$result) {
              echo "Error updating mwkills table entry [2a].{$break}\n";
              exit;
            }
          }
          $result = sql_queryn($link, "UPDATE {$dbpre}weapons SET wp_frags=wp_frags+1,wp_kills=wp_kills+1 WHERE wp_num=$gkkweapon LIMIT 1");
          if (!$result) {
            echo "Error updating weapons table entry [2a].{$break}\n";
            exit;
          }
          $result = sql_queryn($link, "UPDATE {$dbpre}weapons SET wp_deaths=wp_deaths+1 WHERE wp_num=$gkvweapon LIMIT 1");
          if (!$result) {
            echo "Error updating weapons table entry [2a].{$break}\n";
            exit;
          }
        }
        else {
          // Victim Weapon: suicides+1
          $pwk = findpwk($victim, $gkkweapon);
          $result = sql_queryn($link, "UPDATE {$dbpre}pwkills SET pwk_suicides=pwk_suicides+1 WHERE pwk_num=$pwk LIMIT 1");
          if (!$result) {
            echo "Error updating pwkills table entry [2].{$break}\n";
            exit;
          }
          $mwk = findmwk($match->mapnum, $gkkweapon);
          $result = sql_queryn($link, "UPDATE {$dbpre}mwkills SET mwk_suicides=mwk_suicides+1 WHERE mwk_num=$mwk LIMIT 1");
          if (!$result) {
            echo "Error updating mwkills table entry [2].{$break}\n";
            exit;
          }
          // Killer Weapon Totals: nwsuicides+1
          $result = sql_queryn($link, "UPDATE {$dbpre}weapons SET wp_nwsuicides=wp_nwsuicides+1 WHERE wp_num=$gkkweapon LIMIT 1");
          if (!$result) {
            echo "Error updating weapons table entry [2].{$break}\n";
            exit;
          }
          // Weapon Specific for Game
          if (strtolower($dbtype) == "sqlite")
            $result = sql_queryn($link, "UPDATE temp_wtkills SET wt_suicides=wt_suicides+1 WHERE wt_plr=$gkvictim AND wt_desc='{$weapsg[$gkkweapon]['wp_desc']}'");
          else
            $result = sql_queryn($link, "UPDATE IGNORE temp_wtkills SET wt_suicides=wt_suicides+1 WHERE wt_plr=$gkvictim AND wt_desc='{$weapsg[$gkkweapon]['wp_desc']}'");
          if (!$result)
            echo "Error inserting into temp table for weapon specific per player totals (suicides).{$break}\n";
          }
      }
      else {
        // Killer Weapon: frags+1 / kills+1
        $pwk = findpwk($killer, $gkkweapon);
        $result = sql_queryn($link, "UPDATE {$dbpre}pwkills SET pwk_frags=pwk_frags+1,pwk_kills=pwk_kills+1 WHERE pwk_num=$pwk LIMIT 1");
        if (!$result) {
          echo "Error updating pwkills table entry [3].{$break}\n";
          exit;
        }
        if ($victim < 0 && $match->gametype == 9) { // Log monster's death
          // Killer Weapon: frags+1 / kills+1
          $pwk = findpwk($killer, $gkvweapon);
          $result = sql_queryn($link, "UPDATE {$dbpre}pwkills SET pwk_kills=pwk_kills+1 WHERE pwk_num=$pwk LIMIT 1");
          if (!$result) {
            echo "Error updating pwkills table entry [3a].{$break}\n";
            exit;
          }
        }
        $mwk = findmwk($match->mapnum, $gkkweapon);
        $result = sql_queryn($link, "UPDATE {$dbpre}mwkills SET mwk_kills=mwk_kills+1,mwk_deaths=mwk_deaths+1 WHERE mwk_num=$mwk LIMIT 1");
        if (!$result) {
          echo "Error updating mwkills table entry [3].{$break}\n";
          exit;
        }
        // Victim Weapon: deaths+1
        if ($victim >= 0) {
          $pwk = findpwk($victim, $gkkweapon);
          $result = sql_queryn($link, "UPDATE {$dbpre}pwkills SET pwk_deaths=pwk_deaths+1 WHERE pwk_num=$pwk LIMIT 1");
          if (!$result) {
            echo "Error updating pwkills table entry [4].{$break}\n";
            exit;
          }
          // Victim Held Weapon: held+1
          $pwk = findpwk($victim, $gkvweapon);
          $result = sql_queryn($link, "UPDATE {$dbpre}pwkills SET pwk_held=pwk_held+1 WHERE pwk_num=$pwk LIMIT 1");
          if (!$result) {
            echo "Error updating pwkills table entry [5].{$break}\n";
            exit;
          }
          $mwk = findmwk($match->mapnum, $gkvweapon);
          $result = sql_queryn($link, "UPDATE {$dbpre}mwkills SET mwk_held=mwk_held+1 WHERE mwk_num=$mwk LIMIT 1");
          if (!$result) {
            echo "Error updating mwkills table entry [5].{$break}\n";
            exit;
          }
        }
        else {
          $mwk = findmwk($match->mapnum, $gkvweapon);
          $result = sql_queryn($link, "UPDATE {$dbpre}mwkills SET mwk_deaths=mwk_deaths+1 WHERE mwk_num=$mwk LIMIT 1");
          if (!$result) {
            echo "Error updating mwkills table entry [6].{$break}\n";
            exit;
          }
        }
        // Killer Weapon Totals: frags+1 / kills+1
        $result = sql_queryn($link, "UPDATE {$dbpre}weapons SET wp_frags=wp_frags+1,wp_kills=wp_kills+1 WHERE wp_num=$gkkweapon LIMIT 1");
        if (!$result) {
          echo "Error updating weapons table entry [3].{$break}\n";
          exit;
        }
        // Victim Weapon Totals: deaths+1
        $result = sql_queryn($link, "UPDATE {$dbpre}weapons SET wp_deaths=wp_deaths+1 WHERE wp_num=$gkvweapon LIMIT 1");
        if (!$result) {
          echo "Error updating weapons table entry [4].{$break}\n";
          exit;
        }

        $sweapk = sql_addslashes($weapsg[$gkkweapon]['wp_desc']);
        $sweapv = sql_addslashes($weapsg[$gkvweapon]['wp_desc']);
        // Weapon Specific for Game
        if (strtolower($dbtype) == "sqlite")
          $result = sql_queryn($link, "UPDATE temp_wtkills SET wt_kills=wt_kills+1 WHERE wt_plr=$gkkiller AND wt_desc='$sweapk'");
        else
          $result = sql_queryn($link, "UPDATE IGNORE temp_wtkills SET wt_kills=wt_kills+1 WHERE wt_plr=$gkkiller AND wt_desc='$sweapk'");
        if (!$result)
          echo "Error inserting into temp table for weapon specific per player totals (kills).{$break}\n";
        if ($gkvictim >= 0) {
          if (strtolower($dbtype) == "sqlite")
            $result = sql_queryn($link, "UPDATE temp_wtkills SET wt_deaths=wt_deaths+1 WHERE wt_plr=$gkvictim AND wt_desc='$sweapk'");
          else
            $result = sql_queryn($link, "UPDATE IGNORE temp_wtkills SET wt_deaths=wt_deaths+1 WHERE wt_plr=$gkvictim AND wt_desc='$sweapk'");
          if (!$result)
            echo "Error inserting into temp table for weapon specific per player totals (deaths).{$break}\n";
          if (strtolower($dbtype) == "sqlite")
            $result = sql_queryn($link, "UPDATE temp_wtkills SET wt_held=wt_held+1 WHERE wt_plr=$gkvictim AND wt_desc='$sweapv'");
          else
            $result = sql_queryn($link, "UPDATE IGNORE temp_wtkills SET wt_held=wt_held+1 WHERE wt_plr=$gkvictim AND wt_desc='$sweapv'");
          if (!$result)
            echo "Error inserting into temp table for weapon specific per player totals (held).{$break}\n";
        }
      }
    }
  }

  // Save Individual Score Log
  for ($i = 0; $i < $match->gscount; $i++) {
    list($gsplayer, $gstime, $gsscore, $gsteam) = $gscores[$i];
    if ($gsplayer >= 0 && $player[$gsplayer]->name != "") {
      $result = sql_queryn($link, "INSERT INTO {$dbpre}gscores VALUES ($matchnum,$gsplayer,$gstime,$gsscore,$gsteam)");
      if (!$result) {
        echo "Error saving gscores data.{$break}\n";
        exit;
      }
    }
  }

  // Update Single Game and Career High Weapon Totals
  for ($wpn = 0; $wpn <= $maxweapon; $wpn++) {
  	if (isset($weapsg[$wpn])) {
      for ($i = 0; $i <= $match->maxplayer; $i++) {
        if (isset($player[$i]) && $player[$i]->name != "" && ($config["bothighs"] || !$player[$i]->is_bot())) {
          $pnum = $player[$i]->num;
          $sweap = sql_addslashes($weapsg[$wpn]['wp_desc']);

          // Weapon Single Game Highs
          $result = sql_queryn($link, "SELECT wt_num,wt_intnum,wt_kills,wt_deaths,wt_held,wt_suicides FROM temp_wtkills WHERE wt_plr=$i AND wt_desc='$sweap' LIMIT 1");
          if (!$result) {
            echo "Database error during weapon single game kill highs.{$break}\n";
            exit;
          }
          if (list($wtnum,$wtintnum,$wtkills,$wtdeaths,$wtheld,$wtsuicides) = sql_fetch_row($result)) {
            sql_free_result($result);

            // Kills
            if ($wtkills > $weapsg[$wtintnum]["wp_chkillssg"]) {
              $weapsg[$wtintnum]["wp_chkillssg"] = $wtkills;
              $result = sql_queryn($link, "UPDATE {$dbpre}weapons SET wp_chkillssg=$wtkills,wp_chkillssg_plr=$pnum,wp_chkillssg_tm={$match->length},wp_chkillssg_map={$match->mapnum},wp_chkillssg_dt='$sd' WHERE wp_num=$wtnum LIMIT 1");
              if (!$result) {
                echo "Error saving weapon single game kill highs.{$break}\n";
                exit;
              }
            }

            // Deaths
            if ($wtdeaths > $weapsg[$wtintnum]["wp_chdeathssg"]) {
              $weapsg[$wtintnum]["wp_chdeathssg"] = $wtdeaths;
              $result = sql_queryn($link, "UPDATE {$dbpre}weapons SET wp_chdeathssg=$wtdeaths,wp_chdeathssg_plr=$pnum,wp_chdeathssg_tm={$match->length},wp_chdeathssg_map={$match->mapnum},wp_chdeathssg_dt='$sd' WHERE wp_num=$wtnum LIMIT 1");
              if (!$result) {
                echo "Error saving weapon single game death highs.{$break}\n";
                exit;
              }
            }

            // Suicides
            if ($wtsuicides > $weapsg[$wtintnum]["wp_chsuicidessg"]) {
              $weapsg[$wtintnum]["wp_chsuicidessg"] = $wtsuicides;
              $result = sql_queryn($link, "UPDATE {$dbpre}weapons SET wp_chsuicidessg=$wtsuicides,wp_chsuicidessg_plr=$pnum,wp_chsuicidessg_tm={$match->length},wp_chsuicidessg_map={$match->mapnum},wp_chsuicidessg_dt='$sd' WHERE wp_num=$wtnum LIMIT 1");
              if (!$result) {
                echo "Error saving weapon single game suicide highs.{$break}\n";
                exit;
              }
            }

            // Held
            if ($wtheld > $weapsg[$wtintnum]["wp_chdeathshldsg"]) {
              $weapsg[$wtintnum]["wp_chdeathshldsg"] = $wtheld;
              $result = sql_queryn($link, "UPDATE {$dbpre}weapons SET wp_chdeathshldsg=$wtheld,wp_chdeathshldsg_plr=$pnum,wp_chdeathshldsg_tm={$match->length},wp_chdeathshldsg_map={$match->mapnum},wp_chdeathshldsg_dt='$sd' WHERE wp_num=$wtnum LIMIT 1");
              if (!$result) {
                echo "Error saving weapon single game held death highs.{$break}\n";
                exit;
              }
            }
          }

          // Weapon Career Highs
          if ($plr_matches >= $config["minchmatches"] && $plr_time >= $config["minchtime"]) {
            // Load Player Weapon Kills for current player
            $pwkresult = sql_queryn($link, "SELECT pwk_kills,pwk_deaths,pwk_held,pwk_suicides FROM {$dbpre}pwkills WHERE pwk_player=$pnum AND pwk_weapon=$wpn");
            if (list($pwkkills,$pwkdeaths,$pwkheld,$pwksuicides) = sql_fetch_row($pwkresult)) {
              sql_free_result($pwkresult);
              if ($pwkkills > $weapsg[$wpn]["wp_chkills"]) {
                $weapsg[$wpn]["wp_chkills"] = $pwkkills;
                $pwkresult = sql_queryn($link, "UPDATE {$dbpre}weapons SET wp_chkills=$pwkkills, wp_chkills_plr=$pnum, wp_chkills_gms=$plr_matches, wp_chkills_tm=$plr_time WHERE wp_num=$wpn LIMIT 1");
                if (!$pwkresult) {
                  echo "Error updating weapon entry [1].<br />\n";
                  exit;
                }
              }
              if ($pwkdeaths > $weapsg[$wpn]["wp_chdeaths"]) {
                $weapsg[$wpn]["wp_chdeaths"] = $pwkdeaths;
                $pwkresult = sql_queryn($link, "UPDATE {$dbpre}weapons SET wp_chdeaths=$pwkdeaths, wp_chdeaths_plr=$pnum, wp_chdeaths_gms=$plr_matches, wp_chdeaths_tm=$plr_time WHERE wp_num=$wpn LIMIT 1");
                if (!$pwkresult) {
                  echo "Error updating weapon entry [2].<br />\n";
                  exit;
                }
              }
              if ($pwkheld > $weapsg[$wpn]["wp_chdeathshld"]) {
                $weapsg[$wpn]["wp_chdeathshld"] = $pwkheld;
                $pwkresult = sql_queryn($link, "UPDATE {$dbpre}weapons SET wp_chdeathshld=$pwkheld, wp_chdeathshld_plr=$pnum, wp_chdeathshld_gms=$plr_matches, wp_chdeathshld_tm=$plr_time WHERE wp_num=$wpn LIMIT 1");
                if (!$pwkresult) {
                  echo "Error updating weapon entry [3].<br />\n";
                  exit;
                }
              }
              if ($pwksuicides > $weapsg[$wpn]["wp_chsuicides"]) {
                $weapsg[$wpn]["wp_chsuicides"] = $pwksuicides;
                $pwkresult = sql_queryn($link, "UPDATE {$dbpre}weapons SET wp_chsuicides=$pwksuicides, wp_chsuicides_plr=$pnum, wp_chsuicides_gms=$plr_matches, wp_chsuicides_tm=$plr_time WHERE wp_num=$wpn LIMIT 1");
                if (!$pwkresult) {
                  echo "Error updating weapon entry [4].<br />\n";
                  exit;
                }
              }
            }
            else
              sql_free_result($pwkresult);
          }
        }
      }
    }
  }

  if (strtolower($dbtype) == "sqlite")
    sql_queryn($link, "DROP TABLE temp_wtkills");
  else
    sql_queryn($link, "DROP TABLE IF EXISTS temp_wtkills");

  // Save Pickups Data
  for ($itm = 1; $itm <= $match->maxpickups; $itm++) {
    // Save for each player into match and player by type
    for ($i = 0; $i <= $match->maxplayer; $i++) {
      if (isset($player[$i]) && $player[$i]->name != "") {
        if (isset($pickups[$i][$itm]))
          $num = $pickups[$i][$itm];
        else
          $num = 0;
        if ($num) {
          // Save Match Pickups by Player
          $result = sql_queryn($link, "INSERT INTO {$dbpre}gitems VALUES ($matchnum,$itm,$i,$num)");
          if (!$result) {
            echo "Error saving gitems data.{$break}\n";
            exit;
          }

          // Save Player Pickup Totals
          $pnum = $player[$i]->num;
          $result = sql_queryn($link, "SELECT pi_pickups FROM {$dbpre}pitems WHERE pi_plr=$pnum AND pi_item=$itm LIMIT 1");
          if (!$result) {
            echo "Error reading pitems data.{$break}\n";
            exit;
          }
          $row = sql_fetch_row($result);
          sql_free_result($result);
          if ($row) {
            $result = sql_queryn($link, "UPDATE {$dbpre}pitems SET pi_pickups=pi_pickups+$num WHERE pi_plr=$pnum AND pi_item=$itm LIMIT 1");
            if (!$result) {
              echo "Error updating pitems data.{$break}\n";
              exit;
            }
          }
          else {
            $result = sql_queryn($link, "INSERT INTO {$dbpre}pitems VALUES ($pnum,$itm,$num)");
            if (!$result) {
              echo "Error inserting pitems data.{$break}\n";
              exit;
            }
          }

          // Save Item Totals
          $result = sql_queryn($link, "UPDATE {$dbpre}items SET it_pickups=it_pickups+$num WHERE it_num=$itm LIMIT 1");
          if (!$result) {
            echo "Error updating items data.{$break}\n";
            exit;
          }
        }
      }
    }
  }

  // Save Events Data
  for ($i = 0; $i < $match->numevents; $i++) {
    list($geplr, $gevent, $getime, $gelength, $gequant, $gereason, $geopponent, $geitem) = $events[$i];
    $result = sql_queryn($link, "INSERT INTO {$dbpre}gevents VALUES (NULL,$matchnum,$geplr,$gevent,$getime,$gelength,$gequant,$gereason,$geopponent,$geitem)");
    if (!$result) {
      echo "Error saving events data.{$break}\n";
      exit;
    }

    // Save Assault Objectives
    if ($gevent == 7) {
    	$result = sql_queryn($link, "SELECT obj_times,obj_besttime,obj_avgtime FROM {$dbpre}objectives WHERE obj_num=$gequant LIMIT 1");
    	list($obj_times,$obj_besttime,$obj_avgtime) = sql_fetch_row($result);
    	sql_free_result($result);
    	if ($gelength < $obj_besttime || !$obj_besttime)
    	  $obj_besttime = $gelength;
    	$newtime = ((floatval($obj_avgtime) * floatval($obj_times)) + floatval($gelength)) / (floatval($obj_times) + 1.0);
    	$obj_times++;
      $result = sql_queryn($link, "UPDATE {$dbpre}objectives SET obj_times=$obj_times,obj_besttime=$obj_besttime,obj_avgtime=$newtime WHERE obj_num=$gequant LIMIT 1");
    }

    // Save Connection Data
    if ($gevent == 2) {
      $tstamp = $match->matchdate + ($getime / 100);
      if (!$gereason) {
        $result = sql_queryn($link, "SELECT cn_match FROM {$dbpre}connections WHERE cn_match=$matchnum AND cn_pnum=$geplr");
        $row = sql_fetch_row($result);
        sql_free_result($result);
        if (!$row)
          sql_queryn($link, "INSERT INTO {$dbpre}connections VALUES ($matchnum,$geplr,FROM_UNIXTIME($tstamp),0)");
      }
      else
        sql_queryn($link, "UPDATE {$dbpre}connections SET cn_ctime=cn_ctime,cn_dtime=FROM_UNIXTIME($tstamp) WHERE cn_match=$matchnum AND cn_pnum=$geplr");
    }
  }

  // Update Connection Data disconnect time
  $tstamp = $match->matchdate + ($match->endtime / 100);
  sql_queryn($link, "UPDATE {$dbpre}connections SET cn_ctime=cn_ctime,cn_dtime=FROM_UNIXTIME($tstamp) WHERE cn_match=$matchnum AND cn_dtime=0");

  // Save Team Data
  if ($match->tkcount > 0) {
    for ($i = 0; $i < $match->tkcount; $i++) {
      list($tnum, $tscore, $ttime) = $tkills[$i];
      $result = sql_queryn($link, "INSERT INTO {$dbpre}tkills VALUES ($matchnum,$tnum,$tscore,$ttime)");
      if (!$result) {
        echo "Error saving tkills data.{$break}\n";
        exit;
      }
    }
  }

  // Update Game Type Data
  $result = sql_queryn($link, "UPDATE {$dbpre}type SET tp_played=tp_played+1,tp_gtime=tp_gtime+{$match->length},tp_ptime=tp_ptime+$tot_ptime,tp_score=tp_score+{$match->tot_score},tp_kills=tp_kills+{$match->tot_kills},tp_deaths=tp_deaths+{$match->tot_deaths},tp_suicides=tp_suicides+{$match->tot_suicides},tp_teamkills=tp_teamkills+{$match->teamkills} WHERE tp_num={$match->gametnum} LIMIT 1");
  if (!$result) {
    echo "Error saving tkills data.{$break}\n";
    exit;
  }

  // Save Chatlog
  for ($i = 0; $i < $match->numchat; $i++) {
    list($gcplr, $gcteam, $gctime, $gctext) = $chatlog[$i];
    $gctext = sql_addslashes($gctext);
    $result = sql_queryn($link, "INSERT INTO {$dbpre}gchat VALUES (NULL,$matchnum,$gcplr,$gcteam,$gctime,'$gctext')");
    if (!$result) {
      echo "Error saving chat data.{$break}\n";
      exit;
    }
  }

  $status = $matchnum;

  // Update Match Status
  $result = sql_queryn($link, "UPDATE {$dbpre}matches SET gm_status=1 WHERE gm_num=$matchnum");

  return $status;
}

?>