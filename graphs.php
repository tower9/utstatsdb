<?php

/*
    UTStatsDB
    Copyright (C) 2002-2005  Patrick Contreras / Paul Gallier

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

require("includes/statsdb.inc.php");
require("includes/logsql.php");

$magic = get_magic_quotes_gpc();
if ($magic) {
  $matchnum = intval(stripslashes($_GET["match"]));
  $type = intval(stripslashes($_GET["type"]));
}
else {
  $matchnum = intval($_GET["match"]);
  $type = intval($_GET["type"]);
}

if (!$matchnum || !$type || $type < 1 || $type > 4) {
  echo "Run from the main index program.<br>\n";
  exit;
}

//=============================================================================
//========== Configure Main Variables =========================================
//=============================================================================

$x = 550; // Graph image width
$y = 180; // Graph image height
$minx = 36; // Left margin
$maxy = 6; // Top margin
$maxx = $x - 10; // Right margin
$miny = $y - 32; // Bottom margin
$gwa = $maxx - $minx + 1; // Graph Width Area
$gha = $miny - $maxy + 1; // Graph Height Area
$font = 1; // Label font size
$legendfont = 3; // Legend font size
$minstep = 8; // Minimum graph steps

//=============================================================================
//========== Retreive Player Data =============================================
//=============================================================================

// Retreive game info
$result = sql_query("SELECT gm_starttime,gm_length,gm_numplayers,gm_numteams FROM {$dbpre}matches WHERE gm_num=$matchnum LIMIT 1");
if (!$result) {
  echo "Match database error.<br>\n";
  exit;
}
if (!(list($gm_starttime,$gm_length,$gm_numplayers,$gm_numteams) = sql_fetch_row($result))) {
  echo "Error locating match data.<br>\n";
  exit;
}
sql_free_result($result);
$gm_starttime = intval(round($gm_starttime / 100));
$gm_length = intval(round($gm_length / 100));

if ($type == 2) { // Team Scoring Graph
  // Read tkills for each team for the game - set score and time
  $lowscore = $highscore = 0;
  $team[0] = $team[1] = $team[2] = $team[3] = 0;
  $link = sql_connect();
  $lines = $gm_numteams;
  for ($tm = 0; $tm < $gm_numteams; $tm++) { // 0 = Red / 1 = Blue / 3 = Yellow / 4 = Green
    $fg = 0;
    $result = sql_queryn($link, "SELECT tk_score,tk_time FROM {$dbpre}tkills WHERE tk_match=$matchnum AND tk_team=$tm ORDER BY tk_time");
    if (!$result)
    {
      echo "Match kill database error.<br>\n";
      sql_close($link);
      exit;
    }
    while($row = sql_fetch_row($result)) {
      $fg += (int) $row[0];
      $pscore[$tm + 1][] = $fg;
      $ptime[$tm + 1][] = intval(round($row[1] / 100)) - $gm_starttime;
      if ($fg < $lowscore)
        $lowscore = $fg;
      if ($fg > $highscore)
        $highscore = $fg;
    }
    $team[$tm] = $fg;
  }
  sql_free_result($result);
  sql_close($link);

  // Set rank by team color
  $ranks[1] = 2;
  $ranks[2] = 1;
  $ranks[3] = 3;
  $ranks[4] = 4;
}
else if ($type == 3) { // Player Score Graph
  // Read score for each player for the game - set score and time
  $link = sql_connect();

  // Set rankings
  $lines = $maxplayer = 0;
  $i = 1;
  $result = sql_queryn($link, "SELECT gp_num FROM {$dbpre}gplayers USE INDEX (gp_gnumrank) WHERE gp_match=$matchnum ORDER BY gp_rank");
  while($row = sql_fetch_row($result)) {
    $ranks[$i++] = $row[0];
    $lines++;
    if ($row[0] > $maxplayer)
      $maxplayer = $row[0];
  }
  sql_free_result($result);

  if ($lines > 8)
    $lines = 8;

  for ($i = 0; $i <= $maxplayer; $i++)
    $pscorefg[$i] = $plowscore[$i] = $phighscore[$i] = 0;

  $result = sql_queryn($link, "SELECT gs_player,gs_time,gs_score FROM {$dbpre}gscores WHERE gs_match=$matchnum ORDER BY gs_time");
  while(list($num,$time,$score) = sql_fetch_row($result)) {
    $pscorefg[$num] += $score;
    $pscore[$num][] = $pscorefg[$num];
    $ptime[$num][] = intval(round($time / 100)) - $gm_starttime;
    if ($pscorefg[$num] < $plowscore[$num])
      $plowscore[$num] = $pscorefg[$num];
    if ($pscorefg[$num] > $phighscore[$num])
      $phighscore[$num] = $pscorefg[$num];
/*
    if ($pscorefg[$num] < $lowscore)
      $lowscore = $pscorefg[$num];
    if ($pscorefg[$num] > $highscore)
      $highscore = $pscorefg[$num];
*/
  }
  sql_free_result($result);
  sql_close($link);
}
else if ($type == 4) { // Last Man Standing Graph
  // Read gkills for each player for the game - set frag number and time
  $lowscore = $highscore = 0;
  $link = sql_connect();

  // Set rankings
  $lines = $maxplayer = $lives = 0;
  $i = 1;
  $result = sql_queryn($link, "SELECT gp_num,gp_pickup0 FROM {$dbpre}gplayers WHERE gp_match=$matchnum ORDER BY gp_rank");
  while($row = sql_fetch_row($result)) {
  	$num = intval($row[0]);
    $ranks[$i++] = $num;
    $startlives[$num] = intval($row[1]);
    if ($startlives[$num] > $lives)
      $lives = $startlives[$num];
    $lines++;
    if ($num > $maxplayer)
      $maxplayer = $num;
  }
  sql_free_result($result);

  if ($lines > 8)
    $lines = 8;

  $startlivesset = false;
  if ($lives > 0)
    $startlivesset = true;
  else {
    // Find highest death count if start lives not set
    $result = sql_queryn($link, "SELECT COUNT(*) FROM {$dbpre}gkills WHERE gk_match=$matchnum GROUP BY gk_victim");
    while($row = sql_fetch_row($result)) {
      if (intval($row[0]) > $lives)
        $lives = intval($row[0]);
    }
    sql_free_result($result);
  }

  for ($i = 0; $i <= $maxplayer; $i++) {
  	if ($startlivesset) {
  	  if (isset($startlives[$i]))
        $pscorefg[$i] = $plowscore[$i] = $phighscore[$i] = $startlives[$i];
    }
    else
      $pscorefg[$i] = $plowscore[$i] = $phighscore[$i] = $startlives[$i] = $lives;
  }

  $result = sql_queryn($link, "SELECT gk_killer,gk_victim,gk_time FROM {$dbpre}gkills WHERE gk_match=$matchnum ORDER BY gk_time");
  while(list($killer,$victim,$time) = sql_fetch_row($result)) {
    if ($victim >= 0) {
      $pscorefg[$victim]--;
      $pscore[$victim][] = $pscorefg[$victim];
      $ptime[$victim][] = intval(round($time / 100)) - $gm_starttime;
    }
    if ($pscorefg[$victim] < $plowscore[$victim])
      $plowscore[$victim] = $pscorefg[$victim];
    if ($pscorefg[$victim] > $phighscore[$victim])
      $phighscore[$victim] = $pscorefg[$victim];
  }
  sql_free_result($result);
  sql_close($link);
}
else { // Player Frag Graph
  // Read gkills for each player for the game - set frag number and time
  $lowscore = $highscore = 0;
  $link = sql_connect();

  // Set rankings
  $lines = $maxplayer = 0;
  $i = 1;
  $result = sql_queryn($link, "SELECT gp_num FROM {$dbpre}gplayers WHERE gp_match=$matchnum ORDER BY gp_rank");
  while($row = sql_fetch_row($result)) {
    $ranks[$i++] = $row[0];
    $lines++;
    if ($row[0] > $maxplayer)
      $maxplayer = $row[0];
  }
  sql_free_result($result);

  if ($lines > 8)
    $lines = 8;

  for ($i = 0; $i <= $maxplayer; $i++)
    $pscorefg[$i] = $plowscore[$i] = $phighscore[$i] = 0;

  $result = sql_queryn($link, "SELECT gk_killer,gk_victim,gk_time FROM {$dbpre}gkills WHERE gk_match=$matchnum ORDER BY gk_time");
  while(list($killer,$victim,$time) = sql_fetch_row($result)) {
    if ($killer > -2) {
      if ($killer == $victim || $killer < 0) { // Suicide
        $num = $victim;
        $pscorefg[$num]--;
        $pscore[$num][] = $pscorefg[$num];
        $ptime[$num][] = intval(round($time / 100)) - $gm_starttime;
      }
      else {
        $num = $killer;
      	$pscorefg[$num]++;
        $pscore[$num][] = $pscorefg[$num];
        $ptime[$num][] = intval(round($time / 100)) - $gm_starttime;
      }
      if ($pscorefg[$num] < $plowscore[$num])
        $plowscore[$num] = $pscorefg[$num];
      if ($pscorefg[$num] > $phighscore[$num])
        $phighscore[$num] = $pscorefg[$num];
    }
  }
  sql_free_result($result);
  sql_close($link);
}

if ($type != 2) {
  $lowscore = $highscore = 0;
  for ($i = 1; $i <= $lines; $i++) {
    if ($plowscore[$ranks[$i]] < $lowscore)
      $lowscore = $plowscore[$ranks[$i]];
    if ($phighscore[$ranks[$i]] > $highscore)
      $highscore = $phighscore[$ranks[$i]];
  }
}

$fsr = $highscore - $lowscore;
if ($fsr <= 0)
  $fsr = 1;
$ftr = $gm_length;

//=============================================================================
//========== Setup Grid Increments ============================================
//=============================================================================

// Time Steps = 1, 2.5, 5, 10, 20, 50, 100, 500, 1000, 5000, 10000, 50000, 100000
// Max indices = 9
if ($gm_length <= 8 * 60)
  $step = 1;
else if ($gm_length <= 20 * 60)
  $step = 2.5;
else if ($gm_length <= 40 * 60)
  $step = 5;
else if ($gm_length <= 80 * 60)
  $step = 10;
else if ($gm_length <= 160 * 60)
  $step = 20;
else if ($gm_length <= 400 * 60)
  $step = 50;
else if ($gm_length <= 800 * 60)
  $step = 100;
else if ($gm_length <= 4000 * 60)
  $step = 500;
else if ($gm_length <= 8000 * 60)
  $step = 1000;
else if ($gm_length <= 40000 * 60)
  $step = 5000;
else if ($gm_length <= 80000 * 60)
  $step = 10000;
else if ($gm_length <= 400000 * 60)
  $step = 50000;
else if ($gm_length <= 800000 * 60)
  $step = 100000;
// (int) ($gm_length / 60)
for ($i = 0, $n = 1; $i <= $gm_length / 60; $i += $step, $n++)
  $xindex[$n] = $i;
$gridx = (int) (($gm_length / 60) / $step) + 1;

// Score Steps = 1, 2, 5, 10, 20, 50, 100, 500, 1000, 5000, 10000
// Max indices = 5
$range = $fsr;
if ($range <= 4)
  $step = 1;
else if ($range <= 8)
  $step = 2;
else if ($range <= 20)
  $step = 5;
else if ($range <= 40)
  $step = 10;
else if ($range <= 80)
  $step = 20;
else if ($range <= 100)
  $step = 25;
else if ($range <= 200)
  $step = 50;
else if ($range <= 400)
  $step = 100;
else if ($range <= 800)
  $step = 200;
else if ($range <= 1000)
  $step = 250;
else if ($range <= 2000)
  $step = 500;
else if ($range <= 4000)
  $step = 1000;
else if ($range <= 8000)
  $step = 2000;
else if ($range <= 10000)
  $step = 2500;
else if ($range <= 20000)
  $step = 5000;
else if ($range <= 40000)
  $step = 10000;
if ($lowscore < 0) {
  $base = ceil($lowscore / $step) * $step;
  if ($base == 0)
    $base = 0;
}
else
  $base = 0;
for ($i = $base, $n = 1; $i <= $range; $i += $step) {
  $yindex[$n] = $i;
  $n++;
}
$gridy = floor($range / $step) + 1;
   
//=============================================================================
//========== Initialize Image =================================================
//=============================================================================

if (ImageTypes() & IMG_PNG) {
  header("Content-type: image/png");
  $im = imagecreatefrompng("resource/graphimg.png");
}
else if (ImageTypes() & IMG_GIF) {
  header("Content-type: image/gif");
  $im = imagecreatefromgif("resource/graphimg.gif");
}
else {
  header("Content-type: image/jpeg");
  $im = imagecreatefromjpeg("resource/graphimg.jpg");
}
/*
if (ImageTypes() & IMG_PNG)
  header("Content-type: image/png");
else if (ImageTypes() & IMG_GIF)
  header("Content-type: image/gif");
else
  header("Content-type: image/jpeg");
$im = imagecreatefromgd2("resource/graphimg.gd2");
*/
// if (function_exists(imageantialias))
//  imageantialias($im, 1);

//=============================================================================
//========== Set Colors =======================================================
//=============================================================================

$white = imagecolorallocate($im, 255, 255, 255);  // #FFFFFF
$black = imagecolorallocate($im, 0, 0, 0);        // #000000
$back = imagecolorallocate($im, 181, 181, 181);   // #B7B7B7
$violet = imagecolorallocate($im, 128, 128, 192); // #8080C0
$red = imagecolorallocate($im, 255, 0, 0);        // #FF0000
$green = imagecolorallocate($im, 0, 220, 0);      // #00DC00
$blue = imagecolorallocate($im, 0, 0, 255);       // #0000FF
$yellow = imagecolorallocate($im, 245, 245, 0);   // #F5F500
$orange = imagecolorallocate($im, 236, 142, 9);   // #EC8E09
$cyan = imagecolorallocate($im, 46, 214, 193);    // #2ED6C1
$plum = imagecolorallocate($im, 187, 81, 92);     // #BB515C

$dashed = array($black, $black, $black, $black, $white, $white, $white, $white);
$dashedred = array($red, $red, $red, $red, $red, $red, $back, $back, $back, $back); // 3/2
$dashedyellow = array($yellow, $yellow, $yellow, $yellow, $yellow, $yellow, $back, $back, $back, $back); // 3/2
$dashedwhite = array($white, $white, $white, $white, $white, $white, $back, $back, $back, $back); // 3/2

$color[1] = $blue;
$color[2] = $red; // dashedred
$color[3] = $green;
$color[4] = $yellow; // dashedyellow
$color[5] = $orange;
$color[6] = $white; // dashedwhite
$color[7] = $cyan;
$color[8] = $plum;

//=============================================================================
//========== Draw Graph Area Border ===========================================
//=============================================================================

imageline($im, $minx, $miny, $maxx, $miny, $violet);
imageline($im, $maxx, $miny, $maxx, $maxy, $violet);
imageline($im, $maxx, $maxy, $minx, $maxy, $violet);
imageline($im, $minx, $maxy, $minx, $miny, $violet);

//=============================================================================
//========== Create y-Axis Grid & Labels ======================================
//=============================================================================

for ($i = 1; $i <= $gridy; $i++) {
  $vy = $miny - round(($yindex[$i] - $lowscore) * ($gha / $fsr));
  $strposx = ($minx - strlen($yindex[$i])) - (strlen($yindex[$i]) * 5) - 3;
  $strposy = $vy - ($font * 4);
  if ($vy != $miny)
    imageline($im, $minx, $vy, $maxx, $vy, $violet);
  imagefilledrectangle($im, $minx - 1, $vy - 1, $minx + 1, $vy + 1, $black);
  imagestring($im, $font, $strposx, $strposy, $yindex[$i], $black);
}

//=============================================================================
//========== Create x-Axis Grid & Labels ======================================
//=============================================================================

for ($i = 1; $i <= $gridx; $i++) {
  $vx = round(($xindex[$i] * 60) * ($gwa / $ftr)) + $minx;
  $strposx = $vx - strlen($xindex[$i]);
  if ($vx != $minx)
    imageline($im, $vx, $miny, $vx, $maxy, $violet);
  imagefilledrectangle($im, $vx - 1, $miny - 1, $vx + 1, $miny + 1, $black);
  imagestring($im, $font, $strposx, $miny + 5, $xindex[$i], $black);
}

//=============================================================================
//========== Legend ===========================================================
//=============================================================================

// y-Axis Legend
if ($type == 1)
  $ystring = "FRAGS";
else
  $ystring = "SCORE";
$strposx = 2;
$strposy = (int) (($miny - $maxy) / 2) + (strlen($ystring) * 5);
$offset = 5;
$offset2 = strlen($ystring) * 7 + 3;
imageline($im, $strposx + 6, $miny, $strposx + 6, $strposy + $offset, $color[6]);
imagestringup($im, $legendfont, $strposx, $strposy, $ystring, $black);
imageline($im, $strposx + 6, $strposy - $offset2, $strposx + 6, $maxy, $color[6]);

// x-Axis Legend
$xstring = "TIME (min)";
$strposx = (int) (($maxx - $minx) / 2) - strlen($xstring);
$strposy = $y - 16;
$offset = 5;
$offset2 = strlen($xstring) * 7 + 3;
imageline($im, $minx, $strposy + 3, $strposx - $offset, $strposy + 3, $color[6]);
imagestring($im, $legendfont, $strposx, $strposy - 3, $xstring, $black);
imageline($im, $strposx + $offset2, $strposy + 3, $maxx, $strposy + 3, $color[6]);

//=============================================================================
//========== Plot Lines =======================================================
//=============================================================================

// x point = (round) (time * (gwa / ftr))
// y point = (round) (score * (gha / fsr))
for ($r = 1; $r <= $lines; $r++) {
  $i = 0;
  $fromx = $minx;
  if ($type == 4)
    $fromy = $miny - round($highscore * ($gha / $fsr));
  else
    $fromy = $miny - round((0 - $lowscore) * ($gha / $fsr));
  $pointx = $pointy = 0;
  $num = $ranks[$r];
  while (isset($ptime[$num][$i])) {
    $pointx = round($ptime[$num][$i] * ($gwa / $ftr)) + $minx;
    $pointy = $miny - round(($pscore[$num][$i] - $lowscore) * ($gha / $fsr));
    if ($pointx > ($fromx + $minstep)) {
      // gdImageSetAntiAliased(im, blue);
      // gdImageLine(im, 0, 0, 99, 99, gdAntiAliased);
      imageline($im, $fromx, $fromy, $pointx - $minstep, $fromy, $color[$r]);
      imageline($im, $fromx, $fromy + 1, $pointx - $minstep, $fromy + 1, $color[$r]); // Thick line
      $fromx = $pointx - $minstep;
    }
    imageline($im, $fromx, $fromy, $pointx, $pointy, $color[$r]);
    imageline($im, $fromx, $fromy + 1, $pointx, $pointy + 1, $color[$r]); // Thick line
    $fromx = $pointx;
    $fromy = $pointy;
    $i++;
  }
  if ($pointx < $maxx) {
    imageline($im, $fromx, $fromy, $maxx, $fromy, $color[$r]);
    imageline($im, $fromx, $fromy + 1, $maxx, $fromy + 1, $color[$r]); // Thick line
  }
}

//=============================================================================
//========== Generate Image ===================================================
//=============================================================================

if (ImageTypes() & IMG_PNG)
  imagepng($im);
else if (ImageTypes() & IMG_GIF)
  imagegif($im);
else
  imagejpeg($im);
imagedestroy($im);

?>