<?php

  echo <<<EOF
<table cellpadding="1" cellspacing="2" border="0" width="235" class="box">
  <tr>
    <td class="heading" align="center" colspan="5">Totals for This Match</td>
  </tr>
  <tr>
    <td class="smheading" align="center">Score</td>
    <td class="smheading" align="center">Frags</td>
    <td class="smheading" align="center">Kills</td>
    <td class="smheading" align="center">Deaths</td>
    <td class="smheading" align="center">Suicides</td>
  </tr>
  <tr>
    <td class="grey" align="center">$total_score</td>
    <td class="grey" align="center">$total_frags</td>
    <td class="grey" align="center">$gm_kills</td>
    <td class="grey" align="center">$gm_deaths</td>
    <td class="grey" align="center">$gm_suicides</td>
  </tr>
</table>

EOF;

?>