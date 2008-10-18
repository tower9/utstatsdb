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

if (preg_match("/matchlist.php/i", $_SERVER["PHP_SELF"])) {
  echo "{$LANG_ACCESSDENIED}\n";
  die();
}

$link = sql_connect();

$page = 1;
check_get($page, "page");
$page = intval($page);
if ($page < 0)
  $page = 1;

// Calculate Number of Pages
$result = sql_queryn($link, "SELECT COUNT(*) FROM {$dbpre}matches");
list($nummatches) = sql_fetch_row($result);
sql_free_result($result);
$numpages = (int) ceil($nummatches / $matchespage);
if (!$page)
  $page = 1;
else if ($page < 1 || $page > $numpages)
  $page = 1;

if ($numpages > 1) {
  echo "<div class=\"pages\"><b>{$LANG_PAGE} [$page/$numpages] {$LANG_SELECTION}: ";
  $prev = $page - 1;
  $next = $page + 1;
  if ($page != 1)
    echo "<a class=\"pages\" href=\"index.php?stats=matches&amp;page=1\">[{$LANG_FIRST}]</a> / <a class=\"pages\" href=\"index.php?stats=matches&amp;page=$prev\">[{$LANG_PREVIOUS}]</a> / ";
  else
    echo "[{$LANG_FIRST}] / [{$LANG_PREVIOUS}] / ";
  if ($page < $numpages)
    echo "<a class=\"pages\" href=\"index.php?stats=matches&amp;page=$next\">[{$LANG_NEXT}]</a> / <a class=\"pages\" href=\"index.php?stats=matches&amp;page=$numpages\">[{$LANG_LAST}]</a>";
  else
    echo "[{$LANG_NEXT}] / [{$LANG_LAST}]";
  echo "</b></div>";
}

if ($serverlist)
  $cols = 6;
else
  $cols = 5;

echo <<<EOF
<table cellpadding="1" cellspacing="2" border="0" class="box">
  <tr>
    <th class="heading" colspan="$cols" align="center">{$LANG_UTMATCHLIST}</th>
  </tr>
  <tr>
    <td class="smheading" align="center" width="220">{$LANG_DATE}</td>

EOF;

if ($serverlist)
  echo "    <td class=\"smheading\" align=\"center\" width=\"160\">{$LANG_SERVER}</td>\n";

echo <<<EOF
    <td class="smheading" align="center" width="160">{$LANG_MATCHTYPE}</td>
    <td class="smheading" align="center" width="200">{$LANG_MAP}</td>
    <td class="smheading" align="center" width="30">{$LANG_PLRS}</td>
    <td class="smheading" align="center" width="40">{$LANG_TIME}</td>
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
$result = sql_queryn($link, "SELECT gm_num,gm_server,gm_map,gm_type,gm_start,gm_length,gm_numplayers,mp_name,sv_name,sv_shortname FROM {$dbpre}matches LEFT JOIN {$dbpre}maps ON mp_num=gm_map LEFT JOIN {$dbpre}servers ON sv_num=gm_server ORDER BY gm_start DESC LIMIT $start,$matchespage");
if (!$result) {
  echo "{$LANG_GAMEDATABASEERROR}<br>\n";
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
  $length = sprintf("%0.1f", $gm_length / 6000.0);
  $map = stripspecialchars($mp_name);
  if ($useshortname)
    $server = stripspecialchars($sv_shortname);
  else
    $server = stripspecialchars($sv_name);
  if (strlen($server) > 30)
    $server = substr($server, 0, 28)."...";
  $matches++;

  echo <<<EOF
  <tr>
    <td class="darkn" align="center"><a class="darkn" href="matchstats.php?match=$gm_num">$matchdate</a></td>

EOF;

  if ($serverlist)
    echo "    <td class=\"greyn\" align=\"center\">$server</td>\n";

  echo <<<EOF
    <td class="greyn" align="center">$gametype</td>
    <td class="greyn" align="center"><a class="greyn" href="mapstats.php?map=$gm_map">$map</a></td>
    <td class="grey" align="center">$gm_numplayers</td>
    <td class="grey" align="center">$length</td>
  </tr>

EOF;
}
sql_free_result($result);
echo "</table>\n";

if (!$matches) {
  echo <<<EOF
<table cellpadding="1" cellspacing="2" border="0" width="600">
  <tr>
    <td colspan="5">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="5" align="center"><b>{$LANG_NOMATCHESAVAILABLE}</b></td>
  </tr>
</table>

EOF;
}

?>