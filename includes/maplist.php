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

if (preg_match("/maplist.php/i", $_SERVER["PHP_SELF"])) {
  echo "{$LANG_ACCESSDENIED}\n";
  die();
}

$link = sql_connect();

$page = 1;
$searchname = "";
$clear = "";
check_get($page, "page");
check_get($searchname, "SearchName");
check_get($clear, "Clear");
$page = intval($page);
if ($page < 0)
  $page = 1;

if ($clear == "Clear")
  $searchname = "";

$searchstring = "";
if ($searchname) {
  $searchname = rawurldecode($searchname);
  $searchstring = "&amp;SearchName=".rawurlencode($searchname);
}

// Calculate Number of Pages
if ($searchname != "") {
  $slashedname = sql_addslashes($searchname);

  // Binary string types in MySQL have spaces stored as 0xa0!
  for ($i = 0; $i < strlen($slashedname); $i++)
    if ($slashedname[$i] == " ")
      $slashedname[$i] = chr(0xa0);

  $result = sql_queryn($link, "SELECT COUNT(*) FROM {$dbpre}maps WHERE mp_name LIKE '%{$slashedname}%'");
  if (!$result) {
    echo "{$LANG_MAPDATABASEERRORP}<br />\n";
    exit;
  }
  list($nummaps) = sql_fetch_row($result);
}
else {
  $result = sql_queryn($link, "SELECT COUNT(*) FROM {$dbpre}maps");
  if (!$result) {
    echo "{$LANG_MAPDATABASEERRORP}<br />\n";
    exit;
  }
  list($nummaps) = sql_fetch_row($result);
}
sql_free_result($result);

$numpages = (int) ceil($nummaps / $mapspage);
if (!$page)
  $page = 1;
else if ($page < 1 || $page > $numpages)
  $page = 1;

if ($numpages > 1) {
  echo "<div class=\"pages\"><b>{$LANG_PAGE} [$page/$numpages] {$LANG_SELECTION}: ";
  $prev = $page - 1;
  $next = $page + 1;
  if ($page != 1)
    echo "<a class=\"pages\" href=\"index.php?stats=maps&amp;page=1\">[{$LANG_FIRST}]</a> / <a class=\"pages\" href=\"index.php?stats=maps&amp;page=$prev\">[{$LANG_PREVIOUS}]</a> / ";
  else
    echo "[{$LANG_FIRST}] / [{$LANG_PREVIOUS}] / ";
  if ($page < $numpages)
    echo "<a class=\"pages\" href=\"index.php?stats=maps&amp;page=$next\">[{$LANG_NEXT}]</a> / <a class=\"pages\" href=\"index.php?stats=maps&amp;page=$numpages\">[{$LANG_LAST}]</a>";
  else
    echo "[{$LANG_NEXT}] / [{$LANG_LAST}]";
  echo "</b></div>";
}

if ($mapsearch == 1 || ($mapsearch == 2 && $numpages > 1)) {
  echo <<<EOF
<font size="1"><br /></font>
<form name="mapsearch" method="post" action="index.php?stats=maps">
  <table class="searchform">
    <tr>
      <td align="right">{$LANG_NAME}:</td>
      <td width="150" align="left"><input type="text" name="SearchName" maxlength="35" size="20" value="$searchname" class="searchformbox" /></td>
      <td align="left"><input type="submit" name="Default" value="{$LANG_SEARCH}" class="searchform" /></td>
      <td>&nbsp;</td>
      <td><input type="submit" name="Clear" value="{$LANG_CLEAR}" class="searchform" /></td>
    </tr>
  </table>
</form>

EOF;
}

echo <<<EOF
<table cellpadding="1" cellspacing="2" border="0" class="box">
  <tr>
    <td class="heading" colspan="5" align="center">{$LANG_UTMAPLIST}</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="140">{$LANG_MAPNAME}</td>
    <td class="smheading" align="center" width="50">{$LANG_MATCHES}</td>
    <td class="smheading" align="center" width="50">{$LANG_SCORE}</td>
    <td class="smheading" align="center" width="50">{$LANG_HOURS}</td>
    <td class="smheading" align="center" width="200">{$LANG_LASTMATCH}</td>
  </tr>

EOF;

// Load Map Stats
$start = ($page * $mapspage) - $mapspage;
if ($searchname != "")
  $where = "WHERE mp_name LIKE '%{$slashedname}%'";
else
  $where = "";

$result = sql_queryn($link, "SELECT mp_num,mp_name,mp_matches,mp_score,mp_time,mp_lastmatch FROM {$dbpre}maps $where ORDER BY mp_matches DESC LIMIT $start,$mapspage");
if (!$result) {
  echo "$LANG_MAPDATABASEERRORP<br />\n";
  exit;
}
while($row = sql_fetch_assoc($result)) {
  while (list ($key, $val) = each ($row))
    ${$key} = $val;

  $mapname = stripspecialchars($mp_name);
  $start = strtotime($mp_lastmatch);
  $matchdate = formatdate($start, 1);
  $tottime = sprintf("%0.1f", $mp_time / 360000.0);

  echo <<<EOF
  <tr>
    <td class="dark" align="center"><a class="dark" href="mapstats.php?map=$mp_num">$mapname</a></td>
    <td class="grey" align="center">$mp_matches</td>
    <td class="grey" align="center">$mp_score</td>
    <td class="grey" align="center">$tottime</td>
    <td class="grey" align="center">$matchdate</td>
  </tr>

EOF;
}
sql_free_result($result);
sql_close($link);
echo "</table>\n";

if (!$nummaps) {
  echo <<<EOF
<table cellpadding="1" cellspacing="2" border="0" width="600">
  <tr>
    <td align="center"><b>{$LANG_NOMAPSAVAILABLE}</b></td>
  </tr>
</table>

EOF;
}

?>