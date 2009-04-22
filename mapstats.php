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

$mapnum = -1;
check_get($mapnum, "map");
if (!is_numeric($mapnum))
  $mapnum = -1;
if ($mapnum <= 0) {
  echo "Invalid map number.<br />\n";
  echo "Run from the main index program.<br />\n";
  exit;
}

$link = sql_connect();

// Load Map Data
$result = sql_queryn($link, "SELECT * FROM {$dbpre}maps WHERE mp_num=$mapnum LIMIT 1");
if (!$result) {
  echo "Map database error.<br />\n";
  exit;
}
$row = sql_fetch_assoc($result);
sql_free_result($result);
if (!$row) {
  echo "Map not found in database.<br />\n";
  exit;
}
while (list ($key, $val) = each ($row))
  ${$key} = $val;
$mapname = stripspecialchars($mp_name);
$mapdesc = stripspecialchars($mp_desc);
$mapauthor = stripspecialchars($mp_author);
$last = strtotime($mp_lastmatch);
$lastdate = formatdate($last, 1);
$time = sprintf("%0.1f", $mp_time / 360000.0);

echo <<<EOF
<table cellpadding="1" cellspacing="2" border="0" width="720">
  <tr>
    <td class="heading" align="center">Map Stats for $mapname</td>
  </tr>
</table>
<br />

EOF;

$mapimage = strtolower($mapname).".jpg";
if (file_exists("mapimages/$mapimage")) {
  echo <<<EOF
<table cellpadding="0" cellspacing="0" border="0" width="600">
  <tr>
    <td align="center">
      <table cellpadding="1" cellspacing="2" border="0">
        <tr>
          <td class="heading" colspan="4" align="center">Unreal Tournament Map Stats</td>
        </tr>
        <tr>
          <td class="dark" align="center" width="80">Map Name</td>
          <td class="grey" align="center" width="220" colspan="3">$mapname</td>
        </tr>
        <tr>
          <td class="dark" align="center">Map Title</td>
          <td class="grey" align="center" colspan="3">$mapdesc</td>
        </tr>
        <tr>
          <td class="dark" align="center">Author</td>
          <td class="grey" align="center" colspan="3">$mapauthor</td>
        </tr>
        <tr>
          <td class="dark" align="center">Last Match</td>
          <td class="grey" align="center" colspan="3">$lastdate</td>
        </tr>
        <tr>
          <td class="dark" align="center">Game Time</td>
          <td class="grey" align="center" colspan="3">$time hours</td>
        </tr>
        <tr>
          <td style="height:3"></td>
        </tr>
        <tr>
          <td class="dark" align="center">Matches</td>
          <td class="grey" align="center" width="55">$mp_matches</td>
          <td class="dark" align="center" width="75">Kills</td>
          <td class="grey" align="center">$mp_kills</td>
        </tr>
        <tr>
          <td class="dark" align="center">Score</td>
          <td class="grey" align="center">$mp_score</td>
          <td class="dark" align="center">Suicides</td>
          <td class="grey" align="center">$mp_suicides</td>
        </tr>
      </table>
    </td>
    <td width="300" align="center"><img src="mapimages/$mapimage" width="256" height="192" border="1" alt="Map Image" /></td>
  </tr>
</table>

EOF;
}
else {
  echo <<<EOF
<table cellpadding="1" cellspacing="2" border="0">
  <tr>
    <td class="heading" colspan="4" align="center">Unreal Tournament Map Stats</td>
  </tr>
  <tr>
    <td class="dark" align="center" width="80">Map Name</td>
    <td class="grey" align="center" width="250">$mapname</td>
    <td class="dark" align="center" width="75">Matches</td>
    <td class="grey" align="center" width="55">$mp_matches</td>
  </tr>
  <tr>
    <td class="dark" align="center">Map Title</td>
    <td class="grey" align="center">$mapdesc</td>
    <td class="dark" align="center">Score</td>
    <td class="grey" align="center">$mp_score</td>
  </tr>
  <tr>
    <td class="dark" align="center">Author</td>
    <td class="grey" align="center">$mapauthor</td>
    <td class="dark" align="center">Kills</td>
    <td class="grey" align="center">$mp_kills</td>
  </tr>
  <tr>
    <td class="dark" align="center">Last Match</td>
    <td class="grey" align="center">$lastdate</td>
    <td class="dark" align="center">Suicides</td>
    <td class="grey" align="center">$mp_suicides</td>
  </tr>
  <tr>
    <td class="dark" align="center">Game Time</td>
    <td class="grey" align="center">$time hours</td>
    <td class="dark" align="center">&nbsp;</td>
    <td class="grey" align="center">&nbsp;</td>
  </tr>
</table>

EOF;
}

//=============================================================================
//========== Assault Statistics ===============================================
//=============================================================================

$result = sql_queryn($link, "SELECT * FROM {$dbpre}objectives WHERE obj_map=$mapnum");
if (!$result) {
  echo "Objective database error.<br />\n";
  exit;
}
$heading = 0;
while ($row = sql_fetch_assoc($result)) {
  if (!$heading) {
    echo <<<EOF
<br />
<table cellpadding="1" cellspacing="2" border="0">
  <tr>
    <td class="heading" colspan="5" align="center">Assault Objectives</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="20">#</td>
    <td class="smheading" align="center" width="270">Objective</td>
    <td class="smheading" align="center" width="75">Times Completed</td>
    <td class="smheading" align="center" width="55">Best Time</td>
    <td class="smheading" align="center" width="55">Avg. Time</td>
  </tr>

EOF;
    $heading = 1;
  }

  while (list($key,$val) = each($row))
    ${$key} = $val;
  $desc = stripspecialchars($obj_desc);
  $besttime = sprintf("%d:%02d", floor($obj_besttime / 6000), intval(fmod($obj_besttime, 6000)));
  $avgtime = sprintf("%d:%02d", floor($obj_avgtime / 6000), intval(fmod($obj_avgtime, 6000)));

  echo <<<EOF
  <tr>
    <td class="grey" align="center">$obj_priority</td>
    <td class="grey" align="center">$desc</td>
    <td class="grey" align="center">$obj_times</td>
    <td class="grey" align="center">$besttime</td>
    <td class="grey" align="center">$avgtime</td>
  </tr>

EOF;
}
sql_free_result($result);
if ($heading)
  echo "</table>\n";

//=============================================================================
//========== Weapon Specific Totals ===========================================
//=============================================================================
echo <<<EOF
<br />
<table cellpadding="1" cellspacing="2" border="0" width="540">
  <tr>
    <td class="heading" colspan="7" align="center">Weapon Specific Totals</td>
  </tr>
  <tr>
    <td class="smheading" align="center">Weapon</td>
    <td class="smheading" align="center" width="55">Frags</td>
    <td class="smheading" align="center" width="55">Primary Kills</td>
    <td class="smheading" align="center" width="70">Secondary Kills</td>
    <td class="smheading" align="center" width="55">Deaths Holding</td>
    <td class="smheading" align="center" width="55">Suicides</td>
    <td class="smheading" align="center" width="55">Eff.</td>
  </tr>

EOF;

// Load Weapon Descriptions
$result = sql_queryn($link, "SELECT wp_num,wp_secondary,wp_desc,wp_weaptype FROM {$dbpre}weapons");
if (!$result) {
  echo "Error loading weapons descriptions.<br />\n";
  exit;
}
$maxweapon = 0;
$weapons = array();
while($row = sql_fetch_row($result)) {
  $num = $row[0];
  $weapons[$num][0] = $row[2];
  $weapons[$num][1] = $row[1];
  $weapons[$num][2] = $row[3];
  if ($num > $maxweapon)
    $maxweapon = $num;
}
sql_free_result($result);

$wskills = array(array());
/* wskills:
 0 = Primary Kills
 1 = Secondary Kills
 2 = Deaths From
 3 = Deaths Holding
 4 = Suicides
 5 = Weapon Description
 6 = Frags
 7 = Weapon Type
*/
$numweapons = 0;
// Load Map Weapon Kills for current map
$result = sql_queryn($link, "SELECT mwk_weapon,mwk_kills,mwk_deaths,mwk_held,mwk_suicides FROM {$dbpre}mwkills WHERE mwk_map=$mapnum");
while (list($weapon,$kills,$deaths,$held,$suicides) = sql_fetch_row($result)) {
  if ($kills || $deaths || $held || $suicides) {
    $frags = $kills - $suicides;
    // Look for existing matching wskills description
    $weap = -1;
    $secondary = 0;
    for ($i = 0; $i < $numweapons && $weap < 0; $i++) {
      if (!strcmp($wskills[5][$i], $weapons[$weapon][0])) {
        $weap = $i;
        $secondary = $weapons[$weapon][1];
      }
    }
    // Add weapon if not already used
    if ($weap < 0) {
      $wskills[0][$numweapons] = $wskills[1][$numweapons] = 0; // Primary Kills / Secondary Kills
      $wskills[2][$numweapons] = $wskills[3][$numweapons] = 0; // Deaths From / Deaths Holding
      $wskills[4][$numweapons] = 0; // Suicides
      $wskills[5][$numweapons] = $weapons[$weapon][0]; // Description
      $wskills[6][$numweapons] = 0; // Frags
      $wskills[7][$numweapons] = $weapons[$weapon][2]; // Type
      $weap = $numweapons++;
      $secondary = $weapons[$weapon][1];
    }
    $wskills[6][$weap] += $frags;
    if ($secondary)
      $wskills[1][$weap] += $kills;
    else
      $wskills[0][$weap] += $kills;
    $wskills[2][$weap] += $deaths;
    $wskills[3][$weap] += $held;
    $wskills[4][$weap] += $suicides;
  }
}
sql_free_result($result);

if ($numweapons > 0) {
  // Sort by frags, kills, secondary kills, deaths holding, deaths from, suicides, description
  array_multisort($wskills[6], SORT_DESC, SORT_NUMERIC,
                  $wskills[0], SORT_DESC, SORT_NUMERIC,
                  $wskills[1], SORT_DESC, SORT_NUMERIC,
                  $wskills[3], SORT_ASC, SORT_NUMERIC,
                  $wskills[2], SORT_ASC, SORT_NUMERIC,
                  $wskills[4], SORT_ASC, SORT_NUMERIC,
                  $wskills[5], SORT_ASC, SORT_STRING,
                  $wskills[7], SORT_ASC, SORT_NUMERIC);

  for ($i = 0; $i < $numweapons; $i++) {
    if ($wskills[7][$i])
      continue;
    $weapon = $wskills[5][$i];
    $kills = $wskills[0][$i];
    $skills = $wskills[1][$i];
    $deaths = $wskills[2][$i];
    $held = $wskills[3][$i];
    $suicides = $wskills[4][$i];
    $frags = $wskills[6][$i];

    if ($kills + $skills + $held + $suicides == 0)
      $eff = "0.0";
    else
      $eff = sprintf("%0.1f", (($kills + $skills) / ($kills + $skills + $held + $suicides)) * 100.0);

    if ($kills || $skills || $held) {
      echo <<< EOF
  <tr>
    <td class="dark" align="center">$weapon</td>
    <td class="grey" align="center">$frags</td>
    <td class="grey" align="center">$kills</td>
    <td class="grey" align="center">$skills</td>
    <td class="grey" align="center">$held</td>
    <td class="grey" align="center">$suicides</td>
    <td class="grey" align="center">$eff%</td>
  </tr>

EOF;
    }
  }
}
echo "</table>\n";

//=============================================================================
//========== Vehicle and Turret Specific Totals ===============================
//=============================================================================
echo <<<EOF
<br />
<table cellpadding="1" cellspacing="2" border="0" width="540">
  <tr>
    <td class="heading" colspan="7" align="center">Vehicle and Turret Specific Totals</td>
  </tr>
  <tr>
    <td class="smheading" align="center">Vehicle/Turret</td>
    <td class="smheading" align="center" width="55">Frags</td>
    <td class="smheading" align="center" width="55">Primary Kills</td>
    <td class="smheading" align="center" width="70">Secondary Kills</td>
    <td class="smheading" align="center" width="55">Deaths In</td>
    <td class="smheading" align="center" width="55">Suicides</td>
    <td class="smheading" align="center" width="55">Eff.</td>
  </tr>

EOF;

if ($numweapons > 0) {
  // Sort by frags, kills, secondary kills, deaths holding, deaths from, suicides, description
  array_multisort($wskills[6], SORT_DESC, SORT_NUMERIC,
                  $wskills[0], SORT_DESC, SORT_NUMERIC,
                  $wskills[1], SORT_DESC, SORT_NUMERIC,
                  $wskills[3], SORT_ASC, SORT_NUMERIC,
                  $wskills[2], SORT_ASC, SORT_NUMERIC,
                  $wskills[4], SORT_ASC, SORT_NUMERIC,
                  $wskills[5], SORT_ASC, SORT_STRING,
                  $wskills[7], SORT_ASC, SORT_NUMERIC);

  for ($i = 0; $i < $numweapons; $i++) {
    if ($wskills[7][$i] < 1 || $wskills[7][$i] > 2)
      continue;
    $weapon = $wskills[5][$i];
    $kills = $wskills[0][$i];
    $skills = $wskills[1][$i];
    $deaths = $wskills[2][$i];
    $held = $wskills[3][$i];
    $suicides = $wskills[4][$i];
    $frags = $wskills[6][$i];

    if ($kills + $skills + $held + $suicides == 0)
      $eff = "0.0";
    else
      $eff = sprintf("%0.1f", (($kills + $skills) / ($kills + $skills + $held + $suicides)) * 100.0);

    if ($kills || $skills || $deaths || $held) {
      echo <<< EOF
  <tr>
    <td class="dark" align="center">$weapon</td>
    <td class="grey" align="center">$frags</td>
    <td class="grey" align="center">$kills</td>
    <td class="grey" align="center">$skills</td>
    <td class="grey" align="center">$held</td>
    <td class="grey" align="center">$suicides</td>
    <td class="grey" align="center">$eff%</td>
  </tr>

EOF;
    }
  }
}
echo "</table>\n";

//=============================================================================
//========== Invasion Monster Totals ==========================================
//=============================================================================
echo <<<EOF
<br />
<table cellpadding="1" cellspacing="2" border="0" width="340">
  <tr>
    <td class="heading" colspan="3" align="center">Invasion Monster Totals</td>
  </tr>
  <tr>
    <td class="smheading" align="center">Monster</td>
    <td class="smheading" align="center" width="95">Players Killed</td>
    <td class="smheading" align="center" width="55">Deaths</td>
  </tr>

EOF;

if ($numweapons > 0) {
  // Sort by kills, deaths, description
  array_multisort($wskills[0], SORT_DESC, SORT_NUMERIC,
                  $wskills[2], SORT_ASC, SORT_NUMERIC,
                  $wskills[5], SORT_ASC, SORT_STRING,
                  $wskills[6], SORT_ASC, SORT_NUMERIC,
                  $wskills[1], SORT_ASC, SORT_NUMERIC,
                  $wskills[4], SORT_ASC, SORT_NUMERIC,
                  $wskills[3], SORT_ASC, SORT_NUMERIC,
                  $wskills[7], SORT_ASC, SORT_NUMERIC);

  for ($i = 0; $i < $numweapons; $i++) {
    if ($wskills[7][$i] != 3)
      continue;
    $weapon = $wskills[5][$i];
    $kills = $wskills[0][$i];
    $deaths = $wskills[2][$i];

    if ($deaths) {
      echo <<< EOF
  <tr>
    <td class="dark" align="center">$weapon</td>
    <td class="grey" align="center">$kills</td>
    <td class="grey" align="center">$deaths</td>
  </tr>

EOF;
    }
  }
}
echo "</table>\n";

//=============================================================================
//========== Most Recent Matches Played =======================================
//=============================================================================

// Load game types
$numtypes = 0;
$result = sql_queryn($link, "SELECT tp_num,tp_desc FROM {$dbpre}type");
while($row = sql_fetch_row($result))
  $gtype[$numtypes++] = $row;
sql_free_result($result);

echo <<<EOF
<br />
<table cellpadding="1" cellspacing="2" border="0">
  <tr>
    <td class="heading" colspan="5" align="center">Most Recent Matches Played</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="220">Date</td>
    <td class="smheading" align="center" width="150">Match Type</td>
    <td class="smheading" align="center" width="225">Server</td>
    <td class="smheading" align="center" width="50">Players</td>
    <td class="smheading" align="center" width="50">Minutes</td>
  </tr>

EOF;

$matches = 0;
$result = sql_queryn($link, "SELECT gm_num,gm_server,gm_type,gm_start,gm_timeoffset,gm_length,gm_numplayers,sv_name,sv_shortname
                       FROM {$dbpre}matches,{$dbpre}servers
                       WHERE gm_map=$mapnum AND {$dbpre}servers.sv_num=gm_server
                       ORDER BY gm_num DESC LIMIT 21");
if (!$result) {
  echo "Error accessing match database.<br />\n";
  exit;
}
while ($row = sql_fetch_assoc($result)) {
  if ($matches < 20) {
    while (list ($key, $val) = each ($row))
      ${$key} = $val;

    $gametype = "";
    for ($i = 0; $i < $numtypes && !$gametype; $i++) {
      if ($gtype[$i][0] == $gm_type)
        $gametype = $gtype[$i][1];
    }
    $start = strtotime($gm_start);
    $matchdate = formatdate($start, 1);
    $length = sprintf("%0.1f", $gm_length / (60.0 * $gm_timeoffset));
    if ($useshortname && $sv_shortname != "")
      $servername = stripspecialchars($sv_shortname);
    else
      $servername = stripspecialchars($sv_name);

    echo <<<EOF
  <tr>
    <td class="dark" align="center"><a class="dark" href="matchstats.php?match=$gm_num">$matchdate</a></td>
    <td class="grey" align="center">$gametype</td>
    <td class="grey" align="center"><a class="grey" href="serverstats.php?server=$gm_server">$servername</a></td>
    <td class="grey" align="center">$gm_numplayers</td>
    <td class="grey" align="center">$length</td>
  </tr>

EOF;
  }
  $matches++;
}
sql_free_result($result);
if ($matches > 20) {
  echo <<< EOF
  <tr>
    <td class="smheading" colspan="5" align="center"><a href="typematches.php?map=$mapnum" class="smheading">[Show All Matches For Map]</a></td>
  </tr>

EOF;
}
echo <<<EOF
</table>
</td></tr></table>

</body>
</html>

EOF;
sql_close($link);

?>