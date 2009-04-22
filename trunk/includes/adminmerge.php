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
//========== Merge Players/Servers Selection ==================================
//=============================================================================
function mergeentry($type) {
  if ($type == 1) {
    $typeu = "Player";
    $typel = "player";
  }
  else if ($type == 2) {
    $typeu = "Server";
    $typel = "server";
  }

  menu_top();

  echo <<<EOF
<p class="header">UTStatsDB $typeu Merge</p>
<form action="admin.php" method="post">
  <input type="hidden" name="MergeType" value="{$typeu}s" />
  <table>
    <tr>
      <td align="right" width="130" title="$typeu to copy from ($typel ID will be removed upon completion)."><b>Source $typeu:</b></td>
      <td align="left">
        <input type="text" name="srcmerge" value="" size="8" maxlength="8" />
      </td>
    </tr>
    <tr>
      <td align="right" title="$typeu to copy to ($typel ID will be kept with joined stats upon completion)."><b>Destination $typeu:</b></td>
      <td align="left">
        <input type="text" name="dstmerge" value="" size="8" maxlength="8" />
      </td>
    </tr>
    <tr>
      <td align="center" colspan="2">
      	<br />
        <input type="submit" name="Mode" value="Merge" class="formsb" />
      </td>
    </tr>
  </table>
</form>

EOF;

  menu_bottom();
}

//=============================================================================
//========== Merge Players/Servers Verify =====================================
//=============================================================================
function mergeverify() {
  global $dbpre;

  $mtype = check_post("MergeType");
  $src = check_post("srcmerge");
  $dst = check_post("dstmerge");

  if ($mtype == "" || $src == "" || $dst == "" || ($mtype != "Players" && $mtype != "Servers"))
    mainpage();

  menu_top();

  if (!is_numeric($src) || !is_numeric($dst))
  {
  	echo <<<EOF
<p><b>Invalid input - must be numeric.</b></p>

EOF;

    menu_bottom();
    exit;
  }
  
  $src = intval($src);
  $dst = intval($dst);

  if ($src == $dst) {
  	echo <<<EOF
<p><b>Source and destination are the same ($src)!</b></p>

EOF;

    menu_bottom();
    exit;
  }

  if ($mtype == "Players")
    $query = "SELECT plr_name,plr_bot FROM {$dbpre}players WHERE pnum=$src LIMIT 1";
  else
    $query = "SELECT sv_name,sv_shortname FROM {$dbpre}servers WHERE sv_num=$src LIMIT 1";

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
<p><b>Invalid source ID ($src).</b></p>

EOF;

    menu_bottom();
    sql_close($link);
    exit;
  }
  $src_name = $row[0];
  $src_extra = $row[1];

  if ($mtype == "Players")
    $query = "SELECT plr_name,plr_bot FROM {$dbpre}players WHERE pnum=$dst LIMIT 1";
  else
    $query = "SELECT sv_name,sv_shortname FROM {$dbpre}servers WHERE sv_num=$dst LIMIT 1";

  $result = sql_queryn($link, $query);
  if (!$result) {
  	echo "Database error - ".sql_error($link)."<br />\n";
    exit;
  }
  $row = sql_fetch_row($result);
  sql_free_result($result);
  sql_close($link);
  if (!$row) {
  	echo <<<EOF
<p><b>Invalid destination ID ($dst).</b></p>

EOF;

    menu_bottom();
    exit;
  }
  $dst_name = $row[0];
  $dst_extra = $row[1];

  if ($mtype == "Players") {
    if ($src_extra)
      $srcbot = " (bot)";
    else
      $srcbot = "";

    if ($dst_extra)
      $dstbot = " (bot)";
    else
      $dstbot = "";

    echo <<<EOF
<p><b>Source Player: [$src] $src_name{$srcbot}</b><br />
<b>Destination Player: [$dst] $dst_name{$dstbot}</b></p>

EOF;
  }
  else {
    echo <<<EOF
<p><b>Source Server: [$src] $src_name ($src_extra)</b><br />
<b>Destination Server: [$dst] $dst_name ($dst_extra)</b></p>

EOF;
  }

echo <<<EOF

<form action="admin.php" method="post">
  <input type="hidden" name="Mode" value="DoMerge" />
  <input type="hidden" name="MergeType" value="$mtype" />
  <input type="hidden" name="srcmerge" value="$src" />
  <input type="hidden" name="dstmerge" value="$dst" />
  <input type="submit" name="Default" value="Merge" />
  &nbsp;&nbsp;&nbsp;&nbsp;
  <input type="submit" name="Mode" value="Cancel" />
</form>

EOF;

  menu_bottom();
  exit;
}

//=============================================================================
//========== Merge Players/Servers Execute ====================================
//=============================================================================
function mergeexecute() {
  $mtype = check_post("MergeType");
  $src = check_post("srcmerge");
  $dst = check_post("dstmerge");

  if ($mtype == "" || $src == "" || $dst == "" || ($mtype != "Players" && $mtype != "Servers") || !is_numeric($src) || !is_numeric($dst))
    mainpage();

  $src = intval($src);
  $dst = intval($dst);

  menu_top();

  if ($mtype == "Players") {
    require("includes/plrmerge.php");
    player_merge($src, $dst);
  }
  else {
    require("includes/svrmerge.php");
    server_merge($src, $dst);
  }

  menu_bottom();
  exit;
}

?>