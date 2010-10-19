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

if (preg_match("/logkillevents.php/i", $_SERVER["PHP_SELF"])) {
  echo "Access denied.\n";
  die();
}

function tag_k ($i, $data)
{
  global $match, $player, $gkills, $spree, $multi, $killmatch, $tchange;
  global $special, $numspec, $specialtypes, $numspectypes;

  if ($i < 6 || $match->ended || !$match->started)
    return;

  $killtime = ctime($data[0]);
  $killer = check_player($data[2]);
  $killweapon = substr($data[3], 0, 60);
  $victim = check_player($data[4]);
  $victweapon = substr($data[5], 0, 60);

  if ($killer >= 0)
  {
    $tmk = $player[$killer]->team;
    if ($tmk < 0 || $tmk > 3)
      $tmk = 0;
  }
  else
    $tmk = 0;

  if ($victim >= 0)
  {
    $tmv = $player[$victim]->team;
    if ($tmv < 0 || $tmv > 3)
      $tmv = 0;
  }
  else
    $tmv = 0;

  // Get Kill Weapon
  list($killweaponnum,$killweaptype,$killweapsec) = get_weapon($killweapon, 0);
  // Get Victim Weapon
  list($victweaponnum,$victweaptype,$victweapsec) = get_weapon($victweapon, 0);

  if ($killer >= 0 && $killer == $victim) { // Shot self
    $player[$victim]->suicides[$tmv]++; // Suicides
    $reason = 2;
    $match->tot_suicides++;
    if (isset($killmatch[$victim][$victim]))
      $killmatch[$victim][$victim]++;
    else
      $killmatch[$victim][$victim] = 1;
  }
  else if ($killer < 0 && $killer > -99 && $victim >= 0) { // Fell, etc.
    // Check for Team Change Suicide
    if ($killer < 0 && $victim >= 0 && $tchange[$victim] >= $killtime - 1 && !strcasecmp($killweapon, "DamageType")) {
      $killweapon = "TeamChange";
      $tchange[$victim] = 0;
      $reason = 6;
    }
    else {
      if ($killweaptype > 0 || ($match->gametype == 9 && !$match->logger)) {
        $player[$victim]->deaths[$tmv]++; // Auto-turrets and monsters count as deaths
        $killer = -2;
        $reason = 3;
        $match->tot_deaths++;
      }
      else {
        $player[$victim]->suicides[$tmv]++; // Event Suicide
        $reason = 3;
        $match->tot_suicides++;
        if (isset($killmatch[$victim][$victim]))
          $killmatch[$victim][$victim]++;
        else
          $killmatch[$victim][$victim] = 1;
      }
    }
  }
  else {
    if ($victim < 0 || $killer == -99)
      return;
    $player[$killer]->kills[$tmk]++; // Kills
    $match->tot_kills++;
    $player[$victim]->deaths[$tmv]++; // Deaths
    $reason = 1;
    $match->tot_deaths++;
    if (isset($killmatch[$killer][$victim]))
      $killmatch[$killer][$victim]++;
    else
      $killmatch[$killer][$victim] = 1;

    // Check for special event
    for ($i = 0; $i < $numspectypes; $i++) {
      if (stristr($killweapon, $specialtypes[$i][1]) && $special[$specialtypes[$i][0]][2] == 0 && $special[$specialtypes[$i][0]][3] > 0) {
        $player[$killer]->specialcount[$specialtypes[$i][0]]++;
        if ($player[$killer]->specialcount[$specialtypes[$i][0]] == $specialtypes[$i][3]) {
          $player[$killer]->specialevents[$specialtypes[$i][0]]++;
          $match->specialevents[$specialtypes[$i][0]]++;
          weaponspecial($killtime, $killer, $specialtypes[$i][0], 0);
          $player[$killer]->specialcount[$specialtypes[$i][0]] = 0;
        }
      }
    }

    // Check for road kill
    if ($killweapsec == 4) {
      for ($i = 0, $roadkill = -1; $i < $numspectypes; $i++) {
        if (stristr("Road Kill", $specialtypes[$i][1]) && $specialtypes[$i][3] == 2) {
          $roadkill = $i;
          break;
        }
      }

      if ($roadkill >= 0)
        $player[$killer]->specialcount[$specialtypes[$roadkill][0]]++; // roadkills++;

      // Check for road rampage
      if ($player[$killer]->roadkills >= 15 && !$player[$killer]->roadrampage) {
        $player[$killer]->roadrampage = 1;
        weaponspecial($killtime, $killer, 5, 0);
      }
    }

    // Track Killing Sprees for Killer
    if (!$spree[$killer][1]) {
      $spree[$killer][0] = $killtime; // First Kill
      $spree[$killer][1] = 1;
    }
    else
      $spree[$killer][1]++; // Kills

    // Track Multi-Kills for Killer
    if ($killtime - $multi[$killer][2] < 400) { // Within multi range
      if (!$multi[$killer][1]) {
        $multi[$killer][0] = $killtime; // Start Time
        $multi[$killer][1] = 1; // Kills
      }
      else
        $multi[$killer][1]++; // Kills
      $multi[$killer][2] = $killtime; // Last Kill Time
    }
    else {
      endmulti($killer, $killtime); // End Multi-Kill for Killer
      $multi[$killer][0] = $killtime;
      $multi[$killer][1] = 1;
      $multi[$killer][2] = $killtime;
    }
  }

  if ($victim >= 0) {
    endspree($victim, $killtime, $reason, $killweaponnum, $killer); // End Killing Spree for Victim
    endmulti($victim, $killtime); // End Multi-Kill for Victim
    flag_check($victim, $killtime, 0);
  }
  $gkills[$match->gkcount][0] = $killer;        // Killer
  $gkills[$match->gkcount][1] = $victim;        // Victim
  $gkills[$match->gkcount][2] = $killtime;      // Time
  $gkills[$match->gkcount][3] = $killweaponnum; // Killer's Weapon Number
  $gkills[$match->gkcount][4] = $victweaponnum; // Victim's Weapon Number

  if ($killer >= 0)
    $gkills[$match->gkcount][5] = $tmk; // Killer Team
  else
    $gkills[$match->gkcount][5] = -1;

  if ($victim >= 0)
    $gkills[$match->gkcount][6] = $tmv; // Victim Team
  else
    $gkills[$match->gkcount][6] = -1;

  $gkills[$match->gkcount][7] = $killweaptype; // Killer's Weapon Type
  $gkills[$match->gkcount++][8] = $victweaptype; // Victim's Weapon Type

  if (($match->gametype == 10 || $match->gametype == 19) && $victim >= 0) {
    if ($player[$victim]->lives - (array_sum($player[$victim]->deaths) + array_sum($player[$victim]->suicides)) == 0)
      lmsout($killtime, $victim, 0);
  }
}

function tag_tk ($i, $data)
{
  global $match, $player, $gkills, $killmatch;

  if ($i < 6 || $match->ended || !$match->started)
    return;

  $killtime = ctime($data[0]);
  $killer = check_player($data[2]);
  $killweapon = substr($data[3], 0, 60);
  $victim = check_player($data[4]);
  $victweapon = substr($data[5], 0, 60);

  if ($killer >= 0)
  {
    $tmk = $player[$killer]->team;
    if ($tmk < 0 || $tmk > 3)
      $tmk = 0;
  }
  else
    $tmk = 0;

  if ($victim >= 0)
  {
    $tmv = $player[$victim]->team;
    if ($tmv < 0 || $tmv > 3)
      $tmv = 0;
  }
  else
    $tmv = 0;

  if ($killer < 0 || $victim < 0)
    return;

  $match->teamkills++;
  if (isset($killmatch[$killer][$killer]))
    $killmatch[$killer][$killer]++; // Count as suicide in rankings
  else
    $killmatch[$killer][$killer] = 1;

  $player[$killer]->teamkills[$tmk]++; // TeamKills
  $player[$victim]->teamdeaths[$tmv]++; // TeamDeaths

  // Get Kill Weapon
  list($killweaponnum,$killweaptype,$killweapsec) = get_weapon($killweapon, 0);
  // Get Victim Weapon
  list($victweaponnum,$victweaptype,$victweapsec) = get_weapon($victweapon, 0);

  if ($victim >= 0) {
    endspree($victim, $killtime, 5, $killweaponnum, $killer); // End Killing Spree for Victim
    endmulti($victim, $killtime); // End Multi-Kill for Victim
    flag_check($victim, $killtime, 0);
  }
  $gkills[$match->gkcount][0] = $killer;     // Killer
  $gkills[$match->gkcount][1] = $victim;     // Victim
  $gkills[$match->gkcount][2] = $killtime;   // Time
  $gkills[$match->gkcount][3] = $killweaponnum; // Killer's Weapon Number
  $gkills[$match->gkcount][4] = $victweaponnum; // Victim's Weapon Number

  if ($killer >= 0)
    $gkills[$match->gkcount][5] = $player[$killer]->team; // Killer Team
  else
    $gkills[$match->gkcount][5] = -1;

  if ($victim >= 0)
    $gkills[$match->gkcount][6] = $player[$victim]->team; // Victim Team
  else
    $gkills[$match->gkcount][6] = -1;

  $gkills[$match->gkcount][7] = $killweaptype; // Killer's Weapon Type
  $gkills[$match->gkcount++][8] = $victweaptype; // Victim's Weapon Type
}

function tag_mk ($i, $data)
{
  global $match, $player, $gkills;

  if ($i < 5 || $match->ended || !$match->started)
    return;

  $time = ctime($data[0]);
  $victim = check_player($data[2]);
  if ($victim < 0)
    return;
  $monster = substr($data[3], 0, 60);
  $victweapon = substr($data[4], 0, 60);

  if ($victim >= 0)
  {
    $tmv = $player[$victim]->team;
    if ($tmv < 0 || $tmv > 3)
      $tmv = 0;
  }
  else
    $tmv = 0;

  // Get Monster
  list($monsternum,$monstertype,$monstersec) = get_weapon($monster, 1);
  // Get Victim Weapon
  list($victweaponnum,$victweaptype,$victweapsec) = get_weapon($victweapon, 0);

  $player[$victim]->deaths[$tmv]++; // Kills
  $match->tot_deaths++;
  $reason = 3;

  if ($victim >= 0) {
    endspree($victim, $time, $reason, $monsternum, -3); // End Killing Spree for Victim
    endmulti($victim, $time); // End Multi-Kill for Victim
    flag_check($victim, $time, 0);
  }
  $gkills[$match->gkcount][0] = -3;
  $gkills[$match->gkcount][1] = $victim;
  $gkills[$match->gkcount][2] = $time;
  $gkills[$match->gkcount][3] = $monsternum;
  $gkills[$match->gkcount][4] = $victweaponnum;
  $gkills[$match->gkcount][5] = -1;
  $gkills[$match->gkcount][6] = -1;
  $gkills[$match->gkcount][7] = 3;
  $gkills[$match->gkcount++][8] = $victweaptype;
}

function tag_md ($i, $data)
{
  global $match, $player, $gkills;

  if ($i < 5 || $match->ended || !$match->started)
    return;

  $time = ctime($data[0]);
  $killer = check_player($data[2]);
  if ($killer < 0)
    return;
  $weapon = substr($data[3], 0, 60);
  $monster = substr($data[4], 0, 60);

  if ($killer >= 0)
  {
    $tmk = $player[$killer]->team;
    if ($tmk < 0 || $tmk > 3)
      $tmk = 0;
  }
  else
    $tmk = 0;

  // Get Kill Weapon
  list($weapnum,$weaptype,$weapsec) = get_weapon($weapon, 0);
  // Get Monster
  list($monsternum,$monstertype,$monstersec) = get_weapon($monster, 1);

  $player[$killer]->kills[$tmk]++; // Kills
  $match->tot_kills++;
  $reason = 1;

  $gkills[$match->gkcount][0] = $killer;
  $gkills[$match->gkcount][1] = -3;
  $gkills[$match->gkcount][2] = $time;
  $gkills[$match->gkcount][3] = $weapnum;
  $gkills[$match->gkcount][4] = $monsternum;
  if ($killer >= 0)
    $gkills[$match->gkcount][5] = $player[$killer]->team;
  else
    $gkills[$match->gkcount][5] = -1;
  $gkills[$match->gkcount][6] = -1;
  $gkills[$match->gkcount][7] = $weaptype;
  $gkills[$match->gkcount++][8] = 3;
}

?>