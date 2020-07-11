<?php
/* ------------------------------------------------------------------
Script to create a comment

Author:     Andrin Weiler, IMS
Date:       2020-02-18

History:
Version    	Date            Changes			        Changer
1.0        	2020-02-18      Creation    	        Andrin

Copyright ©2020 Andrin Weiler, Switzerland. All rights reserved.
------------------------------------------------------------------ */

include_once('db-con.inc.php');
include_once('functions.inc.php');
include_once('../../classes/CClient.class.php');
include_once('../../classes/CMedia.class.php');
include_once('../../classes/CPost.class.php');
include_once('../../classes/CComment.class.php');
include_once('../../classes/CTopicContribution.class.php');
session_start();

// check if user is signed in
if (isset($_SESSION['user']) && isset($_SESSION['token'])){ # check if user is signed in
    $username = $_SESSION['user'];
    $token = $_SESSION['token'];

    $user = createClient($con, $username);

    // check if sign in is valid
    if($user == false){
        header('Location: ../../index.php?alert=user-object-not-created');
        exit();
    }

    if($user->getToken() != $token){
        header('Location: ../../index.php?alert=invalid-signin');
        exit();
    }

}else{
    header('Location: ../../index.php?alert=donoteventry&p=po-a');
    exit();
}

if(isset($_POST['comment'])){
    // VAR
    $id = $_GET['id'];
    $content = mysqli_real_escape_string($con, $_POST['content']);

    // ERROR HANDLING
    if (! $post = createPost($con, $id)){ # fatal error
        header("Location: ../post.php?alert=post-not-found");
        exit();
    }

    if (empty($content)){
        header("Location: ../post.php?alert=empty&id=$id");
        exit();
    }

    $comment = new CComment($content, $post, $user);
    $comment->insert($con);

    header("Location: ../post.php?id=$id");
    exit();

}else{ # enter site without upload button
    header('Location: ../../index.php?alert=donoteventry&p=com');
    exit();
}
