<?php

  echo <<<EOF
<table cellpadding="1" cellspacing="2" border="0" width="235" class="box">
  <tr>
    <td class="heading" align="center" colspan="2">Totals for This Match</td>
  </tr>
  <tr>
    <td class="smheading" align="center">Team Score</td>
    <td class="smheading" align="center">Suicides</td>
  </tr>
  <tr>
    <td class="grey" align="center">$total_tscore</td>
    <td class="grey" align="center">$gm_suicides</td>
  </tr>
</table>

EOF;

?>