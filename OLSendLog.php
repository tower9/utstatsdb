<?php

// OLSendLog v1.32
//
// OverloadUT's OLSendLog for OLStats 3.01 modified by Panther for UTStatsDB 3.03

/*
  Possible return codes:
    R1 OK
    R2 Bad Password
    R3 No filename specified
    R4 No data sent
    R5 Could not create logfile
    R6 Error saving data
    R7 Configuration error
    R8 Invalid Prefix
*/

echo "OLStatsSendLogTarget\n";

error_reporting(0);

require("includes/statsdb.inc.php");
require("includes/logsql.php");

function check_post($val)
{
  $magic = get_magic_quotes_gpc();

  if (isset($_POST["$val"])) {
    if ($magic)
      $store = stripslashes($_POST["$val"]);
    else
      $store = $_POST["$val"];
    if ($store)
      return $store;
    return 1;
  }

  return 0;
}

function loadconfig()
{
  global $dbpre, $UpdatePass, $conflogs;

  $magicrt = get_magic_quotes_runtime();
  $link = sql_connect();
  $result = sql_querynb($link, "SELECT value FROM {$dbpre}config WHERE conf='UpdatePass' LIMIT 1");
  if ($result && sql_num_rows($result)) {
    $row = sql_fetch_row($result);
    $UpdatePass = $magicrt ? stripslashes($row[0]) : $row[0];
    sql_free_result($result);
  }
  else {
    send_result("7");
  	exit;
  }

  $conflogs = array(array());
  $result = sql_querynb($link, "SELECT logpath,prefix FROM {$dbpre}configlogs ORDER BY num");
  if ($result && sql_num_rows($result)) {
    $num = 0;
    while ($row = sql_fetch_row($result)) {
      $conflogs["logpath"][$num] = $magicrt ? stripslashes($row[0]) : $row[0];
      $conflogs["logprefix"][$num++] = $magicrt ? stripslashes($row[1]) : $row[1];
    }
    sql_free_result($result);
  }
  else {
    send_result("7");
  	exit;
  }

  sql_close($link);
}

function checkprefix($fn)
{
  global $conflogs;

  $logpath = "";
  $num = 0;
  while (isset($conflogs["logpath"][$num]) && $logpath == "") {
  	$len = strlen($conflogs["logprefix"][$num]);
    if (!strncasecmp($fn, $conflogs["logprefix"][$num], $len))
      $logpath = $conflogs["logpath"][$num];
    $num++;
  }

  return $logpath;
}

function send_result($rc)
{
  // There is a bug in LibHTTP2 that makes it so the last couple lines sometimes 
  // do not come through, so I add buffer to the end of the output to fix that.
  echo "$rc\nBuffer\nBuffer\nBuffer";
}

loadconfig();

$password = check_post("pass");
$filename = check_post("filename");
$data = check_post("data");

if ($password == "" || $UpdatePass == "" || $password != $UpdatePass) {
  send_result("2");
  exit;
}

if ($filename === 0)
{
	send_result("3");
	exit;
}

if ($data === 0)
{
	send_result("4");
	exit;
}

$logpath = checkprefix($filename);
if ($logpath == "") {
  send_result("8");
  exit;
}

$filename = $logpath.$filename.".log";

if (($f = @fopen($filename, "w")) === FALSE)
{
  send_result("5");
  exit;
}

if ((fwrite($f, $data, strlen($data))) === FALSE)
  send_result("6");
else
  send_result("1");

fclose($f);

?>