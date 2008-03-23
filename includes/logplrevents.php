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

if (preg_match("/logplrevents.php/i", $_SERVER["PHP_SELF"])) {
  echo "Access denied.\n";
  die();
}

// Player Connect
function tag_c ($i, $data)
{
  global $match, $player, $relog;

  if ($i < 4)
    return;

  $time = ctime($data[0]);
  $plr = intval($data[2]);
  $pcon = -1;

  if (!isset($player[$plr]))
    add_player($time, $plr);

  if ($i == 4)
    $player[$plr]->name = substr($data[3], 0, 30);
  else if ($i == 5 || $i == 7) {
    $player[$plr]->team = intval($data[3]);
    set_name($plr, substr($data[4], 0, 30));
    // Check for existing player name
    $relogged = 0;
    if (!$player[$plr]->is_bot()) {
      for ($i2 = 0; $i2 <= $match->maxplayer && $relog[$plr] < 0; $i2++) {
        if (isset($player[$i2]) && $plr != $i2 && !strcmp($player[$plr]->name, $player[$i2]->name) && !$player[$i2]->connected && !$player[$i2]->user && !$player[$i2]->id) {
          $relog[$plr] = $i2;
          $player[$plr]->name = "";
          $player[$i2]->connected = 1;
          $player[$i2]->starttime = $time;
          connections($i2, $time, 0);
        }
      }
    }
    if ($i == 5)
      $player[$plr]->bot = true;
    if ($i == 7) {
      $player[$plr]->key = intval($data[5]);
      $player[$plr]->ip = substr($data[6], 0, 21);
    }
    if ($player[$plr]->team == 255)
      $player[$plr]->team = 0;
    else {
      if ($player[$plr]->team >= 0 && $player[$plr]->team <= 3) {
        teamchange($time, $plr, $player[$plr]->team);
        if ($player[$plr]->team + 1 > $match->numteams)
          $match->numteams = $player[$plr]->team + 1;
      }
    }
  }
  else if ($i > 5) {
    $player[$plr]->key = substr($data[3], 0, 32);
    $player[$plr]->user = substr($data[4], 0, 35);
    $player[$plr]->id = substr($data[5], 0, 32);
  }
  $pcon = $plr;

  // Check for existing user when user/id is set
  if ($player[$plr]->name != "" && $player[$plr]->id != "") {
    for ($i2 = 0; $i2 <= $match->maxplayer && $relog[$plr] < 0; $i2++) {
      if ($plr != $i2 && isset($player[$i2]) && !strcmp($player[$plr]->key, $player[$i2]->key) && !strcmp($player[$plr]->user, $player[$i2]->user) && !strcmp($player[$plr]->id, $player[$i2]->id)) {
        if ($player[$i2]->connected) { // Make sure old player is disconnected
          $datax = array($data[0], "D", $i2);
          tag_d(3, $datax);
        }
        $relog[$plr] = $i2;
        $player[$plr]->name = "";
        $player[$i2]->starttime = $time;
        $pcon = $i2;
      }
    }
  }
  // Check for existing player name when user/id not set
  else {
    if ($player[$plr]->named) {
      for ($i2 = 0; $i2 <= $match->maxplayer && $relog[$plr] < 0; $i2++) {
        if ($plr != $i2 && isset($player[$i2]) && !strcmp($player[$plr]->name, $player[$i2]->name) && !$player[$i2]->connected && !$player[$i2]->user && !$player[$i2]->id) {
          if ($player[$i2]->connected) { // Make sure old player is disconnected
            $datax = array($data[0], "D", $i2);
            tag_d(3, $datax);
          }
          $relog[$plr] = $i2;
          $player[$plr]->name = "";
          $player[$i2]->starttime = $time;
          $pcon = $i2;
        }
      }
    }
  }
  if ($pcon >= 0 && !$player[$pcon]->connected) {
    $player[$pcon]->connected = 1;
    connections($pcon, $time, 0);
  }
  if ($plr > $match->maxplayer)
    $match->maxplayer = $plr;
}

// Player Disconnect
function tag_d ($i, $data)
{
  global $match, $player, $relog;

  if ($match->ended || $i < 3)
    return;

  $time = ctime($data[0]);
  $pl = intval($data[2]);
  $plr = check_player($pl);

  if ($plr < 0)
    return;

  // Check for relogged connection from this one
  for ($n = $match->maxplayer; $n > $pl; $n--)
    if (isset($relog[$n]) && $relog[$n] == $plr)
      return;

  $player[$plr]->connected = 0;

  if  (!$match->ended) {
    $tm = $player[$plr]->team;
    if ($tm < 0 || $tm > 3)
      $tm = 0;
    $ptime = $time - $player[$plr]->starttime;
    $player[$plr]->totaltime[$tm] += $ptime;
    endspree($plr, $time, 4, 0, 0); // End Killing Sprees
    endmulti($plr, $time); // End Multi-Kills
    flag_check($plr, $time, 0);
  }

  connections($plr, $time, 1);
}

// Player Info
function tag_ps ($i, $data)
{
  global $player;

  if ($i < 5)
    return;

  $plr = check_player($data[2]);

  if ($plr >= 0 && isset($player[$plr])) {
    $player[$plr]->ip = substr($data[3], 0, 21);
    $player[$plr]->netspeed = intval($data[4]);
    if ($i >= 6)
      $player[$plr]->hash = substr($data[5], 0, 32);
  }
}

// Player Ping
function tag_pp ($i, $data)
{
  global $player;

  if ($i < 4)
    return;

  $time = ctime($data[0]);
  $plr = check_player($data[2]);
  $ping = intval($data[3]);

  if ($plr < 0)
    return;

  $player[$plr]->ping += $ping;
  $player[$plr]->pingcount++;
}

// Player Accuracy
function tag_pa ($i, $data)
{
  global $player;

  if ($i < 7)
    return;

  $time = ctime($data[0]);
  $plr = check_player($data[2]);

  if ($plr < 0)
    return;

  $weapon = substr($data[3], 0, 35);
  $fired = intval($data[4]);
  $hit = intval($data[5]);
  $damage = intval($data[6]);

  list($weaponnum,$weapontype,$weaponsec) = get_weapon($weapon, 0);
  pwa_add($plr, $weaponnum, $fired, $hit, $damage);
}

// Bot Info
function tag_bi ($i, $data)
{
  global $player;

  if ($i < 11)
    return;

  $time = ctime($data[0]);
  $plr = check_player($data[2]);

  if ($plr < 0)
    return;

  $bot_skill = intval($data[3]);
  $bot_alertness = floatval($data[4]);
  $bot_accuracy = floatval($data[5]);
  $bot_aggressive = floatval($data[6]);
  $bot_strafing = floatval($data[7]);
  $bot_style = floatval($data[8]);
  $bot_tactics = floatval($data[9]);
  $bot_transloc = floatval($data[10]);
  $bot_reaction = 0;
  $bot_jumpiness = floatval($data[11]);

  list($bot_favorite,$weapontype,$weaponsec) = get_weapon(substr($data[12], 0, 35), 0);
  bot_add($plr,$bot_skill,$bot_alertness,$bot_accuracy,$bot_aggressive,$bot_strafing,$bot_style,$bot_tactics,$bot_transloc,$bot_reaction,$bot_jumpiness,$bot_favorite);
}

// Chat
function tag_v ($i, $data)
{
  global $player, $match, $chatlog;

  if ($i < 4)
    return;

  $time = ctime($data[0]);
  if (substr($data[2], 0, 5) == "spec_")
    $plr = -1;
  else {
    $plr = check_player($data[2]);
    if (!isset($player[$plr]) || $player[$plr]->name == "")
      $plr = -1;
  }

  $chatlog[$match->numchat][0] = $plr;
  $chatlog[$match->numchat][1] = 0;
  $chatlog[$match->numchat][2] = $time;
  $chatlog[$match->numchat++][3] = $data[3];
}

// Team Chat
function tag_tv ($i, $data)
{
  global $player, $match, $chatlog;

  if ($i < 4)
    return;

  $time = ctime($data[0]);
  $plr = check_player($data[2]);
  if (!isset($player[$plr]) || $player[$plr]->name == "")
    $plr = -1;

  $chatlog[$match->numchat][0] = $plr;
  $chatlog[$match->numchat][1] = $player[$plr]->team + 1;
  $chatlog[$match->numchat][2] = $time;
  $chatlog[$match->numchat++][3] = $data[3];
}

// Map Vote
function tag_mv ($i, $data)
{
  global $player;

  if ($i < 5)
    return;

  $time = ctime($data[0]);
  $plr = check_player($data[2]);
  $map = substr($data[3], 0, 32);
  $votes = intval($data[4]);

  if ($plr >= -1)
    map_vote($time, $plr, $map, $votes, 2);
}

// Kick Vote
function tag_kv ($i, $data)
{
  global $player;

  if ($i < 5)
    return;

  $time = ctime($data[0]);
  $plr = check_player($data[2]);
  $kick = check_player($data[3]);
  $votes = intval($data[4]);

  if ($plr >= -1 && $kick >= 0)
    map_vote($time, $plr, $kick, $votes, 4);
}

// Game Vote
function tag_gv ($i, $data)
{
  global $player;

  if ($i < 5)
    return;

  $time = ctime($data[0]);
  $plr = check_player($data[2]);
  $game = substr($data[3], 0, 32);
  $votes = intval($data[4]);

  if ($plr >= -1)
    map_vote($time, $plr, $game, $votes, 5);
}

// Item Pickup
function tag_i ($i, $data)
{
  global $link, $dbpre, $match, $pickups, $break;

  if ($i < 4 || $match->ended || !$match->started)
    return;

  // Remove OLTeams prefix from pickups
  if (strlen($data[3]) > 7 && substr($data[3], 0, 7) == "OLTeams")
    $item = sql_addslashes(substr($data[3], 7, 40));
  else
    $item = sql_addslashes(substr($data[3], 0, 40));

  $plr = check_player($data[2]);
  if ($plr < 0)
    return;

  // Get Item Number
  $result = sql_queryn($link, "SELECT it_num FROM {$dbpre}items WHERE it_type='$item' LIMIT 1");
  if (!$result) {
    echo "Error reading items table.{$break}\n";
    exit;
  }
  $row = sql_fetch_row($result);
  sql_free_result($result);
  if ($row) {
    $num = $row[0];
    if (isset($pickups[$plr][$num]))
      $pickups[$plr][$num]++;
    else
      $pickups[$plr][$num] = 1;
  }
  else { // Add new item
    if (strlen($item) > 7 && strtolower(substr($item, -7)) == "_pickup")
      $itemdesc = substr($item, 0, -7);
    else if (strlen($item) > 6 && strtolower(substr($item, -6)) == "pickup")
      $itemdesc = substr($item, 0, -6);
    else
      $itemdesc = $item;

    $result = sql_queryn($link, "INSERT INTO {$dbpre}items (it_type,it_desc) VALUES('$item','$itemdesc')");
    if (!$result) {
      echo "Error saving new item.{$break}\n";
      exit;
    }
    $num = sql_insert_id($link);
    if (isset($pickups[$plr][$num]))
      $pickups[$plr][$num]++;
    else
      $pickups[$plr][$num] = 1;
  }
  if ($num > $match->maxpickups)
    $match->maxpickups = $num;
}

?>