<?php

/*
    UTStatsDB
    Copyright (C) 2002-2010  Patrick Contreras / Paul Gallier

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

if (preg_match("/logparse.php/i", $_SERVER["PHP_SELF"])) {
  echo "Access denied.\n";
  die();
}

require("logclasses.php");
require("logspecial.php");
require("logmatchevents.php");
require("logplrevents.php");
require("loggameevents.php");
require("logkillevents.php");
require("logscoreevents.php");
require("logutevents.php");

function parselog($file,$chatfile)
{
  global $link, $dbtype, $dbpre, $mysqlverh, $mysqlverl, $config, $server, $player, $match;
  global $events, $pickups, $gkills, $gscores, $tkills, $chatlog, $safemode;
  global $spree, $multi, $tchange, $uselimit, $assist, $relog, $pwastats;
  global $stattype, $nohtml, $flagstatus, $killmatch, $mutantstat, $lms;
  global $special, $numspec, $specialtypes, $numspectypes;

  if ($nohtml)
    $break = "";
  else
    $break = "<br />";

  $match = new Match;

  $relog = array();
  $botstats = array(array());
  $player = array();
  $pickups = array();
  $mutantstat = array(-1, 0);
  $lms = 0;

  add_player(0, -1); // Set player array
  $chatlog = array();
  $assist = array();
  $pwastats = array(array(array()));

  // Check database version
  $uselimit = 0;
  if (strtolower($dbtype) == "mysql" && ($mysqlverh > 3 || ($mysqlverh == 3 && $mysqlverl >= 23)))
    $uselimit = 1;

  // Load Special Events
  $result = sql_queryn($link, "SELECT se_num,se_title,se_trigtype,se_trignum FROM {$dbpre}special ORDER BY se_num");
  if (!$result) {
    echo "Special event database error.<br />\n";
    exit;
  }
  $numspec = 0;
  $special = array(array ());
  while ($row = sql_fetch_row($result)) {
    $special[$numspec][0] = intval($row[0]);
    $special[$numspec][1] = $row[1];
    $special[$numspec][2] = intval($row[2]);
    if ($special[$numspec][2] == 2)
      $match->roadkill = $numspec;
    if ($special[$numspec][2] == 3)
      $match->roadrampage = $numspec;
    $special[$numspec++][3] = intval($row[3]);
  }
  sql_free_result($result);
  $result = sql_queryn($link, "SELECT st_type,st_snum FROM {$dbpre}specialtypes");
  if (!$result) {
    echo "Special event type database error.<br />\n";
    exit;
  }
  $numspectypes = 0;
  $specialtypes = array(array());
  while ($row = sql_fetch_row($result)) {
    $specialtypes[$numspectypes][0] = $row[0];
    $specialtypes[$numspectypes++][1] = intval($row[1]);
  }
  sql_free_result($result);

  $numlogfiles = $chatfile != "" ? 2 : 1;
  for ($logfiles = 0; $logfiles < $numlogfiles; $logfiles++) {

  if ($logfiles == 0) {
    if (!($fp = fopen($file, "r"))) {
      echo "Error opening log!{$break}\n";
      return -1;
    }
  }
  else {
    if (!($fp = fopen($chatfile, "r"))) {
      echo "Error opening chat log!{$break}\n";
      $fp = null;
    }
  }

  $line_num = $stf = $utf16 = $utfcheck = 0;
  while ($fp != null && !feof($fp) && $line = fgets($fp, 1536)) {
    if (!$safemode)
      set_time_limit($config["php_timelimit"]); // Reset script timeout counter

    if (!$utfcheck) {
      if (ord($line[1]) == 0 && ord($line[3]) == 0 && ord($line[5]) == 0)
        $utf16 = 1;
      $utfcheck = 1;
    }
    if ($utf16) {
      $line2 = "";
      for ($i = $stf; $i < strlen($line); $i += 2)
        $line2.=$line[$i];
      $line = $line2;
      $stf = 1;
    }

    $line_num++;
    $i = 0;
    while (parseline($line, $param))
      $data[$i++] = trim($param);
    if ($line_num == 1 && $i == 1) {
      if (substr($data[0], 0, 7) == "OLStats")
        $match->logger = 1;
      if (substr($data[0], 0, 8) == "UT3Stats")
        $match->logger = 2;
    }
    if ($i > 1) {
      $tt = strtoupper($data[1]);
      switch ($tt) {
        case "NG": // New Game
          tag_ng($i, $data);
          break;
        case "SI": // Server Info
          if (tag_si($i, $data))
            return $match->ended;
          break;
        case "SG": // Start Game
          tag_sg($i, $data);
          break;
        case "C": // Player connect - playernumber / playername | playernumber / cd-key hash / id name, id pass hash
          tag_c($i, $data);
          break;
        case "D": // Player disconnect
          tag_d($i, $data);
          break;
        case "PS": // Player Info
          tag_ps($i, $data);
          break;
        case "PP": // Player Ping
          tag_pp($i, $data);
          break;
        case "PA": // Player Accuracy
          tag_pa($i, $data);
          break;
        case "BI": // Bot Info
          tag_bi($i, $data);
          break;
        case "G": // Game event
          tag_g($i, $data);
          break;
        case "P": // Special event - player / event
          tag_p($i, $data);
          break;
        case "K": // Kill - killer / damagetype / victim / victimweapon
          tag_k($i, $data);
          break;
        case "TK": // Team Kill (teammate kill)
          tag_tk($i, $data);
          break;
        case "S": // Score
          tag_s($i, $data);
          break;
        case "T": // Team score
          tag_t($i, $data);
          break;
        case "MK": // Monster Kill (monster kills player)
          tag_mk($i, $data);
          break;
        case "MD": // Monster Death (player kills monster)
          tag_md($i, $data);
          break;
        case "EG": // End Game
          tag_eg($i, $data);
          break;
        case "I": // Item Pickup (2 = Player / 3 = Item)
          tag_i($i, $data);
          break;
        case "V": // Chat
          tag_v($i, $data);
          break;
        case "TV": // Team Chat
        case "VT":
          tag_tv($i, $data);
          break;
        case "MV": // Map Vote
          tag_mv($i, $data);
          break;
        case "KV": // Kick Vote
          tag_kv($i, $data);
          break;
        case "GV": // Game Vote
          tag_gv($i, $data);
          break;

        // Unreal Tournament '99 Events
        case "INFO":
          tagut_info($i, $data);
          break;
        case "MAP":
          tagut_map($i, $data);
          break;
        case "GAME":
          if (tagut_game($i, $data))
            return $match->ended;
          break;
        case "PLAYER":
          tagut_player($i, $data);
          break;
        case "GAME_START":
        case "REALSTART":
          tagut_game_start($i, $data);
          break;
        case "ITEM_GET":
          tagut_item_get($i, $data);
          break;
        case "KILL":
          tagut_kill(0, $i, $data);
          break;
        case "TEAMKILL": // Bug causes teamkills in non-teamgames
          tagut_kill(1, $i, $data);
          break;
        case "SUICIDE": // 2	Pulse Gun	Fell	None
          tagut_suicide($i, $data);
          break;
        case "HEADSHOT":
          tagut_headshot($i, $data);
          break;
        case "FLAG_TAKEN":
          tagut_flag_taken($i, $data);
          break;
        case "FLAG_DROPPED":
          tagut_flag_dropped($i, $data);
          break;
        case "FLAG_PICKEDUP":
          tagut_flag_pickedup($i, $data);
          break;
        case "FLAG_RETURNED":
        case "FLAG_RETURNED_MID":
        case "FLAG_RETURN_CLOSESAVE":
        case "FLAG_RETURN_BASE":
        case "FLAG_RETURN_ENEMYBASE":
          tagut_flag_returned($i, $data);
          break;
        case "FLAG_RETURNED_TIMEOUT":
          tagut_flag_returned_timeout($i, $data);
          break;
        case "FLAG_CAPTURED":
          tagut_flag_captured($i, $data);
          break;
        case "FLAG_COVER":
          break;
        case "THROW_TRANSLOCATOR":
          tagut_throw_translocator($i, $data);
          break;
        case "TRANSLOCATE":
          tagut_translocate($i, $data);
          break;
        case "TRANSLOCATE_FAIL":
          tagut_translocate_fail($i, $data);
          break;
        case "ITEM_ACTIVATE": // Damage Amplifier	5
          tagut_item_activate($i, $data);
          break;
        case "ITEM_DEACTIVATE":
          tagut_item_deactivate($i, $data);
          break;
        case "ASSAULT_TIMELIMIT":
          tagut_assault_timelimit($i, $data);
          break;
        case "ASSAULT_GAMECODE":
          tagut_assault_gamecode($i, $data);
          break;
        case "ASSAULT_DEFENDER":
          tagut_assault_defender($i, $data);
          break;
        case "ASSAULT_ATTACKER":
          tagut_assault_attacker($i, $data);
          break;
        case "DOM_PLAYERSCORE_UPDATE":
          tagut_dom_playerscore_update($i, $data);
          break;
        case "CONTROLPOINT_CAPTURE":
          tagut_controlpoint_capture($i, $data);
          break;
        case "DOM_SCORE_UPDATE":
          tagut_dom_score_update($i, $data);
          break;
        case "TYPING": // 0	0/1 - shows when a player is typing
          tagut_typing($i, $data);
          break;
        case "GAME_END": // fraglimit
          tagut_game_end($i, $data);
          break;
        case "WEAP_SHOTCOUNT":
          tagut_weapshots($i, $data);
          break;
        case "WEAP_HITCOUNT":
          tagut_weaphits($i, $data);
          break;
        case "WEAP_DAMAGEGIVEN":
          tagut_weapdamage($i, $data);
          break;
        case "TEAM_CAPTURED":
          tagut_teamcap($i, $data);
          break;
        case "TEAM_RELEASED":
          tagut_teamrel($i, $data);
          break;
        case "ARENA_STARTED":
          break;
        case "ARENA_WON":
          break;
        case "SAY":
          tagut_say($i, $data);
          break;
        case "TEAMSAY":
          tagut_teamsay($i, $data);
          break;
        case "ROUND_START":
          tagut_tacops(0, $i, $data);
          break;
        case "ROUND_END":
          tagut_tacops(1, $i, $data);
          break;
      }
    }
  }

  $match->numplayers = $match->numhumans = $i = 0;
  reset($player);
  $playerc = current($player);
  while ($playerc !== FALSE) {
    if (isset($playerc->name) && $playerc->name != "" && $relog[$playerc->plr] < 0) {
      $match->numplayers++;
      if (!$playerc->is_bot())
        $match->numhumans++;
      if (!$match->rankset) {
        $ranks[0][$i] = array_sum($playerc->kills) - array_sum($playerc->suicides);
        $ranks[1][$i] = array_sum($playerc->deaths);
        $ranks[2][$i] = array_sum($playerc->suicides);
        $ranks[3][$i] = $playerc->plr;

        if ($match->gametype == 10 || $match->gametype == 19) {
          if ($playerc->plr == $match->lastman)
            $ranks[4][$i] = 1;
          else
            $ranks[4][$i] = $playerc->lms + 1;
        }
        else
          $ranks[4][$i] = 0;

        $ranks[5][$i++] = array_sum($playerc->tscore);
      }
    }
    $playerc = next($player);
  }

  // Sort actual player rankings (frags, deaths, suicides or team score)
  if ($match->numplayers > 0 && !$match->rankset) {
    if ($match->gametype == 10 || $match->gametype == 19) // Sort by lives, score for LMS
      array_multisort($ranks[4], SORT_ASC, SORT_NUMERIC,
                      $ranks[5], SORT_DESC, SORT_NUMERIC,
                      $ranks[0], SORT_DESC, SORT_NUMERIC,
                      $ranks[1], SORT_ASC, SORT_NUMERIC,
                      $ranks[2], SORT_ASC, SORT_NUMERIC,
                      $ranks[3], SORT_ASC, SORT_NUMERIC);
    else if ($match->teamgame || $match->gametype == 8) // Sort by Team Score for team games
      array_multisort($ranks[5], SORT_DESC, SORT_NUMERIC,
                      $ranks[0], SORT_DESC, SORT_NUMERIC,
                      $ranks[1], SORT_ASC, SORT_NUMERIC,
                      $ranks[2], SORT_ASC, SORT_NUMERIC,
                      $ranks[3], SORT_ASC, SORT_NUMERIC,
                      $ranks[4], SORT_DESC, SORT_NUMERIC);
    else
      array_multisort($ranks[0], SORT_DESC, SORT_NUMERIC,
                      $ranks[1], SORT_ASC, SORT_NUMERIC,
                      $ranks[2], SORT_ASC, SORT_NUMERIC,
                      $ranks[3], SORT_ASC, SORT_NUMERIC,
                      $ranks[5], SORT_DESC, SORT_NUMERIC,
                      $ranks[4], SORT_DESC, SORT_NUMERIC);

    for ($i = 0, $r = 1; $i < $match->numplayers; $i++) {
      if ($relog[$ranks[3][$i]] < 0 && $player[$ranks[3][$i]]->name != "")
        $player[$ranks[3][$i]]->rank = $r++;
    }
  }

  if ($match->ended && $match->ended != 4) {
    if (!$match->started || !$match->ngfound)
      $match->ended = 3;
  }

  if ($match->ended == 1) {
    if ($match->numplayers == 0)
      $match->ended = 3;
  }

  if ($match->ended == 1 && $config["discardscoreless"]) {
    $okscore = 0;
    if ($match->team[0] > 0 || $match->team[1] > 0 || $match->team[2] > 0 || $match->team[3] > 0)
      $okscore = 1;
    reset($player);
    $playerc = current($player);
    while ($playerc !== FALSE && !$okscore) {
      if (isset($playerc->name) && $playerc->name != "" && $playerc->named && ((array_sum($playerc->kills) - array_sum($playerc->suicides)) > 0 || $playerc->tscore[0] > 0 || $playerc->tscore[1] > 0 || $playerc->tscore[2] > 0 || $playerc->tscore[3] > 0))
        $okscore = 1;
      $playerc = next($player);
    }
    if (!$okscore)
      $match->ended = 9;
  }

  }

  // 1 = Ended Normally / 2 = Mapswitch, etc. / 3 = No 'NG' or 'SG' found / 4 = Existing Game / 9 = Scoreless
  return $match->ended;
}

?>