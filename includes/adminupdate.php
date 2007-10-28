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
    $result = sql_queryn($link, "UPDATE {$dbpre}matches SET gm_init=gm_start,gm_start=gm_init+(gm_starttime DIV 110)");
  else
    $result = sql_queryn($link, "UPDATE {$dbpre}matches SET gm_init=gm_start,gm_start=ADDTIME(gm_init, SEC_TO_TIME(gm_starttime DIV 110))");
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