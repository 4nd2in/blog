<!doctype html>
<html lang="en">

<?php include_once('includes/head.inc.php'); ?>

<?php
if (isset($_SESSION['user']) && isset($_SESSION['token'])){ # check if user is signed in
    $username = $_SESSION['user'];
    $token = $_SESSION['token'];

    $user = createClient($con, $username);

    // check if sign in is valid
    if($user == false){
        header('Location: ../index.php?alert=user-object-not-created');
        exit();
    }

    if($user->getToken() != $token){
        header('Location: ../index.php?alert=invalid-signin');
        exit();
    }

    // check if client has permission
    if($user->getGroup() == "User"){
        header('Location: ../index.php?alert=permission-denied');
        exit();
    }
}
?>

<body>
    <?php include_once('includes/header.inc.php'); ?>

    <section class="container py-2 mb-4"> <!-- Start Main Page -->
        <div class="row">
            <div class="offset-lg-2 col-lg-8"> <!-- Start Main Container -->
                <h1><i class="fas fa-tag"></i> Add Categories</h1>


                <div class="card">
                    <div class="card-body">
                        <form class="" method="POST" action="includes/categories.inc.php">

                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                    </div>
                                    <input class="form-control" type="text" name="cat" placeholder="Star Wars..." required />
                                </div>
                            </div>

                            <div class="mt-2 mb-4" style="min-height: 40px;">
                                <button type="button" id="cancel" class="btn btn-outline-info float-left" style="width: 40%;">Cancel</button>
                                <button type="submit" name="add" id="add" class="btn btn-primary float-right" style="width: 40%;" disabled><i class="fas fa-check"></i> Add</button>
                            </div>

                        </form>
                        
                        <form class="" method="POST" action="includes/categories.inc.php">
                            <div class="form-group">
                                <label>Your Categories:</label>

                                <ul class="list-group">
                                    <?php
                                    // get all categories from signed in user
                                    $categories = $user->getCategories();
                                    foreach($categories as $cat){

                                        if ($cat->getPost() == null){
                                            $name = $cat->getName();
                                            echo "<li class=\"py-1 list-group-item d-flex justify-content-between align-items-center\">"
                                            . $name
                                            . "<span class=\"badge badge-pill\"><button class=\"btn btn-danger\" type=\"submit\" name=\"del\" value=\"$name\"><i class=\"fas fa-trash-alt\"></i></button></span></li>";
                                        }

                                    }
                                    ?>
                                </ul>
                            </div>
                        </form>
                    </div>
                </div>

            </div> <!-- End Main Container -->
        </div>

    </section> <!-- End Main Page -->
    <?php include_once('includes/footer.inc.php'); ?>

    <!-- Sweet Alert -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script>
    $(document).ready(function(){
        // enable/disable submit button
        $("input").on("change paste keyup", function(){
            if($("input").val().length > 0){
                $("#add").prop('disabled', false);
            }else{
                $("#add").prop('disabled', true);
            }
        });

        // cancel button -> clear input
        $("#cancel").click(function(){
            $("input").val('');
        });
    });
</script>

</body>
</html>
<?php
// SHOWING ERRORS
if (isset($_GET['alert'])){
    $alertStatus = $_GET['alert'];
    echo showAlert($alertStatus);
}
?>
