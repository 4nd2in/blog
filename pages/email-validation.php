<?php
/* ------------------------------------------------------------------
Script that check if EMail has been confirmed.

Author:     Andrin Weiler, IMS
Date:       2019-12-27

History:
Version    	Date            Changes			 Changer
1.2         2020-02-01      Bug fix          Andrin
1.1         2020-01-11      Signin Update    Andrin
1.0        	2019-12-27      Creation    	 Andrin

Copyright ©2019 Andrin Weiler, Switzerland. All rights reserved.
------------------------------------------------------------------ */

include_once('includes/db-con.inc.php');
include_once('functions.inc.php');
include_once('../classes/CClient.class.php');
session_start();

if (isset($_GET['username']) && isset($_GET['email']) && isset($_GET['token'])){

    // VAR
    $username = mysqli_real_escape_string($con, $_GET['username']);
    $email = mysqli_real_escape_string($con, $_GET['email']);
    $token = mysqli_real_escape_string($con, $_GET['token']);

    // check if token is correct
    $sql = "SELECT * FROM TUsers WHERE UserUsername = ? AND UserToken = ?";
    $stmt = mysqli_stmt_init($con);

    if (mysqli_stmt_prepare($stmt, $sql)){
        mysqli_stmt_bind_param($stmt, 'ss', $username, $token);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }else{
        header('Location: signup.php?alert=sql-error&l=33');
        exit();
    }

    // check if user was found
    if($result->num_rows < 1){ # should never be the case if the user enters the site correctly
        mysqli_stmt_close($stmt);
        mysqli_close($con);
        header('Location: signup.php?alert=user-not-found');
        exit();
    }

    // VALIDATE EMAIL
    if ($row = mysqli_fetch_assoc($result)){

        // create object -> client must be user because he just signed up
        $user = new CUser($con, $row['UserUsername'], $row['UserEmail'], $row['UserFirstname'], $row['UserLastname'], $row['UserPassword'],
                $row['UserToken'], $row['UserDesc'], $row['UserGroup']);

        // insert email in database and add to object
        if ($user->setEmail($con, $email) == false){
            mysqli_stmt_close($stmt);
            mysqli_close($con);
            header('Location: signup.php?alert=sql-error&l=51');
            exit();
        }

        // reset token
        if ($user->setToken($con, null) == false){
            mysqli_stmt_close($stmt);
            mysqli_close($con);
            header('Location: signup.php?alert=sql-error&l=57');
            exit();
        }

        // send to sign in

        header('Location: signin.php?alert=verification-success');
        exit();

    }else{ # error while fetching result in row
        mysqli_stmt_close($stmt);
        mysqli_close($con);
        header('Location: signup.php?alert=sql-error&l=58');
        exit();
    }
}else{ # enter site without email
    header('Location: ../index.php?alert=donoteventry&p=mail');
    exit();
}
