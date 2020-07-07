<!doctype html>
<html lang="en">

<?php include_once('includes/head.inc.php'); ?>

<body>
    <?php include_once('includes/header.inc.php'); ?>

    <section class="container py-2 mb-4"> <!-- Start Main Page -->
        <div class="row">
            <div class="offset-lg-3 col-lg-6" style="margin-top: 7%;"> <!-- Start Main Container -->
                <div class="card mb-3">

                    <div class="card-header">
                        <h1><i class="fas fa-sign-in-alt"></i> Sign In</h1>
                    </div>

                    <div class="card-body">
                        <form class="" method="POST" action="includes/signin.inc.php" autocomplete="on">

                            <div class="form-group">
                                <label>Username</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input class="form-control" type="text" name="username" placeholder="Darth Vader..." required/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Password</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    </div>
                                    <input class="form-control" type="password" name="password" placeholder="Password..." required/>
                                </div>
                            </div>

                            <div class="form-group">
                                <p>Forgot your password? <a href="forgot-password.php">Click Here!</i></a></p>
                            </div>

                            <div class="form-group mt-5">
                                <button type="submit" name="signin" class="btn btn-primary btn-block">
                                    <i class="fas fa-check"></i> Sign In
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
                <div class="col text-center">
                    <p>Don't have an account? <a href="signup.php">Sign Up <i class="fas fa-user-plus"></i></a></p>
                </div>
            </div> <!-- End Main Container -->
        </div>
    </section> <!-- End Main Page -->
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
