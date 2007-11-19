<?php

echo <<<EOF
      <table class="status" cellspacing="0" cellpadding="1">
        <tr>
          <td class="statustitle" align="center" colspan="2">
            <b>Current Status for {$sq_server['hostname']}</b>
          </td>
        </tr>
        <tr>
          <td width="320">
            <table class="statusnb" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td class="statusnbw" align="right" width="95">Address:</td>
                <td class="statusnbw" align="left" width="225">&nbsp;<a href="$query_link" class="status">$displaylink</a></td>
              </tr>
              <tr>
                <td colspan="2" style="height:5"></td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Game Type:</td>
                <td class="statusnbw" align="left">&nbsp;{$sq_server['gametype']}</td>
              </tr>
              <tr>
                <td colspan="2" style="height:5"></td>
              </tr>
              <tr>
                <td class="statusnbw" align="right" width="110">Current Players:</td>
                <td class="statusnbw" align="left" width="60">&nbsp;{$sq_server["numplayers"]}</td>
              </tr>
              <tr>
                <td colspan="2" style="height:5"></td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Max Players:</td>
                <td class="statusnbw" align="left">&nbsp;{$sq_server['maxplayers']}</td>
              </tr>
              <tr>
                <td colspan="2" style="height:5"></td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Map:</td>
                <td class="statusnbw" align="left">&nbsp;{$sq_server['mapname']}</td>
              </tr>
            </table>
          </td>
          <td align="right" valign="middle" width="260">
            <img src="mapimages/$mapimage" width="256" height="192" border="1" alt="Map Image" />
          </td>
        </tr>
      </table>

EOF;

?>