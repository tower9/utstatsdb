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

//=============================================================================
//========== Track Player Selection ===========================================
//=============================================================================
function trackplayer() {
  menu_top();

  echo <<<EOF
<p class="header">UTStatsDB Player Tracker</p>
<form action="admin.php" method="post">
  <table>
    <tr>
      <td align="right" width="130" title="Player ID to track."><b>Player ID:</b></td>
      <td align="left">
        <input type="text" name="player" value="" size="8" maxlength="8" />
      </td>
    </tr>
    <tr>
      <td align="center" colspan="2">
      	<br />
        <input type="submit" name="Mode" value="Track" class="formsb" />
      </td>
    </tr>
  </table>
</form>

EOF;

  menu_bottom();
}

//=============================================================================
//========== Track Player Display =============================================
//=============================================================================
function trackplayerdisplay() {
  global $dbpre;

  $plr = check_post("player");

  if ($plr == "")
    mainpage();

  menu_top();

  if (!is_numeric($plr))
  {
  	echo <<<EOF
<p><b>Invalid input - must be numeric.</b></p>

EOF;

    menu_bottom();
    exit;
  }
  
  $plr = intval($plr);
  $query = "SELECT plr_name,plr_bot,plr_ip FROM {$dbpre}players WHERE pnum=$plr LIMIT 1";
  $link = sql_connect();
  $result = sql_queryn($link, $query);
  if (!$result) {
  	echo "Database error - ".sql_error($link)."<br />\n";
    exit;
  }
  $row = sql_fetch_row($result);
  sql_free_result($result);
  if (!$row) {
  	echo <<<EOF
<p><b>Invalid player ID ($plr).</b></p>

EOF;

    menu_bottom();
    sql_close($link);
    exit;
  }
  $plr_name = $row[0];
  $plr_bot = intval($row[1]);
  $plr_ip = $row[2];

  if ($plr_bot)
    $plrbot = " (bot)";
  else
    $plrbot = "";

  if ($plr_ip != "")
    $ip = $plr_ip;
  else
    $ip = "&lt;not logged&gt;";

  echo <<<EOF
<table>
  <tr>
    <td align="right"><b>Player:</b></td>
    <td>[$plr] $plr_name{$plrbot}</td>
  </tr>
  <tr>
    <td align="right"><b>Most recent IP address:</b></td>
    <td>$ip</td>
  </tr>
</table>
<br />
<table>
  <tr>
    <th width="230" align="left">CD Keys Used</th>
    <th width="125" colspan="2" align="left">Other Players</th>
    <th align="left">Recent IP Address</th>
  </tr>

EOF;

  $query = "SELECT al_key FROM {$dbpre}aliases WHERE al_pnum=$plr";
  $result = sql_querynb($link, $query);
  if (!$result) {
    echo "Database error - ".sql_error($link)."<br />\n";
    exit;
  }
  while ($row = sql_fetch_row($result)) {
    $key = $row[0];
    $query = "SELECT al_pnum FROM {$dbpre}aliases WHERE al_key='$key'";
    $result2 = sql_querynb($link, $query);
    if (!$result2) {
      echo "Database error - ".sql_error($link)."<br />\n";
      exit;
    }
    $numrows = sql_num_rows($result2);

    if ($numrows < 2) {
      echo <<<EOF
  <tr>
    <td>$key</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>

EOF;
    }

    $listed = 0;
    while ($row = sql_fetch_row($result2)) {
      $plr2 = intval($row[0]);
      $query = "SELECT plr_name,plr_ip FROM {$dbpre}players WHERE pnum=$plr2";
      $result3 = sql_querynb($link, $query);
      if (!$result3) {
        echo "Database error - ".sql_error($link)."<br />\n";
        exit;
      }
      while ($row = sql_fetch_row($result3)) {
        $p2_name = $row[0];
        $p2_ip = $row[1];

        if ($listed)
          $keyl = "&nbsp;";
        else
          $keyl = $key;

        if ($plr_name != $p2_name) {
          echo <<<EOF
  <tr>
    <td>$keyl</td>
    <td width="10" align="right">[$plr2]</td>
    <td>$p2_name</td>
    <td>$p2_ip</td>
  </tr>

EOF;
          $listed++;
        }
      }
      sql_free_result($result3);
    }
    sql_free_result($result2);
    echo "</table>\n";
  }
  sql_free_result($result);

  menu_bottom();
  sql_close($link);
  exit;
}

?>