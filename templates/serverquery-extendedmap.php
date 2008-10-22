<?php

$rows = 7;
if (isset($sq_server["gamestats"]))
  $rows++;
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
if (isset($sq_server["goalscore"]))
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
                <td class="statusnbw" align="right" width="95">Address:&nbsp;</td>
                <td class="statusnbw" align="left" width="120"><a href="$query_link" class="status">$displaylink</a></td>
                <td rowspan="$rows" align="center" valign="middle" width="268">
                  <img src="mapimages/$mapimage" width="256" height="192" border="1" alt="Map Image" title="{$sq_server['mapname']}" />
                </td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Map:&nbsp;</td>
                <td class="statusnbw" align="left">{$sq_server['mapname']}</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Game Type:&nbsp;</td>
                <td class="statusnbw" align="left">{$sq_server['gametype']}</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Password:&nbsp;</td>
                <td class="statusnbw" align="left">$password</td>
              </tr>

EOF;

if (isset($sq_server["gamestats"]))
  echo "              <tr><td class=\"statusnbw\" align=\"right\">Game Stats:&nbsp;</td><td class=\"statusnbw\" align=\"left\">{$sq_server['gamestats']}</td></tr>\n";

if (isset($sq_server["translocator"]))
  echo "              <tr><td class=\"statusnbw\" align=\"right\">Translocator:&nbsp;</td><td class=\"statusnbw\" align=\"left\">$translocator</td></tr>\n";

if (isset($sq_server["elapsedtime"]))
  echo "              <tr><td class=\"statusnbw\" align=\"right\">Time In Game:&nbsp;</td><td class=\"statusnbw\" align=\"left\">$elapsedtime minutes</td></tr>\n";

if (isset($sq_server["mapvoting"]))
  echo "              <tr><td class=\"statusnbw\" align=\"right\">Map Voting:&nbsp;</td><td class=\"statusnbw\" align=\"left\">{$sq_server['mapvoting']}</td></tr>\n";

if (isset($sq_server["kickvoting"]))
  echo "              <tr><td class=\"statusnbw\" align=\"right\">Kick Voting:&nbsp;</td><td class=\"statusnbw\" align=\"left\">{$sq_server['kickvoting']}</td></tr>\n";

if (isset($sq_server["friendlyfire"]))
  echo "              <tr><td class=\"statusnbw\" align=\"right\">Friendly Fire:&nbsp;</td><td class=\"statusnbw\" align=\"left\">$friendlyfire</td></tr>\n";

if (isset($sq_server["version"]))
  echo "              <tr><td class=\"statusnbw\" align=\"right\">Version:&nbsp;</td><td class=\"statusnbw\" align=\"left\" width=\"60\">$version</td></tr>\n";

echo <<<EOF
              <tr>
                <td class="statusnbw" align="right">Current Players:&nbsp;</td>
                <td class="statusnbw" align="left">{$sq_server['numplayers']}</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Min Players:&nbsp;</td>
                <td class="statusnbw" align="left">{$sq_server['minplayers']}</td>
              </tr>
              <tr>
                <td class="statusnbw" align="right">Max Players:&nbsp;</td>
                <td class="statusnbw" align="left">{$sq_server['maxplayers']}</td>
              </tr>

EOF;

if (isset($sq_server["goalscore"]))
  echo "              <tr><td class=\"statusnbw\" align=\"right\">Score Limit:&nbsp;</td><td class=\"statusnbw\" align=\"left\">{$sq_server['goalscore']}</td></tr>\n";

if (isset($sq_server["timelimit"]))
  echo "              <tr><td class=\"statusnbw\" align=\"right\">Time Limit:&nbsp;</td><td class=\"statusnbw\" align=\"left\">{$timelimit}</td></tr>\n";

if (isset($sq_server["overtime"]))
  echo "              <tr><td class=\"statusnbw\" align=\"right\">Overtime:&nbsp;</td><td class=\"statusnbw\" align=\"left\">{$overtime}</td></tr>\n";

if (isset($sq_server["balanceteams"]))
  echo "              <tr><td class=\"statusnbw\" align=\"right\">Balance Teams:&nbsp;</td><td class=\"statusnbw\" align=\"left\">{$balanceteams}</td></tr>\n";

if (isset($sq_server["nextmap"]))
  echo "              <tr><td class=\"statusnbw\" align=\"right\">Next Map:&nbsp;</td><td align=\"left\" colspan=\"3\">{$nextmap}</td></tr>\n";

if (isset($sq_server["mutator"]))
  echo "              <tr><td class=\"statusnbw\" align=\"right\">Mutators:&nbsp;</td><td align=\"left\" colspan=\"3\">{$sq_server['mutator']}</td></tr>\n";

echo <<<EOF
            </table>
          </td>
        </tr>
      </table>

EOF;

?>