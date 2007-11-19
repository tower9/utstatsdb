<?php

/*
    UTStatsDB
    Copyright (C) 2002-2007  Patrick Contreras / Paul Gallier

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

if (preg_match("/serverlist.php/i", $_SERVER["PHP_SELF"])) {
  echo "{$LANG_ACCESSDENIED}\n";
  die();
}

$page = 1;
check_get($page, "page");
if (!is_numeric($page))
  $page = 1;

$link = sql_connect();

// Calculate Number of Pages
$result = sql_queryn($link, "SELECT COUNT(*) FROM {$dbpre}servers");
list($numservers) = sql_fetch_row($result);
sql_free_result($result);
$numpages = (int) ceil($numservers / $serverspage);
if (!$page)
  $page = 1;
else if ($page < 1 || $page > $numpages)
  $page = 1;

if ($numpages > 1) {
  echo "<div class=\"pages\"><b>{$LANG_PAGE} [$page/$numpages] {$LANG_SELECTION}: ";
  $prev = $page - 1;
  $next = $page + 1;
  if ($page != 1)
    echo "<a class=\"pages\" href=\"index.php?stats=servers&amp;page=1\">[{$LANG_FIRST}]</a> / <a class=\"pages\" href=\"index.php?stats=servers&amp;page=$prev\">[{$LANG_PREVIOUS}]</a> / ";
  else
    echo "[{$LANG_FIRST}] / [{$LANG_PREVIOUS}] / ";
  if ($page < $numpages)
    echo "<a class=\"pages\" href=\"index.php?stats=servers&amp;page=$next\">[{$LANG_NEXT}]</a> / <a class=\"pages\" href=\"index.php?stats=servers&amp;page=$numpages\">[{$LANG_LAST}]</a>";
  else
    echo "[{$LANG_NEXT}] / [{$LANG_LAST}]";
  echo "</b></div>";
}

echo <<<EOF
<table cellpadding="1" cellspacing="2" border="0" class="box">
  <tr>
    <td class="heading" colspan="7" align="center">{$LANG_UTSERVERLIST}</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="25">{$LANG_ID}</td>
    <td class="smheading" align="center" width="150">{$LANG_SERVERNAME}</td>
    <td class="smheading" align="center" width="60">{$LANG_MATCHES}</td>
    <td class="smheading" align="center" width="45">{$LANG_FRAGS}</td>
    <td class="smheading" align="center" width="45">{$LANG_SCORE}</td>
    <td class="smheading" align="center" width="45">{$LANG_HOURS}</td>
    <td class="smheading" align="center" width="200">{$LANG_LASTMATCH}</td>
  </tr>

EOF;

// Load Server Stats
$start = ($page * $serverspage) - $serverspage;
$result = sql_queryn($link, "SELECT sv_num,sv_name,sv_matches,sv_frags,sv_score,sv_time,sv_lastmatch FROM {$dbpre}servers ORDER BY sv_matches DESC LIMIT $start,$serverspage");
if (!$result) {
  echo "{$LANG_SERVERDATABASEERROR}<br>\n";
  exit;
}
while($row = sql_fetch_assoc($result)) {
  while (list ($key, $val) = each ($row))
    ${$key} = $val;

  $servername = stripspecialchars($sv_name);
  $start = strtotime($sv_lastmatch);
  $matchdate = formatdate($start, 1);
  $tottime = sprintf("%0.1f", $sv_time / 360000.0);

  echo <<<EOF
  <tr>
    <td class="dark" align="center"><a class="dark" href="serverstats.php?server=$sv_num">$sv_num</a></td>
    <td class="dark" align="center"><a class="dark" href="serverstats.php?server=$sv_num">$servername</a></td>
    <td class="grey" align="center">$sv_matches</td>
    <td class="grey" align="center">$sv_frags</td>
    <td class="grey" align="center">$sv_score</td>
    <td class="grey" align="center">$tottime</td>
    <td class="grey" align="center">$matchdate</td>
  </tr>

EOF;
}
sql_free_result($result);
echo "</table>\n";

if (!$numservers) {
  echo <<<EOF
<table cellpadding="1" cellspacing="2" border="0" width="600">
  <tr>
    <td colspan="5">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="5" align="center"><b>{$LANG_NOSERVERSAVAILABLE}</b></td>
  </tr>
</table>

EOF;
}

?>