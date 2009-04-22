<div class="help">

<p><span class="helphdr">Frequently Asked Questions</span></p>

<ol>
  <li><a class="help" href="#info">Information about how the stats work</a></li>
  <li><a class="help" href="#rank">How does ranking work?</a></li>
  <li><a class="help" href="#score">Scoring - Individual player points awarded?</a></li>
  <li><a class="help" href="#tscore">Team Scores?</a></li>
  <li><a class="help" href="#glossary">Glossary - Terms and abbreviations.</a></li>
</ol>

<p><a name="info"></a><b>Information about how the stats work</b></p>
<blockquote>A server with stat tracking enabled will send information to the 
stats server about the game - each frag, score, etc. Once the game is 
completed the match is processed and posted on the stats pages. The best 
way to look up a recent match you played is either by your stats ID or the 
server ID. You can search for your user ID or the server ID by name. 
The user names used on the stats pages are based on the last player name you 
used in a match - your stats user name you entered in the networking settings is 
not displayed.</blockquote>

<p><a name="rank"></a><b>How does ranking work?</b></p>
<blockquote>Ranking is based on ELO Chess ranking.
<p>Everyone starts with a rank score of 0. Points are based on how often you 
trigger positive personal score events, e.g. kills, captures, assists, etc. 
Against your score are deaths, suicides, teamkills, opposing team scores, 
etc.</p>
<p>The lower the rank of the other player or team is, against whom you score, the 
less points you will get, so the ripping of weak opponents will get you less 
rank points, than beating a stronger (in rank) opponent.</p></blockquote>

<p><a name="score"></a><b>Scoring - Individual player points award:</b></p>
<blockquote>These depend on the game type you are playing. There are 
individual player scores awarded for special achievements, such as captures, 
assists, etc. See the Score tables on the Players or Matches subpages to find 
out how many points are actually awarded for each score type.</blockquote>

<p><a name="tscore"></a><b>Team Scores:</b></p>
<blockquote>Aside from the individual player scoring in team based gametypes 
(TDM, CTF, BR, AS, ONS, DD), there are also Team Scores, that are awarded to 
your team as a whole, for fulfilling a gametype specific objective:<br />
<ul>
  <li>Team Deathmatch - killing a player from the other team.</li>
  <li>Capture the Flag - capturing the flag.</li>
  <li>Bombing Run - either throwing the ball through the goal, or jumping 
      through the goal holding the ball.</li>
  <li>Assault - achieving the final objective.</li>
  <li>Onslaught - destroying the enemy core.</li>
  <li>Double Domination - 'holding' both domination points for more than 10 
      seconds.</li>
</ul>
Note: A team based game is won by the Team Score; the individual player score 
sums do not matter.</blockquote>

<p><br /><a name="glossary"></a><b>Glossary</b></p>
<blockquote>

<p><a name="fAbb"></a><b>Abbreviations</b></p>
<blockquote>Common abbreviations used in UTStatsDB:<br />
<ul>
  <li>K = Kills</li>
  <li>S = Suicides</li>
  <li>F = Frags</li>
  <li>D = Deaths</li>
  <li>TK = Team Kills</li>
  <li>TD = Team Deaths</li>
  <li>FPH = Frags per Hour</li>
  <li>SPH = Score per Hour</li>
  <li>TTL = Time To Live</li>
</ul>
Game Types:
<ul>
  <li>DM = Deathmatch</li>
  <li>TDM = Team Deathmatch</li>
  <li>CTF = Capture the Flag</li>
  <li>DD = Double Domination</li>
  <li>BR = Bombing Run</li>
  <li>ONS = Onslaught</li>
  <li>LMS = Last Man Standing</li>
</ul></blockquote>

<p><a name="fDeaths"></a><b>Deaths</b></p>
<blockquote>Number of times a player gets killed by another player.<br />
<ul>
  <li>This does not include environment induced deaths, like trap doors. These and 
      self kills are counted separately, as suicides.</li>
  <li>Team based deaths are counted as team deaths.</li>
  <li>In tables with weapon specific information, deaths are the number of times a 
      player died holding that weapon.</li>
</ul></blockquote>

<p><a name="fEff"></a><b>Efficiency</b></p>
<blockquote>A ratio that denotes the player's kill skill by comparing it with 
his overall performance. A perfect efficiency is equal to 1 (100%), 
anything less than 0.5 (50%) is below average.<br />
Formula:   Kills / (Kills + Deaths + Suicides [+Team Kills])</blockquote>

<p><a name="fEvents"></a><b>Events</b></p>
<blockquote>Anything not related to frags, deaths, suicides or kills is defined 
as an event. Typical events would be a flag capture (score related) or a flag 
drop (not score related). Events are mostly used to track all the other things 
going on in a game, that are not frag-related.</blockquote>

<p><a name="fFB"></a><b>First Blood</b></p>
<blockquote>Special event awarded to the player who gets the first kill in a 
newly started match.</blockquote>

<p><a name="fFrags"></a><b>Frags (F)</b></p>
<blockquote>A player's frag count is equal to their kills minus 
suicides. In team games team kills (not team suicides) are also subtracted 
from the player's kills.</blockquote>

<p><a name="fFPH"></a><b>Frags Per Hour (FPH)</b></p>
<blockquote>A ratio between the number of frags a player scores per one 
hour. 30 frags in 5 minutes will give you 360 FPH.<br />
Formula: Frags / (Time played in hours)</blockquote>

<p><a name="fHS"></a><b>Head Shot</b></p>
<blockquote>A special event awarded to a player who kills with a precise shot 
to the head of the victim, causing instant death.</blockquote>

<p><a name="fKills"></a><b>Kills (K)</b></p>
<blockquote>Number of times a player kills another player.</blockquote>

<p><a name="fMK"></a><b>Multi Kills</b></p>
<blockquote>Special event awarded to the player for killing other players in 
a certain time frame. Every time a player scores a kill he has up to 3 
seconds to make another kill. So 2 kills in 3 seconds gets you a Double 
Kill, 3 kills within 3 seconds apart from another a Multi Kill and so on:<br />
<ul>
<li>Double Kill = 2 kills</li>
<li>Multi Kill = 3 kills</li>
<li>Mega Kill = 4 kills</li>
<li>Ultra Kill = 5 kills</li>
<li>Monster Kill = 6 kills</li>
<li>Ludicrous Kill = 7 kills</li>
<li>Holy Shit Kill = 8+ kills</li>
</ul></blockquote>

<p><a name="fPing"></a><b>Ping</b></p>
<blockquote>Measure of your connection quality. Ping is the round trip 
delay in milliseconds that your computer has to the game server. Low 
values are not important for a fun game, but it sure helps.</blockquote>

<p><a name="fSpree"></a><b>Killing Sprees</b></p>
<blockquote>Special event: If you manage to kill 5 or more opponents without 
dying yourself, you will be on a killing spree. If you kill more than 10 
opponents, you are on a rampage, etc.:
<ul>
  <li>Killing Spree! 5 kills</li>
  <li>Rampage! 10 kills</li>
  <li>Dominating! 15 kills</li>
  <li>Unstoppable! 20 kills</li>
  <li>God Like! 25 kills</li>
  <li>Wicked Sick! 30+ kills</li>
</ul></blockquote>

<p><a name="fSuicides"></a><b>Suicides (S)</b></p>
<blockquote>Number of times a player dies due to action of their own cause. 
Suicides can be environment induced (drowning, getting crushed, falling) or 
weapon related (fatal splash damage from their own weapon).</blockquote>

<p><a name="fTD"></a><b>Team Deaths (TD)</b></p>
<blockquote>Number of times a player in a team based game is killed by 
someone on their own team.</blockquote>

<p><a name="fTK"></a><b>Team Kills (TK)</b></p>
<blockquote>Number of times a player in a team based game kills someone on 
their own team. Team kills subtract from a player's personal frags and thus the team frags 
as a whole.</blockquote>

<p><a name="fTTL"></a><b>Time To Live (TTL)</b></p>
<blockquote>A player's average amount of time between deaths.</blockquote>

</blockquote>

</div>
