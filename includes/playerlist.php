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

if (preg_match("/playerlist.php/i", $_SERVER["PHP_SELF"])) {
  echo "{$LANG_ACCESSDENIED}\n";
  die();
}

$page = 1;
$type = "";
$rank = "";
$searchid = 0;
$searchname = "";
$clear = "";
check_get($page, "page");
check_get($type, "type");
check_get($rank, "rank");
check_get($searchid, "SearchID");
check_get($searchname, "SearchName");
check_get($clear, "Clear");
if (!is_numeric($searchid))
  $searchid = 0;
if (!is_numeric($page))
  $page = 1;

if ($clear == "Clear") {
  $searchid = 0;
  $searchname = "";
}

$searchstring = "";
if ($searchid) {
  $searchidvalue = "VALUE=\"$searchid\"";
  $searchstring = "&amp;SearchID=$searchid";
}
else
  $searchidvalue = "";
if ($searchname && !$searchid)
  $searchstring = "&amp;SearchName=$searchname";

$typelink = "";
if ($type == "") {
  if ($plistall) {
    $type = "all";
    $typelink = "&amp;type=all";
  }
  else
    $type = "humans";
}
else if ($type == "bots")
  $typelink = "&amp;type=bots";
else if ($type == "all")
  $typelink = "&amp;type=all";

// Show only humans if showbots is not enabled
if (!$showbots) {
  $type = "humans";
  $typelink = "";
}

$link = sql_connect();

// Calculate Number of Pages
if ($searchid > 0)
  $numpages = 1;
else if ($searchname != "") {
  $slashedname = sql_addslashes($searchname);

  // Binary string types in MySQL have spaces stored as 0xa0!
  for ($i = 0; $i < strlen($slashedname); $i++)
    if ($slashedname[$i] == " ")
      $slashedname[$i] = chr(0xa0);

  if ($type == "bots")
    $result = sql_queryn($link, "SELECT COUNT(*) FROM {$dbpre}players WHERE plr_name LIKE '%{$slashedname}%' AND plr_bot=1");
  else if ($type == "all")
    $result = sql_queryn($link, "SELECT COUNT(*) FROM {$dbpre}players WHERE plr_name LIKE '%{$slashedname}%'");
  else
    $result = sql_queryn($link, "SELECT COUNT(*) FROM {$dbpre}players WHERE plr_name LIKE '%{$slashedname}%' AND plr_bot=0");
  if (!$result) {
    echo "{$LANG_PLAYERDATABASEERROR}<br>\n";
    exit;
  }
  list($numplayers) = sql_fetch_row($result);
  sql_free_result($result);
  $numpages = (int) ceil($numplayers / $playerspage);
}
else {
  if ($type == "bots")
    $result = sql_queryn($link, "SELECT COUNT(*) FROM {$dbpre}players WHERE plr_bot=1");
  else if ($type == "all")
    $result = sql_queryn($link, "SELECT COUNT(*) FROM {$dbpre}players");
  else
    $result = sql_queryn($link, "SELECT COUNT(*) FROM {$dbpre}players WHERE plr_bot=0");
  if (!$result) {
    echo "{$LANG_PLAYERDATABASEERROR}<br>\n";
    exit;
  }
  list($numplayers) = sql_fetch_row($result);
  sql_free_result($result);
  $numpages = (int) ceil($numplayers / $playerspage);
}

if (!$page)
  $page = 1;
else if ($page < 1 || $page > $numpages)
  $page = 1;

if ($type == "bots") {
  $selplayers = "";
  $selbots = "SELECTED";
  $selall = "";
}
else if ($type == "all") {
  $selplayers = "";
  $selbots = "";
  $selall = "SELECTED";
}
else {
  $selplayers = "SELECTED";
  $selbots = "";
  $selall = "";
}

if ($playersearch == 1 || ($playersearch == 2 && $numpages > 1)) {
  echo <<<EOF
<font size="1"><br /></font>
<form name="playersearch" method="post" action="index.php?stats=players">
  <input type="hidden" name="type" value="$type">
  <table class="searchform">
    <tr>
      <td align="right">{$LANG_ID}:</td>
      <td width="90" align="left"><input type="text" name="SearchID" maxlength="10" size="10" $searchidvalue class="searchformbox"></td>
      <td align="right">{$LANG_NAME}:</td>
      <td width="150" align="left"><input type="text" name="SearchName" maxlength="35" size="20" value="$searchname" class="searchformbox"></td>
      <td align="left"><input type="submit" name="Default" value="{$LANG_SEARCH}" class="searchform"></td>
      <td>&nbsp;</td>
      <td><input type="submit" name="Clear" value="{$LANG_CLEAR}" class="searchform"></td>

EOF;

  if ($showbots) {
    echo <<<EOF
      <td width="120" align="right">
        <select name="plrbot" onChange="changePage(this.form.plrbot)">
          <option value="humans" $selplayers>{$LANG_HUMANS}</option>
          <option value="bots" $selbots>{$LANG_BOTS}</option>
          <option value="all" $selall>{$LANG_ALL}</option>
        </select>
      </td>

EOF;
  }

  echo <<<EOF
    </tr>
  </table>
</form>

EOF;
}
else if ($showbots) {
  echo <<<EOF
<div class="searchform">
  <form name="playersearch" method="post" action="index.php?stats=players">
    <span class="opnote">*{$LANG_SELECTHEADINGS}</span>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Type:
    <select name="plrbot" onChange="changePage(this.form.plrbot)">
      <option value="humans" $selplayers>{$LANG_HUMANS}</option>
      <option value="bots" $selbots>{$LANG_BOTS}</option>
      <option value="all" $selall>{$LANG_ALL}</option>
    </select>
  </form>
</div>

EOF;
}

if ($numpages > 1) {
  echo "<div class=\"pages\"><b>{$LANG_PAGE} [$page/$numpages] {$LANG_SELECTION}: ";
  $prev = $page - 1;
  $next = $page + 1;
  if ($rank)
    $rankurl = "&amp;rank=$rank";
  else
    $rankurl = "";
  if ($page != 1)
    echo "<a class=\"pages\" href=\"index.php?stats=players{$rankurl}&amp;page=1{$typelink}{$searchstring}\">[{$LANG_FIRST}]</a> / <a class=\"pages\" href=\"index.php?stats=players{$rankurl}&amp;page={$prev}{$typelink}\">[{$LANG_PREVIOUS}]</a> / ";
  else
    echo "[{$LANG_FIRST}] / [{$LANG_PREVIOUS}] / ";
  if ($page < $numpages)
    echo "<a class=\"pages\" href=\"index.php?stats=players{$rankurl}&amp;page={$next}{$typelink}{$searchstring}\">[{$LANG_NEXT}]</a> / <a class=\"pages\" href=\"index.php?stats=players{$rankurl}&amp;page={$numpages}{$typelink}{$searchstring}\">[{$LANG_LAST}]</a>";
  else
    echo "[{$LANG_NEXT}] / [{$LANG_LAST}]";
  echo "</b></div>\n";
}

switch ($rank) {
  case "num": $order = "pnum ASC"; $index = "(pnum)"; $extra = ""; break;
  case "name": $order = "plr_name ASC"; $index = "(plr_bot)"; $extra = ""; break;
  case "frags": $order = "plr_frags DESC,plr_deaths DESC"; $index = "(plr_sfrags)"; $extra = ""; break;
  case "kills": $order = "plr_kills DESC,plr_frags DESC,plr_deaths DESC"; $index = "(plr_skills)"; $extra = ""; break;
  case "deaths": $order = "plr_deaths DESC,plr_frags DESC"; $index = "(plr_sdeaths)"; $extra = ""; break;
  case "suicides": $order = "plr_suicides DESC,plr_frags DESC,plr_deaths DESC"; $index = "(plr_ssuicides)"; $extra = ""; break;
  case "eff": $order = "plr_eff DESC,plr_kills DESC,plr_frags DESC,plr_deaths DESC"; $index = "(plr_seff)"; $extra = ",plr_eff"; break;
  case "fph": $order = "plr_fph DESC,plr_kills DESC,plr_frags DESC,plr_deaths DESC"; $index = "(plr_sfph)"; $extra = ",plr_fph"; break;
  case "sph": $order = "plr_sph DESC,plr_kills DESC,plr_frags DESC,plr_deaths DESC"; $index = "(plr_ssph)"; $extra = ",plr_sph"; break;
  case "matches": $order = "plr_matches DESC,plr_kills DESC,plr_frags DESC,plr_deaths DESC"; $index = "(plr_smatches)"; $extra = ""; break;
  case "wins": $order = "(plr_wins+plr_teamwins)/plr_matches DESC,plr_matches DESC,plr_kills DESC,plr_frags DESC,plr_deaths DESC"; $index = "(plr_swins)"; $extra = ""; break;
  case "time": $order = "plr_time DESC,plr_kills DESC,plr_frags DESC,plr_deaths DESC"; $index = "(plr_stime)"; $extra = ""; break;
  default: $order = "plr_score DESC,plr_frags DESC,plr_deaths DESC"; $index = "(plr_sscore)"; $extra = ""; break; // score
}

if ($playersearch == 1 || ($playersearch == 2 && $numpages > 1) || !$showbots)
  echo "<div class=\"opnote\">*Select headings to change sort order (default=score).</div>\n";

echo <<<EOF
<table cellpadding="1" cellspacing="2" border="0" width="700" class="box">
  <tr>
    <td class="heading" colspan="14" align="center">Unreal Tournament Player Stats</td>
  </tr>
  <tr>
    <th class="smheading" align="center" width="30">{$LANG_SORT}</th>
    <th class="smheading" align="center" width="18"><a class="smheading" href="index.php?stats=players&amp;rank=num{$typelink}{$searchstring}">{$LANG_ID}</a></th>
    <th class="smheading" align="center"><a class="smheading" href="index.php?stats=players&amp;rank=name{$typelink}{$searchstring}">{$LANG_PLAYER}</a></th>
    <th class="smheading" align="center" width="35"><a class="smheading" href="index.php?stats=players&amp;rank=frags{$typelink}{$searchstring}">{$LANG_FRAGS}</a></th>
    <th class="smheading" align="center" width="37"><a class="smheading" href="index.php?stats=players&amp;rank=score{$typelink}{$searchstring}">{$LANG_SCORE}</a></th>
    <th class="smheading" align="center" width="32"><a class="smheading" href="index.php?stats=players&amp;rank=kills{$typelink}{$searchstring}">{$LANG_KILLS}</a></th>
    <th class="smheading" align="center" width="40"><a class="smheading" href="index.php?stats=players&amp;rank=deaths{$typelink}{$searchstring}">{$LANG_DEATHS}</a></th>
    <th class="smheading" align="center" width="55"><a class="smheading" href="index.php?stats=players&amp;rank=suicides{$typelink}{$searchstring}">{$LANG_SUICIDES}</a></th>
    <th class="smheading" align="center" width="40"><a class="smheading" href="index.php?stats=players&amp;rank=eff{$typelink}{$searchstring}">{$LANG_EFF}</a></th>
    <th class="smheading" align="center" width="40"><a class="smheading" href="index.php?stats=players&amp;rank=fph{$typelink}{$searchstring}">{$LANG_FPH}</a></th>
    <th class="smheading" align="center" width="40"><a class="smheading" href="index.php?stats=players&amp;rank=sph{$typelink}{$searchstring}">{$LANG_SPH}</a></th>
    <th class="smheading" align="center" width="55"><a class="smheading" href="index.php?stats=players&amp;rank=matches{$typelink}{$searchstring}">{$LANG_MATCHES}</a></th>
    <th class="smheading" style="white-space: nowrap" align="center" width="50"><a class="smheading" href="index.php?stats=players&amp;rank=wins{$typelink}{$searchstring}">{$LANG_PERCENTWINS}</a></th>
    <th class="smheading" align="center" width="40"><a class="smheading" href="index.php?stats=players&amp;rank=time{$typelink}{$searchstring}">{$LANG_HOURS}</a></th>
  </tr>

EOF;

$start = ($page * $playerspage) - $playerspage;
if ($searchid > 0) {
  if ($type == "bots")
    $where = "WHERE pnum=$searchid AND plr_bot=1";
  else
    $where = "WHERE pnum=$searchid AND plr_bot=0";
  $limit = "1";
}
else if ($searchname != "") {
  if ($type == "bots")
    $where = "WHERE plr_name LIKE '%{$slashedname}%' AND plr_bot=1";
  else if ($type == "all")
    $where = "WHERE plr_name LIKE '%{$slashedname}%'";
  else
    $where = "WHERE plr_name LIKE '%{$slashedname}%' AND plr_bot=0";
  $limit = "$start,$playerspage";
}
else {
  if ($type == "bots")
    $where = "WHERE plr_bot=1";
  else if ($type == "all")
    $where = "";
  else
    $where = "WHERE plr_bot=0";
  $limit = "$start,$playerspage";
}

$result = sql_queryn($link, "SELECT pnum,plr_name,plr_bot,plr_frags,plr_score,plr_kills,plr_deaths,plr_suicides,plr_matches,plr_time,plr_wins,plr_teamwins{$extra} FROM {$dbpre}players USE INDEX $index $where ORDER BY $order LIMIT $limit");
if (!$result) {
  echo "Player database error.<br>\n";
  exit;
}
$rank = $start + 1;
while($row = sql_fetch_array($result)) {
  while (list ($key, $val) = each ($row))
    ${$key} = $val;

  // $time = $dm_time + $tdm_time + $dd_time + $ctf_time + $br_time + $as_time + $ons_time + $mu_time + $in_time + $lm_time + $other_time;
  if ($plr_kills + $plr_deaths + $plr_suicides == 0)
    $eff = "0.0";
  else
    $eff = sprintf("%0.1f", ($plr_kills / ($plr_kills + $plr_deaths + $plr_suicides)) * 100.0);
  if ($plr_time == 0) {
    $fph = "0.0";
    $sph = "0.0";
  }
  else {
    $fph = sprintf("%0.1f", $plr_frags / ($plr_time / 360000.0));
    $sph = sprintf("%0.1f", $plr_score / ($plr_time / 360000.0));
  }
  $time = sprintf("%0.1f", $plr_time / 360000.0);
  if ($plr_bot)
    $nameclass = "darkbot";
  else
    $nameclass = "darkhuman";
  $plrname = stripspecialchars($plr_name);

  if (!$plr_matches)
    $winpercent = "0%";
  else
    $winpercent = strval(round((($plr_wins + $plr_teamwins) / $plr_matches) * 100))."%";

  echo <<<EOF
  <tr>
    <td class="dark" align="center">$rank</td>
    <td class="dark" align="center"><a class="$nameclass" href="playerstats.php?player=$pnum">$pnum</a></td>
    <td class="dark" align="center"><a class="$nameclass" href="playerstats.php?player=$pnum">$plrname</a></td>
    <td class="grey" align="center">$plr_frags</td>
    <td class="grey" align="center">$plr_score</td>
    <td class="grey" align="center">$plr_kills</td>
    <td class="grey" align="center">$plr_deaths</td>
    <td class="grey" align="center">$plr_suicides</td>
    <td class="grey" align="center">$eff%</td>
    <td class="grey" align="center">$fph</td>
    <td class="grey" align="center">$sph</td>
    <td class="grey" align="center">$plr_matches</td>
    <td class="grey" align="center">$winpercent</td>
    <td class="grey" align="center">$time</td>
  </tr>

EOF;
  $rank++;
}
sql_free_result($result);
sql_close($link);
echo "</table>\n";

?>