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

if (preg_match("/logscoreevents.php/i", $_SERVER["PHP_SELF"])) {
  echo "Access denied.\n";
  die();
}

function tag_s ($i, $data)
{
  global $config, $match, $player, $gscores, $assist;

  if ($i < 5 || $match->ended || !$match->started)
    return;

  $time = ctime($data[0]);
  $plr = check_player($data[2]);
  $score = floatval($data[3]);

  if ($plr < 0)
    return;

  $tm = $player[$plr]->team;
  if ($tm < 0 || $tm > 3)
    $tm = 0;

  switch ($data[4]) {
    case "critical_frag":
      // Fix for critical frag score bug
      if ($config["criticalfix"])
        $score = 2.00;
      $player[$plr]->tscore[$tm] += $score;
      $player[$plr]->typekill[$tm]++;
      break;
    case "ball_thrown_final":
      $match->lastball = $plr;
      $player[$plr]->tscore[$tm] += $score;
      $player[$plr]->tossed[$tm]++;
      break;
    case "ball_cap_final":
      $match->lastball = $plr;
      $player[$plr]->tscore[$tm] += $score;
      $player[$plr]->capcarry[$tm]++;
      break;
    case "ball_score_assist":
      $player[$plr]->tscore[$tm] += $score;
      if ($plr != $match->lastball)
        $player[$plr]->assist[$tm]++;
      break;
    case "dom_score":
      $player[$plr]->tscore[$tm] += $score;
      $player[$plr]->capcarry[$tm]++;
      break;
    case "flag_cap_assist":
      $player[$plr]->tscore[$tm] += $score;
      $assist[$plr] = 1;
      break;
    case "blue_powernode_constructed":
      $player[$plr]->tscore[1] += $score;
      $player[$plr]->pickup[1]++;
      break;
    case "red_powernode_constructed":
      $player[$plr]->tscore[0] += $score;
      $player[$plr]->pickup[0]++;
      break;
    case "red_constructing_powernode_destroyed":
      $player[$plr]->tscore[1] += $score;
      $player[$plr]->dropped[1]++;
      break;
    case "blue_constructing_powernode_destroyed":
      $player[$plr]->tscore[0] += $score;
      $player[$plr]->dropped[0]++;
      break;
    case "red_powernode_destroyed":
      $player[$plr]->tscore[1] += $score;
      $player[$plr]->taken[1]++;
      break;
    case "blue_powernode_destroyed": //
      $player[$plr]->tscore[0] += $score;
      $player[$plr]->taken[0]++;
      break;
    case "red_powercore_destroyed": //
      $player[$plr]->tscore[1] += $score;
      $player[$plr]->capcarry[1]++;
      break;
    case "blue_powercore_destroyed": //
      $player[$plr]->tscore[0] += $score;
      $player[$plr]->capcarry[0]++;
      break;
    case "objectivescore":
    case "frag":
    case "self_frag":
    case "team_frag":
    case "flag_cap_1st_touch":
    case "ball_score_1st_touch":
    case "flag_ret_friendly":
    case "flag_ret_enemy":
    case "flag_cap_final":
    case "bottom_feeder_frag":
    default:
      $player[$plr]->tscore[$tm] += $score;
      break;
  }

  $match->tot_score += $score;
  $gscores[$match->gscount][0] = intval($plr);   // Player
  $gscores[$match->gscount][1] = $time;  // Time
  $gscores[$match->gscount][2] = $score; // Score
  $gscores[$match->gscount++][3] = $tm; // Team
}

function tag_t ($i, $data)
{
  global $match;

  if ($i < 5 || $match->ended || !$match->started)
    return;

  $event = strtolower($data[4]);
  $time = ctime($data[0]);
  $tm = intval($data[2]);
  if ($tm < 0 || $tm > 3)
    return;
  $score = floatval($data[3]);

  switch ($event) {
    case "tdm_frag":
      teamscore($time, $tm, $score, 1);
      break;
    case "invasion_frag":
      teamscore($time, $tm, $score, 1);
      break;
    case "flag_cap":
      teamscore($time, $tm, $score, 2);
      break;
    case "ball_carried":
      teamscore($time, $tm, $score, 3);
      break;
    case "ball_tossed":
      teamscore($time, $tm, $score, 4);
      break;
    case "dom_teamscore":
      teamscore($time, $tm, $score, 5);
      break;
    case "enemy_core_destroyed":
      teamscore($time, $tm, $score, 6);
      break;
    case "pair_of_round_winner":
      teamscore($time, $tm, $score, 7);
      assault_round($time, 2, $tm, 0);
      break;
    default:
      teamscore($time, $tm, $score, 0);
      break;
      break;
  }
}

?>