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

if (preg_match("/help.php/i", $_SERVER["PHP_SELF"])) {
  echo "Access denied.\n";
  die();
}

echo <<<EOF
<table cellpadding="1" cellspacing="2" border="0" width="300" class="box">
  <tr>
    <td class="heading" colspan="2" align="center">UT Stats Legend</td>
  </tr>
  <tr>
    <td class="medheading" align="center" width="75">Abbr.</td>
    <td class="medheading">Description</td>
  </tr>
  <tr>
    <td class="dark" align="center">K</td>
    <td class="grey">Kills</td>
  </tr>
  <tr>
    <td class="dark" align="center">S</td>
    <td class="grey">Suicides</td>
  </tr>
  <tr>
    <td class="dark" align="center">F</td>
    <td class="grey">Frags</td>
  </tr>
  <tr>
    <td class="dark" align="center">D</td>
    <td class="grey">Deaths</td>
  </tr>
  <tr>
    <td class="dark" align="center">TK</td>
    <td class="grey">Team Kills</td>
  </tr>
  <tr>
    <td class="dark" align="center">TD</td>
    <td class="grey">Team Deaths</td>
  </tr>
  <tr>
    <td class="dark" align="center">DM</td>
    <td class="grey">Deathmatch</td>
  </tr>
  <tr>
    <td class="dark" align="center">TDM</td>
    <td class="grey">Team Deathmatch</td>
  </tr>
  <tr>
    <td class="dark" align="center">CTF</td>
    <td class="grey">Capture the Flag</td>
  </tr>
  <tr>
    <td class="dark" align="center">DD</td>
    <td class="grey">Double Domination</td>
  </tr>
  <tr>
    <td class="dark" align="center">BR</td>
    <td class="grey">Bombing Run</td>
  </tr>
  <tr>
    <td class="dark" align="center">AS</td>
    <td class="grey">Assault</td>
  </tr>
  <tr>
    <td class="dark" align="center">ONS</td>
    <td class="grey">Onslaught</td>
  </tr>
  <tr>
    <td class="dark" align="center">LMS</td>
    <td class="grey">Last Man Standing</td>
  </tr>
  <tr>
    <td class="dark" align="center">IN</td>
    <td class="grey">Invasion</td>
  </tr>
  <tr>
    <td class="dark" align="center">MU</td>
    <td class="grey">Mutant</td>
  </tr>
  <tr>
    <td class="dark" align="center">VCTF</td>
    <td class="grey">Vehicle CTF</td>
  </tr>
  <tr>
    <td class="dark" align="center">FPH</td>
    <td class="grey">Frags per Hour</td>
  </tr>
  <tr>
    <td class="dark" align="center">SPH</td>
    <td class="grey">Score per Hour</td>
  </tr>
  <tr>
    <td class="dark" align="center">TTL</td>
    <td class="grey">Time to Live</td>
  </tr>
  <tr>
    <td class="dark" align="center">[d]</td>
    <td class="grey">Time in days</td>
  </tr>
  <tr>
    <td class="dark" align="center">[h]</td>
    <td class="grey">Time in hours</td>
  </tr>
  <tr>
    <td class="dark" align="center">[m],[min]</td>
    <td class="grey">Time in minutes</td>
  </tr>
  <tr>
    <td class="dark" align="center">[s],[sec]</td>
    <td class="grey">Time in seconds</td>
  </tr>
</table>

EOF;

?>