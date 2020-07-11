<?php
/* ------------------------------------------------------------------
Script that connects to database

Author:     Andrin Weiler, IMS
Date:       2019-12-23

History:
Version    	Date            Changes		 		Changer
1.2         2020-02-08      added constant      Andrin
1.1			2020-01-09		added host data		Andrin
1.0        	2019-12-23      Creation	 		Andrin

Copyright ©2019 Andrin Weiler, Switzerland. All rights reserved.
------------------------------------------------------------------ */
// CONST
const DBHOST = 'ukugohak.mysql.db.hostpoint.ch';
const DBPASSWD = '4#D!?VT6q?akeUZ';

const MAILHOST = 'asmtp.mail.hostpoint.ch';
const MAILPASSWD = 'KHUHUzDZVPz9MRS';

const PEPPER = '.zXr-4y=^OVVkh;QsN\Na';
const COST = 12;

// DATABASE CONNECTION
$dbHost = DBHOST;
$dbUser = 'ukugohak_php';
$dbPassword = DBPASSWD;
$dbName = 'ukugohak_blog';

// create connection with mysqli class
$con = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);
$con->set_charset("utf8mb4");

// checking db connection
if($con->connect_error){
    header("Location: home.php?alert=connection-failed");
    exit();
}
