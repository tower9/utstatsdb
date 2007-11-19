<?php

$rows = 8;
if (isset($sq_server["translocator"]))
  $rows++;
if (isset($sq_server["elapsedtime"]))
  $rows++;
if (isset($sq_server["mapvoting"]))
  $rows++;
if (isset($sq_server["kickvoting"]))
  $rows++;
if (isset($sq_server["friendlyfire"]))
  $rows++;
if (isset($sq_server["version"]))
  $rows++;
if (isset($sq_server["timelimit"]))
  $rows++;
if (isset($sq_server["overtime"]))
  $rows++;
if (isset($sq_server["balanceteams"]))
  $rows++;
if (isset($sq_server["nextmap"]))
  $rows++;

echo <<<EOF
      <table class="status" cellspacing="0" cellpadding="1">
        <tr>
          <td class="statustitle" align="center" colspan="2">
            <b>Current Status for {$sq_server['hostname']}</b>
          </td>
        </tr>
        <tr>
          <td>
            <table class="statusnb" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td class="statusnbw" align="right" width="95">Address:</td>
                <td class="statusnbw" align="left" width="120">&nbsp;<a href="$query_link" class="status">$displaylink</a></td>
                <td rowspan="$rows" align="center" valign="middle" width="268">
                  <img src="mapimages/$mapimage" width="256" height="192" border="1" alt="Map Image" />
                </td>
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
                <td class="statusnbw" align="right">Password:</td>
                <td class="statusnbw" align="left">&nbsp;$password</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Game Stats:</td>
                <td class="statusnbw" align="left">&nbsp;{$sq_server['gamestats']}</td>
              </tr>

EOF;

    if (isset($sq_server["translocator"]))
      echo "              <tr><td class=\"statusnbw\" align=\"right\">Translocator:</td><td class=\"statusnbw\" align=\"left\">&nbsp;$translocator</td></tr>\n";

    if (isset($sq_server["elapsedtime"]))
      echo "              <tr><td class=\"statusnbw\" align=\"right\">Time In Game:</td><td class=\"statusnbw\" align=\"left\">&nbsp;$elapsedtime minutes</td></tr>\n";

    if (isset($sq_server["mapvoting"]))
      echo "              <tr><td class=\"statusnbw\" align=\"right\">Map Voting:</td><td class=\"statusnbw\" align=\"left\">&nbsp;{$sq_server['mapvoting']}</td></tr>\n";

    if (isset($sq_server["kickvoting"]))
      echo "              <tr><td class=\"statusnbw\" align=\"right\">Kick Voting:</td><td class=\"statusnbw\" align=\"left\">&nbsp;{$sq_server['kickvoting']}</td></tr>\n";

    if (isset($sq_server["friendlyfire"]))
      echo "              <tr><td class=\"statusnbw\" align=\"right\">Friendly Fire:</td><td class=\"statusnbw\" align=\"left\">&nbsp;$friendlyfire</td></tr>\n";

    if (isset($sq_server["version"]))
      echo "              <tr><td class=\"statusnbw\" align=\"right\">Version:</td><td class=\"statusnbw\" align=\"left\" width=\"60\">&nbsp;$version</td></tr>\n";

    echo <<<EOF
              <tr>
                <td class="statusnbw" align="right">Current Players:</td>
                <td class="statusnbw" align="left">&nbsp;{$sq_server['numplayers']}</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Min Players:</td>
                <td class="statusnbw" align="left">&nbsp;{$sq_server['minplayers']}</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Max Players:</td>
                <td class="statusnbw" align="left">&nbsp;{$sq_server['maxplayers']}</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Score Limit:</td>
                <td class="statusnbw" align="left">&nbsp;{$sq_server['goalscore']}</td>
              </tr>

EOF;

    if (isset($sq_server["timelimit"]))
      echo "              <tr><td class=\"statusnbw\" align=\"right\">Time Limit:</td><td class=\"statusnbw\" align=\"left\">&nbsp;$timelimit</td></tr>\n";

    if (isset($sq_server["overtime"]))
      echo "              <tr><td class=\"statusnbw\" align=\"right\">Overtime:</td><td class=\"statusnbw\" align=\"left\">&nbsp;$overtime</td></tr>\n";

    if (isset($sq_server["balanceteams"]))
      echo "              <tr><td class=\"statusnbw\" align=\"right\">Balance Teams:</td><td class=\"statusnbw\" align=\"left\">&nbsp;$balanceteams</td></tr>\n";

    if (isset($sq_server["nextmap"]))
      echo "              <tr><td class=\"statusnbw\" align=\"right\">Next Map:</td><td align=\"left\" colspan=\"3\">&nbsp;$nextmap</td></tr>\n";

    if (isset($sq_server["mutator"]))
      echo "              <tr><td class=\"statusnbw\" align=\"right\">Mutators:</td><td align=\"left\" colspan=\"3\">&nbsp;{$sq_server['mutator']}</td></tr>\n";

echo <<<EOF
            </table>
          </td>
        </tr>
      </table>

EOF;

?>