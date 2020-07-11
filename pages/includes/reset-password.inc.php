<?php
/* ------------------------------------------------------------------
Script that resets the password of a user after correct inputs.

Author:     Andrin Weiler, IMS
Date:       2019-12-27

History:
Version    	Date            Changes			 Changer
1.1         2020-02-06      Bug and
                            vulnerbility fix Andrin
1.0        	2019-12-27      Creation    	 Andrin

Copyright ©2019 Andrin Weiler, Switzerland. All rights reserved.
------------------------------------------------------------------ */

include_once('db-con.inc.php');
include_once('functions.inc.php');
include_once('../../classes/CClient.class.php');
session_start();

if (isset($_POST['reset-pw'])){

    // VAR
    if (isset($_GET['username']) && isset($_GET['email']) && isset($_GET['token'])){
        $username = mysqli_real_escape_string($con, $_GET['username']);
        $email = mysqli_real_escape_string($con, $_GET['email']);
        $token = mysqli_real_escape_string($con, $_GET['token']);
    }else{
        header("Location: ../reset-password.php?alert=empty&msg=wrong-get");
        exit();
    }

    if (isset($_POST['newPassword']) && isset($_POST['confNewPassword'])){
        $newPw = mysqli_real_escape_string($con, $_POST['newPassword']);
        $confNewPw = mysqli_real_escape_string($con, $_POST['confNewPassword']);

    }else{ # empty fields -> not best UX because user do not set the GET attributes
        header("Location: ../reset-password.php?alert=empty&email=$email&username=$username&token=$token");
        exit();
    }

    // ERROR HANDLING

    // check if user exists
    $sql = "SELECT * FROM TUsers WHERE UserUsername = ?";
    $stmt = mysqli_stmt_init($con);

    if (mysqli_stmt_prepare($stmt, $sql)){
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }else{
        header("Location: ../reset-password.php?alert=sql-error&l=42&email=$email&username=$username&token=$token");
        exit();
    }

    if ($row = mysqli_fetch_assoc($result)){ # result must contain one row
        $user = createClient($con, $username);

        // check if passwords are equal
        if ($newPw == $confNewPw){
            $confNewPw = null;

            // check if password is one of the most used passwords
            $file = fopen("../../../../data/4nd2in/common-passwd.txt", "r");
            while (!feof($file)){
                $result = fgets($file);
                if ($newPw == $result){
                    header("Location: ../reset-password.php?alert=pw-unsafe&email=$email&username=$username&token=$token");
                    exit();
                }
            }
            fclose($file);

            // check if password contains at least 1 number, 1 special character, 1 uppercase letter, 1 lowercase letter and has the right length
            $number = preg_match('@[0-9]@', $newPw);
            $specialCharacter = preg_match('@[^\w]@', $newPw);
            $uppercase = preg_match('@[A-Z]@', $newPw);
            $lowercase = preg_match('@[a-z]@', $newPw);

            if (!$number || !$specialCharacter || !$uppercase || !$lowercase || strlen($newPw) < 10){
                header("Location: ../reset-password.php?alert=pw-unsafe&email=$email&username=$username&token=$token");
                exit();
            }

            // HASH PASSWORD
            $options = ['cost' => COST];
            $hashedPassword = password_hash($newPw . PEPPER, PASSWORD_DEFAULT, $options);
            $newPw = null;

            // SET NEW PROPERTIES
            $user->setPassword($con, $hashedPassword);
            $user->setToken($con, null);

            // SEND TO SIGN IN
            mysqli_stmt_close($stmt);
            header('Location: ../signin.php?alert=pw-reset-success');
            exit();
        }else{ # passwords not equal
            mysqli_stmt_close($stmt);
            header("Location: ../reset-password.php?alert=pw-not-equal&email=$email&username=$username&token=$token");
            exit();
        }
    }else{ # error while fetching result in row
        mysqli_stmt_close($stmt);
        header("Location: ../reset-password.php?alert=sql-error&l=120&email=$email&username=$username&token=$token");
        exit();
    }
}else{ # enter site without email
    header('Location: ../../index.php?alert=donoteventry&p=pw');
    exit();
}
