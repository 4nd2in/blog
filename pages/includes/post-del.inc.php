<?php
/* ------------------------------------------------------------------
Script to delete a posts

Author:     Andrin Weiler, IMS
Date:       2020-02-20

History:
Version    	Date            Changes			        Changer
1.0        	2020-02-20      Creation    	        Andrin

Copyright ©2020 Andrin Weiler, Switzerland. All rights reserved.
------------------------------------------------------------------ */
include_once('db-con.inc.php');
include_once('functions.inc.php');
include_once('../../classes/CClient.class.php');
include_once('../../classes/CMedia.class.php');
include_once('../../classes/CPost.class.php');
include_once('../../classes/CTopicContribution.class.php');
session_start();

if (isset($_GET['id'])){
    // VAR
    $id = mysqli_real_escape_string($con, $_GET['id']);

    if (! $post = createPost($con, $id)){
        header('Location: ../admin-home.php?alert=sql-error');
        exit();
    }

    // delete post
    $post->setIsDeleted($con, 1);

    header('Location: ../admin-home.php');
    exit();

}else{
    header('Location: ../../index.php');
    exit();
}
