<!doctype html>
<html lang="en">

<?php include_once('includes/head.inc.php'); ?>

<body>
    <?php include_once('includes/header.inc.php'); ?>

    <section class="container py-2 mb-4"> <!-- Start Main Page -->
        <div class="row">
            <div class="offset-lg-2 col-lg-8" style=""> <!-- Start Main Container -->

                <h1><i class="fas fa-clipboard"></i> My Posts</h1><hr />
                <!-- TODO: confirmation for deletion -->
                <?php echo showPostUser($con, $_SESSION['user']); ?>

            </div> <!-- End Main Container -->
        </div>
    </section> <!-- End Main Page -->
    <?php include_once('includes/footer.inc.php'); ?>

    <script>
        // add fluid class to all images
        $(document).ready(function(){
            $("img").addClass("img-fluid");
        });
    </script>
</body>
</html>
