<?php

/*
    UTStatsDB
    Copyright (C) 2002-2009  Patrick Contreras / Paul Gallier

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

if (preg_match("/logmatchevents.php/i", $_SERVER["PHP_SELF"])) {
  echo "Access denied.\n";
  die();
}

// New Game
function tag_ng ($i, $data)
{
  global $link, $dbpre, $config, $match, $break;

  if ($i < 10 || $match->ended)
    return;

  $match->ngfound = 1;
  $match->matchdate = strtotime($data[2]); // Date/Time (2002-10-26 21:55:20)
  $match->timeoffset = 110;
  $match->timezone = $data[3]; // Time zone
  $match->mapfile = sql_addslashes(substr($data[4], 0, 32)); // Map filename
  $match->map = sql_addslashes(substr($data[5], 0, 32)); // Map title
  $match->author = sql_addslashes(substr($data[6], 0, 32)); // Map author
  $match->gtype = $data[7]; // Game type
  $match->gname = sql_addslashes(substr($data[8], 0, 32)); // Game name
  $mut = sql_addslashes($data[9], 0, 255); // Mutators

  if ($match->map == "Untitled")
    $match->map = $match->mapfile;

  // Drop "Log " from beginning of game type description
  if ($config["ignorelogtype"] && substr($match->gname, 0, 4) == "Log ")
    $match->gname = substr($match->gname, 4);

  // Drop "xGame.x" from beginning of game type description
  if ($config["ignorelogtype"] && substr($match->gname, 0, 7) == "xGame.x")
    $match->gname = substr($match->gname, 7);

  // UT3 - remove "Game_Content"
  if (substr($match->gname, -12) == "Game_Content")
    $match->gname = substr($match->gname, 0, -12);
  switch ($match->gname) {
    case "UTDeathmatch":   $match->gname = "Deathmatch";       break;
    case "UTCTF":          $match->gname = "Capture the Flag"; break;
    case "UTOnslaught":    $match->gname = "Warfare";          break;
    case "UTVehicleCTF":   $match->gname = "Vehicle CTF";      break;
    case "UTTeamGame":     $match->gname = "Team Deathmatch";  break;
    case "UTDuelGame":     $match->gname = "Duel";             break;
    case "UTBRGame":       $match->gname = "Bombing Run";      break;
    case "UTGreed":        $match->gname = "Greed";            break;
    case "UTBetrayalGame": $match->gname = "Betrayal";         break;
  }

  // Look up game type
  $result = sql_queryn($link, "SELECT tp_num,tp_type,tp_team FROM {$dbpre}type WHERE tp_desc='{$match->gname}' LIMIT 1");
  if (!$result) {
    echo "Error reading game type table.{$break}\n";
    exit;
  }
  $row = sql_fetch_row($result);
  sql_free_result($result);
  if ($row) {
    $match->gametnum = $row[0];
    $match->gametype = $row[1];
    $match->teamgame = $row[2];
  }
  else { // Add new game type
    if ($match->gtype == "Deathball.DB_DeathBall") {
      $match->gametype = 18;
      $match->teamgame = 1;
      $result = sql_queryn($link, "INSERT INTO {$dbpre}type (tp_desc,tp_type,tp_team) VALUES('{$match->gname}', 18, 1)");
    }
    else {
      $match->gametype = $config["deftype"];
      $match->teamgame = $config["defteam"];
      $result = sql_queryn($link, "INSERT INTO {$dbpre}type (tp_desc,tp_type,tp_team) VALUES('{$match->gname}',{$match->gametype},{$match->teamgame})");
    }
    if (!$result) {
      echo "Error saving new game type.{$break}\n";
      exit;
    }
    $match->gametnum = sql_insert_id($link);
  }

  // Locate Map Number
  $result = sql_queryn($link, "SELECT mp_num FROM {$dbpre}maps WHERE mp_name='{$match->mapfile}' AND mp_author='{$match->author}' LIMIT 1");
  if (!$result) {
    echo "Error accessing map database.{$break}\n";
    exit;
  }
  $row = sql_fetch_row($result);
  sql_free_result($result);
  if ($row)
    $match->mapnum = (int) $row[0];
  else {
    // Add New Map
    $result = sql_queryn($link, "INSERT INTO {$dbpre}maps (mp_name,mp_desc,mp_author) VALUES('{$match->mapfile}','{$match->map}','{$match->author}')");
    if (!$result) {
      echo "Error saving new map in database.{$break}\n";
      exit;
    }
    $match->mapnum = sql_insert_id($link);
  }

  $match->mutators = "";
  $tok = strtok($mut, ".");
  if ($tok != "") {
    $tok = strtok("|\n");
    while ($tok) {
      $mut = $tok;
      if (substr($mut, 0, 3) == "Mut")
        $mut = substr($mut, 3);
      if (!strcasecmp($mut, "UT2004RPG"))
        $match->rpg = 1;
      $match->mutators.=$mut.", ";
      $tok = strtok(".");
      $tok = strtok("|\n");
    }
    $match->mutators = rtrim($match->mutators, ", ");
  }
}

// Server Info
function tag_si($i, $data)
{
  global $link, $dbpre, $config, $match, $break;

  if ($i != 8 || $match->ended)
    return 0;

  $name = preg_replace("(\x1B...)", "", $data[2]); // Strip color codes
  $match->servername = substr($name, 0, 45); // Server name
  $match->shortname = "";
  $match->timezone = $data[3]; // Time zone
  $match->admin = substr($data[4], 0, 35); // Admin name
  $match->email = substr($data[5], 0, 45); // Admin email

  $siline = $data[7];

  while (parseserverdata($siline, $param, $val)) {
    $info = strtolower(trim($param));
    $status = trim($val);
    $statusl = strtolower($status);
    switch($info) {
      case "servermode": // dedicated
        break;
      case "adminname":
        break;
      case "adminemail":
        break;
      case "serverversion":
        $match->serverversion = sql_addslashes(substr($status, 0, 9));
        break;
      case "password":
      case "gamepassword":
        if ($statusl == "true" || intval($statusl) == 1)
          $match->password = 1;
        else
          $match->password = 0;
        break;
      case "gamestats":
        if ($statusl == "true" || intval($statusl) == 1)
          $match->gamestats = 1;
        else
          $match->gamestats = 0;
        break;
      case "mapvoting":
        if ($statusl == "true" || intval($statusl) == 1)
          $match->mapvoting = 1;
        else
          $match->mapvoting = 0;
        break;
      case "kickvoting":
        if ($statusl == "true" || intval($statusl) == 1)
          $match->kickvoting = 1;
        else
          $match->kickvoting = 0;
        break;
      case "minplayers":
        $match->minplayers = intval($status);
        break;
      case "endtimedelay":
        $match->endtimedelay = floatval($status);
        break;
      case "goalscore":
        $match->fraglimit = intval($status);
        break;
      case "timelimit":
      case "time limit":
        $match->timelimit = intval($status);
        break;
      case "overtime time":
        $match->overtime = intval($status);
        break;
      case "translocator":
        if ($statusl == "true" || intval($statusl) == 1)
          $match->translocator = 1;
        else
          $match->translocator = 0;
        break;
      case "bbalanceteams":
      case "balanceteams":
        if ($statusl == "true" || intval($statusl) == 1)
          $match->balanceteams = 1;
        else
          $match->balanceteams = 0;
        break;
      case "bplayersbalanceteams":
      case "playersbalanceteams":
        if ($statusl == "true" || intval($statusl) == 1)
          $match->playersbalanceteams = 1;
        else
          $match->playersbalanceteams = 0;
        break;
      case "friendlyfirescale": // percentage
      case "friendlyfire":
        $match->friendlyfirescale = sql_addslashes(substr($statusl, 0, 9));
        break;
      case "linksetup":
        $match->linksetup = sql_addslashes(substr($status, 0, 25));
        break;
      case "gamespeed":
        $match->gamespeed = floatval($status);
        $match->timeoffset *= $match->gamespeed;
        break;
      case "campthreshold":
        // $campthreshold = 600.00;
        break;
      case "camperrewarninterval":
        // $camperrewarninterval = 10;
        break;
      case "healthforkills":
        if ($statusl == "true" || intval($statusl) == 1)
          $match->healthforkills = 1;
        else
          $match->healthforkills = 0;
        break;
      case "allowsuperweapons":
        if ($statusl == "true" || intval($statusl) == 1)
          $match->allowsuperweapons = 1;
        else
          $match->allowsuperweapons = 0;
        break;
      case "camperalarm":
        if ($statusl == "true" || intval($statusl) == 1)
          $match->camperalarm = 1;
        else
          $match->camperalarm = 0;
        break;
      case "allowpickups":
        if ($statusl == "true" || intval($statusl) == 1)
          $match->allowpickups = 1;
        else
          $match->allowpickups = 0;
        break;
      case "allowadrenaline":
        if ($statusl == "true" || intval($statusl) == 1)
          $match->allowadrenaline = 1;
        else
          $match->allowadrenaline = 0;
        break;
      case "fullammo":
        if ($statusl == "true" || intval($statusl) == 1)
          $match->fullammo = 1;
        else
          $match->fullammo = 0;
        break;
      case "gamedifficulty":
        $match->difficulty = intval($status);
        break;
      case "shortname":
        $match->shortname = substr($status, 0, 30);
        break;
      default:
        break;
    }
    $tok = strtok("\\\n");
  }

  // Locate Server Number
  $match->servernamesl = sql_addslashes($match->servername);
  $match->shortnamesl = sql_addslashes($match->shortname);
  $match->adminsl = sql_addslashes($match->admin);
  $match->emailsl = sql_addslashes($match->email);
  if ($config["useshortname"] && strlen($match->shortname) > 0)
    $result = sql_queryn($link, "SELECT sv_num,sv_name,sv_shortname,sv_admin,sv_email FROM {$dbpre}servers WHERE sv_shortname='{$match->shortnamesl}' LIMIT 1");
  else
    $result = sql_queryn($link, "SELECT sv_num,sv_name,sv_shortname,sv_admin,sv_email FROM {$dbpre}servers WHERE sv_name='{$match->servernamesl}' LIMIT 1");
  if (!$result) {
    echo "Error accessing server database.{$break}\n";
    exit;
  }
  $row = sql_fetch_row($result);
  sql_free_result($result);
  if ($row) {
    $match->servernum = intval($row[0]);
    $sv_name = $row[1];
    $sv_shortname = $row[2];
    $sv_admin = $row[3];
    $sv_email = $row[4];
    if (strcmp($match->servername, $sv_name) || strcmp($match->admin, $sv_admin) || strcmp($match->email, $sv_email) || strcmp($match->shortname, $sv_shortname))
      sql_queryn($link, "UPDATE {$dbpre}servers SET sv_name='{$match->servernamesl}',sv_shortname='{$match->shortnamesl}',sv_admin='{$match->adminsl}',sv_email='{$match->emailsl}' WHERE sv_num={$match->servernum}");
  }
  else {
    // Add New Server
    $result = sql_queryn($link, "INSERT INTO {$dbpre}servers (sv_name,sv_shortname,sv_admin,sv_email) VALUES('{$match->servernamesl}','{$match->shortnamesl}','{$match->adminsl}','{$match->emailsl}')");
    if (!$result) {
      echo "Error saving new server in database.{$break}\n";
      exit;
    }
    $match->servernum = sql_insert_id($link);
  }

  // Check for existing match in database
  $md = date("Y-m-d H:i:s", $match->matchdate);
  $result = sql_queryn($link, "SELECT gm_status FROM {$dbpre}matches WHERE gm_server={$match->servernum} AND gm_map={$match->mapnum} AND gm_type={$match->gametnum} AND gm_init='$md' LIMIT 1");
  if (!$result) {
    echo "Error accessing match database.{$break}\n";
    exit;
  }
  $row = sql_fetch_row($result);
  sql_free_result($result);
  if ($row) {
    if ($row[0] == 1)
      $match->ended = 4;
    else
      $match->ended = 10;
    return 1;
  }
}

// Start Game
function tag_sg($i, $data)
{
  global $match, $player, $events, $gkills, $tkills, $pickups, $spree, $multi, $assist, $flagstatus, $killmatch;

  if ($match->ended)
    return;

  $match->starttime = ctime($data[0]);
  $match->started = 1;
  $match->startdate = $match->matchdate + intval($match->starttime / $match->timeoffset);

  for ($n = 0; $n <= $match->maxplayer; $n++)
    if (isset($player[$n]))
      clear_player($match->starttime, $n);

  $match->team = array(0.0, 0.0, 0.0, 0.0);
  $match->tot_kills = 0;
  $match->tot_deaths = 0;
  $match->tot_suicides = 0;
  $match->headshots = 0;
  $match->teamkills = 0;

  for ($i = 0; $i < $match->numevents; $i++) {
  	$ev = $events[$i][1];
  	if ($ev != 2 && $ev != 3 && $ev != 4 && $ev != 10) // Connections, Game Events, Team Changes, Map Votes
      $events[$i][1] = -1;
  }

  $gkills = array(array());
  $match->gkcount = 0;

  $tkills = array(array());
  $match->tkcount = 0;

  $pickups = array(array());
  $match->maxpickups = 0;

  $spree = array(array());
  $multi = array(array());
  $assist = array();
  $flagstatus = array();
  $killmatch = array(array());

  foreach ($player as $plr) {
  	$p = $plr->plr;
    $spree[$p][0] = 0;
    $spree[$p][1] = 0;
    $multi[$p][0] = 0;
    $multi[$p][1] = 0;
    $multi[$p][2] = 0;
    $tchange[$p] = 0;
    $assist[$p] = 0;
    $relog[$p] = -1;
    $flagstatus[$p] = 0;
  }

  gameevent($match->starttime, 0);
}

// End Game
function tag_eg($i, $data)
{
  global $match, $player, $relog;

  if ($match->ended)
    return;

  $event = strtolower($data[2]);
  $time = ctime($data[0]);
  $reason = 0;

  switch($event) {
    case "fraglimit":
      $reason = 1;
      $match->ended = 1;
      break;
    case "timelimit":
      $reason = 2;
      $match->ended = 1;
      break;
    case "teamscorelimit":
      $reason = 3;
      $match->ended = 1;
      break;
    case "goalscorelimit":
      $reason = 4;
      $match->ended = 1;
      break;
    case "roundlimit":
      $reason = 5;
      $match->ended = 1;
      break;
    case "draw":
      $reason = 11;
      $match->ended = 1;
      break;
    case "artifacts":
      $reason = 12;
      $match->ended = 1;
      break;
    case "lastman": // TODO: Fix Last Man Standing rank list checking
      $reason = 6;
      $match->ended = 1;
      if ($i > 3) { // Retrieve rank list
        // $rankset = 1;
        $rn = 1;
        while ($rn + 2 < $i) {
          $rp = check_player($data[$rn + 2]);
          if ($rp >= 0) {
            // $player[$rp]->rank = $rn;
            if ($player[$rp]->lives - (array_sum($player[$rp]->deaths) + array_sum($player[$rp]->suicides)) > 0) {
              lmsout($time, $rp, 1);
              $match->lastman = $rp;
            }
          }
          $rn++;
        }
      }
      break;
    case "mapchange":
      $reason = 9;
      $match->ended = 6;
      break;
    case "serverquit":
      $reason = 10;
      $match->ended = 7;
      break;
    case "endwarmup":
      $reason = 13;
      $match->ended = 11;
      break;
    default: // Other EndGame reasons
      $match->ended = 2;
  }
  $match->length = $time - $match->starttime;
  for ($n = 0; $n <= $match->maxplayer; $n++) {
    if ($n >= 0 && isset($player[$n]) && $player[$n]->connected) {
      $ptime = $time - $player[$n]->starttime;
      $tm = $player[$n]->team;
      if ($tm < 0 || $tm > 3)
        $tm = 0;
      $player[$n]->totaltime[$tm] += $ptime;

      endspree($n, $time, 0, 0, 0); // End Killing Sprees
      endmulti($n, $time); // End Multi-Kills
      flag_check($n, $time, 0);
    }
  }
  gameevent($time, 1, $reason);
  $match->endtime = $time;
}

?>