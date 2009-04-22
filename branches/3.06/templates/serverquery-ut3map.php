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
                <td class="mapimage" align="right" valign="middle" width="260" rowspan="14">
                  <img src="mapimages/$mapimage" width="256" height="192" border="1" alt="Map Image" title="{$sq_server['mapname']}" />
                </td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Map:</td>
                <td class="statusnbw" align="left">{$sq_server['mapname']}</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Game Type:</td>
                <td class="statusnbw" align="left">{$sq_server['gametype']}</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Standard Game:</td>
                <td class="statusnbw" align="left">{$sq_server['standard']}</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Password:</td>
                <td class="statusnbw" align="left">$password</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Join in Progress:</td>
                <td class="statusnbw" align="left">{$sq_server['joininprogress']}</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Game Stats:</td>
                <td class="statusnbw" align="left">{$sq_server['gamestats']}</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right" width="110">Current Players:</td>
                <td class="statusnbw" align="left" width="60">{$sq_server["numplayers"]}</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Max Players:</td>
                <td class="statusnbw" align="left">{$sq_server['maxplayers']}</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Score Limit:</td>
                <td class="statusnbw" align="left">{$sq_server['goalscore']}</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Time Limit:</td>
                <td class="statusnbw" align="left">{$sq_server['timelimit']}</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Bots:</td>
                <td class="statusnbw" align="left">{$sq_server['numbots']} ({$sq_server['botskill']})</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Vs. Bots:</td>
                <td class="statusnbw" align="left">{$sq_server['vsbots']}</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Forced Respawn:</td>
                <td class="statusnb" align="left">{$sq_server['forcedrespawn']}</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Mutators:</td>
                <td class="statusnb" colspan="2" align="left">{$sq_server['mutator']}</td>
              </tr>
            </table>
          </td>
        </tr>
      </table>

EOF;

?>