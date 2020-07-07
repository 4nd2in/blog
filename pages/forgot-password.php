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
                        <h1><i class="fas fa-sync-alt"></i> FORGOT PASSWORD</h1>
                    </div>

                    <div class="card-body">
                        <form class="" method="POST" action="includes/forgot-password.inc.php" autocomplete="on">

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
                                <label>EMail</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    </div>
                                    <input class="form-control" type="email" name="email" placeholder="darth.vader@dark.side..." required/>
                                </div>
                            </div>

                            <div class="form-group mt-5">
                                <button type="submit" name="forgot-pw" class="btn btn-primary btn-block">
                                    <i class="fas fa-check"></i> SEND EMail
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
                <div class="col text-center">
                    <p>Back to <a href="signin.php">SIGN IN <i class="fas fa-user-plus"></i></a></p>
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
