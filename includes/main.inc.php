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

require("statsdb.inc.php"); // Set to the location of your account settings file
require("logsql.php");
$magicrt = get_magic_quotes_runtime();
load_config();
require("language/lang_{$lang}.php");

function check_get(&$store, $val)
{
  $magic = get_magic_quotes_gpc();
  if (isset($_POST["$val"])) {
    if ($magic)
      $store = stripslashes($_POST["$val"]);
    else
      $store = $_POST["$val"];
  }
  else if (isset($_GET["$val"])) {
    if ($magic)
      $store = stripslashes($_GET["$val"]);
    else
      $store = $_GET["$val"];
  }
}

function dtime($tm) // Convert Time Format
{
  $t = intval(round($tm / 100));
  $t1 = intval(floor($t / 3600));
  $t2 = intval(floor(($t - ($t1 * 3600)) / 60));
  $t3 = intval(floor($t - ($t1 * 3600) - ($t2 * 60)));
  if ($t1)
    $time = sprintf("%d:%02d:%02d", $t1, $t2, $t3);
  else
    $time = sprintf("%d:%02d", $t2, $t3);
  return $time;
}

function stripspecialchars($str)
{
  $nstr = htmlspecialchars(ereg_replace("\x1b...", "", $str));
  return $nstr;
}

function load_config()
{
  global $dbpre, $menulinks, $menu_url, $menu_descr, $magicrt;

  $result = sql_query("SELECT conf,value FROM {$dbpre}config");
  if (!$result) {
    echo "Error loading configuration.<br />\n";
    exit;
  }
  while ($row = sql_fetch_row($result))
  {
    global ${$row[0]};
    ${$row[0]} = $row[1];
  }
  sql_free_result($result);

  $result = sql_query("SELECT title_msg FROM {$dbpre}configset LIMIT 1");
  if (!$result) {
    echo "Error loading configuration.<br />\n";
    exit;
  }
  $row = sql_fetch_row($result);
  global $title_msg;
  $title_msg = $magicrt ? stripslashes($row[0]) : $row[0];
  sql_free_result($result);

  $result = sql_query("SELECT url,descr FROM {$dbpre}configmenu");
  if (!$result) {
    echo "Error loading configuration.<br />\n";
    exit;
  }
  $menulinks = 0;
  while ($row = sql_fetch_row($result))
  {
    if (strlen($row[0]) && strlen($row[1])) {
      $menu_url[$menulinks] = htmlspecialchars($magicrt ? stripslashes($row[0]) : $row[0]);
      $menu_descr[$menulinks++] = htmlspecialchars($magicrt ? stripslashes($row[1]) : $row[1]);
    }
  }
  sql_free_result($result);

  if (!isset($lang) || strlen($lang) != 2) {
    global $lang;
    $lang = "en";
  }
  else
    $lang = strtolower($lang);
}

function formatdate($dt, $tm)
{
  global $dateformat;

  if ($tm) {
    switch ($dateformat) {
      case 1: return date('D, M d Y \a\t G:i:s', $dt); break;
      case 2: return date('D, d. M Y \a\t G:i:s', $dt); break;
      default: return date('D, M d Y \a\t g:i:s A', $dt);
    }
  }
  else {
    switch ($dateformat) {
      case 1: return date('D, M d Y', $dt); break;
      case 2: return date('D, d. M Y', $dt); break;
      default: return date('D, M d Y', $dt);
    }
  }
}

function unhtmlspecialchars($string)
{
  $string = str_replace('&amp;', '&', $string);
  $string = str_replace('&quot;', '"', $string);
  $string = str_replace('&#039;', '\'', $string);
  $string = str_replace('&lt;', '<', $string);
  $string = str_replace('&gt;', '>', $string);
  return $string;
}

$twidth = 720;
$twidthm = $twidth + 160;

if (!isset($layout) || !$layout) {
  echo "Configuration error.<br />\n";
  exit;
}
$stylefile = "style{$layout}.css";
$logofile = "utstatsdblogo.png";
$utlogofile = "utlogo.png";

// Team colors
$teamcolor = array("Red","Blue","Green","Gold");
$teamcolorbar = array(2,1,3,4);
$teamclass = array("redteam","blueteam","greenteam","goldteam");
$teamchat = array("chatred","chatblue","chatgreen","chatgold");
$teamscore = array("redteamscore","blueteamscore","greenteamscore","goldteamscore");

echo <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <title>$title</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <link rel="icon" href="resource/uicon.png" type="image/png" />
  <link rel="stylesheet" href="resource/{$stylefile}" type="text/css" />
  <script language="JavaScript" type="text/JavaScript">
<!--
    function changePage(newLoc) {
      nextPage = "index.php?stats=players&type=" + newLoc.options[newLoc.selectedIndex].value
      if (nextPage != "")
        document.location.href = nextPage
    }
-->
  </script>
</head>

EOF;

if (!$navbar) {
  // =========== Side Menu Bar ===========
  echo <<<EOF
<body style="background: url(resource/sidebar{$layout}.gif); background-repeat: repeat-y">

<table cellpadding="0" cellspacing="0" border="0"><tr>
<td width="159" class="sidebar" valign="top">
  <a href="http://www.unrealtournament.com"><img src="resource/{$utlogofile}" border="0" alt="Unreal Tournament Logo" /></a>
  <br />
  <p><a class="sidebar" href="index.php">{$LANG_INC_MAIN}</a></p>
  <p><a class="sidebar" href="index.php?stats=matches">{$LANG_INC_MATCHES}</a></p>
  <p><a class="sidebar" href="index.php?stats=players">{$LANG_INC_PLAYERS}</a></p>

EOF;

  if (isset($ranksystem) && $ranksystem) {
    echo <<<EOF
  <p><a class="sidebar" href="rankings.php">{$LANG_INC_RANKINGS}</a></p>

EOF;
  }

  echo <<<EOF
  <p><a class="sidebar" href="index.php?stats=maps">{$LANG_INC_MAPS}</a></p>

EOF;

  if ($serverlist) {
    echo <<<EOF
  <p><a class="sidebar" href="index.php?stats=servers">{$LANG_INC_SERVERS}</a></p>

EOF;
  }

  echo <<<EOF
  <p><a class="sidebar" href="totals.php">{$LANG_INC_TOTALS}</a></p>
  <p><a class="sidebar" href="careerhighs.php">{$LANG_INC_CAREERHIGHS}</a></p>
  <p><a class="sidebar" href="matchhighs.php">{$LANG_INC_MATCHHIGHS}</a></p>
  <p><a class="sidebar" href="index.php?stats=help">{$LANG_INC_HELP}</a></p>

EOF;

  for ($i = 0; $i < $menulinks; $i++) {
    if ($i == 0)
      echo "  <font size=\"1\"><br /></font>\n";
    echo "  <p>&nbsp;<a class=\"sidebar\" href=\"{$menu_url[$i]}\">{$menu_descr[$i]}</a></p>\n";
  }

  echo <<<EOF
</td>
<td width="$twidth" valign="top" align="center">

<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
  <tr>
    <td align="center">
      <a href="http://www.utstatsdb.com"><img src="resource/{$logofile}" border="0" alt="UTStatsDB Logo" /></a>
    </td>
  </tr>
</table>
<font size="1"><br /></font>

EOF;
}
else {
  // =========== Top Menu Bar ===========
  echo <<<EOF
<body>

<table cellpadding="0" cellspacing="0" border="0" align="center">
  <tr>
    <td align="center">
      <a href="http://www.unrealtournament.com"><img src="resource/{$utlogofile}" border="0" alt="Unreal Tournament Logo" /></a>
    </td>
    <td align="center">
      <a href="http://www.utstatsdb.com"><img src="resource/{$logofile}" border="0" alt="UTStatsDB Logo" /></a>
    </td>
  </tr>
  <tr><td>&nbsp;</td></tr>
</table>

<table cellpadding="0" cellspacing="0" border="0" align="center">
<tr>
<td class="topbar" valign="top" align="center">
  <a class="topbar" href="index.php">{$LANG_INC_MAIN}</a>
  &nbsp;· &nbsp;<a class="topbar" href="index.php?stats=matches">{$LANG_INC_MATCHES}</a>
  &nbsp;· &nbsp;<a class="topbar" href="index.php?stats=players">{$LANG_INC_PLAYERS}</a>

EOF;

  if (isset($ranksystem) && $ranksystem)
    echo "&nbsp;· &nbsp;<a class=\"topbar\" href=\"rankings.php\">{$LANG_INC_RANKINGS}</a>\n";

  echo "&nbsp;· &nbsp;<a class=\"topbar\" href=\"index.php?stats=maps\">{$LANG_INC_MAPS}</a>\n";

  if ($serverlist)
    echo "&nbsp;· &nbsp;<a class=\"topbar\" href=\"index.php?stats=servers\">{$LANG_INC_SERVERS}</a>\n";

  echo "&nbsp;· &nbsp;<a class=\"topbar\" href=\"totals.php\">{$LANG_INC_TOTALS}</a>\n";

  if (isset($ranksystem) && $ranksystem && $serverlist)
    echo "<br />\n";
  else
    echo "&nbsp;· &nbsp;";

  echo <<<EOF
  <a class="topbar" href="careerhighs.php">{$LANG_INC_CAREERHIGHS}</a>
  &nbsp;· &nbsp;<a class="topbar" href="matchhighs.php">{$LANG_INC_MATCHHIGHS}</a>
  &nbsp;· &nbsp;<a class="topbar" href="index.php?stats=help">{$LANG_INC_HELP}</a>

EOF;

  if ($menulinks)
    echo "<br />\n";
  for ($i = 0; $i < $menulinks; $i++) {
    if ($i == 0)
      echo "  <font size=\"1\"><br /></font>\n";
    echo "  &nbsp;<a class=\"topbar\" href=\"{$menu_url[$i]}\">{$menu_descr[$i]}</a>\n";
  }

  echo <<<EOF
</td>
</tr>
<tr><td><font size="1">&nbsp;</font></td></tr>
<tr>
<td width="$twidth" valign="top" align="center">

<font size="1"><br /></font>

EOF;
}

?>