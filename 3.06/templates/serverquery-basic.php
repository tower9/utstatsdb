<?php

echo <<<EOF
      <table class="status" cellspacing="0" cellpadding="1" width="500">
        <tr>
          <td class="statustitle" align="center" colspan="5">
            <b>Current Status for {$sq_server['hostname']}</b>
          </td>
        </tr>
        <tr>
          <td class="statusnbw" align="right" width="95">Address:</td>
          <td class="statusnbw" align="left" width="225">&nbsp;<a href="$query_link" class="status">$displaylink</a></td>
          <td width="10">&nbsp;</td>
          <td class="statusnbw" align="right" width="110">Version:</td>
          <td class="statusnbw" align="left" width="60">&nbsp;$version</td>
        </tr>
        <tr>
          <td class="statusnbw" align="right">Map:</td>
          <td class="statusnbw" align="left">&nbsp;{$sq_server['mapname']}</td>
          <td>&nbsp;</td>
          <td class="statusnbw" align="right">Current Players:</td>
          <td class="statusnbw" align="left">&nbsp;{$sq_server["numplayers"]}</td>
        </tr>
        <tr>
          <td class="statusnbw" align="right">Game Type:</td>
          <td class="statusnbw" align="left">&nbsp;{$sq_server['gametype']}</td>
          <td>&nbsp;</td>
          <td class="statusnbw" align="right">Max Players:</td>
          <td class="statusnbw" align="left">&nbsp;{$sq_server['maxplayers']}</td>
        </tr>
        <tr>
          <td class="statusnbw" align="right">Password:</td>
          <td class="statusnbw" align="left">&nbsp;$password</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      </table>

EOF;

?>