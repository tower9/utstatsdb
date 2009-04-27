<?php

/*
    UTStatsDB
    Copyright (C) 2002-2009  Patrick Contreras / Paul Gallier

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

if (preg_match("/loggameevents.php/i", $_SERVER["PHP_SELF"])) {
  echo "Access denied.\n";
  die();
}

// Game Event
function tag_g ($i, $data)
{
  global $link, $dbpre, $match, $player, $assist, $relog;

  if ($i < 3)
    return;

  $time = ctime($data[0]);
  $event = strtolower($data[2]);
  $plr = check_player($data[3]);

  switch ($event) {
    case "namechange":
      if ($match->ended)
        break;
      if ($i > 4 && $data[4] && $plr >= 0) {
      	$name = preg_replace("(\x1B...)", "", $data[4]); // Strip color codes
      	set_name($plr, substr($name, 0, 30));

        // Check for existing player name
        $relogged = 0;
        if ($plr == intval($data[3]) && !$player[$plr]->is_bot()) {
          for ($i2 = 0; $i2 <= $match->maxplayer && $relog[$plr] < 0; $i2++) {
            if (isset($player[$i2]) && !strcmp($player[$plr]->name, $player[$i2]->name) && !$player[$i2]->connected && !$player[$i2]->user && !$player[$i2]->id) {
              $relog[$plr] = $i2;
              $player[$plr]->name = "";
              $player[$i2]->connected = 1;
              $player[$i2]->starttime = $time;
              connections($i2, $time, 0);
            }
          }
        }
      }
      break;
    case "teamchange": // Team = 0-3
      if ($match->ended || $i < 5 || $plr < 0)
        break;

      $tm = intval($data[4]);
      if ($tm >= 0 && $tm <= 3) {
        // Add time for time spent on old team
        if ($match->started && $time - $player[$plr]->starttime > 1) {
          $oldteam = $player[$plr]->team;
          if ($oldteam >= 0 && $oldteam <= 3) {
            $player[$plr]->totaltime[$oldteam] += $time - $player[$plr]->starttime;
            $player[$plr]->starttime = $time;
          }
        }
        flag_check($plr, $time, 0);
        $player[$plr]->team = $tm;
        teamchange($time, $plr, $tm);
        if ($tm + 1 > $match->numteams)
          $match->numteams = $tm + 1;
      }
      break;
    case "flag_dropped":
    case "bomb_dropped":
      if ($match->ended)
        break;
      if ($i > 3 && $plr >= 0) {
        $tm = $player[$plr]->team;
        if ($tm >= 0 && $tm <= 3)
        {
          $player[$plr]->dropped[$tm]++;
          flag_check($plr, $time, 0);
        }
      }
      break;
    case "flag_taken":
    case "bomb_taken":
      if ($match->ended)
        break;
      if ($i > 3 && $plr >= 0) {
        $tm = $player[$plr]->team;
        if ($tm >= 0 && $tm <= 3)
        {
          $player[$plr]->taken[$tm]++;
          flag_check($plr, $time, 1);
        }
      }
      break;
    case "flag_returned":
      if ($match->ended)
        break;
      if ($i > 3 && $plr >= 0) {
        $tm = $player[$plr]->team;
        if ($tm >= 0 && $tm <= 3)
        {
          $player[$plr]->return[$tm]++;
          flag_check($plr, $time, 0);
        }
      }
      break;
    case "flag_pickup":
    case "bomb_pickup":
    {
      if ($match->ended)
        break;

      if ($i > 3 && $plr >= 0) {
        $tm = $player[$plr]->team;
        if ($tm >= 0 && $tm <= 3)
        {
          $player[$plr]->pickup[$tm]++;
          flag_check($plr, $time, 1);
        }
      }
      break;
    }
    case "flag_captured": // Player / Opposing Team
    {
      if ($match->ended || $i < 4 || $plr < 0)
        break;

      $tm = $player[$plr]->team;
      if ($tm < 0 || $tm > 3)
        break;

      $player[$plr]->capcarry[$tm]++;

      // Check for assists
      reset($player);
      $playerc = current($player);
      while ($playerc !== FALSE) {
        if (isset($playerc->name) && $playerc->name != "") {
          $i = $playerc->plr;
          if (isset($assist[$i]) && $assist[$i] && $i != $plr) {
            $tmx = $player[$i]->team;
            if ($tmx >= 0 && $tmx <= 3) {
              $player[$i]->assist[$tmx]++;
              $assist[$i] = 0;
            }
          }
        }
        $playerc = next($player);
      }

      flag_check($plr, $time, 0);
      $use_fcevent = 1;

      // Check for CTF4 opposing team report
      if ($i > 4) {
        $tme = intval($data[4]);
        if ($tme < 0 || $tme > 3)
          break;
      }
      else
        $tme = -1;

      teamevent($time, 0, $tm, $tme);
      break;
    }
    case "objectivecompleted_trophy":
    {
      if ($match->ended)
        break;

      if ($i > 4 && $plr >= 0) {
        $tm = $player[$plr]->team;
        if ($tm < 0)
          $tm = 0;
        $player[$plr]->capcarry[$tm]++;

        // Parse objective priority
        $line = $data[4];
        $loc = strpos($line, " ");
        if ($loc === FALSE)
          break;
        $obj_pri = intval(substr($line, 0, $loc));
        $line = substr($line, $loc + 1);

        // Parse objective time
        $loc = strpos($line, " ");
        if ($loc === FALSE)
          break;
        $obj_time = intval(substr($line, 0, $loc));

        // Parse objective description
        $obj_desc = sql_addslashes(substr($line, $loc + 1, 60));

        // Check for existing objective
        $result = sql_queryn($link, "SELECT obj_num FROM {$dbpre}objectives WHERE obj_desc='$obj_desc' LIMIT 1");
        if (!$result) {
          echo "Error reading objectives table.{$break}\n";
          exit;
        }
        $row = sql_fetch_row($result);
        sql_free_result($result);
        if ($row)
          $obj_num = $row[0];
        else { // Add new objective
          $result = sql_queryn($link, "INSERT INTO {$dbpre}objectives (obj_map,obj_priority,obj_desc) VALUES({$match->mapnum},$obj_pri,'$obj_desc')");
          if (!$result) {
            echo "Error saving new objective.{$break}\n";
            exit;
          }
          $obj_num = sql_insert_id($link);
        }

        // Save last objective time
        $match->lastobj = $obj_time;
        // Store objective as event
        objective($time, $match->curteam, $obj_num, $obj_time);
      }
      break;
    }
    case "as_beginround":
      if ($match->ended)
        break;

      $match->lastobj = 0; // Reset last objective time
      if ($i >= 5)
        $match->curteam = intval($data[4]);
      break;
    case "as_attackers_win":
      if ($match->ended)
        break;

      assault_round($time, 1, $match->curteam, $match->lastobj);
      break;
    case "as_defenders_win":
      if ($match->ended)
        break;

      assault_round($time, 0, 3 - $match->curteam, 0);
      break;
    case "endround_trophy":
      break;
    case "new_invasion_wave":
      if ($match->ended)
        break;

      if ($i >= 5) {
        $wave = intval($data[4]);
        $match->maxwave = $wave + 1;
        invasion_wave($time, $wave + 1);
      }
      break;
    case "carjack":
      if ($match->ended)
        break;

      if ($i >= 4 && $plr >= 0) {
        $player[$plr]->carjack++;
        if ($i >= 5) {
          $vehicle = substr($data[4], 0, 60);
          list($vehiclenum,$vehicletype,$vehiclesec) = get_weapon($vehicle, 0);
          weaponspecial($time, $plr, 3, $vehiclenum);
        }
        else
          weaponspecial($time, $plr, 3, 0);
      }
      break;
    case "mapvote":
      if ($i >= 5)
        map_vote($time, $plr, $data[4], 0, 1);
      break;
    case "kickvote":
      if ($i >= 5)
        map_vote($time, $plr, $data[4], 0, 3);
      break;
    case "gametypevote":
      if ($i >= 5)
        map_vote($time, $plr, $data[4], 0, 6);
      break;
    case "new_mutant":
      if ($i >= 4)
        mutant($time, $plr, 1);
      break;
    case "new_bottomfeeder":
      if ($i >= 4 && $plr >= 0) {
        $player[$plr]->pickup[1]++;
        mutant($time, $plr, 0);
      }
      break;
    case "lives":
      if ($i >= 5 && $plr >= 0)
        $player[$plr]->lives = intval($data[4]);
      break;
    case "dom_point_capture":
      if ($i >= 5 && $plr >= 0) {
        $tm = $player[$plr]->team;
        if ($tm >= 0 && $tm <= 3)
        {
          $player[$plr]->pickup[$tm]++;
          pointcap($time, $plr, $data[4]);
        }
      }
      break;
    case "flag_returned_timeout":
    case "bomb_returned_timeout":
    default:
      break;
  }
}

// Special Event
function tag_p ($i, $data)
{
  global $link, $dbpre, $match, $player;

  if ($i < 4 || $match->ended || !$match->started)
    return;

  $time = ctime($data[0]);
  $plr = check_player($data[2]);
  $event = strtolower($data[3]);

  if ($plr < 0)
    return;

  switch ($event) {
    case "first_blood":
      $match->firstblood = $plr;
      $player[$plr]->firstblood = 1;
      break;
    case "spree_1": // Killing Spree!
    case "spree_2": // Rampage!
    case "spree_3": // Dominating!
    case "spree_4": // Unstoppable!
    case "spree_5": // Godlike!
    case "spree_6": // Wicked Sick!
      break;
    case "multikill_1": // Double Kill
      $player[$plr]->multi[0]++;
      break;
    case "multikill_2": // Multi Kill
      $player[$plr]->multi[1]++;
      if ($player[$plr]->multi[0])
        $player[$plr]->multi[0]--;
      break;
    case "multikill_3": // Mega Kill
      $player[$plr]->multi[2]++;
      if ($player[$plr]->multi[1])
        $player[$plr]->multi[1]--;
      break;
    case "multikill_4": // Ultra Kill
      $player[$plr]->multi[3]++;
      if ($player[$plr]->multi[2])
        $player[$plr]->multi[2]--;
      break;
    case "multikill_5": // Monster Kill
      $player[$plr]->multi[4]++;
      if ($player[$plr]->multi[3])
        $player[$plr]->multi[3]--;
      break;
    case "multikill_6": // Ludicrous Kill
      $player[$plr]->multi[5]++;
      if ($player[$plr]->multi[4])
        $player[$plr]->multi[4]--;
      break;
    case "multikill_7": // Holy Shit (ignore after 7)
      $player[$plr]->multi[6]++;
      if ($player[$plr]->multi[5])
        $player[$plr]->multi[5]--;
      break;
    case "combospeed":
    case "xgame.combospeed": // Speed (XGame.ComboSpeed)
    case "olteamgames.olteamscombospeed": // OLTeamGames.OLTeamsComboSpeed
      $player[$plr]->combo[0]++;
      break;
    case "combodefensive":
    case "xgame.combodefensive": // Booster (XGame.ComboDefensive)
      $player[$plr]->combo[1]++;
      break;
    case "comboinvis":
    case "xgame.comboinvis": // Invisible (XGame.ComboInvis)
      $player[$plr]->combo[2]++;
      break;
    case "comboberserk":
    case "xgame.comboberserk": // Berserk (XGame.ComboBerserk)
      $player[$plr]->combo[3]++;
      break;
    case "bonuspack.combominime": // Pint-Sized (BonusPack.ComboMiniMe)
    case "bonuspack.combocrate": // (BonusPack.ComboCrate)
      break;
    case "translocate_gib":
      $player[$plr]->transgib++;
      break;
    case "carjack":
      if ($i >= 4 && $plr >= 0) {
        $player[$plr]->carjack++;
        if ($i >= 5) {
          $vehicle = substr($data[4], 0, 60);
          list($vehiclenum,$vehicletype,$vehiclesec) = get_weapon($vehicle, 0);
          weaponspecial($time, $plr, 3, $vehiclenum);
        }
        else
          weaponspecial($time, $plr, 3, 0);
      }
      break;
    // DeathBall
    case "goodpass":
      $tm = $player[$plr]->team;
      if ($tm >= 0 && $tm <= 3) {
        $player[$plr]->tossed[$tm]++;
        flag_check($plr, $time, 0);
      }
      break;
    case "badpass":
      $tm = $player[$plr]->team;
      if ($tm >= 0 && $tm <= 3) {
        $player[$plr]->extrac[$tm]++;
        flag_check($plr, $time, 0);
      }
      break;
    case "save":
      $tm = $player[$plr]->team;
      if ($tm >= 0 && $tm <= 3)
        $player[$plr]->return[$tm]++;
      break;
    case "failedsave":
      $tm = $player[$plr]->team;
      if ($tm >= 0 && $tm <= 3)
        $player[$plr]->extraa[$tm]++;
      break;
    case "interception":
      $tm = $player[$plr]->team;
      if ($tm >= 0 && $tm <= 3) {
        $player[$plr]->taken[$tm]++;
        flag_check($plr, $time, 1);
      }
      break;
    case "goal":
      $tm = $player[$plr]->team;
      if ($tm >= 0 && $tm <= 3)
        $player[$plr]->capcarry[$tm]++;
      break;
    case "missedgoals":
      $tm = $player[$plr]->team;
      if ($tm >= 0 && $tm <= 3)
        $player[$plr]->pickup[$tm]++;
      break;
    case "hattrick":
      $player[$plr]->carjack++;
      break;
    case "tackles":
      $tm = $player[$plr]->team;
      if ($tm >= 0 && $tm <= 3)
        $player[$plr]->typekill[$tm]++;
      break;
    case "tackled":
      $tm = $player[$plr]->team;
      if ($tm >= 0 && $tm <= 3) {
        $player[$plr]->dropped[$tm]++;
        flag_check($plr, $time, 0);
      }
      break;
    case "goodkill":
      $tm = $player[$plr]->team;
      if ($tm >= 0 && $tm <= 3)
        $player[$plr]->kills[$tm]++;
      break;
    case "badkill":
      $tm = $player[$plr]->team;
      if ($tm >= 0 && $tm <= 3)
        $player[$plr]->teamkills[$tm]++;
      break;
    case "flagscore": // UT3
      $tm = $player[$plr]->team;
      if ($tm >= 0 && $tm <= 3) {
        $player[$plr]->capcarry[$tm]++;
        teamscore($time, $tm, 1, 2);
      }
      break;
    case "killedcarrier": // UT3
      $tm = $player[$plr]->team;
      if ($tm >= 0 && $tm <= 3)
        $player[$plr]->typekill[$tm]++;
      break;
    case "flagreturned": // UT3
      $tm = $player[$plr]->team;
      if ($tm >= 0 && $tm <= 3)
        $player[$plr]->return[$tm]++;
      break;
  }
}

?>