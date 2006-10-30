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
          <td class="statusnbw" align="left">{$sq_server['mapname']}</td>
          <td>&nbsp;</td>
          <td class="statusnbw" align="right">Current Players:</td>
          <td class="statusnbw" align="left">&nbsp;{$sq_server['numplayers']}</td>
        </tr>
        <tr>
          <td class="statusnbw" align="right">Game Type:</td>
          <td class="statusnbw" align="left">{$sq_server['gametype']}</td>
          <td>&nbsp;</td>
          <td class="statusnbw" align="right">Min Players:</td>
          <td class="statusnbw" align="left">&nbsp;{$sq_server['minplayers']}</td>
        </tr>
        <tr>
          <td class="statusnbw" align="right">Password:</td>
          <td class="statusnbw" align="left">$password</td>
          <td>&nbsp;</td>
          <td class="statusnbw" align="right">Max Players:</td>
          <td class="statusnbw" align="left">&nbsp;{$sq_server['maxplayers']}</td>
        </tr>
        <tr>
          <td class="statusnbw" align="right">Game Stats:</td>
          <td class="statusnbw" align="left">&nbsp;{$sq_server['gamestats']}</td>
          <td>&nbsp;</td>
          <td class="statusnbw" align="right">Score Limit:</td>
          <td class="statusnbw" align="left">&nbsp;{$sq_server['goalscore']}</td>
        </tr>

EOF;

    if (isset($sq_server["translocator"]) || isset($sq_server["timelimit"]))
      echo <<<EOF
        <tr>
          <td class="statusnbw" align="right">Translocator:</td>
          <td class="statusnbw" align="left">&nbsp;$translocator</td>
          <td>&nbsp;</td>
          <td class="statusnbw" align="right">Time Limit:</td>
          <td class="statusnbw" align="left">&nbsp;$timelimit</td>
        </tr>

EOF;

    if (isset($sq_server["elapsedtime"]) || isset($sq_server["overtime"]))
      echo <<<EOF
        <tr>
          <td class="statusnbw" align="right">Time In Game:</td>
          <td class="statusnbw" align="left">&nbsp;$elapsedtime minutes</td>
          <td>&nbsp;</td>
          <td class="statusnbw" align="right">Overtime:</td>
          <td class="statusnbw" align="left">&nbsp;$overtime</td>
        </tr>

EOF;

    if (isset($sq_server["mapvoting"])) {
      echo <<<EOF
        <tr>
          <td class="statusnbw" align="right">Map Voting:</td>
          <td class="statusnbw" align="left">&nbsp;{$sq_server['mapvoting']}</td>
          <td>&nbsp;</td>
          <td class="statusnbw" align="right">Kick Voting:</td>
          <td class="statusnbw" align="left">&nbsp;{$sq_server['kickvoting']}</td>
        </tr>

EOF;
    }

    if (isset($sq_server["friendlyfire"]) || isset($sq_server["balanceteams"])) {
      echo <<<EOF
        <tr>
          <td class="statusnbw" align="right">Friendly Fire:</td>
          <td class="statusnbw" align="left">&nbsp;$friendlyfire</td>
          <td>&nbsp;</td>
          <td class="statusnbw" align="right">Balance Teams:</td>
          <td class="statusnbw" align="left">&nbsp;$balanceteams</td>
        </tr>

EOF;
    }

    if (isset($sq_server["nextmap"])) {
      echo <<<EOF
        <tr>
          <td class="statusw" align="right">Next Map:</td>
          <td align="left" colspan="4">&nbsp;$nextmap</td>
        </tr>

EOF;
    }

      echo <<<EOF
        <tr>
          <td class="statusw" align="right">Mutators:</td>
          <td align="left" colspan="4">&nbsp;{$sq_server['mutator']}</td>
        </tr>
      </table>

EOF;

?>