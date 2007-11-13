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
  global $weapons, $numweapons;

  // Sort by num, games, time, description, player
  switch ($group) {
    case 1: // Kills
      array_multisort($weapons[1], SORT_DESC, SORT_NUMERIC,
                      $weapons[4], SORT_ASC, SORT_NUMERIC,
                      $weapons[3], SORT_ASC, SORT_NUMERIC,
                      $weapons[0], SORT_ASC, SORT_STRING,
                      $weapons[2], SORT_ASC, SORT_NUMERIC,
                      $weapons[5], $weapons[6], $weapons[7], $weapons[8],
                      $weapons[9], $weapons[10], $weapons[11], $weapons[12],
                      $weapons[13], $weapons[14], $weapons[15], $weapons[16]);
      break;
    case 2: // Deaths
      array_multisort($weapons[5], SORT_DESC, SORT_NUMERIC,
                      $weapons[8], SORT_ASC, SORT_NUMERIC,
                      $weapons[7], SORT_ASC, SORT_NUMERIC,
                      $weapons[0], SORT_ASC, SORT_STRING,
                      $weapons[6], SORT_ASC, SORT_NUMERIC,
                      $weapons[1], $weapons[2], $weapons[3], $weapons[4],
                      $weapons[9], $weapons[10], $weapons[11], $weapons[12],
                      $weapons[13], $weapons[14], $weapons[15], $weapons[16]);
      break;
    case 3: // Deaths while Holding
      array_multisort($weapons[9], SORT_DESC, SORT_NUMERIC,
                      $weapons[12], SORT_ASC, SORT_NUMERIC,
                      $weapons[11], SORT_ASC, SORT_NUMERIC,
                      $weapons[0], SORT_ASC, SORT_STRING,
                      $weapons[10], SORT_ASC, SORT_NUMERIC,
                      $weapons[1], $weapons[2], $weapons[3], $weapons[4],
                      $weapons[5], $weapons[6], $weapons[7], $weapons[8],
                      $weapons[13], $weapons[14], $weapons[15], $weapons[16]);
      break;
    case 4: // Suicides
      array_multisort($weapons[13], SORT_DESC, SORT_NUMERIC,
                      $weapons[16], SORT_ASC, SORT_NUMERIC,
                      $weapons[15], SORT_ASC, SORT_NUMERIC,
                      $weapons[0], SORT_ASC, SORT_STRING,
                      $weapons[14], SORT_ASC, SORT_NUMERIC,
                      $weapons[1], $weapons[2], $weapons[3], $weapons[4],
                      $weapons[5], $weapons[6], $weapons[7], $weapons[8],
                      $weapons[9], $weapons[10], $weapons[11], $weapons[12]);
      break;
  }

  for ($i = 0; $i < $numweapons; $i++) {
    $num = $weapons[$group * 4 - 3][$i];
    if ($num > 0) {
      $wpdesc = $weapons[0][$i];
      if (strcmp($wpdesc, "None")) {
        $player = getplayer($weapons[$group * 4 - 2][$i]);
        $time = sprintf("%0.1f", $weapons[$group * 4 - 1][$i] / 360000);
        $games = $weapons[$group * 4][$i];

        echo <<< EOF
    <tr>
      <td class="dark" align="center">$wpdesc</td>
      <td class="darkhuman" align="center">$player</td>
      <td class="grey" align="center">$num</td>
      <td class="grey" align="center">$games</td>
      <td class="grey" align="center">$time</td>
    </tr>

EOF;
      }
    }
  }
  echo "</table>\n";
}

require("includes/main.inc.php");

$link = sql_connect();
$result = sql_queryn($link, "SELECT * FROM {$dbpre}totals LIMIT 1");
if (!$result) {
  echo "{$LANG_DATABASEERROR}<br />\n";
  exit;
}
$row = sql_fetch_array($result);
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
    <td class="smheading" align="center" width="60">$LANG_FRAGS</td>
    <td class="smheading" align="center" width="60">$LANG_KILLS</td>
    <td class="smheading" align="center" width="60">$LANG_DEATHS</td>
    <td class="smheading" align="center" width="60">$LANG_SUICIDES</td>
    <td class="smheading" align="center" width="55">$LANG_MATCHES</td>
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

while ($row = sql_fetch_array($result)) {
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
//========== Career Highs =====================================================
//=============================================================================

$fragsplayer = getplayer($tl_chfrags_plr);
$fragstime = sprintf("%0.1f", $tl_chfrags_tm / 360000);
$killsplayer = getplayer($tl_chkills_plr);
$killstime = sprintf("%0.1f", $tl_chkills_tm / 360000);
$deathsplayer = getplayer($tl_chdeaths_plr);
$deathstime = sprintf("%0.1f", $tl_chdeaths_tm / 360000);
$suicidesplayer = getplayer($tl_chsuicides_plr);
$suicidestime = sprintf("%0.1f", $tl_chsuicides_tm / 360000);
$headshotsplayer = getplayer($tl_chheadshots_plr);
$headshotstime = sprintf("%0.1f", $tl_chheadshots_tm / 360000);
$firstbloodplayer = getplayer($tl_chfirstblood_plr);
$firstbloodtime = sprintf("%0.1f", $tl_chfirstblood_tm / 360000);
$carjackplayer = getplayer($tl_chcarjack_plr);
$carjacktime = sprintf("%0.1f", $tl_chcarjack_tm / 360000);
$roadkillsplayer = getplayer($tl_chroadkills_plr);
$roadkillstime = sprintf("%0.1f", $tl_chroadkills_tm / 360000);
$headhunterplayer = getplayer($tl_chheadhunter_plr);
$headhuntertime = sprintf("%0.1f", $tl_chheadhunter_tm / 360000);
$flakmonkeyplayer = getplayer($tl_chflakmonkey_plr);
$flakmonkeytime = sprintf("%0.1f", $tl_chflakmonkey_tm / 360000);
$combowhoreplayer = getplayer($tl_chcombowhore_plr);
$combowhoretime = sprintf("%0.1f", $tl_chcombowhore_tm / 360000);
$roadrampageplayer = getplayer($tl_chroadrampage_plr);
$roadrampagetime = sprintf("%0.1f", $tl_chroadrampage_tm / 360000);
$multi1player = getplayer($tl_chmulti1_plr);
$multi1time = sprintf("%0.1f", $tl_chmulti1_tm / 360000);
$multi2player = getplayer($tl_chmulti2_plr);
$multi2time = sprintf("%0.1f", $tl_chmulti2_tm / 360000);
$multi3player = getplayer($tl_chmulti3_plr);
$multi3time = sprintf("%0.1f", $tl_chmulti3_tm / 360000);
$multi4player = getplayer($tl_chmulti4_plr);
$multi4time = sprintf("%0.1f", $tl_chmulti4_tm / 360000);
$multi5player = getplayer($tl_chmulti5_plr);
$multi5time = sprintf("%0.1f", $tl_chmulti5_tm / 360000);
$multi6player = getplayer($tl_chmulti6_plr);
$multi6time = sprintf("%0.1f", $tl_chmulti6_tm / 360000);
$multi7player = getplayer($tl_chmulti7_plr);
$multi7time = sprintf("%0.1f", $tl_chmulti7_tm / 360000);
$spree1player = getplayer($tl_chspree1_plr);
$spree1time = sprintf("%0.1f", $tl_chspree1_tm / 360000);
$spree2player = getplayer($tl_chspree2_plr);
$spree2time = sprintf("%0.1f", $tl_chspree2_tm / 360000);
$spree3player = getplayer($tl_chspree3_plr);
$spree3time = sprintf("%0.1f", $tl_chspree3_tm / 360000);
$spree4player = getplayer($tl_chspree4_plr);
$spree4time = sprintf("%0.1f", $tl_chspree4_tm / 360000);
$spree5player = getplayer($tl_chspree5_plr);
$spree5time = sprintf("%0.1f", $tl_chspree5_tm / 360000);
$spree6player = getplayer($tl_chspree6_plr);
$spree6time = sprintf("%0.1f", $tl_chspree6_tm / 360000);
$fph = sprintf("%0.1f", $tl_chfph);
$fphplayer = getplayer($tl_chfph_plr);
$fphtime = sprintf("%0.1f", $tl_chfph_tm / 360000);
$winsplayer = getplayer($tl_chwins_plr);
$winstime = sprintf("%0.1f", $tl_chwins_tm / 360000);
$teamwinsplayer = getplayer($tl_chteamwins_plr);
$teamwinstime = sprintf("%0.1f", $tl_chteamwins_tm / 360000);
$flagcaptureplayer = getplayer($tl_chflagcapture_plr);
$flagcapturetime = sprintf("%0.1f", $tl_chflagcapture_tm / 360000);
$flagreturnplayer = getplayer($tl_chflagreturn_plr);
$flagreturntime = sprintf("%0.1f", $tl_chflagreturn_tm / 360000);
$flagkillplayer = getplayer($tl_chflagkill_plr);
$flagkilltime = sprintf("%0.1f", $tl_chflagkill_tm / 360000);
$cpcaptureplayer = getplayer($tl_chcpcapture_plr);
$cpcapturetime = sprintf("%0.1f", $tl_chcpcapture_tm / 360000);
$bombcarriedplayer = getplayer($tl_chbombcarried_plr);
$bombcarriedtime = sprintf("%0.1f", $tl_chbombcarried_tm / 360000);
$bombtossedplayer = getplayer($tl_chbombtossed_plr);
$bombtossedtime = sprintf("%0.1f", $tl_chbombtossed_tm / 360000);
$bombkillplayer = getplayer($tl_chbombkill_plr);
$bombkilltime = sprintf("%0.1f", $tl_chbombkill_tm / 360000);
$nodeconstructedplayer = getplayer($tl_chnodeconstructed_plr);
$nodeconstructedtime = sprintf("%0.1f", $tl_chnodeconstructed_tm / 360000);
$nodedestroyedplayer = getplayer($tl_chnodedestroyed_plr);
$nodedestroyedtime = sprintf("%0.1f", $tl_chnodedestroyed_tm / 360000);
$nodeconstdestroyedplayer = getplayer($tl_chnodeconstdestroyed_plr);
$nodeconstdestroyedtime = sprintf("%0.1f", $tl_chnodeconstdestroyed_tm / 360000);

echo <<<EOF
<font size="1"><br /></font>
<table cellpadding="1" cellspacing="2" border="0" width="580" class="box">
  <tr>
    <td class="heading" colspan="5" align="center">{$LANG_CAREERHIGHS}</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="220">{$LANG_CATEGORY}</td>
    <td class="smheading" align="center">{$LANG_PLAYER}</td>
    <td class="smheading" align="center" width="60">{$LANG_SCORE}</td>
    <td class="smheading" align="center" width="60">{$LANG_MATCHES}</td>
    <td class="smheading" align="center" width="60">{$LANG_HOURS}</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTFRAGS}</td>
    <td class="darkhuman" align="center">$fragsplayer</td>
    <td class="grey" align="center">$tl_chfrags</td>
    <td class="grey" align="center">$tl_chfrags_gms</td>
    <td class="grey" align="center">$fragstime</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTKILLS}</td>
    <td class="darkhuman" align="center">$killsplayer</td>
    <td class="grey" align="center">$tl_chkills</td>
    <td class="grey" align="center">$tl_chkills_gms</td>
    <td class="grey" align="center">$killstime</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTDEATHS}</td>
    <td class="darkhuman" align="center">$deathsplayer</td>
    <td class="grey" align="center">$tl_chdeaths</td>
    <td class="grey" align="center">$tl_chdeaths_gms</td>
    <td class="grey" align="center">$deathstime</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTSUICIDES}</td>
    <td class="darkhuman" align="center">$suicidesplayer</td>
    <td class="grey" align="center">$tl_chsuicides</td>
    <td class="grey" align="center">$tl_chsuicides_gms</td>
    <td class="grey" align="center">$suicidestime</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTWINS}</td>
    <td class="darkhuman" align="center">$winsplayer</td>
    <td class="grey" align="center">$tl_chwins</td>
    <td class="grey" align="center">$tl_chwins_gms</td>
    <td class="grey" align="center">$winstime</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTTEAMWINS}</td>
    <td class="darkhuman" align="center">$teamwinsplayer</td>
    <td class="grey" align="center">$tl_chteamwins</td>
    <td class="grey" align="center">$tl_chteamwins_gms</td>
    <td class="grey" align="center">$teamwinstime</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTHEADSHOTS}</td>
    <td class="darkhuman" align="center">$headshotsplayer</td>
    <td class="grey" align="center">$tl_chheadshots</td>
    <td class="grey" align="center">$tl_chheadshots_gms</td>
    <td class="grey" align="center">$headshotstime</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_HIGHESTFPH}</td>
    <td class="darkhuman" align="center">$fphplayer</td>
    <td class="grey" align="center">$fph</td>
    <td class="grey" align="center">$tl_chfph_gms</td>
    <td class="grey" align="center">$fphtime</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTFIRSTBLOOD}</td>
    <td class="darkhuman" align="center">$firstbloodplayer</td>
    <td class="grey" align="center">$tl_chfirstblood</td>
    <td class="grey" align="center">$tl_chfirstblood_gms</td>
    <td class="grey" align="center">$firstbloodtime</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTCARJACKINGS}</td>
    <td class="darkhuman" align="center">$carjackplayer</td>
    <td class="grey" align="center">$tl_chcarjack</td>
    <td class="grey" align="center">$tl_chcarjack_gms</td>
    <td class="grey" align="center">$carjacktime</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTROADKILLS}</td>
    <td class="darkhuman" align="center">$roadkillsplayer</td>
    <td class="grey" align="center">$tl_chroadkills</td>
    <td class="grey" align="center">$tl_chroadkills_gms</td>
    <td class="grey" align="center">$roadkillstime</td>
  </tr>
</table>
<font size="1"><br /></font>
<table cellpadding="1" cellspacing="2" border="0" width="580" class="box">
  <tr>
    <td class="heading" colspan="5" align="center">{$LANG_WEAPONSPECIALS}</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="220">{$LANG_CATEGORY}</td>
    <td class="smheading" align="center">{$LANG_PLAYER}</td>
    <td class="smheading" align="center" width="60">{$LANG_SCORE}</td>
    <td class="smheading" align="center" width="60">{$LANG_MATCHES}</td>
    <td class="smheading" align="center" width="60">{$LANG_HOURS}</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTHEADHUNTER}</td>
    <td class="darkhuman" align="center">$headhunterplayer</td>
    <td class="grey" align="center">$tl_chheadhunter</td>
    <td class="grey" align="center">$tl_chheadhunter_gms</td>
    <td class="grey" align="center">$headhuntertime</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTFLAKMONKEY}</td>
    <td class="darkhuman" align="center">$flakmonkeyplayer</td>
    <td class="grey" align="center">$tl_chflakmonkey</td>
    <td class="grey" align="center">$tl_chflakmonkey_gms</td>
    <td class="grey" align="center">$flakmonkeytime</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTCOMBOWHORE}</td>
    <td class="darkhuman" align="center">$combowhoreplayer</td>
    <td class="grey" align="center">$tl_chcombowhore</td>
    <td class="grey" align="center">$tl_chcombowhore_gms</td>
    <td class="grey" align="center">$combowhoretime</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTROADRAMPAGE}</td>
    <td class="darkhuman" align="center">$roadrampageplayer</td>
    <td class="grey" align="center">$tl_chroadrampage</td>
    <td class="grey" align="center">$tl_chroadrampage_gms</td>
    <td class="grey" align="center">$roadrampagetime</td>
  </tr>
</table>
<font size="1"><br /></font>
<table cellpadding="1" cellspacing="2" border="0" width="580" class="box">
  <tr>
    <td class="heading" colspan="5" align="center">{$LANG_MULTIKILLS}</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="220">{$LANG_CATEGORY}</td>
    <td class="smheading" align="center">{$LANG_PLAYER}</td>
    <td class="smheading" align="center" width="60">{$LANG_SCORE}</td>
    <td class="smheading" align="center" width="60">{$LANG_MATCHES}</td>
    <td class="smheading" align="center" width="60">{$LANG_HOURS}</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTDOUBLEKILLS}</td>
    <td class="darkhuman" align="center">$multi1player</td>
    <td class="grey" align="center">$tl_chmulti1</td>
    <td class="grey" align="center">$tl_chmulti1_gms</td>
    <td class="grey" align="center">$multi1time</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTMULTIKILLS}</td>
    <td class="darkhuman" align="center">$multi2player</td>
    <td class="grey" align="center">$tl_chmulti2</td>
    <td class="grey" align="center">$tl_chmulti2_gms</td>
    <td class="grey" align="center">$multi2time</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTMEGAKILLS}</td>
    <td class="darkhuman" align="center">$multi3player</td>
    <td class="grey" align="center">$tl_chmulti3</td>
    <td class="grey" align="center">$tl_chmulti3_gms</td>
    <td class="grey" align="center">$multi3time</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTULTRAKILLS}</td>
    <td class="darkhuman" align="center">$multi4player</td>
    <td class="grey" align="center">$tl_chmulti4</td>
    <td class="grey" align="center">$tl_chmulti4_gms</td>
    <td class="grey" align="center">$multi4time</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTMONSTERKILLS}</td>
    <td class="darkhuman" align="center">$multi5player</td>
    <td class="grey" align="center">$tl_chmulti5</td>
    <td class="grey" align="center">$tl_chmulti5_gms</td>
    <td class="grey" align="center">$multi5time</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTLUDICROUSKILLS}</td>
    <td class="darkhuman" align="center">$multi6player</td>
    <td class="grey" align="center">$tl_chmulti6</td>
    <td class="grey" align="center">$tl_chmulti6_gms</td>
    <td class="grey" align="center">$multi6time</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTHOLYSHITKILLS}</td>
    <td class="darkhuman" align="center">$multi7player</td>
    <td class="grey" align="center">$tl_chmulti7</td>
    <td class="grey" align="center">$tl_chmulti7_gms</td>
    <td class="grey" align="center">$multi7time</td>
  </tr>
</table>
<font size="1"><br /></font>
<table cellpadding="1" cellspacing="2" border="0" width="580" class="box">
  <tr>
    <td class="heading" colspan="5" align="center">{$LANG_KILLINGSPREES}</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="220">{$LANG_CATEGORY}</td>
    <td class="smheading" align="center">{$LANG_PLAYER}</td>
    <td class="smheading" align="center" width="60">{$LANG_SCORE}</td>
    <td class="smheading" align="center" width="60">{$LANG_MATCHES}</td>
    <td class="smheading" align="center" width="60">{$LANG_HOURS}</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTKILLINGSPREES}</td>
    <td class="darkhuman" align="center">$spree1player</td>
    <td class="grey" align="center">$tl_chspree1</td>
    <td class="grey" align="center">$tl_chspree1_gms</td>
    <td class="grey" align="center">$spree1time</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTRAMPAGES}</td>
    <td class="darkhuman" align="center">$spree2player</td>
    <td class="grey" align="center">$tl_chspree2</td>
    <td class="grey" align="center">$tl_chspree2_gms</td>
    <td class="grey" align="center">$spree2time</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTDOMINATING}</td>
    <td class="darkhuman" align="center">$spree3player</td>
    <td class="grey" align="center">$tl_chspree3</td>
    <td class="grey" align="center">$tl_chspree3_gms</td>
    <td class="grey" align="center">$spree3time</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTUNSTOPPABLE}</td>
    <td class="darkhuman" align="center">$spree4player</td>
    <td class="grey" align="center">$tl_chspree4</td>
    <td class="grey" align="center">$tl_chspree4_gms</td>
    <td class="grey" align="center">$spree4time</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTGODLIKE}</td>
    <td class="darkhuman" align="center">$spree5player</td>
    <td class="grey" align="center">$tl_chspree5</td>
    <td class="grey" align="center">$tl_chspree5_gms</td>
    <td class="grey" align="center">$spree5time</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTWICKEDSICK}</td>
    <td class="darkhuman" align="center">$spree6player</td>
    <td class="grey" align="center">$tl_chspree6</td>
    <td class="grey" align="center">$tl_chspree6_gms</td>
    <td class="grey" align="center">$spree6time</td>
  </tr>
</table>
<font size="1"><br /></font>
<table cellpadding="1" cellspacing="2" border="0" width="580" class="box">
  <tr>
    <td class="heading" colspan="5" align="center">{$LANG_GAMETYPESPECIFIC}</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="220">{$LANG_CATEGORY}</td>
    <td class="smheading" align="center">{$LANG_PLAYER}</td>
    <td class="smheading" align="center" width="60">{$LANG_SCORE}</td>
    <td class="smheading" align="center" width="60">{$LANG_MATCHES}</td>
    <td class="smheading" align="center" width="60">{$LANG_HOURS}</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTFLAGCAPTURES}</td>
    <td class="darkhuman" align="center">$flagcaptureplayer</td>
    <td class="grey" align="center">$tl_chflagcapture</td>
    <td class="grey" align="center">$tl_chflagcapture_gms</td>
    <td class="grey" align="center">$flagcapturetime</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTFLAGRETURNS}</td>
    <td class="darkhuman" align="center">$flagreturnplayer</td>
    <td class="grey" align="center">$tl_chflagreturn</td>
    <td class="grey" align="center">$tl_chflagreturn_gms</td>
    <td class="grey" align="center">$flagreturntime</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTFLAGKILLS}</td>
    <td class="darkhuman" align="center">$flagkillplayer</td>
    <td class="grey" align="center">$tl_chflagkill</td>
    <td class="grey" align="center">$tl_chflagkill_gms</td>
    <td class="grey" align="center">$flagkilltime</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTCONTROLPOINTCAPTURES}</td>
    <td class="darkhuman" align="center">$cpcaptureplayer</td>
    <td class="grey" align="center">$tl_chcpcapture</td>
    <td class="grey" align="center">$tl_chcpcapture_gms</td>
    <td class="grey" align="center">$cpcapturetime</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTBOMBSDELIVEREDCARRIED}</td>
    <td class="darkhuman" align="center">$bombcarriedplayer</td>
    <td class="grey" align="center">$tl_chbombcarried</td>
    <td class="grey" align="center">$tl_chbombcarried_gms</td>
    <td class="grey" align="center">$bombcarriedtime</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTBOMBSDELIVEREDTOSSED}</td>
    <td class="darkhuman" align="center">$bombtossedplayer</td>
    <td class="grey" align="center">$tl_chbombtossed</td>
    <td class="grey" align="center">$tl_chbombtossed_gms</td>
    <td class="grey" align="center">$bombtossedtime</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTBOMBKILLS}</td>
    <td class="darkhuman" align="center">$bombkillplayer</td>
    <td class="grey" align="center">$tl_chbombkill</td>
    <td class="grey" align="center">$tl_chbombkill_gms</td>
    <td class="grey" align="center">$bombkilltime</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTNODESCONSTRUCTED}</td>
    <td class="darkhuman" align="center">$nodeconstructedplayer</td>
    <td class="grey" align="center">$tl_chnodeconstructed</td>
    <td class="grey" align="center">$tl_chnodeconstructed_gms</td>
    <td class="grey" align="center">$nodeconstructedtime</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTNODESDESTROYED}</td>
    <td class="darkhuman" align="center">$nodedestroyedplayer</td>
    <td class="grey" align="center">$tl_chnodedestroyed</td>
    <td class="grey" align="center">$tl_chnodedestroyed_gms</td>
    <td class="grey" align="center">$nodedestroyedtime</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MOSTCONSTNODESDESTROYED}</td>
    <td class="darkhuman" align="center">$nodeconstdestroyedplayer</td>
    <td class="grey" align="center">$tl_chnodeconstdestroyed</td>
    <td class="grey" align="center">$tl_chnodeconstdestroyed_gms</td>
    <td class="grey" align="center">$nodeconstdestroyedtime</td>
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
while ($row = sql_fetch_array($result)) {
  for ($i = 0, $weap = -1; $i < $numweapons && $weap < 0; $i++) {
    if (strcmp($weapons[0][$i], $row["wp_desc"]) == 0)
      $weap = $i;
  }
  if ($weap < 0) {
    $weapons[0][$numweapons] = $row["wp_desc"];
    $weapons[1][$numweapons] = $row["wp_chkills"];
    $weapons[2][$numweapons] = $row["wp_chkills_plr"];
    $weapons[3][$numweapons] = $row["wp_chkills_tm"];
    $weapons[4][$numweapons] = $row["wp_chkills_gms"];
    $weapons[5][$numweapons] = $row["wp_chdeaths"];
    $weapons[6][$numweapons] = $row["wp_chdeaths_plr"];
    $weapons[7][$numweapons] = $row["wp_chdeaths_tm"];
    $weapons[8][$numweapons] = $row["wp_chdeaths_gms"];
    $weapons[9][$numweapons] = $row["wp_chdeathshld"];
    $weapons[10][$numweapons] = $row["wp_chdeathshld_plr"];
    $weapons[11][$numweapons] = $row["wp_chdeathshld_tm"];
    $weapons[12][$numweapons] = $row["wp_chdeathshld_gms"];
    $weapons[13][$numweapons] = $row["wp_chsuicides"];
    $weapons[14][$numweapons] = $row["wp_chsuicides_plr"];
    $weapons[15][$numweapons] = $row["wp_chsuicides_tm"];
    $weapons[16][$numweapons++] = $row["wp_chsuicides_gms"];
  }
  else {
    // Career Kills
    if ($row["wp_chkills_plr"] == $weapons[2][$weap]) {
      $weapons[1][$weap] += $row["wp_chkills"];
      if ($row["wp_chkills_gms"] > $weapons[4][$weap]) {
        $weapons[3][$weap] = $row["wp_chkills_tm"];
        $weapons[4][$weap] = $row["wp_chkills_gms"];
      }
    }
    else if ($row["wp_chkills"] > $weapons[1][$weap]) {
      $weapons[1][$weap] = $row["wp_chkills"];
      $weapons[2][$weap] = $row["wp_chkills_plr"];
      if ($row["wp_chkills_gms"] > $weapons[4][$weap]) {
        $weapons[3][$weap] = $row["wp_chkills_tm"];
        $weapons[4][$weap] = $row["wp_chkills_gms"];
      }
    }
    // Career Deaths
    if ($row["wp_chdeaths_plr"] == $weapons[6][$weap]) {
      $weapons[5][$weap] += $row["wp_chdeaths"];
      if ($row["wp_chdeaths_gms"] > $weapons[8][$weap]) {
        $weapons[7][$weap] = $row["wp_chdeaths_tm"];
        $weapons[8][$weap] = $row["wp_chdeaths_gms"];
      }
    }
    else if ($row["wp_chdeaths"] > $weapons[5][$weap]) {
      $weapons[5][$weap] = $row["wp_chdeaths"];
      $weapons[6][$weap] = $row["wp_chdeaths_plr"];
      if ($row["wp_chdeaths_gms"] > $weapons[8][$weap]) {
        $weapons[7][$weap] = $row["wp_chdeaths_tm"];
        $weapons[8][$weap] = $row["wp_chdeaths_gms"];
      }
    }
    // Career Deaths while Holding
    if ($row["wp_chdeathshld_plr"] == $weapons[10][$weap]) {
      $weapons[9][$weap] += $row["wp_chdeathshld"];
      if ($row["wp_chdeathshld_gms"] > $weapons[12][$weap]) {
        $weapons[11][$weap] = $row["wp_chdeathshld_tm"];
        $weapons[12][$weap] = $row["wp_chdeathshld_gms"];
      }
    }
    else if ($row["wp_chdeathshld"] > $weapons[9][$weap]) {
      $weapons[9][$weap] = $row["wp_chdeathshld"];
      $weapons[10][$weap] = $row["wp_chdeathshld_plr"];
      if ($row["wp_chdeathshld_gms"] > $weapons[12][$weap]) {
        $weapons[11][$weap] = $row["wp_chdeathshld_tm"];
        $weapons[12][$weap] = $row["wp_chdeathshld_gms"];
      }
    }
    // Career Suicides
    if ($row["wp_chsuicides_plr"] == $weapons[14][$weap]) {
      $weapons[13][$weap] += $row["wp_chsuicides"];
      if ($row["wp_chsuicides_gms"] > $weapons[16][$weap]) {
        $weapons[15][$weap] = $row["wp_chsuicides_tm"];
        $weapons[16][$weap] = $row["wp_chsuicides_gms"];
      }
    }
    else if ($row["wp_chsuicides"] > $weapons[13][$weap]) {
      $weapons[13][$weap] = $row["wp_chsuicides"];
      $weapons[14][$weap] = $row["wp_chsuicides_plr"];
      if ($row["wp_chsuicides_gms"] > $weapons[16][$weap]) {
        $weapons[15][$weap] = $row["wp_chsuicides_tm"];
        $weapons[16][$weap] = $row["wp_chsuicides_gms"];
      }
    }
  }
}
sql_free_result($result);

//=============================================================================
//========== Most Career Kills with a Weapon ==================================
//=============================================================================

echo <<<EOF
<font size="1"><br /></font>
<table cellpadding="1" cellspacing="2" border="0" width="550" class="box">
  <tr>
    <td class="heading" colspan="6" align="center">{$LANG_MOSTCAREERKILLSWITHAWEAPON}</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="180">{$LANG_WEAPON}</td>
    <td class="smheading" align="center">{$LANG_PLAYER}</td>
    <td class="smheading" align="center" width="60">{$LANG_KILLS}</td>
    <td class="smheading" align="center" width="60">{$LANG_MATCHES}</td>
    <td class="smheading" align="center" width="60">{$LANG_HOURS}</td>
  </tr>

EOF;
showweapons(1);

//=============================================================================
//========== Most Career Deaths by a Weapon ===================================
//=============================================================================

echo <<<EOF
<font size="1"><br /></font>
<table cellpadding="1" cellspacing="2" border="0" width="550" class="box">
  <tr>
    <td class="heading" colspan="6" align="center">{$LANG_MOSTCAREERDEATHSBYAWEAPON}</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="180">{$LANG_WEAPON}</td>
    <td class="smheading" align="center">{$LANG_PLAYER}</td>
    <td class="smheading" align="center" width="60">{$LANG_DEATHS}</td>
    <td class="smheading" align="center" width="60">{$LANG_MATCHES}</td>
    <td class="smheading" align="center" width="60">{$LANG_HOURS}</td>
  </tr>

EOF;
showweapons(2);

//=============================================================================
//========== Most Career Deaths While Holding a Weapon ========================
//=============================================================================

echo <<<EOF
<font size="1"><br /></font>
<table cellpadding="1" cellspacing="2" border="0" width="550" class="box">
  <tr>
    <td class="heading" colspan="6" align="center">{$LANG_MOSTCAREERDEATHSWHILEHOLDING}</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="180">{$LANG_WEAPON}</td>
    <td class="smheading" align="center">{$LANG_PLAYER}</td>
    <td class="smheading" align="center" width="60">{$LANG_DEATHS}</td>
    <td class="smheading" align="center" width="60">{$LANG_MATCHES}</td>
    <td class="smheading" align="center" width="60">{$LANG_HOURS}</td>
  </tr>

EOF;
showweapons(3);

//=============================================================================
//========== Most Career Suicides =============================================
//=============================================================================

echo <<<EOF
<font size="1"><br /></font>
<table cellpadding="1" cellspacing="2" border="0" width="550" class="box">
  <tr>
    <td class="heading" colspan="6" align="center">{$LANG_MOSTCAREERSUICIDES}</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="180">{$LANG_CAUSE}</td>
    <td class="smheading" align="center">{$LANG_PLAYER}</td>
    <td class="smheading" align="center" width="60">{$LANG_SUICIDES}</td>
    <td class="smheading" align="center" width="60">{$LANG_MATCHES}</td>
    <td class="smheading" align="center" width="60">{$LANG_HOURS}</td>
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