<?php

/*
    UTStatsDB
    Copyright (C) 2002-2006  Patrick Contreras / Paul Gallier

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
//========== Main Configuration ===============================================
//=============================================================================
function mainconfig() {
  global $dbpre, $magicrt;

  menu_top();

  echo <<<EOF
<form action="admin.php" method="post">
  <input type="hidden" name="SaveType" value="Main">
  <table class="forms">
    <tr>
      <th width="110">&nbsp;</th>
      <th align="left" class="header">
      	UTStatsDB Main Configuration
      </th>
    </tr>

EOF;

  $result = sql_query("SELECT num,conf,type,value,name,descr FROM {$dbpre}config ORDER BY num");
  if (!$result) {
  	echo "Config database error!<br />\n";
    exit;
  }
  while ($row = sql_fetch_row($result))
  {
    $num = intval($row[0]);
    $option = $row[1];
    $type = strtolower(substr($row[2], 0, 1));
    if ($type == "b") {
      $tmp = explode("|", substr($row[2], 1));
      $len = intval($tmp[0]);
      for ($i = 0; $i < $len; $i++) {
        if (isset($tmp[$i + 1]))
          $buttons[$i] = $tmp[$i + 1];
        else
          $buttons[$i] = $i;
      }
    }
    else
      $len = intval(substr($row[2], 1));
    $value = $magicrt ? stripslashes($row[3]) : $row[3];
    $name = $magicrt ? stripslashes($row[4]) : $row[4];
    $desc = $magicrt ? stripslashes($row[5]) : $row[5];

    switch ($type) {
      case "s": // String
      case "i": // Integer
      {
        $maxlen = $len;
        if ($len > 80)
          $len = 80;

        echo <<<EOF
    <tr>
      <td align="right" class="forms" title="$desc"><b>$name:</b></td>
      <td align="left" class="forms">
        <input type="text" name="$option" value="$value" size="$len" maxlength="$maxlen" class="forms">
      </td>
    </tr>

EOF;
        break;
      }
      case "p": // Password String
      {
        echo <<<EOF
    <tr>
      <td align="right" class="forms" title="$desc"><b>$name:</b></td>
      <td align="left" class="forms">
        <input type="password" name="$option" value="$value" size="$len" maxlength="$len" class="forms">
      </td>
    </tr>

EOF;
        break;
      }
      case "b": // Boolean
      {
        echo <<<EOF
    <tr>
      <td align="right" class="forms" title="$desc"><b>$name:</b></td>
      <td align="left" class="forms">

EOF;

        for ($i = 0; $i < $len; $i++) {
          if ($i == $value)
            $setcheck = "checked";
          else
            $setcheck = "";
          echo "<input type=\"radio\" name=\"$option\" value=\"$i\" $setcheck>{$buttons[$i]}\n";
        }

        echo <<<EOF
      </td>
    </tr>

EOF;
        break;
      }
      case "h": // Hidden
        break;
    }
  }
  sql_free_result($result);

  $result = sql_query("SELECT title_msg,title_msgDesc FROM {$dbpre}configset LIMIT 1");
  if (!$result) {
  	echo "Config database error!<br />\n";
    exit;
  }
  $row = sql_fetch_row($result);
  sql_free_result($result);
  $title_msg = htmlspecialchars($row[0]);
  $title_msgDesc = $row[1];

  echo <<<EOF
    <tr>
      <td align="right" class="forms" title="$title_msgDesc"><b>Title Message:</b></td>
      <td align="left" class="forms">
        <textarea name="title_msg" cols="110" rows="8" class="forms">$title_msg</textarea>
      </td>
    </tr>

EOF;

  echo <<<EOF
    <tr>
      <td colspan="2" class="forms">
      	<br />
      	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="submit" name="Mode" value="Save" class="formsb">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Mouse over text for full description.
      </td>
    </tr>
  </table>
</form>

EOF;

  menu_bottom();
}

//=============================================================================
//========== Logs Configuration ===============================================
//=============================================================================
function logsconfig() {
  global $dbpre, $magicrt;

  menu_top();

  echo <<<EOF
<form action="admin.php" method="post">
  <input type="hidden" name="SaveType" value="Logs">
  <table class="forms">
    <tr>
      <th width="85">&nbsp;</th>
      <th align="left" class="header">
      	UTStatsDB Logs Configuration
      </th>
    </tr>

EOF;

  $result = sql_query("SELECT num,logpath,backuppath,prefix,noport,ftpserver,ftppath,passive,alllogs,ftpuser,ftppass,deftype,defteam,demoftppath,multicheck FROM {$dbpre}configlogs ORDER BY num");
  if (!$result) {
  	echo "Config database error!<br />\n";
    exit;
  }
  $logsets = 0;
  while ($row = sql_fetch_row($result))
  {
    $logsets++;
    $num = intval($row[0]);
    $logpath = $magicrt ? stripslashes($row[1]) : $row[1];
    $backuppath = $magicrt ? stripslashes($row[2]) : $row[2];
    $prefix = $magicrt ? stripslashes($row[3]) : $row[3];
    $noport = intval($row[4]);
    $ftpserver = $magicrt ? stripslashes($row[5]) : $row[5];
    $ftppath = $magicrt ? stripslashes($row[6]) : $row[6];
    $passive = intval($row[7]);
    $alllogs = intval($row[8]);
    $ftpuser = $magicrt ? stripslashes($row[9]) : $row[9];
    $ftppass = $magicrt ? stripslashes($row[10]) : $row[10];
    $deftype = intval($row[11]);
    $defteam = intval($row[12]);
    $demoftppath = $magicrt ? stripslashes($row[13]) : $row[13];
    $multicheck = intval($row[14]);

    $noportx0 = $noport ? "" : "checked";
    $noportx1 = $noport ? "checked" : "";
    $passivex0 = $passive ? "" : "checked";
    $passivex1 = $passive ? "checked" : "";
    $alllogsx0 = $alllogs ? "" : "checked";
    $alllogsx1 = $alllogs ? "checked" : "";
    $defteamx0 = $defteam ? "" : "checked";
    $defteamx1 = $defteam ? "checked" : "";
    $multicheckx0 = $multicheck ? "" : "checked";
    $multicheckx1 = $multicheck ? "checked" : "";

    if ($logsets > 1)
      echo <<<EOF
    <tr>
      <td colspan="4">&nbsp;</td>
    </tr>

EOF;

    echo <<<EOF
    <tr>
      <td align="right" class="forms" title="Path to where log files are stored or downloaded to on stats server (relative or absolute)."><b>Log Path:</b></td>
      <td align="left" colspan="3" class="forms">
        <input type="text" name="logpath{$num}" value="$logpath" size="120" maxlength="200" class="forms">
      </td>
    </tr>
    <tr>
      <td align="right" class="forms" title="Path to where log files are backed up after successfully parsing.  Leave blank to not backup."><b>Backup Path:</b></td>
      <td align="left" colspan="3" class="forms">
        <input type="text" name="backuppath{$num}" value="$backuppath" size="120" maxlength="200" class="forms">
      </td>
    </tr>
    <tr>
      <td align="right" class="forms" title="Prefix of log files, before the timestamp or port number."><b>Log Prefix:</b></td>
      <td align="left" class="forms">
        <input type="text" name="prefix{$num}" value="$prefix" size="80" maxlength="60" class="forms">
      </td>
      <td align="right" class="forms" title="Set true if the log filenames do not contain the server port number."><b>No Port:</b></td>
      <td align="left" class="forms">
        <input type="radio" name="noport{$num}" value="0" $noportx0>False
        <input type="radio" name="noport{$num}" value="1" $noportx1>True
      </td>
    </tr>
    <tr>
      <td align="right" class="forms" title="FTP server address, including ftp:// or ftps// and port number (:21 default)."><b>FTP Server:</b></td>
      <td align="left" class="forms">
        <input type="text" name="ftpserver{$num}" value="$ftpserver" size="80" maxlength="100" class="forms">
      </td>
      <td align="right" class="forms" title="Set true to use FTP passive mode, false for active mode."><b>Passive Mode:</b></td>
      <td align="left" class="forms">
        <input type="radio" name="passive{$num}" value="0" $passivex0>False
        <input type="radio" name="passive{$num}" value="1" $passivex1>True
      </td>
    </tr>
    <tr>
      <td align="right" class="forms" title="FTP server username."><b>FTP User:</b></td>
      <td align="left" class="forms">
        <input type="text" name="ftpuser{$num}" value="$ftpuser" size="80" maxlength="30" class="forms">
      </td>
      <td align="right" class="forms" title="Set to default game type (0=Other,1=DM,2=CTF,3=BR,4=TDM,5=AS,6=ONS,7=DD,8=MU,9=INV,10=LMS)."><b>Default Type:</b></td>
      <td align="left" class="forms">
        <input type="text" name="deftype{$num}" value="$deftype" size="4" maxlength="2" class="forms">
      </td>
    </tr>
    <tr>
      <td align="right" class="forms" title="FTP server password."><b>FTP Password:</b></td>
      <td align="left" class="forms">
        <input type="password" name="ftppass{$num}" value="$ftppass" size="80" maxlength="30" class="forms">
      </td>
      <td align="right" class="forms" title="Set true to have new game types default as team games."><b>Team Game:</b></td>
      <td align="left" class="forms">
        <input type="radio" name="defteam{$num}" value="0" $defteamx0>False
        <input type="radio" name="defteam{$num}" value="1" $defteamx1>True
      </td>
    </tr>
    <tr>
      <td align="right" class="forms" title="Path to logs on FTP server - relative to path at login."><b>FTP Path:</b></td>
      <td align="left" class="forms">
        <input type="text" name="ftppath{$num}" value="$ftppath" size="80" maxlength="200" class="forms">
      </td>
      <td align="right" class="forms" title="Set false to leave the most recent log on the server."><b>All Logs:</b></td>
      <td align="left" class="forms">
        <input type="radio" name="alllogs{$num}" value="0" $alllogsx0>False
        <input type="radio" name="alllogs{$num}" value="1" $alllogsx1>True
      </td>
    </tr>
    <tr>
      <td align="right" class="forms" title="Path to demorec logs on FTP server - relative to path at login."><b>Demorec Path:</b></td>
      <td align="left" class="forms">
        <input type="text" name="demoftppath{$num}" value="$demoftppath" size="80" maxlength="200" class="forms">
      </td>
      <td align="right" class="forms" title="Set true for UTStatsDB to handle calculating multi-kills."><b>Multi-check:</b></td>
      <td align="left" class="forms">
        <input type="radio" name="multicheck{$num}" value="0" $multicheckx0>False
        <input type="radio" name="multicheck{$num}" value="1" $multicheckx1>True
      </td>
    </tr>

EOF;
  }
  sql_free_result($result);

  echo <<<EOF
    <tr>
      <td colspan="4">
      	<br />
      	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="submit" name="Mode" value="Save" class="formsb">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="submit" name="Mode" value="Add" class="formsb">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;To delete a record: clear the log path field and save. Mouse-over headings for full descriptions.
      </td>
    </tr>
  </table>
</form>

EOF;

  menu_bottom();
}

//=============================================================================
//========== Query Configuration ==============================================
//=============================================================================
function queryconfig() {
  global $dbpre, $magicrt, $QueryUp, $QueryDown;

  menu_top();

  echo <<<EOF
<form action="admin.php" method="post">
  <input type="hidden" name="SaveType" value="Query">
  <table class="forms">
    <tr>
      <th width="5">&nbsp;</th>
      <th width="80">&nbsp;</th>
      <th align="left" class="header">
      	UTStatsDB Query Configuration
      </th>
    </tr>

EOF;

  $result = sql_query("SELECT num,server,port,type,password,link,spectators,bots FROM {$dbpre}configquery ORDER BY num");
  if (!$result) {
  	echo "Config database error!<br />\n";
    exit;
  }
  $queryset = 0;
  $numrows = sql_num_rows($result);
  while ($row = sql_fetch_row($result))
  {
    $queryset++;
    $num = intval($row[0]);
    $server = $magicrt ? stripslashes($row[1]) : $row[1];
    $port = intval($row[2]);
    $type = intval($row[3]);
    $password = $magicrt ? stripslashes($row[4]) : $row[4];
    $link = $magicrt ? stripslashes($row[5]) : $row[5];
    $spectators = intval($row[6]);
    $bots = intval($row[7]);

    $typex = array("", "", "");
    $typex[$type] = "checked";
    $spectatorsx0 = $spectators ? "" : "checked";
    $spectatorsx1 = $spectators ? "checked" : "";
    $botsx0 = $bots ? "" : "checked";
    $botsx1 = $bots ? "checked" : "";

    if ($queryset > 1)
      echo <<<EOF
    <tr>
      <td>&nbsp;</td>
    </tr>

EOF;

    echo "    <tr>\n";

    if ($queryset > 1)
      echo "      <td align=\"left\" class=\"forms\" title=\"Move server up on list\"><input type=\"image\" name=\"QueryUp\" value=\"$queryset\" class=\"formsb\" src=\"resource/move_up.gif\"></td>\n";
    else
      echo "      <td align=\"left\" class=\"forms\">&nbsp;</td>\n";

    echo <<<EOF
      <td align="right" class="forms" title="Server address (domain or IP) to query (excluding port)."><b>Query Server:</b></td>
      <td align="left" class="forms">
        <input type="text" name="server{$num}" value="$server" size="60" maxlength="200" class="forms">
      </td>
      <td align="right" class="forms" title="Query password for playerhashes query (ServerExt)."><b>Password:</b></td>
      <td align="left" class="forms">
        <input type="text" name="password{$num}" value="$password" size="25" maxlength="40" class="forms">
      </td>
    </tr>
    <tr>

EOF;

    if ($queryset < $numrows)
      echo "      <td align=\"left\" class=\"forms\" title=\"Move server down on list\"><input type=\"image\" name=\"QueryDown\" value=\"$queryset\" class=\"formsb\" src=\"resource/move_down.gif\"></td>\n";
    else
      echo "      <td align=\"left\" class=\"forms\">&nbsp;</td>\n";

    echo <<<EOF
      <td align="right" class="forms" title="Port number of server (game port)."><b>Game Port:</b></td>
      <td align="left" class="forms">
        <input type="text" name="port{$num}" value="$port" size="60" maxlength="60" class="forms">
      </td>
      <td align="right" class="forms" title="Query type (GameSpy or UT). Use UT style query if possible."><b>Query Type:</b></td>
      <td align="left" class="forms">
    <input type="radio" name="type{$num}" value="0" {$typex[0]}>Unreal
    <input type="radio" name="type{$num}" value="1" {$typex[1]}>Game Spy
    <input type="radio" name="type{$num}" value="2" {$typex[2]}>UT 99
      </td>
    </tr>
    <tr>
      <td align="right" class="forms" title="Full URL to game server." colspan="2"><b>Server Link:</b></td>
      <td align="left" colspan="3" class="forms">
        <input type="text" name="link{$num}" value="$link" size="120" maxlength="200" class="forms">
      </td>
    </tr>
    <tr>
      <td align="right" class="forms" title="Include spectators in server query status." colspan="2"><b>Spectators:</b></td>
      <td align="left" class="forms">
        <input type="radio" name="spectators{$num}" value="0" $spectatorsx0>Disable
        <input type="radio" name="spectators{$num}" value="1" $spectatorsx1>Enable
      </td>
      <td align="right" class="forms" title="Include bots in server query status."><b>Bots:</b></td>
      <td align="left" class="forms">
        <input type="radio" name="bots{$num}" value="0" $botsx0>Disable
        <input type="radio" name="bots{$num}" value="1" $botsx1>Enable
      </td>
    </tr>

EOF;
  }
  sql_free_result($result);

  if ($queryset > 1)
    $qstxt = "To delete a record: clear the query server field and save.<br />Mouse-over headings for full descriptions. Click on arrows to modify display order.";
  else
    $qstxt = "To delete a record: clear the query server field and save. Mouse-over headings for full descriptions.";

  echo <<<EOF
    <tr>
      <td colspan="4">
        <table cellspacing="0" cellpadding="10">
          <tr>
            <td><input type="submit" name="Mode" value="Save" class="formsb"></td>
            <td><input type="submit" name="Mode" value="Add" class="formsb"></td>
            <td>{$qstxt}</td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</form>

EOF;

  menu_bottom();
}

//=============================================================================
//========== Menu Configuration ===============================================
//=============================================================================
function menuconfig() {
  global $dbpre, $magicrt;

  menu_top();

  echo <<<EOF
<form action="admin.php" method="post">
  <input type="hidden" name="SaveType" value="Menu">
  <table class="forms">
    <tr>
      <th width="60">&nbsp;</th>
      <th align="left" class="header">
      	UTStatsDB Menu Configuration
      </th>
    </tr>

EOF;

  $result = sql_query("SELECT num,url,descr FROM {$dbpre}configmenu ORDER BY num");
  if (!$result) {
  	echo "Config database error!<br />\n";
    exit;
  }
  $menusets = 0;
  while ($row = sql_fetch_row($result))
  {
    $menusets++;
    $num = intval($row[0]);
    $url = $magicrt ? stripslashes($row[1]) : $row[1];
    $urls = htmlspecialchars($url);
    $descr = $magicrt ? stripslashes($row[2]) : $row[2];
    $descrs = htmlspecialchars($descr);

    if ($menusets > 1)
      echo <<<EOF
    <tr>
      <td>&nbsp;</td>
    </tr>

EOF;

    echo <<<EOF
    <tr>
      <td align="right" class="forms" title="Full or relative URL."><b>URL:</b></td>
      <td align="left" colspan="3" class="forms">
        <input type="text" name="url{$num}" value="$urls" size="120" maxlength="200" class="forms">
      </td>
    </tr>
    <tr>
      <td align="right" class="forms" title="Description to display on menu."><b>Description:</b></td>
      <td align="left" colspan="3" class="forms">
        <input type="text" name="descr{$num}" value="$descrs" size="120" maxlength="200" class="forms">
      </td>
    </tr>

EOF;
  }
  sql_free_result($result);

  echo <<<EOF
    <tr>
      <td colspan="4">
      	<br />
      	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="submit" name="Mode" value="Save" class="formsb">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="submit" name="Mode" value="Add" class="formsb">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;To delete a record: clear the URL field and save.
      </td>
    </tr>
  </table>
</form>

EOF;

  menu_bottom();
}

//=============================================================================
//========== Save Config ======================================================
//=============================================================================
function saveconfig() {
  global $dbpre, $magic, $magicrt;

  $saves = 0;
  $savevalues = array();
  $apchange = 0;

  $link = sql_connect();

  $SaveType = check_post("SaveType");
  switch ($SaveType) {
    case "Main":
    {
      $result = sql_queryn($link, "SELECT num,conf,type,value FROM {$dbpre}config");
      if (!$result) {
  	    echo "Config database error!<br />\n";
  	    sql_close($link);
        exit;
      }
      while ($row = sql_fetch_row($result))
      {
  	    $num = intval($row[0]);
      	$option = $row[1];
      	$type = intval($row[2]);
  	    $value = $magicrt ? stripslashes($row[3]) : $row[3];

        if (isset($_POST["$option"])) {
          $newval = $magic ? stripslashes($_POST["$option"]) : $_POST["$option"];
          $valid = 1;
          if (substr($type, 0, 1) == "i") {
            $len = intval(substr($type, 1));
            if (strlen($newval) >= pow(10, $len))
              $valid = 0;
          }
          else if (substr($type, 0, 1) == "b") {
            $len = intval(substr($type, 1));
            if ($newval > $len - 1 || $newval < 0)
              $valid = 0;
          }
          if ($option == "layout" && $newval < 1)
            $valid = 0;
          if ($valid && $value != $newval) {
            $savevalues[$saves][0] = $num;
            $savevalues[$saves++][1] = $newval;
            if ($option == "AdminPass")
              $apchange = 1;
          }
        }
      }
      sql_free_result($result);

      for ($i = 0; $i < $saves; $i++)
        sql_queryn($link, "UPDATE {$dbpre}config SET value='{$savevalues[$i][1]}' WHERE num={$savevalues[$i][0]}");

      if (isset($_POST["title_msg"])) {
  	    $newval = $magic ? stripslashes($_POST["title_msg"]) : $_POST["title_msg"];
        $result = sql_queryn($link, "SELECT title_msg FROM {$dbpre}configset LIMIT 1");
        if (!$result) {
  	      echo "Config database error!<br />\n";
          exit;
        }
        $row = sql_fetch_row($result);
        sql_free_result($result);
        $value = $row[0];

		// unhtmlspecialchars
        if ($value != $newval)
          sql_queryn($link, "UPDATE {$dbpre}configset SET title_msg='$newval' WHERE cnfs_num=0");
      }
      break;
    }
    case "Logs":
    {
      $resultb = sql_querynb($link, "SELECT num,logpath,backuppath,prefix,noport,ftpserver,ftppath,passive,alllogs,ftpuser,ftppass,deftype,defteam,demoftppath,multicheck FROM {$dbpre}configlogs ORDER BY num");
      if (!$resultb) {
  	    echo "Config database error!<br />\n";
  	    sql_close($link);
        exit;
      }
      while ($row = sql_fetch_row($resultb))
      {
        $num = intval($row[0]);
        $logpath = $magicrt ? stripslashes($row[1]) : $row[1];
        $backuppath = $magicrt ? stripslashes($row[2]) : $row[2];
        $prefix = $magicrt ? stripslashes($row[3]) : $row[3];
        $noport = intval($row[4]);
        $ftpserver = $magicrt ? stripslashes($row[5]) : $row[5];
        $ftppath = $magicrt ? stripslashes($row[6]) : $row[6];
        $passive = intval($row[7]);
        $alllogs = intval($row[8]);
        $ftpuser = $magicrt ? stripslashes($row[9]) : $row[9];
        $ftppass = $magicrt ? stripslashes($row[10]) : $row[10];
        $deftype = intval($row[11]);
        $defteam = intval($row[12]);
        $demoftppath = $magicrt ? stripslashes($row[13]) : $row[13];
        $multicheck = intval($row[14]);

        if (isset($_POST["logpath{$num}"]) && isset($_POST["backuppath{$num}"]) && isset($_POST["prefix{$num}"]) &&
            isset($_POST["noport{$num}"]) && isset($_POST["ftpserver{$num}"]) && isset($_POST["ftppath{$num}"]) &&
            isset($_POST["ftppath{$num}"]) && isset($_POST["passive{$num}"]) && isset($_POST["alllogs{$num}"]) &&
            isset($_POST["ftpuser{$num}"]) && isset($_POST["ftppass{$num}"]) && isset($_POST["deftype{$num}"]) &&
            isset($_POST["defteam{$num}"]) && isset($_POST["demoftppath{$num}"]) && isset($_POST["multicheck{$num}"]))
        {
          $newlogpath = $magic ? stripslashes($_POST["logpath{$num}"]) : $_POST["logpath{$num}"];
          $newbackuppath = $magic ? stripslashes($_POST["backuppath{$num}"]) : $_POST["backuppath{$num}"];
          $newprefix = $magic ? stripslashes($_POST["prefix{$num}"]) : $_POST["prefix{$num}"];
          $newnoport = intval($_POST["noport{$num}"]);
          $newftpserver = $magic ? stripslashes($_POST["ftpserver{$num}"]) : $_POST["ftpserver{$num}"];
          $newftppath = $magic ? stripslashes($_POST["ftppath{$num}"]) : $_POST["ftppath{$num}"];
          $newpassive = intval($_POST["passive{$num}"]);
          $newalllogs = intval($_POST["alllogs{$num}"]);
          $newftpuser = $magic ? stripslashes($_POST["ftpuser{$num}"]) : $_POST["ftpuser{$num}"];
          $newftppass = $magic ? stripslashes($_POST["ftppass{$num}"]) : $_POST["ftppass{$num}"];
          $newdeftype = intval($_POST["deftype{$num}"]);
          $newdefteam = intval($_POST["defteam{$num}"]);
          $newdemoftppath = $magic ? stripslashes($_POST["demoftppath{$num}"]) : $_POST["demoftppath{$num}"];
          $newmulticheck = intval($_POST["multicheck{$num}"]);

          if ($logpath != $newlogpath || $backuppath != $newbackuppath || $prefix != $newprefix || $noport != $newnoport ||
              $ftpserver != $newftpserver || $ftppath != $newftppath || $passive != $newpassive || $alllogs != $newalllogs ||
              $ftpuser != $newftpuser || $ftppass != $newftppass || $deftype != $newdeftype || $defteam != $newdefteam ||
              $demoftppath != $newdemoftppath || $multicheck != $newmulticheck)
          {
            $newlogpath = addslashes($newlogpath);
            $newbackuppath = addslashes($newbackuppath);
            $newprefix = addslashes($newprefix);
            $newftpserver = addslashes($newftpserver);
            $newftppath = addslashes($newftppath);
            $newftpuser = addslashes($newftpuser);
            $newftppass = addslashes($newftppass);
            $newdemoftppath = addslashes($newdemoftppath);
            if ($newlogpath == "")
              sql_queryn($link, "DELETE FROM {$dbpre}configlogs WHERE num=$num");
            else
              sql_queryn($link, "UPDATE {$dbpre}configlogs SET logpath='$newlogpath',backuppath='$newbackuppath',prefix='$newprefix',noport=$newnoport,ftpserver='$newftpserver',ftppath='$newftppath',passive=$newpassive,alllogs=$newalllogs,ftpuser='$newftpuser',ftppass='$newftppass',deftype=$newdeftype,defteam=$newdefteam,demoftppath='$newdemoftppath',multicheck=$newmulticheck WHERE num=$num");
          }
        }
      }
      sql_free_result($resultb);
      break;
    }
    case "Query":
    {
      $resultb = sql_querynb($link, "SELECT num,server,port,type,password,link,spectators,bots FROM {$dbpre}configquery ORDER BY num");
      if (!$resultb) {
  	    echo "Config database error!<br />\n";
  	    sql_close($link);
        exit;
      }
      while ($row = sql_fetch_row($resultb))
      {
        $num = intval($row[0]);
        $server = $magicrt ? stripslashes($row[1]) : $row[1];
        $port = intval($row[2]);
        $type = intval($row[3]);
        $password = $magicrt ? stripslashes($row[4]) : $row[4];
        $slink = $magicrt ? stripslashes($row[5]) : $row[5];
        $spectators = intval($row[6]);
        $bots = intval($row[7]);

        if (isset($_POST["server{$num}"]) && isset($_POST["port{$num}"]) && isset($_POST["type{$num}"]) &&
            isset($_POST["password{$num}"]) && isset($_POST["link{$num}"]) &&
            isset($_POST["spectators{$num}"]) && isset($_POST["bots{$num}"]))
        {
          $newserver = $magic ? stripslashes($_POST["server{$num}"]) : $_POST["server{$num}"];
          $newport = intval($_POST["port{$num}"]);
          $newtype = intval($_POST["type{$num}"]);
          $newpassword = $magic ? stripslashes($_POST["password{$num}"]) : $_POST["password{$num}"];
          $newlink = $magic ? stripslashes($_POST["link{$num}"]) : $_POST["link{$num}"];
          $newspectators = intval($_POST["spectators{$num}"]);
          $newbots = intval($_POST["bots{$num}"]);

          if ($server != $newserver || $port != $newport || $type != $newtype || $password != $newpassword || 
              $slink != $newlink || $spectators != $newspectators || $bots != $newbots)
          {
            if ($newserver == "")
              sql_queryn($link, "DELETE FROM {$dbpre}configquery WHERE num=$num");
            else
              sql_queryn($link, "UPDATE {$dbpre}configquery SET server='$newserver',port=$newport,type=$newtype,password='$newpassword',link='$newlink',spectators=$newspectators,bots=$newbots WHERE num=$num");
          }
        }
      }
      sql_free_result($resultb);
      break;
    }
    case "Menu":
    {
      $resultb = sql_querynb($link, "SELECT num,url,descr FROM {$dbpre}configmenu ORDER BY num");
      if (!$resultb) {
  	    echo "Config database error!<br />\n";
  	    sql_close($link);
        exit;
      }
      while ($row = sql_fetch_row($resultb))
      {
        $num = intval($row[0]);
        $url = $magicrt ? stripslashes($row[1]) : $row[1];
        $descr = $magicrt ? stripslashes($row[2]) : $row[2];

        if (isset($_POST["url{$num}"]) && isset($_POST["descr{$num}"]))
        {
          $newurl = $magic ? stripslashes($_POST["url{$num}"]) : $_POST["url{$num}"];
          $newdescr = $magic ? stripslashes($_POST["descr{$num}"]) : $_POST["descr{$num}"];

          if ($url != $newurl || $descr != $newdescr)
          {
            if ($newurl == "")
              sql_queryn($link, "DELETE FROM {$dbpre}configmenu WHERE num=$num");
            else {
              $newurl = sql_addslashes($newurl);
              $newdescr = sql_addslashes($newdescr);
              sql_queryn($link, "UPDATE {$dbpre}configmenu SET url='$newurl',descr='$newdescr' WHERE num=$num");
            }
          }
        }
      }
      sql_free_result($resultb);
      break;
    }
  }

  sql_close($link);

  if ($apchange)
    login();
}

//=============================================================================
//========== Add Config =======================================================
//=============================================================================
function addconfig() {
  global $dbpre;

  $SaveType = check_post("SaveType");

  if ($SaveType == "Logs") {
    sql_query("INSERT INTO {$dbpre}configlogs (logpath,prefix) VALUES('./Logs/','Stats_')");
    logsconfig();
  }

  if ($SaveType == "Query") {
    sql_query("INSERT INTO {$dbpre}configquery (server,link) VALUES('127.0.0.1','ut2004://localhost')");
    queryconfig();
  }

  if ($SaveType == "Menu") {
    sql_query("INSERT INTO {$dbpre}configmenu (url,descr) VALUES('http://localhost','New Link')");
    menuconfig();
  }
}

//=============================================================================
//========== Modify Query Order ===============================================
//=============================================================================
function movequery() {
  global $dbpre, $QueryUp, $QueryDown;

  $SaveType = check_post("SaveType");

  // Make sure this was called with legitimate values
  if ($SaveType != "Query" || ($QueryUp > 0 && $QueryDown > 0))
    mainconfig();

  // Get current row positions
  $result = sql_query("SELECT num FROM {$dbpre}configquery");
  if (!$result) {
  	echo "Config database error!<br />\n";
    exit;
  }
  $numrows = sql_num_rows($result);
  $querynum = array();
  $count = 1;
  while (list($num) = sql_fetch_row($result))
    $querynum[$count++] = $num;
  sql_free_result($result);

  // Check for valid ranges
  if ($numrows < 2)
    mainconfig();
  if ($QueryUp > 0 && ($QueryUp < 2 || $QueryUp > $numrows))
    mainconfig();
  if ($QueryDown > $numrows - 1)
    mainconfig();

  if ($QueryUp > 0) {
    $moveval = $querynum[$QueryUp];
    $newval = $querynum[$QueryUp - 1];

    // Move temporarily
    $result = sql_query("UPDATE {$dbpre}configquery SET num=0 WHERE num=$moveval LIMIT 1");
    if (!$result) {
      echo "Config database error!<br />\n";
      exit;
    }

    // Move above query down
    $result = sql_query("UPDATE {$dbpre}configquery SET num=$moveval WHERE num=$newval LIMIT 1");
    if (!$result) {
      echo "Config database error!<br />\n";
      exit;
    }

    // Move to new location
    $result = sql_query("UPDATE {$dbpre}configquery SET num=$newval WHERE num=0");
    if (!$result) {
      echo "Config database error!<br />\n";
      exit;
    }
  }
  else if ($QueryDown > 0) {
    $moveval = $querynum[$QueryDown];
    $newval = $querynum[$QueryDown + 1];

    // Move temporarily
    $result = sql_query("UPDATE {$dbpre}configquery SET num=0 WHERE num=$moveval LIMIT 1");
    if (!$result) {
      echo "Config database error!<br />\n";
      exit;
    }

    // Move below query up
    $result = sql_query("UPDATE {$dbpre}configquery SET num=$moveval WHERE num=$newval LIMIT 1");
    if (!$result) {
      echo "Config database error!<br />\n";
      exit;
    }

    // Move to new location
    $result = sql_query("UPDATE {$dbpre}configquery SET num=$newval WHERE num=0");
    if (!$result) {
      echo "Config database error!<br />\n";
      exit;
    }
  }

  queryconfig();
}

?>