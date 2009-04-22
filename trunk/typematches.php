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

$plr = -1;
$mapnum = -1;
$servernum = -1;
$page = 1;
check_get($plr, "player");
check_get($mapnum, "map");
check_get($servernum, "server");
check_get($page, "page");
if (!is_numeric($plr))
  $plr = -1;
if (!is_numeric($mapnum))
  $mapnum = -1;
if (!is_numeric($servernum))
  $servernum = -1;
if (!is_numeric($page))
  $page = 1;

if ($plr <= 0 && $servernum <= 0 && $mapnum <= 0) {
  echo "Run from the main index program.<br>\n";
  exit;
}

$link = sql_connect();

if ($plr > 0) {
  $result = sql_queryn($link, "SELECT plr_name,plr_bot FROM {$dbpre}players WHERE pnum=$plr LIMIT 1");
  if (!$result) {
    echo "Player Database Error.<br>\n";
    exit;
  }
  $row = sql_fetch_row($result);
  sql_free_result($result);
  if (!$row) {
    echo "Player not found in database.<br>\n";
    exit;
  }
  $plrname = stripspecialchars($row[0]);
  $plrbot = intval($row[1]);
  if ($plrbot)
    $bot = " (bot)";
  else
    $bot = "";
  $heading = "{$plrname}{$bot} [$plr]";
  $pagetype = "player=$plr";
}
else if ($servernum > 0) {
  $result = sql_queryn($link, "SELECT sv_name,sv_shortname FROM {$dbpre}servers WHERE sv_num=$servernum LIMIT 1");
  if (!$result) {
    echo "Server Database Error.<br>\n";
    exit;
  }
  list($sv_name,$sv_shortname) = sql_fetch_row($result);
  sql_free_result($result);
  if ($useshortname && $sv_shortname != "")
    $servername = stripspecialchars($sv_shortname);
  else
    $servername = stripspecialchars($sv_name);
  $heading = "$servername";
  $pagetype = "server=$servernum";
}
else {
  $result = sql_queryn($link, "SELECT mp_name FROM {$dbpre}maps WHERE mp_num=$mapnum LIMIT 1");
  if (!$result) {
    echo "Map Database Error.<br>\n";
    exit;
  }
  list($mp_name) = sql_fetch_row($result);
  sql_free_result($result);
  $mapname = stripspecialchars($mp_name);
  $heading = "$mapname";
  $pagetype = "map=$mapnum";
}

// Calculate Number of Pages
if ($plr > 0)
  $result = sql_queryn($link, "SELECT COUNT(*) FROM {$dbpre}gplayers WHERE gp_pnum=$plr");
else if ($servernum > 0)
  $result = sql_queryn($link, "SELECT COUNT(*) FROM {$dbpre}matches WHERE gm_server=$servernum");
else
  $result = sql_queryn($link, "SELECT COUNT(*) FROM {$dbpre}matches WHERE gm_map=$mapnum");
list($nummatches) = sql_fetch_row($result);
sql_free_result($result);
$numpages = (int) ceil($nummatches / $matchespage);
if (!$page)
  $page = 1;
else if ($page < 1 || $page > $numpages)
  $page = 1;

if ($numpages > 1) {
  echo "<div class=\"pages\"><b>Page [$page/$numpages] Selection: ";
  $prev = $page - 1;
  $next = $page + 1;
  if ($page != 1)
    echo "<a class=\"pages\" href=\"typematches.php?$pagetype&amp;page=1\">[First]</a> / <a class=\"pages\" href=\"typematches.php?$pagetype&amp;page=$prev\">[Previous]</a> / ";
  else
    echo "[First] / [Previous] / ";
  if ($page < $numpages)
    echo "<a class=\"pages\" href=\"typematches.php?$pagetype&amp;page=$next\">[Next]</a> / <a class=\"pages\" href=\"typematches.php?$pagetype&amp;page=$numpages\">[Last]</a>";
  else
    echo "[Next] / [Last]";
  echo "</b></div>";
}

echo <<<EOF
<table cellpadding="1" cellspacing="2" border="0" class="box">
  <tr>
    <td class="heading" colspan="5" align="center">Unreal Tournament Match List for $heading</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="250">Date</td>
    <td class="smheading" align="center" width="180">Match Type</td>
    <td class="smheading" align="center" width="225">Map</td>
    <td class="smheading" align="center" width="50">Players</td>
    <td class="smheading" align="center" width="50">Minutes</td>
  </tr>

EOF;

// Load game types
$numtypes = 0;
$result = sql_queryn($link, "SELECT tp_num,tp_desc FROM {$dbpre}type");
while($row = sql_fetch_row($result))
  $gtype[$numtypes++] = $row;
sql_free_result($result);

// Load Game Stats
$matches = 0;
$start = ($page * $matchespage) - $matchespage;
if ($plr > 0)
  $result = sql_queryn($link, "SELECT gm_num,gm_type,gm_start,gm_timeoffset,gm_length,gm_map,gm_numplayers,mp_name FROM {$dbpre}gplayers,{$dbpre}matches,{$dbpre}maps WHERE {$dbpre}gplayers.gp_pnum=$plr AND {$dbpre}matches.gm_num={$dbpre}gplayers.gp_match AND {$dbpre}maps.mp_num=gm_map ORDER BY gm_num DESC LIMIT $start,$matchespage");
else if ($servernum > 0)
  $result = sql_queryn($link, "SELECT gm_num,gm_type,gm_start,gm_timeoffset,gm_length,gm_map,gm_numplayers,mp_name FROM {$dbpre}matches USE INDEX (gm_svnum),{$dbpre}maps WHERE gm_server=$servernum AND {$dbpre}maps.mp_num=gm_map ORDER BY gm_num DESC LIMIT $start,$matchespage");
else
  $result = sql_queryn($link, "SELECT gm_num,gm_type,gm_start,gm_timeoffset,gm_length,gm_map,gm_numplayers,mp_name FROM {$dbpre}matches,{$dbpre}maps WHERE gm_map=$mapnum AND {$dbpre}maps.mp_num=gm_map ORDER BY gm_num DESC LIMIT $start,$matchespage");
if (!$result) {
  echo "Game database error.<br>\n";
  exit;
}
while($row = sql_fetch_assoc($result)) {
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
  $map = stripspecialchars($mp_name);
  $matches++;

  echo <<<EOF
  <tr>
    <td class="dark" align="center"><a class="dark" href="matchstats.php?match=$gm_num">$matchdate</a></td>
    <td class="grey" align="center">$gametype</td>
    <td class="grey" align="center"><a class="grey" href="mapstats.php?map=$gm_map">$map</a></td>
    <td class="grey" align="center">$gm_numplayers</td>
    <td class="grey" align="center">$length</td>
  </tr>

EOF;
}
sql_free_result($result);

sql_close($link);

echo "</table>\n";

if (!$matches) {
  echo <<<EOF
<table cellpadding="1" cellspacing="2" border="0" width="600">
  <tr>
    <td colspan="5">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="5" align="center"><b>No matches available.</b></td>
  </tr>
</table>

EOF;
}

echo <<<EOF
    </td>
  </tr>
</table>

EOF;

?>