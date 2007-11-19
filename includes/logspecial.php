<?php

/*
    UTStatsDB
    Copyright (C) 2002-2007  Patrick Contreras / Paul Gallier

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

if (preg_match("/logspecial.php/i", $_SERVER["PHP_SELF"])) {
  echo "Access denied.\n";
  die();
}

function add_player($time, $plr)
{
  global $player, $spree, $multi, $tchange, $assist, $relog, $flagstatus, $killmatch;

  if ($plr >= 0) {
    $name = "Player $plr";

    $player[$plr] = new Player;
    $player[$plr]->plr = $plr;
    $player[$plr]->name = $name;
    $player[$plr]->starttime = $time;

    $spree[$plr][0] = 0; // Start Time
    $spree[$plr][1] = 0; // Kills
    $multi[$plr][0] = 0; // Start Time
    $multi[$plr][1] = 0; // Kills
    $multi[$plr][2] = 0; // Last Kill Time
    $tchange[$plr] = 0;  // Team Change Tracking
    $assist[$plr] = 0;   // Assists
    $relog[$plr] = -1;   // Relog tracking
    $flagstatus[$plr] = 0; // Flag status tracking
  }
  else {
    $name = "";

    $player = array();

    $spree = array(array());
    $multi = array(array());
    $tchange = array();
    $assist = array();
    $relog = array();
    $flagstatus = array();
    $killmatch = array(array());
  }
}

function set_name($plr, $name)
{
  global $player;

  $bot = 0;
  if (substr($name, 0, 5) == "[BOT]") {
    $name = substr($name, 5);
    $bot = true;
  }
  $player[$plr]->name = $name;
  $player[$plr]->bot = $bot;
  $player[$plr]->named = true;
}

function clear_player($time, $plr)
{
  global $player, $spree, $multi, $tchange, $assist, $relog, $flagstatus, $killmatch;

  if ($plr < 0)
    return;

  $player[$plr]->ranks = 0;
  $player[$plr]->rankc = 0;
  $player[$plr]->suicr = 0;
  $player[$plr]->kills = array(0, 0, 0, 0);
  $player[$plr]->deaths = array(0, 0, 0, 0);
  $player[$plr]->suicides = array(0, 0, 0, 0);
  $player[$plr]->starttime = $time;
  $player[$plr]->headshots = 0;
  $player[$plr]->firstblood = 0;
  $player[$plr]->multi1 = array(0, 0, 0, 0, 0, 0, 0);
  $player[$plr]->spree = array(0, 0, 0, 0, 0, 0);
  $player[$plr]->spreet = array(0, 0, 0, 0, 0, 0);
  $player[$plr]->spreek = array(0, 0, 0, 0, 0, 0);
  $player[$plr]->combo = array(0, 0, 0, 0);
  $player[$plr]->totaltime = array(0, 0, 0, 0);
  $player[$plr]->tscore = array(0.0, 0.0, 0.0, 0.0);
  $player[$plr]->teamkills = array(0, 0, 0, 0);
  $player[$plr]->teamdeaths = array(0, 0, 0, 0);
  $player[$plr]->pickup = array(0, 0, 0, 0);
  $player[$plr]->taken = array(0, 0, 0, 0);
  $player[$plr]->dropped = array(0, 0, 0, 0);
  $player[$plr]->assist = array(0, 0, 0, 0);
  $player[$plr]->typekill = array(0, 0, 0, 0);
  $player[$plr]->return = array(0, 0, 0, 0);
  $player[$plr]->capcarry = array(0, 0, 0, 0);
  $player[$plr]->tossed = array(0, 0, 0, 0);
  $player[$plr]->holdtime = array(0, 0, 0, 0);
  $player[$plr]->transgib = 0;
  $player[$plr]->headhunter = 0;
  $player[$plr]->flakkills = 0;
  $player[$plr]->flakmonkey = 0;
  $player[$plr]->combokills = 0;
  $player[$plr]->combowhore = 0;
  $player[$plr]->roadrampage = 0;
  $player[$plr]->carjack = 0;
  $player[$plr]->roadkills = 0;
  $player[$plr]->rank = 0;
  $player[$plr]->num = 0;
  $player[$plr]->extraa = array(0, 0, 0, 0);
  $player[$plr]->extrab = array(0, 0, 0, 0);
  $player[$plr]->extrac = array(0, 0, 0, 0);

  $spree[$plr][0] = 0; // Start Time
  $spree[$plr][1] = 0; // Kills
  $multi[$plr][0] = 0; // Start Time
  $multi[$plr][1] = 0; // Kills
  $multi[$plr][2] = 0; // Last Kill Time
  $tchange[$plr] = 0;  // Team Change Tracking
  $assist[$plr] = 0;   // Assists
  $flagstatus[$plr] = 0; // Flag status tracking
}

function parseline(&$line, &$param)
{
  $ok = TRUE;
  if (!strlen($line))
    $ok = FALSE;
  else {
    $loc = strpos($line, "\t");
    if ($loc === FALSE) {
      $param = $line;
      $line = "";
    }
    else {
      if ($loc > 0)
        $param = substr($line, 0, $loc);
      else
        $param = "";
      $line = substr($line, $loc + 1);
    }
    if (strlen($param) > 1536)
      $param = substr($param, 0, 1535);
  }

  return $ok;
}

function parseserverdata(&$line, &$param, &$val)
{
  $ok = TRUE;
  if (strlen($line) == 0)
    $ok = FALSE;
  else {
    $loc = strpos($line, "\\");
    if ($loc === FALSE)
      $ok = FALSE;
    else {
      $line = substr($line, $loc + 1);
      $loc = strpos($line, "\\");
      if ($loc === FALSE)
        $ok = FALSE;
      else {
        $param = substr($line, 0, $loc);
        $line = substr($line, $loc + 1);
        $loc = strpos($line, "\\");
        if ($loc === FALSE) {
          $val = $line;
          $line = "";
        }
        else {
          $val = substr($line, 0, $loc);
          $line = substr($line, $loc);
        }
      }
    }
  }

  return $ok;
}

function check_player($plr)
{
  global $player, $relog;

  // Verify numeric values
  if (!is_numeric($plr))
    $plr = -1;
  else
    $plr = intval($plr);

  // Check for relogged player
  if ($plr >= 0 && isset($relog[$plr]) && $relog[$plr] >= 0)
    $plr = $relog[$plr];

  // Check for unlogged connection
  if ($plr >= 0 && !isset($player[$plr]))
    $plr = -99;

  return $plr;
}

function ctime($tm)
{
  return intval(floatval($tm) * 100);
}

function get_weapon($weapon, $monster)
{
  global $link, $dbpre, $match, $config, $break;

  // Remove custom prefixes
  if (strlen($weapon) > 8 && strtolower(substr($weapon, 0, 8)) == "ut2vweap")
    $weapon = substr($weapon, 8);
  else if (strlen($weapon) > 7 && substr($weapon, 0, 7) == "OLTeams")
    $weapon = substr($weapon, 7);
  else if (strlen($weapon) > 7 && substr($weapon, 0, 7) == "NewNet_") // NewNet
    $weapon = substr($weapon, 7);
  else if (strlen($weapon) > 3 && substr($weapon, 0, 3) == "BS_") // UTComp
    $weapon = substr($weapon, 3);
  else if (strlen($weapon) > 5 && substr($weapon, -5) == "_3SPN") // Team Arenamaster
    $weapon = substr($weapon, 0, -5);

  // Convert improper TAM names
  if ($weapon == "DamType_FlakChunk")
    $weapon = "DamTypeFlakChunk";
  else if ($weapon == "DamType_FlakShell")
    $weapon = "DamTypeFlakShell";
  else if ($weapon == "DamType_ShockCombo")
    $weapon = "DamTypeShockCombo";
  else if ($weapon == "DamType_Headshot")
    $weapon = "DamTypeHeadshot";

  if ($match->uttype == 1 && $config["ut99weapons"])
    $weapon = "UT99 ".$weapon;

  $weapons = sql_addslashes($weapon);
  $result = sql_queryn($link, "SELECT wp_num,wp_weaptype,wp_secondary FROM {$dbpre}weapons WHERE wp_type='$weapons' LIMIT 1");
  if (!$result) {
    echo "Error reading weapons table.{$break}\n";
    die();
  }
  if ($row = sql_fetch_row($result)) {
    $weaponnum = $row[0];
    $weaptype = $row[1];
    $weapsec = $row[2];
    sql_free_result($result);
  }
  else { // Add new weapon
    sql_free_result($result);
    if ($monster)
      $result = sql_queryn($link, "INSERT INTO {$dbpre}weapons (wp_type,wp_desc,wp_weaptype) VALUES('$weapons','$weapons',3)");
    else
      $result = sql_queryn($link, "INSERT INTO {$dbpre}weapons (wp_type,wp_desc) VALUES('$weapons','$weapons')");
    if (!$result) {
      echo "Error adding new weapon.{$break}\n";
      die();
    }
    $weaponnum = sql_insert_id($link);
    $weaptype = $weapsec = 0;
  }
  $ret = array($weaponnum,$weaptype,$weapsec);
  return $ret;
}

function pwa_add($plr, $weaponnum, $fired, $hit, $damage)
{
  global $pwastats;

  if ($plr < 0 || !$weaponnum)
    return;

  if (!isset($pwastats[$plr][$weaponnum])) {
    if ($fired > 0)
      $pwastats[$plr][$weaponnum][0] = $fired;
    else
      $pwastats[$plr][$weaponnum][0] = 0;
    if ($hit > 0)
      $pwastats[$plr][$weaponnum][1] = $hit;
    else
      $pwastats[$plr][$weaponnum][1] = 0;
    if ($damage > 0)
      $pwastats[$plr][$weaponnum][2] = $damage;
    else
      $pwastats[$plr][$weaponnum][2] = 0;
  }
  else {
    if ($fired > 0)
      $pwastats[$plr][$weaponnum][0] += $fired;
    if ($hit > 0)
      $pwastats[$plr][$weaponnum][1] += $hit;
    if ($damage > 0)
      $pwastats[$plr][$weaponnum][2] += $damage;
  }
}

function bot_add($bot,$skill,$alertness,$accuracy,$aggressive,$strafing,$style,$tactics,$transloc,$reaction,$jumpiness,$favorite)
{
  global $botstats;

  $botstats[$bot][0] = $skill;
  $botstats[$bot][1] = $alertness;
  $botstats[$bot][2] = $accuracy;
  $botstats[$bot][3] = $aggressive;
  $botstats[$bot][4] = $strafing;
  $botstats[$bot][5] = $style;
  $botstats[$bot][6] = $tactics;
  $botstats[$bot][7] = $transloc;
  $botstats[$bot][8] = $reaction;
  $botstats[$bot][9] = $jumpiness;
  $botstats[$bot][10] = $favorite;
}

function endspree($plr, $time, $reason, $weapon, $opponent)
{
  global $player, $spree, $events, $match;

  // Reason: 0=Game Ended, 1=Killed by Player, 2=Suicided, 3=Environmental Death,
  //         4=Disconnected, 5=Team Killed, 6=Team Change
  if ($plr < 0)
    return;
  $num = $spree[$plr][1];
  if ($num) {
    $length = $time - $spree[$plr][0];
    $spreelev = 0;

    if ($num >= 5 && $num < 10)
      $spreelev = 1;
    else if ($num >= 10 && $num < 15)
      $spreelev = 2;
    else if ($num >= 15 && $num < 20)
      $spreelev = 3;
    else if ($num >= 20 && $num < 25)
      $spreelev = 4;
    else if ($num >= 25 && $num < 30)
      $spreelev = 5;
    else if ($num >= 30)
      $spreelev = 6;

    if ($spreelev > 0) {
      $player[$plr]->spree[$spreelev - 1]++;
      $player[$plr]->spreet[$spreelev - 1] += $length;
      $player[$plr]->spreek[$spreelev - 1] += $num;
    }

    $spree[$plr][0] = 0;
    $spree[$plr][1] = 0;

    if ($num >= 5) {
      $events[$match->numevents][0] = $plr;      // Player
      $events[$match->numevents][1] = 1;         // Event
      $events[$match->numevents][2] = $time;     // Time
      $events[$match->numevents][3] = $length;   // Length
      $events[$match->numevents][4] = $num;      // Quant
      $events[$match->numevents][5] = $reason;   // Reason
      $events[$match->numevents][6] = $opponent; // Opponent
      $events[$match->numevents++][7] = $weapon; // Item
    }
  }
}

function endmulti($plr, $time)
{
  global $player, $multi, $stattype, $multicheck;

  if ($multicheck && $multi[$plr][1] && $stattype != 1) {
    switch ($multi[$plr][1]) {
      case 1:
        break;
      case 2:
        $player[$plr]->multi[0]++;
        break;
      case 3:
        $player[$plr]->multi[1]++;
        break;
      case 4:
        $player[$plr]->multi[2]++;
        break;
      case 5:
        $player[$plr]->multi[3]++;
        break;
      case 6:
        $player[$plr]->multi[4]++;
        break;
      case 7:
        $player[$plr]->multi[5]++;
        break;
      default:
        $player[$plr]->multi[6]++;
    }
  }
  $multi[$plr][0] = 0;
  $multi[$plr][1] = 0;
  $multi[$plr][2] = 0;
}

function flag_check($plr, $time, $set) // Set = 1 on pickup, 0 on drop
{
  global $player, $flagstatus;

  if ($plr < 0)
    $return;

  if ($set) {
    if (!$flagstatus[$plr])
      $flagstatus[$plr] = $time;
  }
  else {
    if ($flagstatus[$plr]) {
      if ($player[$plr]->team >= 0 && $player[$plr]->team <= 3)
        $player[$plr]->holdtime[$player[$plr]->team] += $time - $flagstatus[$plr];
      $flagstatus[$plr] = 0;
    }
  }
  return;
}

function connections($plr, $time, $reason)
{
  global $events, $match;

  // Reason: 0 = Connect / 1 = Disconnect
  $events[$match->numevents][0] = intval($plr);    // Player
  $events[$match->numevents][1] = 2;               // Event
  $events[$match->numevents][2] = intval($time);   // Time
  $events[$match->numevents][3] = 0;               // Length
  $events[$match->numevents][4] = 0;               // Quant
  $events[$match->numevents][5] = intval($reason); // Reason
  $events[$match->numevents][6] = 0;               // Opponent
  $events[$match->numevents++][7] = 0;             // Item
}

function gameevent($time, $reason)
{
  global $events, $match;

  // Reason: 0 = Match Start / 1 = Match End
  $events[$match->numevents][0] = 0;               // Player
  $events[$match->numevents][1] = 3;               // Event
  $events[$match->numevents][2] = intval($time);   // Time
  $events[$match->numevents][3] = 0;               // Length
  $events[$match->numevents][4] = 0;               // Quant
  $events[$match->numevents][5] = intval($reason); // Reason
  $events[$match->numevents][6] = 0;               // Opponent
  $events[$match->numevents++][7] = 0;             // Item
}

function teamchange($time, $plr, $team)
{
  global $events, $match, $tchange;

  $events[$match->numevents][0] = intval($plr);  // Player
  $events[$match->numevents][1] = 4;             // Event
  $events[$match->numevents][2] = intval($time); // Time
  $events[$match->numevents][3] = 0;             // Length
  $events[$match->numevents][4] = intval($team); // Quant
  $events[$match->numevents][5] = 0;             // Reason
  $events[$match->numevents][6] = 0;             // Opponent
  $events[$match->numevents++][7] = 0;           // Item

  $tchange[$plr] = $time;
}

function teamscore($time, $tm, $score, $reason)
{
  global $events, $match, $tkills;

  $events[$match->numevents][0] = intval($tm);     // Player
  $events[$match->numevents][1] = 5;               // Event
  $events[$match->numevents][2] = intval($time);   // Time
  $events[$match->numevents][3] = 0;               // Length
  $events[$match->numevents][4] = intval($score);  // Quant
  $events[$match->numevents][5] = intval($reason); // Reason
  $events[$match->numevents][6] = 0;               // Opponent
  $events[$match->numevents++][7] = 0;             // Item

  $match->team[$tm] += floatval($score);
  $tkills[$match->tkcount][0] = intval($tm);     // Team number
  $tkills[$match->tkcount][1] = intval($score);  // Score
  $tkills[$match->tkcount++][2] = intval($time); // Time
}

function weaponspecial($time, $plr, $reason, $item)
{
  global $events, $match;

  // Reason: 1 = Headhunter / 2 = Flak Monkey / 3 = Carjack / 4 = Combo Whore / 5 = Road Rampage
  $events[$match->numevents][0] = intval($plr);    // Player
  $events[$match->numevents][1] = 6;               // Event
  $events[$match->numevents][2] = intval($time);   // Time
  $events[$match->numevents][3] = 0;               // Length
  $events[$match->numevents][4] = 0;               // Quant
  $events[$match->numevents][5] = intval($reason); // Reason
  $events[$match->numevents][6] = 0;               // Opponent
  $events[$match->numevents++][7] = $item;         // Item
}

function objective($time, $team, $obj, $length)
{
  global $events, $match;

  $events[$match->numevents][0] = intval($team);   // Team (Red=1/Blue=2)
  $events[$match->numevents][1] = 7;               // Event
  $events[$match->numevents][2] = intval($time);   // Time
  $events[$match->numevents][3] = intval($length); // Length
  $events[$match->numevents][4] = intval($obj);    // Objective
  $events[$match->numevents][5] = 0;
  $events[$match->numevents][6] = 0;
  $events[$match->numevents++][7] = 0;
}

function assault_round($time, $attack, $team, $length)
{
  global $events, $match;

  $events[$match->numevents][0] = intval($team);   // Team (Red=1/Blue=2)
  $events[$match->numevents][1] = 8;               // Event
  $events[$match->numevents][2] = intval($time);   // Time
  $events[$match->numevents][3] = intval($length); // Length
  $events[$match->numevents][4] = 0;
  $events[$match->numevents][5] = intval($attack); // Reason (0=Defend / 1=Attack / 2=RoundPair Winner)
  $events[$match->numevents][6] = 0;
  $events[$match->numevents++][7] = 0;
}

function invasion_wave($time, $wave)
{
  global $events, $match;

  $events[$match->numevents][0] = intval($wave); // Wave
  $events[$match->numevents][1] = 9;             // Event
  $events[$match->numevents][2] = intval($time); // Time
  $events[$match->numevents][3] = 0;
  $events[$match->numevents][4] = 0;
  $events[$match->numevents][5] = 0;
  $events[$match->numevents][6] = 0;
  $events[$match->numevents++][7] = 0;
}

function map_vote($time, $plr, $tgt, $votes, $reason)
{
  global $link, $dbpre, $events, $match, $break;

  // Reason: 1 = mapvote event / 2 = map vote / 3 = kick event / 4 = kick vote / 5 = gamevote events / 6 = gametype vote
  switch ($reason) {
    case 1:
    case 2:
    case 5:
    case 6:
      $tgt = sql_addslashes($tgt);
      $result = sql_queryn($link, "SELECT ed_num FROM {$dbpre}eventdesc WHERE ed_desc='$tgt' LIMIT 1");
      if (!$result) {
        echo "Error accessing event descriptions database.{$break}\n";
        exit;
      }
      $row = sql_fetch_row($result);
      sql_free_result($result);
      if ($row)
        $target = intval($row[0]);
      else {
        // Add new event description
        $result = sql_queryn($link, "INSERT INTO {$dbpre}eventdesc (ed_desc) VALUES('$tgt')");
        if (!$result) {
          echo "Error saving new event description in database.{$break}\n";
          exit;
        }
        $target = sql_insert_id($link);
      }
      break;
    case 3:
    case 4:
      $target = check_player($tgt);
      if ($target < -1)
        return;
      break;
    default:
      return;
  }
  $events[$match->numevents][0] = intval($plr);     // Player
  $events[$match->numevents][1] = 10;               // Event
  $events[$match->numevents][2] = intval($time);    // Time
  $events[$match->numevents][3] = 0;
  $events[$match->numevents][4] = $target;          // Map / Kick Player
  $events[$match->numevents][5] = intval($reason);  // Reason
  $events[$match->numevents][6] = 0;
  $events[$match->numevents++][7] = intval($votes); // Votes
}

function mutant($time, $plr, $mut)
{
  global $events, $match, $player, $mutantstat;

  if ($mut && $mutantstat[0] >= 0)
    $player[$mutantstat[0]]->holdtime[0] += $time - $mutantstat[1];

  if ($mut && $plr >= 0) {
    $player[$plr]->pickup[0]++;
    $mutantstat[0] = intval($plr);
    $mutantstat[1] = intval($time);
  }

  $events[$match->numevents][0] = intval($plr);  // Player
  $events[$match->numevents][1] = 11;            // Event
  $events[$match->numevents][2] = intval($time); // Time
  $events[$match->numevents][3] = 0;
  $events[$match->numevents][4] = 0;
  $events[$match->numevents][5] = intval($mut);  // 1=Mutant / 0=Bottom Feeder
  $events[$match->numevents][6] = 0;
  $events[$match->numevents++][7] = 0;
}

function lmsout($time, $plr, $last)
{
  global $events, $match, $player, $lms;

  $player[$plr]->lms = ++$lms;
  $events[$match->numevents][0] = intval($plr);  // Player
  $events[$match->numevents][1] = 12;            // Event
  $events[$match->numevents][2] = intval($time); // Time
  $events[$match->numevents][3] = 0;
  $events[$match->numevents][4] = 0;
  $events[$match->numevents][5] = intval($last);
  $events[$match->numevents][6] = 0;
  $events[$match->numevents++][7] = 0;
}

function pointcap($time, $plr, $point)
{
  global $events, $match;

  $events[$match->numevents][0] = intval($plr);  // Player
  $events[$match->numevents][1] = 13;            // Event
  $events[$match->numevents][2] = intval($time); // Time
  $events[$match->numevents][3] = 0;
  $events[$match->numevents][4] = 0;
  $events[$match->numevents][5] = intval($point);
  $events[$match->numevents][6] = 0;
  $events[$match->numevents++][7] = 0;
}

function teamevent($time, $reason, $team, $opp)
{
  global $events, $match;

  // Reason: 0 = Flag Capture
  $events[$match->numevents][0] = intval($team);   // Team
  $events[$match->numevents][1] = 14;              // Event
  $events[$match->numevents][2] = intval($time);   // Time
  $events[$match->numevents][3] = 0;
  $events[$match->numevents][4] = 0;
  $events[$match->numevents][5] = intval($reason); // Reason
  $events[$match->numevents][6] = intval($opp);    // Opponent
  $events[$match->numevents++][7] = 0;             // Item
}

?>