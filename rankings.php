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
  $result = sql_queryn($link, "SELECT tp_desc FROM {$dbpre}type WHERE tp_num=$rtype LIMIT 1");
  if (!$result) {
    echo "{$LANG_DBERRORGAMETYPES}<br>\n";
    exit;
  }
  $row = sql_fetch_row($result);
  $label = $row[0];
  sql_free_result($result);

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

  $result = sql_queryn($link, "SELECT pnum,plr_name,plr_bot,gt_rank FROM {$dbpre}players LEFT JOIN {$dbpre}playersgt ON pnum=gt_pnum WHERE gt_tnum=$rtype AND gt_rank>0 ORDER BY gt_rank DESC LIMIT 100");
  if (!$result) {
    echo "{$LANG_PLAYERDATABASEERROR}<br />\n";
    exit;
  }
  $r = 1;
  while (list($pnum,$plr_name,$bot,$rankp) = sql_fetch_row($result)) {
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