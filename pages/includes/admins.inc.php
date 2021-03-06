<?php
/* ------------------------------------------------------------------
Script that includes all needed functions of blog_cms

Author:     Andrin Weiler, IMS
Date:       2020-02-25

History:
Version    	Date            Changes				                 Changer
1.0        	2020-02-25      creation                        	 Andrin

Copyright ©2020 Andrin Weiler, Switzerland. All rights reserved.
------------------------------------------------------------------ */
include_once('db-con.inc.php');
include_once('functions.inc.php');
include_once('../../classes/CClient.class.php');
session_start();

// check if client is owner
$user = createClient($con, $_SESSION['user']);
if($user->getGroup() == "Owner"){

    if (isset($_POST['update'])){
        // VAR
        $userGroups = $_POST['userGroups']; # POST returns an array
        $usernames = $_POST['usernames']; # POST returns an array

        foreach ($userGroups as $key=>$userGroup) {
            $username = mysqli_real_escape_string($con, $_POST['username']);
            $user = createClient($con, $usernames[$key]); # arrays are sync
            $sql = "";

            // check wich is selected and update userGroup if needed
            if ($userGroup == "User" && $user->getGroup() != "User"){
                $user->setGroup($con, 'User');

            }else if ($userGroup == "BlogAdmin" && $user->getGroup() != "BlogAdmin"){
                $user->setGroup($con, 'BlogAdmin');

            }else if ($userGroup == "Owner" && $user->getGroup() != "Owner"){
                $user->setGroup($con, 'Owner');
            }
        }

        header('Location: ../admins.php?alert=gourps-updated-success');
        exit();

    }else{ # enter site without upload button
        header('Location: ../../index.php?alert=donoteventry');
        exit();
    }
}else{ # no permission
    header('Location: ../../index.php?alert=permission-denied');
    exit();
}
