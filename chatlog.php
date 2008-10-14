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

require("includes/main.inc.php");

$matchnum = -1;
check_get($matchnum, "match");
if (!is_numeric($matchnum))
  $matchnum = -1;
if ($matchnum <= 0) {
  echo "Run from the main index program.<br />\n";
  exit;
}

$link = sql_connect();

// Load game data
$result = sql_queryn($link, "SELECT * FROM {$dbpre}matches WHERE gm_num=$matchnum LIMIT 1");
if (!$result) {
  echo "Games database error.<br />\n";
  exit;
}
$row = sql_fetch_assoc($result);
if (!$row) {
  echo "Game not found in database.<br />\n";
  exit;
}
while (list ($key, $val) = each ($row))
  ${$key} = $val;
sql_free_result($result);

// Set game type
$result = sql_queryn($link, "SELECT tp_type FROM {$dbpre}type WHERE tp_num=$gm_type LIMIT 1");
$row = sql_fetch_row($result);
if (!$row) {
  echo "Error locating game type.<br />\n";
  exit;
}
$gametval = $row[0];
sql_free_result($result);

// Load Players
$result = sql_queryn($link, "SELECT gp_num,gp_pnum,gp_bot,plr_name FROM {$dbpre}gplayers,{$dbpre}players WHERE gp_match=$matchnum AND {$dbpre}players.pnum=gp_pnum");
if (!$result) {
  echo "Game player list database error.<br />\n";
  exit;
}
while($row = sql_fetch_assoc($result)) {
  $num = $row["gp_num"];
  $gplayer[$num] = $row;
  $gplayer[$num]["gp_name"] = stripspecialchars($row["plr_name"]);
}
sql_free_result($result);

// Load Weapon Descriptions
$result = sql_queryn($link, "SELECT wp_num,wp_type,wp_desc FROM {$dbpre}weapons");
if (!$result) {
  echo "Error loading weapons descriptions.<br />\n";
  exit;
}
$maxweapon = 0;
$weapons = array();
$weaponst = array();
while($row = sql_fetch_row($result)) {
  $num = $row[0];
  $weapons[$num] = $row[2];
  $weaponst[$num] = $row[1];
  if ($num > $maxweapon)
    $maxweapon = $num;
}
sql_free_result($result);

//=============================================================================
//========== Chat Log =========================================================
//=============================================================================

$start = strtotime($gm_start);
$delay = $start - strtotime($gm_init);
$matchdate = formatdate($start, 1);

// Get Server Name
$result = sql_queryn($link, "SELECT sv_name,sv_shortname FROM {$dbpre}servers WHERE sv_num=$gm_server LIMIT 1");
if (!$result) {
  echo "Server database error.<br />\n";
  exit;
}
list($sv_name,$sv_shortname) = sql_fetch_row($result);
sql_free_result($result);
if ($useshortname)
  $server = stripspecialchars($sv_shortname);
else
  $server = stripspecialchars($sv_name);

// Get Map Name
$result = sql_queryn($link, "SELECT mp_name FROM {$dbpre}maps WHERE mp_num=$gm_map LIMIT 1");
if (!$result) {
  echo "Map database error.<br />\n";
  exit;
}
list($map) = sql_fetch_row($result);
sql_free_result($result);
$map = stripspecialchars($map);

echo <<<EOF
<font size="1"><br /></font>
<table cellpadding="1" cellspacing="1" border="0" class="box">
  <tr>
    <td class="heading" colspan="3" align="center">Chat Log for $server - $map<br />on $matchdate</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="55">Minutes</td>
    <td class="smheading" align="center" width="200">Player</td>
    <td class="smheading" align="center" width="450">Text</td>
  </tr>

EOF;

//========== System Events ====================================================
$numchat = 0;
$chatlog = array();
$result = sql_querynb($link, "SELECT * FROM {$dbpre}gevents WHERE ge_match=$matchnum AND ge_event BETWEEN 2 AND 14 ORDER BY ge_num");
if (!$result) {
  echo "Error loading events log.<br />\n";
  exit;
}
while ($row = sql_fetch_assoc($result)) {
  $time = $row["ge_time"] - ($delay * $gm_timeoffset);
  $event = $row["ge_event"];
  $plr = $row["ge_plr"];
  $reas = $row["ge_reason"];
  $quant = $row["ge_quant"];
  $item = $row["ge_item"];
  $opp = $row["ge_opponent"];

  $reason = "";
  switch ($event) {
    case 2: // Connect/Disconnect
      switch ($reas) {
        case 0:
          if ($gametval < 2 || $gametval > 4)
            $reason = "Connected";
          else
            $reason = "";
          $prior = 2;
          break;
        case 1:
          $reason = "Disconnected";
          $prior = 4;
          break;
        default:
          $reason = "";
      }
      $sysclass = "chatsys";
      break;
    case 3: // Match Start/End
      switch ($reas) {
        case 0:
          $reason = "Match Start";
          $prior = 1;
          $time = 0;
          break;
        case 1:
          $reason = "Match Ended";
          $prior = 5;
          break;
        default:
          $reason = "";
      }
      $sysclass = "chatsys";
      $plr = -1;
      break;
    case 4: // Team Change
      $reason = "Team Change to {$teamcolor[$quant]} Team";
      $sysclass = $teamchat[$quant];
      $prior = 3;
      break;
    case 5: // Team Score
      if ($gametval == 9) // Skip for Invasion
        break;
      switch ($reas) {
        case 1:
          $reason = "{$teamcolor[$plr]} Team Scored Frag!";
          break;
        case 2: // Show only for 2 team games
          if ($gm_numteams < 3)
            $reason = "{$teamcolor[$plr]} Team Captured Flag!";
          break;
        case 3:
          $reason = "{$teamcolor[$plr]} Team Carried Ball for Score!";
          break;
        case 4:
          $reason = "{$teamcolor[$plr]} Team Scored Ball Throw!";
          break;
        case 5:
          $reason = "{$teamcolor[$plr]} Team Dominated!";
          break;
        case 6:
          $reason = "{$teamcolor[$plr]} Team Captured a Point!";
          break;
        default:
          if ($quant > 0)
            $reason = "{$teamcolor[$plr]} Team Scored!";
          break;
      }
      $sysclass = $teamscore[$plr];
      $plr = -1;
      $prior = 2;
      break;
    case 6: // Weapon Special
      switch ($reas) {
        case 1:
          $reason = "Headhunter!";
          $sysclass = "chatweapspec";
          $prior = 3;
          break;
        case 2:
          $reason = "Flak Monkey!";
          $sysclass = "chatweapspec";
          $prior = 3;
          break;
        case 3:
          if ($item > 0)
            $reason = "Carjacked a {$weapons[$item]}";
          else
            $reason = "Carjacked a vehicle";
          $sysclass = "chatweapspec";
          $prior = 3;
          break;
        case 4:
          $reason = "Combo Whore!";
          $sysclass = "chatweapspec";
          $prior = 3;
          break;
        default:
          $reason = "";
      }
      break;
    case 7: // Assault Objective
      if ($plr == 1) {
        $teamname = "Red";
        $sysclass = "redteamscore";
      }
      else {
        $teamname = "Blue";
        $sysclass = "blueteamscore";
      }
      $plr = -1;
      $prior = 3;
      $result2 = sql_queryn($link, "SELECT obj_priority,obj_secondary,obj_desc FROM {$dbpre}objectives WHERE obj_num=$quant LIMIT 1");
      list($objpri,$objsec,$objname) = sql_fetch_row($result2);
      sql_free_result($result2);
      $reason = $teamname." team completed objective: $objname.";
      break;
    case 8: // Assault Round
      if ($plr == 1) {
        $teamname = "Red";
        $sysclass = "redteamscore";
      }
      else {
        $teamname = "Blue";
        $sysclass = "blueteamscore";
      }
      $plr = -1;
      $prior = 3;
      if ($reas == 1)
        $reason = $teamname." team successfully attacked.";
      else
        $reason = $teamname." team successfully defended.";
      break;
    case 9: // Invasion Wave
      $reason = "Begin Invasion Wave $plr";
      $sysclass = "chatsys";
      $plr = -1;
      $prior = 3;
      break;
    case 10: // Map/Kick Vote
      $sysclass = "votesys";
      $prior = 3;
      switch ($reas) {
        case 1:
        case 2:
          $result2 = sql_queryn($link, "SELECT ed_desc FROM {$dbpre}eventdesc WHERE ed_num=$quant LIMIT 1");
          if (!$result2) {
            echo "Error accessing event descriptions database.{$break}\n";
            exit;
          }
          $row2 = sql_fetch_row($result2);
          sql_free_result($result2);
          if ($row2)
            $mapname = "'{$row2[0]}'";
          else
            $mapname = "an unknown map";
          if ($reas == 1) {
            if ($plr >= 0)
              $reason = "Map Vote forced by admin for $mapname";
            else
              $reason = "Map Vote succeeded for $mapname";
            $prior = 4;
          }
          else {
            if ($item == 1)
              $votes = "$item vote";
            else if ($item == 0)
              $votes = "admin";
            else
              $votes = "$item votes";
            $reason = "Voted for $mapname ($votes)";
          }
          break;
        case 3:
        case 4:
          $kplayer = $gplayer[$quant]["gp_name"];
          if ($reas == 3) {
            if ($plr >= 0)
              $reason = "Kick Vote of $kplayer forced by admin";
            else
              $reason = "Kick Vote for $kplayer succeeded";
            $prior = 4;
          }
          else
            $reason = "Voted to kick $kplayer";
          break;
        case 5:
        case 6:
          $result2 = sql_queryn($link, "SELECT ed_desc FROM {$dbpre}eventdesc WHERE ed_num=$quant LIMIT 1");
          if (!$result2) {
            echo "Error accessing event descriptions database.{$break}\n";
            exit;
          }
          $row2 = sql_fetch_row($result2);
          sql_free_result($result2);
          if ($row2)
            $gamename = "'{$row2[0]}'";
          else
            $gamename = "an unknown game type";
          if ($reas == 6) {
            if ($plr >= 0)
              $reason = "Game Type Vote forced by admin for $gamename";
            else
              $reason = "Game Type Vote succeeded for $gamename";
            $prior = 4;
          }
          else {
            if ($item == 1)
              $votes = "$item vote";
            else if ($item == 0)
              $votes = "admin";
            else
              $votes = "$item votes";
            $reason = "Voted for $gamename ($votes)";
          }
          break;
        default:
          $reason = "";
      }
      break;
    case 11: // Mutant
      if ($reas) {
      	if ($plr >= 0)
          $reason = "Mutant";
        else
          $reason = "Mutant Suicided";
        $sysclass = "chatred";
      }
      else {
      	if ($plr >= 0)
          $reason = "Bottom Feeder";
        else
          $reason = "Bottom Feeder Suicided";
        $sysclass = "chatblue";
      }
      $prior = 3;
      break;
    case 12: // LMS Out
      if ($reas) {
        $reason = "Last Man Standing";
        $sysclass = "chatblue";
        $prior = 4;
      }
      else {
        $reason = "Player Out";
        $sysclass = "chatred";
        $prior = 3;
      }
      break;
    case 13: // Double Domination
      if ($reas)
        $reason = "Captured Point B";
      else
        $reason = "Captured Point A";
      $sysclass = "chatred";
      $prior = 4;
      break;
    case 14: // Team Event
      if ($reas == 0 && $gm_numteams > 2) { // Flag Capture for 4 team games
      	if ($opp >= 0)
          $reason = "{$teamcolor[$plr]} Team Captured {$teamcolor[$opp]} Flag!";
        else
          $reason = "{$teamcolor[$plr]} Team Captured Flag!";
        $sysclass = $teamscore[$plr];
        $plr = -1;
        $prior = 2;
      }
      break;
  }
  if ($reason != "") {
    $chatlog[0][$numchat] = $time;
    $chatlog[1][$numchat] = $plr;
    $chatlog[2][$numchat] = 0;
    $chatlog[3][$numchat] = $sysclass;
    $chatlog[4][$numchat] = $reason;
    $chatlog[5][$numchat++] = $prior;
  }
}
sql_free_result($result);

//========== Kills ============================================================
$result = sql_queryn($link, "SELECT * FROM {$dbpre}gkills WHERE gk_match=$matchnum");
if (!$result) {
  echo "Error reading gkills data.<br />\n";
  exit;
}
while ($row = sql_fetch_assoc($result)) {
  $killern = $row["gk_killer"];
  $victimn = $row["gk_victim"];
  if ($victimn >= 0) {
    $weapon = $weapons[$row["gk_kweapon"]];
    $weapont = $weaponst[$row["gk_kweapon"]];
    if ($killern < 0) {
      $chatlog[1][$numchat] = $victimn;
      if (!strcmp($weapon, "Suicided") || !strcmp($weapon, "Drowned"))
        $chatlog[4][$numchat] = "$weapon";
      else if (!strcmp($weapon, "Corroded") || !strcmp($weapon, "Crushed") || !strcmp($weapon, "Gibbed") || !strcmp($weapon, "Depressurized"))
        $chatlog[4][$numchat] = "Was $weapon";
      else if (!strcmp($weapon, "Fell"))
        $chatlog[4][$numchat] = "Fell to their death";
      else if (!strcmp($weapon, "Fell Into Lava"))
        $chatlog[4][$numchat] = "Fell into Lava";
      else if (!strcmp($weapon, "Swam Too Far"))
        $chatlog[4][$numchat] = "Tried to Swim Too Far";
      else if (!strcmp($weapon, "Gibbed by Convoy"))
        $chatlog[4][$numchat] = "Gibbed by the Convoy";
      else {
        $wfl = strtoupper($weapon[0]);
        $weapon = stripspecialchars($weapon);
        if ($wfl == 'A' || $wfl == 'E' || $wfl == 'I' || $wfl == 'O' || $wfl == 'U' || $wfl == 'Y')
          $chatlog[4][$numchat] = "Died from an $weapon";
        else
          $chatlog[4][$numchat] = "Died from a $weapon";
      }
    }
    else if ($killern == $victimn) {
      $chatlog[1][$numchat] = $killern;
      if (!strcmp($weapon, "Suicided") || !strcmp($weapon, "Drowned"))
        $chatlog[4][$numchat] = "$weapon";
      else if (!strcmp($weapon, "Corroded") || !strcmp($weapon, "Crushed") || !strcmp($weapon, "Gibbed") || !strcmp($weapon, "Depressurized"))
        $chatlog[4][$numchat] = "Was $weapon";
      else if (!strcmp($weapon, "Fell"))
        $chatlog[4][$numchat] = "Fell to their death";
      else if (!strcmp($weapon, "Fell Into Lava"))
        $chatlog[4][$numchat] = "Fell into Lava";
      else if (!strcmp($weapon, "Swam Too Far"))
        $chatlog[4][$numchat] = "Tried to Swim Too Far";
      else if (!strcmp($weapon, "Vehicle Explosion"))
        $chatlog[4][$numchat] = "Suicided from a Vehicle Explosion";
      else if (!strcmp($weapon, "Reckless Driving"))
        $chatlog[4][$numchat] = "Suicided from Reckless Driving";
      else {
        $wfl = strtoupper($weapon[0]);
        $weapon = stripspecialchars($weapon);
        if ($wfl == 'A' || $wfl == 'E' || $wfl == 'I' || $wfl == 'O' || $wfl == 'U' || $wfl == 'Y')
          $chatlog[4][$numchat] = "Suicided with an $weapon";
        else
          $chatlog[4][$numchat] = "Suicided with a $weapon";
      }
    }
    else {
      $chatlog[1][$numchat] = $killern;
      $victim = $gplayer[$victimn]["gp_name"];
      if (!strcmp($weapon, "Fell"))
        $chatlog[4][$numchat] = "Knocked $victim off a ledge";
      else if (!strcmp($weapon, "Fell Into Lava"))
        $chatlog[4][$numchat] = "Pushed $victim into lava";
      else if (!strcmp($weapon, "Crushed") || !strcmp($weapon, "Telefragged") || !strcmp($weapon, "Depressurized"))
        $chatlog[4][$numchat] = "$weapon $victim";
      else {
        $wfl = strtoupper($weapon[0]);
      	$weapon = stripspecialchars($weapon);
        if (stristr($weapont, "HeadShot"))
          $ktype = "<span class=\"chatweapspec\">Headshot</span>";
        else
          $ktype = "Killed";
        if ($wfl == 'A' || $wfl == 'E' || $wfl == 'I' || $wfl == 'O' || $wfl == 'U' || $wfl == 'Y')
          $chatlog[4][$numchat] = "$ktype $victim with an $weapon";
        else
          $chatlog[4][$numchat] = "$ktype $victim with a $weapon";
      }
    }
    $chatlog[0][$numchat] = $row["gk_time"] - ($delay * $gm_timeoffset);
    $chatlog[2][$numchat] = 0;
    $chatlog[3][$numchat] = "chatkill";
    $chatlog[5][$numchat++] = 2;
  }
}
sql_free_result($result);

//========== Chat =============================================================
$result = sql_queryn($link, "SELECT * FROM {$dbpre}gchat WHERE gc_match=$matchnum ORDER BY gc_num");
if (!$result) {
  echo "Error loading chat log.<br />\n";
  exit;
}
while ($row = sql_fetch_assoc($result)) {
  $chatlog[0][$numchat] = $row["gc_time"] - ($delay * $gm_timeoffset);
  $chatlog[1][$numchat] = $row["gc_plr"];
  $chatlog[2][$numchat] = $row["gc_team"];
  $text = $row["gc_text"];
  $tm = $row["gc_team"];
  if ($tm > 0)
    $chatcol = $teamchat[$tm - 1];
  else {
    $chatcol = "chat";
    $sadmin = substr($text, -33);
    if ($sadmin && !strcmp($sadmin, "ged in as a server administrator."))
      $chatcol = "chatsys";
    else if ($sadmin && !strcmp($sadmin, " gave up administrator abilities."))
      $chatcol = "chatsys";
  }
  $chatlog[3][$numchat] = "$chatcol";
  $chatlog[4][$numchat] = $text;
  $chatlog[5][$numchat++] = 3;
}
sql_free_result($result);

//========== Sort and display =================================================
if ($numchat) {
  array_multisort($chatlog[0], SORT_ASC, SORT_NUMERIC, // Time
                  $chatlog[5], SORT_ASC, SORT_NUMERIC, // Priority
                  $chatlog[1], SORT_ASC, SORT_STRING,  // Player
                  $chatlog[2], SORT_ASC, SORT_NUMERIC, // Team
                  $chatlog[3], SORT_DESC, SORT_STRING, // Class
                  $chatlog[4], SORT_ASC, SORT_STRING); // Text
}

for ($i = 0; $i < $numchat; $i++) {
  $nodisplay = 0;
  $time = sprintf("%0.2f", $chatlog[0][$i] / 6000);
  $plr = $chatlog[1][$i];
  if ($plr >= 0) {
    if (isset($gplayer[$plr])) {
      $name = $gplayer[$plr]["gp_name"];
      $bot = $gplayer[$plr]["gp_bot"];
      if ($bot)
        $nameclass = "darkbot";
      else
        $nameclass = "darkhuman";
    }
    else
      $nodisplay = 1;
  }
  else {
    $name = "";
    $nameclass = "dark";
  }
  if (!$nodisplay) {
    $team = $chatlog[2][$i];
    $cclass = $chatlog[3][$i];
    if ($cclass == "chatkill") // Special handling for kill messages
      $text = $chatlog[4][$i];
    else
      $text = stripspecialchars($chatlog[4][$i]);

    echo <<<EOF
  <tr>
    <td class="dark" align="center">$time</td>
    <td class="$nameclass" align="center">$name</td>
    <td class="$cclass" align="left">$text</td>
  </tr>

EOF;
  }
}
echo "</table>\n";

echo <<<EOF
<br />
<table cellpadding="1" cellspacing="1" border="0" class="box">
  <tr>
    <td class="smheading" align="center" colspan="2" width="350">Chat Color Key</td>
  </tr>
  <tr>
    <td class="chat" align="left">Player Chat Messages</td>
    <td class="chatsys" align="left">Game Event Messages</td>
  </tr>
  <tr>
    <td class="chatblue" align="left">Blue Team Chat Messages</td>
    <td class="chatkill" align="left">Kill/Suicide Events</td>
  </tr>
  <tr>
    <td class="chatred" align="left">Red Team Chat Messages</td>
    <td class="blueteamscore" align="left">Blue Team Score Events</td>
  </tr>
  <tr>
    <td class="chatweapspec" align="left">Weapon Special Events</td>
    <td class="redteamscore" align="left">Red Team Score Events</td>
  </tr>
</table>

EOF;

sql_close($link);

echo <<<EOF
</td></tr></table>

</body>
</html>

EOF;

?>