<?php

  echo <<<EOF
<table cellpadding="1" cellspacing="2" border="0" class="box">
  <tr>
    <td class="heading" align="center" colspan="5">Totals for This Match</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="40">Goals</td>
    <td class="smheading" align="center" width="50">Passes</td>
    <td class="smheading" align="center" width="45">Saves</td>
    <td class="smheading" align="center" width="55">Tackles</td>
    <td class="smheading" align="center" width="70">Intercepts</td>
  </tr>
  <tr>
    <td class="grey" align="center">$total_score</td>
    <td class="grey" align="center">$gm_kills</td>
    <td class="grey" align="center">$gm_headshots</td>
    <td class="grey" align="center">$gm_deaths</td>
    <td class="grey" align="center">$gm_suicides</td>
  </tr>
</table>

EOF;

?>