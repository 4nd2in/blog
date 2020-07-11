<?php
/* ------------------------------------------------------------------
Script that sign in the user after clicking on sign in button.

Author:     Andrin Weiler, IMS
Date:       2019-12-26

History:
Version    	Date            Changes			        Changer
1.5         2020-02-08      added cost, pepper      Andrin
1.4         2020-01-11      Signin Update           Andrin
1.3         2020-01-09      bug fix and CC          Andrin
1.2         2019-12-31      password_needs_rehash   Andrin
1.1         2019-12-27      Added different         Andrin
                            classes for signin
1.0        	2019-12-26      Creation    	        Andrin

Copyright ©2019 Andrin Weiler, Switzerland. All rights reserved.
------------------------------------------------------------------ */

include_once('db-con.inc.php');
include_once('functions.inc.php');
include_once('../../classes/CClient.class.php');
include_once('../../classes/CPost.class.php');
session_start();

// check if already signed in
if (isset($_SESSION['user'])){
    header('Location: ../home.php?alert=already-signed-in');
    exit();
}

if (isset($_POST['signin'])){

    // VAR
    if (isset($_POST['username']) && isset($_POST['password'])){
        $username = mysqli_real_escape_string($con, $_POST['username']); # reduces sql injection vulnerabilities
        $password = mysqli_real_escape_string($con, $_POST['password']);
    }else{
        header('Location: ../signin.php?alert=empty');
        exit();
    }

    // check if username and password match
    $sql = "SELECT * FROM TUsers WHERE UserUsername = ?";
    $stmt = mysqli_stmt_init($con);

    if (mysqli_stmt_prepare($stmt, $sql)){
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }else{
        $stmt->close();
        mysqli_stmt_close($stmt);
        header('Location: ../signin.php?alert=sql-error');
        exit();
    }

    // if no results -> send back to signin
    if($result->num_rows < 1){
        mysqli_stmt_close($stmt);
        header('Location: ../signin.php?alert=signin-error');
        exit();
    }

    // get data from DB in $row
    if ($row = mysqli_fetch_assoc($result)){

        // check if password matches
        if (password_verify($password . PEPPER, $row['UserPassword'])){

            // check if email is confirmed
            if ($row['UserEmail'] == null){
                mysqli_stmt_close($stmt);
                header('Location: ../signin.php?alert=email-not-confirmed');
                exit();
            }

            // check if user is deleted
            if ($row['UserIsDeleted'] == 1){
                mysqli_stmt_close($stmt);
                header('Location: ../signin.php?alert=user-deleted');
                exit();
            }

            // SIGN IN
            $user = createClient($con, $username);

            if ($user == false){
                header('Location: ../signin.php?alert=user-object-not-created');
                exit();
            }

            // set temporary sign in token to check, if user signed in just now
            $token = str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ");
            $token = substr($token, 0, 8);

            if($user->setToken($con, $token) == false){
                header('Location: ../signin.php?alert=sql-error&m=token-not-set');
                exit();
            }

            $hash = $row['UserPassword'];
            $options = ['cost' => COST];

            if (password_needs_rehash($hash, PASSWORD_DEFAULT, $options)){
                $hashedPassword = password_hash($password . PEPPER, PASSWORD_DEFAULT, $options);
                $password = null;
                $user->setPassword($con, $hashedPassword);
            }

            $_SESSION['user'] = $user->getUsername();
            $_SESSION['token'] = $user->getToken();
            $_SESSION['expire'] = time() + (60 * 60); # time of creation plus time in seconds
            mysqli_stmt_close($stmt);

            header('Location: ../../index.php');
            exit();

        }else{ # wrong password
            mysqli_stmt_close($stmt);
            header('Location: ../signin.php?alert=signin-error');
            exit();
        }
    }else{ # error while fetching result in row
        mysqli_stmt_close($stmt);
        header('Location: ../signin.php?alert=sql-error');
        exit();
    }
}else{ # enter site without sign in button
    header('Location: ../../index.php?alert=donoteventry&p=in');
    exit();
}
