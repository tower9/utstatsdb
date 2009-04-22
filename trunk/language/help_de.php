<div class="help">

<p><span class="helphdr">H�ufig Gestellte Fragen</span></p>

<ol>
  <li><a class="help" href="#info">Wie die Stats funktionieren</a></li>
  <li><a class="help" href="#rank">Wie funktioniert Ranking?</a></li>
  <li><a class="help" href="#score">Scoring - Wie werden individuelle Spielerpunkte vergeben?</a></li>
  <li><a class="help" href="#tscore">Team Scores?</a></li>
  <li><a class="help" href="#glossary">Glossar - Bezeichnungen und Abk�rzungen</a></li>
</ol>

<p><a name="info"></a><b>Wie die Stats funktionieren</b></p>
<blockquote>Ein Server mit aktiviertem Stat Tracking schickt Spieldaten zum 
Stats Server - jedes frag, score etc.  Sobald ein Spiel beendet ist, wird das 
Match vom Stat Server verarbeitet und auf der Stats Seite ver�ffentlicht.  Am 
leichtesten findest Du ein Spiel, das Du k�rzlich gespielt hast, wenn Du 
entweder unter Deiner Stats ID oder der ID des Servers auf dem Du gespielt hast, 
suchst.  Beide kannst Du unter dem jeweiligen Namen finden.  Auf den Stats 
Seiten findest Du Daten unter dem Spielernamen ver�ffentlicht, den Du im zuletzt 
gespielten Match benutzt hast - Dein Stats Benutzername, den Du unter 
Netzwerkeinstellungen eingegeben hast, wird nicht angezeigt.</blockquote>

<p><a name="rank"></a><b>Wie funktioniert Ranking?</b></p>
<blockquote>Ranking basiert auf der Berechnung der ELO Rangfolge im Schachspiel.
<p>Jeder Spieler beginnt mit 0.  Die Vergabe von Punkten erfolgt jenachdem wie 
oft positive pers�nliche score events wie kills, captures, assists, etc. 
erfolgen.  Deaths, suicides, teamkills, opposing team scores, und so weiter 
beeinflussen Deine Rangfolge negativ.</p>
<p>Je niedriger der Rang des gegnerischen Spielers oder Teams gegen die Du 
punktest, desto weniger Punkte erh�ltst Du.  Das Abschlachten schw�cherer Gegner 
wird Dir also weniger einbringen als wenn Du einen rangh�heren Gegner 
besiegst.</p></blockquote>

<p><a name="score"></a><b>Scoring - Wie werden individuelle Spielerpunkte vergeben?</b></p>
<blockquote>Dies h�ngt davon ab, welchen Spieltyp Du spielst.  Individuelle 
Spielerpunkte werden f�r verschiedene Leistungen wie captures, assists, etc. 
vergeben.  Schau Dir die Score Tabellen auf den "Players" oder "Matches" Seiten 
an um herauszufinden, wieviele Punkte f�r jeden Score Typ vergeben 
werden.</blockquote>

<p><a name="tscore"></a><b>Team Scores:</b></p>
<blockquote>Zus�tzlich zu den oben genannten individuellen Spielerpunkten werden 
in Teamspielen (TDM, CTF, BR, DD) Team Scores vergeben.  Diese Punkte gehen an 
Dein gesamtes Team, fur spieltypabh�ngige Leistungen:<br />
<ul>
  <li>Team Deathmatch - T�ten eines Spielers des gegnerischen Teams.</li>
  <li>Capture the Flag - Eroberung der gegnerischen Flagge.</li>
  <li>Bombing Run - Entweder ein Torwurf oder Sprung durch das Tor w�hrend Du den Ball h�ltst.</li>
  <li>Assault - das Endziel erreichen.</li>
  <li>Onslaught - das Core des Feindes zu zerst�ren.</li>
  <li>Double Domination - Das 'Halten' beider domination points fur �ber 10 Sekunden.</li>
</ul>
Wichtig: Ein Teamspiel wird nach Teampunkten gewonnen, die Summe der 
individuellen Spielerpunkte bleibt unber�cksichtigt.</blockquote>

<p><br /><a name="glossary"></a><b>Glossar</b></p>
<blockquote>

<p><a name="fAbb"></a><b>Abk�rzungen</b></p>
<blockquote>�bliche UTStatsDB Abk�rzungen:<br />
<ul>
  <li>K = Kills</li>
  <li>S = Suicides</li>
  <li>F = Frags</li>
  <li>D = Deaths</li>
  <li>TK = Team Kills</li>
  <li>TD = Team Deaths</li>
  <li>FPH = Frags per Stunde (Hour)</li>
  <li>SPH = Score per Stunde (Hour)</li>
  <li>TTL = Durchscnittliche �berlebenszeit</li>
</ul>
Spieltypen:
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
<blockquote>Wie oft ein Spieler von anderen Spielern get�tet wurde.<br />
<ul>
  <li>Situationen, in denen ein Spieler durch andere Umst�nde get�tet wird (z.B. 
  Fallt�ren o.�.), werden hierunter nicht gez�hlt, sondern zusammen mit 
  "Selbstt�tungen" (unabsichtliche Selbstmorde) unter "suicides".</li>
  <li>In Teamspielen werden "deaths" als "team deaths" gez�hlt.</li>
  <li>In Tabellen mit waffenspezifischen Informationen werden "deaths" als 
  Situationen erfasst, in denen ein Spieler stirbt w�hrend er die erw�hnte Waffe 
  h�lt.</li>
</ul></blockquote>

<p><a name="fEff"></a><b>Effizienz</b></p>
<blockquote>Das Verh�ltnis der Geschicklichkeit eines Spielers zu seiner 
allgemeinen Leistung.  Perfekte Effizienz entspricht 1 (100%), alle Werte unter 
0.5 (50%) sind unterdurchschnittlich.<br />
Formel: Kills / (Kills + Deaths + Suicides [+Team Kills])</blockquote>

<p><a name="fEvents"></a><b>Ereignisse</b></p>
<blockquote>Alles, was nichts mit frags, deaths, suicides oder kills zu tun hat, 
wird als "event" bezeichnet, z.B. ein flag capture (beeinflusst den Spielstand) 
oder ein flag drop (hat keinen Einfluss auf den Spielstand).  Events werden 
haupts�chlich benutzt, um all die anderen Vorg�nge im Spiel zu erfassen, die 
nichts mit frags zu tun haben.</blockquote>

<p><a name="fFB"></a><b>First Blood</b></p>
<blockquote>Spezielles Ereignis, das anzeigt wenn ein Spieler den ersten kill in 
einem neu begonnenen Match ausgef�ht hat.</blockquote>

<p><a name="fFrags"></a><b>Frags (F)</b></p>
<blockquote>Der frag count eines Spielers ist die Anzahl seiner kills minus 
suicides.  In Teamspielen werden auch team kills (aber nicht team suicides) von 
den kills eines Spielers abgezogen.</blockquote>

<p><a name="fFPH"></a><b>Frags Per Hour (FPH)</b></p>
<blockquote>Anzahl der frags die ein Spieler per Stunde erreicht.  30 frags in 5 
Minuten bedeutet ein Verh�ltnis von 360 FPH.<br />
Formel: Frags / (Spielzeit in Stunden)</blockquote>

<p><a name="fHS"></a><b>Head Shot</b></p>
<blockquote>"Kopfschuss" - spezielles Ereignis, das anzeigt, wenn ein Spieler 
sein Opfer durch einen pr�zisen Kopfschuss t�tet.</blockquote>

<p><a name="fKills"></a><b>Kills (K)</b></p>
<blockquote>Anzahl der kills eines Spielers gegen andere Spieler.</blockquote>

<p><a name="fMK"></a><b>Multi Kills</b></p>
<blockquote>Spezielles Ereignis, das anzeigt, wenn ein Spieler eine Anzahl 
andere Spieler in einem bestimmten Zeitraum t�tet.  Jedesmal wenn ein Spieler 
einen kill erzielt, hat er bis zu 3 Sekunden Zeit, den n�chsten zu erreichen.  2 
kills in 3 Sekunden bedeutet einen Double Kill, 3 kills jeweils 3 Sekunden 
auseinander einen Multi Kill und so weiter:<br />
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
<blockquote>Bezeichnung fur die Qualit�t der Verbindung zum Server.  Der Ping 
misst die Zeit in Millisekunden, die ein Datenpaket braucht, um von Deinem 
Computer zum Server und wieder zur�ck geschickt zu werden.  Niedrige Werte sind 
nicht unbedingt wichtig um Spass an einem Spiel zu haben, steigern aber die 
Spielqualit�t.</blockquote>

<p><a name="fSpree"></a><b>Killing Sprees</b></p>
<blockquote>Spezielles Ereignis: Wenn Du es schaffst, 5 oder mehr Gegner zu 
t�ten, ohne selbst get�tet zu werden, bist Du auf einer "killing spree".  Wenn 
Du mehr als 10 Gegner t�test, ist es eine "rampage" usw.:
<ul>
  <li>Killing Spree! 5 kills</li>
  <li>Rampage! 10 kills</li>
  <li>Dominating! 15 kills</li>
  <li>Unstoppable! 20 kills</li>
  <li>God Like! 25 kills</li>
  <li>Wicked Sick! 30+ kills</li>
</ul></blockquote>

<p><a name="fSuicides"></a><b>Suicides (S)</b></p>
<blockquote>Erfasst wie oft ein Spieler durch eigenes Verschulden stirbt. 
Suicides k�nnen durch Ereignisse in der Spielumgebung (ertrinken, erdr�ckt 
werden, abst�rzen) oder durch Waffen (t�dliche Verletzung durch die eigene 
Waffe) ausgel�st werden.</blockquote>

<p><a name="fTD"></a><b>Team Deaths (TD)</b></p>
<blockquote>Erfasst wie oft ein Spieler in einem Teamspiel von jemandem aus dem 
eigenen Team get�tet wird.</blockquote>

<p><a name="fTK"></a><b>Team Kills (TK)</b></p>
<blockquote>Erfasst, wie oft ein Spieler in einem Teamspiel jemand aus dem 
eigenen Team t�tet.  Team kills werden von den frags des betreffenden Spielers 
abgezogen und verringern so auch den Stand der team frags.</blockquote>

<p><a name="fTTL"></a><b>Time To Live (TTL)</b></p>
<blockquote>Durchscnittliche �berlebenszeit eines Spielers zwischen deaths.</blockquote>

</blockquote>

</div>
