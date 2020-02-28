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
        <div class="offset-2 col-8" style=""> <!-- Start Main Container -->

            <h1><i class="fas fa-edit"></i></i> Add Your Post</h1>

            <div class="card mb-3">

                <div class="card-body">
                    <form id="postAdd" class="" method="POST" action="includes/post-add.inc.php" enctype="multipart/form-data">

                        <!-- STEP 1 / 5 -->
                        <fieldset class="step-field">
                            <!-- categories -->
                            <div class="form-group">
                                <label>Categories</label>
                                <select name="categories[]" id="categories" multiple class="form-control" required>
                                    <?php
                                    // get all categories from signed in user
                                    $categories = $user->getCategories();

                                    if (!empty($categories)){

                                        foreach($categories as $cat){
                                            if ($cat->getPost() == null){
                                                echo("<option value=\"" . $cat->getName() . "\">" . $cat->getName() . "</option>");
                                            }
                                        }

                                    }else{ # error while getting categories
                                        header('Location: post-add.php?alert=sql-error');
                                        exit();
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col text-center">
                                <p>Category not listed? <a href="categories.php">Add Category <i class="fas fa-plus-circle"></i></a></p>
                            </div>

                            <div class="form-group">
                                <button type="button" name="next" class="next btn btn-primary float-right">Next</button>
                            </div>
                        </fieldset>


                        <!-- STEP 2 / 5 -->
                        <fieldset class="step-field">
                            <!-- title -->
                            <div class="form-group">
                                <label>Title</label>
                                <div class="input-group mb-3">
                                    <input class="form-control" type="text" name="title" id="title" placeholder="Star Wars culture..." max="45" required/>
                                </div>
                            </div>

                            <!-- header -->
                            <div class="form-group">
                                <label>Header</label>
                                <div class="input-group mb-3">
                                    <textarea class="form-control" rows="3" type="text" name="header" id="header" placeholder="How the saga changed lives..." max="256" required></textarea>
                                </div>
                            </div>

                            <div class="form-group mt-5">
                                <button type="button" name="prev" class="prev btn btn-outline-info float-left">Previous</button>
                                <button type="button" name="next" class="next btn btn-primary float-right">Next</button>
                            </div>
                        </fieldset>


                        <!-- STEP 3 / 5 -->
                        <fieldset class="step-field">
                            <!-- banner -->
                            <div class="form-group">
                                <label>Banner</label><span class="ml-1" data-toggle="tooltip" data-placement="top" title="The banner is shown on the post's preview"><i class="far fa-question-circle"></i></span>
                                <div class="input-group mb-3">
                                    <input class="form-control" type="url" name="banner" id="banner" placeholder="Your link..." required/>
                                </div>
                            </div>

                            <div class="form-group mt-5">
                                <button type="button" name="prev" class="prev btn btn-outline-info float-left">Previous</button>
                                <button type="button" name="next" class="next btn btn-primary float-right">Next</button>
                            </div>
                        </fieldset>

                        <!-- STEP 4 / 5 -->
                        <fieldset class="step-field">
                            <!-- content -->
                            <div class="form-group">
                                <label>Content</label>
                                <div class="input-group mb-3">
                                    <textarea class="form-control" name="content" id="content" placeholder="A long time ago in a galaxy far, far away..." required></textarea>
                                </div>
                            </div>

                            <div class="form-group mt-5">
                                <button type="button" name="prev" class="prev btn btn-outline-info float-left">Previous</button>
                                <button type="button" name="next" id="render" class="next btn btn-primary float-right">Next</button>
                            </div>
                        </fieldset>


                        <!-- STEP 5 / 5 -->
                        <fieldset class="step-field">
                            <div class="card border-0" id="renderPost"></div>
                            <div id="renderCat mt-3"></div>
                            <div>
                                <?php
                                $a = $user->getUsername();
                                $d = date("d.m.Y H:i");
                                echo("<span>Author: <strong>$a</strong></span><br /><span>Date: <strong>$d</strong></span>");
                                ?>
                            </div>
                            <div class="form-group mt-5 float">
                                <button type="button" name="prev" class="prev btn btn-outline-info float-left">Previous</button>
                                <button type="submit" name="postAdd" class="btn btn-primary float-right">Upload</button>
                            </div>
                        </fieldset>

                    </form>
                </div> <!-- End Card Body -->
            </div> <!-- End Card -->
            <!-- Circles as progressbar -->
            <div class="row">
                <div class="offset-4 col-4 d-flex justify-content-between my-3" id="progressbar">
                    <span class="step active"></span>
                    <span class="step"></span>
                    <span class="step"></span>
                    <span class="step"></span>
                    <span class="step"></span>
                </div>
            </div>

        </div> <!-- End Main Container -->
    </div>
</section> <!-- End Main Page -->

<!-- Sweet Alert -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<!-- Multi Step Form -->
<script>
    // SUMMERNOTE
    $(document).ready(function() {
        $('#content').summernote({
            placeholder: 'A long time ago in a galaxy far, far away...',
            minHeight: 300,
            focus: true, // set focus to summernote element after initialize
            toolbar: [ // custom toolbar
                ['misc', ['undo', 'redo']],
                ['style', ['style']],
                ['fontstyle', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['superscript', 'subscript']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table',['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['help']]
            ]
        });
    });

    // PRERENDER
    $('#render').click(function(){
        var obj, cat, t, h, b, c, m, out = true;
        obj = $("#renderPost");
        cat = $('#categories').val();
        t = $('#title').val();
        h = $('#header').val();
        b = $('#banner').val();
        c = $('#content').val();

        out = `
        <div class="border-body border-0">
        <h2>${t}</h2><hr />
        <img class="img-rounded img-fluid" src="${b}"/><hr />
        <h3>${h}</h3>
        <p>${c}</p>
        </div>
        `;

        obj.html(out);

        obj = $("#renderCat");
        out = `<span>Categories: `;

        for(let i = 0; i < cat.length; i++){
            out += `<h5 class="badge badge-secondary mr-1 py-1 px-2">${cat[i]}</h5>`;
        }

        out += `</span><br />`;
        obj.html(out);
    });

    // NAVIGATION
    var current_fs, next_fs, previous_fs; //fieldsets

    // next button
    $(".next").click(function(){
        current_fs = $(this).parent().parent();
        next_fs = $(this).parent().parent().next();

        // activate next step on progressbar using the index of next_fs
        $("#progressbar span").eq($("fieldset").index(next_fs)).addClass("active");

        // show the next fieldset
        next_fs.show();
        next_fs.css("opacity", 1);

        // hide the current fieldset
        current_fs.hide();
        current_fs.css("opacity", 0);
    });

    // previous button
    $(".prev").click(function(){
        current_fs = $(this).parent().parent();
        previous_fs = $(this).parent().parent().prev();

        // de-activate current step on progressbar
        $("#progressbar span").eq($("fieldset").index(current_fs)).removeClass("active");

        // show the previous fieldset
        previous_fs.show();
        previous_fs.css("opacity", 1);

        // hide the current fieldset
        current_fs.hide();
        current_fs.css("opacity", 0);
    });

</script>

<?php
// SHOWING ERRORS
if (isset($_GET['alert'])){
    $alertStatus = $_GET['alert'];
    echo showAlert($alertStatus);
}
$con->close();
?>
</body>
</html>
