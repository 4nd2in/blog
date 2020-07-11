<?php
/* ------------------------------------------------------------------
Script to edit a posts

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
    $post = createPost($con, )
}else{
    header('Location: ../..index.php');
    exit();
}
