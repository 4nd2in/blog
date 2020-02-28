<!doctype html>
<html lang="en">
<?php include_once('includes/head.inc.php'); ?>
<?php
function getAction(){
    if (isset($_GET['username']) && isset($_GET['email']) && isset($_GET['token'])){
        $username =  $_GET['username'];
        $email = $_GET['email'];
        $token = $_GET['token'];
    }
    return "action=\"includes/reset-password.inc.php?username=$username&email=$email&token=$token\"";
}
?>
<!-- PasswordStrengthMeter -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.2.0/zxcvbn.js"></script>
<body>
    <section class="container py-2"> <!-- Start Main Page -->
        <div class="row">
            <div class="offset-lg-3 col-lg-6" style="margin-top: 15%;"> <!-- Start Main Container -->
                <div class="card mb-3">

                    <div class="card-header">
                        <h1><i class="fas fa-redo-alt"></i> RESET PASSWORD</h1>
                    </div>

                    <div class="card-body">
                        <form class="" method="POST" <?php  echo getAction();?> >

                            <div class="form-group">
                                <label>New Password</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    </div>
                                    <input class="form-control" type="password" id="password" name="newPassword" placeholder="New Password..." min="10" required/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Confirm New Password</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    </div>
                                    <input class="form-control" type="password" name="confNewPassword" placeholder="Confirm new Password..." min="10" required/>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <meter max="4" id="password-strength-meter"></meter>
                                <p id="password-strength-text"></p>
                            </div>

                            <!-- PasswordStrengthMeter -->
                            <script src="../js/pw-strength.js"></script>

                            <div class="form-group mt-5">
                                <button type="submit" name="reset-pw" class="btn btn-primary btn-block">
                                    <i class="fas fa-check"></i> RESET PASSWORD
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div> <!-- End Main Container -->
        </div>
    </section> <!-- End Main Page -->

    <script>
    $('#year').text(new Date().getFullYear());
    </script>

    <!-- Sweet Alert, PasswordStrengthMeter, Fontawesome -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.2.0/zxcvbn.js"></script>
    <script src="https://kit.fontawesome.com/d205ad48cb.js"></script>

    <?php $con->close(); ?>
</body>
</html>
<?php
// SHOWING ERRORS
if (isset($_GET['alert'])){
    $alertStatus = $_GET['alert'];
    echo showAlert($alertStatus);
}
?>
