<?php

  echo <<<EOF
<table cellpadding="0" cellspacing="0" border="0" width="600">
  <tr>
    <td width="250" align="center">
      <table cellpadding="1" cellspacing="2" border="0" width="150" class="box">
        <tr>
          <td class="heading" align="center" colspan="3">Match Totals</td>
        </tr>
        <tr>
          <td class="dark" width="70" align="center">Team</td>
          <td class="grey" width="35" align="center">$total_tscore</td>
        </tr>
        <tr>
          <td class="dark" align="center">Score</td>
          <td class="grey" align="center">$total_score</td>
        </tr>
        <tr>
          <td class="dark" align="center">Frags</td>
          <td class="grey" align="center">$total_frags</td>
        </tr>
        <tr>
          <td class="dark" align="center">Kills</td>
          <td class="grey" align="center">$gm_kills</td>
        </tr>
        <tr>
          <td class="dark" align="center">Deaths</td>
          <td class="grey" align="center">$gm_deaths</td>
        </tr>
        <tr>
          <td class="dark" align="center">Suicides</td>
          <td class="grey" align="center">$gm_suicides</td>
        </tr>
      </table>
    </td>
    <td width="350" align="center"><img src="mapimages/$mapimage" width="256" height="192" border="1" alt="Map Image" /></td>
  </tr>
</table>

EOF;

?>