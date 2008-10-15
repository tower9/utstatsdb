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

function InitQuery($ip, $port)
{
  if (($fs = fsockopen("udp://$ip", $port, $errno, $errstr, 3)) === FALSE)
    return false;

  if (function_exists('stream_set_blocking'))
    stream_set_blocking($fs, TRUE);
  else if (function_exists('socket_set_blocking'))
    socket_set_blocking($fs, TRUE);
  else
    set_socket_blocking($fs, TRUE);

  if (function_exists('stream_set_timeout'))
    stream_set_timeout($fs, 3, 0);
  else
    socket_set_timeout($fs, 3, 0);

  return $fs;
}

function SendQuery($fs, $query)
{
  global $bytes_read;

  $data = "";
  $bytes_read = 0;

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

  return $data;
}

function SendQuery3($fs, $query)
{
  global $bytes_read;

  $data = array();

  if (fwrite($fs, $query) < 0) {
    fclose($fs);
    return $data;
  }

  // Packets may not be received in correct order
  for ($i = 0, $lastpacket = -1; $i < 4; $i++) {
    $data[] = @fread($fs, 2048);

    if (strlen($data[$i]) < 19)
      break;

    if (substr($data[$i], -3) == "\x00\x00\x00")
      $lastpacket = ord(substr($data[$i], 15, 1));

    if ($lastpacket >= 0 && $i == $lastpacket)
      break;
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

  switch ($query_type) {
  	case 0: $port += 10; break;
  	case 1:
    case 2: $port++; break;
  }

  if (($fs = InitQuery($ip, $port)) == FALSE)
    return false;

  if ($query_type == 1) {
    // Server Info
    $data = SendQuery($fs, "\x7f\x00\x00\x00\x00");
    $len2 = 0;
    if ($bytes_read < 32)
    {
      fclose($fs);
      return false;
    }

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
    $data = SendQuery($fs, "\x7f\x00\x00\x00\x01");
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
    $data = SendQuery($fs, "\x7f\x00\x00\x00\x02");
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
  else if ($query_type == 0 || $query_type == 2) {
    $data = SendQuery($fs, "\\basic\\\\info\\\\rules\\\\gamestatus\\\\echo\\nothing");
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
    $data = SendQuery($fs, $query_string);
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
    $data = SendQuery($fs, "\\teams\\");
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
  else if ($query_type == 3) { // UT3
    $data = SendQuery($fs, "\xFE\xFD\x09\x11\x22\x33\x44\xFF\xFF\xFF\x01");
    if ($bytes_read < 6)
    {
      fclose($fs);
      return false;
    }
    
    $challenge = substr($data, 5);

    while (strlen($challenge) > 1 && $challenge[strlen($challenge) - 1] == 0)
      $challenge = substr($challenge, 0, strlen($challenge) - 1);
    if ($challenge < 0)
      $challenge = 4294967296 + $challenge;

    $qstring = sprintf("\xFE\xFD\x00\x11\x22\x33\x44%c%c%c%c\xFF\xFF\xFF\x01", $challenge >> 24, $challenge >> 16, $challenge >> 8, $challenge );

    $datax = SendQuery3($fs, $qstring);

    // Reorder packets
    $data = array();
    for ($i = 0; isset($datax[$i]); $i++) {
      $x = ord(substr($datax[$i], 15, 1));
      $data[$x] = $datax[$i];
    }

    if (strlen($data[0]) < 30)
    {
      fclose($fs);
      return false;
    }

    if (substr($data[0], -3) == "\x00\x00\x00")
      $data[0] = substr($data[0], 16, -2);
    else
      $data[0] = substr($data[0], 16);

    for ($i = 1; isset($data[$i]) && strlen($data[$i]) > 16; $i++) {
      if (substr($data[$i], -3) == "\x00\x00\x00")
        $data[$i] = substr($data[$i], 16, -2);
      else
        $data[$i] = substr($data[$i], 16);

      $p = strpos($data[$i], "\x00");
      if ($p < 3)
        break;

      $ar = substr($data[$i], 0, $p);
      $ap = ord(substr($data[$i], $p + 1, 1));
      $or = strpos($data[0], $ar);
      if ($or == FALSE) {
        // Find last 00 00 and insert after there.
        $op = strrpos($data[0], "\x00\x00"); // PHP 5!
        $data[0] = substr($data[0], 0, $op + 2);
        $data[0] .= $data[$i];
      }
      else {
        for ($x = 0, $op = $or; $x < $ap + 2; $x++) {
          $op = strpos($data[0], "\x00", $op + 1);
          if ($op == FALSE)
            break;
        }
  
        if ($op != FALSE) {
          $data[0] = substr($data[0], 0, $op + 1);
          $data[0] .= substr($data[$i], $p + 2);
        }
      }
    }

    $data[0] = substr($data[0], 0, -1); // Remove null from end
    $temp = explode("\x00\x00\x01", $data[0]);
    $data_main = explode("\x00\x00", $temp[0]);
    if (isset($temp[1])) {
      $temp = explode("\x00\x00\x02", $temp[1]);

      if (isset($temp[0]))
        $data_plr = explode("\x00\x00", $temp[0]);
      else
        $data_plr = "";

      if (isset($temp[1]))
        $data_team = explode("\x00\x00", $temp[1]);
      else
        $data_team = "";
    }
    else {
      $data_plr = "";
      $data_team = "";
    }

    if (count($data_main) < 2) {
      fclose($fs);
      return $ok;
    }

    $xmut = "";
    for ($i = 0; isset($data_main[$i]); $i++) {
      $data = explode("\x00", $data_main[$i]);

      for ($x = 0; isset($data[$x]) && isset($data[$x + 1]); $x += 2) {
        $ok = 1;
        $param = $data[$x];
        $val = $data[$x + 1];

        if ($param == "mapname")
        {
          $mdata = explode(",", $val);
          $md = 0;
          while (isset($mdata[$md])) {
            if (($eq = strpos($mdata[$md], "=")) !== FALSE) {
              $param = substr($mdata[$md], 0, $eq - 1);
              $val = substr($mdata[$md], $eq + 1);
            }
            $md++;
          }
        }
        else {
          switch ($param) {
            case "hostname": $sq_server["hostname"] = $val; break;
            case "hostport": $sq_server["hostport"] = $val; break;
            case "p1073741826":
              switch ($val) {
                case "UTGame.UTDeathmatch": $sq_server["gametype"] = "Deathmatch"; break;
                case "UTGameContent.UTCTFGame_Content": $sq_server["gametype"] = "CTF"; break;
                case "UTGameContent.UTOnslaughtGame_Content": $sq_server["gametype"] = "Warfare"; break;
                case "UTGameContent.UTVehicleCTFGame_Content": $sq_server["gametype"] = "Vehicle CTF"; break;
                case "UTGame.UTTeamGame": $sq_server["gametype"] = "Team Deathmatch"; break;
                case "UTGame.UTGame.UTDuelGame": $sq_server["gametype"] = "Duel"; break;
                default: $sq_server["gametype"] = $val;
              }
              break;
            case "p1073741825": $sq_server["mapname"] = $val; break;
            case "numplayers": $sq_server["numplayers"] = $val; break;
            case "maxplayers": $sq_server["maxplayers"] = $val; break;
            case "p268435704": $sq_server["goalscore"] = $val; break;
            case "p268435705": $sq_server["timelimit"] = $val; break;
            case "p268435703": $sq_server["numbots"] = $val; break;
            case "bUsesStats": $sq_server["gamestats"] = ($val ? "Enabled" : "Disabled"); break;
            case "gamemode": $sq_server["joininprogress"] = ($val == "openplaying" ? "Allowed" : "Disallowed"); break;
            case "NumOpenPublicConnections": $sq_server["playerslots"] = $val; break;
            case "s0":
              switch ($val) {
                case 1: $sq_server["botskill"] = "Novice"; break;
                case 2: $sq_server["botskill"] = "Average"; break;
                case 3: $sq_server["botskill"] = "Experienced"; break;
                case 4: $sq_server["botskill"] = "Skilled"; break;
                case 5: $sq_server["botskill"] = "Adept"; break;
                case 6: $sq_server["botskill"] = "Masterful"; break;
                case 7: $sq_server["botskill"] = "Inhuman"; break;
                case 8: $sq_server["botskill"] = "Godlike"; break;
                default: $sq_server["botskill"] = $val;
              }
              break;
            case "s6": $sq_server["standard"] = ($val ? "Yes" : "No"); break;
            case "s7": $sq_server["password"] = ($val == 0 ? "None" : "Required"); break;
            case "s8":
              switch ($val) {
                case 0: $sq_server["vsbots"] = "Disabled"; break;
                case 1: $sq_server["vsbots"] = "Enabled"; break;
                case 2: $sq_server["vsbots"] = "1:1"; break;
                case 3: $sq_server["vsbots"] = "3:2"; break;
                case 4: $sq_server["vsbots"] = "2:1"; break;
                default: $sq_server["vsbots"] = $val;
              }
              break;
            case "s10": $sq_server["forcedrespawn"] = ($val == 0 ? "No" : "Yes"); break;
            case "p1073741827": $sq_server["description"] = $val; break;
            case "p268435717":
            {
              $mut = "";
              $ival = intval($val);
              if ($ival & 0x0001)
                $mut .= "Kills Slow Time, "; // UTGame.UTMutator_???? = 1
              if ($ival & 0x0002)
                $mut .= "Big Head, "; // UTGame.UTMutator_BigHead = 2
              if ($ival & 0x0008)
                $mut .= "Friendly Fire, "; // UTGame.UTMutator_FriendlyFire = 8
              if ($ival & 0x0010)
                $mut .= "Handicap, "; // UTGame.UTMutator_Handicap = 16
              if ($ival & 0x0020)
                $mut .= "Instagib, "; // UTGame.UTMutator_Instagib = 32
              if ($ival & 0x0040)
                $mut .= "Low Gravity, "; // UTGame.UTMutator_LowGrav = 64
              if ($ival & 0x0080)
                $mut .= "No Super Pickups, "; // UTGame.UTMutator_NoPowerups = 128
              if ($ival & 0x0100)
                $mut .= "No Translocator, "; // UTGame.UTMutator_NoTranslocator = 256
              if ($ival & 0x0200)
                $mut .= "Slo Mo, "; // UTGame.UTMutator_Slomo = 512
              if ($ival & 0x0400)
                $mut .= "Speed Freak, "; // UTGame.UTMutator_SpeedFreak = 1024
              if ($ival & 0x0800)
                $mut .= "Super Berserk, "; // UTGame.UTMutator_SuperBerserk = 2048
              if ($ival & 0x1000)
                $mut .= "Weapon Replacement, "; // UTGame.UTMutator_WeaponReplacement = 8192
              if ($ival & 0x2000)
                $mut .= "Weapons Respawn, "; // UTGame.UTMutator_WeaponsRespawn = 16384
              if ($mut == "")
                $sq_server["mutator"] = "";
              else
                $sq_server["mutator"] = substr($mut, 0, -2);
              break;
            }
            case "p1073741828":
            {
              $xmut = "";
            	for ($y = 0; $y < strlen($val); $y++)
            	{
            	  if (ord($val[$y]) == 28)
            	    $xmut .= ", ";
            	  else
            	    $xmut .= $val[$y];
            	}
            	break;
            }
            case "NumPrivateConnections": $sq_server["maxspectators"] = $val; break;
            case "NumOpenPrivateConnections": $sq_server["spectateslots"] = $val; break;
          }
        }
      }
    }

    if ($sq_server["mutator"] == "" && $xmut == "")
      $sq_server["mutator"] = "None";
    else if ($sq_server["mutator"] != "" && $xmut != "")
      $sq_server["mutator"] .= ", ";
    if ($xmut != "")
      $sq_server["mutator"] .= $xmut;

    $y = strpos($sq_server["mapname"], '-');
    if (strlen($sq_server["mapname"]) > $y + 2)
      $sq_server["mapname"] = substr($sq_server["mapname"], 0, $y + 2).strtolower(substr($sq_server["mapname"], $y + 2));

    // Players
    for ($i = 0; isset($data_plr[$i]) && isset($data_plr[$i + 1]); $i += 2) {
      $param = $data_plr[$i];
      $val = $data_plr[$i + 1];

      if (strlen($val) > 0 && ord($val) != 0x00) {
        switch ($param) {
          case "player_":
            $data = explode("\x00", $val);
            $x = 0;
            foreach ($data as $tempd)
              $sq_player[$x++]["player"] = $tempd;
            break;
          case "score_":
            $data = explode("\x00", $val);
            $x = 0;
            foreach ($data as $tempd)
              $sq_player[$x++]["score"] = $tempd;
            break;
          case "ping_":
            $data = explode("\x00", $val);
            $x = 0;
            foreach ($data as $tempd)
              $sq_player[$x++]["ping"] = $tempd;
            break;
          case "team_": // This doesn't appear to tell who is on what team.
            $data = explode("\x00", $val);
            $x = 0;
            foreach ($data as $tempd)
              $sq_player[$x++]["team"] = 0; // Lacking proper info in UT3
            break;
          case "deaths_":
            $data = explode("\x00", $val);
            $x = 0;
            foreach ($data as $tempd)
              $sq_player[$x++]["deaths"] = $tempd;
            break;
          case "pid_":
            break;
        }
      }
    }

    // Teams
    for ($i = 0; isset($data_team[$i]) && isset($data_team[$i + 1]); $i += 2) {
      $param = $data_team[$i];
      $val = $data_team[$i + 1];

      if (strlen($val) > 0 && ord($val) != 0x00) {
        switch ($param) {
          case "team_t":
            break;
          case "score_t":
            $data = explode("\x00", $val);
            $teams = 0;
            foreach ($data as $tempd) {
              switch($teams) {
                case 0: $sq_team[$teams]["team"] = "Red"; break;
                case 1: $sq_team[$teams]["team"] = "Blue"; break;
                case 2: $sq_team[$teams]["team"] = "Color3"; break;
                case 3: $sq_team[$teams]["team"] = "Color4"; break;
                default: $sq_team[$teams]["team"] = "TeamX";
              }
              $sq_team[$teams]["size"] = 0; // Team info incomplete in UT3 query
              $sq_team[$teams++]["score"] = $tempd;
            }
            $teams = -1; // Team info incomplete in UT3 query
            break;
        }
      }
    }
  }

  fclose($fs);
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
  if ($query_type == 3) {
    $sname = str_replace(" ", "", $sq_server["mapname"]);
    $mapimage = strtolower($sname).".jpg";
  }
  else
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

  if ($query_type == 3) {
  	if ($display_map)
      include("templates/serverquery-ut3map.php");
    else
      include("templates/serverquery-ut3.php");
  }
  else if ($query_type) {
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

EOF;

  if (isset($sq_team[1]) && $teams >= 0) {
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
        <tr>
          <td>

EOF;

    DisplayPlayers(1);
    echo <<<EOF
          </td>
        </tr>
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
        <tr>
          <td>

EOF;
    DisplayPlayers(2);
    echo <<<EOF
          </td>
        </tr>
      </table>

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
  else if ($teams < 0) {
    echo <<<EOF
  <tr>
    <td colspan="5">
      <table class="status" cellspacing="0" cellpadding="1" width="100%">
        <tr>
          <td class="redteambar" align="center" width="50%">
            <b>Red Team Score: {$sq_team[0]['score']}</b>
          </td>
          <td class="blueteambar" align="center" width="50%">
            <b>Blue Team Score: {$sq_team[1]['score']}</b>
          </td>
        </tr>
      </table>

EOF;

    DisplayPlayers(0);
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
    <td align="center">Server is <span class="offline">Offline or Not Responding</span></td>
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
  if ($query_type == 3) {
    // Sort by score
    $numplr = 0;
    foreach($sq_player as $plr) {
      if (isset($plr["player"])) {
      	if (isset($plr["player"])) {
      	  $name[] = $plr["player"];
          $score[] = $plr["score"];
          $deaths[] = $plr["deaths"];
          $ping[] = $plr["ping"];
          if (isset($plr["team"]))
            $team[] = $plr["team"];
          if ($score[$numplr] == 0xffff)
            $score[$numplr] = 0;
          $numplr++;
        }
      }
    }
    if ($numplr)
      array_multisort($score, SORT_NUMERIC, SORT_DESC, $ping);

    $header = 0;
    for ($i = 0; $i < $numplr; $i++) {
      if (!$header) {
    echo <<<EOF
      <tr>
        <td>
          <table class="status" cellspacing="0" cellpadding="1" width="100%">
            <tr>
              <td class="statustitle" align="center" colspan="4">
                <b>Players</b>
              </td>
            </tr>
            <tr>
              <td width="200"><b>Name</b></td>
              <td width="50"><b>Score</b></td>
              <td width="50"><b>Deaths</b></td>
              <td width="50"><b>Ping</b></td>
            </tr>
EOF;
          $header = 1;
      }

      if (isset($name[$i]) && (!$teamnum || (isset($team[$i]) && $team[$i] == $teamnum - 1))) {
        echo <<<EOF
            <tr>
              <td>{$name[$i]}</td>
              <td>{$score[$i]}</td>
              <td>{$deaths[$i]}</td>
              <td>{$ping[$i]}</td>
            </tr>
EOF;
      }
    }
    if ($header)
      echo <<<EOF
          </table>
        </td>
      </tr>

EOF;
  }
  else if ($query_type) {
    // Sort by score
    $numplr = 0;
    foreach($sq_player as $plr) {
      if (isset($plr["player"])) {
      	if (isset($plr["player"])) {
      	  $name[] = $plr["player"];
          $score[] = $plr["score"];
          $ping[] = $plr["ping"];
          if (isset($plr["team"]))
            $team[] = $plr["team"];
          if ($score[$numplr] == 0xffff)
            $score[$numplr] = 0;
          $numplr++;
        }
      }
    }
    if ($numplr)
      array_multisort($score, SORT_NUMERIC, SORT_DESC, $ping);

    $header = 0;
    for ($i = 0; $i < $numplr; $i++) {
      if (!$header) {
    echo <<<EOF
      <tr>
        <td>
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

      if (isset($name[$i]) && (!$teamnum || (isset($team[$i]) && $team[$i] == $teamnum - 1))) {
        echo <<<EOF
            <tr>
              <td>{$name[$i]}</td>
              <td>{$score[$i]}</td>
              <td>{$ping[$i]}</td>
            </tr>
EOF;
      }
    }
    if ($header)
      echo <<<EOF
          </table>
        </td>
      </tr>

EOF;
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