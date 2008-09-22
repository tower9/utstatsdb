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

require("includes/main.inc.php");

function nostripspecialchars($string)
{
  $string = str_replace("&amp;", "&", $string);
  $string = str_replace("&quot;", "\"", $string);
  $string = str_replace("&#039;", "'", $string);
  $string = str_replace("&lt;", "<", $string);
  $string = str_replace("&gt;", ">", $string);
  return $string;
}

$matchnum = -1;
check_get($matchnum, "match");
if (!is_numeric($matchnum))
  $matchnum = -1;
if ($matchnum <= 0) {
  echo "{$LANG_RUNFROMTHEMAININDEX}<br />\n";
  exit;
}

$link = sql_connect();

// Load game data
$result = sql_queryn($link, "SELECT * FROM {$dbpre}matches WHERE gm_num=$matchnum LIMIT 1");
if (!$result) {
  echo "{$LANG_MATCHDATABASEERROR}<br />\n";
  exit;
}
$row = sql_fetch_assoc($result);
sql_free_result($result);
if (!$row) {
  echo "{$LANG_MATCHNOTFOUNDINDATABASE}<br />\n";
  exit;
}
while (list($key,$val) = each($row))
  ${$key} = $val;

$start = strtotime($gm_start);
$init = strtotime($gm_init);
$delay = $start - strtotime($gm_init);
$matchdate = formatdate($start, 1);
$matchinit = formatdate($init, 1);

// Set game type
$result = sql_queryn($link, "SELECT tp_desc,tp_type,tp_team FROM {$dbpre}type WHERE tp_num=$gm_type LIMIT 1");
if (!$result) {
  echo "{$LANG_GAMETYPEDATABASEERROR}<br />\n";
  exit;
}
if (!(list($gametype,$gametval,$teams) = sql_fetch_row($result))) {
  echo "{$LANG_ERRORLOCATINGGAMETYPE}<br />\n";
  exit;
}
sql_free_result($result);

// Load Server Data
$result = sql_queryn($link, "SELECT sv_name,sv_admin,sv_email FROM {$dbpre}servers WHERE sv_num=$gm_server LIMIT 1");
if (!$result) {
  echo "{$LANG_SERVERDATABASEERROR}<br />\n";
  exit;
}
if (!list($sv_name,$sv_admin,$sv_email) = sql_fetch_row($result)) {
  echo "{$LANG_SERVERNOTFOUND}<br />\n";
  exit;
}
sql_free_result($result);
$servername = stripspecialchars($sv_name);
$serveradmin = stripspecialchars($sv_admin);
$serveremail = stripspecialchars($sv_email);

// Load Map Data
$result = sql_queryn($link, "SELECT mp_name,mp_desc,mp_author FROM {$dbpre}maps WHERE mp_num=$gm_map LIMIT 1");
if (!$result) {
  echo "{$LANG_MAPDATABASEERRORP}<br />\n";
  exit;
}
if (!list($mp_name,$mp_desc,$mp_author) = sql_fetch_row($result)) {
  echo "{$LANG_MAPNOTFOUND}<br />\n";
  exit;
}
sql_free_result($result);
$mapname = stripspecialchars($mp_name);
$mapdesc = stripspecialchars($mp_desc);
$mapauthor = stripspecialchars($mp_author);

// Load Players
$maxplayer = 0;
if (strtolower($dbtype) == "sqlite")
  $result = sql_querynb($link, "SELECT * FROM {$dbpre}gplayers WHERE {$dbpre}gplayers.gp_match=$matchnum");
else
  $result = sql_queryn($link, "SELECT {$dbpre}gplayers.*,{$dbpre}players.plr_name FROM {$dbpre}gplayers,{$dbpre}players WHERE {$dbpre}gplayers.gp_match=$matchnum AND {$dbpre}players.pnum={$dbpre}gplayers.gp_pnum");
if (!$result) {
  echo "{$LANG_GAMEPLAYERLISTDATABASEERROR}<br />\n";
  exit;
}
while($row = sql_fetch_assoc($result)) {
  $num = $row["gp_num"];
  if ($num > $maxplayer)
    $maxplayer = $num;
  $row["gp_time0"] = floatval($row["gp_time0"] / 100);
  $row["gp_time1"] = floatval($row["gp_time1"] / 100);
  $row["gp_time2"] = floatval($row["gp_time2"] / 100);
  $row["gp_time3"] = floatval($row["gp_time3"] / 100);
  $gplayer[$num] = $row;
  if (strtolower($dbtype) == "sqlite") {
    $result2 = sql_queryn($link, "SELECT plr_name FROM {$dbpre}players WHERE pnum={$row['gp_pnum']} LIMIT 1");
    list($gplayer[$num]["gp_name"]) = sql_fetch_row($result2);
    sql_free_result($result2);
    $gplayer[$num]["gp_name"] = stripspecialchars($gplayer[$num]["gp_name"]);
  }
  else
    $gplayer[$num]["gp_name"] = stripspecialchars($row["plr_name"]);
}
sql_free_result($result);

// Set Player Ranks
$ranks = array();
for ($r = 1; $r <= $gm_numplayers; $r++) {
  for ($i = 0, $ranks[$r] = 0; $i <= $maxplayer && !$ranks[$r]; $i++) {
    if (isset($gplayer[$i]) && $gplayer[$i]["gp_rank"] == $r)
      $ranks[$r] = $i;
  }
}

if (isset($password) && $password)
  $pw = "{$LANG_ENABLED}";
else
  $pw = "{$LANG_DISABLED}";
if (isset($gamestats) && $gamestats)
  $stats = "{$LANG_ENABLED}";
else
  $stats = "{$LANG_DISABLED}";
if ($gm_translocator)
  $trans = "{$LANG_ENABLED}";
else
  $trans = "{$LANG_DISABLED}";
if ($gm_mapvoting)
  $mapvoting = "{$LANG_ENABLED}";
else
  $mapvoting = "{$LANG_DISABLED}";
if ($gm_kickvoting)
  $kickvoting = "{$LANG_ENABLED}";
else
  $kickvoting = "{$LANG_DISABLED}";
if ($gm_balanceteams)
  $balanceteams = "{$LANG_ENABLED}";
else
  $balanceteams = "{$LANG_DISABLED}";
if ($gm_playersbalanceteams)
  $playersbalance = "{$LANG_ENABLED}";
else
  $playersbalance = "{$LANG_DISABLED}";
$linksetup = stripspecialchars($gm_linksetup);
if (isset($gm_gamespeed))
  $gamespeed = (floatval($gm_gamespeed) * 100.0)."%";
else
  $gamespeed = "100%";

if ($gm_healthforkills)
  $healthforkills = "{$LANG_ENABLED}";
else
  $healthforkills = "{$LANG_DISABLED}";
if ($gm_allowsuperweapons)
  $allowsuperweapons = "{$LANG_ENABLED}";
else
  $allowsuperweapons = "{$LANG_DISABLED}";
if ($gm_camperalarm)
  $camperalarm = "{$LANG_ENABLED}";
else
  $camperalarm = "{$LANG_DISABLED}";
if ($gm_allowpickups)
  $allowpickups = "{$LANG_ENABLED}";
else
  $allowpickups = "{$LANG_DISABLED}";
if ($gm_allowadrenaline)
  $allowadrenaline = "{$LANG_ENABLED}";
else
  $allowadrenaline = "{$LANG_DISABLED}";
if ($gm_fullammo)
  $fullammo = "{$LANG_ENABLED}";
else
  $fullammo = "{$LANG_DISABLED}";

if ($gametval > 1)
  $tlabel = "{$LANG_SCORE}";
else
  $tlabel = "{$LANG_FRAGS}";

if ($gm_fraglimit && $gm_timelimit)
  $limits = "$gm_fraglimit $tlabel / $gm_timelimit {$LANG_MINUTES}";
else if ($gm_fraglimit)
  $limits = "$gm_fraglimit $tlabel";
else if ($gm_timelimit)
  $limits = "$gm_timelimit {$LANG_MINUTES}";
else
  $limits = "No Limit";

switch ($gm_difficulty) {
  case 0: $difficulty = "{$LANG_NOVICE}"; break;
  case 1: $difficulty = "{$LANG_AVERAGE}"; break;
  case 2: $difficulty = "{$LANG_EXPERIENCED}"; break;
  case 3: $difficulty = "{$LANG_SKILLED}"; break;
  case 4: $difficulty = "{$LANG_ADEPT}"; break;
  case 5: $difficulty = "{$LANG_MASTERFUL}"; break;
  case 6: $difficulty = "{$LANG_INHUMAN}"; break;
  case 7: $difficulty = "{$LANG_GODLIKE}"; break;
  default: $difficulty = "";
}

$total_frags = $gm_kills - $gm_suicides;

$demodl = "";
if ($demodir != "" && $demoext != "") {
  if (substr($demodir, -1) != "/")
    $demodir.="/";
  $demofile = date('md-Hi', $init)."-".$mp_name.".".$demoext;
  $demopath = $demodir.$demofile;
  if (file_exists($demopath))
    $demodl = $demopath;
}

echo <<<EOF
<center>
<table cellpadding="1" cellspacing="2" border="0" width="720">
  <tr>
    <td class="heading" align="center">{$LANG_MATCHSTATSFOR} $servername : $mapname</td>
  </tr>
</table>
<br />

EOF;

$total_tscore = $gm_tscore0 + $gm_tscore1 + $gm_tscore2 + $gm_tscore3;
$total_score = 0;
for ($i = 0; $i <= $maxplayer; $i++)
  if (isset($gplayer[$i]))
    $total_score += $gplayer[$i]["gp_tscore0"] + $gplayer[$i]["gp_tscore1"] + $gplayer[$i]["gp_tscore2"] + $gplayer[$i]["gp_tscore3"];

$display_map = false;
$mapimage = strtolower($mapname).".jpg";
if (file_exists("mapimages/$mapimage"))
  $display_map = true;
else {
  $mapimage = strtolower($mapname).".gif";
  if (file_exists("mapimages/$mapimage"))
    $display_map = true;
}

if ($gametval == 9 && $gm_logger != 1) { // Basic Invasion Stats
  if ($display_map)
    include("templates/matchstats-invtotalsmap.php");
  else
    include("templates/matchstats-invtotals.php");
}
else if ($gametval == 18) { // DeathBall
  if ($display_map)
    include("templates/matchstats-dbtotalsmap.php");
  else
    include("templates/matchstats-dbtotals.php");
}
else if ($teams && $gametval != 9) { // Team Games
  if ($display_map)
    include("templates/matchstats-teamtotalsmap.php");
  else
    include("templates/matchstats-teamtotals.php");
}
else {
  if ($display_map)
    include("templates/matchstats-totalsmap.php");
  else
    include("templates/matchstats-totals.php");
}

echo <<<EOF
<br />
<table cellpadding="1" cellspacing="2" border="0" width="650">
  <tr>
    <td class="heading" colspan="4" align="center">{$LANG_UNREALTOURNAMENTMATCHSTATS}</td>
  </tr>
  <tr>
    <td class="dark" align="center" width="100">{$LANG_MATCHDATE}</td>
    <td class="grey" align="center" width="220" title="{$LANG_MATCHINIT}: $matchinit">$matchdate</td>
    <td class="dark" align="center" width="105">{$LANG_SERVER}</td>
    <td class="grey" align="center"><a class="grey" href="serverstats.php?server=$gm_server">$servername</a></td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_GAMETYPE}</td>
    <td class="grey" align="center">$gametype</td>
    <td class="dark" align="center">{$LANG_ADMINNAME}</td>
    <td class="grey" align="center">$serveradmin</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MAPNAME}</td>
    <td class="grey" align="center"><a class="grey" href="mapstats.php?map=$gm_map">$mapname</a></td>
    <td class="dark" align="center">{$LANG_ADMINEMAIL}</td>
    <td class="grey" align="center">$serveremail</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_GLOBALSTATS}</td>
    <td class="grey" align="center">$stats</td>
    <td class="dark" align="center">{$LANG_GAMESPEED}</td>
    <td class="grey" align="center">$gamespeed</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MATCHLIMIT}</td>
    <td class="grey" align="center">$limits</td>
    <td class="dark" align="center">{$LANG_NUMPLAYERS}</td>
    <td class="grey" align="center">$gm_numplayers</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_DIFFICULTY}</td>
    <td class="grey" align="center">$difficulty</td>
    <td class="dark" align="center">{$LANG_TRANSLOCATOR}</td>
    <td class="grey" align="center">$trans</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MAPVOTING}</td>
    <td class="grey" align="center">$mapvoting</td>
    <td class="dark" align="center">{$LANG_KICKVOTING}</td>
    <td class="grey" align="center">$kickvoting</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_FULLAMMO}</td>
    <td class="grey" align="center">$fullammo</td>
    <td class="dark" align="center">{$LANG_HEALTHFORKILLS}</td>
    <td class="grey" align="center">$healthforkills</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_CAMPERALARM}</td>
    <td class="grey" align="center">$camperalarm</td>
    <td class="dark" align="center">{$LANG_SUPERWEAPONS}</td>
    <td class="grey" align="center">$allowsuperweapons</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_PICKUPS}</td>
    <td class="grey" align="center">$allowpickups</td>
    <td class="dark" align="center">{$LANG_ADRENALINE}</td>
    <td class="grey" align="center">$allowadrenaline</td>
  </tr>

EOF;

if ($teams) {
  echo <<<EOF
  <tr>
    <td class="dark" align="center">{$LANG_BALANCETEAMS}</td>
    <td class="grey" align="center">$balanceteams</td>
    <td class="dark" align="center">{$LANG_PLAYERSBALANCE}</td>
    <td class="grey" align="center">$playersbalance</td>
  </tr>

EOF;

  if ($gametval == 6) {
    $SpecialText = "{$LANG_LINKSETUP}";
    $special = $linksetup;
  }
  else if ($gametval == 9 && $gm_logger == 1) {
    $SpecialText = "{$LANG_WAVES}";
    $special = $gm_maxwave;
  }
  else if ($gametval == 18) {
  	$SpecialText = "{$LANG_OVERTIME}";
  	$special = "$gm_overtime {$LANG_MINUTES}";
  }
  else {
    $SpecialText = "&nbsp;";
    $special = "&nbsp;";
  }

  echo <<<EOF
  <tr>
    <td class="dark" align="center">{$LANG_FRIENDLYFIRE}</td>
    <td class="grey" align="center">$gm_friendlyfirescale</td>
    <td class="dark" align="center">$SpecialText</td>
    <td class="grey" align="center">$special</td>
  </tr>

EOF;
}

if ($demodl) {
  echo <<<EOF
  <tr>
    <td class="dark" align="center">{$LANG_DEMOREC}</td>
    <td class="grey" align="center" colspan="3"><a href="$demodl" class="grey">$demofile</a></td>
  </tr>

EOF;
}

echo <<<EOF
  <tr>
    <td class="dark" align="center">{$LANG_MUTATORS}</td>
    <td class="grey" align="center" colspan="3">$gm_mutators</td>
  </tr>
</table>

EOF;

//=============================================================================
//========== Flag Event Summary ===============================================
//=============================================================================
if ($gametval == 2) {
  echo <<<EOF
<br />
<table cellpadding="0" cellspacing="2" border="0" width="690">
  <tr>
    <td class="heading" colspan="11" align="center">{$LANG_FLAGEVENTSUMMARY}</td>
  </tr>

EOF;

  for ($tm = 0; $tm < $gm_numteams; $tm++) {
    echo <<<EOF
  <tr>
    <td class="hlheading" colspan="11" align="center">{$teamcolor[$tm]} {$LANG_TEAM}</td>
  </tr>
  <tr>
    <td class="smheading" align="center" rowspan="2" width="40">{$LANG_RANK}</td>
    <td class="smheading" align="center" rowspan="2">{$LANG_PLAYER}</td>
    <td class="smheading" align="center" colspan="2" width="90">{$LANG_SCORE}</td>
    <td class="smheading" align="center" rowspan="2" width="60">{$LANG_FLAGCAPTURES}</td>
    <td class="smheading" align="center" rowspan="2" width="60">{$LANG_FLAGASSISTS}</td>
    <td class="smheading" align="center" rowspan="2" width="50">{$LANG_FLAGKILLS}</td>
    <td class="smheading" align="center" rowspan="2" width="50">{$LANG_FLAGSAVES}</td>
    <td class="smheading" align="center" rowspan="2" width="60">{$LANG_FLAGPICKUPS}</td>
    <td class="smheading" align="center" rowspan="2" width="50">{$LANG_FLAGDROPS}</td>
    <td class="smheading" align="center" rowspan="2" width="50">{$LANG_CARRYTIME}</td>
  </tr>
  <tr>
    <td class="smheading" align="center">{$LANG_TEAM}</td>
    <td class="smheading" align="center">{$LANG_PLAYER}</td>
  </tr>

EOF;

    $tscore = $tcapture = $tflagkill = $tassist = $treturn = $tpickup = $tdrop = $theld = 0;
    for ($r = 1; $r <= $gm_numplayers; $r++) {
      $i = $ranks[$r];

      if ($gplayer[$i]["gp_team"] == $tm || $gplayer[$i]["gp_tscore{$tm}"]) {
        reset($gplayer[$i]);
        while (list ($key, $val) = each ($gplayer[$i]))
          ${$key} = $val;

        $score = ${"gp_tscore{$tm}"};
        $capcarry = ${"gp_capcarry{$tm}"};
        $typekill = ${"gp_typekill{$tm}"};
        $assist = ${"gp_assist{$tm}"};
        $return = ${"gp_return{$tm}"};
        $pickup = ${"gp_pickup{$tm}"};
        $drop = ${"gp_drop{$tm}"};
        $held = ${"gp_holdtime{$tm}"};

        $tscore += $score;
        $tcapture += $capcarry;
        $tflagkill += $typekill;
        $tassist += $assist;
        $treturn += $return;
        $tpickup += $pickup;
        $tdrop += $drop;
        $theld += $held;

        if ($gp_bot)
          $nameclass = "darkbot";
        else
          $nameclass = "darkhuman";
        $gpplayer = "<a class=\"$nameclass\" href=\"matchplayer.php?match=$matchnum&amp;player=$gp_num\">$gp_name</a>";
        $held = sprintf("%0.1f", $held / 6000.0);

        echo <<<EOF
  <tr>
    <td class="dark" align="center">$gp_rank</td>
    <td class="$nameclass" align="center">$gpplayer</td>
    <td class="grey" align="center">&nbsp;</td>
    <td class="grey" align="center">$score</td>
    <td class="grey" align="center">$capcarry</td>
    <td class="grey" align="center">$assist</td>
    <td class="grey" align="center">$typekill</td>
    <td class="grey" align="center">$return</td>
    <td class="grey" align="center">$pickup</td>
    <td class="grey" align="center">$drop</td>
    <td class="grey" align="center">$held</td>
  </tr>

EOF;
      }
    }

    $teamscore = ${"gm_tscore{$tm}"};
    $theld = sprintf("%0.1f", $theld / 6000.0);

    echo <<<EOF
  <tr>
    <td class="dark" align="center" colspan="2">{$LANG_TOTALS}</td>
    <td class="darkgrey" align="center">$teamscore</td>
    <td class="darkgrey" align="center">$tscore</td>
    <td class="darkgrey" align="center">$tcapture</td>
    <td class="darkgrey" align="center">$tassist</td>
    <td class="darkgrey" align="center">$tflagkill</td>
    <td class="darkgrey" align="center">$treturn</td>
    <td class="darkgrey" align="center">$tpickup</td>
    <td class="darkgrey" align="center">$tdrop</td>
    <td class="darkgrey" align="center">$theld</td>
  </tr>

EOF;
  }

  echo <<<EOF
</table>

EOF;
}

//=============================================================================
//========== Bombing Run Event Summary ========================================
//=============================================================================
if ($gametval == 3) {
  echo <<<EOF
<br />
<table cellpadding="0" cellspacing="2" border="0">
  <tr>
    <td class="heading" colspan="10" align="center">{$LANG_BOMBINGRUNEVENTSUMMARY}</td>
  </tr>

EOF;

  for ($tm = 0; $tm < $gm_numteams; $tm++) {
    echo <<<EOF
  <tr>
    <td class="hlheading" colspan="9" align="center">{$teamcolor[$tm]} {$LANG_TEAM}</td>
  </tr>
  <tr>
    <td class="smheading" align="center" rowspan="2" width="40">{$LANG_RANK}</td>
    <td class="smheading" align="center" rowspan="2">{$LANG_PLAYER}</td>
    <td class="smheading" align="center" colspan="2" width="90">{$LANG_SCORE}</td>
    <td class="smheading" align="center" rowspan="2" width="55">{$LANG_BOMBSTOSSED}</td>
    <td class="smheading" align="center" rowspan="2" width="55">{$LANG_BOMBSCARRIED}</td>
    <td class="smheading" align="center" rowspan="2" width="55">{$LANG_CRITICALKILLS}</td>
    <td class="smheading" align="center" rowspan="2" width="55">{$LANG_ASSISTS}</td>
    <td class="smheading" align="center" rowspan="2" width="50">{$LANG_CARRYTIME}</td>
  </tr>
  <tr>
    <td class="smheading" align="center">{$LANG_TEAM}</td>
    <td class="smheading" align="center">{$LANG_PLAYER}</td>
  </tr>

EOF;

    $tscore = $ttossed = $tcarried = $tballkill = $tassist = $theld = 0;
    for ($r = 1; $r <= $gm_numplayers; $r++) {
      $i = $ranks[$r];

      if ($gplayer[$i]["gp_team"] == $tm || $gplayer[$i]["gp_tscore{$tm}"]) {
        reset($gplayer[$i]);
        while (list ($key, $val) = each ($gplayer[$i]))
          ${$key} = $val;

        $score = ${"gp_tscore{$tm}"};
        $tossed = ${"gp_tossed{$tm}"};
        $carried = ${"gp_capcarry{$tm}"};
        $ballkill = ${"gp_typekill{$tm}"};
        $assist = ${"gp_assist{$tm}"};
        $held = ${"gp_holdtime{$tm}"};

        $tscore += $score;
        $ttossed += $tossed;
        $tcarried += $carried;
        $tballkill += $ballkill;
        $tassist += $assist;
        $theld += $held;

        if ($gp_bot)
          $nameclass = "darkbot";
        else
          $nameclass = "darkhuman";
        $gpplayer = "<a class=\"$nameclass\" href=\"matchplayer.php?match=$matchnum&amp;player=$gp_num\">$gp_name</a>";
        $held = sprintf("%0.1f", $held / 6000.0);

        echo <<<EOF
  <tr>
    <td class="dark" align="center">$gp_rank</td>
    <td class="$nameclass" align="center">$gpplayer</td>
    <td class="grey" align="center">&nbsp;</td>
    <td class="grey" align="center">$score</td>
    <td class="grey" align="center">$tossed</td>
    <td class="grey" align="center">$carried</td>
    <td class="grey" align="center">$ballkill</td>
    <td class="grey" align="center">$assist</td>
    <td class="grey" align="center">$held</td>
  </tr>

EOF;
      }
    }

    $teamscore = ${"gm_tscore{$tm}"};
    $theld = sprintf("%0.1f", $theld / 6000.0);

    echo <<<EOF
  <tr>
    <td class="dark" align="center" colspan="2">{$LANG_TOTALS}</td>
    <td class="darkgrey" align="center">$teamscore</td>
    <td class="darkgrey" align="center">$tscore</td>
    <td class="darkgrey" align="center">$ttossed</td>
    <td class="darkgrey" align="center">$tcarried</td>
    <td class="darkgrey" align="center">$tballkill</td>
    <td class="darkgrey" align="center">$tassist</td>
    <td class="darkgrey" align="center">$theld</td>
  </tr>

EOF;
  }

  echo <<<EOF
</table>

EOF;
}

//=============================================================================
//========== Double Domination Control Point Summary ==========================
//=============================================================================
if ($gametval == 7) {
  echo <<<EOF
<br />
<table cellpadding="0" cellspacing="2" border="0" width="430">
  <tr>
    <td class="heading" colspan="11" align="center">{$LANG_CONTROLPOINTSUMMARY}</td>
  </tr>

EOF;

  for ($tm = 1; $tm >= 0; $tm--) {
    if ($tm == 1)
      $tmcolor = "{$LANG_BLUE}";
    else
      $tmcolor = "{$LANG_RED}";

    echo <<<EOF
  <tr>
    <td class="hlheading" colspan="7" align="center">$tmcolor {$LANG_TEAM}</td>
  </tr>
  <tr>
    <td class="smheading" align="center" rowspan="2" width="40">{$LANG_RANK}</td>
    <td class="smheading" align="center" rowspan="2">{$LANG_PLAYER}</td>
    <td class="smheading" align="center" colspan="2" width="90">{$LANG_SCORE}</td>
    <td class="smheading" align="center" rowspan="2" width="65">{$LANG_POINTSHELD}</td>
    <td class="smheading" align="center" rowspan="2" width="65">{$LANG_POINTSCAPTURED}</td>
    <td class="smheading" align="center" rowspan="2" width="60">{$LANG_CRITICALKILLS}</td>
  </tr>
  <tr>
    <td class="smheading" align="center">{$LANG_TEAM}</td>
    <td class="smheading" align="center">{$LANG_PLAYER}</td>
  </tr>

EOF;

    $tscore = $theld = $tcapture = $ttypekill = 0;
    for ($r = 1; $r <= $gm_numplayers; $r++) {
      $i = $ranks[$r];

      if ($gplayer[$i]["gp_team"] == $tm || $gplayer[$i]["gp_tscore{$tm}"]) {
        reset($gplayer[$i]);
        while (list ($key, $val) = each ($gplayer[$i]))
          ${$key} = $val;

        $score = ${"gp_tscore{$tm}"};
        $held = ${"gp_capcarry{$tm}"};
        $capture = ${"gp_pickup{$tm}"};
        $typekill = ${"gp_typekill{$tm}"};

        $tscore += $score;
        $theld += $held;
        $tcapture += $capture;
        $ttypekill += $typekill;

        if ($gp_bot)
          $nameclass = "darkbot";
        else
          $nameclass = "darkhuman";
        $gpplayer = "<a class=\"$nameclass\" href=\"matchplayer.php?match=$matchnum&amp;player=$gp_num\">$gp_name</a>";

        echo <<<EOF
  <tr>
    <td class="dark" align="center">$gp_rank</td>
    <td class="$nameclass" align="center">$gpplayer</td>
    <td class="grey" align="center">&nbsp;</td>
    <td class="grey" align="center">$score</td>
    <td class="grey" align="center">$held</td>
    <td class="grey" align="center">$capture</td>
    <td class="grey" align="center">$typekill</td>
  </tr>

EOF;
      }
    }

    $teamscore = ${"gm_tscore{$tm}"};

    echo <<<EOF
  <tr>
    <td class="dark" align="center" colspan="2">{$LANG_TOTALS}</td>
    <td class="darkgrey" align="center">$teamscore</td>
    <td class="darkgrey" align="center">$tscore</td>
    <td class="darkgrey" align="center">$theld</td>
    <td class="darkgrey" align="center">$tcapture</td>
    <td class="darkgrey" align="center">$ttypekill</td>
  </tr>

EOF;
  }

  echo <<<EOF
</table>

EOF;
}

//=============================================================================
//========== Assault Objectives ===============================================
//=============================================================================
if ($gametval == 5) {
  echo <<<EOF
<br />
<table cellpadding="0" cellspacing="2" border="0">
  <tr>
    <td class="heading" colspan="4" align="center">{$LANG_ASSAULTOBJECTIVES}</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="20">{$LANG_NUM}</td>
    <td class="smheading" align="center" width="270">{$LANG_OBJECTIVE}</td>
    <td class="smheading" align="center" width="50">{$LANG_REDTIME}</td>
    <td class="smheading" align="center" width="50">{$LANG_BLUETIME}</td>
  </tr>

EOF;

  $objectives = array();
  $result = sql_querynb($link, "SELECT ge_num,ge_plr,ge_time,ge_length,ge_quant FROM {$dbpre}gevents WHERE ge_match=$matchnum AND ge_event=7 ORDER BY ge_num");
  if (!$result) {
    echo "{$LANG_ERRORLOADINGASSAULTOBJECTIVE}<br />\n";
    exit;
  }
  while ($row = sql_fetch_row($result)) {
    $team = $row[1];
    $time = $row[2] / 100;
    $length = $row[3];
    $objnum = $row[4];
    $lengtht = sprintf("%d:%02d", floor($length / 60), intval(fmod($length, 60)));

    if ($team == 1) {
      if (isset($objectives[$objnum])) {
        $objectives[$objnum][0] = $time;
        $objectives[$objnum][2] = $lengtht;
      }
      else {
        $result2 = sql_queryn($link, "SELECT obj_priority,obj_secondary,obj_desc FROM {$dbpre}objectives WHERE obj_num=$objnum LIMIT 1");
        if (!(list($priority,$secondary,$desc) = sql_fetch_row($result2))) {
          sql_free_result($result2);
          continue;
        }
        sql_free_result($result2);
        $objectives[$objnum] = array($time, 0, $lengtht, "--", $priority, $secondary, $desc);
      }
    }
    else {
      if (isset($objectives[$objnum])) {
        $objectives[$objnum][1] = $time;
        $objectives[$objnum][3] = $lengtht;
      }
      else {
        $result2 = sql_queryn($link, "SELECT obj_priority,obj_secondary,obj_desc FROM {$dbpre}objectives WHERE obj_num=$objnum LIMIT 1");
        if (!(list($priority,$secondary,$desc) = sql_fetch_row($result2))) {
          sql_free_result($result2);
          continue;
        }
        sql_free_result($result2);
        $objectives[$objnum] = array(0, $time, "--", $lengtht, $priority, $secondary, $desc);
      }
    }
  }

  reset($objectives);
  foreach ($objectives as $obj) {
    echo <<<EOF
  <tr>
    <td class="grey" align="center">$obj[4]</td>
    <td class="grey" align="center">$obj[6]</td>
    <td class="grey" align="center">$obj[2]</td>
    <td class="grey" align="center">$obj[3]</td>
  </tr>

EOF;
  }
  sql_free_result($result);

  $ttime = array(1 => -1, -1);
  $result = sql_queryn($link, "SELECT ge_plr,ge_length,ge_reason FROM {$dbpre}gevents WHERE ge_match=$matchnum AND ge_event=8 ORDER BY ge_num");
  while ($row = sql_fetch_row($result)) {
    $team = $row[0];
    if ($team == 1)
      $teamname = "{$LANG_RED}";
    else
      $teamname = "{$LANG_BLUE}";
    $length = $row[1];
    $lengtht = sprintf("%d:%02d", floor($length / 60), intval(fmod($length, 60)));
    $reason = $row[2];
    switch ($reason) {
      case 0:
        $info = $teamname." {$LANG_TEAMSUCCESSFULLYDEFENDED}";
        $ttime[3-$team] = -1;
        $evclass = "darkgrey";
        break;
      case 1:
        $info = $teamname." {$LANG_TEAMSUCCESSFULLYATTACKEDIN} $lengtht";
        $ttime[$team] = $length;
        $evclass = "darkgrey";
        break;
      case 2:
        if ($team == 0) {
          $info = "{$LANG_REDTEAMWINS}";
          $evclass = "chatred";
        }
        else if ($team == 1) {
          $info = "{$LANG_BLUETEAMWINS}";
          $evclass = "chatblue";
        }
        else {
          $info = "{$LANG_MATCHISADRAW}";
          $evclass = "chat";
        }
        break;
    }
    echo <<<EOF
  <tr>
    <td class="$evclass" align="center" colspan="4">$info</td>
  </tr>
EOF;
  }

  echo "</table>\n";
}

//=============================================================================
//========== Onslaught Summary ================================================
//=============================================================================
if ($gametval == 6) {
  echo <<<EOF
<br />
<table cellpadding="0" cellspacing="2" border="0" width="670">
  <tr>
    <td class="heading" colspan="7" align="center">{$LANG_ONSLAUGHTSUMMARY}</td>
  </tr>

EOF;

  for ($tm = 1; $tm >= 0; $tm--) {
    $wins = "";
    if ($tm == 1) {
      $tmcolor = "{$LANG_BLUE}";
      if ($gm_tscore1 > $gm_tscore0)
        $wins = " Wins!";
    }
    else {
      $tmcolor = "{$LANG_RED}";
      if ($gm_tscore0 > $gm_tscore1)
        $wins = " Wins!";
    }

    echo <<<EOF
  <tr>
    <td class="hlheading" colspan="7" align="center">$tmcolor {$LANG_TEAM}{$wins}</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="40">{$LANG_RANK}</td>
    <td class="smheading" align="center">{$LANG_PLAYER}</td>
    <td class="smheading" align="center" width="45">{$LANG_SCORE}</td>
    <td class="smheading" align="center" width="90">{$LANG_POWERNODESCONSTRUCTED}</td>
    <td class="smheading" align="center" width="90">{$LANG_POWERNODESDESTROYED}</td>
    <td class="smheading" align="center" width="120">{$LANG_CONSTRUCTINGNODESDESTROYED}</td>
    <td class="smheading" align="center" width="90">{$LANG_POWERCORESDESTROYED}</td>
  </tr>

EOF;

    $tscore = $tconstructed = $tdestroyed = $tconstdest = $tcoredest = 0;
    for ($r = 1; $r <= $gm_numplayers; $r++) {
      $i = $ranks[$r];

      if ($gplayer[$i]["gp_team"] == $tm || $gplayer[$i]["gp_tscore{$tm}"]) {
        reset($gplayer[$i]);
        while (list ($key, $val) = each ($gplayer[$i]))
          ${$key} = $val;

        $score = ${"gp_tscore{$tm}"};
        $nodeconstructed = ${"gp_pickup{$tm}"};
        $nodedestroyed = ${"gp_taken{$tm}"};
        $nodeconstdestroyed = ${"gp_drop{$tm}"};
        $coredestroyed = ${"gp_capcarry{$tm}"};

        if ($gp_bot)
          $nameclass = "darkbot";
        else
          $nameclass = "darkhuman";
        $gpplayer = "<a class=\"$nameclass\" href=\"matchplayer.php?match=$matchnum&amp;player=$gp_num\">$gp_name</a>";

        echo <<<EOF
  <tr>
    <td class="dark" align="center">$gp_rank</td>
    <td class="$nameclass" align="center">$gpplayer</td>
    <td class="grey" align="center">$score</td>
    <td class="grey" align="center">$nodeconstructed</td>
    <td class="grey" align="center">$nodedestroyed</td>
    <td class="grey" align="center">$nodeconstdestroyed</td>
    <td class="grey" align="center">$coredestroyed</td>
  </tr>

EOF;
      }
    }
  }
  echo "</table>\n";
}

//=============================================================================
//========== Mutant Summary ===================================================
//=============================================================================
if ($gametval == 8) {
  echo <<<EOF
<br />
<table cellpadding="0" cellspacing="2" border="0" width="300">
  <tr>
    <td class="heading" colspan="6" align="center">{$LANG_MUTANTSUMMARY}</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="40">{$LANG_RANK}</td>
    <td class="smheading" align="center">{$LANG_PLAYER}</td>
    <td class="smheading" align="center" width="55">{$LANG_MUTANT}</td>
    <td class="smheading" align="center" width="55">{$LANG_MUTANTTIME}</td>
    <td class="smheading" align="center" width="55">{$LANG_BOTTOMFEEDER}</td>
  </tr>

EOF;

  for ($r = 1; $r <= $gm_numplayers; $r++) {
    $i = $ranks[$r];
    $gp_num = $gplayer[$i]["gp_num"];
    $gp_name = $gplayer[$i]["gp_name"];
    $gp_bot = $gplayer[$i]["gp_bot"];
    $gp_rank = $gplayer[$i]["gp_rank"];
    $mutant = $gplayer[$i]["gp_pickup0"];
    $muttime = sprintf("%0.1f", $gplayer[$i]["gp_holdtime0"] / 6000.0);
    $bottom = $gplayer[$i]["gp_pickup1"];

    if ($gp_bot)
      $nameclass = "darkbot";
    else
      $nameclass = "darkhuman";
    $gpplayer = "<a class=\"$nameclass\" href=\"matchplayer.php?match=$matchnum&amp;player=$gp_num\">$gp_name</a>";

    echo <<<EOF
  <tr>
    <td class="dark" align="center">$gp_rank</td>
    <td class="$nameclass" align="center">$gpplayer</td>
    <td class="grey" align="center">$mutant</td>
    <td class="grey" align="center">$muttime</td>
    <td class="grey" align="center">$bottom</td>
  </tr>

EOF;
  }

  echo "</table>\n";
}

//=============================================================================
//========== Last Man Standing Summary ========================================
//=============================================================================
if ($gametval == 10 || $gametval == 19) {
  $displaylms = false;
  for ($r = 1; $r <= $gm_numplayers; $r++) {
    if ($gplayer[$ranks[$r]]["gp_pickup0"] > 0)
      $displaylms = true;
  }    

  if ($displaylms) {
    echo <<<EOF
<br />
<table cellpadding="0" cellspacing="2" border="0" width="335">
  <tr>
    <td class="heading" colspan="5" align="center">{$LANG_LASTMANSTANDINGSUMMARY}</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="40">{$LANG_RANK}</td>
    <td class="smheading" align="center">{$LANG_PLAYER}</td>
    <td class="smheading" align="center" width="60">{$LANG_STARTINGLIVES}</td>
    <td class="smheading" align="center" width="75">{$LANG_LIVESREMAINING}</td>
  </tr>

EOF;

    for ($r = 1; $r <= $gm_numplayers; $r++) {
      $i = $ranks[$r];
      $gp_num = $gplayer[$i]["gp_num"];
      $gp_name = $gplayer[$i]["gp_name"];
      $gp_bot = $gplayer[$i]["gp_bot"];
      $gp_rank = $gplayer[$i]["gp_rank"];
      $lives_start = $gplayer[$i]["gp_pickup0"];
      $lives_remain = $gplayer[$i]["gp_pickup1"];

      if ($gp_bot)
        $nameclass = "darkbot";
      else
        $nameclass = "darkhuman";
      $gpplayer = "<a class=\"$nameclass\" href=\"matchplayer.php?match=$matchnum&amp;player=$gp_num\">$gp_name</a>";

      echo <<<EOF
  <tr>
    <td class="dark" align="center">$gp_rank</td>
    <td class="$nameclass" align="center">$gpplayer</td>
    <td class="grey" align="center">$lives_start</td>
    <td class="grey" align="center">$lives_remain</td>
  </tr>

EOF;
    }

    echo "</table>\n";
  }
}

//=============================================================================
//========== DeathBall Event Summary ==========================================
//=============================================================================
if ($gametval == 18) {
  echo <<<EOF
<br />
<table cellpadding="0" cellspacing="2" border="0">
  <tr>
    <td class="heading" colspan="10" align="center">{$LANG_DEATHBALLEVENTSUMMARY}</td>
  </tr>

EOF;

  for ($tm = 0; $tm < $gm_numteams; $tm++) {
    echo <<<EOF
  <tr>
    <td class="hlheading" colspan="10" align="center">{$teamcolor[$tm]} {$LANG_TEAM}</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="40">{$LANG_RANK}</td>
    <td class="smheading" align="center">{$LANG_PLAYER}</td>
    <td class="smheading" align="center" width="55">{$LANG_GOALS}</td>
    <td class="smheading" align="center" width="55">{$LANG_PASSES}</td>
    <td class="smheading" align="center" width="55">{$LANG_ASSISTS}</td>
    <td class="smheading" align="center" width="55">{$LANG_SAVES}</td>
    <td class="smheading" align="center" width="55">{$LANG_TACKLES}</td>
    <td class="smheading" align="center" width="55">{$LANG_INTERCEPTS}</td>
    <td class="smheading" align="center" width="55">{$LANG_MISSES}</td>
    <td class="smheading" align="center" width="70">{$LANG_BALLTIME}</td>
  </tr>

EOF;

    $tgoals = $tpasses = $tassists = $tsaves = $ttackles = $tintercepts = $tmissed = $tballtime = 0;

    for ($r = 1; $r <= $gm_numplayers; $r++) {
      $i = $ranks[$r];

      if ($gplayer[$i]["gp_team"] == $tm || $gplayer[$i]["gp_capcarry{$tm}"]) {
        reset($gplayer[$i]);
        while (list ($key, $val) = each ($gplayer[$i]))
          ${$key} = $val;

        $goals = ${"gp_capcarry{$tm}"};
        $passes = ${"gp_tossed{$tm}"};
        $assists = ${"gp_assist{$tm}"};
        $saves = ${"gp_return{$tm}"};
        $tackles = ${"gp_typekill{$tm}"};
        $intercepts = ${"gp_taken{$tm}"};
        $missed = ${"gp_pickup{$tm}"};
        $balltime = ${"gp_holdtime{$tm}"};

        $tgoals += $goals;
        $tpasses += $passes;
        $tassists += $assists;
        $tsaves += $saves;
        $ttackles += $tackles;
        $tintercepts += $intercepts;
        $tmissed += $missed;
        $tballtime += $balltime;

        if ($gp_bot)
          $nameclass = "darkbot";
        else
          $nameclass = "darkhuman";
        $gpplayer = "<a class=\"$nameclass\" href=\"matchplayer.php?match=$matchnum&amp;player=$gp_num\">$gp_name</a>";
        $balltime = sprintf("%0.1f", $balltime / 6000.0);

        echo <<<EOF
  <tr>
    <td class="dark" align="center">$gp_rank</td>
    <td class="$nameclass" align="center">$gpplayer</td>
    <td class="grey" align="center">$goals</td>
    <td class="grey" align="center">$passes</td>
    <td class="grey" align="center">$assists</td>
    <td class="grey" align="center">$saves</td>
    <td class="grey" align="center">$tackles</td>
    <td class="grey" align="center">$intercepts</td>
    <td class="grey" align="center">$missed</td>
    <td class="grey" align="center">$balltime</td>
  </tr>

EOF;
      }
    }

    $tballtime = sprintf("%0.1f", $tballtime / 6000.0);

    echo <<<EOF
  <tr>
    <td class="dark" align="center" colspan="2">{$LANG_TOTALS}</td>
    <td class="darkgrey" align="center">$tgoals</td>
    <td class="darkgrey" align="center">$tpasses</td>
    <td class="darkgrey" align="center">$tassists</td>
    <td class="darkgrey" align="center">$tsaves</td>
    <td class="darkgrey" align="center">$ttackles</td>
    <td class="darkgrey" align="center">$tintercepts</td>
    <td class="darkgrey" align="center">$tmissed</td>
    <td class="darkgrey" align="center">$tballtime</td>
  </tr>

EOF;
  }

  echo <<<EOF
</table>

EOF;
}

//=============================================================================
//========== JailBreak Event Summary ==========================================
//=============================================================================
if ($gametval == 20) {
  echo <<<EOF
<br />
<table cellpadding="0" cellspacing="2" border="0">
  <tr>
    <td class="heading" colspan="10" align="center">{$LANG_JAILBREAKEVENTSUMMARY}</td>
  </tr>

EOF;

  for ($tm = 0; $tm < $gm_numteams; $tm++) {
    echo <<<EOF
  <tr>
    <td class="hlheading" colspan="6" align="center">{$teamcolor[$tm]} {$LANG_TEAM}</td>
  </tr>
  <tr>
    <td class="smheading" align="center" rowspan="2" width="40">{$LANG_RANK}</td>
    <td class="smheading" align="center" rowspan="2">{$LANG_PLAYER}</td>
    <td class="smheading" align="center" colspan="2" width="90">{$LANG_SCORE}</td>
    <td class="smheading" align="center" rowspan="2" width="55">{$LANG_TEAMCAPTURED}</td>
    <td class="smheading" align="center" rowspan="2" width="55">{$LANG_TEAMRELEASED}</td>
  </tr>
  <tr>
    <td class="smheading" align="center">{$LANG_TEAM}</td>
    <td class="smheading" align="center">{$LANG_PLAYER}</td>
  </tr>

EOF;

    $tscore = $tcapture = $treturn = 0;
    for ($r = 1; $r <= $gm_numplayers; $r++) {
      $i = $ranks[$r];

      if ($gplayer[$i]["gp_team"] == $tm || $gplayer[$i]["gp_tscore{$tm}"]) {
        reset($gplayer[$i]);
        while (list ($key, $val) = each ($gplayer[$i]))
          ${$key} = $val;

        $score = ${"gp_tscore{$tm}"};
        $capcarry = ${"gp_capcarry{$tm}"};
        $return = ${"gp_return{$tm}"};

        $tscore += $score;
        $tcapture += $capcarry;
        $treturn += $return;

        if ($gp_bot)
          $nameclass = "darkbot";
        else
          $nameclass = "darkhuman";
        $gpplayer = "<a class=\"$nameclass\" href=\"matchplayer.php?match=$matchnum&amp;player=$gp_num\">$gp_name</a>";

        echo <<<EOF
  <tr>
    <td class="dark" align="center">$gp_rank</td>
    <td class="$nameclass" align="center">$gpplayer</td>
    <td class="grey" align="center">&nbsp;</td>
    <td class="grey" align="center">$score</td>
    <td class="grey" align="center">$capcarry</td>
    <td class="grey" align="center">$return</td>
  </tr>

EOF;
      }
    }

    $teamscore = ${"gm_tscore{$tm}"};

    echo <<<EOF
  <tr>
    <td class="dark" align="center" colspan="2">{$LANG_TOTALS}</td>
    <td class="darkgrey" align="center">$teamscore</td>
    <td class="darkgrey" align="center">$tscore</td>
    <td class="darkgrey" align="center">$tcapture</td>
    <td class="darkgrey" align="center">$treturn</td>
  </tr>

EOF;
  }

  echo <<<EOF
</table>

EOF;
}

//=============================================================================
//========== Team Scoring Graph (if imaging available) ========================
//=============================================================================
if ($teams && $gametval != 9 && $gametval != 5 && function_exists('ImageTypes')) {
  if (ImageTypes() & (IMG_JPG | IMG_GIF | IMG_PNG)) {
    echo <<<EOF
<br />
<table>
  <tr>
    <td class="medheading" colspan="10" align="center">Team Scores</td>
  </tr>
  <tr>
    <td><img src="graphs.php?type=2&amp;match=$matchnum" width="550" height="180" alt="{$LANG_TEAMSCORINGGRAPH}" /></td>
  </tr>
</table>

EOF;
  }
}

//=============================================================================
//========== Team Summary =====================================================
//=============================================================================
if ($teams && $gametval != 9 && $gametval != 18) {
  echo <<<EOF
<br />
<table cellpadding="0" cellspacing="2" border="0" width="720">
  <tr>
    <td class="heading" colspan="21" align="center">{$LANG_TEAMSUMMARY}</td>
  </tr>

EOF;

  $opend = 0;
  for ($tm = 0; $tm < $gm_numteams; $tm++) {
    echo <<<EOF
  <tr>
    <td class="hlheading" colspan="21" align="center">{$teamcolor[$tm]} {$LANG_TEAM}</td>
  </tr>
  <tr>
    <td class="smheading" align="center" rowspan="2" width="40">{$LANG_RANK}</td>
    <td class="smheading" align="center" rowspan="2">{$LANG_PLAYER}</td>
    <td class="smheading" align="center" rowspan="2" width="20"><img src="resource/pcolors.gif" width="16" height="16" border="0" alt="{$LANG_COLORBAR}" /></td>
    <td class="smheading" align="center" colspan="2" width="90">{$LANG_SCORE}</td>
    <td class="smheading" align="center" rowspan="2" width="20">{$LANG_F}</td>
    <td class="smheading" align="center" rowspan="2" width="20">{$LANG_K}</td>
    <td class="smheading" align="center" rowspan="2" width="20">{$LANG_D}</td>
    <td class="smheading" align="center" rowspan="2" width="20">{$LANG_S}</td>
    <td class="smheading" align="center" rowspan="2" width="20">{$LANG_TK}</td>
    <td class="smheading" align="center" rowspan="2" width="20">{$LANG_TD}</td>
    <td class="smheading" align="center" rowspan="2" width="55">{$LANG_EFF}</td>
    <td class="smheading" align="center" rowspan="2" width="50">{$LANG_AVGSPH}</td>
    <td class="smheading" align="center" rowspan="2" width="50">{$LANG_AVGTTL}</td>
    <td class="smheading" align="center" rowspan="2" width="40">{$LANG_TIME}</td>
    <td class="smheading" align="center" colspan="6" width="100">{$LANG_SPREES}</td>
  </tr>
  <tr>
    <td class="smheading" align="center">{$LANG_TEAM}</td>
    <td class="smheading" align="center">{$LANG_PLAYER}</td>
    <td class="smheading" align="center">{$LANG_KSPREE}</td>
    <td class="smheading" align="center">{$LANG_RSPREE}</td>
    <td class="smheading" align="center">{$LANG_DSPREE}</td>
    <td class="smheading" align="center">{$LANG_USPREE}</td>
    <td class="smheading" align="center">{$LANG_GSPREE}</td>
    <td class="smheading" align="center">{$LANG_WSPREE}</td>
  </tr>

EOF;

    $tscore = $tkills = $tdeaths = $tsuicides = $ttkills = $ttdeaths = $ttime = 0;
    $tspree1 = $tspree2 = $tspree3 = $tspree4 = $tspree5 = $tspree6 = $teamsize = 0;
    for ($r = 1; $r <= $gm_numplayers; $r++) {
      $i = $ranks[$r];

      if ($gplayer[$i]["gp_team"] == $tm || $gplayer[$i]["gp_tscore{$tm}"]) {
        reset($gplayer[$i]);
        while (list ($key, $val) = each ($gplayer[$i]))
          ${$key} = $val;

        $teamsize++;
        $score = ${"gp_tscore{$tm}"};
        $kills = ${"gp_kills{$tm}"};
        $deaths = ${"gp_deaths{$tm}"};
        $suicides = ${"gp_suicides{$tm}"};
        $frags = $kills - $suicides;
        $teamkills = ${"gp_teamkills{$tm}"};
        $teamdeaths = ${"gp_teamdeaths{$tm}"};
        $ptime = ${"gp_time{$tm}"};

        $tscore += $score;
        $tkills += $kills;
        $tdeaths += $deaths;
        $tsuicides += $suicides;
        $ttkills += $teamkills;
        $ttdeaths += $teamdeaths;
        $ttime += $ptime;
        $tspree1 += $gp_spree1;
        $tspree2 += $gp_spree2;
        $tspree3 += $gp_spree3;
        $tspree4 += $gp_spree4;
        $tspree5 += $gp_spree5;
        $tspree6 += $gp_spree6;

        if ($kills + $deaths + $suicides == 0)
          $eff = "0.0";
        else
          $eff = sprintf("%0.1f", ($kills / ($kills + $deaths + $suicides)) * 100.0);

        if ($ptime == 0)
          $sph = "0.0";
        else
          $sph = sprintf("%0.1f", $score * (3600 / $ptime));
    
        $ttl = sprintf("%0.1f", $ptime / ($deaths + $suicides + 1));
        $time = sprintf("%0.1f", $ptime / 60.0);

        if ($gp_bot)
          $nameclass = "darkbot";
        else
          $nameclass = "darkhuman";
        if ($gp_team != $tm) {
          $gpplayer = "<a class=\"$nameclass\" href=\"matchplayer.php?match=$matchnum&amp;player=$gp_num\">*$gp_name</a>";
          $opend = 1;
        } else
          $gpplayer = "<a class=\"$nameclass\" href=\"matchplayer.php?match=$matchnum&amp;player=$gp_num\">$gp_name</a>";

        echo <<<EOF
  <tr>
    <td class="dark" align="center">$gp_rank</td>
    <td class="$nameclass" align="center">$gpplayer</td>
    <td class="grey" align="center">&nbsp;</td>
    <td class="grey" align="center">&nbsp;</td>
    <td class="grey" align="center">$score</td>
    <td class="grey" align="center">$frags</td>
    <td class="grey" align="center">$kills</td>
    <td class="grey" align="center">$deaths</td>
    <td class="grey" align="center">$suicides</td>
    <td class="grey" align="center">$teamkills</td>
    <td class="grey" align="center">$teamdeaths</td>
    <td class="grey" align="center">$eff%</td>
    <td class="grey" align="center">$sph</td>
    <td class="grey" align="center">$ttl</td>
    <td class="grey" align="center">$time</td>
    <td class="grey" align="center">$gp_spree1</td>
    <td class="grey" align="center">$gp_spree2</td>
    <td class="grey" align="center">$gp_spree3</td>
    <td class="grey" align="center">$gp_spree4</td>
    <td class="grey" align="center">$gp_spree5</td>
    <td class="grey" align="center">$gp_spree6</td>
  </tr>

EOF;
      }
    }
    $cimage = "resource/p{$teamcolorbar[$tm]}color.gif";
    $teamscore = ${"gm_tscore{$tm}"};
    $frags = $tkills - $tsuicides;

    if ($tkills + $tdeaths + $tsuicides == 0)
      $eff = "0.0";
    else
      $eff = sprintf("%0.1f", ($tkills / ($tkills + $tdeaths + $tsuicides)) * 100.0);

    if ($ttime == 0)
      $sph = "0.0";
    else
      $sph = sprintf("%0.1f", $tscore * (3600 / $ttime));

    $ttl = sprintf("%0.1f", $ttime / ($tdeaths + $tsuicides + 1));

    if ($teamsize > 0)
      $time = sprintf("%0.1f", $ttime / 60.0 / $teamsize);
    else
      $time = sprintf("%0.1f", $ttime / 60.0);

    echo <<<EOF
  <tr>
    <td class="dark" align="center" colspan="2">{$LANG_TOTALS}</td>
    <td class="darkgrey" align="center"><img src="$cimage" width="16" height="16" border="0" alt="{$LANG_PLAYERCOLOR}" /></td>
    <td class="darkgrey" align="center">$teamscore</td>
    <td class="darkgrey" align="center">$tscore</td>
    <td class="darkgrey" align="center">$frags</td>
    <td class="darkgrey" align="center">$tkills</td>
    <td class="darkgrey" align="center">$tdeaths</td>
    <td class="darkgrey" align="center">$tsuicides</td>
    <td class="darkgrey" align="center">$ttkills</td>
    <td class="darkgrey" align="center">$ttdeaths</td>
    <td class="darkgrey" align="center">$eff%</td>
    <td class="darkgrey" align="center">$sph</td>
    <td class="darkgrey" align="center">$ttl</td>
    <td class="darkgrey" align="center">$time</td>
    <td class="darkgrey" align="center">$tspree1</td>
    <td class="darkgrey" align="center">$tspree2</td>
    <td class="darkgrey" align="center">$tspree3</td>
    <td class="darkgrey" align="center">$tspree4</td>
    <td class="darkgrey" align="center">$tspree5</td>
    <td class="darkgrey" align="center">$tspree6</td>
  </tr>

EOF;
  }

  if ($opend) {
    echo <<<EOF
  <tr>
    <td class="opnote" colspan="20">{$LANG_ENDEDGAMEONOPPOSINGTEAM}</td>
  </tr>

EOF;
  }

  echo <<<EOF
</table>

EOF;
}

//=============================================================================
//========== Score Graph (if imaging available) ===============================
//=============================================================================
if (function_exists('ImageTypes') && $gametval > 1 && $gametval != 10 && $gametval != 18) {
  if (ImageTypes() & (IMG_JPG | IMG_GIF | IMG_PNG)) {
    echo <<<EOF
<br />
<table>
  <tr>
    <td class="medheading" colspan="10" align="center">{$LANG_INDIVIDUALSCORES}</td>
  </tr>
  <tr>
    <td><img src="graphs.php?type=3&amp;match=$matchnum" width="550" height="180" alt="{$LANG_SCOREGRAPH}" /></td>
  </tr>
</table>

EOF;
  }
}

//=============================================================================
//========== Frag Graph (if imaging available) ================================
//=============================================================================

if (function_exists('ImageTypes') && ($gametval != 9 || $gm_logger == 1) && $gametval != 18) {
  if (ImageTypes() & (IMG_JPG | IMG_GIF | IMG_PNG)) {
    echo <<<EOF
<br />
<table>
  <tr>
    <td class="medheading" colspan="10" align="center">{$LANG_INDIVIDUALFRAGS}</td>
  </tr>
  <tr>
    <td><img src="graphs.php?type=1&amp;match=$matchnum" width="550" height="180" alt="{$LANG_FRAGGRAPH}" /></td>
  </tr>
</table>

EOF;
  }
}

//=============================================================================
//========== Last Man Standing Graph (if imaging available) ===================
//=============================================================================

if (function_exists('ImageTypes') && ($gametval == 10  || $gametval == 19)) {
  if (ImageTypes() & (IMG_JPG | IMG_GIF | IMG_PNG)) {
    echo <<<EOF
<br />
<table>
  <tr>
    <td class="medheading" colspan="10" align="center">{$LANG_LMSLIVESREMAINING}</td>
  </tr>
  <tr>
    <td><img src="graphs.php?type=4&amp;match=$matchnum" width="550" height="180" alt="{$LANG_LMSGRAPH}" /></td>
  </tr>
</table>

EOF;
  }
}

//=============================================================================
//========== Player Summary ===================================================
//=============================================================================
if ($gm_type != 9 || $gm_logger == 1) {
  if ($teams)
    $fphsph = "{$LANG_AVGSPH}";
  else
    $fphsph = "{$LANG_AVGFPH}";

  echo <<<EOF
<br />
<table cellpadding="0" cellspacing="2" border="0" width="720">
  <tr>
    <td class="heading" colspan="19" align="center">{$LANG_PLAYERSUMMARY}</td>
  </tr>
  <tr>
    <td class="smheading" align="center" rowspan="2">{$LANG_RANK}</td>
    <td class="smheading" align="center" rowspan="2">{$LANG_PLAYER}</td>
    <td class="smheading" align="center" rowspan="2" width="20"><img src="resource/pcolors.gif" width="16" height="16" border="0" alt="{$LANG_COLORBAR}" /></td>
    <td class="smheading" align="center" rowspan="2">{$LANG_SCORE}</td>
    <td class="smheading" align="center" rowspan="2">{$LANG_FRAGS}</td>
    <td class="smheading" align="center" rowspan="2">{$LANG_KILLS}</td>
    <td class="smheading" align="center" rowspan="2">{$LANG_DEATHS}</td>
    <td class="smheading" align="center" rowspan="2">{$LANG_SUICIDES}</td>
    <td class="smheading" align="center" rowspan="2">{$LANG_EFF}</td>
    <td width="50" class="smheading" align="center" rowspan="2">$fphsph</td>
    <td width="50" class="smheading" align="center" rowspan="2">{$LANG_AVGTTL}</td>
    <td class="smheading" align="center" rowspan="2">{$LANG_TIME}</td>
    <td class="smheading" align="center" colspan="6">{$LANG_SPREES}</td>
  </tr>
  <tr>
    <td class="smheading" align="center">K</td>
    <td class="smheading" align="center">R</td>
    <td class="smheading" align="center">D</td>
    <td class="smheading" align="center">U</td>
    <td class="smheading" align="center">G</td>
    <td class="smheading" align="center">W</td>
  </tr>

EOF;

  $total_score = $total_frags = $total_kills = $total_deaths = $total_suicides = $total_time = 0;
  $total_eff = $total_fph = $total_ttl = 0;
  $total_spree1 = $total_spree2 = $total_spree3 = $total_spree4 = $total_spree5 = $total_spree6 = 0;
  $lowscore = $highscore = 0;

  for ($r = 1; $r <= $gm_numplayers; $r++) {
    $i = $ranks[$r];

    reset($gplayer[$i]);
    while (list ($key, $val) = each ($gplayer[$i]))
      ${$key} = $val;

    $score = $gp_tscore0 + $gp_tscore1 + $gp_tscore2 + $gp_tscore3;
    $kills = $gp_kills0 + $gp_kills1 + $gp_kills2 + $gp_kills3;
    $deaths = $gp_deaths0 + $gp_deaths1 + $gp_deaths2 + $gp_deaths3;
    $suicides = $gp_suicides0 + $gp_suicides1 + $gp_suicides2 + $gp_suicides3;
    $ptime = $gp_time0 + $gp_time1 + $gp_time2 + $gp_time3;

    $frags = $kills - $suicides;

    if ($kills + $deaths + $suicides == 0)
      $eff = "0.0";
    else
      $eff = sprintf("%0.1f", ($kills / ($kills + $deaths + $suicides)) * 100.0);

    if ($ptime == 0)
      $fph = "0.0";
    else {
      if ($teams)
        $fph = sprintf("%0.1f", $score * (3600 / $ptime));
      else
        $fph = sprintf("%0.1f", $frags * (3600 / $ptime));
    }

    $ttl = sprintf("%0.1f", $ptime / ($deaths + $suicides + 1));
    $time = sprintf("%0.1f", $ptime / 60.0);

    $total_score += $score;
    $total_frags += $frags;
    $total_kills += $kills;
    $total_deaths += $deaths;
    $total_suicides += $suicides;
    $total_time += $ptime;
    $total_spree1 += $gp_spree1;
    $total_spree2 += $gp_spree2;
    $total_spree3 += $gp_spree3;
    $total_spree4 += $gp_spree4;
    $total_spree5 += $gp_spree5;
    $total_spree6 += $gp_spree6;

    if ($frags < $lowscore)
      $lowscore = $frags;
    if ($frags > $highscore)
      $highscore = $frags;

    if ($gp_bot)
      $nameclass = "darkbot";
    else
      $nameclass = "darkhuman";
    $gpplayer = "<a class=\"$nameclass\" href=\"matchplayer.php?match=$matchnum&amp;player=$gp_num\">$gp_name</a>";

    if ($r > 8)
      $cimage = "resource/nocolor.gif";
    else
      $cimage = "resource/p".$r."color.gif";

    echo <<<EOF
  <tr>
    <td class="dark" align="center">$gp_rank</td>
    <td class="$nameclass" align="center">$gpplayer</td>
    <td class="grey" align="center" width="20"><img src="$cimage" width="16" height="16" border="0" alt="{$LANG_PLAYERCOLOR}" /></td>
    <td class="grey" align="center">$score</td>
    <td class="grey" align="center">$frags</td>
    <td class="grey" align="center">$kills</td>
    <td class="grey" align="center">$deaths</td>
    <td class="grey" align="center">$suicides</td>
    <td class="grey" align="center">$eff%</td>
    <td class="grey" align="center">$fph</td>
    <td class="grey" align="center">$ttl</td>
    <td class="grey" align="center">$time</td>
    <td class="grey" align="center">$gp_spree1</td>
    <td class="grey" align="center">$gp_spree2</td>
    <td class="grey" align="center">$gp_spree3</td>
    <td class="grey" align="center">$gp_spree4</td>
    <td class="grey" align="center">$gp_spree5</td>
    <td class="grey" align="center">$gp_spree6</td>
  </tr>

EOF;
  }

  if ($total_kills + $total_deaths + $total_suicides == 0)
    $eff = "0.0";
  else
    $eff = sprintf("%0.1f", ($total_kills / ($total_kills + $total_deaths + $total_suicides)) * 100.0);
  if ($total_time == 0)
    $fph = "0.0";
  else {
    if ($gametval != 6 && $gametval != 7 && $gametval != 8)
      $fph = sprintf("%0.1f", $total_frags * (3600 / $total_time));
    else
      $fph = sprintf("%0.1f", $total_score * (3600 / $total_time));
  }
  $ttl = sprintf("%0.1f", $total_time / ($total_deaths + $total_suicides + 1));
  $time = sprintf("%0.1f", $total_time / $gm_numplayers / 60.0);

  echo <<<EOF
  <tr>
    <td class="dark" colspan="2" align="center">{$LANG_TOTALS}</td>
    <td class="dark" align="center" width="20"><img src="resource/blankcolor.gif" width="16" height="16" border="0" alt="{$LANG_BLANKCOLOR}" /></td>
    <td class="darkgrey" align="center">$total_score</td>
    <td class="darkgrey" align="center">$total_frags</td>
    <td class="darkgrey" align="center">$total_kills</td>
    <td class="darkgrey" align="center">$total_deaths</td>
    <td class="darkgrey" align="center">$total_suicides</td>
    <td class="darkgrey" align="center">$eff%</td>
    <td class="darkgrey" align="center">$fph</td>
    <td class="darkgrey" align="center">$ttl</td>
    <td class="darkgrey" align="center">$time</td>
    <td class="darkgrey" align="center">$total_spree1</td>
    <td class="darkgrey" align="center">$total_spree2</td>
    <td class="darkgrey" align="center">$total_spree3</td>
    <td class="darkgrey" align="center">$total_spree4</td>
    <td class="darkgrey" align="center">$total_spree5</td>
    <td class="darkgrey" align="center">$total_spree6</td>
  </tr>
</table>

EOF;
}

//=============================================================================
//========== Player Ranking ===================================================
//=============================================================================
if (isset($ranksystem) && $ranksystem) {
  echo <<<EOF
<br />
<table cellpadding="0" cellspacing="2" border="0">
  <tr>
    <td class="heading" colspan="5" align="center" width="340">{$LANG_PLAYERRANKPOINTS}</td>
  </tr>
  <tr>
    <td class="smheading" align="center">{$LANG_PLAYER}</td>
    <td class="smheading" align="center" width="60">{$LANG_START}</td>
    <td class="smheading" align="center" width="60">{$LANG_CHANGE}</td>
    <td class="smheading" align="center" width="8">&nbsp;</td>
    <td class="smheading" align="center" width="60">{$LANG_NEW}</td>
  </tr>

EOF;

  $rankpoints = array();
  $n = 0;
  for ($r = 1; $r <= $gm_numplayers; $r++) {
    $i = $ranks[$r];
    if ($rankbots || !$gplayer[$i]["gp_bot"]) {
      $rankpoints[0][$n] = $i; // Player number
      $rankpoints[1][$n] = $gplayer[$i]["gp_rstart"]; // Start rank
      $rankpoints[2][$n] = $gplayer[$i]["gp_rchange"]; // Change
      $rankpoints[3][$n++] = $gplayer[$i]["gp_rstart"] + $gplayer[$i]["gp_rchange"]; // New rank
    }
  }

  // Sort by change, new rank, start rank, rank order
  array_multisort($rankpoints[2], SORT_DESC, SORT_NUMERIC,
                  $rankpoints[3], SORT_DESC, SORT_NUMERIC,
                  $rankpoints[1], SORT_DESC, SORT_NUMERIC,
                  $rankpoints[0], SORT_ASC, SORT_NUMERIC);

  for ($i = 0; $i < $n; $i++) {
    $num = $rankpoints[0][$i];
    if ($rankbots || !$gplayer[$num]["gp_bot"]) {
      $name = $gplayer[$num]["gp_name"];
      $change = $rankpoints[2][$i];
      $nrank = sprintf("%0.2f", $rankpoints[3][$i]);
      $srank = sprintf("%0.2f", $rankpoints[1][$i]);
      if ($change == 0) {
        $change = "0.00";
        $ud = "";
      }
      else if ($change > 0) {
        $change = sprintf("+%0.2f", $change);
        $ud = "<img src=\"resource/rank_up.gif\" alt=\"{$LANG_RANKUP}\" />";
      }
      else {
        $change = sprintf("%0.2f", $change);
        $ud = "<img src=\"resource/rank_down.gif\" alt=\"{$LANG_RANKDOWN}\" />";
      }
      if ($gplayer[$num]["gp_bot"])
        $nameclass = "darkbot";
      else
        $nameclass = "darkhuman";

      echo <<<EOF
  <tr>
    <td class="$nameclass" align="center"><a class="$nameclass" href="matchplayer.php?match=$matchnum&amp;player=$num">$name</a></td>
    <td class="grey" align="center">$srank</td>
    <td class="grey" align="center">$change</td>
    <td class="grey" align="center">$ud</td>
    <td class="grey" align="center">$nrank</td>
  </tr>

EOF;
    }
  }

  echo <<<EOF
</table>

EOF;
}

//=============================================================================
//========== Special Events ===================================================
//=============================================================================
if ($gametval != 9 && $gametval != 18) {
  $transgib = $carjack = $roadkills = $headhunter = $flakmonkey = $combowhore = $roadrampage = 0;
  $multi1 = $multi2 = $multi3 = $multi4 = $multi5 = $multi6 = $multi7 = 0;

  for ($i = 0; $i <= $maxplayer; $i++) {
    if (isset($gplayer[$i])) {
      $transgib += $gplayer[$i]["gp_transgib"];
      $multi1 += $gplayer[$i]["gp_multi1"];
      $multi2 += $gplayer[$i]["gp_multi2"];
      $multi3 += $gplayer[$i]["gp_multi3"];
      $multi4 += $gplayer[$i]["gp_multi4"];
      $multi5 += $gplayer[$i]["gp_multi5"];
      $multi6 += $gplayer[$i]["gp_multi6"];
      $multi7 += $gplayer[$i]["gp_multi7"];
      $carjack += $gplayer[$i]["gp_carjack"];
      $roadkills += $gplayer[$i]["gp_roadkills"];
      if ($gplayer[$i]["gp_headhunter"])
        $headhunter++;
      if ($gplayer[$i]["gp_flakmonkey"])
        $flakmonkey++;
      if ($gplayer[$i]["gp_combowhore"])
        $combowhore++;
      if ($gplayer[$i]["gp_roadrampage"])
        $roadrampage++;
    }
  }

  if ($gm_firstblood >= 0) {
    $name = $gplayer[$gm_firstblood]["gp_name"];
    if ($gplayer[$gm_firstblood]["gp_bot"])
      $nameclass = "greybot";
    else
      $nameclass = "greyhuman";
    $firstblood = "<a class=\"$nameclass\" href=\"matchplayer.php?match=$matchnum&amp;player=$gm_firstblood\">$name</a>";
  }
  else
    $firstblood = "&nbsp;";

  if ($gm_logger == 1)
    $carjackt = "{$LANG_CARJACKINGS}";
  else {
    $carjack = "&nbsp;";
    $carjackt = "&nbsp;";
  }

  echo <<<EOF
<br />
<table cellpadding="1" cellspacing="2" border="0">
  <tr>
    <td class="heading" colspan="8" align="center">{$LANG_SPECTIALEVENTS}</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="95">{$LANG_CATEGORY}</td>
    <td class="smheading" align="center" width="95">{$LANG_VALUE}</td>
    <td class="smheading" align="center" width="100">{$LANG_CATEGORY}</td>
    <td class="smheading" align="center" width="45">{$LANG_VALUE}</td>
    <td class="smheading" align="center" width="95">{$LANG_CATEGORY}</td>
    <td class="smheading" align="center" width="45">{$LANG_VALUE}</td>
    <td class="smheading" align="center" width="100">{$LANG_CATEGORY}</td>
    <td class="smheading" align="center" width="45">{$LANG_VALUE}</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_FIRSTBLOOD}</td>
    <td class="grey" align="center">$firstblood</td>
    <td class="dark" align="center">{$LANG_HEADSHOTS}</td>
    <td class="grey" align="center">$gm_headshots</td>
    <td class="dark" align="center">{$LANG_ROADKILLS}</td>
    <td class="grey" align="center">$roadkills</td>
    <td class="dark" align="center">$carjackt</td>
    <td class="grey" align="center">$carjack</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_DOUBLEKILLS}</td>
    <td class="grey" align="center">$multi1</td>
    <td class="dark" align="center">{$LANG_MULTIKILLS}</td>
    <td class="grey" align="center">$multi2</td>
    <td class="dark" align="center">{$LANG_MEGAKILLS}</td>
    <td class="grey" align="center">$multi3</td>
    <td class="dark" align="center">{$LANG_ULTRAKILLS}</td>
    <td class="grey" align="center">$multi4</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_MONSTERKILLS}</td>
    <td class="grey" align="center">$multi5</td>
    <td class="dark" align="center">{$LANG_LUDICROUSKILLS}</td>
    <td class="grey" align="center">$multi6</td>
    <td class="dark" align="center">{$LANG_HOLYSHITKILLS}</td>
    <td class="grey" align="center">$multi7</td>
    <td class="dark" align="center">{$LANG_FAILEDTRANSLOC}</td>
    <td class="grey" align="center">$transgib</td>
  </tr>
  <tr>
    <td class="dark" align="center">{$LANG_HEADHUNTER}</td>
    <td class="grey" align="center">$headhunter</td>
    <td class="dark" align="center">{$LANG_FLAKMONKEY}</td>
    <td class="grey" align="center">$flakmonkey</td>
    <td class="dark" align="center">{$LANG_COMBOWHORE}</td>
    <td class="grey" align="center">$combowhore</td>
    <td class="dark" align="center">{$LANG_ROADRAMPAGE}</td>
    <td class="grey" align="center">$roadrampage</td>
  </tr>
</table>

EOF;
}

//=============================================================================
//========== Combos ===========================================================
//=============================================================================
if ($gametval != 18) {
  $combo1 = $combo2 = $combo3 = $combo4 = 0;
  for ($i = 0; $i <= $maxplayer; $i++) {
    if (isset($gplayer[$i])) {
      $combo1 += $gplayer[$i]["gp_combo1"];
      $combo2 += $gplayer[$i]["gp_combo2"];
      $combo3 += $gplayer[$i]["gp_combo3"];
      $combo4 += $gplayer[$i]["gp_combo4"];
    }
  }

  echo <<<EOF
<br />
<table cellpadding="1" cellspacing="2" border="0">
  <tr>
    <td class="heading" colspan="8" align="center">{$LANG_COMBOSUSED}</td>
  </tr>
  <tr>
    <td class="dark" align="center" width="60">{$LANG_SPEED}</td>
    <td class="grey" align="center" width="35">$combo1</td>
    <td class="dark" align="center" width="60">{$LANG_BOOSTER}</td>
    <td class="grey" align="center" width="35">$combo2</td>
    <td class="dark" align="center" width="60">{$LANG_INVISIBLE}</td>
    <td class="grey" align="center" width="35">$combo3</td>
    <td class="dark" align="center" width="60">{$LANG_BERZERK}</td>
    <td class="grey" align="center" width="35">$combo4</td>
  </tr>
</table>
EOF;
}

//=============================================================================
//========== Kills Match Up ===================================================
//=============================================================================
if ($gametval != 9 && $gametval != 18) {
  $tcols = $gm_numplayers + 2;
  $twidth = ($gm_numplayers * 22) + 225;
  $blankspan = 2;
  if ($teams) {
    $tcols++;
    $twidth += 20;
    $blankspan = 3;
  }
  $km_name = array();

  for ($r = 1; $r <= $gm_numplayers; $r++) {
    $i = $ranks[$r];
    $name = nostripspecialchars($gplayer[$i]['gp_name']);
    $km_name[$i] = "";
    for ($i2 = 0; $i2 < strlen($name); $i2++)
      $km_name[$i].=substr($name, $i2, 1)."<br />";
  }

  // Read Individual Kill Log
  $result = sql_queryn($link, "SELECT * FROM {$dbpre}gkills WHERE gk_match=$matchnum");
  if (!$result) {
    echo "Error reading gkills data.<br />\n";
    exit;
  }
  for ($r = 1; $r <= $gm_numplayers; $r++) {
    $i = $ranks[$r];
    for ($r2 = 1; $r2 <= $gm_numplayers; $r2++) {
      $i2 = $ranks[$r2];
      $killmatch[$i][$i2] = 0;
    }
  }
  while ($row = sql_fetch_assoc($result)) {
    while (list ($key, $val) = each ($row))
      ${$key} = $val;
    if ($gk_killer >= 0 && $gk_victim >= 0)
      $killmatch[$gk_killer][$gk_victim]++;
    else if ($gk_victim >= 0)
      $killmatch[$gk_victim][$gk_victim]++;
  }
  sql_free_result($result);

  echo <<<EOF
<br />
<div id="matchup">
<table cellpadding="1" cellspacing="2" border="0" width="$twidth">
  <tr>
    <td class="heading" align="center" colspan="$tcols">{$LANG_KILLSMATCHUP}</td>
  </tr>
  <tr>
    <td class="dark" align="center" colspan="$blankspan" rowspan="$blankspan">&nbsp;</td>
    <td class="dark" align="center" colspan="$gm_numplayers">{$LANG_VICTIM}</td>
  </tr>
  <tr>

EOF;

  if ($teams) { // Team games
    // Display Victim Names
    for ($t = 0; $t < $gm_numteams; $t++) {
      for ($r = 1; $r <= $gm_numplayers; $r++) {
        $i = $ranks[$r];
        if ($gplayer[$i]["gp_team"] == $t) {
          if ($gplayer[$i]["gp_bot"])
            $nameclass = "darkbot";
          else
            $nameclass = "darkhuman";
          $gpnum = $gplayer[$i]["gp_num"];
          echo "    <td class=\"$nameclass\" align=\"center\"><a class=\"$nameclass\" href=\"matchplayer.php?match=$matchnum&amp;player=$gpnum\">$km_name[$i]</a></td>\n";
        }
      }
    }

    // Display Victim Team Colors
    echo "  </tr>
  <tr>
";
    for ($t = 0; $t < $gm_numteams; $t++) {
      for ($r = 1; $r <= $gm_numplayers; $r++) {
        $i = $ranks[$r];
        if ($gplayer[$i]["gp_team"] == $t)
          echo"    <td class=\"{$teamclass[$t]}\" align=\"center\" width=\"20\">&nbsp;</td>\n";
      }
    }

    for ($l = 0, $kstring = ""; $l < strlen($LANG_KILLER); $l++)
      $kstring .= substr($LANG_KILLER, $l, 1) . "<br />";
    $kstring .= substr($LANG_KILLER, $l, 1);

    echo <<<EOF
  </tr>
  <tr>
    <td class="dark" align="center" rowspan="$gm_numplayers" width="20">$kstring</td>

EOF;
    $firstrow = 1;
    // Display Killer Names / Team Color / Kills Per Victim
    for ($t = 0; $t < $gm_numteams; $t++) {
      for ($r = 1; $r <= $gm_numplayers; $r++) {
        $i = $ranks[$r];
        if ($gplayer[$i]["gp_team"] == $t) {
          $name = $gplayer[$i]['gp_name'];
          if ($gplayer[$i]["gp_bot"])
            $nameclass = "darkbot";
          else
            $nameclass = "darkhuman";
          $gpnum = $gplayer[$i]["gp_num"];
          if (!$firstrow)
            echo "  <tr>\n";
          else
            $firstrow = 0;
          echo <<<EOF
    <td class="$nameclass" align="center"><a class="$nameclass" href="matchplayer.php?match=$matchnum&amp;player=$gpnum">$name</a></td>

EOF;
          echo "    <td class=\"{$teamclass[$t]}\" align=\"center\" width=\"20\">&nbsp;</td>\n";

          for ($t2 = 0; $t2 < $gm_numteams; $t2++) {
            for ($r2 = 1; $r2 <= $gm_numplayers; $r2++) {
              $i2 = $ranks[$r2];
              if ($gplayer[$i2]["gp_team"] == $t2) {
                if ($i == $i2)
                  $cbox = "darkgrey";
                else
                  $cbox = "grey";
                $km = $killmatch[$i][$i2];
                if ($km == 0)
                  $km = "&nbsp;";
                echo "    <td class=\"$cbox\" align=\"center\" width=\"20\">$km</td>\n";
              }
            }
          }
          echo "  </tr>\n";
        }
      }
    }
  }
  else { // Non Team Games
    for ($r = 1; $r <= $gm_numplayers; $r++) {
      $i = $ranks[$r];
      if ($gplayer[$i]["gp_bot"])
        $nameclass = "darkbot";
      else
        $nameclass = "darkhuman";
      $gpnum = $gplayer[$i]["gp_num"];
      echo "    <td class=\"$nameclass\" align=\"center\"><a class=\"$nameclass\" href=\"matchplayer.php?match=$matchnum&amp;player=$gpnum\">$km_name[$i]</a></td>\n";
    }

    for ($l = 0, $kstring = ""; $l < strlen($LANG_KILLER); $l++)
      $kstring .= substr($LANG_KILLER, $l, 1) . "<br />";
    $kstring .= substr($LANG_KILLER, $l, 1);

    echo <<<EOF
  </tr>
  <tr>
    <td class="dark" align="center" rowspan="$gm_numplayers" width="20">$kstring</td>

EOF;
    $firstrow = 1;
    for ($r = 1; $r <= $gm_numplayers; $r++) {
      $i = $ranks[$r];
      $name = $gplayer[$i]['gp_name'];
      if ($gplayer[$i]["gp_bot"])
        $nameclass = "darkbot";
      else
        $nameclass = "darkhuman";
      $gpnum = $gplayer[$i]["gp_num"];
      if (!$firstrow)
        echo "  <tr>\n";
      else
        $firstrow = 0;
      echo <<<EOF
    <td class="$nameclass" align="center"><a class="$nameclass" href="matchplayer.php?match=$matchnum&amp;player=$gpnum">$name</a></td>

EOF;
      for ($r2 = 1; $r2 <= $gm_numplayers; $r2++) {
        $i2 = $ranks[$r2];
        if ($i == $i2)
          $cbox = "darkgrey";
        else
          $cbox = "grey";
        $km = $killmatch[$i][$i2];
        if ($km == 0)
          $km = "&nbsp;";
        echo "    <td class=\"$cbox\" align=\"center\" width=\"20\">$km</td>\n";
      }
      echo "
  </tr>
";
    }
  }
  echo <<<EOF
</table>
</div>

EOF;
}

//=============================================================================
//========== Weapon/Suicide Specific Information ==============================
//=============================================================================
if ($gametval != 18) {
  echo <<<EOF
<br />
<table cellpadding="1" cellspacing="2" border="0" width="595">
  <tr>
    <td class="heading" colspan="7" align="center">{$LANG_WEAPONSUICIDESPECIFICINFO}</td>
  </tr>
  <tr>
    <td class="smheading" align="center">{$LANG_WEAPON}</td>
    <td class="smheading" align="center" width="55">{$LANG_FRAGS}</td>
    <td class="smheading" align="center" width="70">{$LANG_PRIMARYKILLS}</td>
    <td class="smheading" align="center" width="70">{$LANG_SECONDARYKILLS}</td>
    <td class="smheading" align="center" width="55">{$LANG_DEATHSHOLDING}</td>
    <td class="smheading" align="center" width="55">{$LANG_SUICIDES}</td>
    <td class="smheading" align="center" width="60">{$LANG_EFF}</td>
  </tr>

EOF;

  // Load Weapon Descriptions
  $result = sql_queryn($link, "SELECT wp_num,wp_secondary,wp_desc,wp_weaptype FROM {$dbpre}weapons");
  if (!$result) {
    echo "{$LANG_ERRORLOADINGWEAPONSDESC}<br />\n";
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
   2 = Deaths Holding
   3 = Suicides
   4 = Weapon Description
   5 = Frags
   6 = Weapon Type
   7 = Road Kills
  */
  $numweapons = 0;
  // Load Weapon Kills for current match
  $result = sql_queryn($link, "SELECT gk_killer,gk_victim,gk_kweapon,gk_vweapon FROM {$dbpre}gkills WHERE gk_match=$matchnum");
  while ($row = sql_fetch_row($result)) {
    $killer = $row[0];
    $victim = $row[1];
    $weap = $row[2];
    $hweap = $row[3];

    // Look for existing kill weapon in wskills description
    $weapon = -1;
    $secondary = 0;
    for ($i = 0; $i < $numweapons; $i++) {
      if ($weap > 0 && $weapon < 0 && !strcmp($wskills[4][$i], $weapons[$weap][0])) {
        $weapon = $i;
        $secondary = $weapons[$weap][1];
      }
    }
    // Add killer's weapon if not already used
    if ($weap > 0 && $weapon < 0) {
      $wskills[0][$numweapons] = $wskills[1][$numweapons] = 0; // Primary Kills / Secondary Kills
      $wskills[2][$numweapons] = $wskills[3][$numweapons] = 0; // Deaths Holding / Suicides
      $wskills[4][$numweapons] = $weapons[$weap][0]; // Description
      $wskills[6][$numweapons] = $weapons[$weap][2]; // Type
      $wskills[7][$numweapons] = 0; // Road Kills
      $weapon = $numweapons++;
      $secondary = $weapons[$weap][1];
    }

    // Look for existing held weapon in wskills description
    $held = -1;
    for ($i = 0; $i < $numweapons; $i++) {
      if ($hweap > 0 && $held < 0 && !strcmp($wskills[4][$i], $weapons[$hweap][0]))
        $held = $i;
    }
    // Add victim's weapon if not already used
    if ($hweap > 0 && $held < 0) {
      $wskills[0][$numweapons] = $wskills[1][$numweapons] = 0; // Primary Kills / Secondary Kills
      $wskills[2][$numweapons] = $wskills[3][$numweapons] = 0; // Deaths Holding / Suicides
      $wskills[4][$numweapons] = $weapons[$hweap][0]; // Description
      $wskills[6][$numweapons] = $weapons[$hweap][2]; // Type
      $wskills[7][$numweapons] = 0; // Road Kills
      $held = $numweapons++;
    }

    if ($killer < -1) {
      if ($weapons[$weap][2] > 0 || $gametval == 9) // Auto-turret/Monster death
        $wskills[0][$weapon]++; // Primary Kill
      else
        $wskills[3][$weapon]++; // Event Suicide
      $wskills[2][$held]++;   // In-hand
    }
    else if ($killer == $victim)
      $wskills[3][$weapon]++; // Suicide
    else {
      if ($secondary == 4)
        $wskills[7][$weapon]++; // Roadkill
      else if ($secondary)
        $wskills[1][$weapon]++; // Secondary Kill
      else
        $wskills[0][$weapon]++; // Primary Kill
      $wskills[2][$held]++;   // In-hand
    }
  }
  sql_free_result($result);

if ($numweapons > 0) {
  for ($i = 0; $i < $numweapons; $i++)
    $wskills[5][$i] = ($wskills[0][$i] + $wskills[1][$i] + $wskills[7][$i]) - $wskills[3][$i];

    // Sort by frags,kills,secondary kills,deaths holding,suicides,description,type,road kills
    array_multisort($wskills[5], SORT_DESC, SORT_NUMERIC,
                    $wskills[0], SORT_DESC, SORT_NUMERIC,
                    $wskills[1], SORT_DESC, SORT_NUMERIC,
                    $wskills[2], SORT_ASC, SORT_NUMERIC,
                    $wskills[3], SORT_ASC, SORT_NUMERIC,
                    $wskills[4], SORT_ASC, SORT_STRING,
                    $wskills[6], SORT_ASC, SORT_NUMERIC,
                    $wskills[7], SORT_ASC, SORT_NUMERIC);

    for ($i = 0; $i < $numweapons; $i++) {
      if ($wskills[6][$i])
        continue;
      $weapon = $wskills[4][$i];
      $kills = $wskills[0][$i];
      $skills = $wskills[1][$i];
      $held = $wskills[2][$i];
      $suicides = $wskills[3][$i];
      $frags = $wskills[5][$i];
  
      if (($kills || $skills || $held || $suicides) && strcmp($weapon, "{$LANG_NONE}")) {
        if ($kills + $skills + $held + $suicides == 0)
          $eff = "0.0";
        else
          $eff = sprintf("%0.1f", (($kills + $skills) / ($kills + $skills + $held + $suicides)) * 100.0);
  
        echo <<< EOF
  <tr>
    <td class="dark" align="center">$weapon</td>
    <td class="grey" align="center">$frags</td>
    <td class="grey" align="center">$kills</td>
    <td class="grey" align="center">$skills</td>
    <td class="grey" align="center">$held</td>
    <td class="grey" align="center">$suicides</td>
    <td class="grey" align="center">$eff%</td>
  </tr>

EOF;
      }
    }
  }
  else {
    echo <<< EOF
  <tr>
    <td class="grey" align="center" colspan="7">{$LANG_NOWEAPONKILLSORDEATHS}</td>
  </tr>

EOF;
  }
  echo "</table>\n";
}

//=============================================================================
//========== Weapon Accuracy Information ======================================
//=============================================================================
$gwaweaps = array(array());
/* gwaweaps:
 0 = Fired
 1 = Hits
 2 = Damage
 3 = Description
*/
$numgwaweaps = 0;

// Load Weapon Accuracy data for current match
$result = sql_queryn($link, "SELECT gwa_weapon,gwa_fired,gwa_hits,gwa_damage FROM {$dbpre}gwaccuracy WHERE gwa_match=$matchnum");
while ($row = sql_fetch_row($result)) {
  $weap = $row[0];
  $fired = $row[1];
  $hits = $row[2];
  $damage = $row[3];

  // Look for existing weapon in gwaweaps description
  $weapon = -1;
  for ($i = 0; $i < $numgwaweaps; $i++) {
    if ($weap > 0 && $weapon < 0 && !strcmp($gwaweaps[3][$i], $weapons[$weap][0]))
      $weapon = $i;
  }
  // Add weapon if not already used
  if ($weap > 0 && $weapon < 0) {
    $gwaweaps[0][$numgwaweaps] = 0;
    $gwaweaps[1][$numgwaweaps] = 0;
    $gwaweaps[2][$numgwaweaps] = 0;
    $gwaweaps[3][$numgwaweaps] = $weapons[$weap][0]; // Description
    $weapon = $numgwaweaps++;
  }

  // Update gwaweaps data
  $gwaweaps[0][$weapon] += $fired;
  $gwaweaps[1][$weapon] += $hits;
  $gwaweaps[2][$weapon] += $damage;
}
sql_free_result($result);

if ($numgwaweaps > 0) {
  echo <<<EOF
<br />
<table cellpadding="1" cellspacing="2" border="0" width="470">
  <tr>
    <td class="heading" colspan="5" align="center">{$LANG_WEAPONACCURACYINFORMATION}</td>
  </tr>
  <tr>
    <td class="smheading" align="center">{$LANG_WEAPON}</td>
    <td class="smheading" align="center" width="78">{$LANG_SHOTSFIRED}</td>
    <td class="smheading" align="center" width="55">{$LANG_HITS}</td>
    <td class="smheading" align="center" width="60">{$LANG_DAMAGE}</td>
    <td class="smheading" align="center" width="65">{$LANG_ACCURACY}</td>
  </tr>

EOF;

  // Sort by fired,hits,damage,description
  array_multisort($gwaweaps[2], SORT_DESC, SORT_NUMERIC,
                  $gwaweaps[1], SORT_DESC, SORT_NUMERIC,
                  $gwaweaps[0], SORT_DESC, SORT_NUMERIC,
                  $gwaweaps[3], SORT_ASC, SORT_STRING);

  for ($i = 0; $i < $numgwaweaps; $i++) {
    $weapon = $gwaweaps[3][$i];
    $fired = $gwaweaps[0][$i];
    $hits = $gwaweaps[1][$i];
    $damage = $gwaweaps[2][$i];
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
  echo "</table>\n";
}

//=============================================================================
//========== Vehicle and Turret Specific Information ==========================
//=============================================================================
if ($gm_logger == 1 && $gametval != 18) {
  echo <<<EOF
<br />
<table cellpadding="1" cellspacing="2" border="0" width="660">
  <tr>
    <td class="heading" colspan="8" align="center">{$LANG_VEHICLETURRETSPECIFICINFORMATION}</td>
  </tr>
  <tr>
    <td class="smheading" align="center">{$LANG_VEHICLETURRET}</td>
    <td class="smheading" align="center" width="55">{$LANG_FRAGS}</td>
    <td class="smheading" align="center" width="70">{$LANG_PRIMARYKILLS}</td>
    <td class="smheading" align="center" width="70">{$LANG_SECONDARYKILLS}</td>
    <td class="smheading" align="center" width="55">{$LANG_ROADKILLS}</td>
    <td class="smheading" align="center" width="55">{$LANG_DEATHSIN}</td>
    <td class="smheading" align="center" width="55">{$LANG_SUICIDES}</td>
    <td class="smheading" align="center" width="60">{$LANG_EFF}</td>
  </tr>

EOF;

  if ($numweapons > 0) {
    // Sort by frags,kills,secondary kills,road kills,deaths in,suicides,description,type
    array_multisort($wskills[5], SORT_DESC, SORT_NUMERIC,
                    $wskills[0], SORT_DESC, SORT_NUMERIC,
                    $wskills[1], SORT_DESC, SORT_NUMERIC,
                    $wskills[7], SORT_DESC, SORT_NUMERIC,
                    $wskills[2], SORT_ASC, SORT_NUMERIC,
                    $wskills[3], SORT_ASC, SORT_NUMERIC,
                    $wskills[4], SORT_ASC, SORT_STRING,
                    $wskills[6], SORT_ASC, SORT_NUMERIC);

    for ($i = 0; $i < $numweapons; $i++) {
      if ($wskills[6][$i] < 1 || $wskills[6][$i] > 2)
        continue;
      $weapon = $wskills[4][$i];
      $kills = $wskills[0][$i];
      $skills = $wskills[1][$i];
      $held = $wskills[2][$i];
      $suicides = $wskills[3][$i];
      $frags = $wskills[5][$i];
      $roadkills = $wskills[7][$i];
  
      if ($kills || $skills || $roadkills || $held || $suicides) {
        if ($kills + $skills + $roadkills + $held + $suicides == 0)
          $eff = "0.0";
        else
          $eff = sprintf("%0.1f", (($kills + $skills + $roadkills) / ($kills + $skills + $roadkills + $held + $suicides)) * 100.0);

        echo <<< EOF
  <tr>
    <td class="dark" align="center">$weapon</td>
    <td class="grey" align="center">$frags</td>
    <td class="grey" align="center">$kills</td>
    <td class="grey" align="center">$skills</td>
    <td class="grey" align="center">$roadkills</td>
    <td class="grey" align="center">$held</td>
    <td class="grey" align="center">$suicides</td>
    <td class="grey" align="center">$eff%</td>
  </tr>

EOF;
      }
    }
  }
  else {
    echo <<< EOF
  <tr>
    <td class="grey" align="center" colspan="5">{$LANG_NOVEHICLEORTURRETKILLS}</td>
  </tr>

EOF;
  }
  echo "</table>\n";
}
else if ($gametval != 18) {
  echo <<<EOF
<br />
<table cellpadding="1" cellspacing="2" border="0" width="560">
  <tr>
    <td class="heading" colspan="6" align="center">{$LANG_VEHICLETURRETSPECIFICINFORMATION}</td>
  </tr>
  <tr>
    <td class="smheading" align="center">{$LANG_VEHICLETURRET}</td>
    <td class="smheading" align="center" width="55">{$LANG_FRAGS}</td>
    <td class="smheading" align="center" width="70">{$LANG_PRIMARYKILLS}</td>
    <td class="smheading" align="center" width="70">{$LANG_SECONDARYKILLS}</td>
    <td class="smheading" align="center" width="55">{$LANG_ROADKILLS}</td>
    <td class="smheading" align="center" width="55">{$LANG_SUICIDES}</td>
  </tr>

EOF;

  if ($numweapons > 0) {
    // Sort by frags,kills,secondary kills,road kills,suicides,description,deaths holding,type
    array_multisort($wskills[5], SORT_DESC, SORT_NUMERIC,
                    $wskills[0], SORT_DESC, SORT_NUMERIC,
                    $wskills[1], SORT_DESC, SORT_NUMERIC,
                    $wskills[7], SORT_DESC, SORT_NUMERIC,
                    $wskills[3], SORT_ASC, SORT_NUMERIC,
                    $wskills[4], SORT_ASC, SORT_STRING,
                    $wskills[2], SORT_ASC, SORT_NUMERIC,
                    $wskills[6], SORT_ASC, SORT_NUMERIC);

    for ($i = 0; $i < $numweapons; $i++) {
      if ($wskills[6][$i] < 1 || $wskills[6][$i] > 2)
        continue;
      $weapon = $wskills[4][$i];
      $kills = $wskills[0][$i];
      $skills = $wskills[1][$i];
      $suicides = $wskills[3][$i];
      $frags = $wskills[5][$i];
      $roadkills = $wskills[7][$i];
  
      if ($kills || $skills || $roadkills || $suicides) {
        echo <<< EOF
  <tr>
    <td class="dark" align="center">$weapon</td>
    <td class="grey" align="center">$frags</td>
    <td class="grey" align="center">$kills</td>
    <td class="grey" align="center">$skills</td>
    <td class="grey" align="center">$roadkills</td>
    <td class="grey" align="center">$suicides</td>
  </tr>

EOF;
      }
    }
  }
  else {
    echo <<< EOF
  <tr>
    <td class="grey" align="center" colspan="5">{$LANG_NOVEHICLEORTURRETKILLS}</td>
  </tr>

EOF;
  }
  echo "</table>\n";
}

//=============================================================================
//========== Invasion Monster Information =====================================
//=============================================================================
if ($gametval == 9) {
  echo <<<EOF
<br />
<table cellpadding="1" cellspacing="2" border="0" width="340">
  <tr>
    <td class="heading" colspan="3" align="center">{$LANG_INVASIONMONSTERINFORMATION}</td>
  </tr>
  <tr>
    <td class="smheading" align="center">{$LANG_MONSTER}</td>
    <td class="smheading" align="center" width="95">{$LANG_PLAYERSKILLED}</td>
    <td class="smheading" align="center" width="55">{$LANG_DEATHS}</td>
  </tr>

EOF;

  if ($numweapons > 0) {
    // Sort by kills,deaths,description
    array_multisort($wskills[5], SORT_DESC, SORT_NUMERIC,
                    $wskills[2], SORT_ASC, SORT_NUMERIC,
                    $wskills[0], SORT_DESC, SORT_NUMERIC,
                    $wskills[1], SORT_DESC, SORT_NUMERIC,
                    $wskills[4], SORT_ASC, SORT_STRING,
                    $wskills[3], SORT_ASC, SORT_NUMERIC,
                    $wskills[6], SORT_ASC, SORT_NUMERIC,
                    $wskills[7], SORT_ASC, SORT_NUMERIC);

    for ($i = 0; $i < $numweapons; $i++) {
      if ($wskills[6][$i] != 3)
        continue;
      $weapon = $wskills[4][$i];
      $kills = $wskills[0][$i] + $wskills[1][$i];
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
  else {
    echo <<< EOF
  <tr>
    <td class="grey" align="center" colspan="5">No Vehicle or Turret Kills</td>
  </tr>

EOF;
  }
  echo "</table>\n";
}

//=============================================================================
//========== Killing Sprees ===================================================
//=============================================================================
if ($gametval != 9 && $gametval != 18) {
  $result = sql_queryn($link, "SELECT * FROM {$dbpre}gevents WHERE ge_event=1 AND ge_match=$matchnum ORDER BY ge_num");
  if (!$result) {
    echo "{$LANG_ERRORLOADINGEVENTS}<br />\n";
    exit;
  }
  $sprees = $header = 0;
  while ($row = sql_fetch_assoc($result)) {
    if ($row["ge_quant"] >= 5) {
      while (list ($key, $val) = each ($row))
        ${$key} = $val;

      if ($ge_plr >= 0 && isset($gplayer[$ge_plr]) && $gplayer[$ge_plr]["gp_name"] != "") {
        $name = $gplayer[$ge_plr]["gp_name"];
        $bot = $gplayer[$ge_plr]["gp_bot"];
        if ($bot)
          $nameclass = "darkbot";
        else
          $nameclass = "darkhuman";
      }
      else {
        $name = "";
        $bot = 0;
      }
      $player = "<a class=\"$nameclass\" href=\"matchplayer.php?match=$matchnum&amp;player=$ge_plr\">$name</a>";
      $time = sprintf("%0.1f", ($ge_time - $ge_length) / 6000);
      $length = sprintf("%0.1f", $ge_length / 6000);

      $type = "";
      if ($ge_quant >= 5 && $ge_quant < 10)
        $type = "{$LANG_KILLINGSPREE}";
      else if ($ge_quant >= 10 && $ge_quant < 15)
        $type = "{$LANG_RAMPAGE}";
      else if ($ge_quant >= 15 && $ge_quant < 20)
        $type = "{$LANG_DOMINATING}";
      else if ($ge_quant >= 20 && $ge_quant < 25)
        $type = "{$LANG_UNSTOPPABLE}";
      else if ($ge_quant >= 25 && $ge_quant < 30)
        $type = "{$LANG_GODLIKE}";
      else if ($ge_quant >= 30)
        $type = "{$LANG_WICKEDSICK}";

      switch ($ge_reason) {
        case 0: // Game Ended
          $reason = "{$LANG_GAMEENDED}";
          break;
        case 1: // Killed by {player} with a {weapon}
          $killer = $gplayer[$ge_opponent]["gp_name"];
          $weapon = $weapons[$ge_item][0];
          if (!strcmp($weapon, "{$LANG_FELL}"))
            $reason = "{$LANG_KNOCKEDOFFALEDGEBY} $killer";
          else if (!strcmp($weapon, "{$LANG_FELLINTOLAVA}"))
            $reason = "{$LANG_KNOCKEDINTOLAVABY} $killer";
          else if (!strcmp($weapon, "{$LANG_CRUSHED}") || !strcmp($weapon, "{$LANG_TELEFRAGGED}")  || !strcmp($weapon, "{$LANG_DEPRESSURIZED}"))
            $reason = "$weapon by $killer";
          else {
            $wfl = strtoupper($weapon[0]);
            if ($wfl == 'A' || $wfl == 'E' || $wfl == 'I' || $wfl == 'O' || $wfl == 'U' || $wfl == 'Y')
              $reason = "{$LANG_KILLEDBY} $killer {$LANG_WITHAN} $weapon";
            else
              $reason = "{$LANG_KILLEDBY} $killer {$LANG_WITHA} $weapon";
          }
          break;
        case 2: // Suicided with {weapon}
          $weapon = $weapons[$ge_item][0];
          if (!strcmp($weapon, "{$LANG_SUICIDED}") || !strcmp($weapon, "{$LANG_DROWNED}"))
            $reason = "$weapon";
          else if (!strcmp($weapon, "{$LANG_CORRODED}") || !strcmp($weapon, "{$LANG_CRUSHED}") || !strcmp($weapon, "{$LANG_GIBBED}") || !strcmp($weapon, "{$LANG_DEPRESSURIZED}"))
            $reason = "Was $weapon";
          else if (!strcmp($weapon, "{$LANG_FELL}"))
            $reason = "{$LANG_FELLTOTHEIRDEATH}";
          else if (!strcmp($weapon, "{$LANG_FELLINTOLAVA}"))
            $reason = "{$LANG_FELLINTOLAVA}";
          else if (!strcmp($weapon, "{$LANG_SWAMTOOFAR}"))
            $reason = "{$LANG_TRIEDTOSWIMTOOFAR}";
          else if (!strcmp($weapon, "{$LANG_VEHICLEEXPLOSION}"))
            $reason = "{$LANG_SUICIDEDFROMVEHICLEEXPLOSION}";
          else if (!strcmp($weapon, "{$LANG_RECKLESSDRIVING}"))
            $reason = "{$LANG_SUICIDEDFROMRECKLESSDRIVING}";
          else {
            $wfl = strtoupper($weapon[0]);
            if ($wfl == 'A' || $wfl == 'E' || $wfl == 'I' || $wfl == 'O' || $wfl == 'U' || $wfl == 'Y')
              $reason = "{$LANG_SUICIDEDWITHAN} $weapon";
            else
              $reason = "{$LANG_SUICIDEDWITHA} $weapon";
          }
          break;
        case 3: // Died from {weapon}
          $weapon = $weapons[$ge_item][0];
          if (!strcmp($weapon, "{$LANG_SUICIDED}") || !strcmp($weapon, "{$LANG_DROWNED}"))
            $reason = "$weapon";
          else if (!strcmp($weapon, "{$LANG_CORRODED}") || !strcmp($weapon, "{$LANG_CRUSHED}") || !strcmp($weapon, "{$LANG_GIBBED}") || !strcmp($weapon, "{$LANG_DEPRESSURIZED}"))
            $reason = "Was $weapon";
          else if (!strcmp($weapon, "{$LANG_FELL}"))
            $reason = "{$LANG_FELLTOTHEIRDEATH}";
          else if (!strcmp($weapon, "{$LANG_FELLINTOLAVA}"))
            $reason = "{$LANG_FELLINTOLAVA}";
          else if (!strcmp($weapon, "{$LANG_SWAMTOOFAR}"))
            $reason = "{$LANG_TRIEDTOSWIMTOOFAR}";
          else {
            $wfl = strtoupper($weapon[0]);
            if ($wfl == 'A' || $wfl == 'E' || $wfl == 'I' || $wfl == 'O' || $wfl == 'U' || $wfl == 'Y')
              $reason = "{$LANG_DIEDFROMAN} $weapon";
            else
              $reason = "{$LANG_DIEDFROMA} $weapon";
          }
          break;
        case 4: // Disconnected
          $reason = "{$LANG_DISCONNECTED}";
          break;
        case 5: // Team Killed
          $killer = $gplayer[$ge_opponent]["gp_name"];
          $weapon = $weapons[$ge_item][0];
          if (!strcmp($weapon, "{$LANG_CRUSHED}") || !strcmp($weapon, "{$LANG_TELEFRAGGED}")  || !strcmp($weapon, "{$LANG_DEPRESSURIZED}"))
            $reason = "{$LANG_TEAMKILLED} - $weapon {$LANG_BY} $killer";
          else {
            $wfl = strtoupper($weapon[0]);
            if ($wfl == 'A' || $wfl == 'E' || $wfl == 'I' || $wfl == 'O' || $wfl == 'U' || $wfl == 'Y')
              $reason = "{$LANG_TEAMKILLEDBY} $killer {$LANG_WITHAN} $weapon";
            else
              $reason = "{$LANG_TEAMKILLEDBY} $killer {$LANG_WITHA} $weapon";
          }
          break;
        case 6: // Team Change
          $reason = "{$LANG_CHANGEDTEAMS}";
          break;
        default:
          $reason = "{$LANG_UNKNOWN}";
      }

      if (!$header) {
        echo <<<EOF
<br />
<table cellpadding="1" cellspacing="2" border="0" width="680">
  <tr>
    <td class="heading" colspan="6" align="center">{$LANG_KILLINGSPREES}</td>
  </tr>
  <tr>
    <td class="smheading" align="center">{$LANG_PLAYER}</td>
    <td class="smheading" align="center" width="90">{$LANG_SPREETYPE}</td>
    <td class="smheading" align="center" width="45">{$LANG_STARTTIME}</td>
    <td class="smheading" align="center" width="55">{$LANG_TIMEINSPREE}</td>
    <td class="smheading" align="center" width="80">{$LANG_KILLSDURINGSPREE}</td>
    <td class="smheading" align="center">{$LANG_REASONSPREESTOPPED}</td>
  </tr>

EOF;
        $header = 1;
      }

      echo <<<EOF
  <tr>
    <td class="$nameclass" align="center">$player</td>
    <td class="dark" align="center">$type</td>
    <td class="grey" align="center">$time</td>
    <td class="grey" align="center">$length</td>
    <td class="grey" align="center">$ge_quant</td>
    <td class="grey" align="center">$reason</td>
  </tr>

EOF;
      $sprees++;
    }
  }
  sql_free_result($result);
  if (!$sprees) {
    echo <<<EOF
<br />
<table cellpadding="1" cellspacing="2" border="0" width="600">
  <tr>
    <td class="heading" align="center">{$LANG_NOKILLINGSPREES}</td>
  </tr>

EOF;
  }
  echo "</table>\n";
}

//=============================================================================
//========== Total Items Collected ============================================
//=============================================================================
if ($gametval != 18) {
  echo <<<EOF
<br />
<table cellpadding="1" cellspacing="2" border="0" width="600">
  <tr>
    <td class="heading" colspan="6" align="center">{$LANG_TOTALITEMSCOLLECTED}</td>
  </tr>
  <tr>
    <td class="smheading" align="center">{$LANG_ITEMTYPE}</td>
    <td class="smheading" align="center" width="35">{$LANG_NO}</td>
    <td class="smheading" align="center">{$LANG_ITEMTYPE}</td>
    <td class="smheading" align="center" width="35">{$LANG_NO}</td>
    <td class="smheading" align="center">{$LANG_ITEMTYPE}</td>
    <td class="smheading" align="center" width="35">{$LANG_NO}</td>
  </tr>

EOF;

  $result = sql_queryn($link, "SELECT it_num,it_desc FROM {$dbpre}items");
  if (!$result) {
    echo "{$LANG_ERRORLOADINGITEMPICKUPDESC}<br />\n";
    exit;
  }
  $numitems = 0;
  while ($row = sql_fetch_row($result)) {
    $num = intval($row[0]);
    $pickups[0][$num] = $row[1];
    $pickups[1][$num] = 0;
    $numitems++;
  }
  sql_free_result($result);

  $result = sql_queryn($link, "SELECT gi_item,gi_pickups FROM {$dbpre}gitems WHERE gi_match=$matchnum");
  if (!$result) {
    echo "{$LANG_ERRORLOADINGITEMPICKUPS}<br />\n";
    exit;
  }
  while ($row = sql_fetch_row($result)) {
    $num = intval($row[0]);
    $pickups[1][$num] += $row[1];
  }
  sql_free_result($result);

  if ($numitems > 0)
    array_multisort($pickups[1], SORT_DESC, SORT_NUMERIC,
                    $pickups[0], SORT_ASC, SORT_STRING);

  $col = $totpickups = 0;
  for ($i = 0; $i < $numitems; $i++) {
    $item = $pickups[0][$i];
    $num = $pickups[1][$i];
    if ($num) {
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
      $totpickups++;
    }
  }

  if (!$totpickups) {
    echo <<<EOF
  <tr>
    <td class="dark" align="center" colspan="6">{$LANG_THEREWERENOITEMPICKUPSLOGGED}</td>
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
}

//=============================================================================
//========== Player Info ======================================================
//=============================================================================
if ($gm_logger == 1) {
  echo <<<EOF
<br />
<table cellpadding="1" cellspacing="2" border="0" width="350">
  <tr>
    <td class="heading" colspan="3" align="center">{$LANG_PLAYERNETSPEEDANDPINGTIME}</td>
  </tr>
  <tr>
    <td class="smheading" align="center">{$LANG_PLAYER}</td>
    <td class="smheading" align="center" width="70">{$LANG_NETSPEED}</td>
    <td class="smheading" align="center" width="70">{$LANG_AVGPING}</td>
  </tr>

EOF;

  for ($r = 1; $r <= $gm_numplayers; $r++) {
    $i = $ranks[$r];
    $bot = $gplayer[$i]["gp_bot"];
    if ($bot) // Do not list bots here
      continue;
    $name = "<a class=\"darkhuman\" href=\"matchplayer.php?match=$matchnum&amp;player=$i\">{$gplayer[$i]['gp_name']}</a>";
    $netspeed = $gplayer[$i]["gp_netspeed"];
    $ping = $gplayer[$i]["gp_ping"];

    echo <<<EOF
  <tr>
    <td class="darkhuman" align="center">$name</td>
    <td class="grey" align="center">$netspeed</td>
    <td class="grey" align="center">$ping</td>
  </tr>

EOF;
  }
  echo "</table>\n";
}

//=============================================================================
//========== Connection Log ===================================================
//=============================================================================
echo <<<EOF
<br />
<table cellpadding="1" cellspacing="2" border="0">
  <tr>
    <td class="heading" colspan="3" align="center">{$LANG_CONNECTIONLOG}</td>
  </tr>
  <tr>
    <td class="smheading" align="right" width="50">{$LANG_TIME}</td>
    <td class="smheading" align="center" width="200">{$LANG_PLAYER}</td>
    <td class="smheading" align="center" width="100">{$LANG_STATUS}</td>
  </tr>

EOF;

$result = sql_querynb($link, "SELECT ge_plr,ge_event,ge_time,ge_quant,ge_reason FROM {$dbpre}gevents WHERE ge_match=$matchnum AND ((ge_event BETWEEN 2 AND 4) OR ge_event=10) ORDER BY ge_num");
if (!$result) {
  echo "{$LANG_ERRORLOADINGCONNECTIONEVENTS}<br />\n";
  exit;
}
while ($row = sql_fetch_assoc($result)) {
  $plr = $row["ge_plr"];
  if ($plr >= 0 && isset($gplayer[$plr]) && $gplayer[$plr]["gp_name"] != "") {
    $name = $gplayer[$plr]["gp_name"];
    $bot = $gplayer[$plr]["gp_bot"];
    if ($bot)
      $nameclass = "darkbot";
    else
      $nameclass = "darkhuman";
  }
  else {
    $name = "";
    $bot = 0;
  }

  $time = sprintf("%0.1f", ($row["ge_time"] - ($delay * $gm_timeoffset)) / 6000);
  $quant = $row["ge_quant"];
  $reas = $row["ge_reason"];

  switch ($row["ge_event"]) {
    case 2: // Connect/Disconnect
      switch ($row["ge_reason"]) {
        case 0:
          if (($gametval < 2 || $gametval > 4) && $name != "")
            $reason = "{$LANG_CONNECTED}";
          else
            $reason = "";
          $rclass = "grey";
          break;
        case 1:
          if ($name != "")
            $reason = "{$LANG_DISCONNECTED}";
          else
            $reason = "";
          $rclass = "warn";
          break;
        default:
          $reson = "{$LANG_UNKNOWN}";
          $rclass = "grey";
      }
      $player = "<a class=\"$nameclass\" href=\"matchplayer.php?match=$matchnum&amp;player=$plr\">$name</a>";
      break;
    case 3:
      switch ($row["ge_reason"]) {
        case 0:
          $reason = "{$LANG_GAMESTART}";
          $time = "0.0";
          break;
        case 1:
          $reason = "{$LANG_GAMEENDED}";
          break;
        default:
          $reason = "{$LANG_UNKNOWN}";
      }
      $player = "";
      $rclass = "gselog";
      break;
    case 4:
      $reason = "{$teamcolor[$quant]} Team";
      $player = "<a class=\"$nameclass\" href=\"matchplayer.php?match=$matchnum&amp;player=$plr\">$name</a>";
      $rclass = "tclog";
      break;
    case 10:
      $rclass = "votesys";
      switch ($reas) {
        case 1:
        case 2:
          $result2 = sql_queryn($link, "SELECT ed_desc FROM {$dbpre}eventdesc WHERE ed_num=$quant LIMIT 1");
          if (!$result2) {
            echo "{$LANG_ERRORACCESSINGEVENTDESCDB}{$break}\n";
            exit;
          }
          $row2 = sql_fetch_row($result2);
          sql_free_result($result2);
          if ($row2)
            $mapname = "'{$row2[0]}'";
          else
            $mapname = "{$LANG_ANUNKNOWNMAP}";
          if ($reas == 1) {
            if ($plr >= 0)
              $reason = "{$LANG_MAPVOTEFORCEDBYADMINFOR} $mapname";
            else
              $reason = "{$LANG_MAPVOTESUCCEEDEDFOR} $mapname";
            $prior = 4;
          }
          else {
            if ($item == 1)
              $votes = "$item {$LANG_VOTE}";
            else if ($item == 0)
              $votes = "{$LANG_ADMIN}";
            else
              $votes = "$item {$LANG_VOTE}";
            $reason = "{$LANG_VOTEDFOR} $mapname ($votes)";
          }
          if ($plr >= 0)
            $player = "<a class=\"$nameclass\" href=\"matchplayer.php?match=$matchnum&amp;player=$plr\">$name</a>";
          else
            $player = "";
          break;
        case 3:
        case 4:
          $kplayer = stripspecialchars($gplayer[$quant]["gp_name"]);
          if ($reas == 3) {
            if ($plr >= 0)
              $reason = "{$LANG_KICKVOTEOF} $kplayer {$LANG_FORCEDBYADMIN}";
            else
              $reason = "{$LANG_KICKVOTEFOR} $kplayer {$LANG_SUCCEEDED}";
          }
          else
            $reason = "{$LANG_VOTEDTOKICK} $kplayer";
          if ($plr >= 0)
            $player = "<a class=\"$nameclass\" href=\"matchplayer.php?match=$matchnum&amp;player=$plr\">$name</a>";
          else
            $player = "";
          break;
        case 5:
        case 6:
          $result2 = sql_queryn($link, "SELECT ed_desc FROM {$dbpre}eventdesc WHERE ed_num=$quant LIMIT 1");
          if (!$result2) {
            echo "{$LANG_ERRORACCESSINGEVENTDESCDB}{$break}\n";
            exit;
          }
          $row2 = sql_fetch_row($result2);
          sql_free_result($result2);
          if ($row2)
            $gamename = "'{$row2[0]}'";
          else
            $gamename = "{$LANG_ANUNKNOWNGAMETYPE}";
          if ($plr >= 0)
            $reason = "{$LANG_GAMETYPEVOTEFORCEDBYADMIN} $gamename";
          else
            $reason = "{$LANG_GAMETYPEVOTESUCCEEDEDFOR} $gamename";
          break;
        default:
          $reason = "";
      }
  }

  if ($reason != "") {
    echo <<<EOF
  <tr>
    <td class="dark" align="right">$time</td>
    <td class="$nameclass" align="center">$player</td>
    <td class="$rclass" align="center">$reason</td>
  </tr>

EOF;
  }
}
echo "</table>\n";

//=============================================================================
//========== Chat Log Link ====================================================
//=============================================================================
$result = sql_queryn($link, "SELECT COUNT(*) FROM {$dbpre}gchat WHERE gc_match=$matchnum ORDER BY gc_time");
if (!$result) {
  echo "{$LANG_ERRORACCESSINGCHATLOG}<br />\n";
  exit;
}
list($numchat) = sql_fetch_row($result);
sql_free_result($result);
if ($numchat == 1)
  $cmsg = $LANG_CHATMESSAGE;
else
  $cmsg = $LANG_CHATMESSAGES;

echo <<<EOF
<br />
<table cellpadding="1" cellspacing="2" border="0" width="400">
  <tr>
    <td class="chatlink" colspan="3" align="center"><a class="chatlink" href="chatlog.php?match=$matchnum">{$LANG_CHATEVENTLOG}</a> {$LANG_CONTAINS} $numchat {$cmsg}</td>
  </tr>
</table>

EOF;

sql_close($link);

echo <<<EOF
</center>

</td></tr></table>

</body>
</html>

EOF;

?>