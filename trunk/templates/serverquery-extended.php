<?php

echo <<<EOF
      <table class="status" cellspacing="0" cellpadding="1" width="500">
        <tr>
          <td class="statustitle" align="center" colspan="5">
            <b>Current Status for {$sq_server['hostname']}</b>
          </td>
        </tr>
        <tr>
          <td class="statusnbw" align="right" width="95">Address:&nbsp;</td>
          <td class="statusnbw" align="left" width="225"><a href="$query_link" class="status">$displaylink</a></td>
          <td width="10">&nbsp;</td>
          <td class="statusnbw" align="right" width="110">Version:&nbsp;</td>
          <td class="statusnbw" align="left" width="60">$version</td>
        </tr>
        <tr>
          <td class="statusnbw" align="right">Map:&nbsp;</td>
          <td class="statusnbw" align="left">{$sq_server['mapname']}</td>
          <td>&nbsp;</td>
          <td class="statusnbw" align="right">Current Players:&nbsp;</td>
          <td class="statusnbw" align="left">{$sq_server['numplayers']}</td>
        </tr>
        <tr>
          <td class="statusnbw" align="right">Game Type:&nbsp;</td>
          <td class="statusnbw" align="left">{$sq_server['gametype']}</td>
          <td>&nbsp;</td>
          <td class="statusnbw" align="right">Min Players:&nbsp;</td>
          <td class="statusnbw" align="left">{$sq_server['minplayers']}</td>
        </tr>
        <tr>
          <td class="statusnbw" align="right">Password:&nbsp;</td>
          <td class="statusnbw" align="left">$password</td>
          <td>&nbsp;</td>
          <td class="statusnbw" align="right">Max Players:&nbsp;</td>
          <td class="statusnbw" align="left">{$sq_server['maxplayers']}</td>
        </tr>

EOF;

for ($x = 0, $lr = 0; $x < 10; $x++) {
  switch ($x) {
  	case 0:
  	  if (isset($sq_server["gamestats"])) {
        if ($lr == 0) {
          echo "        <tr>\n";
          $lr++;
        }
        echo "          <td class=\"statusnbw\" align=\"right\">Game Stats:&nbsp;</td><td class=\"statusnbw\" align=\"left\">{$sq_server['gamestats']}</td>\n";
        $lr++;
      }
      break;
  	case 1:
  	  if (isset($sq_server["goalscore"])) {
        if ($lr == 0) {
          echo "        <tr>\n";
          $lr++;
        }
        echo "          <td class=\"statusnbw\" align=\"right\">Score Limit:&nbsp;</td><td class=\"statusnbw\" align=\"left\">{$sq_server['goalscore']}</td>\n";
        $lr++;
      }
      break;
    case 2:
      if (isset($sq_server["translocator"])) {
        if ($lr == 0) {
          echo "        <tr>\n";
          $lr++;
        }
        echo "          <td class=\"statusnbw\" align=\"right\">Translocator:&nbsp;</td><td class=\"statusnbw\" align=\"left\">$translocator</td>\n";
        $lr++;
      }
      break;
    case 3:
      if (isset($sq_server["timelimit"])) {
        if ($lr == 0) {
          echo "        <tr>\n";
          $lr++;
        }
        echo "          <td class=\"statusnbw\" align=\"right\">Time Limit:&nbsp;</td><td class=\"statusnbw\" align=\"left\">$timelimit</td>\n";
        $lr++;
      }
      break;
    case 4:
      if (isset($sq_server["elapsedtime"])) {
        if ($lr == 0) {
          echo "        <tr>\n";
          $lr++;
        }
        echo "          <td class=\"statusnbw\" align=\"right\">Time In Game:&nbsp;</td><td class=\"statusnbw\" align=\"left\">$elapsedtime minutes</td>\n";
        $lr++;
      }
      break;
    case 5:
      if (isset($sq_server["overtime"])) {
        if ($lr == 0) {
          echo "        <tr>\n";
          $lr++;
        }
        echo "          <td class=\"statusnbw\" align=\"right\">Overtime:&nbsp;</td><td class=\"statusnbw\" align=\"left\">$overtime</td>\n";
        $lr++;
      }
      break;
    case 6:
      if (isset($sq_server["friendlyfire"])) {
        if ($lr == 0) {
          echo "        <tr>\n";
          $lr++;
        }
        echo "          <td class=\"statusnbw\" align=\"right\">Friendly Fire:&nbsp;</td><td class=\"statusnbw\" align=\"left\">$friendlyfire</td>\n";
        $lr++;
      }
      break;
    case 7:
      if (isset($sq_server["balanceteams"])) {
        if ($lr == 0) {
          echo "        <tr>\n";
          $lr++;
        }
        echo "          <td class=\"statusnbw\" align=\"right\">Balance Teams:&nbsp;</td><td class=\"statusnbw\" align=\"left\">$balanceteams</td>\n";
        $lr++;
      }
      break;
  }

  if ($lr == 2) {
    echo "          <td>&nbsp;</td>\n";
    $lr++;
  }
  else if ($lr == 4) {
    echo "        </tr>\n";
    $lr = 0;
  }
}

if ($lr == 3)
    echo "          <td class=\"statusnbw\" align=\"right\">&nbsp;</td><td class=\"statusnbw\" align=\"left\">&nbsp;</td></tr>\n";

if (isset($sq_server["mapvoting"])) {
  echo <<<EOF
        <tr>
          <td class="statusnbw" align="right">Map Voting:&nbsp;</td>
          <td class="statusnbw" align="left">{$sq_server['mapvoting']}</td>
          <td>&nbsp;</td>
          <td class="statusnbw" align="right">Kick Voting:&nbsp;</td>
          <td class="statusnbw" align="left">{$sq_server['kickvoting']}</td>
        </tr>

EOF;
}

if (isset($sq_server["nextmap"])) {
  echo <<<EOF
        <tr>
          <td class="statusw" align="right">Next Map:&nbsp;</td>
          <td align="left" colspan="4">$nextmap</td>
        </tr>

EOF;
}

echo <<<EOF
        <tr>
          <td class="statusw" align="right">Mutators:&nbsp;</td>
          <td align="left" colspan="4">{$sq_server['mutator']}</td>
        </tr>
      </table>

EOF;

?>