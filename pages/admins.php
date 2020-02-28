<!doctype html>
<html lang="en">

<?php include_once('includes/head.inc.php'); ?>

<body>
    <?php include_once('includes/header.inc.php'); ?>

    <section class="container py-2 mb-4"> <!-- Start Main Page -->
        <div class="row">
            <div class="offset-lg-2 col-lg-8" style=""> <!-- Start Main Container -->

                <h1><i class="fas fa-user-cog"></i> Manage Users</h1><hr />
                <input class="form-control mb-2" id="userInput" type="text" placeholder="Search..">

                <?php echo showUsers($con); ?>

            </div> <!-- End Main Container -->
        </div>
    </section> <!-- End Main Page -->
    <?php include_once('includes/footer.inc.php'); ?>

    <script>
    $(document).ready(function(){
        // filterable table
        $("#userInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#userTable tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
    </script>
</body>
</html>
