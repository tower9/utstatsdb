UTStatsDB
  Copyright (C) 2002-2008  Patrick Contreras / Paul Gallier

UTStatsDB is designed to work with PHP 4.2 and MySQL 3.2 or newer. The 
latest version can always be found at http://ut2003stats.sourceforge.net.  You 
can check the version of your file in the file "VERSION.txt". Visit the homepage 
for more information on bug reporting, feature requests and general support.

Please review the LICENSE.txt file included with this program.

Additional documentation can be found in the docs directory, including 
descriptions and examples of the admin configuration screens.

===============================================================================
========== Enabling Local Stats Logging =======================================
===============================================================================
To enable local stats logging on UT3, download UT3Stats from 
http://www.utstatsdb.com and follow the included instructions.


To enable local stats logging on UT2004, add the following to your UT2004.ini file:

[Engine.GameStats]
bLocalLog=True

You have to make sure stats logging is enabled, e.g. ?GameStats=True on the 
commandline.

If you don't want global stats logging change:

GameStatsClass=IpDrv.MasterServerGameStats
to:
GameStatsClass=Engine.GameStats

An extension to the built-in UT2004 logger by El Muerte called ServerExt adds chat logging.
You can find it here: http://ut2004.elmuerte.com/ServerExt

Optionally you may wish to use OLStats by OverloadUT.  This mod adds many 
logging features that don't exist in the built-in UT2004 logs.  Unfortunately, 
using OLStats will likely cause your server to not be listed in the global 
stats, though future versions might fix this.


In order to use this program with UT2003 you will need to download a local stats 
logging program.  I know of two good programs at this time:

LocalStats by Michiel 'El Muerte' Hendriks
 -Server actor that works completely transparent to the clients.
 -Supports remote host via a TCP connection.
 -Bots can be used but are not logged - i.e. bot logins, kills, and deaths do 
  not show up in logs.
 -Item pickups not logged.
 Home Page: http://www.drunksnipers.com/services/ut/ut2003/localstats

LocalLog (Mod) by ^(Hellraiser)^ & McNaz
 -Mod that creates a mirrored set of game types (Log Deathmatch, etc.).
 -Fully supports bots.
 -Includes item pickups, chat log.
 -Clients download mod upon connection.

PHP is available from http://www.php.net
MySQL is available at http://www.mysql.com

===============================================================================
========== Important Changes to Version 3.0 ===================================
===============================================================================
UTStatsDB now uses an HTML admin menu for configuration.  Run admin.php from 
your UTStatsDB path via a web browser.  The default admin password is "admin" 
and the init password is specified in your utstatsdb.inc.php file (see below).

The "CREATE TEMPORARY TABLES" privilege is required as of UTStatsDB 3.0.  Make 
sure you've granted your UTStatsDB database user this privilege.

===============================================================================
========== UTStatsDB installation: ============================================
===============================================================================
Extract the contents of this archive to a directory within your web server's 
public path.

Edit the file statsdb.inc.php:

$dbtype = "MySQL";      // Database type - currently supported: MySQL SQLite.
$dbpre = "ut_";         // Prefix to be prepended to all database table names.
$SQLhost = "localhost"; // The MySQL database host.
$SQLdb = "utstatsdb";   // The MySQL database.
$SQLus = "utstats";     // A MySQL user with SELECT,INSERT,UPDATE,DELETE,CREATE,INDEX grants.
$SQLpw = "statspass";   // The password for the above MySQL user.
$InitPass = "initpass"; // Required for initializing the database tables.
$AutoParse = false;     // Enable to have OLSendLog automatically parse after receiving a new log.

For added security you can optionally copy the 'statsdb.inc.php' file to a 
location outside of your public path.  If so, create a new statsdb.inc.php with 
the following:

<?php require("<absolute-path-to-new-file.php>"); ?>

If you have not yet configure a user and database in your MySQL server, do so 
now - see the MySQL section below.

Run admin.php from a web browser.  On your first run this will ask for the init 
password ($InitPass) specified in statsdb.inc.php.  Make sure all your tables 
are successfully created then continue to the main menu.  The default admin 
password is "admin".

While it's possible to run everything else default, you will need to set options 
in the log configuration.  These settings specify where to find your log files 
and what prefix to use.  They also allow you to specify an ftp server from which 
to download log files.

Also, be sure to change the admin and update passwords in the main configuration.

===============================================================================
========== FTP Notes ==========================================================
===============================================================================
UTStatsDB can download log files from an FTP server.  FTP settings are 
configured in the Log Configuration section of the admin menu. Your web server 
must have FTP support included in PHP (standard on Windows) and must have ftp 
access through the firewall.  Some game hosts do not have ftp ports open or do 
not have the necessary firewall configuration to support ftp.

===============================================================================
========== Remote Logger ======================================================
===============================================================================
There are at least two utilities designed to allow the game server to send logs 
to the web server.  One is included in OverloadUT's OLStats and the other in El 
Muerte's ServerExt.  Please refer to the documentation on these mods for 
specifics.

The older version of ServerExt, called LocalStats, had the ability to stream log 
data out via a TCP connection.  Another program (called rlog) was written to 
receive the data.  Rlog simply listens on a given TCP port for data from your 
game server and stores the data in a log file.  If you would like to use this 
program you can request it on the UTStatsDB site.

===============================================================================
========== PHP ================================================================
===============================================================================
PHP version 4.2 or newer is required for UTStatsDB.  PHP 5.0 or newer is 
required if you wish to run without a MySQL database and use the built-in SQLite 
database.

A few changes are recommended (some even required) to PHP's default 
configuration for UTStatsDB to run properly.  For Windows systems you need to 
have the GD2, MySQL, and sockets extensions enabled.  To enable them uncomment 
(remove the semi-colon) the following lines from your php.ini file:

extension=php_gd2.dll
extension=php_mysql.dll
extension=php_sockets.dll

It's also recommended to set your PHP memory limit to at least 16MB (32MB 
recommended) via:

memory_limit = 32M

===============================================================================
========== MySQL Database =====================================================
===============================================================================
For the most part this assumes familiarity with MySQL, however, here's a brief 
step-by-step on setting up a MySQL database for use with UTStatsDB.

If you already have a user and database you can use, or if you have no options 
of such, such as when leasing space on a shared server, just enter your MySQL 
username and database information into utstatsdb.inc.php.  Since the MySQL 
server is usually running on the same computer as the web server, you can 
generally leave the hostname ($SQLhost) as its default 'localhost'.  If the 
database is running on a different server you will need to change this value.

If you're running the server on your own system then you will probably want to 
create a seperate user and database to use with UTStatsDB.  To create a new 
database, login to MySQL with an account that has full privileges (such as root) 
and enter:

CREATE utstatsdb;

This will create a new database called 'utstatsdb'.  Next create a new user for 
the UTStatsDB program and give them the necessary rights:

GRANT SELECT,INSERT,UPDATE,DELETE,CREATE,DROP,ALTER,INDEX
ON utstatsdb.*
TO utsuser@localhost
IDENTIFIED BY 'password';

Change 'password' to something more secure and change it to match in 
utstatsdb.inc.php.  You're now ready to run createtables.php to setup your 
tables.

===============================================================================
========== SQLite Database ====================================================
===============================================================================
UTStatsDB now supports SQLite, a public domain database system that's built into 
PHP 5.x.  As of this writing, PHP 5 and SQLite support is in beta.  There are no 
guarantees they will work okay for you.  Using SQLite with PHP 5 requires no 
special configuration - the entire MySQL setup can be ignored.  Just set your 
$dbtype variable in statsdb.inc.php to "SQLite" and $SQLdb to whatever filename 
you wish to use (absolute and relative paths are supported) and that's it. 
SQLite is much slower than MySQL, so you will need to increase your 
max_execution_time in php.ini and $php_timelimit in config.inc.php to at least a 
few minutes.

===============================================================================
========== Running the Log Parser =============================================
===============================================================================
To parse the log files run:
http://yourwebsite.com/utstats/logs.php?pass=updatepass
(change "updatepass" to your UpdatePass specified in statsdb.inc.php)

Optional Parameters:
&savelogs - Doesn't delete the log files (if you run it again it
            will attempt to parse the same files over again)
&multi    - Calculates multi-kills - use only for logs generated by
            older versions of LocalLog (pre 0.93)
&nohtml   - Doesn't display html tags (for command line use).

The logs.php file can be run from the command line.
You can run it using:
php logs.php pass=test

You can easily add this to your crontab or task scheduler to run periodically.

When the log parser is run, it will delete all but the last two incomplete logs
for each individual server name.  This is to prevent the system from deleting
logs for games that are still in session.

Only matches that have and EndGame (EG) tag line for a frag/score limit or time
limit reached will be added to the database.  If the server is shut down or the
map changed mid-game it will not be logged.

The main viewer page is accessed via index.php.  Set your web server to service 
the index.php page in your utstats directory by default.

The log parser can also be run from the admin menu.

===============================================================================
========== Upgrading ==========================================================
===============================================================================
UTStatsDB 3.00 is not compatible with earlier versions of the database.  You 
must reinitialize your 1.x or 2.x database tables.  There is no update utility. 
If you saved logs from earlier you can reparse them after rebuilding the tables.

===============================================================================
========== Server Query =======================================================
===============================================================================
The server query (see the notes on the config.inc.php file) supports server 
queries via the GameSpy protocol.  Support is included for El Muerte's ServQuery 
protocol (now part of ServerExt).  ServQuery is an extension to the existing 
GameSpy query and allows for more information to be attained.  You can find 
ServQuery here:
UT2003: http://www.drunksnipers.com/services/ut/ut2003/servquery/
UT2004: http://ut2004.elmuerte.com/ServerExt

The GameSpy protocol can also be used, but this is disabled on systems that are 
not set to advertise the server.  Also, GameSpy queries results sent by the game 
server are delayed information.

Socket support is required in PHP in order to use this function.  This is 
enabled on Windows versions by uncommenting the following line in your php.ini 
file:
extension=php_sockets.dll

===============================================================================
========== DemoRec Files ======================================================
===============================================================================
The Match Stats page of UTStatsDB will look for a file containing a matching 
date/time string in the directory specified by the $demodir config variable 
(with $demoext as the extension) and provide a link to the user if found.  The 
link is generated with the prefix take from $demourl.  UTStatsDB can also be 
configured to automatically download demorec files via ftp (see the 
config.inc.php notes).  If you've maxmatches set, older demorec files will be 
deleted as the associated matches are removed.  In your DemoRecord.ini file you 
must set 'bSetNums' to False and leave 'FileName' blank.

===============================================================================
========== Platform Notes =====================================================
===============================================================================
Windows servers:
 You may need to manually enable the gd library extension my editing your 
 php.ini file.  Look for and entry under ";Windows Extensions" that looks
 like this:
 ;extension=php_gd2.dll
 Remove the semicolon and restart your web server.
 Older versions of PHP use php_gd.dll which will work as well.

===============================================================================
========== Map Images =========================================================
===============================================================================
UTStatsDB 3.00 supports displaying map images when available.  Simply place 
images in a directory called "images" within your UTStatsDB directory.  Images 
must be either use .gif or .jpg extensions and the filename must be all 
lowercase and match the filenames of the maps.  Images are displayed as 256 
pixels wide by 192 pixels high.  There is a map collection pack available for 
download on the UTStatsDB downloads page.

===============================================================================
========== Database File ======================================================
===============================================================================
Here is a list of all associated database tables:

ut_aliases		CD keys that are logged by each player
ut_config		Main configuration
ut_configlogs	Log file and ftp configuration
ut_configmenu	Side-bar menu configuration
ut_configquery	Server query configuration
ut_configset	Other config settings that don't fit in the more structured config table
ut_connections	Player connection / disconnect times
ut_gbots		Individual match bot stats
ut_gchat		Individual match chat logs
ut_gevents		Individual match event logs
ut_gitems		Individual match item pickups
ut_gkills		Individual match kill log
ut_gplayers		Individual match player list
ut_gscores		Individual match scoring log
ut_gwaccuracy	Individual match weapon accuracy
ut_items		Item list (each unique item found in the game)
ut_maps			Map statistics
ut_matches		Individual match logs
ut_mwkills		Individual match weapon stats
ut_objectives	Assault objectives
ut_pitems		Individual player item pickups
ut_players		Player data (all players, both humans and bots)
ut_playersgt	Game type specific player data
ut_pwkills		Individual player weapon stats
ut_servers		Server statistics
ut_tkills		Individual match team scoring
ut_totals		Global totals and high scores
ut_type			Game types
ut_weapons		Global weapons list with stats

There are a few tables that you may want to edit if you install certain mods:

ut_type - This stores a list of each game description (Deathmatch, CTF, etc.)
          and the type of game.  If you were to install a mod that includes a
          deathmatch style game called "Death Arena" for example, you'd want
          to include an entry in the ut_type table with tp_desc set to match
          the game type description ("Death Arena") and tp_type set to "1"
          which corresponds to the type deathmatch.  The game will automatically
          add any non-existent game types it encounters, but all stats will be
          added to the "Other" type category until modified.

ut_weapons - This is a list of all weapons found in the game including the
             descriptors for each as found in the logs.  Again, the game will
             automatically add any new weapons found in the logs here, but
             preferably these should be added before you process the logs.
             wp_type is the log description for the weapon, such as
             "DamTypeFlakShell".  In the wp_desc field you would want to set
             this to "Flak Cannon".  Since the flak shell is the weapon's
             secondary function you'll want to set wp_secondary to 1.
             Set wp_secondary to 2 for tertiary functions such as the shock
             combo, though this will still be calculated into the totals for
             secondary functions.

ut_items - A list of any items picked up in the game, including weapons and ammo.
           If new items are added to your game you'll want to edit the item's
           description (it_desc) to an appropriate name (such as "Damage Amplifer"
           for the "UDamagePickup").

The rest of the tables read descriptions from these three tables and should never
require any modification.

There is a step-by-step installation guide included with this archive.  See 
SETUP_GUIDE.txt for details.
