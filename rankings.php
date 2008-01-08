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

require("includes/main.inc.php");

check_get($rtype, "type");

$link = sql_connect();

if ($rtype != "") {
  //=============================================================================
  //========== Gametype Rankings ================================================
  //=============================================================================
  $col = 0;
  $page = 1;
  $type = "";
  $rank = "";
  $searchid = 0;
  check_get($page, "page");
  check_get($type, "type");
  check_get($rank, "rank");
  check_get($searchid, "SearchID");
  if (!is_numeric($searchid))
    $searchid = 0;
  if (!is_numeric($page))
    $page = 1;

  $searchstring = "";
  if ($searchid) {
    $searchidvalue = "VALUE=\"$searchid\"";
    $searchstring = "&amp;SearchID=$searchid";
  }
  else
    $searchidvalue = "";

  $result = sql_queryn($link, "SELECT tp_desc FROM {$dbpre}type WHERE tp_num=$rtype LIMIT 1");
  if (!$result) {
    echo "{$LANG_DBERRORGAMETYPES}<br>\n";
    exit;
  }
  $row = sql_fetch_row($result);
  $label = $row[0];
  sql_free_result($result);

  // Calculate Number of Pages
  $result = sql_queryn($link, "SELECT COUNT(*) FROM {$dbpre}playersgt WHERE gt_tnum=$rtype AND gt_rank>0");
  if (!$result) {
    echo "{$LANG_PLAYERDATABASEERROR}<br />\n";
    exit;
  }
  list($num) = sql_fetch_row($result);
  sql_free_result($result);
  $numpages = (int) ceil($num / $playerspage);

  // Set page number if searching by ID
  if ($searchid) {
    $result = sql_querynb($link, "SELECT gt_rank FROM {$dbpre}playersgt WHERE gt_pnum=$searchid AND gt_tnum=$rtype AND gt_rank>0 LIMIT 1");
    if (!$result) {
      echo "{$LANG_PLAYERDATABASEERROR}<br />\n";
      exit;
    }
    if (sql_num_rows($result) == 0)
      $prank = 0;
    else
      list($prank) = sql_fetch_row($result);
    sql_free_result($result);

    if ($prank) {
      $result = sql_queryn($link, "SELECT COUNT(*) FROM {$dbpre}playersgt WHERE gt_tnum=$rtype AND gt_rank>$prank");
      if (!$result) {
        echo "{$LANG_PLAYERDATABASEERROR}<br />\n";
        exit;
      }
      list($pranknum) = sql_fetch_row($result);
      sql_free_result($result);
      $pranknum++;
      $page = (int) ceil($pranknum / $playerspage);
    }
  }

  if (!$page)
    $page = 1;
  else if ($page < 1 || $page > $numpages)
    $page = 1;

  if ($numpages > 1) {
    echo <<<EOF
<font size="1px"><br /></font>
<form name="playersearch" method="post" action="rankings.php">
  <input type="hidden" name="type" value="$rtype" />
  <table class="searchform">
    <tr>
      <td align="right">{$LANG_ID}:</td>
      <td width="90" align="left"><input type="text" name="SearchID" maxlength="10" size="10" $searchidvalue class="searchformbox" /></td>
      <td align="left"><input type="submit" name="Default" value="{$LANG_SEARCH}" class="searchform" /></td>
    </tr>
  </table>
</form>

EOF;

    echo "<div class=\"pages\"><b>{$LANG_PAGE} [$page/$numpages] {$LANG_SELECTION}: ";
    $prev = $page - 1;
    $next = $page + 1;
    if ($page != 1)
      echo "<a class=\"pages\" href=\"rankings.php?type=1&amp;page=1\">[{$LANG_FIRST}]</a> / <a class=\"pages\" href=\"rankings.php?type=1&amp;page={$prev}\">[{$LANG_PREVIOUS}]</a> / ";
    else
      echo "[{$LANG_FIRST}] / [{$LANG_PREVIOUS}] / ";
    if ($page < $numpages)
      echo "<a class=\"pages\" href=\"rankings.php?type=1&amp;page={$next}\">[{$LANG_NEXT}]</a> / <a class=\"pages\" href=\"rankings.php?type=1&amp;page={$numpages}\">[{$LANG_LAST}]</a>";
    else
      echo "[{$LANG_NEXT}] / [{$LANG_LAST}]";
    echo "</b></div>\n";
    echo "<div style=\"font-size: 1px\">&nbsp;</div>\n";
  }

  echo <<<EOF
<center>
<table cellpadding="1" cellspacing="1" border="0" class="box">
  <tr>
<td class="heading" align="center" colspan="3">{$LANG_TOPRANKINGS}</td>
  </tr>
  <tr>
    <td>
      <table>
        <tr>
          <td class="hlheading" align="center" colspan="3">$label {$LANG_RANKINGS}</td>
        </tr>
        <tr>
          <td class="smheading" align="center" width="35">{$LANG_RANK}</td>
          <td class="smheading" align="center" width="180">{$LANG_PLAYER}</td>
          <td class="smheading" align="center" width="60">{$LANG_POINTS}</td>
        </tr>

EOF;

  $start = ($page * $playerspage) - $playerspage;
  $limit = "$start,$playerspage";

  $result = sql_queryn($link, "SELECT pnum,plr_name,plr_bot,gt_rank FROM {$dbpre}players LEFT JOIN {$dbpre}playersgt ON pnum=gt_pnum WHERE gt_tnum=$rtype AND gt_rank>0 ORDER BY gt_rank DESC LIMIT $limit");
  if (!$result) {
    echo "{$LANG_PLAYERDATABASEERROR}<br />\n";
    exit;
  }
  $r = $start + 1;
  while (list($pnum,$plr_name,$bot,$rankp) = sql_fetch_row($result)) {
    $name = stripspecialchars($plr_name)." [$pnum]";
    if ($searchid && $pnum == $searchid)
      $nameclass = "idmatch";
    else if ($bot)
      $nameclass = "darkbot";
    else
      $nameclass = "darkhuman";
    $rank = sprintf("%0.2f", $rankp);

    echo <<<EOF
        <tr>
          <td class="grey" align="center">$r</td>
          <td class="dark" align="center"><a class="$nameclass" href="playerstats.php?player=$pnum">$name</a></td>
          <td class="grey" align="center">$rank</td>
        </tr>

EOF;
    $r++;
  }
  sql_free_result($result);
  echo <<<EOF
      </table>
    </td>
  </tr>

EOF;
}
else {
  //=============================================================================
  //========== Player Rankings ==================================================
  //=============================================================================
  echo <<<EOF
<center>
<table cellpadding="1" cellspacing="1" border="0" class="box">
  <tr>
    <td class="heading" align="center" colspan="6">{$LANG_PLAYERRANKINGS}</td>
  </tr>

EOF;

  $col = 0;
  $result = sql_queryn($link, "SELECT tp_desc,tp_num FROM {$dbpre}type");
  if (!$result) {
    echo "{$LANG_DBERRORGAMETYPES}<br>\n";
    exit;
  }
  $gametypes = 0;
  while ($row = sql_fetch_row($result)) {
    $gametypes++;
    $gtype[$row[1]] = $row[0];
  }
  sql_free_result($result);

  for ($type = 1; $type <= $gametypes; $type++) {
  	$tp_desc = $gtype[$type];
    $result = sql_queryn($link, "SELECT pnum,plr_name,plr_bot,gt_rank FROM {$dbpre}players LEFT JOIN {$dbpre}playersgt ON pnum=gt_pnum WHERE gt_tnum=$type AND gt_rank>0 ORDER BY gt_rank DESC LIMIT 10");
    if (!$result) {
      echo "{$LANG_PLAYERDATABASEERROR}<br />\n";
      exit;
    }
    $r = 1;
    $header = 0;
    while (list($pnum,$plr_name,$bot,$rankp) = sql_fetch_row($result)) {
      if (!$header) {
        if (!$col)
          echo "  <tr>\n";
        echo <<<EOF
    <td valign="top">
      <table cellpadding="1" cellspacing="2" border="0">
        <tr>
          <td class="hlheading" align="center" colspan="3"><a href="rankings.php?type=$type" class="hlheading">$tp_desc</a></td>
        </tr>
        <tr>
          <td class="smheading" align="center" width="35">{$LANG_RANK}</td>
          <td class="smheading" align="center" width="180">{$LANG_PLAYER}</td>
          <td class="smheading" align="center" width="60">{$LANG_POINTS}</td>
        </tr>

EOF;
        $header = 1;
      }

      $name = stripspecialchars($plr_name)." [$pnum]";
      if ($bot)
        $nameclass = "darkbot";
      else
        $nameclass = "darkhuman";
      $rank = sprintf("%0.2f", $rankp);

      echo <<<EOF
        <tr>
          <td class="grey" align="center">$r</td>
          <td class="dark" align="center"><a class="$nameclass" href="playerstats.php?player=$pnum">$name</a></td>
          <td class="grey" align="center">$rank</td>
        </tr>

EOF;
      $r++;
    }
    sql_free_result($result);

    while ($header && $r < 11) {
      echo <<<EOF
        <tr>
          <td class="grey" align="center">&nbsp;</td>
          <td class="dark" align="center">&nbsp;</td>
          <td class="grey" align="center">&nbsp;</td>
        </tr>

EOF;
      $r++;
    }

    if ($header) {
      echo <<<EOF
      </table>
    </td>

EOF;
      if ($col)
        echo "  </tr>\n";
      $col++;
      if ($col > 1)
        $col = 0;
    }
  }
}

sql_close($link);

if ($col)
  echo "  </tr>\n";

echo <<<EOF
</table>
</center>

</td></tr></table>

</body>
</html>

EOF;

?>