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

      $query = mssql_queryfix($query);
      $result = @mssql_query("$query");
      if ($result == FALSE) {
      	$err = "*Error in database query: '$query'";
      	error_log($err);
        // error_log(mssql_error());
      }
      // @mssql_close($link); // This clears mssql results
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

  if (!isset($uselimit) || !$uselimit) { // Remove LIMIT 1 from UPDATE queries for unsupported versions
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
      $query = mssql_queryfix($query);
      $result = @mssql_query("$query");
      if ($result == FALSE) {
      	$err = "*Error in database query: '$query'";
      	error_log($err);
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
      $query = mssql_queryfix($query);
      $result = @mssql_query("$query");
      if (!$result) {
      	$err = "*Error in database query: '$query'";
      	error_log($err);
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
      if ($result != FALSE) {
        $row = @mssql_fetch_row($result);
        $i = 0;
        while (isset($row[$i])) {
          if (is_string($row[$i]) && $row[$i] == " ")
            $row[$i] = "";
          $i++;
        }
      }
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
      if ($result != FALSE)
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
      if ($result != FALSE)
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
      if ($result != FALSE)
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
      $result = @mssql_query("SELECT @@IDENTITY");
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
      $str = addslashes($str);
      break;
    case "sqlite":
      $str = sqlite_escape_string($str);
      break;
    case "mssql":
      $str = str_replace("'", "''", $str);
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

// Modified from code by Jon Jensen
function sqlite_alter_table($link, $table, $alterdefs)
{
  $result = sqlite_query($link, "SELECT sql,name,type FROM sqlite_master WHERE tbl_name = '".$table."' ORDER BY type DESC");

  if (sqlite_num_rows($result) > 0) {
    $row = sqlite_fetch_array($result);
    $tmpname = 't'.time();
    $origsql = trim(preg_replace("/[\s]+/"," ",str_replace(",",", ",preg_replace("/[\(]/","( ",$row['sql'],1))));
    $createtemptableSQL = 'CREATE TEMPORARY '.substr(trim(preg_replace("'".$table."'",$tmpname,$origsql,1)),6);
    $createindexsql = array();
    $i = 0;
    $defs = preg_split("/[,]+/",$alterdefs,-1,PREG_SPLIT_NO_EMPTY);
    $prevword = $table;

    // $oldcols = preg_split("/[,]+/",substr(trim($createtemptableSQL),strpos(trim($createtemptableSQL),'(')+1),-1,PREG_SPLIT_NO_EMPTY);
    $oldcols = array();
    $tmpcols = trim($origsql);
    $p = strpos($tmpcols, "(");
    $tmpcols = substr($tmpcols, $p + 1);
    $tmpcols = trim($tmpcols);
    $p = strpos($tmpcols, ",");

    while ($p != FALSE) {
      $n = 0;
      if (substr($tmpcols, $p - 2, 1) != "(" && substr($tmpcols, $p - 3, 1) != "(") {
        $oldcols[] = substr($tmpcols, 0, $p);
        $tmpcols = substr($tmpcols, $p + 1);
      }
      else
        $n = $p + 1;
      $p = strpos($tmpcols, ",", $n);
    }

    $newcols = array();

    for ($i=0;$i<sizeof($oldcols);$i++) {
      $colparts = preg_split("/[\s]+/",$oldcols[$i],-1,PREG_SPLIT_NO_EMPTY);
      $oldcols[$i] = $colparts[0];
      $newcols[$colparts[0]] = $colparts[0];
    }

    $newcolumns = '';
    $oldcolumns = '';
    reset($newcols);

    while (list($key,$val) = each($newcols)) {
      $newcolumns .= ($newcolumns?', ':'').$val;
      $oldcolumns .= ($oldcolumns?', ':'').$key;
    }

    $copytotempsql = 'INSERT INTO '.$tmpname.'('.$newcolumns.') SELECT '.$oldcolumns.' FROM '.$table;
    $dropoldsql = 'DROP TABLE '.$table;
    $createtesttableSQL = $createtemptableSQL;

    foreach ($defs as $def) {
      $defparts = preg_split("/[\s]+/", $def, -1, PREG_SPLIT_NO_EMPTY);
      $action = strtolower($defparts[0]);
      switch($action) {
      case 'add':
        if (sizeof($defparts) <= 2) {
          trigger_error('near "'.$defparts[0].($defparts[1]?' '.$defparts[1]:'').'": syntax error',E_USER_WARNING);
          return false;
        }
        $createtesttableSQL = substr($createtesttableSQL,0,strlen($createtesttableSQL)-1).',';
        for ($i = 1; $i < sizeof($defparts); $i++)
          $createtesttableSQL.=' '.$defparts[$i];
        $createtesttableSQL.=')';
        break;
      case 'change':
        if (sizeof($defparts) <= 3) {
          trigger_error('near "'.$defparts[0].($defparts[1]?' '.$defparts[1]:'').($defparts[2]?' '.$defparts[2]:'').'": syntax error',E_USER_WARNING);
          return false;
        }
        if ($severpos = strpos($createtesttableSQL,' '.$defparts[1].' ')) {
          if ($newcols[$defparts[1]] != $defparts[1]) {
            trigger_error('unknown column "'.$defparts[1].'" in "'.$table.'"',E_USER_WARNING);
            return false;
          }
          $newcols[$defparts[1]] = $defparts[2];
          $nextcommapos = strpos($createtesttableSQL,',',$severpos);
          $insertval = '';
          for ($i=2;$i<sizeof($defparts);$i++)
            $insertval.=' '.$defparts[$i];
          if ($nextcommapos)
            $createtesttableSQL = substr($createtesttableSQL,0,$severpos).$insertval.substr($createtesttableSQL,$nextcommapos);
          else
            $createtesttableSQL = substr($createtesttableSQL,0,$severpos-(strpos($createtesttableSQL,',')?0:1)).$insertval.')';
        }
        else {
          trigger_error('unknown column "'.$defparts[1].'" in "'.$table.'"',E_USER_WARNING);
          return false;
        }
        break;
      case 'drop':
        if (sizeof($defparts) < 2) {
          trigger_error('near "'.$defparts[0].($defparts[1]?' '.$defparts[1]:'').'": syntax error',E_USER_WARNING);
          return false;
        }
        if ($severpos = strpos($createtesttableSQL,' '.$defparts[1].' ')) {
          $nextcommapos = strpos($createtesttableSQL,',',$severpos);
          if ($nextcommapos)
            $createtesttableSQL = substr($createtesttableSQL,0,$severpos).substr($createtesttableSQL,$nextcommapos + 1);
          else
            $createtesttableSQL = substr($createtesttableSQL,0,$severpos-(strpos($createtesttableSQL,',')?0:1) - 1).')';
          unset($newcols[$defparts[1]]);
        }
        else {
          trigger_error('unknown column "'.$defparts[1].'" in "'.$table.'"',E_USER_WARNING);
          return false;
        }
        break;
      default:
        trigger_error('near "'.$prevword.'": syntax error',E_USER_WARNING);
        return false;
      }
      $prevword = $defparts[sizeof($defparts)-1];
    }

    // Generates a test table simply to verify that the columns specifed are valid in an sql statement
    $result = sqlite_query($link, $createtesttableSQL);
    if (!$result) {
      print("SQLite Error creating test table.<br>\n");
      return false;
    }
    $droptempsql = 'DROP TABLE '.$tmpname;
    $result = sqlite_query($link, $droptempsql);
    if (!$result) {
      print("SQLite Error dropping test table.<br>\n");
      return false;
    }

    $createnewtableSQL = 'CREATE '.substr(trim(preg_replace("'".$tmpname."'",$table,$createtesttableSQL,1)),17);
    $newcolumns = '';
    $oldcolumns = '';
    reset($newcols);

    while (list($key,$val) = each($newcols)) {
      $newcolumns .= ($newcolumns?', ':'').$val;
      $oldcolumns .= ($oldcolumns?', ':'').$key;
    }
    $copytonewsql = 'INSERT INTO '.$table.'('.$newcolumns.') SELECT '.$oldcolumns.' FROM '.$tmpname;

    $result = sqlite_query($link, $createtemptableSQL); // Create temp table
    if (!$result) {
      print("SQLite Error creating temp table.<br>\n");
      return false;
    }
    $result = sqlite_query($link, $copytotempsql); // Copy to table
    if (!$result) {
      print("SQLite Error copying to temp table.<br>\n");
      return false;
    }
    $result = sqlite_query($link, $dropoldsql); // Drop old table
    if (!$result) {
      print("SQLite Error dropping old table.<br>\n");
      return false;
    }

    $result = sqlite_query($link, $createnewtableSQL); // Recreate original table
    if (!$result) {
      print("SQLite Error creating original table.<br>\n");
      return false;
    }
    $result = sqlite_query($link, $copytonewsql); // Copy back to original table
    if (!$result) {
      print("SQLite Error copying to original table.<br>\n");
      return false;
    }
    $result = sqlite_query($link, $droptempsql); // Drop temp table
    if (!$result) {
      print("SQLite Error dropping temp table.<br>\n");
      return false;
    }
  }
  else {
    trigger_error('no such table: '.$table,E_USER_WARNING);
    return false;
  }

  return true;
}

function mssql_queryfix($query)
{
  // Convert from LIMIT to TOP in SELECT queries
  if (!strcmp(substr($query, 0, 6), "SELECT") && strstr($query, " LIMIT ") !== false) {
  	if (($pl = strpos($query, " LIMIT ")) !== false) {
  	  $lim = substr($query, $pl + 7);
  	  if (($pc = strpos(substr($query, $pl + 7), ",")) === false)
        $query = "SELECT TOP $lim " . substr($query, 6, $pl - 6);
      else {
        $lim1 = intval(substr($lim, 0, $pc));
        $lim2 = intval(substr($lim, $pc + 1));
        $lim1 += $lim2;
        $query = "SELECT TOP $lim2 * FROM (SELECT TOP $lim1 " . substr($query, 6, $pl - 6) . ") AS mslimit";
      }
    }
  }

  // Convert FROM_UNIXTIME
  if (($ut = strpos($query, "FROM_UNIXTIME(")) !== false) {
  	if (($eq = strpos(substr($query, $ut + 14), ")")) !== false) {
  	  $dt = date('Y-m-d H:i:s', substr($query, $ut + 14, $eq));
  	  $query = substr($query, 0, $ut)."'".$dt."'".substr($query, $ut + $eq + 15);
    }
  }

  // Fix date queries
  if (!strcmp(substr($query, 0, 6), "SELECT")) {
    $daterows = array("cn_ctime", "cn_dtime", "mp_lastmatch", "gm_init", "gm_start", "sv_lastmatch", "tl_chfragssg_date", "tl_chkillssg_date", "tl_chdeathssg_date", "tl_chsuicidessg_date", "tl_chcarjacksg_date", "tl_chroadkillssg_date", "tl_chcpcapturesg_date", "tl_chflagcapturesg_date", "tl_chflagreturnsg_date", "tl_chflagkillsg_date", "tl_chbombcarriedsg_date", "tl_chbombtossedsg_date", "tl_chbombkillsg_date", "tl_chnodeconstructedsg_date", "tl_chnodeconstdestroyedsg_date", "tl_chnodedestroyedsg_date", "wp_chkillssg_dt", "wp_chdeathssg_dt", "wp_chdeathshldsg_dt", "wp_chsuicidessg_dt");
    foreach ($daterows as $daterow) {
      if (($p = strpos($query, $daterow)) !== false && substr($query, $p + strlen($daterow), 1) != "=") {
        if (strstr($query, "MAX(") === false)
          $query = substr($query, 0, $p) . "CONVERT(char(19), " . substr($query, $p, strlen($daterow)) . ", 20) AS $daterow" . substr($query, $p + strlen($daterow));
        else
          $query = substr($query, 0, $p) . "CONVERT(char(19), " . substr($query, $p, strlen($daterow)) . ", 20)" . substr($query, $p + strlen($daterow));
      }
    }
  }

  // Strip USE INDEX
  if (($ui = strpos($query, "USE INDEX")) !== false) {
    if (($ep = strpos(substr($query, $ui + 9), ")")) !== false)
      $query = substr($query, 0, $ui) . substr($query, $ui + $ep + 10);
  }

  return $query;
}

?>