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

function sql_query($query) {
  global $SQLhost, $SQLport, $SQLdb, $SQLus, $SQLpw, $dbtype;

  switch (strtolower($dbtype)) {
    case "mysql":
      if (!function_exists("mysql_connect"))
        die("No MySQL support found!");
      if (!isset($SQLport) || $SQLport == "")
        $SQLport = 3306;
      $link = @mysql_connect("$SQLhost:$SQLport","$SQLus","$SQLpw");
      if (!$link) {
        echo "Database access error.\n";
        exit;
      }
      $result = @mysql_select_db("$SQLdb");
      if (!$result) {
        echo "Error selecting database '$SQLdb'.\n";
        exit;
      }
      $result = @mysql_query("$query");
      if (!$result) {
      	$err = "*Error in database query: '$query'";
      	error_log($err);
        $err = mysql_errno($link) . ": " . mysql_error($link);
        error_log($err);
      }
      @mysql_close($link);
      break;
    case "sqlite":
      if (!function_exists("sqlite_open"))
        die("No SQLite support found!");
      $link = @sqlite_open("$SQLdb", 0640, $sqlite_err);
      if (!$link) {
        echo "Database access error.\n";
        die($sqlite_err);
      }
      $result = @sqlite_query($link, "$query");
      if (!$result) {
      	$err = "*Error in database query: '$query'";
      	error_log($err);
        error_log(sqlite_error_string(sqlite_last_error($link)));
      }
      @sqlite_close($link);
      break;
    case "mssql":
      if (!function_exists("mssql_connect"))
        die("No MS SQL support found!");
      $link = @mssql_connect("$SQLhost","$SQLus","$SQLpw");
      if (!$link) {
        echo "Database access error.\n";
        exit;
      }
      $result = @mssql_select_db("$SQLdb");
      if (!$result) {
      	echo "Error selecting database '$SQLdb'.\n";
      	exit;
      }

      // Convert from LIMIT to TOP in UPDATE queries
      if (!strcmp(substr($query, 0, 6), "SELECT") && !strcmp(substr($query, -7), "LIMIT 1")) {
        $msquery = "SELECT TOP 1 " . substr($query, 6, -7);
        $query = $msquery;
      }
      /*
        SELECT TOP <Length> * FROM [Table] WHERE [Primary_Key] NOT IN
        (
           SELECT TOP <Start> [Primary_Key]
           FROM [Table]
           ORDER BY [Sort_Field]
        )
        ORDER BY [Sort_Field]
      */
      $result = @mssql_query("$query");
      if ($result == FALSE) {
      	$err = "*Error in database query: '$query'";
      	error_log($err);
        // error_log(mssql_error());
      }
      @mssql_close($link);
      break;
    default:
      echo "Database type error.\n";
      exit;
  }
  return $result;
}

function sql_connect() {
  global $SQLhost, $SQLport, $SQLdb, $SQLus, $SQLpw, $dbtype;

  switch (strtolower($dbtype)) {
    case "mysql":
      if (!isset($SQLport) || $SQLport == "")
        $SQLport = 3306;
      $link = @mysql_connect("$SQLhost:$SQLport","$SQLus","$SQLpw");
      if (!$link) {
        echo "Database access error.\n";
        exit;
      }
      $result = @mysql_select_db("$SQLdb");
      if (!$result) {
        echo "Error selecting database '$SQLdb'.\n";
        exit;
      }
      break;
    case "sqlite":
      $link = @sqlite_open("$SQLdb", 0640, $sqlite_err);
      if (!$link) {
        echo "Database access error.\n";
        die($sqlite_err);
      }
      @sqlite_create_function($link, 'FROM_UNIXTIME', 'from_unixtime', 1);
      @sqlite_unbuffered_query($link, "BEGIN");
      break;
    case "mssql":
      $link = @mssql_connect("$SQLhost","$SQLus","$SQLpw");
      if (!$link) {
        echo "Database access error.\n";
        exit;
      }
      $result = @mssql_select_db("$SQLdb");
      if (!$result) {
        echo "Error selecting database '$SQLdb'.\n";
        exit;
      }
      break;
    default:
      echo "Database type error.\n";
      exit;
  }
  return $link;
}

function sql_queryn($link, $query) {
  global $uselimit, $dbtype;

  if (!isset($uselimit) || !$uselimit) { // Remove LIMIT 1 from UPDATE queries
    if (!strcmp(substr($query, 0, 6), "UPDATE") && !strcmp(substr($query, -7), "LIMIT 1"))
      $query = substr($query, 0, -7);
  }
  switch (strtolower($dbtype)) {
    case "mysql":
      $result = @mysql_unbuffered_query("$query", $link);
      if (!$result) {
      	$err = "*Error in database query: '$query'";
      	error_log($err);
        $err = mysql_errno($link) . ": " . mysql_error($link);
        error_log($err);
      }
      break;
    case "sqlite":
      $query = ereg_replace(" USE INDEX \(.*\)", "", $query);
      $result = @sqlite_unbuffered_query($link, "$query");
      if (!$result) {
      	$err = "*Error in database query: '$query'";
      	error_log($err);
        error_log(sqlite_error_string(sqlite_last_error($link)));
      }
      break;
    case "mssql":
      $result = @mssql_query("$query");
      if (!$result) {
      	$err = "*Error in database query: '$query'";
      	error_log($err);
        // error_log(mssql_error());
      }
      break;
    default:
      echo "Database type error.\n";
      exit;
      break;
  }
  return $result;
}

function sql_querynb($link, $query) {
  global $uselimit, $dbtype;

  if (!isset($uselimit) || !$uselimit) { // Remove LIMIT 1 from UPDATE queries
    if (!strcmp(substr($query, 0, 6), "UPDATE") && !strcmp(substr($query, -7), "LIMIT 1"))
      $query = substr($query, 0, -7);
  }
  switch (strtolower($dbtype)) {
    case "mysql":
      $result = @mysql_query("$query", $link);
      if (!$result) {
      	$err = "*Error in database query: '$query'";
      	error_log($err);
        $err = mysql_errno($link) . ": " . mysql_error($link);
        error_log($err);
      }
      break;
    case "sqlite":
      $query = ereg_replace(" USE INDEX \(.*\)", "", $query);
      $result = @sqlite_query($link, "$query");
      if (!$result) {
      	$err = "*Error in database query: '$query'";
      	error_log($err);
        error_log(sqlite_error_string(sqlite_last_error($link)));
      }
      break;
    case "mssql":
      $result = @mssql_query("$query");
      if (!$result) {
      	$err = "*Error in database query: '$query'";
      	error_log($err);
        // error_log(mssql_error());
      }
      break;
    default:
      echo "Database type error.\n";
      exit;
      break;
  }
  return $result;
}

function sql_fetch_row($result) {
  global $dbtype;

  switch (strtolower($dbtype)) {
    case "mysql":
      $row = @mysql_fetch_row($result);
      break;
    case "sqlite":
      $row = @sqlite_fetch_array($result, SQLITE_NUM);
      break;
    case "mssql":
      if ($result != TRUE && $result != FALSE)
        $row = @mssql_fetch_row($result);
      else
        $row = NULL;
      break;
    default:
      echo "Database type error.\n";
      exit;
      break;
  }
  return $row;
}

function sql_fetch_assoc($result) {
  global $dbtype;

  switch (strtolower($dbtype)) {
    case "mysql":
      $row = @mysql_fetch_assoc($result);
      break;
    case "sqlite":
      $row = @sqlite_fetch_array($result, SQLITE_ASSOC);
      break;
    case "mssql":
      if ($result != TRUE && $result != FALSE)
        $row = @mssql_fetch_assoc($result);
      else
        $row = NULL;
      break;
    default:
      echo "Database type error.\n";
      exit;
      break;
  }
  return $row;
}

function sql_fetch_array($result) {
  global $dbtype;

  switch (strtolower($dbtype)) {
    case "mysql":
      $row = @mysql_fetch_array($result);
      break;
    case "sqlite":
      $row = @sqlite_fetch_array($result, SQLITE_BOTH);
      break;
    case "mssql":
      if ($result != TRUE && $result != FALSE)
        $row = @mssql_fetch_array($result);
      else
        $row = NULL;
      break;
    default:
      echo "Database type error.\n";
      exit;
      break;
  }
  return $row;
}

function sql_free_result($result) {
  global $dbtype;

  switch (strtolower($dbtype)) {
    case "mysql":
      @mysql_free_result($result);
      break;
    case "sqlite":
      break;
    case "mssql":
      if ($result != TRUE && $result != FALSE)
        @mssql_free_result($result);
      break;
    default:
      echo "Database type error.\n";
      exit;
      break;
  }
}

function sql_insert_id($link) {
  global $dbtype;

  switch (strtolower($dbtype)) {
    case "mysql":
      $num = @mysql_insert_id();
      break;
    case "sqlite":
      $num = @sqlite_last_insert_rowid($link);
      break;
    case "mssql":
      $result = @mssql_query("SELECT @@IDENTITY"); // SCOPE_IDENTITY
      // $result = mssql_query("SELECT IDENT_CURRENT()");
      if ($result) {
        $num = reset(mssql_fetch_row($result));
        if (!is_numeric($num))
          $num = 0;
      }
      else
        $num = 0;
      break;
    default:
      echo "Database type error.\n";
      exit;
      break;
  }
  return $num;
}

function sql_num_rows($result) {
  global $dbtype;

  switch (strtolower($dbtype)) {
    case "mysql": // Not available in unbuffered mode
      $num = mysql_num_rows($result);
      break;
    case "sqlite": // Not available in unbuffered mode
      $num = sqlite_num_rows($result);
      break;
    case "mssql":
      $num = mssql_num_rows($result);
      break;
    default:
      echo "Database type error.\n";
      exit;
      break;
  }
  return $num;
}

function sql_close($link) {
  global $dbtype;

  switch (strtolower($dbtype)) {
    case "mysql":
      mysql_close($link);
      break;
    case "sqlite":
      sqlite_unbuffered_query($link, "COMMIT");
      sqlite_close($link);
      break;
    case "mssql":
      mssql_close($link);
      break;
    default:
      echo "Database type error.\n";
      exit;
      break;
  }
}

function sql_addslashes($str) {
  global $dbtype;

  switch (strtolower($dbtype)) {
    case "mysql":
    case "mssql":
      $str = addslashes($str);
      break;
    case "sqlite":
      $str = sqlite_escape_string($str);
      break;
      break;
    default:
      echo "Database type error.\n";
      exit;
      break;
  }
  return $str;
}

function sql_error($link) {
  global $dbtype;

  switch (strtolower($dbtype)) {
    case "mysql":
      $err = mysql_error($link);
      break;
    case "mssql":
      $err = "";
      break;
    case "sqlite":
      $err = sqlite_error_string(sqlite_last_error($link));
      break;
    case "pgsql":
      $err = pg_last_error($link);
      break;
    default:
      echo "Database type error.\n";
      exit;
      break;
  }
  return $err;
}

function from_unixtime($unixtime)
{
  return "'".date('Y-m-d H:i:s', $unixtime)."'";
}

function sql_show_tables($query) {
  global $SQLhost, $SQLport, $SQLdb, $SQLus, $SQLpw, $dbtype;

  switch (strtolower($dbtype)) {
    case "mysql":
      if (!isset($SQLport) || $SQLport == "")
        $SQLport = 3306;
      $link = @mysql_connect("$SQLhost:$SQLport","$SQLus","$SQLpw");
      if (!$link) {
        echo "Database access error.\n";
        exit;
      }
      $result = mysql_select_db("$SQLdb");
      if (!$result) {
        echo "Error selecting database '$SQLdb'.\n";
        exit;
      }
      $result = mysql_query("$query");
      mysql_close($link);
      break;
    case "sqlite":
      $link = sqlite_open("$SQLdb", 0640, $sqlite_err);
      if (!$link) {
        echo "Database access error.\n";
        die($sqlite_err);
      }
      $result = sqlite_query($link, "$query");
      sqlite_close($link);
      break;
    case "mssql":
      $link = @mssql_connect("$SQLhost","$SQLus","$SQLpw");
      if (!$link) {
        echo "Database access error.\n";
        exit;
      }
      $result = @mssql_select_db("$SQLdb");
      if (!$result) {
      	echo "Error selecting database '$SQLdb'.\n";
      	exit;
      }
      $result = @mssql_query("$query");
      mssql_close($link);
      break;
    default:
      echo "Database type error.\n";
      exit;
  }
  return $result;
}

?>