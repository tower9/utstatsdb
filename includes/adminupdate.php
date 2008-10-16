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

function update305()
{
  global $dbtype, $dbpre, $break;

  $link = sql_connect();

  echo "Updating {$dbpre}config...<br />\n";
  $result = sql_queryn($link, "INSERT INTO {$dbpre}config (conf,type,value,name,descr) VALUES('AutoParse','b2|Disabled|Enabled','0','Auto-parse','Set to true to have OLSendLog automatically parse after receiving a new log.')");
  if (!$result) {
    echo "<br />Error updating config table.{$break}\n";
    exit;
  }
  $result = sql_queryn($link, "UPDATE {$dbpre}config SET descr='Use server short name instead of full name.' WHERE conf='useshortname'");
  if (!$result) {
    echo "<br />Error updating config table.{$break}\n";
    exit;
  }

  echo "Updating {$dbpre}gplayers...<br />\n";
  if (strtolower($dbtype) == "sqlite")
    $result = sqlite_alter_table($link, "{$dbpre}gplayers", "ADD gp_packetloss int(10) NOT NULL default 0");
  else
    $result = sql_queryn($link, "ALTER TABLE {$dbpre}gplayers ADD gp_packetloss int(10) unsigned NOT NULL default 0 AFTER gp_ping");
  if (!$result) {
    echo "<br />Error updating gplayers table.{$break}\n";
    exit;
  }

  echo "Updating version....<br />\n";
  $result = sql_queryn($link, "UPDATE {$dbpre}config SET value='3.05' WHERE conf='Version'");
  if (!$result) {
    echo "<br />Error updating version.{$break}\n";
    exit;
  }

  sql_close($link);
  echo "<br />Database updates complete.<br />\n";
}

function update304()
{
  global $dbtype, $dbpre, $break;

  $link = sql_connect();

  echo "Updating {$dbpre}weapons...<br />\n";
  if (strtolower($dbtype) == "mysql")
  {
    $result = sql_queryn($link, "ALTER TABLE {$dbpre}weapons MODIFY wp_type varchar(60) NOT NULL default '', MODIFY wp_desc varchar(40) NOT NULL default ''");
    if (!$result) {
      echo "<br />Error updating weapons table.{$break}\n";
      exit;
    }
  }

  echo "Updating weapon descriptions....<br />\n";
  $fname = "tables/".strtolower($dbtype)."/weapons.sql";
  if (file_exists($fname)) {
    $sqldata = file($fname);

    while($row = each($sqldata)) {
      $line = trim($row[1], "\t\n\r\0;");
      $line = str_replace("\n", "", $line);

      $ltype = 0;
      if (strlen($line) > 59 && substr($line, 0, 1) != "#" && strstr($line, "INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES(") != FALSE)
        $ltype = 1;
      else if (strlen($line) > 73 && substr($line, 0, 1) != "#" && strstr($line, "INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES(") != FALSE)
        $ltype = 2;
      else if (strlen($line) > 74 && substr($line, 0, 1) != "#" && strstr($line, "INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES(") != FALSE)
        $ltype = 3;
      else if (strlen($line) > 88 && substr($line, 0, 1) != "#" && strstr($line, "INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES(") != FALSE)
        $ltype = 4;

      if ($ltype > 0) {
        switch($ltype) {
          case 1: $linex = substr($line, 52); break;
          case 2: $linex = substr($line, 64); break;
          case 3: $linex = substr($line, 65); break;
          case 4: $linex = substr($line, 77); break;
        }

        $linex = rtrim($linex, ")");
        $lines = explode(',', $linex);

        for ($i = 0; isset($lines[$i]); $i++) {
          $lines[$i] = ltrim($lines[$i], "'");
          $lines[$i] = rtrim($lines[$i], "'");
        }

        $result = sql_querynb($link, "SELECT COUNT(*) FROM {$dbpre}weapons WHERE wp_type='{$lines[0]}'");
        if (sql_num_rows($result) > 0) {
          sql_free_result($result);
          $qstring = "";
          switch($ltype) {
            case 1: $qstring = "UPDATE {$dbpre}weapons SET wp_desc='{$lines[1]}' WHERE wp_type='{$lines[0]}'"; break;
            case 2: $qstring = "UPDATE {$dbpre}weapons SET wp_desc='{$lines[1]}',wp_weaptype='{$lines[2]}' WHERE wp_type='{$lines[0]}'"; break;
            case 3: $qstring = "UPDATE {$dbpre}weapons SET wp_desc='{$lines[1]}',wp_secondary='{$lines[2]}' WHERE wp_type='{$lines[0]}'"; break;
            case 4: $qstring = "UPDATE {$dbpre}weapons SET wp_desc='{$lines[1]}',wp_weaptype='{$lines[2]}',wp_secondary='{$lines[3]}' WHERE wp_type='{$lines[0]}'"; break;
          }
          sql_querynb($link, $qstring);
        }
        else {
          sql_free_result($result);
          $line = str_replace("%dbpre%", "$dbpre", $line);
          sql_querynb($link, $line);
        }
      }
    }
  }

  echo "Updating item descriptions....<br />\n";
  $fname = "tables/".strtolower($dbtype)."/items.sql";
  if (file_exists($fname)) {
    $sqldata = file($fname);

    while($row = each($sqldata)) {
      $line = trim($row[1], "\t\n\r\0;");
      $line = str_replace("\n", "", $line);

      if (strlen($line) > 57 && substr($line, 0, 1) != "#" && strstr($line, "INSERT INTO %dbpre%items (it_type,it_desc) VALUES(") != FALSE) {
        $linex = substr($line, 50);
        $linex = rtrim($linex, ")");
        $lines = explode(',', $linex);

        for ($i = 0; isset($lines[$i]); $i++) {
          $lines[$i] = ltrim($lines[$i], "'");
          $lines[$i] = rtrim($lines[$i], "'");
        }

        $result = sql_querynb($link, "SELECT COUNT(*) FROM {$dbpre}items WHERE it_type='{$lines[0]}'");
        if (sql_num_rows($result) > 0) {
          sql_free_result($result);
          sql_querynb($link, "UPDATE {$dbpre}items SET it_desc='{$lines[1]}' WHERE it_type='{$lines[0]}'");
        }
        else {
          sql_free_result($result);
          $line = str_replace("%dbpre%", "$dbpre", $line);
          sql_querynb($link, $line);
        }
      }
    }
  }

  echo "Updating version....<br />\n";
  $result = sql_queryn($link, "UPDATE {$dbpre}config SET value='3.04' WHERE conf='Version'");
  if (!$result) {
    echo "<br />Error updating version.{$break}\n";
    exit;
  }

  sql_close($link);
  echo "<br />Database updates complete.<br />\n";
}

function update303()
{
  global $dbtype, $dbpre, $break;

  $link = sql_connect();

  echo "Updating {$dbpre}config....<br />\n";
  $result = sql_queryn($link, "SELECT num FROM {$dbpre}config WHERE conf='php_timelimit'");
  if (!$result) {
  	echo "<br />Config database error!<br />\n";
    exit;
  }
  $row = sql_fetch_row($result);
  if (!$row) {
  	echo "<br />Config database error!<br />\n";
    exit;
  }
  $num = $row[0];
  sql_free_result($result);

  if (strtolower($dbtype) == "sqlite") {
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='ut99weapons'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='criticalfix'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='bothighs'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='invasiontotals'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='serverlist'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='allowswitches'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='fullvehiclestats'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='plistall'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='showbots'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='mapsearch'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='playersearch'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='usestatsname'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='ignorelogtype'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='skipinsession'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='discardscoreless'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='allowincomplete'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='savesingle'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='rankbots'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='ranksystem'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='useshortname'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='minranktime'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='minrankmatches'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='minchtime'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='minchmatches'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='dateformat'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='layout'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='navbar'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='matchespage'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='mapspage'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='serverspage'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='playerspage'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='demoext'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='demodir'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='rpgini'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='lockname'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='maxmatches'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE conf='php_timelimit'");
  }
  else
    $result = sql_queryn($link, "UPDATE {$dbpre}config SET num=num+1 WHERE num>=$num ORDER BY num DESC");
  if (!$result) {
    echo "<br />Error updating config table.{$break}\n";
    exit;
  }

  $result = sql_queryn($link, "INSERT INTO {$dbpre}config (num,conf,type,value,name,descr) VALUES($num,'lang','s2','EN','Language','Language (current translations available: EN, DE)')");
  if (!$result) {
    echo "<br />Error updating config table.{$break}\n";
    exit;
  }

  echo "Updating {$dbpre}configlogs...<br />\n";
  if (strtolower($dbtype) == "sqlite")
    $result = sql_queryn($link, "ALTER TABLE {$dbpre}configlogs ADD COLUMN chatprefix varchar(60) NOT NULL default '', ADD COLUMN chatrequire tinyint(1) NOT NULL default 0");
  else
    $result = sql_queryn($link, "ALTER TABLE {$dbpre}configlogs ADD chatprefix varchar(60) NOT NULL default '' AFTER prefix, ADD COLUMN chatrequire tinyint(1) NOT NULL default 0 AFTER chatprefix");
  if (!$result) {
    echo "<br />Error updating configlogs table.{$break}\n";
    exit;
  }

  echo "Updating {$dbpre}matches...<br />\n";
  if (strtolower($dbtype) == "sqlite")
    $result = sql_queryn($link, "ALTER TABLE {$dbpre}matches ADD COLUMN gm_timeoffset float NOT NULL default 100");
  else
    $result = sql_queryn($link, "ALTER TABLE {$dbpre}matches ADD gm_timeoffset float unsigned NOT NULL default 100 AFTER gm_logname");
  if (!$result) {
    echo "<br />Error updating matches table.{$break}\n";
    exit;
  }

  $result = sql_queryn($link, "UPDATE {$dbpre}matches SET gm_timeoffset=110");
  if (!$result) {
    echo "<br />Error updating matches table.{$break}\n";
    exit;
  }

  // Try to detect UT '99 matches based on server version
  $result = sql_queryn($link, "UPDATE {$dbpre}matches SET gm_timeoffset=118.25 WHERE gm_serverversion=436 OR gm_serverversion=451");
  if (!$result) {
    echo "<br />Error updating matches table.{$break}\n";
    exit;
  }

  echo "Updating version....<br />\n";
  $result = sql_queryn($link, "UPDATE {$dbpre}config SET value='3.03' WHERE conf='Version'");
  if (!$result) {
    echo "<br />Error updating version.{$break}\n";
    exit;
  }

  sql_close($link);
  echo "<br />Database updates complete.<br />\n";
}

function update302()
{
  global $dbtype, $dbpre, $break;

  $link = sql_connect();

  echo "Updating {$dbpre}matches...<br />\n";
  if (strtolower($dbtype) == "sqlite")
    $result = sql_queryn($link, "ALTER TABLE {$dbpre}matches ADD COLUMN gm_init datetime NOT NULL default '0000-00-00 00:00:00'");
  else
    $result = sql_queryn($link, "ALTER TABLE {$dbpre}matches ADD gm_init datetime NOT NULL default '0000-00-00 00:00:00' AFTER gm_uttype");
  if (!$result) {
    echo "<br />Error updating matches table.{$break}\n";
    exit;
  }

  echo "Updating match start dates....<br />\n";
  if (strtolower($dbtype) == "sqlite")
    $result = sql_queryn($link, "UPDATE {$dbpre}matches SET gm_init=gm_start,gm_start=gm_init+(gm_starttime DIV 110)"); // 118.25 for UT '99
  else
    $result = sql_queryn($link, "UPDATE {$dbpre}matches SET gm_init=gm_start,gm_start=ADDTIME(gm_init, SEC_TO_TIME(gm_starttime DIV 110))"); // 118.25 for UT '99
  if (!$result) {
    echo "<br />Error updating matches table.{$break}\n";
    exit;
  }

  echo "Updating map last match dates....<br />\n";
  $result = sql_queryn($link, "UPDATE {$dbpre}maps SET mp_lastmatch=(SELECT gm_start FROM {$dbpre}matches WHERE gm_map=mp_num ORDER BY gm_init DESC LIMIT 1)");
  if (!$result) {
    echo "<br />Error updating map table.{$break}\n";
    exit;
  }

  echo "Updating server last match dates....<br />\n";
  $result = sql_queryn($link, "UPDATE {$dbpre}servers SET sv_lastmatch=(SELECT gm_start FROM {$dbpre}matches WHERE gm_server=sv_num ORDER BY gm_init DESC LIMIT 1)");
  if (!$result) {
    echo "<br />Error updating server table.{$break}\n";
    exit;
  }

  echo "Updating version....<br />\n";
  $result = sql_queryn($link, "UPDATE {$dbpre}config SET value='3.02' WHERE conf='Version'");
  if (!$result) {
    echo "<br />Error updating version.{$break}\n";
    exit;
  }

  sql_close($link);
  echo "<br />Database updates complete.<br />\n";
}

function update301()
{
  global $dbtype, $dbpre, $break;

  $link = sql_connect();
  echo "Updating {$dbpre}config....<br />\n";
  $result = sql_queryn($link, "SELECT num FROM {$dbpre}config WHERE conf='playerspage'");
  if (!$result) {
  	echo "<br />Config database error!<br />\n";
    exit;
  }
  $row = sql_fetch_row($result);
  if (!$row) {
  	echo "<br />Config database error!<br />\n";
    exit;
  }
  $num = $row[0];
  sql_free_result($result);

  if (strtolower($dbtype) == "sqlite") {
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE conf='ut99weapons'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE conf='criticalfix'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE conf='bothighs'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE conf='invasiontotals'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE conf='serverlist'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE conf='allowswitches'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE conf='fullvehiclestats'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE conf='plistall'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE conf='showbots'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE conf='mapsearch'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE conf='playersearch'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE conf='usestatsname'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE conf='ignorelogtype'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE conf='skipinsession'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE conf='discardscoreless'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE conf='allowincomplete'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE conf='savesingle'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE conf='rankbots'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE conf='ranksystem'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE conf='useshortname'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE conf='minranktime'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE conf='minrankmatches'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE conf='minchtime'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE conf='minchmatches'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE conf='dateformat'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE conf='layout'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE conf='navbar'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE conf='matchespage'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE conf='mapspage'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE conf='serverspage'");
    sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE conf='playerspage'");
  }
  else
    $result = sql_queryn($link, "UPDATE {$dbpre}config SET num=num+2 WHERE num>=$num ORDER BY num DESC");
  if (!$result) {
    echo "<br />Error updating config table.{$break}\n";
    exit;
  }

  $result = sql_queryn($link, "INSERT INTO {$dbpre}config (num,conf,type,value,name,descr) VALUES($num,'demodir','s200','','Demorec Path','Path to locate or store demorecs into.')");
  if (!$result) {
    echo "<br />Error updating config table.{$break}\n";
    exit;
  }
  $num++;
  $result = sql_queryn($link, "INSERT INTO {$dbpre}config (num,conf,type,value,name,descr) VALUES($num,'demoext','s10','','Demorec Extension','Extension of demorec files.')");
  if (!$result) {
    echo "<br />Error updating config table.{$break}\n";
    exit;
  }

  echo "Updating {$dbpre}configlogs....<br />\n";
  if (strtolower($dbtype) == "sqlite") {
    $result = sql_queryn($link, "ALTER TABLE {$dbpre}configlogs ADD COLUMN demoftppath varchar(200) NOT NULL default ''");
    if (!$result) {
      echo "<br />Error updating configlogs table.{$break}\n";
      exit;
    }
    $result = sql_queryn($link, "ALTER TABLE {$dbpre}configlogs ADD COLUMN multicheck tinyint(1) NOT NULL default 0");
  }
  else {
    $result = sql_queryn($link, "ALTER TABLE {$dbpre}configlogs ADD demoftppath varchar(200) NOT NULL default '' AFTER defteam");
    if (!$result) {
      echo "<br />Error updating configlogs table.{$break}\n";
      exit;
    }
    $result = sql_queryn($link, "ALTER TABLE {$dbpre}configlogs ADD multicheck tinyint(1) NOT NULL default 0 AFTER demoftppath");
  }
  if (!$result) {
    echo "<br />Error updating configlogs table.{$break}\n";
    exit;
  }

  echo "Updating version....<br />\n";
  $result = sql_queryn($link, "UPDATE {$dbpre}config SET value='3.01' WHERE conf='Version'");
  if (!$result) {
    echo "<br />Error updating version.{$break}\n";
    exit;
  }

  sql_close($link);
  echo "<br />Database updates complete.<br />\n";
}

?>