<?php
/* ------------------------------------------------------------------
Script that leads to correct home site.

Author:     Andrin Weiler, IMS
Date:       2019-12-23

History:
Version    	Date            Changes				Changer
1.1         2020-01-11      Signin Update       Andrin
1.0        	2019-12-23      Creation			Andrin

Copyright ©2019 Andrin Weiler, Switzerland. All rights reserved.
------------------------------------------------------------------ */

// scripts which are needed on (almost) every site
session_start();
include_once('pages/includes/functions.inc.php');
include_once('pages/includes/db-con.inc.php');

// include classes
include_once('classes/CMedia.class.php');
include_once('classes/CPost.class.php');
include_once('classes/CComment.class.php');
include_once('classes/CTopicContribution.class.php');
include_once('classes/CClient.class.php');

// check if user is signed in and redirect correctly
if(isset($_SESSION['user']) && isset($_SESSION['token'])){
    // VAR
    $username = $_SESSION['user'];
    $user = createClient($con, $username);

    // blog-admin or owner -> send to admin home
    if($user->getGroup() == "BlogAdmin" || $user->getGroup() == "Owner"){
        if(isset($_GET['alert'])){
            header('Location: pages/admin-home.php?alert=' . $_GET['alert']);
            exit();
        }else{
            header('Location: pages/admin-home.php');
            exit();
        }
    }

    // user -> send to home
    if(isset($_GET['alert'])){
        header('Location: pages/home.php?alert=' . $_GET['alert']);
        exit();
    }else{
        header('Location: pages/home.php');
        exit();
    }
}

// visitor -> send to home
if(isset($_GET['alert'])){
    header('Location: pages/home.php?alert=' . $_GET['alert']);
    exit();
}else{
    header('Location: pages/home.php');
    exit();
}
