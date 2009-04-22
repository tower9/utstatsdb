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

require("includes/main.inc.php");

$link = sql_connect();
$result = sql_queryn($link, "SELECT * FROM {$dbpre}totals LIMIT 1");
if (!$result) {
  echo "{$LANG_DATABASEERROR}<br />\n";
  exit;
}
$row = sql_fetch_assoc($result);
sql_free_result($result);
if (!$row) {
  echo "{$LANG_NOTOTALSDATA}<br />\n";
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
    <td class="heading" align="center">{$LANG_CUMULATIVETOTAL}</td>
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
    <td class="medheading" align="center" colspan="12">{$LANG_SUMMARY}</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="150">{$LANG_GAMETYPE}</td>
    <td class="smheading" align="center" width="45">{$LANG_SCORE}</td>
    <td class="smheading" align="center" width="35">{$LANG_F}</td>
    <td class="smheading" align="center" width="35">{$LANG_K}</td>
    <td class="smheading" align="center" width="35">{$LANG_D}</td>
    <td class="smheading" align="center" width="35">{$LANG_S}</td>
    <td class="smheading" align="center" width="35">{$LANG_TK}</td>
    <td class="smheading" align="center" width="45">{$LANG_EFF}</td>
    <td class="smheading" align="center" width="40">{$LANG_AVGFPH}</td>
    <td class="smheading" align="center" width="40">{$LANG_AVGTTL}</td>
    <td class="smheading" align="center" width="50">{$LANG_MATCHES}</td>
    <td class="smheading" align="center" width="45">{$LANG_HOURS}</td>
  </tr>

EOF;

$tot_score = $tot_frags = $tot_kills = $tot_deaths = $tot_suicides = 0;
$tot_teamkills = $tot_played = $tot_time = 0;

$result = sql_queryn($link, "SELECT * FROM {$dbpre}type");
if (!$result) {
  echo "{$LANG_DBERRORGAMETYPES}<br />\n";
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
      $fph = sprintf("%0.1f", $frags * (3600.0 / $tp_gtime));
    $ttl = sprintf("%0.1f", $tp_gtime / ($tp_deaths + $tp_suicides + 1));
    $hours = sprintf("%0.1f", $tp_gtime / 3600.0);
    
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
    <td class="dark" align="center">{$LANG_TOTALS}</td>
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
    <td class="medheading" align="center" colspan="11">{$LANG_CTFBRDDEVENTSSUMMARY}</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_FLAGCAPTURES}</td>
    <td class="dark" align="center">{$LANG_FLAGKILLS}</td>
    <td class="dark" align="center">{$LANG_FLAGASSISTS}</td>
    <td class="dark" align="center">{$LANG_FLAGSAVES}</td>
    <td class="dark" align="center">{$LANG_FLAGPICKUPS}</td>
    <td class="dark" align="center">{$LANG_FLAGDROPS}</td>
    <td class="dark" align="center">{$LANG_BOMBCARRIED}</td>
    <td class="dark" align="center">{$LANG_BOMBTOSSED}</td>
    <td class="dark" align="center">{$LANG_BOMBKILLS}</td>
    <td class="dark" align="center">{$LANG_BOMBDROPS}</td>
    <td class="dark" align="center">{$LANG_CPCAPTURES}</td>
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
    <td class="medheading" align="center" colspan="11">{$LANG_ONSEVENTSSUMMARY}</td>
  </tr>
  <tr>
    <td class="dark" align="center">$LANG_NODESCONSTRUCTED</td>
    <td class="dark" align="center">$LANG_NODESDESTROYED</td>
    <td class="dark" align="center">$LANG_CONSTNODESDESTROYED</td>
    <td class="dark" align="center">$LANG_CORESDESTROYED</td>
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
$result = sql_queryn($link, "SELECT se_num,se_title,se_desc,se_total FROM {$dbpre}special ORDER BY se_num");
if (!$result) {
  echo "Special event database error.<br />\n";
  exit;
}
$numspec = 0;
while ($row = sql_fetch_row($result)) {
  $special[$numspec]["title"] = $row[1];
  $special[$numspec]["desc"] = $row[2];
  $special[$numspec++]["total"] = $row[3] != NULL ? $row[3] : 0;
}
sql_free_result($result);

echo <<<EOF
<font size="1"><br /></font>
<table cellpadding="1" cellspacing="2" border="0" class="box">
  <tr>
    <td class="medheading" align="center" colspan="6">$LANG_SPECTIALEVENTS</td>
  </tr>
  <tr>
    <td class="dark" align="center" style="white-space:nowrap" width="100">{$LANG_HEADSHOTS}</td>
    <td class="grey" align="center" width="45">$tl_headshots</td>
    <td class="dark" align="center" style="white-space:nowrap" width="105">{$LANG_FAILEDTRANSLOC}</td>
    <td class="grey" align="center" width="45">$tl_transgib</td>
    <td class="dark" align="center" style="white-space:nowrap" width="95">{$LANG_DOUBLEKILLS}</td>
    <td class="grey" align="center" width="45">$tl_multi1</td>
  </tr>
  <tr>
    <td class="dark" align="center" style="white-space:nowrap">{$LANG_MULTIKILLS}</td>
    <td class="grey" align="center">$tl_multi2</td>
    <td class="dark" align="center" style="white-space:nowrap">{$LANG_MEGAKILLS}</td>
    <td class="grey" align="center">$tl_multi3</td>
    <td class="dark" align="center" style="white-space:nowrap">{$LANG_ULTRAKILLS}</td>
    <td class="grey" align="center">$tl_multi4</td>
  </tr>
  <tr>
    <td class="dark" align="center" style="white-space:nowrap">{$LANG_MONSTERKILLS}</td>
    <td class="grey" align="center">$tl_multi5</td>
    <td class="dark" align="center" style="white-space:nowrap">{$LANG_LUDICROUSKILLS}</td>
    <td class="grey" align="center">$tl_multi6</td>
    <td class="dark" align="center" style="white-space:nowrap">{$LANG_HOLYSHITKILLS}</td>
    <td class="grey" align="center">$tl_multi7</td>
  </tr>

EOF;

$col = 0;
for ($i = 0; $i < $numspec; $i++) {
  if ($col == 0)
    echo "  <tr>\n";

  echo <<<EOF
    <td class="dark" align="center" style="white-space:nowrap" title="{$special[$i]['desc']}">{$special[$i]['title']}</td>
    <td class="grey" align="center">{$special[$i]['total']}</td>

EOF;

  $col++;
  if ($col == 3) {
    echo "  </tr>\n";
    $col = 0;
  }
}

if ($col > 0) {
  while ($col < 3) {
    echo "    <td class=\"dark\" align=\"center\">&nbsp;</td>\n    <td class=\"grey\" align=\"center\">&nbsp;</td>\n";
    $col++;
  }

  echo "  </tr>\n";
}

echo "</table>\n";

//=============================================================================
//========== Weapon Specific Totals ===========================================
//=============================================================================
$result = sql_queryn($link, "SELECT wp_desc,wp_secondary,wp_frags,wp_kills,wp_deaths,wp_suicides,wp_nwsuicides FROM {$dbpre}weapons WHERE wp_weaptype=0");
if (!$result) {
  echo "{$LANG_WEAPDATABASEERROR}<br />\n";
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
    <td class="heading" colspan="7" align="center">{$LANG_WEAPONSPECIFICTOTALS}</td>
  </tr>
  <tr>
    <td class="smheading" align="center">{$LANG_WEAPON}</td>
    <td class="smheading" align="center" width="55">{$LANG_FRAGS}</td>
    <td class="smheading" align="center" width="70">{$LANG_PRIMARYKILLS}</td>
    <td class="smheading" align="center" width="70">{$LANG_SECONDARYKILLS}</td>
    <td class="smheading" align="center" width="55">{$LANG_DEATHSHOLDING}</td>
    <td class="smheading" align="center" width="55">{$LANG_SUICIDES}</td>
    <td class="smheading" align="center" width="60">{$LANG_EFF}</td>
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

  if (($frags || $kills || $skills || $deaths) && strcmp($weapon, "{$LANG_NONE}")) {
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
  echo "{$LANG_WEAPDATABASEERROR}<br />\n";
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
    <td class="heading" colspan="5" align="center">{$LANG_WEAPONACCURACYINFO}</td>
  </tr>
  <tr>
    <td class="smheading" align="center">{$LANG_WEAPON}</td>
    <td class="smheading" align="center" width="55">{$LANG_FIRED}</td>
    <td class="smheading" align="center" width="55">{$LANG_HITS}</td>
    <td class="smheading" align="center" width="60">{$LANG_DAMAGE}</td>
    <td class="smheading" align="center" width="65">{$LANG_ACCURACY}</td>
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
  echo "{$LANG_WEAPDATABASEERROR}<br />\n";
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
    <td class="heading" colspan="8" align="center">{$LANG_VEHICLETURRETSPECIFICTOTALS}</td>
  </tr>
  <tr>
    <td class="smheading" align="center">{$LANG_VEHICLETURRET}</td>
    <td class="smheading" align="center" width="55">{$LANG_FRAGS}</td>
    <td class="smheading" align="center" width="70">{$LANG_PRIMARYKILLS}</td>
    <td class="smheading" align="center" width="70">{$LANG_SECONDARYKILLS}</td>
    <td class="smheading" align="center" width="55">{$LANG_ROADKILLS}</td>
    <td class="smheading" align="center" width="55">{$LANG_DEATHSIN}</td>
    <td class="smheading" align="center" width="55">{$LANG_SUICIDES}</td>
    <td class="smheading" align="center" width="60">{$LANG_EFF}</td>
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
  echo "{$LANG_WEAPDATABASEERROR}<br />\n";
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
    <td class="heading" colspan="3" align="center">{$LANG_INVASIONMONSTERTOTALS}</td>
  </tr>
  <tr>
    <td class="smheading" align="center">{$LANG_MONSTER}</td>
    <td class="smheading" align="center" width="95">{$LANG_PLAYERSKILLED}</td>
    <td class="smheading" align="center" width="55">{$LANG_DEATHS}</td>
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
  echo "{$LANG_WEAPDATABASEERROR}<br />\n";
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
    <td class="heading" colspan="2" align="center">{$LANG_SUICIDETOTALS}</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="200">{$LANG_TYPE}</td>
    <td class="smheading" align="center" width="60">{$LANG_SUICIDES}</td>
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
    <td class="medheading" align="center" colspan="4">{$LANG_KILLINGSPREESBYTYPE}</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="110">{$LANG_SPREETYPE}</td>
    <td class="smheading" align="center" width="85">{$LANG_NUMOFSPREES}</td>
    <td class="smheading" align="center" width="115">{$LANG_TOTALTIMEMIN}</td>
    <td class="smheading" align="center" width="80">{$LANG_TOTALKILLS}</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_KILLINGSPREE}</td>
    <td class="grey" align="center">$tl_spree1</td>
    <td class="grey" align="center">$time1</td>
    <td class="grey" align="center">$tl_spreek1</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_RAMPAGE}</td>
    <td class="grey" align="center">$tl_spree2</td>
    <td class="grey" align="center">$time2</td>
    <td class="grey" align="center">$tl_spreek2</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_DOMINATING}</td>
    <td class="grey" align="center">$tl_spree3</td>
    <td class="grey" align="center">$time3</td>
    <td class="grey" align="center">$tl_spreek3</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_UNSTOPPABLE}</td>
    <td class="grey" align="center">$tl_spree4</td>
    <td class="grey" align="center">$time4</td>
    <td class="grey" align="center">$tl_spreek4</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_GODLIKE}</td>
    <td class="grey" align="center">$tl_spree5</td>
    <td class="grey" align="center">$time5</td>
    <td class="grey" align="center">$tl_spreek5</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_WICKEDSICK}</td>
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
    <td class="heading" colspan="6" align="center">{$LANG_TOTALITEMSCOLLECTED}</td>
  </tr>
  <tr>
    <td class="smheading" align="center">{$LANG_ITEMTYPE}</td>
    <td class="smheading" align="center" width="35">{$LANG_NO}</td>
    <td class="smheading" align="center">{$LANG_ITEMTYPE}</td>
    <td class="smheading" align="center" width="35">{$LANG_NO}</td>
    <td class="smheading" align="center">{$LANG_ITEMTYPE}</td>
    <td class="smheading" align="center" width="35">{$LANG_NO}</td>
  </tr>

EOF;

$result = sql_queryn($link, "SELECT it_desc,it_pickups FROM {$dbpre}items ORDER BY it_pickups DESC,it_desc ASC");
if (!$result) {
  echo "{$LANG_ERRORLOADINGITEMPICKUPDESC}<br />\n";
  exit;
}

$items = array(array());
$totpickups = 0;
while ($row = sql_fetch_row($result)) {
  for ($i = 0, $item = -1; $i < $totpickups && $item < 0; $i++) {
    if (!strcmp($items[0][$i], $row[0]))
      $item = $i;
  }
  if ($item < 0) {
    $items[0][$totpickups] = $row[0];
    $items[1][$totpickups++] = $row[1];
  }
  else
    $items[1][$item] += $row[1];
}
sql_free_result($result);

if ($totpickups) {
  array_multisort($items[1], SORT_DESC, SORT_NUMERIC,
                  $items[0], SORT_ASC, SORT_STRING);

  $col = 0;
  for ($i = 0; $i < $totpickups; $i++) {
    $item = $items[0][$i];
    $num = $items[1][$i];
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
    }
  }
}

if (!$totpickups) {
  echo <<<EOF
  <tr>
    <td class="dark" align="center" colspan="6">{$LANG_NOITEMPICKUPS}</td>
  </tr>

EOF;
}
else {
  if ($col < 3) {
    while ($col < 3) {
      echo <<<EOF
  <td class="dark" align="center">&nbsp;</td>
  <td class="grey" align="center">&nbsp;</td>

EOF;
      $col++;
    }
    echo "</tr>\n";
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