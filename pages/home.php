<!doctype html>
<html lang="en">

<?php include_once('includes/head.inc.php'); ?>

<body>
    <?php include_once('includes/header.inc.php'); ?>

    <section class="container py-2 mb-4"> <!-- Start Main Page -->
        <div class="row">

            <div class="col-xl-2"></div>

            <div class="col-xl-8">
                <form class="form-inline mb-3 float-right" method="GET" action="">
                    <input class="form-control mr-2" name="query" type="search" placeholder="Search..." aria-label="Search">
                    <button class="btn btn-primary my-2" type="submit">Search</button>
                </form>
            </div>

            <div class="col-xl-2"></div>

            <div class="col-xl-2"></div>

            <div class="col-xl-8" style=""> <!-- Start Main Container -->

                <?php
                // check if GET is set
		$query = null;
                if (isset($_GET['query'])){ $query = mysqli_real_escape_string($con, $_GET['query']); }

                echo showPostPrev($con, $query);
                ?>

            </div> <!-- End Main Container -->

            <div class="col-xl-2">  <!-- Start Secondary Conntainer -->
                <div class="card">
                    <div class="card-body">

                        <h5>Categories</h5>
                        <?php echo showCategories($con); ?>

                    </div>
                </div>
            </div> <!-- End Secondary Conntainer -->

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
