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

function SendQuery($ip, $port, $query)
{
  global $bytes_read;

  $data = "";
  $bytes_read = 0;
  if (($fs = fsockopen("udp://$ip", $port, $errno, $errstr, 3)) === FALSE)
    return $data;
  else {
    if (function_exists('stream_set_blocking'))
      stream_set_blocking($fs, TRUE);
    else if (function_exists('socket_set_blocking'))
      socket_set_blocking($fs, TRUE);
    else
      set_socket_blocking($fs, TRUE);
    socket_set_timeout($fs, 1, 0);
    if (fwrite($fs, $query) < 0) {
      fclose($fs);
      return $data;
    }

    $packets = array();
    $done = $final = 0;
    $qstart = time() + 5;
    while (!$done) {
      $received = 0;
      do {
        $data .= fgetc($fs);
        $received++;
        if (function_exists('stream_get_meta_data'))
          $status = stream_get_meta_data($fs);
        else
          $status = socket_get_status($fs);
      } while($status["unread_bytes"]);
      if ($received == 1)
        $received = 0;

      if (substr($query, 0, 1) != "\\") {
      	$bytes_read = $received;
        $done = 1;
      }
      else {
        if ($received) {
          $bytes_read += $received;
          if (substr($data, -7) == "\\final\\") {
            if (substr($data, -9, 1) == ".")
              $packet_id = intval(substr($data, -8, 1));
            else
              $packet_id = intval(substr($data, -9, 2));
            $packets[$packet_id] = 2;
            $final = $packet_id;
            if ($packet_id == 1)
              $done = 1;
          }
          else {
            if (substr($data, -2, 1) == ".")
              $packet_id = intval(substr($data, -1, 1));
            else
              $packet_id = intval(substr($data, -2, 2));
            $packets[$packet_id] = 1;
          }
          if (!$done) {
            if (!$packet_id) // Error in packet
              $done = 1;
            else if ($final) {
              // Check to make sure all packets have been received
              $ok = 1;
              for ($i = 1; $i < $final; $i++)
                if (!isset($packets[$i]) || !$packets[$i])
                  $ok = 0;
              if ($ok)
                $done = 1;
            }
          }
        }
        else
          $done = 1;
        if (!$done && time() > $qstart) // Timeout
          $done = 1;
      }
    }
    fclose($fs);
  }
  return $data;
}

function ParseQuery(&$data, &$param, &$val, &$num)
{
  $num = -1;
  if (($loc = strpos($data, "\\")) === FALSE)
    $data = "";
  else {
    if ($loc == 0) {
      $param = substr($data, 1);
      if (($loc2 = strpos($param, "\\")) === FALSE)
        $data = $param = $val = "";
      else {
        $val = substr($param, $loc2 + 1);
        $param = strtolower(substr($param, 0, $loc2));
        if (!strcmp($param, "final") || !strcmp($param, "echo") || !strcmp($param, "echo_reply") || ($loc3 = strpos($val, "\\")) === FALSE) {
          $data = $param = $val = "";
        }
        else {
          $data = substr($data, $loc + $loc2 + $loc3 + 2);
          $val = substr($val, 0, $loc3);
          if (($us = strpos($param, "_")) !== FALSE) {
            $num = (int) substr($param, $us + 1);
            $param = substr($param, 0, $us);
          }
        }
      }
    }
    else
      $data = $param = $val = "";
  }
  if ($param != "" && $val != "")
    $ok = 1;
  else
    $ok = 0;
  return $ok;
}

function GetStatus($ip, $port)
{
  global $query_type, $query_password, $sq_server, $sq_player, $sq_team, $sq_spect, $sq_bot, $bytes_read;
  global $query_spectators, $query_bots, $teams, $teamcount;

  $ok = 0;
  $sq_server = array();
  $sq_player = array(array());
  $sq_server["ip"] = $ip;
  $port = intval($port);
  $sq_team = array(array());
  $sq_spect = array(array());
  $sq_bot = array(array());
  $teams = 0;
  $teamcount = array();
  for ($i = 0; $i < 4; $i++)
    $teamcount[$i] = 0;

  if ($query_type == 1) {
    // Server Info
    $data = SendQuery($ip, $port + 1, "\x7f\x00\x00\x00\x00");
    $len2 = 0;
    if ($bytes_read < 32)
      return 0;

    $sq_server["hostport"] = ord($data[11]) * 256 + ord($data[10]);
    for ($i = 0; $i < 3; $i++) {
      $len = ord($data[$len2 + 18 + $i]);
      if ($len) {
        $val = substr($data, $len2 + $i + 19, $len - 1);
        $len2 += $len;
        switch ($i) {
          case 0:
            $sq_server["hostname"] = stripspecialchars($val);
            break;
          case 1:
            $sq_server["mapname"] = stripspecialchars($val);
            break;
          case 2:
            switch (strtolower($val)) {
              case "asgameinfo": // ASGameInfo
              case "logasgameinfo":
                $val = "Assault";
                break;
              case "xbombingrun": // xBombingRun
              case "logbombingrun":
                $val = "Bombing Run";
                break;
              case "xctfgame": // xCTFGame
              case "logctfgame":
                $val = "Capture The Flag";
                break;
              case "xdeathmatch": // xDeathMatch
              case "logdeathmatch":
                $val = "DeathMatch";
                break;
              case "xdoubledom": // xDoubleDom
              case "logdoubledom":
                $val = "Double Domination";
                break;
              case "invasion": // Invasion
              case "loginvasion":
                $val = "Invasion";
                break;
              case "xlastmanstandinggame": // xLastManStandingGame
              case "logxlastmanstandinggame":
                $val = "Last Man Standing";
                break;
              case "xmutantgame": // xMutantGame
              case "logxmutantgame":
                $val = "Mutant";
                break;
              case "onsonslaughtgame": // ONSOnslaughtGame
              case "logonsonslaughtgame":
                $val = "Onslaught";
                break;
              case "xteamgame": // xTeamGame
              case "logteamgame":
                $val = "Team DeathMatch";
                break;
              case "xvehiclectfgame": // xVehicleCTFGame
              case "logxvehiclectfgame":
                $val = "Vehicle CTF";
                break;
            }
            $sq_server["gametype"] = $val;
            break;
        }
      }
    }
    $sq_server["numplayers"] = ord($data[$len2 + 21]);
    $sq_server["maxplayers"] = ord($data[$len2 + 25]);

    // Game Info
    $data = SendQuery($ip, $port + 1, "\x7f\x00\x00\x00\x01");
    $len2 = $i = 0;
    while ($len2 < $bytes_read - 2 && $len2 + $i + 7 < strlen($data)) {
      $ok = 1;
      $len = ord($data[$len2 + $i + 5]);
      if ($len) {
        $param = strtolower(substr($data, $len2 + $i + 6, $len - 1));
        $len2 += $len;

        $len = ord($data[$len2 + $i + 6]);
        if ($len) {
          $val = substr($data, $len2 + $i + 7, $len - 1);
          $len2 += $len;
          switch ($param) {
            case "servermode":
            case "adminname":
              $val = stripspecialchars($val);
              break;
            case "goalscore":
            case "timelimit":
              if (!$val)
                $val = "None";
              break;
            case "gamestats":
            case "translocator":
            case "mapvoting":
            case "kickvoting":
              if (!strcasecmp($val, "true") || $val == "1")
                $val = "Enabled";
              else
                $val = "Disabled";
              break;
            case "gamever":
            case "serverversion":
              $param = "version";
              $val = intval($val);
              break;
            case "mutator":
              $val = stripspecialchars($val);
              if (strtolower(substr($val, 0, 3)) == "mut")
                $val = substr($val, 3);
              if (isset($sq_server["mutator"]))
                $val = $sq_server["mutator"].", ".$val;
              break;
          }
          $sq_server[$param] = $val;
        }
      }
      $i += 2;
    }

    if (!isset($sq_server["mutator"]))
      $sq_server["mutator"] = "None";

    // Player Info
    $data = SendQuery($ip, $port + 1, "\x7f\x00\x00\x00\x02");
    $len2 = 0;
    $num = 0;
    while ($len2 < $bytes_read - 21) {
      $num++;
      $len = ord($data[$len2 + 9]);
      if ($len > 0) {
        $sq_player[$num]["player"] = stripspecialchars(substr($data, $len2 + 10, $len - 1));
        $sq_player[$num]["ping"] = ord($data[$len2 + $len + 11]) * 256 + ord($data[$len2 + $len + 10]);
        $sq_player[$num]["score"] = ord($data[$len2 + $len + 15]) * 256 + ord($data[$len2 + $len + 14]);
      }
      $len2 += $len + 17;
    }
  }
  else {
    if ($query_type == 2)
      $qport = $port + 1;
    else
      $qport = $port + 10;

    $data = SendQuery($ip, $qport, "\\basic\\\\info\\\\rules\\\\gamestatus\\\\echo\\nothing");
    while (strlen($data)) {
      $ok = 1;
      if (ParseQuery($data, $param, $val, $num)) {
        switch ($param) {
          case "gametype":
            switch (strtolower($val)) {
              case "asgameinfo": // ASGameInfo
              case "logasgameinfo":
                $val = "Assault";
                break;
              case "xbombingrun": // xBombingRun
              case "logbombingrun":
                $val = "Bombing Run";
                break;
              case "xctfgame": // xCTFGame
              case "logctfgame":
                $val = "Capture The Flag";
                break;
              case "xdeathmatch": // xDeathMatch
              case "logdeathmatch":
                $val = "DeathMatch";
                break;
              case "xdoubledom": // xDoubleDom
              case "logdoubledom":
                $val = "Double Domination";
                break;
              case "invasion": // Invasion
              case "loginvasion":
                $val = "Invasion";
                break;
              case "xlastmanstandinggame": // xLastManStandingGame
              case "logxlastmanstandinggame":
                $val = "Last Man Standing";
                break;
              case "xmutantgame": // xMutantGame
              case "logxmutantgame":
                $val = "Mutant";
                break;
              case "onsonslaughtgame": // ONSOnslaughtGame
              case "logonsonslaughtgame":
                $val = "Onslaught";
                break;
              case "xteamgame": // xTeamGame
              case "logteamgame":
                $val = "Team DeathMatch";
                break;
              case "xvehiclectfgame": // xVehicleCTFGame
              case "logxvehiclectfgame":
                $val = "Vehicle CTF";
                break;
            }
            break;
          case "mapname":
          case "maptitle":
          case "adminname":
          case "adminemail":
          case "nextmap":
            $val = stripspecialchars($val);
            break;
          case "timelimit":
          case "goalscore":
            if (!$val)
              $val = "None";
            break;
          case "balanceteams":
          case "gamestats":
          case "translocator":
          case "mapvoting":
          case "kickvoting":
            if (!strcasecmp($val, "true") || $val == "1")
              $val = "Enabled";
            else
              $val = "Disabled";
            break;
          case "overtime":
            if (!strcasecmp($val, "true") || $val == "1")
              $val = "True";
            else
              $val = "False";
            break;
          case "password":
          case "gamepassword":
            if (!strcasecmp($val, "true") || $val == "1")
              $val = "Required";
            else
              $val = "None";
            break;
          case "elapsedtime":
            $val = sprintf("%0.1f", $val / 60.0);
            break;
          case "mutator":
            if (strtolower(substr($val, 0, 3)) == "mut")
              $val = substr($val, 3);
            $val = stripspecialchars($val);
            if (isset($sq_server["mutator"]))
              $val = $sq_server["mutator"].", ".$val;
            break;
          case "hostport":
            $val = intval($val);
            break;
          case "gamever":
          case "serverversion":
            $param = "version";
            $val = intval($val);
            break;
        }
        $sq_server[$param] = $val;
      }
    }

    if (!isset($sq_server["mutator"]))
      $sq_server["mutator"] = "None";

    $query_string = "\\players\\\\olstatsids\\\\playerhashes_$query_password\\";
    if ($query_spectators)
      $query_string .= "\\spectators\\";
    if ($query_bots)
      $query_string .= "\\bots\\";
    $query_string .= "\\echo\\nothing";

	$lastparam = "";
    $data = SendQuery($ip, $qport, $query_string);
    while (strlen($data)) {
      if (ParseQuery($data, $param, $val, $num)) {
        if ($num >= 0) {
          if ($param == "player" || $param == "psidname" || $param == "phname" || $param == "spectator" || $param == "bot")
            $lastparam = $param;
          switch ($lastparam) {
            case "player":
              if ($param == "player")
                $val = stripspecialchars($val);
              $sq_player[$num][$param] = $val;
              if ($param == "team") {
              	$teamcount[$val]++;
              	if ($val > $teams)
                  $teams = $val;
              }
              break;
            case "spectator":
              if ($param == "spectator")
                $val = stripspecialchars($val);
              $sq_spect[$num][$param] = $val;
              break;
            case "bot":
              if ($param == "bot")
                $val = stripspecialchars($val);
              $sq_bot[$num][$param] = $val;
              if ($param == "team") {
              	$teamcount[$val]++;
              	if ($val > $teams)
                  $teams = $val;
              }
              break;
            case "psidname":
              if ($param == "psidname" || $param == "pstatsid")
                $sq_player[$num][$param] = $val;
              break;
            case "phname": // \phname_<id>\<player name>\phash_<id>\<player id hash>\phip_<id>\<player IP>\
              if ($param == "psidname" || $param == "pstatsid")
                $sq_player[$num][$param] = $val;
              break;
          }
        }
      }
    }

	$lastparam = "";
    $data = SendQuery($ip, $qport, "\\teams\\");
    while (strlen($data)) {
      if (ParseQuery($data, $param, $val, $num)) {
        if ($num >= 0) {
          if ($param == "team")
            $val = stripspecialchars($val);
          $sq_team[$num][$param] = $val;
        }
      }
    }
  }

  return $ok;
}

function DisplayStatus($query_link)
{
  global $sq_server, $sq_team, $query_spectators, $query_bots, $teams, $teamcount, $query_type;

  // Strip :// from link address
  $pos = strpos($query_link, "://");
  if ($pos !== FALSE)
    $displaylink = substr($query_link, $pos + 3);
  else
    $displaylink = "";

  // Check for port / password
  $pos = strpos($displaylink, ":");
  if ($pos !== FALSE) {
    $pos2 = strpos($displaylink, "?");
    if ($pos2) {
      $displaylink = substr($displaylink, 0, $pos2);
      if (strlen($displaylink) > 1 && $displaylink[strlen($displaylink) - 1] == "/")
        $displaylink = substr($displaylink, 0, -1);
    }
  }
  else {
    $displaylink.= ":{$sq_server['hostport']}";
    $query_link.= ":{$sq_server['hostport']}";
  }

  if (isset($sq_server["version"]))
    $version = $sq_server["version"];
  if (isset($sq_server["password"]))
    $password = $sq_server["password"];
  else
    $password = "None";
  if (isset($sq_server["gamepassword"]))
    $password = $sq_server["gamepassword"];
  if (isset($sq_server["translocator"]))
    $translocator = $sq_server["translocator"];
  if (isset($sq_server["timelimit"]))
    $timelimit = $sq_server["timelimit"];
  if (isset($sq_server["elapsedtime"]))
    $elapsedtime = $sq_server["elapsedtime"];
  if (isset($sq_server["overtime"]))
    $overtime = $sq_server["overtime"];
  if (isset($sq_server["friendlyfire"]))
    $friendlyfire = $sq_server["friendlyfire"];
  else
    $friendlyfire = "n/a";
  if (isset($sq_server["friendlyfirescale"]))
    $friendlyfire = $sq_server["friendlyfirescale"];
  else
    $friendlyfire = "n/a";
  if (isset($sq_server["balanceteams"]))
    $balanceteams = $sq_server["balanceteams"];
  else
    $balanceteams = "n/a";

  // Strip parameters after map name
  if (isset($sq_server["nextmap"])) {
    $nextmap = $sq_server["nextmap"];
    if ($loc = strpos($nextmap, "?"))
      $nextmap = substr($nextmap, 0, $loc);
  }

  $display_map = false;
  $mapimage = strtolower($sq_server["mapname"]).".jpg";
  if (file_exists("mapimages/$mapimage"))
    $display_map = true;
  else {
    $mapimage = strtolower($sq_server["mapname"]).".gif";
    if (file_exists("mapimages/$mapimage"))
      $display_map = true;
  }

  echo <<<EOF
<br />
<table cellspacing="0" cellpadding="0" border="0">
  <tr>
    <td>

EOF;

  if ($query_type) {
  	if ($display_map)
      include("templates/serverquery-gamespymap.php");
    else
      include("templates/serverquery-gamespy.php");
  }
  else if (isset($sq_server["minplayers"])) {
  	if ($display_map)
      include("templates/serverquery-extendedmap.php");
    else
      include("templates/serverquery-extended.php");
  }
  else {
  	if ($display_map)
      include("templates/serverquery-basicmap.php");
    else
      include("templates/serverquery-basic.php");
  }

  echo <<<EOF
    </td>
  </tr>
  <tr>
    <td>

EOF;

  if (isset($sq_team[1])) {
    if (isset($sq_server["minplayers"]))
      $ncol = 5;
    else
      $ncol = 3;

    echo <<<EOF
  <tr>
    <td colspan="5">
      <table class="status" cellspacing="0" cellpadding="1" width="100%">
        <tr>
          <td class="redteambar" align="center" colspan="$ncol">
            <b>{$sq_team[0]['team']} Team: &nbsp;&nbsp;Players: {$sq_team[0]['size']} &nbsp;&nbsp;Score: {$sq_team[0]['score']}</b>
          </td>
        </tr>

EOF;

    DisplayPlayers(1);
    echo <<<EOF
      </table>
    </td>
  </tr>
  <tr>
    <td colspan="5">
      <table class="status" cellspacing="0" cellpadding="1" width="100%">
        <tr>
          <td class="blueteambar" align="center" colspan="$ncol">
            <b>{$sq_team[1]['team']} Team: &nbsp;&nbsp;Players: {$sq_team[1]['size']} &nbsp;&nbsp;Score: {$sq_team[1]['score']}</b>
          </td>
        </tr>

EOF;
    DisplayPlayers(2);
    echo <<<EOF
      </table>
    </td>
  </tr>

EOF;
  }
  else if ($teams > 0) {
    echo <<<EOF
  <tr>
    <td colspan="5">
      <table class="status" cellspacing="0" cellpadding="1" width="100%">
        <tr>
          <td class="redteambar" align="center" colspan="5">
            <b>Red Team - Players: {$teamcount[0]}</b>
          </td>
        </tr>

EOF;

    DisplayPlayers(1);
    echo <<<EOF
      </table>
    </td>
  </tr>
  <tr>
    <td colspan="5">
      <table class="status" cellspacing="0" cellpadding="1" width="100%">
        <tr>
          <td class="blueteambar" align="center" colspan="5">
            <b>Blue Team - Players: {$teamcount[1]}</b>
          </td>
        </tr>

EOF;
    DisplayPlayers(2);
    echo <<<EOF
      </table>
    </td>
  </tr>

EOF;
  }
  else
    DisplayPlayers(0);

  if ($query_spectators)
    DisplaySpectators();

  echo <<<EOF
    </td>
  </tr>
</table>

EOF;
}

function DisplayDown($svr)
{
  echo <<<EOF
<br />
<table class="status" cellspacing="0" cellpadding="1" width="500">
  <tr>
    <td class="statustitle" align="center">
      <b>Current Status for $svr</b>
    </td>
  </tr>

  <tr>
    <td align="center">Server is <span class="offline">Offline</span></td>
  </tr>
</table>

EOF;
}

function DisplayPlayers($teamnum)
{
  global $query_type, $sq_server, $sq_player, $sq_team, $dbpre, $sq_bot, $query_bots;

  if (!isset($sq_player) || !sizeof($sq_player))
    return;

  $link = -1;
  if ($query_type) {
    // Sort by score
    $numplr = 0;
    foreach($sq_player as $plr) {
      if (isset($plr["player"])) {
      	if (isset($plr["player"])) {
      	  $name[] = $plr["player"];
          $score[] = $plr["score"];
          $ping[] = $plr["ping"];
          if ($score[$numplr] == 0xffff)
            $score[$numplr] = 0;
          $numplr++;
        }
      }
    }
    if ($numplr)
      array_multisort($score, SORT_NUMERIC, SORT_ASC, $ping);

    $header = 0;
    for ($i = 0; $i < $numplr; $i++) {
      if (!$header) {
    echo <<<EOF
      <table class="status" cellspacing="0" cellpadding="1" width="100%">
        <tr>
          <td class="statustitle" align="center" colspan="3">
            <b>Players</b>
          </td>
        </tr>
        <tr>
          <td width="200"><b>Name</b></td>
          <td width="50"><b>Score</b></td>
          <td width="50"><b>Ping</b></td>
        </tr>
EOF;
          $header = 1;
      }

      echo <<<EOF
        <tr>
          <td>$name[$i]</td>
          <td>$score[$i]</td>
          <td>$ping[$i]</td>
        </tr>
EOF;
    }
    echo "      </table>\n";
  }
  else {
    if ($teamnum)
      $type = "Score";
    else
      $type = "Frags";

    if (isset($sq_server["minplayers"]))
      $ncol = 4;
    else
      $ncol = 3;

    // Sort by score
    $numplr = 0;
    foreach($sq_player as $plr) {
      if (isset($plr["player"])) {
        $tempsort[] = $plr["frags"];
        $numplr++;
      }
    }
    if ($numplr)
      array_multisort($tempsort, SORT_ASC, $sq_player);

    $header = 0;
    foreach ($sq_player as $plr) {
      if (isset($plr["player"]) && (!$teamnum || (isset($plr["team"]) && $plr["team"] == $teamnum - 1))) {
        if (!$header) {
          if (!$teamnum) {
            echo <<<EOF
      <table class="status" cellspacing="0" cellpadding="1" width="100%">
        <tr>
          <td class="statustitle" align="center" colspan="$ncol">
            <b>Players</b>
          </td>
        </tr>

EOF;
          }
          echo <<<EOF
        <tr>
          <td width="200"><b>Name</b></td>
          <td width="50"><b>$type</b></td>

EOF;
          if (isset($sq_server["minplayers"])) {
            echo "          <td width=\"50\"><b>Deaths</b></td>\n";
            if ($teamnum)
              echo "          <td width=\"50\"><b>Scored</b></td>\n";
          }
          echo "          <td width=\"50\"><b>Ping</b></td>
        </tr>\n";
          $header = 1;
        }

        $player = $plr["player"];
        if (isset($plr["pstatsid"])) {
          if ($link < 0)
            $link = sql_connect();
          $pstatsid = explode("\t", $plr["pstatsid"], 3);
          $user = sql_addslashes($pstatsid[1]);
          $id = sql_addslashes($pstatsid[2]);
          $result = sql_queryn($link, "SELECT pnum FROM {$dbpre}players WHERE plr_user='$user' AND plr_id='$id' LIMIT 1");
          if ($result) {
            if (list($pnum) = sql_fetch_row($result))
              $player = "<a href=\"playerstats.php?player=$pnum\" class=\"status\">{$plr['player']}</a>";
            sql_free_result($result);
          }
        }

        echo <<<EOF
        <tr>
          <td>$player</td>
          <td>{$plr['frags']}</td>

EOF;
        if (isset($sq_server["minplayers"])) {
          echo "          <td>{$plr['deaths']}</td>\n";
          if ($teamnum)
            echo "          <td>{$plr['scored']}</td>\n";
        }
        echo "          <td>{$plr['ping']}</td>
        </tr>\n";
      }
    }
    if ($link >= 0)
      sql_close($link);

    if ($query_bots && isset($sq_bot) && sizeof($sq_bot) > 0) {
      // Sort by score
      $numbots = 0;
      $tempsort = array();
      foreach($sq_bot as $bot) {
        if (isset($bot["bot"])) {
          $tempsort[] = $bot["frags"];
          $numbots++;
        }
      }

      if ($numbots)
        array_multisort($tempsort, SORT_ASC, $sq_bot);

      foreach ($sq_bot as $bot) {
        if (isset($bot["bot"]) && (!$teamnum || (isset($bot["team"]) && $bot["team"] == $teamnum - 1))) {
          if (!$header) {
            if (!$teamnum) {
              echo <<<EOF
      <table class="status" cellspacing="0" cellpadding="1" width="100%">
        <tr>
          <td class="statustitle" align="center" colspan="$ncol">
            <b>Players</b>
          </td>
        </tr>

EOF;
            }
            echo <<<EOF
        <tr>
          <td width="200"><b>Name</b></td>
          <td width="50"><b>$type</b></td>

EOF;
            if (isset($sq_server["minplayers"])) {
              echo "          <td width=\"50\"><b>Deaths</b></td>\n";
              if ($teamnum)
                echo "          <td width=\"50\"><b>Scored</b></td>\n";
            }
            echo "          <td width=\"50\"><b>Ping</b></td>
        </tr>\n";
            $header = 1;
          }

          echo <<<EOF
        <tr>
          <td>{$bot['bot']}</td>
          <td>{$bot['frags']}</td>

EOF;
          if (isset($sq_server["minplayers"])) {
            echo "          <td>{$bot['deaths']}</td>\n";
            if ($teamnum)
              echo "          <td>{$bot['scored']}</td>\n";
          }
          echo "          <td>[bot]</td>
        </tr>\n";

        }
      }
    }

    if ($header && !$teamnum)
      echo <<<EOF
      </table>

EOF;
  }
}

function DisplaySpectators()
{
  global $sq_server, $sq_spect;

  if (isset($sq_spect) && sizeof($sq_spect) > 0) {
    $header = 0;
    foreach ($sq_spect as $spc) {
      if (isset($spc["spectator"])) {
        if (!$header) {
          echo <<<EOF
      <table class="status" cellspacing="0" cellpadding="1" width="100%">
        <tr>
          <td class="spectitle" align="center" colspan="2">
            <b>Spectators</b>
          </td>
        </tr>
        <tr>
          <td><b>Name</b></td>
          <td><b>Ping</b></td>
        </tr>

EOF;
          $header = 1;
        }

        echo <<<EOF
        <tr>
          <td>{$spc['spectator']}</td>
          <td>{$spc['specping']}</td>
        </tr>

EOF;
      }
    }
    if ($header) {
      echo <<<EOF
      </table>

EOF;
    }
  }
}

?>