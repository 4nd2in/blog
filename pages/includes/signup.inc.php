<?php
/* ------------------------------------------------------------------
Script that sign up the user after clicking on sign up button.

Author:     Andrin Weiler, IMS
Date:       2019-12-26

History:
Version    	Date            Changes			 Changer
1.7         2020-02-13      CC               Andrin
1.6         2020-02-08      add cost, pepper Andrin
1.5         2020-02-01      UX improoved     Andrin
1.4         2020-01-11      Signin Update    Andrin
1.3         2020-01-09      CC added SMTP    Andrin
1.2         2019-12-29      CC pw check      Andrin
1.1         2019-12-27      EMail taken      Andrin
1.0        	2019-12-26      Creation    	 Andrin

Copyright ©2019 Andrin Weiler, Switzerland. All rights reserved.
------------------------------------------------------------------ */

use PHPMailer\PHPMailer\PHPMailer;
require_once('../../classes/phpMailer/PHPMailer.php');
require_once('../../classes/phpMailer/SMTP.php');
require_once('../../classes/phpMailer/Exception.php');

include_once('db-con.inc.php');
include_once('functions.inc.php');
include_once('../../classes/CClient.class.php');
include_once('../../classes/CPost.class.php');
session_start();

// check if already signed in
if (isset($_SESSION['user'])){
    header('Location: ../../index.php?alert=already-signed-in');
    exit();
}

if (isset($_POST['signup'])){

    // VAR
    if (isset($_POST['email']) && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['confPassword'])){
        $email = mysqli_real_escape_string($con, $_POST['email']);
        $username = mysqli_real_escape_string($con, $_POST['username']);
        $password = mysqli_real_escape_string($con, $_POST['password']);
        $confPassword = mysqli_real_escape_string($con, $_POST['confPassword']);
        $group = "User"; # default group
        $isDeleted = 0;
    }else{
        mysqli_close($con);
        header('Location: ../signup.php?alert=empty');
        exit();
    }

    // ERROR HANDLING

    // check if passwords are equal
    if ($password != $confPassword){
        mysqli_close($con);
        header("Location: ../signup.php?alert=pw-not-equal&email=$email&username=$username");
        exit();
    }

    $confPassword = null;

    // check if password is one of the most used passwords
    $file = fopen("../../../../data/4nd2in/common-passwd.txt", "r");
    while (!feof($file)){
        $result = fgets($file);
        if ($password == $result){
            mysqli_close($con);
            header("Location: ../signup.php?alert=pw-unsafe&email=$email&username=$username");
            exit();
        }
    }
    fclose($file);

    // check if password contains at least 1 number, 1 special character, 1 uppercase letter, 1 lowercase letter and has the right length
    $number = preg_match('@[0-9]@', $password);
    $specialCharacter = preg_match('@[^\w]@', $password);
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);

    if (!$number || !$specialCharacter || !$uppercase || !$lowercase || strlen($password) < 10){
        mysqli_close($con);
        header("Location: ../signup.php?alert=pw-validation-failed&email=$email&username=$username");
        exit();
    }

    // check length of username
    if (strlen($username) > 45){
        mysqli_close($con);
        header("Location: ../signup.php?alert=user-length-wrong&email=$email");
        exit();
    }

    // check if email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
        mysqli_close($con);
        header("Location: signup.php?alert=email-invalid&username=$username");
        exit();
    }

    // check if user already exists
    $sql = "SELECT * FROM TUsers WHERE UserUsername = ?";
    $stmt = mysqli_stmt_init($con);

    if (mysqli_stmt_prepare($stmt, $sql)){
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }else{
        mysqli_stmt_close($stmt);
        mysqli_close($con);
        header("Location: ../signup.php?alert=sql-error&l=96&&email=$email&username=$username");
        exit();
    }

    if(mysqli_num_rows($result) > 0){
        mysqli_stmt_close($stmt);
        mysqli_close($con);
        header("Location: ../signup.php?alert=user-taken&email=$email");
        exit();
    }

    // check if email already exists
    $sql = "SELECT * FROM TUsers WHERE UserEmail = ?";
    $stmt = mysqli_stmt_init($con);

    if (mysqli_stmt_prepare($stmt, $sql)){
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }else{ # error while preparing stmt
        mysqli_stmt_close($stmt);
        mysqli_close($con);
        header("Location: ../signup.php?alert=sql-error&l=118&email=$email&username=$username");
        exit();
    }

    if(mysqli_num_rows($result) > 0){
        if($row = mysqli_fetch_assoc($result)){ # email must be unique -> just one row
            $username = $row['UserUsername']; # update username which matches with email

            // CREATE TOKEN for password reset
            $token = str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890");
            $token = substr($token, 0, 16);

            // UPDATE TOKEN
            $sql = "UPDATE TUsers SET UserToken = ? WHERE UserUsername = ?";
            $stmt = mysqli_stmt_init($con);

            if (mysqli_stmt_prepare($stmt, $sql)){
                mysqli_stmt_bind_param($stmt, 'ss', $token, $username);
                mysqli_stmt_execute($stmt);
            }else{ # error while preparing stmt
                mysqli_stmt_close($stmt);
                mysqli_close($con);
                header("Location: ../signup.php?alert=sql-error&l=141&email=$email&username=$username");
                exit();
            }

            // SENDING PASSWORD RESET MAIL

            $mail = new PHPMailer();

            // server settings
            $mail->isSMTP();
            $mail->Host = MAILHOST;
            $mail->Port = 587;
            $mail->SMTPAuth = TRUE;
            $mail->SMTPSecure = 'tls';
            $mail->Username = 'noreply@4nd2in.ch';
            $mail->Password = MAILPASSWD;

            // recipients
            $mail->setFrom('noreply@4nd2in.ch', '4nd2in');
            $mail->addAddress($email, $username);
            $mail->addReplyTo('info@4nd2in.ch', 'Information');

            // content
            $mail->isHTML(true);
            $mail->Subject = 'Blog - Your EMail is already taken';
            $mail->Body = "
            Hey $username!<br /><br />
            Your EMail is already signed up. If you forgot your password
            <a href='https://4nd2in.ch/pages/reset-password.php?username=$username&email=$email&token=$token'>click here</a><br /> to reset it.<br />
            Thanks,<br />
            4nd2in
            ";
            $mail->AltBody = "Hey $username!
                Your EMail is already signed up. If you forgot your password reset it here:
                https://4nd2in.ch/pages/reset-password.php?username=$username&email=$email&token=$token
                Thanks, 4nd2in";

            if ($mail->send()){
                mysqli_stmt_close($stmt);
                mysqli_close($con);
                header('Location: ../signup.php?alert=signup-success');
                exit();
            }else{ # email not sent
                mysqli_stmt_close($stmt);
                mysqli_close($con);
                header("Location: ../signup.php?alert=email-not-sent&l=176&email=$email&username=$username");
                exit();
            }

        }else{ # error while fetching result in row
            mysqli_stmt_close($stmt);
            mysqli_close($con);
            header("Location: ../signup.php?alert=sql-error&l=183&email=$email&username=$username");
            exit();
        }
        exit();
    }

    // HASHING PASSWORD
    $options = ['cost' => COST];
    $hashedPassword = password_hash($password . PEPPER, PASSWORD_DEFAULT, $options);
    $password = null;

    // CREATE TOKEN for email validation
    $token = str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890");
    $token = substr($token, 0, 16);

    // INSERT USER without email -> must be validated
    $sql = "INSERT INTO TUsers (UserUsername, UserPassword, UserToken, UserGroup, UserIsDeleted) VALUES(?, ?, ?, ?, ?)";
    $stmt = mysqli_stmt_init($con);

    if (mysqli_stmt_prepare($stmt, $sql)){

        // SEND VALIDATION MAIL
        $mail = new PHPMailer();

        // server settings
        $mail->isSMTP();
        $mail->Host = MAILHOST;
        $mail->Port = 587;
        $mail->SMTPAuth = TRUE;
        $mail->SMTPSecure = 'tls';
        $mail->Username = 'noreply@4nd2in.ch';
        $mail->Password = MAILPASSWD;

        // recipients
        $mail->setFrom('noreply@4nd2in.ch', '4nd2in');
        $mail->addAddress($email, $username);
        $mail->addReplyTo('info@4nd2in.ch', 'Information');

        // content
        $mail->isHTML(true);
        $mail->Subject = 'Blog - Please verify your EMail';
        $mail->Body = "
        Hey $username!<br /><br />
        Please <a href='https://4nd2in.ch/pages/email-validation.php?username=$username&email=$email&token=$token'>Click Here</a> to verify your EMail.<br /><br />
        Thanks,<br />
        4nd2in
        ";
        $mail->AltBody = "Hey $username!
        Please click on the link below to verify your EMail:
        https://4nd2in.ch/pages/email-validation.php?username=$username&email=$email&token=$token
        Thanks, 4nd2in";

        if ($mail->send()){ # email sent -> insert user
            mysqli_stmt_bind_param($stmt, "ssssi", $username, $hashedPassword, $token, $group, $isDeleted);
            mysqli_stmt_execute($stmt);
        }else{ # email not sent
            mysqli_stmt_close($stmt);
            mysqli_close($con);
            header("Location: ../signup.php?alert=email-not-sent&email=$email&username=$username");
            exit();
        }

        mysqli_stmt_close($stmt);
        mysqli_close($con);

        header('Location: ../signin.php?alert=signup-success'); # success
        exit();
    }else{ # sql error
        mysqli_stmt_close($stmt);
        mysqli_close($con);
        header("Location: ../signup.php?alert=sql-error&l=243&email=$email&username=$username");
        exit();
    }
}else{ # enter page without sign up
    mysqli_close($con);
    header('Location: ../../index.php?alert=donoteventry&p=up');
    exit();
}
