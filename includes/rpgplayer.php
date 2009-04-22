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

function rpg_header($line) {
  $loc = strpos($line, " ");
  if ($loc === FALSE || $loc == 0)
    return FALSE;
  $user = substr($line, 1, $loc - 1);
  $info = substr($line, $loc + 1, 19);
  if (!strcasecmp($info, "RPGPlayerDataObject"))
    return $user;
  else
    return FALSE;
}

function rpg_line($line, &$tag, &$val) {
  $loc = strpos($line, "=");
  if ($loc === FALSE || $loc < 5)
    return FALSE;
  $tag = substr($line, 0, $loc);
  $val = substr($line, $loc + 1);
  return TRUE;
}

function rpg_display($user)
{
  global $rpguser, $rpgabils;

  $username = addslashes($user);
  $level = intval($rpguser["Level"]);
  $healthbonus = intval($rpguser["HealthBonus"]);
  if ($healthbonus > 0)
    $healthbonus = "+".$healthbonus;
  $weaponspeed = intval($rpguser["WeaponSpeed"]);
  if ($weaponspeed > 0)
    $weaponspeed = "+".$weaponspeed;
  $attack = intval($rpguser["Attack"]) / 2;
  if ($attack > 0)
    $attack = "+".$attack;
  $defense = intval($rpguser["Defense"]) / 2;
  $ammomax = intval($rpguser["AmmoMax"]);
  if ($ammomax > 0)
    $ammomax = "+".$ammomax;
  $adrenalinemax = intval($rpguser["AdrenalineMax"]);
  $pointsavail = intval($rpguser["PointsAvailable"]);
  $experience = intval($rpguser["Experience"]);
  $expneeded = intval($rpguser["NeededExp"]);

  echo <<<EOF
<br>
<center>
<table cellpadding="1" cellspacing="2" border="0" width="215" class="box">
  <tr>
    <td class="heading" colspan="2" align="center">RPG Stats</td>
  </tr>
  <tr>
    <td class="dark" align="right">Level:</th>
    <td class="grey">$level</td>
  </tr>
  <tr>
    <td class="dark" align="right">Weapon Speed Bonus:</th>
    <td class="grey">$weaponspeed%</td>
  </tr>
  <tr>
    <td class="dark" align="right">Health Bonus:</th>
    <td class="grey">$healthbonus</td>
  </tr>
  <tr>
    <td class="dark" align="right">Max Adrenaline:</th>
    <td class="grey">$adrenalinemax</td>
  </tr>
  <tr>
    <td class="dark" align="right">Damage Bonus:</th>
    <td class="grey">$attack%</td>
  </tr>
  <tr>
    <td class="dark" align="right">Damage Reduction:</th>
    <td class="grey">$defense%</td>
  </tr>
  <tr>
    <td class="dark" align="right">Max Ammo Bonus:</th>
    <td class="grey">$ammomax%</td>
  </tr>
  <tr>
    <td class="dark" align="right">Stat Points Available:</th>
    <td class="grey">$pointsavail</td>
  </tr>
  <tr>
    <td class="dark" align="right">Experience:</th>
    <td class="grey">$experience pts.</td>
  </tr>
  <tr>
    <td class="dark" align="right">Next Level:</th>
    <td class="grey">$expneeded pts.</td>
  </tr>
</table>

EOF;

  if (isset($rpgabils)) {
    echo <<<EOF
<br>
<table cellpadding="1" cellspacing="2" border="0" width="180" class="box">
  <tr>
    <td class="medheading" align="center">Ability</th>
    <td class="medheading" align="center" width="40">Rank</td>
  </tr>

EOF;
    while (list($abilname,$abilrank) = each($rpgabils)) {
      if ($loc = strpos($abilname, ".Ability"))
        $abilname = substr($abilname, $loc + 8);
      if (substr($abilname, -1) == "'")
        $abilname = substr($abilname, 0, -1);
      $desc = $fulldesc = $idesc = "";
      switch ($abilname) {
        case "Regen":
          $abilname = "Regeneration";
          $desc = "1 health per second per level (max: 5)";
          $idesc = "Regenerates $abilrank health per second.";
          $fulldesc = "Heals 1 health per second per level. Does not heal past starting health amount.<br>
You must have a Health Bonus stat equal to 30 times the ability level you wish<br>
to have before you can purchase it.<br>
Max Level: 5";
          break;
        case "AdrenalineRegen":
          $abilname = "Adrenal Drip";
          $desc = "1 adrenaline per 5 seconds per level (max: 3)";
          $idesc = "Gains $abilrank adrenaline per 5 seconds.";
          $fulldesc = "Gives 1 adrenaline per 5 seconds per level. Does not give adrenaline while<br>
performing a combo. You must have spent 25 points in your Adrenaline Max stat<br>
for each level of this ability you want to purchase.<br>
Max Level: 3";
          break;
        case "AmmoRegen":
          $abilname = "Resupply";
          $desc = "1 ammo per level every 3 seconds (max: 4)";
          $idesc = "Gains $abilrank ammo every 3 seconds.";
          $fulldesc = "Adds 1 ammo per level to each ammo type you own every 3 seconds. Does not give<br>
ammo to superweapons or the translocator. You must have a Max Ammo stat of at<br>
least 50 to purchase this ability.<br>
Max Level: 4";
          break;
        case "CounterShove":
          $abilname = "CounterShove";
          $desc = "25% momentum per level to attacker (max: 5)";
          $idesc = "Attackers suffer ".($abilrank * 25)."% momentum back.";
          $fulldesc = "Whenever you are damaged by another player, 25% of the momentum per level (or<br>
150% at level 5) is also done to the player who hurt you. Will not CounterShove<br>
a CounterShove. You must have a Damage Reduction of at least 50 to purchase this<br>
ability.<br>
Max Level: 5";
          break;
        case "JumpZ":
          $abilname = "Power Jump";
          $desc = "Increase jump by 10% per level (max: 3)";
          $idesc = "Jumps ".($abilrank * 10)."% higher.";
          $fulldesc = "Increases your jumping height by 10% per level. The Speed adrenaline combo will<br>
stack with this effect. You must be a Level equal to ten times the ability level<br>
you wish to have before you can purchase it.<br>
Max Level: 3";
          break;
        case "ReduceFallDamage":
          $abilname = "Iron Legs";
          $desc = "+25% fall distance per level (max: 4)";
          $idesc = "Safely falls ".($abilrank * 25)."% further.";
          $fulldesc = "Increases the distance you can safely fall by 25% per level and reduces fall<br>
damage for distances still beyond your capacity to handle. Your Health Bonus<br>
stat must be at least 50 to purchase this ability.<br>
Max Level: 4";
          break;
        case "Retaliate":
          $abilname = "Retaliation";
          $desc = "5% damage per level to attacker (max: 10)";
          $idesc = "Attackers suffer ".($abilrank * 5)."% damage back.";
          $fulldesc = "Whenever you are damaged by another player, 5% of the damage per level is also<br>
done to the player that hurt you. Your Damage Bonus stat and your oponent's<br>
Damage Reduction stat are applied to this extra damage. You can't retaliate to<br>
retaliation damage. You must have a Damage Reduction of at least 50 to purchase<br>
this ability.<br>
Max Level: 10";
          break;
        case "Speed":
          $abilname = "Quickfoot";
          $desc = "+5% speed per level (max: 4)";
          $idesc = "Speed increased ".($abilrank * 5)."%.";
          $fulldesc = "Increases your speed in all environments by 5% per level. The Speed adrenaline<br>
combo will stack with this effect. You must be a Level equal to ten times the<br>
ability level you wish to have before you can purchase it.<br>
Max Level: 4";
          break;
        case "ShieldStrength":
          $abilname = "Shields Up!";
          $desc = "+25 max shield per level (max: 4)";
          $idesc = "Has maximum of ".(150 + ($abilrank * 25))." shield.";
          $fulldesc = "Increases your maximum shield by 25 per level. You must have a Health Bonus stat<br>
of 100 before you can purchase this ability.<br>
Max Level: 4";
          break;
        case "NoWeaponDrop":
          $abilname = "Denial";
          $desc = "Weapons not dropped upon death (max: 2)";
          if ($abilrank == 1)
            $idesc = "Doesn't drop weapons upon death.";
          else
            $idesc = "Retains weapon held upon respawning after death.";
          $fulldesc = "The first level of this ability simply prevents you from dropping a weapon when<br>
you die (but you don't get it either). The second level allows you to respawn<br>
with the weapon and ammo you were using when you died. You need to be at least<br>
Level 25 to purchase this ability.<br>
Max Level: 2";
          break;
        case "Vampire":
          $abilname = "Vampirism";
          $desc = "Healed 5% per level of damage inflicted (max: 10)";
          $idesc = "Heals ".($abilrank * 5)."% of damage inflicted.";
          $fulldesc = "Whenever you damage another player, you are healed for 5% of the damage per<br>
level (up to your starting health amount + 50). You can't gain health from self-<br>
damage and you can't gain health from damage caused by the Retaliation ability.<br>
You must have a Damage Bonus of at least 50 to purchase this ability.<br>
Max Level: 10";
          break;
        case "Hoarding":
          $abilname = "Hoarding";
          $desc = "Pick up items not needed (max: 1)";
          $idesc = "Picks up items even if not needed.";
          $fulldesc = "Allows you to pick up items even if you don't need them. You need to have at<br>
least 5 points in every stat to purchase this ability.<br>
Max Level: 1";
          break;
        case "ReduceSelfDamage":
          $abilname = "Cautiousness";
          $desc = "Reduce self-damage 15% per level (max: 5)";
          $idesc = "Self-inflicted damage reduced by ".($abilrank * 15)."%.";
          $fulldesc = "Reduces self damage by 15% per level. Your Health Bonus stat must be at least 50<br>
and your Damage Reduction stat at least 25 to purchase this ability.<br>
Max Level: 5";
          break;
        case "SmartHealing":
          $abilname = "Smart Healing";
          $desc = "Healing items heal +25% per level (max: 4)";
          $idesc = "Healing items are ".($abilrank * 25)."% more effective.";
          $fulldesc = "Causes healing items to heal you an addition 25% per level. You need to have a<br>
Health Bonus stat of at least 50 and a Max Ammo stat of at least 25 to purchase<br>
this ability.<br>
Max Level: 4";
          break;
        case "AirControl":
          $abilname = "Airmaster";
          $desc = "+50% air control per level (max: 4)";
          $idesc = "Air control increased by ".($abilrank * 50)."%.";
          $fulldesc = "Increases your air control by 50% per level. You must be a Level equal to ten<br>
times the ability level you wish to have before you can purchase it.<br>
Max Level: 4";
          break;
        case "Ghost":
          $abilname = "Ghost";
          $desc = "Become ghost instead of dying (max: 3)";
          if ($abilrank == 1)
            $idesc = "Becomes non-corporeal upon first death after spawns - recovers with 1 health.";
          else if ($abilrank == 2)
            $idesc = "Becomes non-corporeal upon first death after spawns - recovers with 100 health.";
          else
            $idesc = "Becomes non-corporeal upon first death after spawns - recovers at full health.";
          $fulldesc = "The first time each spawn that you take damage that would kill you, instead of<br>
dying you will become non-corporeal and move to a new location, where you will<br>
continue your life. At level 1 you will move slowly as a ghost and return with a<br>
health of 1. At level 2 you will move somewhat more quickly and will return with<br>
100 health. At level 3 you will move fastest and will return with your normal<br>
starting health. You need to have at least 200 Health Bonus and 100 Damage<br>
Reduction to purchase this ability. You can't have both Ghost and Ultima at the<br>
same time.<br>
Max Level: 3";
          break;
        case "Ultima":
          $abilname = "Ultima";
          $desc = "Body explodes after death (max: 2)";
          $idesc = "Upon death body detonates with a redeemer-like explosion.";
          $fulldesc = "This ability causes your body to release energy when you die. The energy will<br>
collect at a single point which will then cause a Redeemer-like nuclear<br>
explosion. Level 2 of this ability causes the energy to collect for the<br>
explosion in half the time. The ability will only trigger if you have killed at<br>
least one enemy during your life. You need to have a Damage Bonus stat of at<br>
least 250 to purchase this ability. You can't have both Ultima and Ghost at the<br>
same time.<br>
Max Level: 2";
          break;
        case "AdrenalineSurge":
          $abilname = "Adrenal Surge";
          $desc = "+50% adrenaline from kills per level (max: 2)";
          $idesc = "Gains ".($abilrank * 50)."% additional adrenaline from kills.";
          $fulldesc = "For each level of this ability, you gain 50% more adrenaline from all kill<br>
related adrenaline bonuses. You must have a Damage Bonus of at least 50 and an<br>
Adrenaline Max stat at least 150 to purchase this ability.<br>
Max Level: 2";
          break;
        case "FastWeaponSwitch":
          $abilname = "Speed Switcher";
          $desc = "50% faster weapon switch per level (max: 2)";
          $idesc = "Switches weapons ".($abilrank * 50)."% faster.";
          $fulldesc = "For each level of this ability, you switch weapons 50% faster. You need to have<br>
at least 50 Weapon Speed before you can purchase this ability.<br>
Max Level: 2";
          break;
        case "Awareness":
          $abilname = "Awareness";
          $desc = "Adds health bar to enemies (max: 2)";
          if ($abilrank == 1)
            $idesc = "Sees colored health bar for enemies.";
          else
            $idesc = "Sees colored health and shield bars for enemies.";
          $fulldesc = "Informs you of your enemies' health with a display over their heads. At level 1<br>
you get a colored indicator (green, yellow, or red). At level 2 you get a<br>
colored health bar and a shield bar. You need to have at least 5 points in every<br>
stat to purchase this ability.<br>
Max Level: 2";
          break;
        case "MonsterSummon":
          $abilname = "Monster Tongue";
          $desc = "Summons monsters (max: 8)";
          $idesc = "Summons level ".$abilrank." monster.";
          $fulldesc = "With this ability, you can convince monsters to come to your aid. A monster will<br>
appear and follow you around, attacking any enemies it sees, and if it dies,<br>
another will eventually come to take its place. You will get the score and EXP<br>
for any of its kills. The level of the ability determines the type of monster<br>
that will assist you. Additionally, the monster will have the benefits of all of<br>
your stats and abilities except those which act on your death. You must have at<br>
least 75 Damage Bonus and 75 Damage Reduction to purchase this ability.<br>
Max Level: 8";
          break;
      }
      echo <<<EOF
  <tr>
    <td class="dark" align="center" title="$desc">$abilname</td>
    <td class="grey" align="center" title="$idesc">$abilrank</td>
  </tr>

EOF;
    }
    echo <<<EOF
  <tr>
  </tr>
</table>
<span class="tinytext">Mouse over ability name for general description or rank for specific description.</span>
<font size="1"><br /></font>

EOF;
  }
}

function rpg_stats($key, $name)
{
  global $rpgini, $rpguser, $rpgabils;

  $abils = array();
  $rpgabils = array();
  $numabil = $numlev = 0;
  $cuser = $user = "";
  $key = strtoupper($key);

  if (file_exists($rpgini) && ($fp = fopen($rpgini, "r"))) {
    while (!feof($fp)) {
      $line = trim(fgets($fp, 128));
      if ($line[0] == '[') {
        if ($cuser = rpg_header($line)) {
          if (isset($rpguser["OwnerID"]) && strtoupper($rpguser["OwnerID"]) == $key && !strcasecmp($user, $name)) {
            rpg_display($user);
            return;
          }
          else {
            $user = $cuser;
            $rpguser["OwnerID"] = "";
            $rpguser["Experience"] = 0;
            $rpguser["WeaponSpeed"] = 0;
            $rpguser["HealthBonus"] = 0;
            $rpguser["AdrenalineMax"] = 0;
            $rpguser["Attack"] = 0;
            $rpguser["Defense"] = 0;
            $rpguser["AmmoMax"] = 0;
            $rpguser["PointsAvailable"] = 0;
            $rpguser["NeededExp"] = 0;
            $rpguser["BotAbilityGoal"] = "";
            $rpguser["BotGoalAbilityCurrentLevel"] = 0;
            $abils = array();
            $rpgabils = array();
            $numabil = $numlev = 0;
          }
        }
      }
      if (rpg_line($line, $tag, $val)) {
        switch ($tag) {
          case "OwnerID":
          case "Level":
          case "Experience":
          case "WeaponSpeed":
          case "HealthBonus":
          case "AdrenalineMax":
          case "Attack":
          case "Defense":
          case "AmmoMax":
          case "PointsAvailable":
          case "NeededExp":
          case "BotAbilityGoal":
          case "BotGoalAbilityCurrentLevel":
            $rpguser[$tag] = $val;
            break;
          case "Abilities":
            $abils[$numabil++] = $val;
            break;
          case "AbilityLevels":
            $rpgabils[$abils[$numlev++]] = $val;
            break;
        }
      }
    }
  }
  if (isset($rpguser["OwnerID"]) && strtoupper($rpguser["OwnerID"]) == $key && !strcasecmp($user, $name)) {
    rpg_display($user);
    return;
  }
}

?>