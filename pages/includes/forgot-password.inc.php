<?php
/* ------------------------------------------------------------------
Script that send a mail to the user with a link to the password reset

Author:     Andrin Weiler, IMS
Date:       2019-12-26

History:
Version    	Date            Changes			        Changer
1.0        	2020-02-13      Creation    	        Andrin

Copyright ©2019 Andrin Weiler, Switzerland. All rights reserved.
------------------------------------------------------------------ */

use PHPMailer\PHPMailer\PHPMailer;
require_once('../../classes/phpMailer/PHPMailer.php');
require_once('../../classes/phpMailer/SMTP.php');
require_once('../../classes/phpMailer/Exception.php');

include_once('db-con.inc.php');
include_once('functions.inc.php');

if (isset($_POST['forgot-pw'])){

    // VAR
    if (isset($_POST['email'])){
        $username = mysqli_real_escape_string($con, $_POST['username']);
        $email = mysqli_real_escape_string($con, $_POST['email']);
    }else{
        header("Location: ../forgot-password.php?alert=empty");
        exit();
    }

    // SETTING AND UPDATING TOKEN
    $token = str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890");
    $token = substr($token, 0, 16);

    $sql = "UPDATE TUsers SET UserToken = ? WHERE UserUsername = ?";
    $stmt = mysqli_stmt_init($con);
    if (!mysqli_stmt_prepare($stmt, $sql)){
        header("Location: ../forgot-password.php?alert=sql-error");
        exit();
    }
    mysqli_stmt_bind_param($stmt, 'ss', $token, $username);
    if (!mysqli_stmt_execute($stmt)){
        header("Location: ../forgot-password.php?alert=sql-error");
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
    $mail->Subject = 'Blog - Password Reset';
    $mail->Body = "
    Hey $username!<br /><br />
    Here is your password reset Email. To set a new password
    <a href='https://4nd2in.ch/pages/reset-password.php?username=$username&email=$email&token=$token'>click here</a>.<br /><br />
    Thanks,<br />
    4nd2in
    ";
    $mail->AltBody = "Hey $username!
        Here is your password reset Email. To set a new password click here:
        https://4nd2in.ch/pages/reset-password.php?username=$username&email=$email&token=$token
        Thanks, 4nd2in";

    if ($mail->send()){
        mysqli_stmt_close($stmt);
        mysqli_close($con);
        header('Location: ../signin.php?alert=forgot-pw-success');
        exit();
    }else{ # email not sent
        mysqli_stmt_close($stmt);
        mysqli_close($con);
        header("Location: ../forgot-password.php?alert=email-not-sent");
        exit();
    }

}else{ # enter site without sign in button
    header('Location: ../../index.php?alert=donoteventry&p=for');
    exit();
}
