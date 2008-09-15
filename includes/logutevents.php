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

if (preg_match("/logutevents.php/i", $_SERVER["PHP_SELF"])) {
  echo "Access denied.\n";
  die();
}

// Server Info
function tagut_info($i, $data)
{
  global $match;

  if ($i < 4)
    return;

  switch ($data[2])
  {
  	case "Absolute_Time": // 2004.03.12.08.08.36.205.-8.0
  	  if ($i < 4)
  	    break;

      $mdt = $data[3];
      for ($p = 0, $q = 0; $p < strlen($mdt) && $q < 7; $p++)
      {
        if ($mdt[$p] == '.')
        {
          $q++;
          if ($q < 3)
            $mdt[$p] = '-';
          else if ($q == 3)
            $mdt[$p] = ' ';
          else if ($q < 6)
            $mdt[$p] = ':';
          if ($q == 6)
            $r = $p;
        }
      }

      $match->timezone = substr($mdt, $p);
      $match->matchdate = strtotime(substr($mdt, 0, $r));

  	  break;
  	case "Server_ServerName":
      $match->servername = substr($data[3], 0, 45);
      $match->shortname = "";
  	  break;
  	case "Server_AdminName":
      $match->admin = substr($data[3], 0, 35);
  	  break;
  	case "Server_AdminEmail":
      $match->email = substr($data[3], 0, 45);
  	  break;
  	case "Server_IP": // 0.0.0.0
  	  break;
  	case "Server_Port": // 7777
  	  break;
  }
}

// Map Info
function tagut_map($i, $data)
{
  global $match;

  if ($i < 4)
    return;

  switch ($data[2])
  {
  	case "Name": // DM-Q317][.unr
  	  $mpfn = $data[3];
  	  if (!strcasecmp(substr($mpfn, -4), ".unr"))
        $mpfn = substr($mpfn, 0, -4);
      $match->mapfile = sql_addslashes(substr($mpfn, 0, 32));
  	  break;
  	case "Title": // Quake III Arena: The Longest Yard, UT-style!
  	  $match->map = sql_addslashes(substr($data[3], 0, 32));
  	  break;
  	case "Author": // DAVID M.+ {LoD}QuazBotch, Ultron & Dr. Doom
  	  $match->author = sql_addslashes(substr($data[3], 0, 32));
  	  break;
  }
}

// Match Info (New Game)
function tagut_game($i, $data)
{
  global $link, $dbpre, $config, $match, $break;

  if ($i < 4)
    return 0;

  $match->uttype = 1;
  switch ($data[2])
  {
  	case "GameMutator": // Class Botpack.DMMutator
  	{
  	  if ($match->mutators != "")
  	    $match->mutators.=", ";
  	  $mut = $data[3];
  	  if (!strncasecmp($mut, "Class ", 6))
  	    $mut = substr($mut, 6);
      $match->mutators.=trim(sql_addslashes($mut));
      if (strlen($match->mutators > 255)) {
        $match->mutators = substr($match->mutators, 0, 255);
        if (substr($match->mutators, -1) == '\\' && substr($match->mutators, -2, 1) != '\\')
          $match->mutators = substr($match->mutators, 0, -1);
      }

      // Check for JailBreak mutator and set (missing) GameName and GameClass
      if ($mut == "JailBreak.JBMutator") {
        $data[2] = "GameName";
        $data[3] = "JailBreak";
        tagut_game(4, $data);
        $data[2] = "GameClass";
        $data[3] = "JailBreak.JBMutator";
        tagut_game(4, $data);
      }
  	  break;
  	}

  	case "GameName": // Tournament DeathMatch / Capture the Flag
  	{
      $match->gtype = sql_addslashes(substr($data[3], 0, 32));

      // Look up game type
      $result = sql_queryn($link, "SELECT tp_num,tp_type,tp_team FROM {$dbpre}type WHERE tp_desc='{$match->gtype}' LIMIT 1");
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
        $result = sql_queryn($link, "INSERT INTO {$dbpre}type (tp_desc) VALUES('{$match->gtype}')");
        if (!$result) {
          echo "Error saving new game type.{$break}\n";
          exit;
        }
        $match->gametnum = sql_insert_id($link);
        $match->gametype = 0;
        $match->teamgame = 0;
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

      // Check for existing match in database
      $md = date("Y-m-d H:i:s", $match->matchdate);
      $result = sql_queryn($link, "SELECT COUNT(*) FROM {$dbpre}matches WHERE gm_server={$match->servernum} AND gm_map={$match->mapnum} AND gm_type={$match->gametnum} AND gm_init='$md' LIMIT 1");
      if (!$result) {
        echo "Error accessing match database.{$break}\n";
        exit;
      }
      $row = sql_fetch_row($result);
      sql_free_result($result);
      if ($row && $row[0] > 0) {
        $match->ended = 4;
        return $match->ended;
      }

  	  $match->ngfound = 1;
  	}

  	case "GameClass": // Botpack.DeathMatchPlus
  	{
      $match->gname = $data[3];

      if ($p = strpos($match->gname, '.'))
        $match->gname = substr($match->gname, $p + 1);

      $match->gname = sql_addslashes(substr($match->gname, 0, 32));
  	  break;
  	}

  	case "GameVersion": // 436
  	{
      $match->serverversion = sql_addslashes(substr($data[3], 0, 9));
  	  break;
  	}

    case "FragLimit": // 10
    {
      $match->fraglimit = intval($data[3]);
      break;
    }

    case "TimeLimit": // 0
    {
      $match->timelimit = intval($data[3]);
      break;
    }

    case "UseTranslocator": // False
    {
      if (strtolower($data[3]) == "true")
        $match->translocator = 1;
      else
        $match->translocator = 0;
      break;
    }

  	case "NoMonsters": // True
  	  break;
    case "MuteSpectators": // False
      break;
    case "HumansOnly": // False
      break;
    case "WeaponsStay": // True
      break;
    case "ClassicDeathmessages": // False
      break;
    case "LowGore": // False
      break;
    case "VeryLowGore": // False
      break;
    case "TeamGame": // False
      break;
    case "GameSpeed": // 100
      break;
    case "MaxSpectators": // 2
      break;
    case "MaxPlayers": // 16
      break;
    case "MultiPlayerBots": // True
      break;
    case "HardCore": // True
      break;
    case "MegaSpeed": // False
      break;
    case "AirControl": // 0.350000
      break;
    case "JumpMatch": // False
      break;
    case "TournamentMode": // False
      break;
    case "NetMode": // ListenServer
      break;
  }

  return 0;
}

// Player Event
function tagut_player($i, $data)
{
  global $match, $player, $relog;

  if ($i < 4)
    return;

  $time = ctime($data[0]);

  switch ($data[2])
  {
  	case "Rename": // Shadow	0
  	{
      if ($i < 5)
        break;

  	  break;
  	}

  	case "Teamchange": // 0	255
  	{
      if ($i < 5)
        break;

  	  break;
  	}

  	case "Connect": // Shadow	0	False
  	{
      if ($i < 6)
        break;

      $plr = intval($data[4]);
      add_player($time, $plr);
      set_name($plr, $data[3]);

      // Check for existing player name
      for ($i2 = 0; $i2 <= $match->maxplayer && $relog[$plr] < 0; $i2++) {
        if ($i2 != $plr && isset($player[$i2]) && !strcmp($player[$plr]->name, $player[$i2]->name) && !$player[$i2]->connected) {
          $relog[$plr] = $i2;
          $player[$plr]->name = "";
          $player[$i2]->starttime = $time;
          $plr = $i2;
        }
      }

      if ($match->gametype == 10) // Set LMS lives
        $player[$plr]->lives = $match->fraglimit;

      $player[$plr]->connected = 1;
      connections($plr, $time, 0);

      if ($plr > $match->maxplayer)
        $match->maxplayer = $plr;

  	  break;
  	}

    case "Disconnect":
    {
      $time = ctime($data[0]);
      $plr = check_player($data[3]);

      if ($plr < 0)
        break;

      $player[$plr]->connected = 0;

      $tm = $player[$plr]->team;
      if ($tm < 0 || $tm > 3)
        $tm = 0;

      if  (!$match->ended) {
        $ptime = $time - $player[$plr]->starttime;
        $player[$plr]->totaltime[$tm] += $ptime;
        endspree($plr, $time, 4, 0, 0); // End Killing Sprees
        endmulti($plr, $time); // End Multi-Kills
        flag_check($plr, $time, 0);
      }

      connections($plr, $time, 1);
      break;
    }

  	case "TeamName": // 0	Red
  	{
      if ($i < 5)
        break;

  	  break;
  	}

  	case "Team": // 0	255
  	{
      if ($i < 5 || !$match->teamgame)
        break;

      $plr = check_player($data[3]);
      if ($plr >= 0) {
      	$tm = intval($data[4]);
      	if ($tm >= 0 && $tm <= 3) {
          flag_check($plr, $time, 0);
          $player[$plr]->team = $data[4];
          teamchange($time, $plr, $data[4]);
          if ($tm + 1 > $match->numteams)
            $match->numteams = $tm + 1;
        }
      }
  	  break;
  	}

  	case "TeamID": // 0	0
  	{
      if ($i < 5)
        break;

  	  break;
  	}

  	case "Ping": // 0	0
  	{
      if ($i < 5)
        break;

      $plr = check_player($data[3]);
      $ping = intval($data[4]);

      if ($plr >= 0) {
        $player[$plr]->ping += $ping;
        $player[$plr]->pingcount++;
      }
  	  break;
  	}

  	case "IsABot": // 0	False
  	{
      if ($i < 5)
        break;

      $plr = check_player($data[3]);
      if ($plr >=0 && strtolower($data[4]) == "true")
        $player[$plr]->bot = true;
  	  break;
  	}

  	case "Skill": // 0	1.000000
  	{
      if ($i < 5)
        break;

      //$plr = check_player($data[3]);
      //$bot_skill = intval($data[4]);
      //bot_add($plr,$bot_skill,$bot_alertness,$bot_accuracy,$bot_aggressive,$bot_strafing,$bot_style,$bot_tactics,$bot_transloc,$bot_reaction,$bot_jumpiness,$bot_favorite);
  	  break;
  	}

  	case "Novice": // 0	False
  	{
      if ($i < 5)
        break;

  	  break;
  	}

  	case "IP":
  	{
      if ($i < 5)
        break;

      $plr = check_player($data[3]);
      if ($plr >=0)
        $player[$plr]->ip = substr($data[4], 0, 21);
  	  break;
  	}
  }
}

// Start Game
function tagut_game_start($i, $data)
{
  global $match, $player;

  if ($match->ended)
    return;

  $match->starttime = ctime($data[0]);
  $match->started = 1;
  $match->startdate = $match->matchdate + intval($match->starttime / 110);

  for ($n = 0; $n <= $match->maxplayer; $n++)
    if (isset($player[$n]))
      clear_player($match->starttime, $n);

  $match->team = array(0.0, 0.0, 0.0, 0.0);
  gameevent($match->starttime, 0);
}

// Item Pickup
function tagut_item_get($i, $data)
{
  global $link, $dbpre, $match, $pickups, $break;

  if ($i < 4 || $match->ended || !$match->started)
    return;

  // Flak Cannon	3
  // Flak Shells	3
  // Rocket Launcher	0
  // RocketPack	0
  // Pulse Gun	2
  // Pulse Cell	2
  // Enforcer	3
  // Body Armor	0
  // MedBox	3
  // Shock Rifle	0
  // Ripper	3
  // Blade Hopper	3
  // Sniper Rifle	0
  // Box of Rifle Rounds	0

  $item = $data[2];
  $plr = check_player($data[3]);

  if ($plr < 0)
    return;

  // Get Item Number
  $result = sql_queryn($link, "SELECT it_num FROM {$dbpre}items WHERE it_type='$item' LIMIT 1");
  if (!$result) {
    echo "Error reading items table.{$break}\n";
    exit;
  }
  $row = sql_fetch_row($result);
  sql_free_result($result);
  if ($row) {
    $num = $row[0];
    if (isset($pickups[$plr][$num]))
      $pickups[$plr][$num]++;
    else
      $pickups[$plr][$num] = 1;
  }
  else { // Add new item
    $result = sql_queryn($link, "INSERT INTO {$dbpre}items (it_type,it_desc) VALUES('$item','$item')");
    if (!$result) {
      echo "Error saving new item.{$break}\n";
      exit;
    }
    $num = sql_insert_id($link);
    if (isset($pickups[$plr][$num]))
      $pickups[$plr][$num]++;
    else
      $pickups[$plr][$num] = 1;
  }
  if ($num > $match->maxpickups)
    $match->maxpickups = $num;
}

// Kill Event
function tagut_kill($killtype, $i, $data)
{
  global $match, $player, $gkills, $gscores, $spree, $multi, $killmatch;

  if ($i < 7 || $match->ended || !$match->started)
    return;

  $time = ctime($data[0]);
  $killer = check_player($data[2]);
  $killweapon = substr($data[3], 0, 35);
  $victim = check_player($data[4]);
  $victweapon = substr($data[5], 0, 35);
  $damagetype = $data[6];

  if ($killer >= 0)
  {
    $tmk = $player[$killer]->team;
    if ($tmk < 0 || $tmk > 3)
      $tmk = 0;
  }
  else
    $tmk = 0;

  if ($victim >= 0)
  {
    $tmv = $player[$victim]->team;
    if ($tmv < 0 || $tmv > 3)
      $tmv = 0;
  }
  else
    $tmv = 0;

  if ($killtype == 1 && $match->teamgame)
  {
    if ($killer < 0 || $victim < 0)
      return;

    $match->teamkills++;
    if (isset($killmatch[$killer][$killer]))
      $killmatch[$killer][$killer]++; // Count as suicide in rankings
    else
      $killmatch[$killer][$killer] = 1;

    $tm = intval($player[$killer]->team);
    if ($tm < 0 || $tm > 3)
      $tm = 0;
    $player[$killer]->teamkills[$tm]++; // TeamKills

    $tmv = $player[$victim]->team;
    if ($tmv < 0 || $tmv > 3)
      $tmv = 0;
    $player[$victim]->teamdeaths[$tmv]++; // TeamDeaths

    // Decrement score
    $player[$killer]->tscore[$tm] -= 1;
    $match->tot_score -= 1;
    $gscores[$match->gscount][0] = intval($killer);   // Player
    $gscores[$match->gscount][1] = $time;  // Time
    $gscores[$match->gscount][2] = -1; // Score
    $gscores[$match->gscount++][3] = intval($player[$killer]->team); // Team

    // Decrement team score if Team DeathMatch
    if ($match->teamgame && $match->gametype == 4)
      teamscore($time, $tm, -1, 1);

    // Get Kill Weapon
    list($killweaponnum,$killweaptype,$killweapsec) = get_weapon($killweapon, 0);
    // Get Victim Weapon
    list($victweaponnum,$victweaptype,$victweapsec) = get_weapon($victweapon, 0);

    if ($victim >= 0) {
      endspree($victim, $time, 5, $killweaponnum, $killer); // End Killing Spree for Victim
      endmulti($victim, $time); // End Multi-Kill for Victim
      flag_check($victim, $time, 0);
    }
    $gkills[$match->gkcount][0] = $killer;     // Killer
    $gkills[$match->gkcount][1] = $victim;     // Victim
    $gkills[$match->gkcount][2] = $time;   // Time
    $gkills[$match->gkcount][3] = $killweaponnum; // Killer's Weapon Number
    $gkills[$match->gkcount][4] = $victweaponnum; // Victim's Weapon Number
    if ($killer >= 0)
      $gkills[$match->gkcount][5] = $player[$killer]->team; // Killer Team
    else
      $gkills[$match->gkcount][5] = -1;
    if ($victim >= 0)
      $gkills[$match->gkcount][6] = $player[$victim]->team; // Victim Team
    else
      $gkills[$match->gkcount][6] = -1;
    $gkills[$match->gkcount][7] = $killweaptype; // Killer's Weapon Type
    $gkills[$match->gkcount++][8] = $victweaptype; // Victim's Weapon Type
    return;
  }
  // ========== End of TeamKill check ==========

  // 0	Enforcer	2	Enforcer	shot
  // 3	Flak Cannon	1	Enforcer	shredded
  // 2	Enforcer	1	Pulse Gun	shot
  // 1	Flak Cannon	3	Flak Cannon	shredded
  // 0	Sniper Rifle	1	Flak Cannon	Decapitated
  // 0	Sniper Rifle	4	Double Enforcer	RedeemerDeath

  if ($victim < 0 || $killer == -99)
    return;

  // Check for RedeemerDeath
  if (stristr($damagetype, "RedeemerDeath"))
    $killweapon = "Redeemer";

  // Get Kill Weapon
  list($killweaponnum,$killweaptype,$killweapsec) = get_weapon($killweapon, 0);
  // Get Victim Weapon
  list($victweaponnum,$victweaptype,$victweapsec) = get_weapon($victweapon, 0);

  $tm = intval($player[$killer]->team);
  if ($tm < 0 || $tm > 3)
    $tm = 0;

  $player[$killer]->kills[$tm]++; // Kills
  $match->tot_kills++;
  $player[$victim]->deaths[$tm]++; // Deaths
  $reason = 1;
  $match->tot_deaths++;

  // Check for critical kill
  if ($time == $match->flagdrop && $victim == $match->flagdropplr)
  {
    $score = 5;
    $player[$killer]->typekill[$tm]++;
    $match->flagdrop = $match->flagdropplr = -1;
  }
  else
    $score = 1;

  // Add score
  $player[$killer]->tscore[$tm] += $score;
  $match->tot_score += $score;
  $gscores[$match->gscount][0] = intval($killer);   // Player
  $gscores[$match->gscount][1] = $time;  // Time
  $gscores[$match->gscount][2] = $score; // Score
  $gscores[$match->gscount++][3] = intval($player[$killer]->team); // Team

  // Add team score if Team DeathMatch
  if ($match->gametype == 4)
    teamscore($time, $tm, 1, 1);

  if (isset($killmatch[$killer][$victim]))
    $killmatch[$killer][$victim]++;
  else
    $killmatch[$killer][$victim] = 1;

  // Check for first blood
  if ($match->firstblood < 0) {
    $match->firstblood = $killer;
    $player[$killer]->firstblood = 1;
  }

  // Check for headhunter
  if (stristr($damagetype, "Decapitated")) {
    $player[$killer]->headshots++; // Head Shots
    $match->headshots++;
    if ($player[$killer]->headshots >= 15 && !$player[$killer]->headhunter) {
      $player[$killer]->headhunter = 1;
      weaponspecial($time, $killer, 1, 0);
    }
  }

  // Check for flak monkey
  if (stristr($damagetype, "shreded")) {
    $player[$killer]->flakkills++;
    if ($player[$killer]->flakkills >= 15 && !$player[$killer]->flakmonkey) {
      $player[$killer]->flakmonkey = 1;
      weaponspecial($time, $killer, 2, 0);
    }
  }

  // Track Killing Sprees for Killer
  if (!$spree[$killer][1]) {
    $spree[$killer][0] = $time; // First Kill
    $spree[$killer][1] = 1;
  }
  else
    $spree[$killer][1]++; // Kills

  // Track Multi-Kills for Killer
  if ($time - $multi[$killer][2] < 400) { // Within multi range
    if (!$multi[$killer][1]) {
      $multi[$killer][0] = $time; // Start Time
      $multi[$killer][1] = 1; // Kills
    }
    else
      $multi[$killer][1]++; // Kills
    $multi[$killer][2] = $time; // Last Kill Time
  }
  else {
    endmulti($killer, $time); // End Multi-Kill for Killer
    $multi[$killer][0] = $time;
    $multi[$killer][1] = 1;
    $multi[$killer][2] = $time;
  }

  if ($victim >= 0) {
    endspree($victim, $time, $reason, $killweaponnum, $killer); // End Killing Spree for Victim
    endmulti($victim, $time); // End Multi-Kill for Victim
    flag_check($victim, $time, 0);
  }

  $gkills[$match->gkcount][0] = $killer;        // Killer
  $gkills[$match->gkcount][1] = $victim;        // Victim
  $gkills[$match->gkcount][2] = $time;      // Time
  $gkills[$match->gkcount][3] = $killweaponnum; // Killer's Weapon Number
  $gkills[$match->gkcount][4] = $victweaponnum; // Victim's Weapon Number

  if ($killer >= 0)
    $gkills[$match->gkcount][5] = $player[$killer]->team; // Killer Team
  else
    $gkills[$match->gkcount][5] = -1;

  if ($victim >= 0)
    $gkills[$match->gkcount][6] = $player[$victim]->team; // Victim Team
  else
    $gkills[$match->gkcount][6] = -1;

  $gkills[$match->gkcount][7] = $killweaptype; // Killer's Weapon Type
  $gkills[$match->gkcount++][8] = $victweaptype; // Victim's Weapon Type

  if ($match->gametype == 10 && $victim >= 0) {
    if ($player[$victim]->lives - ($player[$victim]->deaths[$tmv] + $player[$victim]->suicides[$tmv]) == 0)
      lmsout($time, $victim, 0);
  }
}

// Suicide Event
function tagut_suicide($i, $data)
{
  global $match, $player, $gkills, $gscores;

  if ($i < 5)
    return;

  $time = ctime($data[0]);
  $plr = check_player($data[2]);
  $weapon = substr($data[3], 0, 35);
  $damagetype = $data[4];

  $tm = intval($player[$plr]->team);
  if ($tm < 0 || $tm > 3)
    $tm = 0;

  $player[$plr]->suicides[$tm]++; // Suicides
  $reason = 2;
  $match->tot_suicides++;

  // Decrement score
  $player[$plr]->tscore[$tm] -= 1;
  $match->tot_score -= 1;
  $gscores[$match->gscount][0] = intval($plr);   // Player
  $gscores[$match->gscount][1] = $time;  // Time
  $gscores[$match->gscount][2] = -1; // Score
  $gscores[$match->gscount++][3] = intval($player[$plr]->team); // Team

  // Decrement team score if Team DeathMatch
  if ($match->teamgame && $match->gametype == 4)
    teamscore($time, $tm, -1, 1);

  list($weaponnum,$weaptype,$weapsec) = get_weapon($weapon, 0);
  list($damweaponnum,$damweaptype,$damweapsec) = get_weapon($damagetype, 0);

  if (isset($killmatch[$plr][$plr]))
    $killmatch[$plr][$plr]++;
  else
    $killmatch[$plr][$plr] = 1;

  endspree($plr, $time, $reason, $damweaponnum, $plr); // End Killing Spree for Victim
  endmulti($plr, $time); // End Multi-Kill for Victim
  flag_check($plr, $time, 0);

  $gkills[$match->gkcount][0] = $plr;
  $gkills[$match->gkcount][1] = $plr;
  $gkills[$match->gkcount][2] = $time;
  $gkills[$match->gkcount][3] = $damweaponnum;
  $gkills[$match->gkcount][4] = $weaponnum;

  $gkills[$match->gkcount][5] = -1;

  if ($plr >= 0)
    $gkills[$match->gkcount][6] = $player[$plr]->team;
  else
    $gkills[$match->gkcount][6] = -1;

  $gkills[$match->gkcount][7] = $damweaptype;
  $gkills[$match->gkcount++][8] = $weaptype;

  if ($match->gametype == 10 && $plr >= 0) {
    if ($player[$plr]->lives - ($player[$plr]->deaths[$tm] + $player[$plr]->suicides[$tm]) == 0)
      lmsout($time, $plr, 0);
  }
}

// Headshot
function tagut_headshot($i, $data)
{
  if ($i < 4)
    return;
}

// Flag Taken
function tagut_flag_taken($i, $data)
{
  global $match, $player;

  if ($i < 4 || $match->ended)
    return;

  $time = ctime($data[0]);
  $plr = check_player($data[2]);
  if ($plr < 0)
    return;

  $tm = intval($player[$plr]->team);
  if ($tm < 0 || $tm > 3)
    return;

  $player[$plr]->taken[$tm]++;
  flag_check($plr, $time, 1);
}

// Flag Dropped
function tagut_flag_dropped($i, $data)
{
  global $match, $player;

  if ($i < 4 || $match->ended)
    return;

  $time = ctime($data[0]);
  $plr = check_player($data[2]);
  if ($plr < 0)
    return;

  $tm = intval($player[$plr]->team);
  if ($tm < 0 || $tm > 3)
    return;

  $player[$plr]->dropped[$tm]++;
  flag_check($plr, $time, 0);
  $match->flagdrop = $time; // Save for flagkill check
  $match->flagdropplr = $plr;
}

// Flag Pickup
function tagut_flag_pickedup($i, $data)
{
  global $match, $player;

  if ($i < 4 || $match->ended)
    return;

  $time = ctime($data[0]);
  $plr = check_player($data[2]);
  if ($plr < 0)
    return;

  // Data[3] = Team's flag
  $tm = $player[$plr]->team;
  if ($tm >= 0 && $tm <= 3)
  {
    $player[$plr]->pickup[$tm]++;
    flag_check($plr, $time, 1);
  }
}

// Flag Returned
function tagut_flag_returned($i, $data)
{
  global $match, $player;

  if ($i < 4 || $match->ended)
    return;

  $time = ctime($data[0]);
  $plr = check_player($data[2]);
  if ($plr < 0)
  	return;

  $tm = $player[$plr]->team;
  if ($tm < 0 || $tm > 3)
    return;

  $player[$plr]->return[$tm]++;
  flag_check($plr, $time, 0);
}

// Flag Timeout
function tagut_flag_returned_timeout($i, $data)
{
  global $match;

  if ($i < 3 || $match->ended)
    return;

  $time = ctime($data[0]);
  $tm = intval($data[2]);
}

// Flag Captured
function tagut_flag_captured($i, $data)
{
  global $match, $player, $gscores, $assist;

  if ($i < 4 || $match->ended)
    return;

  $time = ctime($data[0]);
  $plr = check_player($data[2]);
  if ($plr < 0)
    return;

  $tm = $player[$plr]->team;
  if ($tm < 0 || $tm > 3)
    return;

  // Add score
  $player[$plr]->tscore[$tm] += 7;
  $match->tot_score += 7;
  $gscores[$match->gscount][0] = intval($plr);   // Player
  $gscores[$match->gscount][1] = $time;  // Time
  $gscores[$match->gscount][2] = 7; // Score
  $gscores[$match->gscount++][3] = intval($player[$plr]->team); // Team

  // Add team score
  teamscore($time, $tm, 1, 2);

  $player[$plr]->capcarry[$tm]++;
  reset($player);
  $playerc = current($player);

  while ($playerc !== FALSE) {
    if (isset($playerc->name) && $playerc->name != "") {
      $i = $playerc->plr;
      if (isset($assist[$i]) && $assist[$i] && $i != $plr) {
        $tm = $player[$i]->team;
        $player[$i]->assist[$tm]++;
        $assist[$i] = 0;
      }
    }
    $playerc = next($player);
  }
  flag_check($plr, $time, 0);
}

// Translocator Throw
function tagut_throw_translocator($i, $data)
{
  if ($i < 3)
    return;

  $time = ctime($data[0]);
  $plr = check_player($data[2]);
}

// Translocate
function tagut_translocate($i, $data)
{
  if ($i < 3)
    return;

  $time = ctime($data[0]);
  $plr = check_player($data[2]);
}

// Failed Translocate
function tagut_translocate_fail($i, $data)
{
  if ($i < 3)
    return;

  $time = ctime($data[0]);
  $plr = check_player($data[2]);
}

// Add TransGib

// Powerup Activate
function tagut_item_activate($i, $data)
{
  if ($i < 4)
    return;

  // AntiGrav Boots
  // Invisibility
  // Damage Amplifier
  $time = ctime($data[0]);
  $plr = check_player($data[3]);
}

// Powerup Deactivate
function tagut_item_deactivate($i, $data)
{
  if ($i < 4)
    return;

  $time = ctime($data[0]);
  $plr = check_player($data[3]);
}

function tagut_assault_timelimit($i, $data)
{
  $time = ctime($data[0]);
}

function tagut_assault_gamecode($i, $data)
{
  $time = ctime($data[0]);
}

function tagut_assault_defender($i, $data)
{
  $time = ctime($data[0]);
}

function tagut_assault_attacker($i, $data)
{
  $time = ctime($data[0]);
}

function tagut_dom_playerscore_update($i, $data)
{
  global $match, $player, $gscores;

  if ($i < 4)
    return;

  $time = ctime($data[0]);
  $plr = check_player($data[2]);
  $score = intval($data[3]);
  if ($plr < 0)
    return;

  $tm = intval($player[$plr]->team);
  if ($tm < 0 || $tm > 1)
    return;

  $scorec = $score - $player[$plr]->tscore[$tm];

  // Add score
  $player[$plr]->tscore[$tm] = $score;
  $match->tot_score += $scorec;
  $gscores[$match->gscount][0] = intval($plr);   // Player
  $gscores[$match->gscount][1] = $time;  // Time
  $gscores[$match->gscount][2] = $scorec; // Score
  $gscores[$match->gscount++][3] = $tm; // Team
}

function tagut_controlpoint_capture($i, $data)
{
  global $match, $player;

  if ($i < 4)
    return;

  $time = ctime($data[0]);
  $plr = check_player($data[3]);
  $cpoint = $data[2]; // Control Point Name - Unused
  if ($plr < 0)
    return;

  $tm = intval($player[$plr]->team);
  if ($tm < 0 || $tm > 1)
    return;

  $player[$plr]->capcarry[$tm]++;
  pointcap($time, $plr, 0); // Should be a point number, not a name.
}

function tagut_dom_score_update($i, $data)
{
  global $match;

  if ($i < 4)
    return;

  $time = ctime($data[0]);
  $tm = intval($data[2]);
  $score = floatval($data[3]);
  if ($tm < 0 || $tm > 1)
    return;

  // Update team score
  $scorec = intval($score - $match->teamls[$tm]);
  teamscore($time, $tm, $scorec, 6);
  $match->team[$tm] = $score;
  $match->teamls[$tm] += $scorec;
}

function tagut_teamcap($i, $data)
{
  global $match, $player;

  if ($i < 3 || $match->ended)
    return;

  $time = ctime($data[0]);
  $plr = check_player($data[2]);
  if ($plr < 0)
    return;

  $tm = intval($player[$plr]->team);
  if ($tm < 0 || $tm > 3)
    return;

  $player[$plr]->taken[$tm]++;
  flag_check($plr, $time, 1);

  teamscore($time, 1-$tm, 1, 2);
}

function tagut_teamrel($i, $data)
{
  global $match, $player;

  if ($i < 4 || $match->ended)
    return;

  $time = ctime($data[0]);
  $plr = check_player($data[2]);
  if ($plr < 0)
  	return;

  $tm = $player[$plr]->team;
  if ($tm < 0 || $tm > 3)
    return;

  $player[$plr]->return[$tm]++;
  flag_check($plr, $time, 0);
}

function tagut_typing($i, $data)
{
  global $player;

  if ($i < 4)
    return;

  $time = ctime($data[0]);
  $plr = check_player($data[3]);
  if ($plr < 0)
    return;

  if (!isset($player[$plr]) || $player[$plr]->name == "")
    $plr = -1;

  // $chatlog[$match->numchat][0] = $plr;
  // $chatlog[$match->numchat][1] = 0;
  // $chatlog[$match->numchat][2] = $time;
  // $chatlog[$match->numchat++][3] = $data[2];
}

function tagut_game_end($i, $data)
{
  global $match, $player, $relog;

  if ($match->ended || $i < 3)
    return;

  $event = strtolower($data[2]);
  $time = ctime($data[0]);

  switch($event) {
    case "fraglimit":
    case "timelimit":
    case "teamscorelimit":
    case "goalscorelimit":
    case "roundlimit":
    case "lastmanstanding":
    case "assault succeeded!":
    case "assault failed!":
    {
      $match->ended = 1;
      break;
    }

    case "mapchange":
    {
      $match->ended = 6;
      break;
    }

    case "serverquit":
    {
      $match->ended = 7;
      break;
    }

    default:
      $match->ended = 2;
  }

  $match->length = $time - $match->starttime;

  for ($n = 0; $n <= $match->maxplayer; $n++) {
    if (isset($relog[$n]) && $relog[$n] < 0) {
      if (isset($player[$n]) && $player[$n]->name != "" && $player[$n]->connected == 1) {
        $ptime = $time - $player[$n]->starttime;
        $tm = $player[$n]->team;
        if ($tm < 0 || $tm > 3)
          $tm = 0;
       $player[$n]->totaltime[$tm] += $ptime;
      }
      if (isset($player[$n])) {
        endspree($n, $time, 0, 0, 0); // End Killing Sprees
        endmulti($n, $time); // End Multi-Kills
        flag_check($n, $time, 0);
      }
    }
  }

  gameevent($time, 1);
  $match->endtime = $time;
}

function tagut_weapshots($i, $data) // 921.55	weap_shotcount	Minigun	0	439
{
  global $match, $player;

  if ($i < 5)
    return;

  $time = ctime($data[0]);
  $plr = check_player($data[3]);
  $weapon = substr($data[2], 0, 35);
  $fired = intval($data[4]);

  list($weaponnum,$weapontype,$weaponsec) = get_weapon($weapon, 0);
  pwa_add($plr, $weaponnum, $fired, -1, -1);
}

function tagut_weaphits($i, $data) // 921.55	weap_hitcount	Minigun	0	60
{
  global $match, $player;

  if ($i < 5)
    return;

  $time = ctime($data[0]);
  $plr = check_player($data[3]);
  $weapon = substr($data[2], 0, 35);
  $hit = intval($data[4]);

  list($weaponnum,$weapontype,$weaponsec) = get_weapon($weapon, 0);
  pwa_add($plr, $weaponnum, -1, $hit, -1);
}

function tagut_weapdamage($i, $data) // 921.55	weap_damagegiven	Minigun	0	921
{
  global $match, $player;

  if ($i < 5)
    return;

  $time = ctime($data[0]);
  $plr = check_player($data[3]);
  $weapon = substr($data[2], 0, 35);
  $damage = intval($data[4]);

  list($weaponnum,$weapontype,$weaponsec) = get_weapon($weapon, 0);
  pwa_add($plr, $weaponnum, -1, -1, $damage);
}

function tagut_say ($i, $data)
{
  global $player, $match, $chatlog;

  if ($i < 4)
    return;

  $time = ctime($data[0]);
  if (substr($data[2], 0, 5) == "spec_")
    $plr = -1;
  else {
    $plr = check_player($data[2]);
    if (!isset($player[$plr]) || $player[$plr]->name == "")
      $plr = -1;
  }

  $chatlog[$match->numchat][0] = $plr;
  $chatlog[$match->numchat][1] = 0;
  $chatlog[$match->numchat][2] = $time;
  $chatlog[$match->numchat++][3] = $data[3];
}

function tagut_teamsay ($i, $data)
{
  global $player, $match, $chatlog;

  if ($i < 4)
    return;

  $time = ctime($data[0]);
  $plr = check_player($data[2]);
  if (!isset($player[$plr]) || $player[$plr]->name == "")
    $plr = -1;

  $chatlog[$match->numchat][0] = $plr;
  $chatlog[$match->numchat][1] = $player[$plr]->team + 1;
  $chatlog[$match->numchat][2] = $time;
  $chatlog[$match->numchat++][3] = $data[3];
}

?>