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

function getplayer($plr)
{
  global $link, $dbpre;
  global $LANG_PLAYERDATABASEERROR;

  $result = sql_queryn($link, "SELECT pnum,plr_name,plr_bot FROM {$dbpre}players WHERE pnum=$plr LIMIT 1");
  if (!$result) {
    echo "{$LANG_PLAYERDATABASEERROR}<br />\n";
    exit;
  }
  if (list($pnum,$name,$bot) = sql_fetch_row($result)) {
    $name = stripspecialchars($name);
    if ($bot)
      $nameclass = "darkbot";
    else
      $nameclass = "darkhuman";
    $gpplayer = "<a class=\"$nameclass\" href=\"playerstats.php?player=$pnum\">$name</a>";
  }
  else
    $gpplayer = "&nbsp;";
  sql_free_result($result);

  return $gpplayer;
}

function showweapons($group)
{
  global $weapons, $numweapons, $link, $dbpre;
  global $LANG_NONE, $LANG_MAPDATABASEERRORP;

  // Sort by num, date, description, time, player, map
  switch ($group) {
    case 1: // Kills
      array_multisort($weapons[1], SORT_DESC, SORT_NUMERIC,
                      $weapons[5], SORT_ASC, SORT_NUMERIC,
                      $weapons[0], SORT_ASC, SORT_STRING,
                      $weapons[3], SORT_ASC, SORT_NUMERIC,
                      $weapons[2], SORT_ASC, SORT_NUMERIC,
                      $weapons[4], SORT_ASC, SORT_STRING,
                      $weapons[6], $weapons[7], $weapons[8], $weapons[9], $weapons[10],
                      $weapons[11], $weapons[12], $weapons[13], $weapons[14], $weapons[15],
                      $weapons[16], $weapons[17], $weapons[18], $weapons[19], $weapons[20]);
      break;
    case 2: // Deaths
      array_multisort($weapons[6], SORT_DESC, SORT_NUMERIC,
                      $weapons[10], SORT_ASC, SORT_NUMERIC,
                      $weapons[0], SORT_ASC, SORT_STRING,
                      $weapons[8], SORT_ASC, SORT_NUMERIC,
                      $weapons[7], SORT_ASC, SORT_NUMERIC,
                      $weapons[9], SORT_ASC, SORT_STRING,
                      $weapons[1], $weapons[2], $weapons[3], $weapons[4], $weapons[5],
                      $weapons[11], $weapons[12], $weapons[13], $weapons[14], $weapons[15],
                      $weapons[16], $weapons[17], $weapons[18], $weapons[19], $weapons[20]);
      break;
    case 3: // Deaths while Holding
      array_multisort($weapons[11], SORT_DESC, SORT_NUMERIC,
                      $weapons[15], SORT_ASC, SORT_NUMERIC,
                      $weapons[0], SORT_ASC, SORT_STRING,
                      $weapons[13], SORT_ASC, SORT_NUMERIC,
                      $weapons[12], SORT_ASC, SORT_NUMERIC,
                      $weapons[14], SORT_ASC, SORT_STRING,
                      $weapons[1], $weapons[2], $weapons[3], $weapons[4], $weapons[5],
                      $weapons[6], $weapons[7], $weapons[8], $weapons[9], $weapons[10],
                      $weapons[16], $weapons[17], $weapons[18], $weapons[19], $weapons[20]);
      break;
    case 4: // Suicides
      array_multisort($weapons[16], SORT_DESC, SORT_NUMERIC,
                      $weapons[20], SORT_ASC, SORT_NUMERIC,
                      $weapons[0], SORT_ASC, SORT_STRING,
                      $weapons[18], SORT_ASC, SORT_NUMERIC,
                      $weapons[17], SORT_ASC, SORT_NUMERIC,
                      $weapons[19], SORT_ASC, SORT_STRING,
                      $weapons[1], $weapons[2], $weapons[3], $weapons[4], $weapons[5],
                      $weapons[6], $weapons[7], $weapons[8], $weapons[9], $weapons[10],
                      $weapons[11], $weapons[12], $weapons[13], $weapons[14], $weapons[15]);
      break;
  }

  for ($i = 0; $i < $numweapons; $i++) {
    $num = $weapons[$group * 5 - 4][$i];
    if ($num > 0) {
      $wpdesc = $weapons[0][$i];
      if (strcmp($wpdesc, "{$LANG_NONE}")) {
        $player = getplayer($weapons[$group * 5 - 3][$i]);
        $time = sprintf("%0.1f", $weapons[$group * 5 - 2][$i] / 6000);
        $mapnum = $weapons[$group * 5 - 1][$i];
        $date = formatdate(strtotime($weapons[$group * 5][$i]), 0);

        // Get Map Name
        $result = sql_queryn($link, "SELECT mp_name FROM {$dbpre}maps WHERE mp_num=$mapnum LIMIT 1");
        if (!$result) {
          echo "{$LANG_MAPDATABASEERRORP}<br />\n";
          exit;
        }
        list($map) = sql_fetch_row($result);
        sql_free_result($result);

        echo <<< EOF
    <tr>
      <td class="dark" align="center">$wpdesc</td>
      <td class="dark" align="center">$player</td>
      <td class="grey" align="center">$num</td>
      <td class="grey" align="center">$time</td>
      <td class="grey" align="center"><a class="grey" href="mapstats.php?map=$mapnum">$map</a></td>
      <td class="grey" align="center">$date</td>
    </tr>

EOF;
      }
    }
  }
  echo "</table>\n";
}

function map_name($num)
{
  global $link, $dbpre;
  global $LANG_MAPDATABASEERROR;

  // Get Map Names
  $result = sql_queryn($link, "SELECT mp_name FROM {$dbpre}maps WHERE mp_num=$num LIMIT 1");
  if (!$result) {
    echo "{$LANG_MAPDATABASEERROR}<br />\n";
    exit;
  }
  list($map) = sql_fetch_row($result);
  sql_free_result($result);
  return(stripspecialchars($map));
}

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
//========== Totals Logged ====================================================
//=============================================================================

$frags = $tl_kills - $tl_suicides;
$ghours = sprintf("%0.1f", $tl_gametime / 360000);
$phours = sprintf("%0.1f", $tl_playertime / 360000);

echo <<<EOF
<center>
<table cellpadding="1" cellspacing="2" border="0" class="box">
  <tr>
    <td class="heading" align="center" colspan="7">{$LANG_TOTALSLOGGED}</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="60">{$LANG_FRAGS}</td>
    <td class="smheading" align="center" width="60">{$LANG_KILLS}</td>
    <td class="smheading" align="center" width="60">{$LANG_DEATHS}</td>
    <td class="smheading" align="center" width="60">{$LANG_SUICIDES}</td>
    <td class="smheading" align="center" width="55">{$LANG_MATCHES}</td>
    <td class="smheading" align="center" width="85">{$LANG_GAMEHOURS}</td>
    <td class="smheading" align="center" width="85">{$LANG_PLAYERHOURS}</td>
  </tr>
  <tr>
    <td class="grey" align="center">$frags</td>
    <td class="grey" align="center">$tl_kills</td>
    <td class="grey" align="center">$tl_deaths</td>
    <td class="grey" align="center">$tl_suicides</td>
    <td class="grey" align="center">$tl_matches</td>
    <td class="grey" align="center">$ghours</td>
    <td class="grey" align="center">$phours</td>
  </tr>
</table>

EOF;

//=============================================================================
//========== Total Matches Played by Type =====================================
//=============================================================================

$result = sql_queryn($link, "SELECT * FROM {$dbpre}type");
if (!$result) {
  echo "{$LANG_DBERRORGAMETYPES}<br />\n";
  exit;
}

echo <<<EOF
<font size="1"><br /></font>
<table cellpadding="1" cellspacing="2" border="0" class="box">
  <tr>
    <td class="heading" colspan="4" align="center">{$LANG_TOTALMATCHESPLAYEDBYTYPE}</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="165">{$LANG_GAMEPTYPE}</td>
    <td class="smheading" align="center" width="60">{$LANG_NUMBER}</td>
    <td class="smheading" align="center" width="85">{$LANG_GAMEHOURS}</td>
    <td class="smheading" align="center" width="85">{$LANG_PLAYERHOURS}</td>
  </tr>

EOF;

$tot_played = $tot_gtime = $tot_ptime = 0;

while ($row = sql_fetch_assoc($result)) {
  while (list ($key, $val) = each ($row))
    ${$key} = $val;

  if ($tp_played > 0) {
    $tot_played += $tp_played;
    $ghours = sprintf("%0.1f", $tp_gtime / 360000);
    $phours = sprintf("%0.1f", $tp_ptime / 360000);
    $tot_gtime += $tp_gtime;
    $tot_ptime += $tp_ptime;
  
    echo <<<EOF
  <tr>
    <td class="dark" align="center">$tp_desc</td>
    <td class="grey" align="center">$tp_played</td>
    <td class="grey" align="center">$ghours</td>
    <td class="grey" align="center">$phours</td>
  </tr>
EOF;
  }
}
sql_free_result($result);

$ghours = sprintf("%0.1f", $tot_gtime / 360000);
$phours = sprintf("%0.1f", $tot_ptime / 360000);
echo <<<EOF
  <tr>
    <td class="dark" align="center">{$LANG_TOTALS}</td>
    <td class="darkgrey" align="center">$tot_played</td>
    <td class="darkgrey" align="center">$ghours</td>
    <td class="darkgrey" align="center">$phours</td>
  </tr>
</table>

EOF;

//=============================================================================
//========== Highs - From a Single Match ======================================
//=============================================================================

$fragsplayer = getplayer($tl_chfragssg_plr);
$fragstime = sprintf("%0.1f", $tl_chfragssg_tm / 6000);
if ($tl_chfragssg > 0)
  $fragsdate = formatdate(strtotime($tl_chfragssg_date), 0);
else
  $fragsdate = "&nbsp;";

$killsplayer = getplayer($tl_chkillssg_plr);
$killstime = sprintf("%0.1f", $tl_chkillssg_tm / 6000);
if ($tl_chkillssg > 0)
  $killsdate = formatdate(strtotime($tl_chkillssg_date), 0);
else
  $killsdate = "&nbsp;";

$deathsplayer = getplayer($tl_chdeathssg_plr);
$deathstime = sprintf("%0.1f", $tl_chdeathssg_tm / 6000);
if ($tl_chdeathssg > 0)
  $deathsdate = formatdate(strtotime($tl_chdeathssg_date), 0);
else
  $deathsdate = "&nbsp;";

$suicidesplayer = getplayer($tl_chsuicidessg_plr);
$suicidestime = sprintf("%0.1f", $tl_chsuicidessg_tm / 6000);
if ($tl_chsuicidessg > 0)
  $suicidesdate = formatdate(strtotime($tl_chsuicidessg_date), 0);
else
  $suicidesdate = "&nbsp;";

$flagcaptureplayer = getplayer($tl_chflagcapturesg_plr);
$flagcapturetime = sprintf("%0.1f", $tl_chflagcapturesg_tm / 6000);
if ($tl_chflagcapturesg > 0)
  $flagcapturedate = formatdate(strtotime($tl_chflagcapturesg_date), 0);
else
  $flagcapturedate = "&nbsp;";

$flagreturnplayer = getplayer($tl_chflagreturnsg_plr);
$flagreturntime = sprintf("%0.1f", $tl_chflagreturnsg_tm / 6000);
if ($tl_chflagreturnsg > 0)
  $flagreturndate = formatdate(strtotime($tl_chflagreturnsg_date), 0);
else
  $flagreturndate = "&nbsp;";

$flagkillplayer = getplayer($tl_chflagkillsg_plr);
$flagkilltime = sprintf("%0.1f", $tl_chflagkillsg_tm / 6000);
if ($tl_chflagkillsg > 0)
  $flagkilldate = formatdate(strtotime($tl_chflagkillsg_date), 0);
else
  $flagkilldate = "&nbsp;";

$cpcaptureplayer = getplayer($tl_chcpcapturesg_plr);
$cpcapturetime = sprintf("%0.1f", $tl_chcpcapturesg_tm / 6000);
if ($tl_chcpcapturesg > 0)
  $cpcapturedate = formatdate(strtotime($tl_chcpcapturesg_date), 0);
else
  $cpcapturedate = "&nbsp;";

$bombcarriedplayer = getplayer($tl_chbombcarriedsg_plr);
$bombcarriedtime = sprintf("%0.1f", $tl_chbombcarriedsg_tm / 6000);
if ($tl_chbombcarriedsg > 0)
  $bombcarrieddate = formatdate(strtotime($tl_chbombcarriedsg_date), 0);
else
  $bombcarrieddate = "&nbsp;";

$bombtossedplayer = getplayer($tl_chbombtossedsg_plr);
$bombtossedtime = sprintf("%0.1f", $tl_chbombtossedsg_tm / 6000);
if ($tl_chbombtossedsg > 0)
  $bombtosseddate = formatdate(strtotime($tl_chbombtossedsg_date), 0);
else
  $bombtosseddate = "&nbsp;";

$bombkillplayer = getplayer($tl_chbombkillsg_plr);
$bombkilltime = sprintf("%0.1f", $tl_chbombkillsg_tm / 6000);
if ($tl_chbombkillsg > 0)
  $bombkilldate = formatdate(strtotime($tl_chbombkillsg_date), 0);
else
  $bombkilldate = "&nbsp;";

$nodeconstructedplayer = getplayer($tl_chnodeconstructedsg_plr);
$nodeconstructedtime = sprintf("%0.1f", $tl_chnodeconstructedsg_tm / 6000);
if ($tl_chnodeconstructedsg > 0)
  $nodeconstructeddate = formatdate(strtotime($tl_chnodeconstructedsg_date), 0);
else
  $nodeconstructeddate = "&nbsp;";

$nodedestroyedplayer = getplayer($tl_chnodedestroyedsg_plr);
$nodedestroyedtime = sprintf("%0.1f", $tl_chnodedestroyedsg_tm / 6000);
if ($tl_chnodedestroyedsg > 0)
  $nodedestroyeddate = formatdate(strtotime($tl_chnodedestroyedsg_date), 0);
else
  $nodedestroyeddate = "&nbsp;";

$nodeconstdestroyedplayer = getplayer($tl_chnodeconstdestroyedsg_plr);
$nodeconstdestroyedtime = sprintf("%0.1f", $tl_chnodeconstdestroyedsg_tm / 6000);
if ($tl_chnodeconstdestroyedsg > 0)
  $nodeconstdestroyeddate = formatdate(strtotime($tl_chnodeconstdestroyedsg_date), 0);
else
  $nodeconstdestroyeddate = "&nbsp;";

$tl_chfragssg_mapname = map_name($tl_chfragssg_map);
$tl_chkillssg_mapname = map_name($tl_chkillssg_map);
$tl_chdeathssg_mapname = map_name($tl_chdeathssg_map);
$tl_chsuicidessg_mapname = map_name($tl_chsuicidessg_map);
$tl_chflagkillsg_mapname = map_name($tl_chflagkillsg_map);
$tl_chflagcapturesg_mapname = map_name($tl_chflagcapturesg_map);
$tl_chflagreturnsg_mapname = map_name($tl_chflagreturnsg_map);
$tl_chcpcapturesg_mapname = map_name($tl_chcpcapturesg_map);
$tl_chbombcarriedsg_mapname = map_name($tl_chbombcarriedsg_map);
$tl_chbombtossedsg_mapname = map_name($tl_chbombtossedsg_map);
$tl_chbombkillsg_mapname = map_name($tl_chbombkillsg_map);
$tl_chnodeconstructedsg_mapname = map_name($tl_chnodeconstructedsg_map);
$tl_chnodedestroyedsg_mapname = map_name($tl_chnodedestroyedsg_map);
$tl_chnodeconstdestroyedsg_mapname = map_name($tl_chnodeconstdestroyedsg_map);

echo <<<EOF
<font size="1"><br /></font>
<table cellpadding="1" cellspacing="2" border="0" width="710" class="box">
  <tr>
    <td class="heading" colspan="6" align="center">{$LANG_HIGHSFROMASINGLEMATCH}</td>
  </tr>
  <tr>
    <td class="smheading" align="center">{$LANG_CATEGORY}</td>
    <td class="smheading" align="center">{$LANG_PLAYER}</td>
    <td class="smheading" align="center">{$LANG_SCORE}</td>
    <td class="smheading" align="center">{$LANG_TIME}</td>
    <td class="smheading" align="center">{$LANG_MAP}</td>
    <td class="smheading" align="center">{$LANG_DATE}</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTFRAGS}</td>
    <td class="dark" align="center">$fragsplayer</td>
    <td class="grey" align="center">$tl_chfragssg</td>
    <td class="grey" align="center">$fragstime</td>
    <td class="grey" align="center"><a class="grey" href="mapstats.php?map=$tl_chfragssg_map">$tl_chfragssg_mapname</a></td>
    <td class="grey" align="center">$fragsdate</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTKILLS}</td>
    <td class="dark" align="center">$killsplayer</td>
    <td class="grey" align="center">$tl_chkillssg</td>
    <td class="grey" align="center">$killstime</td>
    <td class="grey" align="center"><a class="grey" href="mapstats.php?map=$tl_chkillssg_map">$tl_chkillssg_mapname</a></td>
    <td class="grey" align="center">$killsdate</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTDEATHS}</td>
    <td class="dark" align="center">$deathsplayer</td>
    <td class="grey" align="center">$tl_chdeathssg</td>
    <td class="grey" align="center">$deathstime</td>
    <td class="grey" align="center"><a class="grey" href="mapstats.php?map=$tl_chdeathssg_map">$tl_chdeathssg_mapname</a></td>
    <td class="grey" align="center">$deathsdate</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTSUICIDES}</td>
    <td class="dark" align="center">$suicidesplayer</td>
    <td class="grey" align="center">$tl_chsuicidessg</td>
    <td class="grey" align="center">$suicidestime</td>
    <td class="grey" align="center"><a class="grey" href="mapstats.php?map=$tl_chsuicidessg_map">$tl_chsuicidessg_mapname</a></td>
    <td class="grey" align="center">$suicidesdate</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTFLAGCAPTURES}</td>
    <td class="dark" align="center">$flagcaptureplayer</td>
    <td class="grey" align="center">$tl_chflagcapturesg</td>
    <td class="grey" align="center">$flagcapturetime</td>
    <td class="grey" align="center"><a class="grey" href="mapstats.php?map=$tl_chflagcapturesg_map">$tl_chflagcapturesg_mapname</a></td>
    <td class="grey" align="center">$flagcapturedate</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTFLAGRETURNS}</td>
    <td class="dark" align="center">$flagreturnplayer</td>
    <td class="grey" align="center">$tl_chflagreturnsg</td>
    <td class="grey" align="center">$flagreturntime</td>
    <td class="grey" align="center"><a class="grey" href="mapstats.php?map=$tl_chflagreturnsg_map">$tl_chflagreturnsg_mapname</a></td>
    <td class="grey" align="center">$flagreturndate</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTFLAGKILLS}</td>
    <td class="dark" align="center">$flagkillplayer</td>
    <td class="grey" align="center">$tl_chflagkillsg</td>
    <td class="grey" align="center">$flagkilltime</td>
    <td class="grey" align="center"><a class="grey" href="mapstats.php?map=$tl_chflagkillsg_map">$tl_chflagkillsg_mapname</a></td>
    <td class="grey" align="center">$flagkilldate</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTCONTROLPOINTCAPTURES}</td>
    <td class="dark" align="center">$cpcaptureplayer</td>
    <td class="grey" align="center">$tl_chcpcapturesg</td>
    <td class="grey" align="center">$cpcapturetime</td>
    <td class="grey" align="center"><a class="grey" href="mapstats.php?map=$tl_chcpcapturesg_map">$tl_chcpcapturesg_mapname</a></td>
    <td class="grey" align="center">$cpcapturedate</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTBOMBSDELIVEREDCARRIED}</td>
    <td class="dark" align="center">$bombcarriedplayer</td>
    <td class="grey" align="center">$tl_chbombcarriedsg</td>
    <td class="grey" align="center">$bombcarriedtime</td>
    <td class="grey" align="center"><a class="grey" href="mapstats.php?map=$tl_chbombcarriedsg_map">$tl_chbombcarriedsg_mapname</a></td>
    <td class="grey" align="center">$bombcarrieddate</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTBOMBSDELIVEREDTOSSED}</td>
    <td class="dark" align="center">$bombtossedplayer</td>
    <td class="grey" align="center">$tl_chbombtossedsg</td>
    <td class="grey" align="center">$bombtossedtime</td>
    <td class="grey" align="center"><a class="grey" href="mapstats.php?map=$tl_chbombtossedsg_map">$tl_chbombtossedsg_mapname</a></td>
    <td class="grey" align="center">$bombtosseddate</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTBOMBKILLS}</td>
    <td class="dark" align="center">$bombkillplayer</td>
    <td class="grey" align="center">$tl_chbombkillsg</td>
    <td class="grey" align="center">$bombkilltime</td>
    <td class="grey" align="center"><a class="grey" href="mapstats.php?map=$tl_chbombkillsg_map">$tl_chbombkillsg_mapname</a></td>
    <td class="grey" align="center">$bombkilldate</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTNODESCONSTRUCTED}</td>
    <td class="dark" align="center">$nodeconstructedplayer</td>
    <td class="grey" align="center">$tl_chnodeconstructedsg</td>
    <td class="grey" align="center">$nodeconstructedtime</td>
    <td class="grey" align="center"><a class="grey" href="mapstats.php?map=$tl_chnodeconstructedsg_map">$tl_chnodeconstructedsg_mapname</a></td>
    <td class="grey" align="center">$nodeconstructeddate</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTNODESDESTROYED}</td>
    <td class="dark" align="center">$nodedestroyedplayer</td>
    <td class="grey" align="center">$tl_chnodedestroyedsg</td>
    <td class="grey" align="center">$nodedestroyedtime</td>
    <td class="grey" align="center"><a class="grey" href="mapstats.php?map=$tl_chnodedestroyedsg_map">$tl_chnodedestroyedsg_mapname</a></td>
    <td class="grey" align="center">$nodedestroyeddate</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTCONSTNODESDESTROYED}</td>
    <td class="dark" align="center">$nodeconstdestroyedplayer</td>
    <td class="grey" align="center">$tl_chnodeconstdestroyedsg</td>
    <td class="grey" align="center">$nodeconstdestroyedtime</td>
    <td class="grey" align="center"><a class="grey" href="mapstats.php?map=$tl_chnodeconstdestroyedsg_map">$tl_chnodeconstdestroyedsg_mapname</a></td>
    <td class="grey" align="center">$nodeconstdestroyeddate</td>
  </tr>
</table>

EOF;

// *****************************
// ***** Load Weapons Data *****
// *****************************

$result = sql_queryn($link, "SELECT * FROM {$dbpre}weapons");
if (!$result) {
  echo "{$LANG_WEAPDATABASEERROR}<br />\n";
  exit;
}
$numweapons = 0;
while ($row = sql_fetch_assoc($result)) {
  for ($i = 0, $weap = -1; $i < $numweapons && $weap < 0; $i++) {
    if (strcmp($weapons[0][$i], $row["wp_desc"]) == 0)
      $weap = $i;
  }
  if ($weap < 0) {
    $weapons[0][$numweapons] = $row["wp_desc"];
    $weapons[1][$numweapons] = $row["wp_chkillssg"];
    $weapons[2][$numweapons] = $row["wp_chkillssg_plr"];
    $weapons[3][$numweapons] = $row["wp_chkillssg_tm"];
    $weapons[4][$numweapons] = $row["wp_chkillssg_map"];
    $weapons[5][$numweapons] = $row["wp_chkillssg_dt"];
    $weapons[6][$numweapons] = $row["wp_chdeathssg"];
    $weapons[7][$numweapons] = $row["wp_chdeathssg_plr"];
    $weapons[8][$numweapons] = $row["wp_chdeathssg_tm"];
    $weapons[9][$numweapons] = $row["wp_chdeathssg_map"];
    $weapons[10][$numweapons] = $row["wp_chdeathssg_dt"];
    $weapons[11][$numweapons] = $row["wp_chdeathshldsg"];
    $weapons[12][$numweapons] = $row["wp_chdeathshldsg_plr"];
    $weapons[13][$numweapons] = $row["wp_chdeathshldsg_tm"];
    $weapons[14][$numweapons] = $row["wp_chdeathshldsg_map"];
    $weapons[15][$numweapons] = $row["wp_chdeathshldsg_dt"];
    $weapons[16][$numweapons] = $row["wp_chsuicidessg"];
    $weapons[17][$numweapons] = $row["wp_chsuicidessg_plr"];
    $weapons[18][$numweapons] = $row["wp_chsuicidessg_tm"];
    $weapons[19][$numweapons] = $row["wp_chsuicidessg_map"];
    $weapons[20][$numweapons++] = $row["wp_chsuicidessg_dt"];
  }
  else {
    // Career SG Kills
    if ($row["wp_chkillssg_plr"] == $weapons[2][$weap] && $row["wp_chkillssg_dt"] == $weapons[5][$weap])
      $weapons[1][$weap] += $row["wp_chkillssg"];
    else if ($row["wp_chkillssg"] > $weapons[1][$weap]) {
      $weapons[1][$weap] = $row["wp_chkillssg"];
      $weapons[2][$weap] = $row["wp_chkillssg_plr"];
      $weapons[3][$weap] = $row["wp_chkillssg_tm"];
      $weapons[4][$weap] = $row["wp_chkillssg_map"];
      $weapons[5][$weap] = $row["wp_chkillssg_dt"];
    }

    // Career SG Deaths
    if ($row["wp_chdeathssg_plr"] == $weapons[7][$weap] && $row["wp_chdeathssg_dt"] == $weapons[10][$weap])
      $weapons[6][$weap] += $row["wp_chdeathssg"];
    else if ($row["wp_chdeathssg"] > $weapons[6][$weap]) {
      $weapons[6][$weap] = $row["wp_chdeathssg"];
      $weapons[7][$weap] = $row["wp_chdeathssg_plr"];
      $weapons[8][$weap] = $row["wp_chdeathssg_tm"];
      $weapons[9][$weap] = $row["wp_chdeathssg_map"];
      $weapons[10][$weap] = $row["wp_chdeathssg_dt"];
    }

    // Career SG Deaths while Holding
    if ($row["wp_chdeathshldsg_plr"] == $weapons[12][$weap] && $row["wp_chdeathshldsg_dt"] == $weapons[15][$weap])
      $weapons[11][$weap] += $row["wp_chdeathshldsg"];
    else if ($row["wp_chdeathshldsg"] > $weapons[11][$weap]) {
      $weapons[11][$weap] = $row["wp_chdeathshldsg"];
      $weapons[12][$weap] = $row["wp_chdeathshldsg_plr"];
      $weapons[13][$weap] = $row["wp_chdeathshldsg_tm"];
      $weapons[14][$weap] = $row["wp_chdeathshldsg_map"];
      $weapons[15][$weap] = $row["wp_chdeathshldsg_dt"];
    }

    // Career SG Suicides
    if ($row["wp_chsuicidessg_plr"] == $weapons[17][$weap] && $row["wp_chsuicidessg_dt"] == $weapons[20][$weap])
      $weapons[16][$weap] += $row["wp_chsuicidessg"];
    else if ($row["wp_chsuicidessg"] > $weapons[16][$weap]) {
      $weapons[16][$weap] = $row["wp_chsuicidessg"];
      $weapons[17][$weap] = $row["wp_chsuicidessg_plr"];
      $weapons[18][$weap] = $row["wp_chsuicidessg_tm"];
      $weapons[19][$weap] = $row["wp_chsuicidessg_map"];
      $weapons[20][$weap] = $row["wp_chsuicidessg_dt"];
    }
  }
}
sql_free_result($result);

//=============================================================================
//========== Most Kills with a Weapon - From a Single Match ===================
//=============================================================================

echo <<<EOF
<font size="1"><br /></font>
<table cellpadding="1" cellspacing="2" border="0" width="710" class="box">
  <tr>
    <td class="heading" colspan="6" align="center">{$LANG_MOSTKILLSWITHWEAPONINGLEMATCH}</td>
  </tr>
  <tr>
    <td class="smheading" align="center">{$LANG_WEAPON}</td>
    <td class="smheading" align="center">{$LANG_PLAYER}</td>
    <td class="smheading" align="center">{$LANG_KILLS}</td>
    <td class="smheading" align="center">{$LANG_TIME}</td>
    <td class="smheading" align="center">{$LANG_MAP}</td>
    <td class="smheading" align="center">{$LANG_DATE}</td>
  </tr>

EOF;
showweapons(1);

//=============================================================================
//========== Most Deaths by a Weapon - From a Single Match ====================
//=============================================================================

echo <<<EOF
<font size="1"><br /></font>
<table cellpadding="1" cellspacing="2" border="0" width="710" class="box">
  <tr>
    <td class="heading" colspan="6" align="center">{$LANG_MOSTDEATHSBYWEAPONSINGLEMATCH}</td>
  </tr>
  <tr>
    <td class="smheading" align="center">{$LANG_WEAPON}</td>
    <td class="smheading" align="center">{$LANG_PLAYER}</td>
    <td class="smheading" align="center">{$LANG_DEATHS}</td>
    <td class="smheading" align="center">{$LANG_TIME}</td>
    <td class="smheading" align="center">{$LANG_MAP}</td>
    <td class="smheading" align="center">{$LANG_DATE}</td>
  </tr>

EOF;
showweapons(2);

//=============================================================================
//========== Most Deaths While Holding a Weapon - From a Single Match =========
//=============================================================================

echo <<<EOF
<font size="1"><br /></font>
<table cellpadding="1" cellspacing="2" border="0" width="710" class="box">
  <tr>
    <td class="heading" colspan="6" align="center">{$LANG_MOSTDEATHSHOLDINGWEAPONSINGLEMATCH}</td>
  </tr>
  <tr>
    <td class="smheading" align="center">{$LANG_WEAPON}</td>
    <td class="smheading" align="center">{$LANG_PLAYER}</td>
    <td class="smheading" align="center">{$LANG_DEATHS}</td>
    <td class="smheading" align="center">{$LANG_TIME}</td>
    <td class="smheading" align="center">{$LANG_MAP}</td>
    <td class="smheading" align="center">{$LANG_DATE}</td>
  </tr>

EOF;
showweapons(3);

//=============================================================================
//========== Most Suicides - From a Single Match ==============================
//=============================================================================

echo <<<EOF
<font size="1"><br /></font>
<table cellpadding="1" cellspacing="2" border="0" width="710" class="box">
  <tr>
    <td class="heading" colspan="6" align="center">{$LANG_MOSTSUICIDESFROMSINGLEMATCH}</td>
  </tr>
  <tr>
    <td class="smheading" align="center">{$LANG_CAUSE}</td>
    <td class="smheading" align="center">{$LANG_PLAYER}</td>
    <td class="smheading" align="center">{$LANG_SUICIDES}</td>
    <td class="smheading" align="center">{$LANG_TIME}</td>
    <td class="smheading" align="center">{$LANG_MAP}</td>
    <td class="smheading" align="center">{$LANG_DATE}</td>
  </tr>

EOF;
showweapons(4);

echo <<<EOF
</center>

</td></tr></table>

</body>
</html>

EOF;

sql_close($link);

?>