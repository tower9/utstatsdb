<?php

/*
    UTStatsDB
    Copyright (C) 2002-2007  Patrick Contreras / Paul Gallier

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

if (preg_match("/logranks.php/i", $_SERVER["PHP_SELF"])) {
  echo "Access denied.\n";
  die();
}

function rankcalc($rank1, $rank2, $score1, $score2)
{
  $mrank1 = $rank1;
  $mrank2 = $rank2;
  $mscore1 = $score1;
  $mscore2 = $score2;

  if ($mscore1 < 0) {
    $mscore1 -= $mscore1;
    $mscore2 -= $mscore1;
  }
  if ($mscore2 < 0) {
    $mscore2 -= $mscore2;
    $mscore1 -= $mscore2;
  }
  $calc = 1.0 + pow(10.0, (-($mrank1 - $mrank2) / 400.0));
  if ($calc == 0.0)
    $dif = 1.0;
  else
    $dif = 1.0 / $calc;

  if ((int) ($mscore1 + $mscore2) == 0)
    $base = 0.5;
  else
    $base = $mscore1 / ($mscore1 + $mscore2);

  $change = round(16.0 * ($base - $dif), 8);

  return $change;
}

function rankplayers()
{
  global $link, $dbpre, $config, $match, $player, $killmatch, $matchnum, $break;

  // Build player reference array
  $numgrid = 0;
  $pgrid = array();
  reset($player);
  $playerc = current($player);
  while ($playerc !== FALSE) {
    if (isset($playerc->name) && $playerc->name != "")
      $pgrid[$numgrid++] = $playerc->plr;
    $playerc = next($player);
  }

  // Calculate suicides based on number of kills and deaths (deathmatch, team deathmatch, non-team other)
  if ($match->teamgame || ($match->gametype == 8 || $match->gametype == 10))
    $rankbykills = 0;
  else
    $rankbykills = 1;

  if ($rankbykills) {
    for ($i = 0; $i < $numgrid; $i++) {
      $num1 = $pgrid[$i];
      $skirm = 0;
      for ($i2 = 0; $i2 < $numgrid; $i2++) {
        $num2 = $pgrid[$i2];
        if ($i != $i2) {
          if (isset($killmatch[$num1][$num2]))
            $skirm += $killmatch[$num1][$num2];
          if (isset($killmatch[$num2][$num1]))
            $skirm += $killmatch[$num2][$num1];
        }
      }
      if ($skirm) {
        if (isset($killmatch[$num1][$num1]))
          $suicr = floatval($killmatch[$num1][$num1]) / floatval($skirm);
        else
          $suicr = 0;
        $player[$num1]->suicr = $suicr;
      }
    }
  }

  $maxptime = 0;

  if (!$rankbykills) {
    $totcount = $totscore = $tottime = 0;
    $totrank = 0.0;

    for ($i = 0; $i < $numgrid; $i++) {
      $num1 = $pgrid[$i];

      $totscore += array_sum($player[$num1]->tscore);
      $totrank += $player[$num1]->ranks;
      $tottime += array_sum($player[$num1]->totaltime);
      $totcount++;
    }

    $avgscore = $totscore / $totcount;
    $avgrank = $totrank / $totcount;
    $avgtime = $tottime / $totcount;
  }

  // Calculate ranking for each player
  for ($i = 0; $i < $numgrid; $i++) {
    $num1 = $pgrid[$i];

    if ($config["rankbots"] || !$player[$num1]->is_bot()) { // Do not rank bots when disabled in config
      if ($maxptime < array_sum($player[$num1]->totaltime))
        $maxptime = array_sum($player[$num1]->totaltime);

      if ($rankbykills) {
        for ($i2 = $i + 1; $i2 < $numgrid; $i2++) {
          $num2 = $pgrid[$i2];

          
          if ($config["rankbots"] || !$player[$num2]->is_bot()) { // Do not rank bots when disabled in config
            // Check for minimum game time to rank
            $young = 0;
            if ($config["minranktime"] && ($player[$num1]->globaltime < $config["minranktime"] || $player[$num2]->globaltime < $config["minranktime"]))
              $young = 1;
            else if ($config["minrankmatches"] && ($player[$num1]->globalmatches < $config["minrankmatches"] || $player[$num2]->globalmatches < $config["minrankmatches"]))
              $young = 1;

            if (array_sum($player[$num1]->totaltime) > 10 && array_sum($player[$num2]->totaltime) > 10 && !$young) {
              $opposing = 0;

              // Get kill match-up or score
              if (isset($killmatch[$num1][$num2]))
                $score1 = $killmatch[$num1][$num2];
              else
                $score1 = 0;
              if (isset($killmatch[$num2][$num1]))
            $score2 = $killmatch[$num2][$num1];
              else
                $score2 = 0;

              // Check for opposing teams
              if ($score1 > 0 || $score2 > 0)
                $opposing = 1;

              if ($opposing) {
                for ($i3 = 1; $i3 <= 2; $i3++) {
                  if (${"score".$i3} > 0) {
                    // Add Killing Spree bonuses
                    ${"score".$i3} += $player[${"num".$i3}]->spree[0];
                    ${"score".$i3} += $player[${"num".$i3}]->spree[1] * 2;
                    ${"score".$i3} += $player[${"num".$i3}]->spree[2] * 3;
                    ${"score".$i3} += $player[${"num".$i3}]->spree[3] * 4;
                    ${"score".$i3} += $player[${"num".$i3}]->spree[4] * 5;
                    ${"score".$i3} += $player[${"num".$i3}]->spree[5] * 6;

                    // Add Multi Kill bonuses
                    ${"score".$i3} += $player[${"num".$i3}]->multi[0];
                    ${"score".$i3} += $player[${"num".$i3}]->multi[1] * 2;
                    ${"score".$i3} += $player[${"num".$i3}]->multi[2] * 3;
                    ${"score".$i3} += $player[${"num".$i3}]->multi[3] * 4;
                    ${"score".$i3} += $player[${"num".$i3}]->multi[4] * 5;
                    ${"score".$i3} += $player[${"num".$i3}]->multi[5] * 6;
                    ${"score".$i3} += $player[${"num".$i3}]->multi[6] * 7;

                    // Add LMS bonus
                    if ($match->gametype == 10) {
                      if ($player[${"num".$i3}]->rank == 1)
                        ${"score".$i3} += 5;
                    }
                  }
                }

                // Add in adjusted suicides
                if ($player[$num1]->suicr) {
                  $as1 = $player[$num1]->suicr * ($score1 + $score2);
                  if ($as1 < 1)
                    $as1 = 1;
                  $ak1 = $score1 - $as1;
                }
                else
                  $ak1 = $score1;

                if ($player[$num2]->suicr) {
                  $as2 = $player[$num2]->suicr * ($score1 + $score2);
                  if ($as2 < 1)
                    $as2 = 1;
                  $ak2 = $score2 - $as2;
                }
                else
                  $ak2 = $score2;

                // Do rank calculation
                $rc = rankcalc($player[$num1]->ranks, $player[$num2]->ranks, $score1, $score2);

/*
                // Do not adjust for time spent in-game
                if (array_sum($player[$num1]->totaltime) == 0.0 || array_sum($player[$num2]->totaltime) == 0.0)
                  $rc = 0.0;
                else {
                  if ($rc > 0)
                    $rc = $rc * ((array_sum($player[$num2]->totaltime) / $match->length) / (array_sum($player[$num1]->totaltime) / $match->length));
                  else if ($rc < 0)
                    $rc = $rc * ((array_sum($player[$num1]->totaltime) / $match->length) / (array_sum($player[$num2]->totaltime) / $match->length));
                }
*/

                // Subtract points only if deaths > 0
                // Check for unbalanced rank to gain points - must be either under 250 difference or 8x
                $pr1 = $player[$num1]->ranks;
                $pr2 = $player[$num2]->ranks;
                if ($rc > 0 || $score2 > 0) {
                  if ($rc < 0 || $pr1 < $pr2 + 250 || $pr1 < $pr2 * 8)
                    $player[$num1]->rankc += $rc;
                }
                if ($rc < 0 || $score1 > 0) {
                  if ($rc > 0 || $pr2 < $pr1 + 250 || $pr2 < $pr1 * 8)
                    $player[$num2]->rankc -= $rc;
                }
              }
            }
          }
        }
      }
      else {
        // Do rank calculation
        $rc = rankcalc($player[$num1]->ranks, $avgrank, array_sum($player[$num1]->tscore), $avgscore) * 8;

        // Adjust for time spent in-game
        if (array_sum($player[$num1]->totaltime) == 0.0)
          $rc = 0.0;
        else {
          if ($rc > 0)
            $rc = $rc * (($avgtime / $match->length) / (array_sum($player[$num1]->totaltime) / $match->length));
          else if ($rc < 0)
            $rc = $rc * ((array_sum($player[$num1]->totaltime) / $match->length) / ($avgtime / $match->length));
        }

        // Subtract points only if deaths > 0
        // Check for unbalanced rank to gain points - must be either under 250 difference or 8x
        $pr1 = $player[$num1]->ranks;
        if ($rc > 0 || $avgscore > 0) {
          if ($rc < 0 || $pr1 < $avgrank + 250 || $pr1 < $avgrank * 8)
            $player[$num1]->rankc += $rc;
        }
      }
    }
  }

  // Save ranks
  for ($i = 0; $i < $numgrid; $i++) {
    $plr = $player[$pgrid[$i]]->plr;
    $num = $player[$pgrid[$i]]->num;
    $rank = $player[$pgrid[$i]]->ranks;

    $change = $player[$pgrid[$i]]->rankc;

    // Negative ranks not allowed
    if ($change < 0 && $rank < abs($change))
      $change = -($rank);
    $nrank = $rank + $change;
    if ($nrank < 0)
      $nrank = 0;

    $result = sql_queryn($link, "UPDATE {$dbpre}playersgt SET gt_rank=$nrank WHERE gt_pnum=$num AND gt_tnum={$match->gametnum}");
    if (!$result) {
      echo "Error updating player gametype ranking.{$break}\n";
      sql_close($link);
      exit;
    }

    $result = sql_queryn($link, "UPDATE {$dbpre}gplayers SET gp_rstart=$rank,gp_rchange=$change WHERE gp_match=$matchnum AND gp_num=$plr");
    if (!$result) {
      echo "Error updating gplayer ranking data.{$break}\n";
      sql_close($link);
      exit;
    }
  }
}

?>