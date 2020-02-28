<!doctype html>
<html lang="en">

<?php include_once('includes/head.inc.php'); ?>

<body>
    <?php include_once('includes/header.inc.php'); ?>

    <section class="container py-2 mb-4"> <!-- Start Main Page -->
        <div class="row">
            <div class="offset-lg-2 col-lg-8" style=""> <!-- Start Main Container -->

                <?php
                $id = $_GET['id'];
                if (isset($id)){
                    $post = createPost($con, $id);
                    echo showPostFull($con, $post);
                }

                echo '<div class="card border-0"><div class="card-body"><hr />';

                $c = 0;
                foreach ($post->getComments() as $comment) {
                    $c++;
                }

                echo "<h4>$c Comments</h4>";

                if (isset($_SESSION['user'])){ #show comment function if user is signed in
                    echo '
                    <form class="mb-2" method="POST" action="includes/post.inc.php?id=' . $id . '">
                        <div class="form-group">
                            <div class="input-group">
                                <textarea type="text" class="form-control" name="content" max="5000" placeholder="I believe in Darth Jar Jar... "></textarea>
                            </div>
                            <div class="d-flex flex-row-reverse mt-2">
                                <button class="btn btn-primary" type="submit" name="comment" id="comment" disabled>Comment</button>
                                <button class="mr-2 btn btn-outline-info" id="cancel" type="button">Cancel</button>
                            </div>
                        </div>
                    </form>';
                }
                echo '<hr />' . showComments($con, $post) . '</div></div>';
                ?>

            </div> <!-- End Main Container -->
        </div>
    </section> <!-- End Main Page -->
    <?php include_once('includes/footer.inc.php'); ?>

    <script>
        $(document).ready(function(){
            // add fluid class to all images
            $("img").addClass("img-fluid");

            // enable/disable submit button
            $("textarea").on("change paste keyup", function(){
                if($("textarea").val().length > 0){
                    $("#comment").prop('disabled', false);
                }else{
                    $("#comment").prop('disabled', true);
                }
            });

            // cancel button -> clear input
            $("#cancel").click(function(){
                $("textarea").val('');
            });
        });
    </script>

</body>
</html>
