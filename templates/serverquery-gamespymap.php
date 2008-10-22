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
                <td class="statusnbw" align="left" width="225"><a href="$query_link" class="status">$displaylink</a></td>
                <td class="mapimage" align="right" valign="middle" width="260" rowspan="5">
                  <img src="mapimages/$mapimage" width="256" height="192" border="1" alt="Map Image" title="{$sq_server['mapname']}" />
                </td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Game Type:</td>
                <td class="statusnbw" align="left">{$sq_server['gametype']}</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Current Players:</td>
                <td class="statusnbw" align="left">{$sq_server["numplayers"]}</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Max Players:</td>
                <td class="statusnbw" align="left">{$sq_server['maxplayers']}</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Map:</td>
                <td class="statusnb" align="left">{$sq_server['mapname']}</td>
              </tr>
            </table>
          </td>
        </tr>
      </table>

EOF;

?>