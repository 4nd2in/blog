<!doctype html>
<html lang="en">
<?php include_once('includes/head.inc.php'); ?>

<!-- PasswordStrengthMeter -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.2.0/zxcvbn.js"></script>
<body>
    <?php include_once('includes/header.inc.php'); ?>

    <section class="container py-2 mb-4"> <!-- Start Main Page -->
        <div class="row">
            <div class="offset-lg-3 col-lg-6" style="margin-top: 7%;"> <!-- Start Main Container -->
                <div class="card mb-3">

                    <div class="card-header">
                        <h1><i class="fas fa-user-plus"></i> Sign Up</h1>
                    </div>

                    <div class="card-body">
                        <form id="signupForm" class="" method="POST" action="includes/signup.inc.php" autocomplete="on">

                            <!-- EMAIL -->
                            <div class="form-group">
                                <label>EMail</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    </div>
                                    <input class="form-control" type="email" name="email" placeholder="darth.vader@dark.side..." required
                                    <?php
                                    if (isset($_GET['email'])){
                                        $email = $_GET['email'];
                                        echo "value='$email'";
                                    }
                                    ?>
                                    />
                                </div>
                            </div>

                            <!-- USERNAME -->
                            <div class="form-group">
                                <label>Username</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input class="form-control" type="text" name="username" placeholder="Darth Vader..." max="45" required
                                    <?php
                                    if (isset($_GET['username'])){
                                        $username = $_GET['username'];
                                        echo "value='$username'";
                                    }
                                    ?>
                                    />
                                </div>
                            </div>

                            <!-- PASSWORD -->
                            <div class="form-group">
                                <label>Password </label><span class="ml-1" data-toggle="tooltip" data-placement="top" title="length of 10, 1 special character, 1 number, 1 uppercase and 1 lowercase letter"><i class="far fa-question-circle"></i></span>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-key"></i>
                                        </span>
                                    </div>
                                    <input class="form-control" type="password" id="password" name="password" placeholder="Password..." min="10" required/>
                                </div>
                            </div>

                            <!-- CONFIRM PASSWORD -->
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-key"></i>
                                        </span>
                                    </div>
                                    <input class="form-control" type="password" id="confPassword" name="confPassword" placeholder="Confirm Password..." min="10" required/>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <p id="confTxt"></p>
                            </div>

                            <!-- PasswordStrengthMeter -->
                            <div class="form-group mt-4">
                                <meter max="4" id="password-strength-meter"></meter>
                                <p id="password-strength-text"></p>
                            </div>

                            <script src="../js/pw-strength.js"></script>

                            <div class="form-group mt-5">
                                <button type="submit" name="signup" class="btn btn-primary btn-block">
                                    <i class="fas fa-check"></i> Sign Up
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
                <div class="col text-center">
                    <p>Already have an account? <a href="signin.php">Sign In <i class="fas fa-sign-in-alt"></i></a></p>
                </div>
            </div> <!-- End Main Container -->
        </div>
    </section> <!-- End Main Page -->

    <script>
    // UX Check if passwords are equal
    jQuery('#confPassword').keyup(async function() {
        if ($('#password').val() == $('#confPassword').val()) {
            $('#confTxt').text(null);
            return true;
        }else{
            $('#confTxt').text('Passwords must be equal!').css('color', 'red');
            return false;
        }
    });
    </script>

    <!-- Sweet Alert -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

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
