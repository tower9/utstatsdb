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
                <td class="statusnbw" align="right">Map:</td>
                <td class="statusnbw" align="left">&nbsp;{$sq_server['mapname']}</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Game Type:</td>
                <td class="statusnbw" align="left">&nbsp;{$sq_server['gametype']}</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Standard Game:</td>
                <td class="statusnbw" align="left">&nbsp;{$sq_server['standard']}</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Password:</td>
                <td class="statusnbw" align="left">&nbsp;$password</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Join in Progress:</td>
                <td class="statusnbw" align="left">&nbsp;{$sq_server['joininprogress']}</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Game Stats:</td>
                <td class="statusnbw" align="left">&nbsp;{$sq_server['gamestats']}</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right" width="110">Current Players:</td>
                <td class="statusnbw" align="left" width="60">&nbsp;{$sq_server["numplayers"]}</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Max Players:</td>
                <td class="statusnbw" align="left">&nbsp;{$sq_server['maxplayers']}</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Score Limit:</td>
                <td class="statusnbw" align="left">&nbsp;{$sq_server['goalscore']}</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Time Limit:</td>
                <td class="statusnbw" align="left">&nbsp;{$sq_server['timelimit']}</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Bots:</td>
                <td class="statusnbw" align="left">&nbsp;{$sq_server['numbots']} ({$sq_server['botskill']})</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Vs. Bots:</td>
                <td class="statusnbw" align="left">&nbsp;{$sq_server['vsbots']}</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Forced Respawn:</td>
                <td class="statusnbw" align="left">&nbsp;{$sq_server['forcedrespawn']}</td>
              </tr>
            </table>
          </td>
          <td align="right" valign="middle" width="260">
            <img src="mapimages/$mapimage" width="256" height="192" border="1" alt="Map Image" title="This is a test\nof the tooltips.\nEnd of test!" />
          </td>
        </tr>
      </table>

EOF;

?>