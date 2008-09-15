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

if (preg_match("/admin.php/i", $_SERVER["PHP_SELF"])) {
  $AdminMenu = 1;
  global $dbtype, $SQLhost, $SQLdb, $SQLus, $SQLpw, $config, $conflogs, $nohtml, $break;
  global $link, $dbtype, $dbpre, $mysqlverh, $mysqlverl, $server, $player, $match;
  global $events, $pickups, $gkills, $gscores, $tkills, $chatlog;
  global $spree, $multi, $tchange, $uselimit, $assist, $relog, $pwastats;
  global $stattype, $flagstatus, $killmatch, $mutantstat, $logname, $matchnum;
}
else {
  $AdminMenu = 0;

  require("includes/statsdb.inc.php");
  require("includes/logsql.php");

  function check_get($val)
  {
    global $magic;

    if (isset($_GET["$val"])) {
      if ($magic)
        $store = stripslashes($_GET["$val"]);
      else
        $store = $_GET["$val"];
      if ($store)
        return $store;
      return 1;
    }
    return 0;
  }
}

$magic = get_magic_quotes_gpc();
$magicrt = get_magic_quotes_runtime();

function loadconfig()
{
  global $dbpre, $magicrt, $config, $conflogs;

  $link = sql_connect();
  $result = sql_querynb($link, "SELECT conf,value FROM {$dbpre}config");
  if ($result && sql_num_rows($result)) {
    while ($row = sql_fetch_row($result))
      $config["{$row[0]}"] = $magicrt ? stripslashes($row[1]) : $row[1];
    sql_free_result($result);
  }
  else {
    echo "Error loading configuration.\n";
  	exit;
  }

  $conflogs = array(array());
  $result = sql_querynb($link, "SELECT logpath,backuppath,prefix,chatprefix,noport,ftpserver,ftppath,passive,alllogs,ftpuser,ftppass,deftype,defteam,demoftppath,multicheck FROM {$dbpre}configlogs ORDER BY num");
  if ($result && sql_num_rows($result)) {
    $num = 0;
    while ($row = sql_fetch_row($result)) {
      $num++;
      $conflogs["logpath"][$num] = $magicrt ? stripslashes($row[0]) : $row[0];
      $conflogs["backuppath"][$num] = $magicrt ? stripslashes($row[1]) : $row[1];
      $conflogs["logprefix"][$num] = $magicrt ? stripslashes($row[2]) : $row[2];
      $conflogs["chatprefix"][$num] = $magicrt ? stripslashes($row[3]) : $row[3];
      $conflogs["noport"][$num] = intval($row[4]);
      $conflogs["ftpserver"][$num] = $magicrt ? stripslashes($row[5]) : $row[5];
      $conflogs["ftppath"][$num] = $magicrt ? stripslashes($row[6]) : $row[6];
      $conflogs["ftppassive"][$num] = intval($row[7]);
      $conflogs["alllogs"][$num] = intval($row[8]);
      $conflogs["ftpuser"][$num] = $magicrt ? stripslashes($row[9]) : $row[9];
      $conflogs["ftppass"][$num] = $magicrt ? stripslashes($row[10]) : $row[10];
      $conflogs["deftype"][$num] = intval($row[11]);
      $conflogs["defteam"][$num] = intval($row[12]);
      $conflogs["demoftppath"][$num] = $magicrt ? stripslashes($row[13]) : $row[13];
      $conflogs["multicheck"][$num] = intval($row[14]);
    }
    sql_free_result($result);
  }
  else {
    echo "Error loading log configuration.\n";
  	exit;
  }

  sql_close($link);
}

function checkfile($pre, $noport, $file, &$fdate)
{
  global $files;

  $stattype = 0;
  $prelen = strlen($pre);
  if (strstr($file, $pre) && (substr($file, -4) == ".log" || substr($file, -4) == ".txt")) {
    $i = strpos($file, $pre);
    $file = substr($file, $i + strlen($pre));

	if (strlen($file) == 19 && is_numeric(substr($file, 0, 8)) && substr($file, 8, 1) == "." && is_numeric(substr($file, 9, 6))) // UT3 Log
	{
		$fd_year = (int) substr($file, 0, 4);
		$fd_month = (int) substr($file, 4, 2);
		$fd_day = (int) substr($file, 6, 2);
		$fd_hour = (int) substr($file, 9, 2);
		$fd_min = (int) substr($file, 11, 2);
		$fd_sec = (int) substr($file, 13, 2);
        $fdate = sprintf("%04u-%02u-%02u %02u:%02u:%02u", $fd_year, $fd_month, $fd_day, $fd_hour, $fd_min, $fd_sec);
        $stattype = 3;
	}
    else if (strstr($pre, "ngLog.") || (substr($file, 4, 1) == "." && substr($file, 7, 1) == "." && substr($file, 10, 1) == "." && substr($file, 13, 1) == "." && substr($file, 16, 1) == "." && substr($file, 19, 1) == ".")) { // UT '99 Log
      $tok = strtok($file, "."); // <year>
      $fd_year = (int) $tok;
      if ($tok != "") {
        $tok = strtok("."); // <month>
        $fd_month = (int) $tok;
      if ($tok != "") {
        $tok = strtok("."); // <day>
        $fd_day = (int) $tok;
      if ($tok != "") {
        $tok = strtok("."); // <hour>
        $fd_hour = (int) $tok;
      if ($tok != "") {
        $tok = strtok("."); // <min>
        $fd_min = (int) $tok;
      if ($tok != "") {
        $tok = strtok("."); // <sec>
        $fd_sec = (int) $tok;
      if ($tok != "") {
          $tok = strtok("."); // <port>

        $fdate = sprintf("%04u-%02u-%02u %02u:%02u:%02u", $fd_year, $fd_month, $fd_day,
                          $fd_hour, $fd_min, $fd_sec); // 2001-12-08 18:52:15
        $stattype = 2;
      }}}}}}
    }
    else {
      if (!$noport)
        $tok = strtok($file, "_ -"); // <port>
      else
        $tok = "x";
      if ($tok != "") {
        if (!$noport)
          $tok = strtok("_ -"); // <year>
        else
          $tok = strtok($file, "_ -"); // <year>
        $fd_year = (int) $tok;
        if ($fd_year < 1000)
          $fd_year += 2000;
      if ($tok != "") {
        $tok = strtok("_ -"); // <month>
        $fd_month = (int) $tok;
      if ($tok != "") {
        $tok = strtok("_ -"); // <day>
        $fd_day = (int) $tok;
      if ($tok != "") {
        $tok = strtok("_ -"); // <hour>
        $fd_hour = (int) $tok;
      if ($tok != "") {
        $tok = strtok("_ -"); // <min>
        $fd_min = (int) $tok;
      if ($tok != "") {
        $tok = strtok("."); // <sec>
        $fd_sec = (int) $tok;
        $fdate = sprintf("%04u-%02u-%02u %02u:%02u:%02u", $fd_year, $fd_month, $fd_day,
                          $fd_hour, $fd_min, $fd_sec); // 2001-12-08 18:52:15
        $stattype = 1;
      }}}}}}
    }
  }
  return($stattype);
}

function rawlist($conn_id, $path)
{ 
  $list = ftp_nlist($conn_id, $path);
  $newlist = array();
  while (list($row) = each($list)) {
    $buf="";
    if ($row[0]=='d'||$row[0]=='-') {
      $buf = substr($row, 55);
      $newlist[] = $buf;
    }
  }
  return $newlist; 
}

function release_lock()
{
  global $lockname, $link;

  if ($lockname != "")
    sql_queryn($link, "DO RELEASE_LOCK('$lockname')");
}

function dellog($file,$chatfile)
{
  global $config, $matchdate, $mapfile;

  unlink($file);

  // Remove chat log file
  if ($chatfile != "" && file_exists($chatfile))
    unlink($chatfile);

  // Remove associated demo log file
  $demodir = $config["demodir"];
  $demoext = $config["demoext"];
  if (isset($matchdate) && isset($mapfile) && $demodir != "" && $demoext != "" && $matchdate && $mapfile) {
    if (substr($demodir, -1) != "/")
      $demodir.="/";
    $demofile = date('md-Hi', $matchdate)."-".$mapfile.".".$demoext;
    $demopath = $demodir.$demofile;
    if (file_exists($demopath))
      unlink($demopath);
  }
}

loadconfig();

$password = "";
$save = $test = $nohtml = 0;

global $safemode;
if (ini_get("safe_mode") || get_cfg_var("safe_mode") || ini_get("safe_mode_gid") || get_cfg_var("safe_mode_gid"))
  $safemode = 1;
else
  $safemode = 0;

if ($AdminMenu)
  $password = $UpdatePass;
else {
  $password = check_get("pass");
  if ($config["allowswitches"]) {
    if (check_get("savelogs"))
      $save = 1;
    if (check_get("debug"))
      $test = 1;
  }

  if (isset($_SERVER["argc"]) && isset($_SERVER["argv"])) {
    $argc = $_SERVER["argc"];
    $argv = $_SERVER["argv"];
  }
  for ($i = 1; $i < $argc; $i++) {
    $pos = strpos($argv[$i], "=");
    if ($pos !== FALSE && strlen($argv[$i]) > $pos) {
      $param = strtoupper(substr($argv[$i], 0, $pos));
      $val = substr($argv[$i], $pos + 1);
    }
    else
      $param = strtoupper($argv[$i]);

    switch ($param) {
      case "PASS":
        $password = $val;
        break;
      case "SAVELOGS":
        if ($config["allowswitches"])
          $save = 1;
        break;
      case "DEBUG":
        if ($config["allowswitches"])
          $test = 1;
        break;
      case "NOHTML":
        $nohtml = 1;
        break;
      default:
        echo "Invalid parameter.\n";
        exit;
    }
  }
}

if ($nohtml) {
  $break = "";
  $bold = "";
  $ebold = "";
}
else {
  $break = "<br />";
  $bold = "<b>";
  $ebold = "</b>";
}

if ($password == "" || $config["UpdatePass"] == "" || $password != $config["UpdatePass"]) {
  echo "Access error.{$break}\n";
  exit;
}

$link = sql_connect();

if (!isset($dbtype)) {
  echo "$dbtype is not set!{break}\n";
  exit;
}

if (strtolower($dbtype) == "mysql") {
  $mysqlver = mysql_get_server_info();
  $dot = strpos($mysqlver, ".");
  if ($dot === FALSE) {
    echo "Unable to determine MySQL version.<br />\n";
    exit;
  }
  $mysqlverh = (int) substr($mysqlver, 0, $dot);
  $dot2 = strpos($mysqlver, ".", $dot + 1);
  if ($dot2 === FALSE) {
    echo "Unable to determine MySQL version.<br />\n";
    exit;
  }
  $mysqlverl = (int) substr($mysqlver, $dot + 1, $dot2 - $dot - 1);
}

// Obtain MySQL lock to insure only one copy of logs.php is running
if ($config["lockname"] != "" && strtolower($dbtype) == "mysql" && ($mysqlverh > 3 || ($mysqlverh == 3 && $mysqlverl >= 23))) {
  $result = sql_queryn($link, "SELECT GET_LOCK('{$config['lockname']}',0)");
  if (!$result) {
    echo "Unable to obtain MySQL lock.{$break}\n";
    sql_close($link);
    exit;
  }
  list($lockresult) = sql_fetch_row($result);
  sql_free_result($result);
  if (!$lockresult) {
    echo "Log parser is currently locked for update.{$break}\n";
    sql_close($link);
    exit;
  }
}

require("includes/logparse.php");
require("includes/logsave.php");

//=============================================================================
//       FTP Transfer
//=============================================================================
$ftpnum = 1;
$demodir = $config["demodir"];
$demoext = $config["demoext"];
if (substr($demodir, -1) != "/")
  $demodir.="/";
while (isset($conflogs["ftpserver"][$ftpnum])) {
  if ($conflogs["ftpserver"][$ftpnum] != "") {
    $ftpserver = $conflogs["ftpserver"][$ftpnum];
    $ftpuser = $conflogs["ftpuser"][$ftpnum];
    $ftppass = $conflogs["ftppass"][$ftpnum];
    $ftppath = $conflogs["ftppath"][$ftpnum];
    $ftppassive = $conflogs["ftppassive"][$ftpnum];
    $logpath = $conflogs["logpath"][$ftpnum];
    $logprefix = $conflogs["logprefix"][$ftpnum];
    $chatprefix = $conflogs["chatprefix"][$ftpnum];
    $noport = $conflogs["noport"][$ftpnum];
    $alllogs = $conflogs["alllogs"][$ftpnum];
    $demoftppath = $conflogs["demoftppath"][$ftpnum];

    if ($ftpuser == "" || $ftppass == "") {
      echo "Error - you must set the FTPuser and FTPpass variables for this ftp server.{$break}\n";
      release_lock();
      sql_close($link);
      exit;
    }

    if ($logpath == "" || $logprefix == "") {
      echo "Error - you must set the logpath and logprefix variables for this ftp server.{$break}\n";
      release_lock();
      sql_close($link);
      exit;
    }

    $ftptype = 0;

    if (substr($logpath, -1) != "/" && substr($logpath, -1) != "\\")
      $logpath.="/";

    if (strtolower(substr($ftpserver, 0, 6)) == "ftp://")
      $ftptype = 1;
    else if (strtolower(substr($ftpserver, 0, 7)) == "ftps://")
      $ftptype = 2;

    if ($ftptype) {
      if ($ftptype == 1) {
        echo "Initializing ftp file transfer.{$break}\n";
        $ftp_server = substr($ftpserver, 6);
      }
      else {
        echo "Initializing ftps file transfer.{$break}\n";
        $ftp_server = substr($ftpserver, 7);
      }

      // Extract ftp server port
      $ftp_port = 21;
      if ($i = strpos($ftp_server, ":")) {
        $ftp_port = (int) substr($ftp_server, $i + 1);
        $ftp_server = substr($ftp_server, 0, $i);
      }

      $conn_id = $login_result = 0;
      if ($ftptype == 1)
        $conn_id = ftp_connect($ftp_server, $ftp_port, 20);
      else
        $conn_id = ftp_ssl_connect($ftp_server, $ftp_port, 20);
      if (!$conn_id)
        echo "Failure connecting to ftp server '$ftp_server' on port '$ftp_port'.{$break}\n";
      else {
        $login_result = ftp_login($conn_id, $ftpuser, $ftppass);
        if (!$conn_id || !$login_result)
          echo "Unable to login to ftp server '$ftp_server' on port '$ftp_port' for user '$ftpuser'.{$break}\n";
        else {
          echo "Connected to '$ftp_server' on port '$ftp_port' for user '$ftpuser'.{$break}\n";
          if ($ftppassive) {
            echo "Enabling passive mode.{$break}\n";
            ftp_pasv($conn_id, 1);
          }
          if ($ftppath) {
            if ((@ftp_chdir($conn_id, $ftppath)) == TRUE)
              echo "Successfully changed ftp directory to '$ftppath'.{$break}\n";
            else
              echo "Error changing ftp directory to '$ftppath'.{$break}\n";
          }
          ftp_set_option($conn_id, FTP_TIMEOUT_SEC, 30);

          if (!$safemode)
            set_time_limit($config["php_timelimit"]); // Reset script timeout counter

          // Retrieve log files
          $loglist = array();
          $listerr = 0;
          if (!($loglist = ftp_nlist($conn_id, "{$logprefix}*"))) {
            if (!($loglist = ftp_nlist($conn_id, ""))) { // Some ftp servers will only do full directory listings
              echo "Error listing ftp directory '{$ftppath}{$logprefix}*'.{$break}\n";
              $listerr = 1;
            }
          }
          if ($test)
            echo "[debug] listing '{$logprefix}*' - records: ".count($loglist)."{$break}\n";

          $i = $files = 0;
          $logs = array();
          $logdate = array();
          while(isset($loglist[$i])) {
            if ($test)
              echo "[debug] Loglist[$i] = '{$loglist[$i]}'{$break}\n";
            $file = $loglist[$i++];
            if (strstr($file, $logprefix) && (substr($file, -4) == ".log" || substr($file, -4) == ".txt")) {
              $fdate = "";
              $stattype = checkfile($logprefix, $noport, $file, $fdate);
              if ($stattype) {
                $logs[$files] = $file;
                $logdate[$files++] = $fdate;
              }
            }
          }
          if (!$listerr) {
            if ($files > 1)
              array_multisort($logdate, $logs, SORT_NUMERIC, SORT_ASC);
            else if (!$files)
              echo "No new logs to download.{$break}\n";
          }

          if ($alllogs)
            $ftplimit = 0;
          else
            $ftplimit = 1;

          for ($i = 0; $i < $files - $ftplimit; $i++) {
            if (!$safemode)
              set_time_limit($config["php_timelimit"]); // Reset script timeout counter
            $file = $logs[$i];
            echo "Downloading log '$file'....";
            if (ftp_get($conn_id, "{$logpath}$file", "$file", FTP_BINARY)) {
              echo "successful";

              if (!$save) {
                if ($test)
                  echo " - not deleted (debug).{$break}\n";
                else if (ftp_delete($conn_id, $file))
                  echo " - deleted.{$break}\n";
                else
                  echo " - deletion failed!{$break}\n";
              } else
                echo ".{$break}\n";
            }
            else
              echo "failed!{$break}\n";
          }

          // Retrieve chat logs
          if ($chatprefix != "") {
            ftp_set_option($conn_id, FTP_TIMEOUT_SEC, 30);

            if (!$safemode)
              set_time_limit($config["php_timelimit"]); // Reset script timeout counter

            $chatlist = array();
            $chatlisterr = 0;
            if (!($chatlist = ftp_nlist($conn_id, "{$chatprefix}*"))) {
              if (!($chatlist = ftp_nlist($conn_id, ""))) {
                echo "Error listing ftp directory '{$ftppath}{$chatprefix}*'.{$break}\n";
                $chatlisterr = 1;
              }
            }
            if ($test)
              echo "[debug] listing '{$chatprefix}*' - records: ".count($chatlist)."{$break}\n";

            $i = $chatfiles = 0;
            $chatlogs = array();
            $chatlogdate = array();
            while(isset($chatlist[$i])) {
              if ($test)
                echo "[debug] Chatlist[$i] = '{$chatlist[$i]}'{$break}\n";
              $file = $chatlist[$i++];
              if (strstr($file, $chatprefix) && (substr($file, -4) == ".log" || substr($file, -4) == ".txt")) {
                $fdate = "";
                $stattype = checkfile($chatprefix, $noport, $file, $fdate);
                if ($stattype) {
                  $chatlogs[$chatfiles] = $file;
                  $chatlogdate[$chatfiles++] = $fdate;
                }
              }
            }
            if (!$chatlisterr && !$chatfiles)
              echo "No new chat logs to download.{$break}\n";

            for ($i = 0; $i < $chatfiles - $ftplimit; $i++) {
              if (!$safemode)
                set_time_limit($config["php_timelimit"]); // Reset script timeout counter
              $file = $chatlogs[$i];
              echo "Downloading chat log '$file'....";
              if (ftp_get($conn_id, "{$logpath}$file", "$file", FTP_BINARY)) {
                echo "successful";
                if (!$save) {
                  if (ftp_delete($conn_id, $file))
                    echo " - deleted.{$break}\n";
                  else
                    echo " - deletion failed!{$break}\n";
                } else
                  echo ".{$break}\n";
              }
              else
                echo "failed!{$break}\n";
            }
          }

          // Retrieve demorecs
          if ($demoftppath != "") {
            if ((@ftp_chdir($conn_id, $demoftppath)) == TRUE)
              echo "Successfully changed ftp directory to '$demoftppath'.{$break}\n";
            else
              echo "Error changing ftp directory to '$demoftppath'.{$break}\n";
            ftp_set_option($conn_id, FTP_TIMEOUT_SEC, 30);

            if (!$safemode)
              set_time_limit($config["php_timelimit"]); // Reset script timeout counter
            $demolist = array();
            $demolisterr = 0;
            if (!($demolist = ftp_nlist($conn_id, "*.{$demoext}"))) {
              if (!($demolist = ftp_nlist($conn_id, ""))) {
                echo "Error listing ftp directory '{$demoftppath}*.{$demoext}'.{$break}\n";
                $demolisterr = 1;
              }
            }
            if ($test)
              echo "[debug] listing '*.{$demoext}' - records: ".count($demolist)."{$break}\n";

            $i = $demos = 0;
            $demologs = array();
            $demologdate = array();
            while(isset($demolist[$i])) {
              if ($test)
                echo "[debug] Demolist[$i] = '{$demolist[$i]}'{$break}\n";
              $file = $demolist[$i++];
              if (substr($file, 0 - strlen($demoext)) == $demoext) {
                $fdate = "";
                $demologs[$files++] = $file;
              }
            }
            if (!$demolisterr && !$files)
              echo "No new demos to download.{$break}\n";

            for ($i = 0; $i < $files - 1; $i++) {
              if (!$safemode)
                set_time_limit($config["php_timelimit"]); // Reset script timeout counter
              $file = $demologs[$i];
              echo "Downloading demo '$file'....";
              if (ftp_get($conn_id, "{$demodir}$file", "$file", FTP_BINARY)) {
                echo "successful";
                $demos++;
                if (!$save) {
                  if (ftp_delete($conn_id, $file))
                    echo " - deleted.{$break}\n";
                  else
                    echo " - deletion failed!{$break}\n";
                } else
                  echo ".{$break}\n";
              }
              else
                echo "failed!{$break}\n";
            }
          }

          // Close connection
          ftp_close($conn_id);
          echo "{$break}\n";
        }
      }
    }
  }
  $ftpnum++;
}

//=============================================================================
//       Search Log Directories
//=============================================================================
$total_saved = 0;
$lognum = 1;

while (isset($conflogs["logpath"][$lognum])) {
  $files = $logs_saved = 0;
  $logs = array();
  $chatlogs = array();
  $logtype = array();
  $logdate = array();
  $logpath = $conflogs["logpath"][$lognum];
  $backuppath = $conflogs["backuppath"][$lognum];
  $config["deftype"] = $conflogs["deftype"][$lognum];
  $config["defteam"] = $conflogs["defteam"][$lognum];
  $multicheck = $conflogs["multicheck"][$lognum];

  if (!isset($conflogs["logprefix"][$lognum])) {
    echo "Error - you must set the logprefix variable for this log path.{$break}\n";
    release_lock();
    sql_close($link);
    exit;
  }

  $logprefix = $conflogs["logprefix"][$lognum];
  $chatprefix = $conflogs["chatprefix"][$lognum];

  if (isset($conflogs["noport"][$lognum]))
    $noport = $conflogs["noport"][$lognum];
  else
    $noport = 0;

  if (substr($logpath, -1) != "/" && substr($logpath, -1) != "\\")
    $logpath.="/";

  if (strlen($backuppath) && substr($backuppath, -1) != "/" && substr($backuppath, -1) != "\\")
    $backuppath.="/";

  echo "{$bold}Processing directory '$logpath' for '{$logprefix}*':{$ebold}{$break}\n";
  $handle = opendir($logpath);
  while (($file = readdir($handle)) != false) {
    if (substr($file, 0, strlen($logprefix)) == $logprefix) {
      $fdate = "";
      if ($stattype = checkfile($logprefix, $noport, $file, $fdate)) {
        $logs[$files] = $file;
        $logtype[$files] = $stattype;
        $chatlogs[$files] = "";
        if ($chatprefix != "")
        {
          $chatfile = $chatprefix.substr($file, strlen($logprefix));
          if (file_exists($logpath.$chatfile))
            $chatlogs[$files] = $chatfile;
        }
        $logdate[$files++] = $fdate;
      }
      if ($test)
        echo "[$stattype] $file / $fdate{$break}\n";
    }
  }
  closedir($handle); 
  if ($files > 1)
    array_multisort($logdate, $logs, $chatlogs, $logtype, SORT_NUMERIC, SORT_ASC);

  $numinc = 0;
  $incomplete = array();
  for ($i = 0; $i < $files; $i++) {
    if (!$safemode)
      set_time_limit($config["php_timelimit"]); // Reset script timeout counter
    echo "Processing log '$logs[$i]'...";
    $file = $logpath.$logs[$i];
    if ($chatlogs[$i] != "")
      $chatfile = $logpath.$chatlogs[$i];
    else
      $chatfile = "";
    $logname = $logs[$i];
    $stattype = $logtype[$i];

    $match->ended = parselog($file,$chatfile);

    // Check for ended on map switch or server quit - set new ended type
    if ($config["allowincomplete"] && $match->ended == 6) // Map Change
      $match->ended = 16;
    else if  ($config["allowincomplete"] >= 2 && $match->ended == 2) // Other Endgame
      $match->ended = 12;
    else if  ($config["allowincomplete"] >= 3 && $match->ended == 7) // Server Quit
      $match->ended = 17;

    if ($match->numhumans < 2 && !$config["savesingle"] && ($match->ended == 1 || $match->ended == 12 || $match->ended == 16 || $match->ended == 17))
      $match->ended = 8;
    else if ($match->numplayers < 2 && $config["savesingle"] == 1 && ($match->ended == 1 || $match->ended == 12 || $match->ended == 16 || $match->ended == 17))
      $match->ended = 5;

    switch ($match->ended) {
      case -1:
        break;
      case 1:
      case 12:
      case 16:
      case 17:
        if ($test) {
          echo "Debug - not stored.{$break}\n";
          $logs_saved++;
        }
        else {
          if ($matchnum = storedata()) {
            echo "match $matchnum successfully processed.{$break}\n";
            $logs_saved++;
          }
          else
            echo "not processed.{$break}\n";
        }
        if (isset($backuppath) && $backuppath) {
          copy($file, "{$backuppath}{$logs[$i]}");
          if ($chatfile != "" && file_exists($chatfile))
            copy($chatfile, "{$backuppath}{$chatlogs[$i]}");
        }
        if (!$save)
          dellog($file,$chatfile);
        break;
      case 2:
        echo "unknown endgame reason.{$break}\n";
        if (!$save)
          dellog($file,$chatfile);
        break;
      case 3:
        echo "invalid.{$break}\n";
        if (!$save)
          dellog($file,$chatfile);
        break;
      case 4:
        echo "already in database.{$break}\n";
        if (!$save)
          dellog($file,$chatfile);
        break;
      case 5:
        echo "insufficient players.{$break}\n";
        if (!$save)
          dellog($file,$chatfile);
        break;
      case 6:
        echo "mapchange.{$break}\n";
        if (!$save)
          dellog($file,$chatfile);
        break;
      case 7:
        echo "serverquit.{$break}\n";
        if (!$save)
          dellog($file,$chatfile);
        break;
      case 8:
        echo "insufficient human players.{$break}\n";
        if (!$save)
          dellog($file,$chatfile);
        break;
      case 9:
        echo "scoreless match.{$break}\n";
        if (!$save)
          dellog($file,$chatfile);
        break;
      case 10:
        echo "bad parse in database.{$break}\n";
        break;
      case 11:
        echo "warm-up match.{$break}\n";
        if (!$save)
          dellog($file,$chatfile);
        break;
      default:
        if (!$config["skipinsession"]) {
          echo "incomplete.{$break}\n";
          if (!$save)
          dellog($file,$chatfile);
        }
        else {
          echo "incomplete (in session?).{$break}\n";
          $incomplete[$numinc][0] = $file;
          $incomplete[$numinc++][1] = $servername;
        }
    }
  }

  if (!$files)
    echo "{$bold}No log files to process.{$ebold}{$break}\n";
  else if (!$logs_saved)
    echo "{$bold}0 of $files logs processed - No new logs added.{$ebold}{$break}\n";
  else {
    // Remove all but most recent two incomplete log files per server
    if (!$safemode)
      set_time_limit($config["php_timelimit"]); // Reset script timeout counter
    if (!$save) {
      $numservers = 0;
      $serverlist = array();
      for ($i = $numinc - 1; $i >= 0; $i--) {
        $file = $incomplete[$i][0];
        $servername = $incomplete[$i][1];
        for ($i2 = 0, $cserver = -1; $i2 < $numservers && $cserver < 0; $i2++) {
          if (!strcmp($servername, $serverlist[$i2][0]))
            $cserver = $i2;
        }
        if ($cserver >= 0) {
          $serverlist[$cserver][1]++;
          if ($serverlist[$cserver][1] > 2) {
            unlink($file);
            $serverlist[$cserver][2]++;
            // Remove associated demo logs - need to figure out way to identify demo files based on log filename
          }
        }
        else {
          $serverlist[$numservers][0] = $servername;
          $serverlist[$numservers][1] = 1;
          $serverlist[$numservers++][2] = 0;
        }
      }
      for ($i = 0, $i2 = 0; $i < $numservers; $i++) {
        if ($serverlist[$i][2]) {
        	if (!$i2) {
        	  echo "{$break}\n";
            $i2 = 1;
          }
          if ($serverlist[$i][2] > 1)
            $lgs = "logs";
          else
            $lgs = "log";
          echo "Removed {$serverlist[$i][2]} incomplete $lgs for {$serverlist[$i][0]}.{$break}\n";
        }
      }
    }
    echo "{$bold}$logs_saved of $files logs processed.{$ebold}{$break}\n";
    $total_saved += $logs_saved;
  }
  $lognum++;
}

if ($lognum > 2)
  echo "{$bold}Total: $total_saved matches saved.{$ebold}{$break}\n";

// Check for limit on matches to keep
if ($total_saved && $config["maxmatches"] && !$test) {
  if (!$safemode)
    set_time_limit($config["php_timelimit"]); // Reset script timeout counter
  $result = sql_queryn($link, "SELECT COUNT(*) FROM {$dbpre}matches");
  list($num) = sql_fetch_row($result);
  sql_free_result($result);
  if ($num > $config["maxmatches"]) {
    $remove = $num - $config["maxmatches"];
    if ($remove > 1)
      echo "{$break}{$bold}Trimming earliest $remove matches from database to limit {$config['maxmatches']} matches.{$ebold}{$break}\n";
    else
      echo "{$break}{$bold}Trimming earliest match from database to limit {$config['maxmatches']} matches.{$ebold}{$break}\n";

    // Get highest match number to remove
    $result = sql_queryn($link, "SELECT MIN(gm_num) FROM {$dbpre}matches");
    list($min) = sql_fetch_row($result);
    sql_free_result($result);
    $gmnum = $min + ($remove - 1);

    // Remove Demo Logs
    $i = $demos = 0;
    while (isset($conflogs["logpath"][$i]) && !$demos) {
      if ($demodir != "" && $demoext != "")
        $demos = 1;
      $i++;
    }
    if ($demos && isset($matchdate) && isset($mapfile) && $matchdate && $mapfile) {
      $result = sql_querynb($link, "SELECT gm_start,mp_name FROM {$dbpre}matches,{$dbpre}maps WHERE gm_num<=$gmnum AND mp_num=gm_map");
      if (!$result) {
        echo "Error selecting matches for demo log removal.{$break}\n";
        release_lock();
        sql_close($link);
        exit;
      }
      while ($row = sql_fetch_row($result)) {
        $start = strtotime($row[0]);
        $mapfile = $row[1];
        $i = $demodeleted = 0;
        while (isset($conflogs["logpath"][$i]) && !$demodeleted) {
          if ($conflogs["demodir"][$i] != "" && $conflogs["demoext"][$i] != "") {
          	$demodir = $conflogs["demodir"][$i];
          	$demoext = $conflogs["demoext"][$i];
            if (substr($demodir, -1) != "/")
              $demodir.="/";
            $demofile = date('md-Hi', $start)."-".$mapfile.".".$demoext;
            $demopath = $demodir.$demofile;
            if (file_exists($demopath)) {
              unlink($demopath);
              $demodeleted = 1;
            }
          }
          $i++;
        }
      }
      sql_free_result($result);
    }

    if (!$safemode)
      set_time_limit($config["php_timelimit"]); // Reset script timeout counter

    // Delete Match Logs ({$dbpre}matches)
    $dresult = sql_queryn($link, "DELETE FROM {$dbpre}matches WHERE gm_num<=$gmnum");
    if (!$dresult)
      echo "Error removing matches!{$break}\n";

    // Delete Match Event Logs ({$dbpre}gevents)
    $dresult = sql_queryn($link, "DELETE FROM {$dbpre}gevents WHERE ge_match<=$gmnum");
    if (!$dresult)
      echo "Error removing match events!{$break}\n";

    // Delete Match Item Logs ({$dbpre}gitems)
    $dresult = sql_queryn($link, "DELETE FROM {$dbpre}gitems WHERE gi_match<=$gmnum");
    if (!$dresult)
      echo "Error removing match items!{$break}\n";

    // Delete Match Kill Logs ({$dbpre}gkills)
    $dresult = sql_queryn($link, "DELETE FROM {$dbpre}gkills WHERE gk_match<=$gmnum");
    if (!$dresult)
      echo "Error removing kill logs!{$break}\n";

    // Delete Match Weapon Accuracy Logs ({$dbpre}gwaccuracy)
    $dresult = sql_queryn($link, "DELETE FROM {$dbpre}gwaccuracy WHERE gwa_match<=$gmnum");
    if (!$dresult)
      echo "Error removing weapon accuracy logs!{$break}\n";

    // Delete Match Score Logs ({$dbpre}gscores)
    $dresult = sql_queryn($link, "DELETE FROM {$dbpre}gscores WHERE gs_match<=$gmnum");
    if (!$dresult)
      echo "Error removing score logs!{$break}\n";

    // Delete Match Player Logs ({$dbpre}gplayers)
    $dresult = sql_queryn($link, "DELETE FROM {$dbpre}gplayers WHERE gp_match<=$gmnum");
    if (!$dresult)
      echo "Error removing match player logs!{$break}\n";

    // Delete Match Chat Logs ({$dbpre}gchat)
    $dresult = sql_queryn($link, "DELETE FROM {$dbpre}gchat WHERE gc_match<=$gmnum");
    if (!$dresult)
      echo "Error removing chat logs!{$break}\n";
  }
  release_lock();
}

sql_close($link);

?>