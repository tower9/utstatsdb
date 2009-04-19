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

function get_rankpos($type, $rank)
{
  global $link, $dbpre;

  $type = strtolower($type);
  if ($rank <= 0)
    $pos = 0;
  else {
    $result = sql_queryn($link, "SELECT COUNT(*) FROM {$dbpre}playersgt WHERE gt_tnum=$type AND gt_rank>0 AND gt_rank>$rank");
    if (!$result) {
      echo "Player Database Error.<br />\n";
      exit;
    }
    $row = sql_fetch_row($result);
    sql_free_result($result);
    if (!$row) {
      echo "Error retrieving rank position..<br />\n";
      exit;
    }
    $pos = intval($row[0]) + 1;
  }

  return $pos;
}

$plr = -1;
check_get($plr, "player");
if (!is_numeric($plr))
  $plr = -1;

if ($plr <= 0) {
  echo "Invalid player number.<br />\n";
  echo "Run from the main index program.<br />\n";
  exit;
}

$link = sql_connect();
$result = sql_queryn($link, "SELECT * FROM {$dbpre}players WHERE pnum=$plr LIMIT 1");
if (!$result) {
  echo "Player Database Error.<br />\n";
  exit;
}
$row = sql_fetch_assoc($result);
sql_free_result($result);
if (!$row) {
  echo "Player not found in database.<br />\n";
  exit;
}
while (list ($key, $val) = each ($row))
  ${$key} = $val;

$plrname = stripspecialchars($plr_name);

if ($plr_bot)
  $botplayer = " (bot)";
else
  $botplayer = "";

$total_time = 0;
$total_score = 0;
$total_teamkills = $total_teamdeaths = 0;
$total_wins = $total_losses = $total_matches = 0;

// Load game type descriptions
$result = sql_queryn($link, "SELECT tp_desc,tp_num FROM {$dbpre}type");
if (!$result) {
  echo "Database error accessing game types.<br />\n";
  exit;
}
while ($row = sql_fetch_row($result))
  $gtype[$row[1]] = $row[0];
sql_free_result($result);

echo <<<EOF
<center>
<table cellpadding="1" cellspacing="2" border="0" width="710" class="box">
  <tr>
<td class="heading" align="center" colspan="15">Career Summary for $plrname{$botplayer} [$pnum]</td>
  </tr>
  <tr>
    <td class="smheading" align="center">Game Type</td>
    <td class="smheading" align="center">Score</td>
    <td class="smheading" align="center">F</td>
    <td class="smheading" align="center">K</td>
    <td class="smheading" align="center">D</td>
    <td class="smheading" align="center">S</td>
    <td class="smheading" align="center">TK</td>
    <td class="smheading" align="center">TD</td>
    <td class="smheading" align="center">Eff</td>
    <td class="smheading" align="center">Avg FPH</td>
    <td class="smheading" align="center">Avg TTL</td>
    <td class="smheading" align="center">Wins</td>
    <td class="smheading" align="center">Losses</td>
    <td class="smheading" align="center">Matches</td>
    <td class="smheading" align="center">Hours</td>
  </tr>

EOF;

$result = sql_queryn($link, "SELECT * FROM {$dbpre}playersgt WHERE gt_pnum=$plr ORDER BY gt_tnum");
if (!$result) {
  echo "Player Database Error.<br />\n";
  exit;
}
$gametypes = 0;
while ($row = sql_fetch_assoc($result)) {
  $gametypes++;
  while (list ($key, $val) = each ($row))
    ${$key} = $val;

  $time = floatval($gt_time / 100.0);
  $hours = sprintf("%0.1f", $time / 3600.0);
  if ($gt_kills + $gt_deaths + $gt_suicides == 0)
    $eff = "0.0";
  else
    $eff = sprintf("%0.1f", ($gt_kills / ($gt_kills + $gt_deaths + $gt_suicides)) * 100.0);
  if ($gt_time == 0)
    $fph = "0.0";
  else
    $fph = sprintf("%0.1f", $gt_frags / ($time / 3600));
  $ttl = sprintf("%0.1f", $time / ($gt_deaths + $gt_suicides + 1));

  $total_time += $time;
  $total_score += $gt_score;
  $total_teamkills += $gt_teamkills;
  $total_teamdeaths += $gt_teamdeaths;
  $total_wins += $gt_wins;
  $total_losses += $gt_losses;
  $total_matches += $gt_matches;

  $typename = $gtype[$gt_tnum];

  echo <<<EOF
  <tr>
    <td class="dark" align="center">$typename</td>
    <td class="grey" align="center">$gt_score</td>
    <td class="grey" align="center">$gt_frags</td>
    <td class="grey" align="center">$gt_kills</td>
    <td class="grey" align="center">$gt_deaths</td>
    <td class="grey" align="center">$gt_suicides</td>
    <td class="grey" align="center">$gt_teamkills</td>
    <td class="grey" align="center">$gt_teamkills</td>
    <td class="grey" align="center">$eff</td>
    <td class="grey" align="center">$fph</td>
    <td class="grey" align="center">$ttl</td>
    <td class="grey" align="center">$gt_wins</td>
    <td class="grey" align="center">$gt_losses</td>
    <td class="grey" align="center">$gt_matches</td>
    <td class="grey" align="center">$hours</td>
  </tr>

EOF;
}
sql_free_result($result);

if (!$gametypes) {
  echo "No gametype data for player.<br />\n";
  exit;
}

$total_hours = sprintf("%0.1f", $total_time / 3600);
if ($plr_kills + $plr_deaths + $plr_suicides == 0)
  $total_eff = "0.0";
else
  $total_eff = sprintf("%0.1f", ($plr_kills / ($plr_kills + $plr_deaths + $plr_suicides)) * 100.0);
if ($total_time == 0)
  $total_fph = "0.0";
else
  $total_fph = sprintf("%0.1f", $plr_frags / ($total_time / 3600));
$total_ttl = sprintf("%0.1f", $total_time / ($plr_deaths + $plr_suicides + 1));

echo <<<EOF
  <tr>
    <td class="dark" align="center">Totals</td>
    <td class="darkgrey" align="center">$total_score</td>
    <td class="darkgrey" align="center">$plr_frags</td>
    <td class="darkgrey" align="center">$plr_kills</td>
    <td class="darkgrey" align="center">$plr_deaths</td>
    <td class="darkgrey" align="center">$plr_suicides</td>
    <td class="darkgrey" align="center">$total_teamkills</td>
    <td class="darkgrey" align="center">$total_teamdeaths</td>
    <td class="darkgrey" align="center">$total_eff</td>
    <td class="darkgrey" align="center">$total_fph</td>
    <td class="darkgrey" align="center">$total_ttl</td>
    <td class="darkgrey" align="center">$total_wins</td>
    <td class="darkgrey" align="center">$total_losses</td>
    <td class="darkgrey" align="center">$total_matches</td>
    <td class="darkgrey" align="center">$total_hours</td>
  </tr>
</table>

EOF;

//=============================================================================
//========== Ranking ==========================================================
//=============================================================================
if (isset($ranksystem) && $ranksystem) {
  echo <<<EOF
<br />
<table cellpadding="1" cellspacing="2" border="0">
  <tr>
    <td class="medheading" align="center" colspan="3">Ranking</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="140">Game Type</td>
    <td class="smheading" align="center" width="60">Ranking</td>
    <td class="smheading" align="center" width="60">Points</td>
  </tr>

EOF;

  $result = sql_queryn($link, "SELECT gt_tnum,gt_rank FROM {$dbpre}playersgt WHERE gt_pnum=$plr ORDER BY gt_tnum");
  if (!$result) {
    echo "Player Database Error.<br />\n";
    exit;
  }
  $ranks = 0;
  while ($row = sql_fetch_row($result)) {
    $rankarrayt[$ranks] = $row[0];
    $rankarray[$ranks++] = $row[1];
  }  	
  sql_free_result($result);

  for ($i = 0; $i < $ranks; $i++) {
    $rankp = sprintf("%0.2f", $rankarray[$i]);
    $rpos = get_rankpos($rankarrayt[$i], $rankarray[$i]);
    $typename = $gtype[$rankarrayt[$i]];

  echo <<<EOF
  <tr>
    <td class="dark" align="center">$typename</td>
    <td class="grey" align="center">$rpos</td>
    <td class="grey" align="center">$rankp</td>
  </tr>

EOF;
  }
  echo "</table>\n";
}

//=============================================================================
//========== Capture the Flag Events Summary ==================================
//=============================================================================
$result = sql_queryn($link, "SELECT gt_capcarry,gt_drop,gt_pickup,gt_return,gt_typekill,gt_assist,gt_holdtime FROM {$dbpre}playersgt WHERE gt_pnum=$plr AND gt_type=2");
if (!$result) {
  echo "Player Database Error.<br />\n";
  exit;
}
$flagcapture = $flagassist = $flagkill = $flagreturn = $flagpickup = $flagdrop = $holdtime = 0;
while ($row = sql_fetch_row($result)) {
  $flagcapture += $row[0];
  $flagassist += $row[5];
  $flagkill += $row[4];
  $flagreturn += $row[3];
  $flagpickup += $row[2];
  $flagdrop += $row[1];
  $holdtime += $row[6];
}
sql_free_result($result);

$flagtime = sprintf("%0.1f", $holdtime / 6000.0);
echo <<<EOF
<br />
<table cellpadding="1" cellspacing="2" border="0">
  <tr>
    <td class="medheading" align="center" colspan="7">Capture the Flag Events Summary</td>
  </tr>
  <tr>
    <td class="dark" align="center" width="65">Captures</td>
    <td class="dark" align="center" width="60">Assists</td>
    <td class="dark" align="center" width="60">Kills</td>
    <td class="dark" align="center" width="60">Saves</td>
    <td class="dark" align="center" width="60">Pickups</td>
    <td class="dark" align="center" width="60">Drops</td>
    <td class="dark" align="center" width="60">Carry Time</td>
  </tr>
  <tr>
    <td class="grey" align="center">$flagcapture</td>
    <td class="grey" align="center">$flagassist</td>
    <td class="grey" align="center">$flagkill</td>
    <td class="grey" align="center">$flagreturn</td>
    <td class="grey" align="center">$flagpickup</td>
    <td class="grey" align="center">$flagdrop</td>
    <td class="grey" align="center">$flagtime</td>
  </tr>
</table>
EOF;

//=============================================================================
//========== Bombing Run and Double Domination Events Summary =================
//=============================================================================
$result = sql_queryn($link, "SELECT gt_capcarry,gt_tossed,gt_drop,gt_typekill,gt_assist,gt_holdtime FROM {$dbpre}playersgt WHERE gt_pnum=$plr AND gt_type=3");
if (!$result) {
  echo "Player Database Error.<br />\n";
  exit;
}
$bombcarried = $bombtossed = $bombassist = $bombkill = $bombdrop = $holdtime = 0;
while ($row = sql_fetch_row($result)) {
  $bombcarried += $row[0];
  $bombtossed += $row[1];
  $bombassist += $row[4];
  $bombkill += $row[3];
  $bombdrop += $row[2];
  $holdtime += $row[5];
}
sql_free_result($result);

$bombtime = sprintf("%0.1f", $holdtime / 6000.0);

$result = sql_queryn($link, "SELECT gt_capcarry FROM {$dbpre}playersgt WHERE gt_pnum=$plr AND gt_type=7");
if (!$result) {
  echo "Player Database Error.<br />\n";
  exit;
}
$cpcapture = 0;
while ($row = sql_fetch_row($result))
  $cpcapture += $row[0];
sql_free_result($result);

echo <<<EOF
<br />
<table cellpadding="1" cellspacing="2" border="0">
  <tr>
    <td class="medheading" align="center" colspan="7">Bombing Run and Double Domination Events Summary</td>
  </tr>
  <tr>
    <td class="dark" align="center" width="60">Carried</td>
    <td class="dark" align="center" width="60">Tossed</td>
    <td class="dark" align="center" width="60">Assists</td>
    <td class="dark" align="center" width="60">Crticial Kills</td>
    <td class="dark" align="center" width="60">Drops</td>
    <td class="dark" align="center" width="60">Carry Time</td>
    <td class="dark" align="center" width="90">Control Point Captures</td>
  </tr>
  <tr>
    <td class="grey" align="center">$bombcarried</td>
    <td class="grey" align="center">$bombtossed</td>
    <td class="grey" align="center">$bombassist</td>
    <td class="grey" align="center">$bombkill</td>
    <td class="grey" align="center">$bombdrop</td>
    <td class="grey" align="center">$bombtime</td>
    <td class="grey" align="center">$cpcapture</td>
  </tr>
</table>
EOF;

//=============================================================================
//========== Assault & Onslaught Events Summary ===============================
//=============================================================================
$result = sql_queryn($link, "SELECT gt_capcarry FROM {$dbpre}playersgt WHERE gt_pnum=$plr AND gt_type=5");
if (!$result) {
  echo "Player Database Error.<br />\n";
  exit;
}
$objectives = 0;
while ($row = sql_fetch_row($result))
  $objectives += $row[0];
sql_free_result($result);

$result = sql_queryn($link, "SELECT gt_capcarry,gt_drop,gt_pickup,gt_taken FROM {$dbpre}playersgt WHERE gt_pnum=$plr AND gt_type=6");
if (!$result) {
  echo "Player Database Error.<br />\n";
  exit;
}
$nodeconstructed = $nodedestroyed = $nodeconstdestroyed = $coredestroyed = 0;
while ($row = sql_fetch_row($result)) {
  $nodeconstructed = $row[2];
  $nodedestroyed = $row[3];
  $nodeconstdestroyed = $row[1];
  $coredestroyed = $row[0];
}
sql_free_result($result);

echo <<<EOF
<br />
<table cellpadding="1" cellspacing="2" border="0">
  <tr>
    <td class="medheading" align="center" colspan="5">Assault &amp; Onslaught Events Summary</td>
  </tr>
  <tr>
    <td class="dark" align="center" width="90">Assault Objectives</td>
    <td class="dark" align="center" width="90">Power Nodes Constructed</td>
    <td class="dark" align="center" width="90">Power Nodes Destroyed</td>
    <td class="dark" align="center" width="120">Constructing Nodes Destroyed</td>
    <td class="dark" align="center" width="90">Power Cores Destroyed</td>
  </tr>
  <tr>
    <td class="grey" align="center">$objectives</td>
    <td class="grey" align="center">$nodeconstructed</td>
    <td class="grey" align="center">$nodedestroyed</td>
    <td class="grey" align="center">$nodeconstdestroyed</td>
    <td class="grey" align="center">$coredestroyed</td>
  </tr>
</table>
EOF;

//=============================================================================
//========== Career Summary - Single Player Tournament Games ==================
//=============================================================================

//=============================================================================
//========== Special Events ===================================================
//=============================================================================
echo <<<EOF
<br />
<table cellpadding="1" cellspacing="2" border="0">
  <tr>
    <td class="heading" colspan="8" align="center">Special Events</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="95">Category</td>
    <td class="smheading" align="center" width="45">Value</td>
    <td class="smheading" align="center" width="100">Category</td>
    <td class="smheading" align="center" width="45">Value</td>
    <td class="smheading" align="center" width="95">Category</td>
    <td class="smheading" align="center" width="45">Value</td>
    <td class="smheading" align="center" width="100">Category</td>
    <td class="smheading" align="center" width="45">Value</td>
  </tr>
  <tr>
    <td class="dark" align="center">First Blood</td>
    <td class="grey" align="center">$plr_firstblood</td>
    <td class="dark" align="center">Head Shots</td>
    <td class="grey" align="center">$plr_headshots</td>
    <td class="dark" align="center">Roadkills</td>
    <td class="grey" align="center">$plr_roadkills</td>
    <td class="dark" align="center">Carjackings</td>
    <td class="grey" align="center">$plr_carjack</td>
  </tr>
  <tr>
    <td class="dark" align="center">Double Kills</td>
    <td class="grey" align="center">$plr_multi1</td>
    <td class="dark" align="center">Multi Kills</td>
    <td class="grey" align="center">$plr_multi2</td>
    <td class="dark" align="center">Mega Kills</td>
    <td class="grey" align="center">$plr_multi3</td>
    <td class="dark" align="center">Ultra Kills</td>
    <td class="grey" align="center">$plr_multi4</td>
  </tr>
  <tr>
    <td class="dark" align="center">Monster Kills</td>
    <td class="grey" align="center">$plr_multi5</td>
    <td class="dark" align="center">Ludicrous Kills</td>
    <td class="grey" align="center">$plr_multi6</td>
    <td class="dark" align="center">Holy Shit Kills</td>
    <td class="grey" align="center">$plr_multi7</td>
    <td class="dark" align="center">Failed Transloc</td>
    <td class="grey" align="center">$plr_transgib</td>
  </tr>
  <tr>
    <td class="dark" align="center">Headhunter</td>
    <td class="grey" align="center">$plr_headhunter</td>
    <td class="dark" align="center">Flak Monkey</td>
    <td class="grey" align="center">$plr_flakmonkey</td>
    <td class="dark" align="center">Combo Whore</td>
    <td class="grey" align="center">$plr_combowhore</td>
    <td class="dark" align="center">Road Rampage</td>
    <td class="grey" align="center">$plr_roadrampage</td>
  </tr>
</table>

EOF;

//=============================================================================
//========== Combos ===========================================================
//=============================================================================
echo <<<EOF
<br />
<table cellpadding="1" cellspacing="2" border="0">
  <tr>
    <td class="heading" colspan="8" align="center">Combos Used</td>
  </tr>
  <tr>
    <td class="dark" align="center" width="60">Speed</td>
    <td class="grey" align="center" width="35">$plr_combo1</td>
    <td class="dark" align="center" width="60">Booster</td>
    <td class="grey" align="center" width="35">$plr_combo2</td>
    <td class="dark" align="center" width="60">Invisible</td>
    <td class="grey" align="center" width="35">$plr_combo3</td>
    <td class="dark" align="center" width="60">Berzerk</td>
    <td class="grey" align="center" width="35">$plr_combo4</td>
  </tr>
</table>
EOF;

//=============================================================================
//========== RPG Stats ========================================================
//=============================================================================
if ($rpgini != "" && $plr_rpg) {
  include("includes/rpgplayer.php");
  if ($plr_bot)
    rpg_stats("Bot", $plr_name);
  else
    rpg_stats($plr_key, $plr_name);
}

//=============================================================================
//========== Weapon Specific Totals ===========================================
//=============================================================================
echo <<<EOF
<br />
<table cellpadding="1" cellspacing="2" border="0" width="595">
  <tr>
    <td class="heading" colspan="8" align="center">Weapon Specific Totals</td>
  </tr>
  <tr>
    <td class="smheading" align="center">Weapon</td>
    <td class="smheading" align="center" width="55">Frags</td>
    <td class="smheading" align="center" width="55">Primary Kills</td>
    <td class="smheading" align="center" width="70">Secondary Kills</td>
    <td class="smheading" align="center" width="55">Deaths From</td>
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
 8 = Road Kills
 9 = Fired
 10 = Hits
 11 = Damage
*/
$numweapons = 0;
// Load Player Weapon Kills for current player
$result = sql_queryn($link, "SELECT pwk_weapon,pwk_frags,pwk_kills,pwk_deaths,pwk_held,pwk_suicides,pwk_fired,pwk_hits,pwk_damage FROM {$dbpre}pwkills WHERE pwk_player=$pnum");
while (list($weapon,$frags,$kills,$deaths,$held,$suicides,$fired,$hits,$damage) = sql_fetch_row($result)) {
  if ($frags || $kills || $deaths || $held || $suicides || $fired || $hits || $damage) {
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
      $wskills[8][$numweapons] = 0; // Road Kills
      $wskills[9][$numweapons] = 0; // Fired
      $wskills[10][$numweapons] = 0; // Hits
      $wskills[11][$numweapons] = 0; // Damage
      $weap = $numweapons++;
      $secondary = $weapons[$weapon][1];
    }
    $wskills[6][$weap] += $frags;
    if ($secondary == 4)
      $wskills[8][$weap] += $kills; // Road Kills
    else if ($secondary)
      $wskills[1][$weap] += $kills;
    else
      $wskills[0][$weap] += $kills;
    $wskills[2][$weap] += $deaths;
    $wskills[3][$weap] += $held;
    $wskills[4][$weap] += $suicides;
    $wskills[9][$weap] += $fired;
    $wskills[10][$weap] += $hits;
    $wskills[11][$weap] += $damage;
  }
}
sql_free_result($result);

if ($numweapons > 0) {
  // Sort by frags,kills,secondary kills,deaths holding,deaths from,suicides,description,road kills
  array_multisort($wskills[6], SORT_DESC, SORT_NUMERIC,
                  $wskills[0], SORT_DESC, SORT_NUMERIC,
                  $wskills[1], SORT_DESC, SORT_NUMERIC,
                  $wskills[3], SORT_ASC, SORT_NUMERIC,
                  $wskills[2], SORT_ASC, SORT_NUMERIC,
                  $wskills[4], SORT_ASC, SORT_NUMERIC,
                  $wskills[5], SORT_ASC, SORT_STRING,
                  $wskills[7], SORT_ASC, SORT_NUMERIC,
                  $wskills[8], SORT_ASC, SORT_NUMERIC,
                  $wskills[9], SORT_ASC, SORT_NUMERIC,
                  $wskills[10], SORT_ASC, SORT_NUMERIC,
                  $wskills[11], SORT_ASC, SORT_NUMERIC);

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

    if ($kills || $skills || $deaths || $held) {
      echo <<< EOF
  <tr>
    <td class="dark" align="center">$weapon</td>
    <td class="grey" align="center">$frags</td>
    <td class="grey" align="center">$kills</td>
    <td class="grey" align="center">$skills</td>
    <td class="grey" align="center">$deaths</td>
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
//========== Weapon Accuracy Information ======================================
//=============================================================================
echo <<<EOF
<br />
<table cellpadding="1" cellspacing="2" border="0" width="440">
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

if ($numweapons > 0) {
  // Sort by fired,hits,damage,description
  array_multisort($wskills[9], SORT_DESC, SORT_NUMERIC,
                $wskills[10], SORT_DESC, SORT_NUMERIC,
                $wskills[11], SORT_DESC, SORT_NUMERIC,
                $wskills[5], SORT_ASC, SORT_STRING,
                $wskills[6], SORT_DESC, SORT_NUMERIC,
                $wskills[0], SORT_DESC, SORT_NUMERIC,
                $wskills[1], SORT_DESC, SORT_NUMERIC,
                $wskills[3], SORT_ASC, SORT_NUMERIC,
                $wskills[2], SORT_ASC, SORT_NUMERIC,
                $wskills[4], SORT_ASC, SORT_NUMERIC,
                $wskills[7], SORT_ASC, SORT_NUMERIC,
                $wskills[8], SORT_ASC, SORT_NUMERIC);

  for ($i = 0; $i < $numweapons; $i++) {
    if ($wskills[7][$i])
      continue;
    $weapon = $wskills[5][$i];
    $fired = $wskills[9][$i];
    $hits = $wskills[10][$i];
    $damage = $wskills[11][$i];
    if ($fired == 0)
      $acc = "0.0";
    else
      $acc = sprintf("%0.1f", ($hits / $fired) * 100.0);

    if ($fired || $hits || $damage) {
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
  }
}
echo "</table>\n";

//=============================================================================
//========== Vehicle and Turret Specific Totals ===============================
//=============================================================================
echo <<<EOF
<br />
<table cellpadding="1" cellspacing="2" border="0" width="670">
  <tr>
    <td class="heading" colspan="9" align="center">Vehicle and Turret Specific Totals</td>
  </tr>
  <tr>
    <td class="smheading" align="center">Vehicle/Turret</td>
    <td class="smheading" align="center" width="55">Frags</td>
    <td class="smheading" align="center" width="55">Primary Kills</td>
    <td class="smheading" align="center" width="60">Secondary Kills</td>
    <td class="smheading" align="center" width="55">Road Kills</td>
    <td class="smheading" align="center" width="55">Deaths From</td>
    <td class="smheading" align="center" width="55">Deaths In</td>
    <td class="smheading" align="center" width="55">Suicides</td>
    <td class="smheading" align="center" width="55">Eff.</td>
  </tr>

EOF;

if ($numweapons > 0) {
  // Sort by frags, kills, secondary kills, deaths holding, deaths from, suicides, description,road kills
  array_multisort($wskills[6], SORT_DESC, SORT_NUMERIC,
                  $wskills[0], SORT_DESC, SORT_NUMERIC,
                  $wskills[1], SORT_DESC, SORT_NUMERIC,
                  $wskills[3], SORT_ASC, SORT_NUMERIC,
                  $wskills[2], SORT_ASC, SORT_NUMERIC,
                  $wskills[4], SORT_ASC, SORT_NUMERIC,
                  $wskills[5], SORT_ASC, SORT_STRING,
                  $wskills[7], SORT_ASC, SORT_NUMERIC,
                  $wskills[8], SORT_ASC, SORT_NUMERIC,
                  $wskills[9], SORT_ASC, SORT_NUMERIC,
                  $wskills[10], SORT_ASC, SORT_NUMERIC,
                  $wskills[11], SORT_ASC, SORT_NUMERIC);

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
    $roadkills = $wskills[8][$i];

    if ($kills + $skills + $roadkills + $held + $suicides == 0)
      $eff = "0.0";
    else
      $eff = sprintf("%0.1f", (($kills + $skills + $roadkills) / ($kills + $skills + $roadkills + $held + $suicides)) * 100.0);

    if ($kills || $skills || $roadkills || $deaths || $held) {
      echo <<< EOF
  <tr>
    <td class="dark" align="center">$weapon</td>
    <td class="grey" align="center">$frags</td>
    <td class="grey" align="center">$kills</td>
    <td class="grey" align="center">$skills</td>
    <td class="grey" align="center">$roadkills</td>
    <td class="grey" align="center">$deaths</td>
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
    <td class="smheading" align="center" width="60">Killed</td>
    <td class="smheading" align="center" width="90">Deaths From</td>
  </tr>

EOF;

if ($numweapons > 0) {
  // Sort by kills,deaths,description
  array_multisort($wskills[0], SORT_DESC, SORT_NUMERIC,
                  $wskills[2], SORT_ASC, SORT_NUMERIC,
                  $wskills[5], SORT_ASC, SORT_STRING,
                  $wskills[6], SORT_ASC, SORT_NUMERIC,
                  $wskills[1], SORT_ASC, SORT_NUMERIC,
                  $wskills[4], SORT_ASC, SORT_NUMERIC,
                  $wskills[3], SORT_ASC, SORT_NUMERIC,
                  $wskills[7], SORT_ASC, SORT_NUMERIC,
                  $wskills[8], SORT_ASC, SORT_NUMERIC,
                  $wskills[9], SORT_ASC, SORT_NUMERIC,
                  $wskills[10], SORT_ASC, SORT_NUMERIC,
                  $wskills[11], SORT_ASC, SORT_NUMERIC);

  for ($i = 0; $i < $numweapons; $i++) {
    if ($wskills[7][$i] != 3)
      continue;
    $weapon = $wskills[5][$i];
    $kills = $wskills[0][$i];
    $deaths = $wskills[2][$i];

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
}
echo "</table>\n";

//=============================================================================
//========== Suicides Totals ==================================================
//=============================================================================
echo <<<EOF
<br />
<table cellpadding="1" cellspacing="2" border="0" width="255">
  <tr>
    <td class="medheading" align="center" colspan="2">Suicides Totals</td>
  </tr>
  <tr>
    <td class="smheading" align="center">Type</td>
    <td class="smheading" align="center" width="55">Suicides</td>
  </tr>

EOF;

if ($numweapons > 0) {
  // Sort by suicides, frags, kills, secondary kills, deaths from, deaths holding, description, weapon type
  array_multisort($wskills[4], SORT_DESC, SORT_NUMERIC,
                  $wskills[6], SORT_DESC, SORT_NUMERIC,
                  $wskills[0], SORT_DESC, SORT_NUMERIC,
                  $wskills[1], SORT_DESC, SORT_NUMERIC,
                  $wskills[2], SORT_ASC, SORT_NUMERIC,
                  $wskills[3], SORT_ASC, SORT_NUMERIC,
                  $wskills[5], SORT_ASC, SORT_STRING,
                  $wskills[7], SORT_ASC, SORT_NUMERIC,
                  $wskills[9], SORT_ASC, SORT_NUMERIC,
                  $wskills[10], SORT_ASC, SORT_NUMERIC,
                  $wskills[11], SORT_ASC, SORT_NUMERIC);

  for ($i = 0; $i < $numweapons; $i++) {
    if ($wskills[4][$i] > 0) {
      $type = $wskills[5][$i];
      $suicides = $wskills[4][$i];
      echo <<<EOF
  <tr>
    <td class="dark" align="center">$type</td>
    <td class="grey" align="center">$suicides</td>
  </tr>

EOF;
    }
  }
}
else {
  echo <<<EOF
  <tr>
    <td class="grey" align="center" colspan="2">No Suicides</td>
  </tr>

EOF;
}
echo "</table>\n";

//=============================================================================
//========== Killing Sprees by Type ===========================================
//=============================================================================
$time1 = sprintf("%0.1f", $plr_spreet1 / 6000);
$time2 = sprintf("%0.1f", $plr_spreet2 / 6000);
$time3 = sprintf("%0.1f", $plr_spreet3 / 6000);
$time4 = sprintf("%0.1f", $plr_spreet4 / 6000);
$time5 = sprintf("%0.1f", $plr_spreet5 / 6000);
$time6 = sprintf("%0.1f", $plr_spreet6 / 6000);

echo <<<EOF
<br />
<table cellpadding="1" cellspacing="2" border="0" width="390">
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
    <td class="grey" align="center">$plr_spree1</td>
    <td class="grey" align="center">$time1</td>
    <td class="grey" align="center">$plr_spreek1</td>
  </tr>
  <tr>
    <td class="dark" align="center">Rampage</td>
    <td class="grey" align="center">$plr_spree2</td>
    <td class="grey" align="center">$time2</td>
    <td class="grey" align="center">$plr_spreek2</td>
  </tr>
  <tr>
    <td class="dark" align="center">Dominating</td>
    <td class="grey" align="center">$plr_spree3</td>
    <td class="grey" align="center">$time3</td>
    <td class="grey" align="center">$plr_spreek3</td>
  </tr>
  <tr>
    <td class="dark" align="center">Unstoppable</td>
    <td class="grey" align="center">$plr_spree4</td>
    <td class="grey" align="center">$time4</td>
    <td class="grey" align="center">$plr_spreek4</td>
  </tr>
  <tr>
    <td class="dark" align="center">Godlike</td>
    <td class="grey" align="center">$plr_spree5</td>
    <td class="grey" align="center">$time5</td>
    <td class="grey" align="center">$plr_spreek5</td>
  </tr>
  <tr>
    <td class="dark" align="center">Wicked Sick</td>
    <td class="grey" align="center">$plr_spree6</td>
    <td class="grey" align="center">$time6</td>
    <td class="grey" align="center">$plr_spreek6</td>
  </tr>
</table>

EOF;

//=============================================================================
//========== Total Items Collected ============================================
//=============================================================================
// Load Item Descriptions
$result = sql_queryn($link, "SELECT it_num,it_desc FROM {$dbpre}items");
if (!$result) {
  echo "Error loading item descriptions.<br />\n";
  exit;
}
$items = array();
while ($row = sql_fetch_row($result))
  $items[$row[0]] = $row[1];
sql_free_result($result);

echo <<<EOF
<br />
<table cellpadding="1" cellspacing="2" border="0" width="600">
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

$result = sql_queryn($link, "SELECT pi_item,pi_pickups FROM {$dbpre}pitems WHERE pi_plr=$pnum");
if (!$result) {
  echo "Error loading player item pickups.<br />\n";
  exit;
}

$pickups = array(array());
$totpickups = 0;
while ($row = sql_fetch_row($result)) {
  for ($i = 0, $item = -1; $i < $totpickups && $item < 0; $i++) {
    if (!strcmp($pickups[0][$i], $items[$row[0]]))
      $item = $i;
  }
  if ($item < 0) {
    $pickups[0][$totpickups] = $items[$row[0]];
    $pickups[1][$totpickups++] = $row[1];
  }
  else
    $pickups[1][$item] += $row[1];
}
sql_free_result($result);

if ($totpickups > 0)
  array_multisort($pickups[1], SORT_DESC, SORT_NUMERIC,
                  $pickups[0], SORT_ASC, SORT_STRING);

$col = 0;
for ($i = 0; $i < $totpickups; $i++) {
  $item = $pickups[0][$i];
  $num = $pickups[1][$i];
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

if (!$totpickups) {
  echo <<<EOF
  <tr>
    <td class="dark" align="center" colspan="6">No Item Pickups</td>
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

//=============================================================================
//========== Most Recent Games Played =========================================
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
    <td class="smheading" align="center" width="225">Map</td>
    <td class="smheading" align="center" width="50">Players</td>
    <td class="smheading" align="center" width="50">Minutes</td>
  </tr>

EOF;

$matches = 0;
$result = sql_querynb($link, "SELECT gm_num,gm_map,gm_type,gm_start,gm_timeoffset,gm_length,gm_numplayers
  FROM {$dbpre}gplayers,{$dbpre}matches
  WHERE {$dbpre}gplayers.gp_pnum=$pnum AND {$dbpre}matches.gm_num={$dbpre}gplayers.gp_match
  ORDER BY {$dbpre}matches.gm_num DESC LIMIT 11");
if (!$result) {
  echo "Error accessing game and game player tables.<br />\n";
  exit;
}
while ($row = sql_fetch_assoc($result)) {
  if ($matches < 10) {
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

    // Load Map Name
    $result2 = sql_queryn($link, "SELECT mp_name FROM {$dbpre}maps WHERE mp_num=$gm_map LIMIT 1");
    if (!$result2) {
      echo "Map database error.<br />\n";
      exit;
    }
    list($mp_name) = sql_fetch_row($result2);
    sql_free_result($result2);
    $mapname = stripspecialchars($mp_name);

    echo <<<EOF
  <tr>
    <td class="dark" align="center"><a class="dark" href="matchstats.php?match=$gm_num">$matchdate</a></td>
    <td class="grey" align="center">$gametype</td>
    <td class="grey" align="center"><a class="grey" href="mapstats.php?map=$gm_map">$mapname</a></td>
    <td class="grey" align="center">$gm_numplayers</td>
    <td class="grey" align="center">$length</td>
  </tr>

EOF;
  }
  $matches++;
}
sql_free_result($result);
if ($matches > 10) {
  echo <<< EOF
  <tr>
    <td class="smheading" colspan="5" align="center"><a href="typematches.php?player=$plr" class="smheading">[Show All Matches For Player]</a></td>
  </tr>

EOF;
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