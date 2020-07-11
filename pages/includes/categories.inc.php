<?php
/* ------------------------------------------------------------------
Script that adds and deletes categories after pressing the right button.

Author:     Andrin Weiler, IMS
Date:       2020-01-03

History:
Version    	Date            Changes			 Changer
1.0        	2020-01-03      Creation    	 Andrin

Copyright ©2019 Andrin Weiler, Switzerland. All rights reserved.
------------------------------------------------------------------ */

include_once('db-con.inc.php');
include_once('functions.inc.php');
include_once('../../classes/CClient.class.php');
include_once('../../classes/CTopicContribution.class.php');
session_start();

// check if user is signed in
if (isset($_SESSION['user']) && isset($_SESSION['token'])){ # user is signed in
    $username = $_SESSION['user'];
    $token = $_SESSION['token'];

    $user = createClient($con, $username);

    // check if sign in is valid
    if($user == false){
        header('Location: ../index.php?alert=user-object-not-created');
        exit();
    }

    if($user->getToken() != $token){
        header('Location: ../index.php?alert=invalid-signin');
        exit();
    }

    // check if client has permission
    if($user->getGroup() == "User"){
        header('Location: ../index.php?alert=permission-denied');
        exit();
    }
}else{
    header('Location: ../categories.php?alert=donoteventry');
    exit();
}

// ADD CATEGORY
if(isset($_POST['add'])){
    // VAR
    $name = mysqli_real_escape_string($con, $_POST['cat']);

    // check if fields are empty
    if (empty($name)){
        header('Location: ../categories.php?alert=empty');
        exit();
    }

    // insert into database
    $user->addCat($con, $name, null);

    header('Location: ../categories.php?success=add');
    exit();

// DELETE CATEGORY
}else if (isset($_POST['del'])){
    // VAR
    $name = mysqli_real_escape_string($con, $_POST['del']);

    // delete category
    foreach ($user->getCategories() as $cat){
        if ($cat->getName() == $name){
            $user->deleteCat($con, $name, null, $cat->getId());
        }
    }

    header('Location: ../categories.php?success=del');
    exit();
}else{ # enter site without delete button
    header('Location: ../../index.php?alert=donoteventry&p=cat');
    exit();
}
