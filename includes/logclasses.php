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

if (preg_match("/logclasses.php/i", $_SERVER["PHP_SELF"])) {
  echo "Access denied.\n";
  die();
}

class Match {
  var $ended = 0;
  var $started = 0;
  var $ngfound = 0;
  var $uttype = 0;
  var $numplayers = 0;
  var $numhumans = 0;
  var $maxplayer = 0;
  var $tot_score = 0;
  var $tot_kills = 0;
  var $tot_deaths = 0;
  var $tot_suicides = 0;
  var $teamkills = 0;
  var $headshots = 0;
  var $password = 0;
  var $gamestats = 0;
  var $fraglimit = 0;
  var $timelimit = 0;
  var $overtime = 0;
  var $minplayers = 0;
  var $starttime = 0;
  var $endtime = 0;
  var $length = 0;
  var $servernum = 0;
  var $map = "";
  var $mapfile = "";
  var $author = "";
  var $matchdate = "";
  var $startdate = "";
  var $mutators = "";
  var $servername = "";
  var $shortname = "";
  var $admin = "";
  var $email = "";
  var $adminsl = "";
  var $emailsl = "";
  var $servernamesl = "";
  var $shortnamesl = "";
  var $linksetup = "";
  var $gtype = "";
  var $gname = "";
  var $timezone = 0;
  var $gametype = 0;
  var $gametnum = 0;
  var $mapnum = -1;
  var $firstblood = -1;
  var $lastball = -1;
  var $serverversion = 0;
  var $friendlyfirescale = 0;
  var $difficulty = -1;
  var $logger = 0;
  var $maxwave = 0;
  var $rpg = 0;
  var $gamespeed = 1.00;
  var $endtimedelay = 0.0;
  var $rankset = 0;
  var $lastman = -1;
  var $numteams = 0;
  var $teamgame = 0;
  var $translocator = 0;
  var $mapvoting = 0;
  var $kickvoting = 0;
  var $balanceteams = 0;
  var $playersbalanceteams = 0;
  var $healthforkills = 0;
  var $camperalarm = 0;
  var $fullammo = 0;
  var $allowsuperweapons = 1;
  var $allowpickups = 1;
  var $allowadrenaline = 1;
  var $flagdrop = 0;
  var $flagdropplr = -1;
  var $curteam = 0;
  var $lastobj = 0;
  var $team = array(0, 0, 0, 0);
  var $teamls = array(0, 0, 0, 0);

  var $numevents = 0;
  var $maxpickups = 0;
  var $gkcount = 0;
  var $gscount = 0;
  var $tkcount = 0;
  var $numchat = 0;
}

class Player {
  var $plr = -1;
  var $name = "";
  var $bot = 0;
  var $lives = 0;
  var $ranks = 0;
  var $rankc = 0;
  var $suicr = 0;
  var $kills = array(0, 0, 0, 0);
  var $deaths = array(0, 0, 0, 0);
  var $suicides = array(0, 0, 0, 0);
  var $starttime = 0;
  var $headshots = 0;
  var $firstblood = 0;
  var $multi = array(0, 0, 0, 0, 0, 0, 0);
  var $spree = array(0, 0, 0, 0, 0, 0);
  var $spreet = array(0, 0, 0, 0, 0 ,0);
  var $spreek = array(0, 0, 0, 0, 0, 0);
  var $combo = array(0, 0, 0, 0);
  var $totaltime = array(0, 0, 0, 0);
  var $connected = 0;
  var $team = -1;
  var $user = "";
  var $id = "";
  var $key = 0;
  var $hash = "";
  var $ip = "";
  var $netspeed = 0;
  var $ping = 0;
  var $pingcount = 0;
  var $tscore = array(0.0, 0.0, 0.0, 0.0);
  var $teamkills = array(0, 0, 0, 0);
  var $teamdeaths = array(0, 0, 0, 0);
  var $pickup = array(0, 0, 0, 0);
  var $taken = array(0, 0, 0, 0);
  var $dropped = array(0, 0, 0, 0);
  var $assist = array(0, 0, 0, 0);
  var $typekill = array(0, 0, 0, 0);
  var $return = array(0, 0, 0, 0);
  var $capcarry = array(0, 0, 0, 0);
  var $tossed = array(0, 0, 0, 0);
  var $holdtime = array(0, 0, 0, 0);
  var $transgib = 0;
  var $headhunter = 0;
  var $flakkills = 0;
  var $flakmonkey = 0;
  var $combokills = 0;
  var $combowhore = 0;
  var $roadrampage = 0;
  var $carjack = 0;
  var $roadkills = 0;
  var $rank = 0;
  var $num = 0;
  var $named = false;
  var $extraa = array(0, 0, 0, 0);
  var $extrab = array(0, 0, 0, 0);
  var $extrac = array(0, 0, 0, 0);
  var $globalmatches = 0;
  var $globaltime = 0;
  var $lms = 0;

  function is_bot()
  {
  	return $this->bot;
  }

  function is_named()
  {
  	return $this->named;
  }
}

?>