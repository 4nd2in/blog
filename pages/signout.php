<?php
/* ------------------------------------------------------------------
Script that sign out the user after clicking on sign out.

Author:     Andrin Weiler, IMS
Date:       2019-12-27

History:
Version    	Date            Changes			 Changer
1.1         2020-01-11      Signin UPDATE    Andrin
1.0        	2019-12-27      Creation    	 Andrin

Copyright ©2019 Andrin Weiler, Switzerland. All rights reserved.
------------------------------------------------------------------ */

include_once('../classes/CClient.class.php');
include_once('includes/functions.inc.php');
include_once('includes/db-con.inc.php');
session_start();

if (isset($_POST['signout']) && isset($_SESSION['user'])){

    $username = $_SESSION['user'];

    $user = createClient($con, $username);

    $user->setToken($con, null); # reset token for next sign in
    $user = null;

    session_destroy(); # unset sessions and delete it from storage
    header('Location: ../index.php?alert=signout-success'); # index redirects the user correctly
    exit();

}else{ # enter page without sign out.
    header('Location: ../index.php?alert=donoteventry&p=out'); # index redirects the user correctly
    exit();
}
