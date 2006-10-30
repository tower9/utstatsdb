<?php

/*
    UTStatsDB
    Copyright (C) 2002-2005  Patrick Contreras / Paul Gallier

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

function server_merge($src, $dst)
{
  global $dbpre;

  $link = sql_connect();

  // Lookup source server
  $result = sql_queryn($link, "SELECT * FROM {$dbpre}servers WHERE sv_num=$src LIMIT 1");
  if (!$result) {
    echo "Server database error!<br />\n";
    sql_close($link);
    return 0;
  }
  $row = sql_fetch_assoc($result);
  sql_free_result($result);
  if (!$row) {
    echo "Invalid source server ID #{$src}.<br />\n";
    sql_close($link);
    return 0;
  }
  while (list($key,$val) = each($row))
    ${"src_".$key} = $val;

  // Lookup destination server
  $result = sql_queryn($link, "SELECT * FROM {$dbpre}servers WHERE sv_num=$dst LIMIT 1");
  if (!$result) {
    echo "Server database error!<br />\n";
    sql_close($link);
    return 0;
  }
  $row2 = sql_fetch_assoc($result);
  sql_free_result($result);
  if (!$row2) {
    echo "Invalid destination server ID #{$dst}.<br />\n";
    sql_close($link);
    return 0;
  }
  while (list($key,$val) = each($row2))
    ${"dst_".$key} = $val;

  echo <<<EOF
<b>Merging servers:</b><br />
<br />
<b>Source:</b> [$src] $src_sv_name<br />
<b>Destination:</b> [$dst] $dst_sv_name<br />
<br />

EOF;

  // Update last match date/time
  if (strtotime($src_sv_lastmatch) > strtotime($dst_sv_lastmatch))
    $lastmatch = $src_sv_lastmatch;
  else
    $lastmatch = $dst_sv_lastmatch;

  echo "Server stats....";
  $query = "UPDATE {$dbpre}servers SET sv_matches=sv_matches+$src_sv_matches,sv_frags=sv_frags+$src_sv_frags,sv_score=sv_score+$src_sv_score,sv_time=sv_time+$src_sv_time,sv_lastmatch='$lastmatch' WHERE sv_num=$dst";
  $result = sql_queryn($link, $query);
  if (!$result) {
    echo "Error updating server data!<br />\n";
    sql_close($link);
    return 0;
  }
  $result = sql_queryn($link, "DELETE FROM {$dbpre}servers WHERE sv_num=$src LIMIT 1");
  echo "done.<br />";

  echo "Match stats....";
  $result = sql_queryn($link, "UPDATE {$dbpre}matches SET gm_server=$dst WHERE gm_server=$src");
  echo "done.<br />";

  sql_close($link);

  echo "<br /><b>Server merge complete.</b><br />";

  return 1;
}

?>