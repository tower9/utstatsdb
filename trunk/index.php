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

$statview = "";
check_get($statview, "stats");

if ($statview == 'players')
  require("includes/playerlist.php");
else if ($statview == 'matches')
  require("includes/matchlist.php");
else if ($statview == 'maps')
  require("includes/maplist.php");
else if ($statview == 'servers')
  require("includes/serverlist.php");
else if ($statview == 'help')
  require("includes/help.php");
else {
  $result = sql_query("SELECT tl_score,tl_kills,tl_suicides,tl_teamkills,tl_players,tl_matches,tl_playertime,tl_headshots FROM {$dbpre}totals LIMIT 1");
  if (!$result) {
    echo "{$LANG_STATSDATABASEERROR}<br />\n";
    exit;
  }
  list($tl_score,$tl_kills,$tl_suicides,$tl_teamkills,$tl_players,$tl_matches,$tl_playertime,$tl_headshots) = sql_fetch_row($result);
  sql_free_result($result);
  $frags = $tl_kills - $tl_suicides;
  $time = sprintf("%0.1f", $tl_playertime / 360000.0);

  $result = sql_query("SELECT COUNT(*) FROM {$dbpre}servers");
  if (!$result) {
    echo "{$LANG_STATSDATABASEERROR}<br />\n";
    exit;
  }
  list($servers) = sql_fetch_row($result);
  sql_free_result($result);

  echo <<<EOF
<table cellpadding="1" cellspacing="2" border="0" width="580" class="box">
  <tr>
    <td class="heading" colspan="9" align="center">{$LANG_UTSTATSDATABASE} v{$Version}</td>
  </tr>
  <tr>
    <td class="smheading" align="center">{$LANG_TOTALSCORE}</td>
    <td class="smheading" align="center">{$LANG_TOTALFRAGS}</td>
    <td class="smheading" align="center">{$LANG_TOTALKILLS}</td>
    <td class="smheading" align="center">{$LANG_TEAMKILLS}</td>
    <td class="smheading" align="center">{$LANG_TOTALHEADSHOTS}</td>
    <td class="smheading" align="center">{$LANG_HUMANPLAYERS}</td>
    <td class="smheading" align="center">{$LANG_SERVERS}</td>
    <td class="smheading" align="center">{$LANG_MATCHESLOGGED}</td>
    <td class="smheading" align="center">{$LANG_PLAYERHOURS}</td>
  </tr>
  <tr>
    <td class="grey" align="center">$tl_score</td>
    <td class="grey" align="center">$frags</td>
    <td class="grey" align="center">$tl_kills</td>
    <td class="grey" align="center">$tl_teamkills</td>
    <td class="grey" align="center">$tl_headshots</td>
    <td class="grey" align="center">$tl_players</td>
    <td class="grey" align="center">$servers</td>
    <td class="grey" align="center">$tl_matches</td>
    <td class="grey" align="center">$time</td>
  </tr>
</table>

EOF;

  // Load Last Match
  $matches = 0;
  $result = sql_query("SELECT MAX(gm_start) FROM {$dbpre}matches");
  if (!$result) {
    echo "{$LANG_MATCHDATABASEERROR} (main).<br />\n";
    exit;
  }
  if (!list($recent) = sql_fetch_row($result)) {
    echo "{$LANG_MATCHDATABASEERROR} (main).<br />\n";
    exit;
  }
  $result = sql_query("SELECT gm_num,gm_map,gm_start FROM {$dbpre}matches WHERE gm_start='$recent' LIMIT 1");
  if (list($gm_num,$gm_map,$gm_start) = sql_fetch_row($result)) {
    sql_free_result($result);
    $map = intval($gm_map);
    $start = strtotime($gm_start);
    $matchdate = formatdate($start, 1);
    $link = "matchstats.php?match=$gm_num";
    $result = sql_query("SELECT mp_name FROM {$dbpre}maps WHERE mp_num=$map LIMIT 1");
    if (!$result) {
      echo "{$LANG_MAPDATABASEERROR} (main).<br />\n";
      exit;
    }
    $row = sql_fetch_row($result);
    $mapname = $row[0];

    echo <<<EOF
<br />
<table cellpadding="1" cellspacing="0" border="0" width="350" class="box">
  <tr>
    <td class="lglheading" align="center"><b>{$LANG_LASTMATCHLOGGED}</b></td>
  </tr>
  <tr>
    <td class="heading" align="center">
      <a class="lglheading" href="$link"><b>$mapname</b><br />
      <b>$matchdate</b></a>
    </td>
  </tr>
</table>

EOF;
  }
  sql_free_result($result);

  if ($title_msg == "")
    $title_msg = "&nbsp;";
  echo <<<EOF
<font size="1"><br /></font>
<table cellpadding="1" cellspacing="2" border="0" width="600">
  <tr>
    <td colspan="10" align="center" class="titlemsg">$title_msg</td>
  </tr>
</table>

EOF;

  $result = sql_query("SELECT server,port,type,password,link,spectators,bots FROM {$dbpre}configquery ORDER BY num");
  while (list($query_server,$query_port,$query_type,$query_password,$query_link,$query_spectators,$query_bots) = sql_fetch_row($result)) {
    if ($query_server != "") {
      include_once("serverquery.php");
      if (GetStatus("$query_server", $query_port))
        DisplayStatus($query_link);
      else
        DisplayDown($query_server.":".$query_port);
    }
  }
  sql_free_result($result);

//=============================================================================
//========== Hourly Activity Graph ============================================
//=============================================================================

  $hactive = array_fill(0, 24, 0);

  if (strtolower($dbtype) == "sqlite")
    $result = sql_query("SELECT strftime('%H', gm_start) AS hour, COUNT(gt_pnum) AS pcount FROM {$dbpre}matches,{$dbpre}playersgt WHERE gm_num=gt_num GROUP BY hour");
  else if (strtolower($dbtype) == "mssql")
    $result = sql_query("SELECT DATEPART(hour, CONVERT(char(19), gm_start, 20)) AS hour, COUNT(gt_pnum) AS pcount FROM {$dbpre}matches,{$dbpre}playersgt WHERE gm_num=gt_num GROUP BY DATEPART(hour, CONVERT(char(19), gm_start, 20))");
  else
    $result = sql_query("SELECT HOUR(gm_start) AS hour, COUNT(gt_pnum) AS pcount FROM {$dbpre}matches,{$dbpre}playersgt WHERE gm_num=gt_num GROUP BY hour");

  while ($row = sql_fetch_row($result))
    $hactive[intval($row[0])] = intval($row[1]);
  sql_free_result($result);
  $hmax = max($hactive);

  if ($hmax > 0) {
    echo <<<EOF
<br />
<table cellpadding="0" cellspacing="0" border="0" width="610">
  <tr>
    <td>
      <table cellpadding="1" cellspacing="0" border="0" width="400" class="box" align="center">
        <tr>
          <td class="tglheading" align="center"><b>{$LANG_SERVERACTIVITYBYHOUR}</b></td>
        </tr>
        <tr>
          <td class="tgheading" align="center">
            <div class="tgmainbox">
              <div class="tgsubbox">&nbsp;</div>

EOF;

    for ($i = 0; $i < 24; $i++) {
      $num = $hactive[$i];
      $height = round(($num / $hmax) * 0.9 * 142);
      $bottom = $height + 2;
      echo "              <div class=\"tgbarspace\">&nbsp;</div><div class=\"tgbar\" style=\"height: {$height}px; bottom: {$bottom}px\" title=\"{$num}\">&nbsp;</div>\n";
    }

    echo "              <div class=\"tgblank\">&nbsp;</div>\n";

    for ($i = 0; $i < 24; $i++)
    {
      $hr = sprintf("%02d", $i);
      echo "              <div class=\"tgbarspace\">&nbsp;</div><div class=\"tglabel\" style=\"bottom: 127px\">$hr</div>\n";
    }

    echo <<<EOF
              <div class="tgblank">&nbsp;</div>
            </div>
          </td>
        </tr>
      </table>
    </td>

EOF;
  }

//=============================================================================
//========== Weekly Activity Graph ============================================
//=============================================================================
  $wactive = array_fill(0, 7, 0);

  if (strtolower($dbtype) == "sqlite")
    $result = sql_query("SELECT strftime('%w', gm_start) + 1 AS weekday, COUNT(gt_pnum) AS pcount FROM {$dbpre}matches,{$dbpre}playersgt WHERE gm_num=gt_num GROUP BY weekday");
  else if (strtolower($dbtype) == "mssql")
    $result = sql_query("SELECT DATEPART(weekday, CONVERT(char(19), gm_start, 20)) AS hour, COUNT(gt_pnum) AS pcount FROM {$dbpre}matches,{$dbpre}playersgt WHERE gm_num=gt_num GROUP BY DATEPART(weekday, CONVERT(char(19), gm_start, 20))");
  else
    $result = sql_query("SELECT DAYOFWEEK(gm_start) AS weekday, COUNT(gt_pnum) AS pcount FROM {$dbpre}matches,{$dbpre}playersgt WHERE gm_num=gt_num GROUP BY weekday");

  while ($row = sql_fetch_row($result))
    $wactive[intval($row[0])] = intval($row[1]);
  sql_free_result($result);
  $hmax = max($wactive);

  if ($hmax > 0) {
    echo <<<EOF
    <td>
      <table cellpadding="1" cellspacing="0" border="0" width="200" class="box" align="center">
        <tr>
          <td class="tglheading" align="center"><b>{$LANG_ACTIVITYBYWEEKDAY}</b></td>
        </tr>
        <tr>
          <td class="tgheading" align="center">
            <div class="wgmainbox">
              <div class="wgsubbox">&nbsp;</div>

EOF;

    for ($i = 1; $i <= 7; $i++) {
      $num = $wactive[$i];
      $height = round(($num / $hmax) * 0.9 * 142);
      $bottom = $height + 2;
      echo "              <div class=\"wgbarspace\">&nbsp;</div><div class=\"wgbar\" style=\"height: {$height}px; bottom: {$bottom}px\" title=\"{$num}\">&nbsp;</div>\n";
    }

    echo "              <div class=\"tgblank\">&nbsp;</div>\n";

    for ($i = 1; $i <= 7; $i++)
    {
      $wd = $LANG_WEEKDAYS[$i - 1];
      $space = $i == 1 ? "wgprespace" : "wgbarspace";
      echo "              <div class=\"$space\">&nbsp;</div><div class=\"wglabel\" style=\"bottom: 127px\">$wd</div>\n";
    }

    echo <<<EOF
              <div class="tgblank">&nbsp;</div>
            </div>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

EOF;
  }

}
echo <<<EOF

</td>
</tr>
</table>

</body>
</html>

EOF;

?>