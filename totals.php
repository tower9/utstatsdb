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

require("includes/main.inc.php");

$link = sql_connect();
$result = sql_queryn($link, "SELECT * FROM {$dbpre}totals LIMIT 1");
if (!$result) {
  echo "Database error.<br />\n";
  exit;
}
$row = sql_fetch_assoc($result);
sql_free_result($result);
if (!$row) {
  echo "No data in stat totals database.<br />\n";
  exit;
}
while (list ($key, $val) = each ($row))
  ${$key} = $val;

//=============================================================================
//========== Cumulative Totals for All Players (Humans and Bots) ==============
//=============================================================================
echo <<<EOF
<center>
<table cellpadding="1" cellspacing="2" border="0" width="710">
  <tr>
    <td class="heading" align="center">Cumulative Totals for All Players</td>
  </tr>
</table>

EOF;

//=============================================================================
//========== Summary ==========================================================
//=============================================================================
echo <<<EOF
<font size="1"><br /></font>
<table cellpadding="1" cellspacing="2" border="0" class="box">
  <tr>
    <td class="medheading" align="center" colspan="12">Summary</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="150">Game Type</td>
    <td class="smheading" align="center" width="45">Score</td>
    <td class="smheading" align="center" width="35">F</td>
    <td class="smheading" align="center" width="35">K</td>
    <td class="smheading" align="center" width="35">D</td>
    <td class="smheading" align="center" width="35">S</td>
    <td class="smheading" align="center" width="35">TK</td>
    <td class="smheading" align="center" width="45">Eff.</td>
    <td class="smheading" align="center" width="40">Avg FPH</td>
    <td class="smheading" align="center" width="40">Avg TTL</td>
    <td class="smheading" align="center" width="50">Matches</td>
    <td class="smheading" align="center" width="45">Hours</td>
  </tr>

EOF;

$tot_score = $tot_frags = $tot_kills = $tot_deaths = $tot_suicides = 0;
$tot_teamkills = $tot_played = $tot_time = 0;

$result = sql_queryn($link, "SELECT * FROM {$dbpre}type");
if (!$result) {
  echo "Database error accessing game types.<br />\n";
  exit;
}
while ($row = sql_fetch_assoc($result)) {
  while (list ($key, $val) = each ($row))
    ${$key} = $val;

  if ($tp_played > 0) {
    $frags = $tp_kills - $tp_suicides;
    $tp_gtime = floatval($tp_gtime / 100);
    if ($tp_kills + $tp_deaths + $tp_suicides == 0)
      $eff = "0.0";
    else
      $eff = sprintf("%0.1f", ($tp_kills / ($tp_kills + $tp_deaths + $tp_suicides)) * 100.0);
    if ($tp_gtime == 0)
      $fph = "0.0";
    else
      $fph = sprintf("%0.1f", $frags * (3600 / $tp_gtime));
    $ttl = sprintf("%0.1f", $tp_gtime / ($tp_deaths + $tp_suicides + 1));
    $hours = sprintf("%0.1f", $tp_gtime / 3600);
    
    $tot_score += $tp_score;
    $tot_frags += $frags;
    $tot_kills += $tp_kills;
    $tot_deaths += $tp_deaths;
    $tot_suicides += $tp_suicides;
    $tot_teamkills += $tp_teamkills;
    $tot_played += $tp_played;
    $tot_time += $tp_gtime;
  
    echo <<<EOF
  <tr>
    <td class="dark" align="center">$tp_desc</td>
    <td class="grey" align="center">$tp_score</td>
    <td class="grey" align="center">$frags</td>
    <td class="grey" align="center">$tp_kills</td>
    <td class="grey" align="center">$tp_deaths</td>
    <td class="grey" align="center">$tp_suicides</td>
    <td class="grey" align="center">$tp_teamkills</td>
    <td class="grey" align="center">$eff%</td>
    <td class="grey" align="center">$fph</td>
    <td class="grey" align="center">$ttl</td>
    <td class="grey" align="center">$tp_played</td>
    <td class="grey" align="center">$hours</td>
  </tr>

EOF;
  }
}
sql_free_result($result);

if ($tot_kills + $tot_deaths + $tot_suicides == 0)
  $tot_eff = "0.0";
else
  $tot_eff = sprintf("%0.1f", ($tot_kills / ($tot_kills + $tot_deaths + $tot_suicides)) * 100.0);
if ($tot_time == 0)
  $tot_fph = "0.0";
else
  $tot_fph = sprintf("%0.1f", $tot_frags * (3600 / $tot_time));
$tot_ttl = sprintf("%0.1f", $tot_time / ($tot_deaths + $tot_suicides + 1));
$tot_hours = sprintf("%0.1f", $tot_time / 3600);

echo <<<EOF
  <tr>
    <td class="dark" align="center">Totals</td>
    <td class="darkgrey" align="center">$tot_score</td>
    <td class="darkgrey" align="center">$tot_frags</td>
    <td class="darkgrey" align="center">$tot_kills</td>
    <td class="darkgrey" align="center">$tot_deaths</td>
    <td class="darkgrey" align="center">$tot_suicides</td>
    <td class="darkgrey" align="center">$tot_teamkills</td>
    <td class="darkgrey" align="center">$tot_eff%</td>
    <td class="darkgrey" align="center">$tot_fph</td>
    <td class="darkgrey" align="center">$tot_ttl</td>
    <td class="darkgrey" align="center">$tot_played</td>
    <td class="darkgrey" align="center">$tot_hours</td>
  </tr>
</table>

EOF;

//=============================================================================
//========== CTF, Bombing Run, and Double Domination Events Summary ===========
//=============================================================================
echo <<<EOF
<font size="1"><br /></font>
<table cellpadding="1" cellspacing="2" border="0" width="600" class="box">
  <tr>
    <td class="medheading" align="center" colspan="11">CTF, Bombing Run, and Double Domination Events Summary</td>
  </tr>
  <tr>
    <td class="dark" align="center">Flag Captures</td>
    <td class="dark" align="center">Flag Kills</td>
    <td class="dark" align="center">Flag Assists</td>
    <td class="dark" align="center">Flag Saves</td>
    <td class="dark" align="center">Flag Pickups</td>
    <td class="dark" align="center">Flag Drops</td>
    <td class="dark" align="center">Bomb Carried</td>
    <td class="dark" align="center">Bomb Tossed</td>
    <td class="dark" align="center">Bomb Kills</td>
    <td class="dark" align="center">Bomb Drops</td>
    <td class="dark" align="center">Control Point Captures</td>
  </tr>
  <tr>
    <td class="grey" align="center">$tl_flagcapture</td>
    <td class="grey" align="center">$tl_flagkill</td>
    <td class="grey" align="center">$tl_flagassist</td>
    <td class="grey" align="center">$tl_flagreturn</td>
    <td class="grey" align="center">$tl_flagpickup</td>
    <td class="grey" align="center">$tl_flagdrop</td>
    <td class="grey" align="center">$tl_bombcarried</td>
    <td class="grey" align="center">$tl_bombtossed</td>
    <td class="grey" align="center">$tl_bombkill</td>
    <td class="grey" align="center">$tl_bombdrop</td>
    <td class="grey" align="center">$tl_cpcapture</td>
  </tr>
</table>

EOF;

//=============================================================================
//========== Onslaught Events Summary =========================================
//=============================================================================
echo <<<EOF
<font size="1"><br /></font>
<table cellpadding="1" cellspacing="2" border="0" width="400" class="box">
  <tr>
    <td class="medheading" align="center" colspan="11">Onslaught Events Summary</td>
  </tr>
  <tr>
    <td class="dark" align="center">Nodes Constructed</td>
    <td class="dark" align="center">Nodes Destroyed</td>
    <td class="dark" align="center">Constructing Nodes Destroyed</td>
    <td class="dark" align="center">Cores Destroyed</td>
  </tr>
  <tr>
    <td class="grey" align="center">$tl_nodeconstructed</td>
    <td class="grey" align="center">$tl_nodedestroyed</td>
    <td class="grey" align="center">$tl_nodeconstdestroyed</td>
    <td class="grey" align="center">$tl_coredestroyed</td>
  </tr>
</table>

EOF;

//=============================================================================
//========== Special Events ===================================================
//=============================================================================
echo <<<EOF
<font size="1"><br /></font>
<table cellpadding="1" cellspacing="2" border="0" class="box">
  <tr>
    <td class="medheading" align="center" colspan="6">Special Events</td>
  </tr>
  <tr>
    <td class="dark" align="center" width="100">Headshots</td>
    <td class="grey" align="center" width="45">$tl_headshots</td>
    <td class="dark" align="center" width="105">Failed Transloc</td>
    <td class="grey" align="center" width="45">$tl_transgib</td>
    <td class="dark" align="center" width="95">Double Kills</td>
    <td class="grey" align="center" width="45">$tl_multi1</td>
  </tr>
  <tr>
    <td class="dark" align="center">Multi Kills</td>
    <td class="grey" align="center">$tl_multi2</td>
    <td class="dark" align="center">Mega Kills</td>
    <td class="grey" align="center">$tl_multi3</td>
    <td class="dark" align="center">Ultra Kills</td>
    <td class="grey" align="center">$tl_multi4</td>
  </tr>
  <tr>
    <td class="dark" align="center">Monster Kills</td>
    <td class="grey" align="center">$tl_multi5</td>
    <td class="dark" align="center">Ludicrous Kills</td>
    <td class="grey" align="center">$tl_multi6</td>
    <td class="dark" align="center">Holy Shit Kills</td>
    <td class="grey" align="center">$tl_multi7</td>
  </tr>
  <tr>
    <td class="dark" align="center">Headhunter</td>
    <td class="grey" align="center">$tl_headhunter</td>
    <td class="dark" align="center">Flak Monkey</td>
    <td class="grey" align="center">$tl_flakmonkey</td>
    <td class="dark" align="center">Combo Whore</td>
    <td class="grey" align="center">$tl_combowhore</td>
  </tr>
  <tr>
    <td class="dark" align="center">Road Rampage</td>
    <td class="grey" align="center">$tl_roadrampage</td>
    <td class="dark" align="center">Roadkills</td>
    <td class="grey" align="center">$tl_roadkills</td>
    <td class="dark" align="center">Carjackings</td>
    <td class="grey" align="center">$tl_carjack</td>
  </tr>
</table>

EOF;

//=============================================================================
//========== Weapon Specific Totals ===========================================
//=============================================================================
$result = sql_queryn($link, "SELECT wp_desc,wp_secondary,wp_frags,wp_kills,wp_deaths,wp_suicides,wp_nwsuicides FROM {$dbpre}weapons WHERE wp_weaptype=0");
if (!$result) {
  echo "Database error accessing weapons table.<br />\n";
  exit;
}

$weapons = array(array());
/* wskills:
 0 = Weapon Description
 1 = Frags
 2 = Primary Kills
 3 = Secondary Kills
 4 = Deaths Holding
 5 = Suicides
 6 = Suicides (including Environmental)
*/
$numweapons = 0;
while ($row = sql_fetch_row($result)) {
  for ($i = 0, $weap = -1; $i < $numweapons && $weap < 0; $i++) {
    if (!strcmp($weapons[0][$i], $row[0]))
      $weap = $i;
  }
  if ($weap < 0) {
    $weapons[0][$numweapons] = $row[0];
    $weapons[1][$numweapons] = $row[2];
    if ($row[1]) {
      $weapons[2][$numweapons] = 0;
      $weapons[3][$numweapons] = $row[3];
    }
    else {
      $weapons[2][$numweapons] = $row[3];
      $weapons[3][$numweapons] = 0;
    }
    $weapons[4][$numweapons] = $row[4];
    $weapons[5][$numweapons] = $row[5];
    $weapons[6][$numweapons++] = $row[5] + $row[6];
  }
  else {
    $weapons[1][$weap] += $row[2];
    if ($row[1])
      $weapons[3][$weap] += $row[3];
    else
      $weapons[2][$weap] += $row[3];
    $weapons[4][$weap] += $row[4];
    $weapons[5][$weap] += $row[5];
    $weapons[6][$weap] += $row[5] + $row[6];
  }
}
sql_free_result($result);

// Sort by frags,deaths,suicides,nwsuicides,kills,secondary kills,description
array_multisort($weapons[1], SORT_DESC, SORT_NUMERIC,
                $weapons[4], SORT_ASC, SORT_NUMERIC,
                $weapons[5], SORT_ASC, SORT_NUMERIC,
                $weapons[6], SORT_ASC, SORT_NUMERIC,
                $weapons[2], SORT_ASC, SORT_NUMERIC,
                $weapons[3], SORT_ASC, SORT_NUMERIC,
                $weapons[0], SORT_ASC, SORT_STRING);

echo <<<EOF
<font size="1"><br /></font>
<table cellpadding="1" cellspacing="2" border="0" width="590" class="box">
  <tr>
    <td class="heading" colspan="7" align="center">Weapon Specific Totals</td>
  </tr>
  <tr>
    <td class="smheading" align="center">Weapon</td>
    <td class="smheading" align="center" width="55">Frags</td>
    <td class="smheading" align="center" width="70">Primary Kills</td>
    <td class="smheading" align="center" width="70">Secondary Kills</td>
    <td class="smheading" align="center" width="55">Deaths Holding</td>
    <td class="smheading" align="center" width="55">Suicides</td>
    <td class="smheading" align="center" width="60">Eff.</td>
  </tr>

EOF;

for ($i = 0; $i < $numweapons; $i++) {
  $weapon = $weapons[0][$i];
  $frags = $weapons[1][$i];
  $kills = $weapons[2][$i];
  $skills = $weapons[3][$i];
  $deaths = $weapons[4][$i];
  $suicides = $weapons[5][$i];
  if ($kills + $skills + $deaths + $suicides == 0)
    $eff = "0.0";
  else
    $eff = sprintf("%0.1f", (($kills + $skills) / ($kills + $skills + $deaths + $suicides)) * 100.0);

  if (($frags || $kills || $skills || $deaths) && strcmp($weapon, "None")) {
    echo <<< EOF
  <tr>
    <td class="dark" align="center">$weapon</td>
    <td class="grey" align="center">$frags</td>
    <td class="grey" align="center">$kills</td>
    <td class="grey" align="center">$skills</td>
    <td class="grey" align="center">$deaths</td>
    <td class="grey" align="center">$suicides</td>
    <td class="grey" align="center">$eff%</td>
  </tr>

EOF;
  }
}
echo "</table>\n";

//=============================================================================
//========== Weapon Accuracy Information ======================================
//=============================================================================
$result = sql_queryn($link, "SELECT wp_desc,wp_fired,wp_hits,wp_damage FROM {$dbpre}weapons WHERE wp_weaptype=0 AND wp_fired > 0");
if (!$result) {
  echo "Database error accessing weapons table.<br />\n";
  exit;
}

$weapons = array(array());
/* wskills:
 0 = Weapon Description
 1 = Fired
 2 = Hits
 3 = Damage
*/
$numweapons = 0;
while ($row = sql_fetch_row($result)) {
  for ($i = 0, $weap = -1; $i < $numweapons && $weap < 0; $i++) {
    if (!strcmp($weapons[0][$i], $row[0]))
      $weap = $i;
  }
  if ($weap < 0) {
    $weapons[0][$numweapons] = $row[0];
    $weapons[1][$numweapons] = $row[1];
    $weapons[2][$numweapons] = $row[2];
    $weapons[3][$numweapons++] = $row[3];
  }
  else {
    $weapons[1][$weap] += $row[1];
    $weapons[2][$weap] += $row[2];
    $weapons[3][$weap] += $row[3];
  }
}
sql_free_result($result);

if ($numweapons) {
  echo <<<EOF
<font size="1"><br /></font>
<table cellpadding="1" cellspacing="2" border="0" width="440" class="box">
  <tr>
    <td class="heading" colspan="5" align="center">Weapon Accuracy Information</td>
  </tr>
  <tr>
    <td class="smheading" align="center">Weapon</td>
    <td class="smheading" align="center" width="55">Fired</td>
    <td class="smheading" align="center" width="55">Hits</td>
    <td class="smheading" align="center" width="60">Damage</td>
    <td class="smheading" align="center" width="65">Accuracy</td>
  </tr>

EOF;

  // Sort by fired,hits,damage,description
  array_multisort($weapons[1], SORT_DESC, SORT_NUMERIC,
                  $weapons[2], SORT_DESC, SORT_NUMERIC,
                  $weapons[3], SORT_DESC, SORT_NUMERIC,
                  $weapons[0], SORT_DESC, SORT_STRING);

  for ($i = 0; $i < $numweapons; $i++) {
    $weapon = $weapons[0][$i];
    $fired = $weapons[1][$i];
    $hits = $weapons[2][$i];
    $damage = $weapons[3][$i];
    if ($fired == 0)
      $acc = "0.0";
    else
      $acc = sprintf("%0.1f", ($hits / $fired) * 100.0);

    echo <<< EOF
  <tr>
    <td class="dark" align="center">$weapon</td>
    <td class="grey" align="center">$fired</td>
    <td class="grey" align="center">$hits</td>
    <td class="grey" align="center">$damage</td>
    <td class="grey" align="center">$acc%</td>
  </tr>

EOF;
  }
  echo "</table>\n";
}

//=============================================================================
//========== Vehicle and Turret Specific Totals ===============================
//=============================================================================
$result = sql_queryn($link, "SELECT wp_desc,wp_secondary,wp_frags,wp_kills,wp_deaths,wp_suicides,wp_nwsuicides FROM {$dbpre}weapons WHERE wp_weaptype=1 OR wp_weaptype=2");
if (!$result) {
  echo "Database error accessing weapons table.<br />\n";
  exit;
}

$weapons = array(array());
/* wskills:
 0 = Weapon Description
 1 = Frags
 2 = Primary Kills
 3 = Secondary Kills
 4 = Deaths In
 5 = Suicides
 6 = Suicides (including Environmental)
 7 = Road Kills
*/
$numweapons = 0;
while ($row = sql_fetch_row($result)) {
  $weapon = $row[0];
  $secondary = intval($row[1]);
  $frags = intval($row[2]);
  $kills = intval($row[3]);
  $deaths = intval($row[4]);
  $suicides = intval($row[5]);
  $nwsuicides = intval($row[6]);

  for ($i = 0, $weap = -1; $i < $numweapons && $weap < 0; $i++) {
    if (!strcmp($weapons[0][$i], $row[0]))
      $weap = $i;
  }

  if ($weap < 0) {
    $weapons[0][$numweapons] = $weapon;
    $weapons[1][$numweapons] = $frags;
    if ($secondary == 4) {
      $weapons[2][$numweapons] = 0;
      $weapons[3][$numweapons] = 0;
      $weapons[7][$numweapons] = $kills;
    }
    else if ($secondary) {
      $weapons[2][$numweapons] = 0;
      $weapons[3][$numweapons] = $kills;
      $weapons[7][$numweapons] = 0;
    }
    else {
      $weapons[2][$numweapons] = $kills;
      $weapons[3][$numweapons] = 0;
      $weapons[7][$numweapons] = 0;
    }
    $weapons[4][$numweapons] = $deaths;
    $weapons[5][$numweapons] = $suicides;
    $weapons[6][$numweapons++] = $suicides + $nwsuicides;
  }
  else {
    $weapons[1][$weap] += $frags;
    if ($secondary == 4)
      $weapons[7][$weap] += $kills;
    else if ($secondary)
      $weapons[3][$weap] += $kills;
    else
      $weapons[2][$weap] += $kills;
    $weapons[4][$weap] += $deaths;
    $weapons[5][$weap] += $suicides;
    $weapons[6][$weap] += $suicides + $nwsuicides;
  }
}
sql_free_result($result);

// Sort by frags,kills,secondary kills,road kills,deaths in,suicides,nwsuicides,description
array_multisort($weapons[1], SORT_DESC, SORT_NUMERIC,
                $weapons[2], SORT_ASC, SORT_NUMERIC,
                $weapons[3], SORT_ASC, SORT_NUMERIC,
                $weapons[7], SORT_ASC, SORT_NUMERIC,
                $weapons[4], SORT_ASC, SORT_NUMERIC,
                $weapons[5], SORT_ASC, SORT_NUMERIC,
                $weapons[6], SORT_ASC, SORT_NUMERIC,
                $weapons[0], SORT_ASC, SORT_STRING);

echo <<<EOF
<font size="1"><br /></font>
<table cellpadding="1" cellspacing="2" border="0" width="660" class="box">
  <tr>
    <td class="heading" colspan="8" align="center">Vehicle and Turret Specific Totals</td>
  </tr>
  <tr>
    <td class="smheading" align="center">Vehicle/Turret</td>
    <td class="smheading" align="center" width="55">Frags</td>
    <td class="smheading" align="center" width="70">Primary Kills</td>
    <td class="smheading" align="center" width="70">Secondary Kills</td>
    <td class="smheading" align="center" width="55">Road Kills</td>
    <td class="smheading" align="center" width="55">Deaths In</td>
    <td class="smheading" align="center" width="55">Suicides</td>
    <td class="smheading" align="center" width="60">Eff.</td>
  </tr>

EOF;

for ($i = 0; $i < $numweapons; $i++) {
  $weapon = $weapons[0][$i];
  $frags = $weapons[1][$i];
  $kills = $weapons[2][$i];
  $skills = $weapons[3][$i];
  $deaths = $weapons[4][$i];
  $suicides = $weapons[5][$i];
  $roadkills = $weapons[7][$i];

  if ($kills + $skills + $roadkills + $deaths + $suicides == 0)
    $eff = "0.0";
  else
    $eff = sprintf("%0.1f", (($kills + $skills + $roadkills) / ($kills + $skills + $roadkills + $deaths + $suicides)) * 100.0);

  if ($frags || $kills || $skills || $roadkills) {
    echo <<< EOF
  <tr>
    <td class="dark" align="center">$weapon</td>
    <td class="grey" align="center">$frags</td>
    <td class="grey" align="center">$kills</td>
    <td class="grey" align="center">$skills</td>
    <td class="grey" align="center">$roadkills</td>
    <td class="grey" align="center">$deaths</td>
    <td class="grey" align="center">$suicides</td>
    <td class="grey" align="center">$eff%</td>
  </tr>

EOF;
  }
}
echo "</table>\n";

//=============================================================================
//========== Invasion Monster Specific Totals =================================
//=============================================================================
$result = sql_queryn($link, "SELECT wp_desc,wp_kills,wp_deaths FROM {$dbpre}weapons WHERE wp_weaptype=3");
if (!$result) {
  echo "Database error accessing weapons table.<br />\n";
  exit;
}

$weapons = array(array());
/* wskills:
 0 = Weapon Description
 1 = Kills
 2 = Deaths
*/
$numweapons = 0;
while ($row = sql_fetch_row($result)) {
  for ($i = 0, $weap = -1; $i < $numweapons && $weap < 0; $i++) {
    if (!strcmp($weapons[0][$i], $row[0]))
      $weap = $i;
  }
  if ($weap < 0) {
    $weapons[0][$numweapons] = $row[0];
    $weapons[1][$numweapons] = $row[1];
    $weapons[2][$numweapons++] = $row[2];
  }
  else
    $weapons[1][$weap] += $row[1];
}
sql_free_result($result);

// Sort by kills,deaths,description
array_multisort($weapons[1], SORT_DESC, SORT_NUMERIC,
                $weapons[2], SORT_ASC, SORT_NUMERIC,
                $weapons[0], SORT_ASC, SORT_STRING);

echo <<<EOF
<font size="1"><br /></font>
<table cellpadding="1" cellspacing="2" border="0" width="340" class="box">
  <tr>
    <td class="heading" colspan="3" align="center">Invasion Monster Totals</td>
  </tr>
  <tr>
    <td class="smheading" align="center">Monster</td>
    <td class="smheading" align="center" width="95">Players Killed</td>
    <td class="smheading" align="center" width="55">Deaths</td>
  </tr>

EOF;

for ($i = 0; $i < $numweapons; $i++) {
  $weapon = $weapons[0][$i];
  $kills = $weapons[1][$i];
  $deaths = $weapons[2][$i];

  if ($kills || $deaths) {
    echo <<< EOF
  <tr>
    <td class="dark" align="center">$weapon</td>
    <td class="grey" align="center">$kills</td>
    <td class="grey" align="center">$deaths</td>
  </tr>

EOF;
  }
}
echo "</table>\n";

//=============================================================================
//========== Suicide Totals ===================================================
//=============================================================================
$result = sql_queryn($link, "SELECT wp_desc,wp_suicides,wp_nwsuicides FROM {$dbpre}weapons WHERE (wp_suicides+wp_nwsuicides)>0");
if (!$result) {
  echo "Database error accessing weapons table.<br />\n";
  exit;
}

$weapons = array(array());
/* wskills:
 0 = Weapon Description
 1 = Suicides
 2 = Suicides (including Environmental)
*/
$numweapons = 0;
while ($row = sql_fetch_row($result)) {
  for ($i = 0, $weap = -1; $i < $numweapons && $weap < 0; $i++) {
    if (!strcmp($weapons[0][$i], $row[0]))
      $weap = $i;
  }
  if ($weap < 0) {
    $weapons[0][$numweapons] = $row[0];
    $weapons[1][$numweapons] = $row[1];
    $weapons[2][$numweapons++] = $row[1] + $row[2];
  }
  else {
    $weapons[1][$weap] += $row[1];
    $weapons[2][$weap] += $row[1] + $row[2];
  }
}
sql_free_result($result);

if ($numweapons) {
  // Sort by suicides,nwsuicides,description
  array_multisort($weapons[2], SORT_DESC, SORT_NUMERIC,
                  $weapons[1], SORT_DESC, SORT_NUMERIC,
                  $weapons[0], SORT_ASC, SORT_STRING);

  echo <<<EOF
<font size="1"><br /></font>
<table cellpadding="1" cellspacing="2" border="0" width="260" class="box">
  <tr>
    <td class="heading" colspan="2" align="center">Suicide Totals</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="200">Type</td>
    <td class="smheading" align="center" width="60">Suicides</td>
  </tr>

EOF;

  for ($i = 0; $i < $numweapons; $i++) {
    $weapon = $weapons[0][$i];
    $suicides = $weapons[1][$i];

    if ($suicides) {
      echo <<< EOF
  <tr>
    <td class="dark" align="center">$weapon</td>
    <td class="grey" align="center">$suicides</td>
  </tr>

EOF;
    }
  }
  echo "</table>\n";
}

//=============================================================================
//========== Killing Sprees by Type ===========================================
//=============================================================================
$time1 = sprintf("%0.1f", $tl_spreet1 / 6000);
$time2 = sprintf("%0.1f", $tl_spreet2 / 6000);
$time3 = sprintf("%0.1f", $tl_spreet3 / 6000);
$time4 = sprintf("%0.1f", $tl_spreet4 / 6000);
$time5 = sprintf("%0.1f", $tl_spreet5 / 6000);
$time6 = sprintf("%0.1f", $tl_spreet6 / 6000);

echo <<<EOF
<font size="1"><br /></font>
<table cellpadding="1" cellspacing="2" border="0" width="390" class="box">
  <tr>
    <td class="medheading" align="center" colspan="4">Killing Sprees by Type</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="110">Spree Type</td>
    <td class="smheading" align="center" width="85"># of Sprees</td>
    <td class="smheading" align="center" width="115">Total Time (min)</td>
    <td class="smheading" align="center" width="80">Total Kills</td>
  </tr>
  <tr>
    <td class="dark" align="center">Killing Spree</td>
    <td class="grey" align="center">$tl_spree1</td>
    <td class="grey" align="center">$time1</td>
    <td class="grey" align="center">$tl_spreek1</td>
  </tr>
  <tr>
    <td class="dark" align="center">Rampage</td>
    <td class="grey" align="center">$tl_spree2</td>
    <td class="grey" align="center">$time2</td>
    <td class="grey" align="center">$tl_spreek2</td>
  </tr>
  <tr>
    <td class="dark" align="center">Dominating</td>
    <td class="grey" align="center">$tl_spree3</td>
    <td class="grey" align="center">$time3</td>
    <td class="grey" align="center">$tl_spreek3</td>
  </tr>
  <tr>
    <td class="dark" align="center">Unstoppable</td>
    <td class="grey" align="center">$tl_spree4</td>
    <td class="grey" align="center">$time4</td>
    <td class="grey" align="center">$tl_spreek4</td>
  </tr>
  <tr>
    <td class="dark" align="center">Godlike</td>
    <td class="grey" align="center">$tl_spree5</td>
    <td class="grey" align="center">$time5</td>
    <td class="grey" align="center">$tl_spreek5</td>
  </tr>
  <tr>
    <td class="dark" align="center">Wicked Sick</td>
    <td class="grey" align="center">$tl_spree6</td>
    <td class="grey" align="center">$time6</td>
    <td class="grey" align="center">$tl_spreek6</td>
  </tr>
</table>

EOF;

//=============================================================================
//========== Total Items Collected ============================================
//=============================================================================
echo <<<EOF
<font size="1"><br /></font>
<table cellpadding="1" cellspacing="2" border="0" width="600" class="box">
  <tr>
    <td class="heading" colspan="6" align="center">Total Items Collected</td>
  </tr>
  <tr>
    <td class="smheading" align="center">Item Type</td>
    <td class="smheading" align="center" width="35">No.</td>
    <td class="smheading" align="center">Item Type</td>
    <td class="smheading" align="center" width="35">No.</td>
    <td class="smheading" align="center">Item Type.</td>
    <td class="smheading" align="center" width="35">No.</td>
  </tr>

EOF;

$result = sql_queryn($link, "SELECT it_desc,it_pickups FROM {$dbpre}items ORDER BY it_pickups DESC,it_desc ASC");
if (!$result) {
  echo "Error loading item pickup descriptions.<br />\n";
  exit;
}
$col = $totpickups = 0;
while ($row = sql_fetch_row($result)) {
  $item = $row[0];
  $num = $row[1];
  if ($num) {
    if ($col > 2)
      $col = 0;
    if ($col == 0)
      echo "  <tr>\n";
    echo <<<EOF
    <td class="dark" align="center">$item</td>
    <td class="grey" align="center">$num</td>

EOF;
    if ($col == 2)
      echo "  </tr>\n";
    $col++;
    $totpickups++;
  }
}
sql_free_result($result);

if (!$totpickups) {
  echo <<<EOF
  <tr>
    <td class="dark" align="center" colspan="6">NO ITEM PICKUPS</td>
  </tr>

EOF;
}
else {
  while ($col < 3) {
    echo <<<EOF
  <td class="dark" align="center">&nbsp;</td>
  <td class="grey" align="center">&nbsp;</td>

EOF;
    $col++;
  }
}
echo "</table>\n";

sql_close($link);

echo <<<EOF
</center>

</td></tr></table>

</body>
</html>

EOF;

?>