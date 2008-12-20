<?php
$dbtype = "MySQL";      // Database type - currently supported: MySQL SQLite MsSQL
$dbpre = "ut_";         // Prefix to be prepended to all database table names.
$SQLhost = "localhost"; // The MySQL database host.
$SQLport = 3306;        // TCP port or Linux socket to use (/var/lib/mysql/mysql.sock)
$SQLdb = "utstatsdb";   // The MySQL database name or full path to SQLite database file.
$SQLus = "utstats";     // A MySQL user with SELECT,INSERT,UPDATE,DELETE,CREATE,INDEX,CREATE TEMPORARY TABLES grants.
$SQLpw = "statspass";   // The password for the above MySQL user.
$InitPass = "initpass"; // Required for initializing the database tables.

// Optionally you can include the following line modified with the path to a file 
// outside of your web path with the above information in it:
// require("/path_to_file/statsdb.inc.php");
