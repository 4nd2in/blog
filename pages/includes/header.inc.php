<div class="horizontal-line"></div>

<nav class="navbar navbar-expand-lg navbar-light"> <!-- Start Navigation -->

    <div class="container"> <!-- Start Container -->
        <a class="navbar-brand" href="home.php">
            <img src="../img/logo-dark.png" alt="logo" width="70px"/>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarTogglerDemo02"> <!-- Start Toggle -->
            <?php
            include_once('includes/db-con.inc.php');
            if (isset($_SESSION['user']) && isset($_SESSION['token'])){ # user is signed in
                $username = $_SESSION['user'];
                $token = $_SESSION['token'];

                $user = createClient($con, $username);

                // check if sign in is valid
                if($user == false || empty($user)){
                    header('Location: signin.php?alert=user-object-not-created');
                    exit();
                }

                if($user->getToken() != $token){
                    header('Location: ../index.php?alert=invalid-signin');
                    exit();
                }

                if ($user->getGroup() == "User"){ # user navigation HOME, CONTACT, ABOUT, PROFILE, SIGN OUT
                    echo('
                    <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                        <li class="nav-item"><a class="nav-link" href="home.php">HOME</a></li>
                        <li class="nav-item"><a class="nav-link" href="contact.php">CONTACT</a></li>
                        <li class="nav-item"><a class="nav-link" href="about.php">ABOUT</a></li>
                        <li class="nav-item"><a class="nav-link" href="profile.php">PROFILE</a></li>
                    </ul>
                    <span class="navbar-text">
                        <form class="form-inline" action="signout.php" method="POST">
                            <button class="btn btn-link" id="signout" type="submit" name="signout">SIGN OUT <i class="fas fa-sign-out-alt"></i></button>
                        </form>
                    </span>
                    ');
                }

                if ($user->getGroup() == "BlogAdmin"){ # admin navigation HOME, CATEGORIES, COMMENTS, ADMINS (disabled), CONTACT, ABOUT, PROFILE, SIGN OUT
                    echo('
                    <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                        <li class="nav-item"><a class="nav-link" href="admin-home.php">HOME</a></li>
                        <li class="nav-item"><a class="nav-link" href="post-add.php">ADD POST</a></li>
                        <li class="nav-item"><a class="nav-link" href="comments.php">COMMENTS</a></li>
                        <li class="nav-item"><a class="nav-link" href="categories.php">CATEGORIES</a></li>
                        <li class="nav-item"><a class="nav-link" href="contact.php">CONTACT</a></li>
                        <li class="nav-item"><a class="nav-link" href="about.php">ABOUT</a></li>
                        <li class="nav-item"><a class="nav-link" href="profile.php">PROFILE</a></li>
                    </ul>
                    <span class="navbar-text">
                        <form class="form-inline" action="signout.php" method="POST">
                            <button class="btn btn-link" id="signout" type="submit" name="signout">SIGN OUT <i class="fas fa-sign-out-alt"></i></button>
                        </form>
                    </span>
                    ');
                }

                if ($user->getGroup() == "Owner"){ # admin navigation HOME, ADD, POST, COMMENTS, CATEGORIES, ADMINS, CONTACT, ABOUT, PROFILE, SIGN OUT
                    echo('
                    <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                        <li class="nav-item"><a class="nav-link" href="admin-home.php">HOME</a></li>
                        <li class="nav-item"><a class="nav-link" href="post-add.php">ADD POST</a></li>
                        <li class="nav-item"><a class="nav-link" href="comments.php">COMMENTS</a></li>
                        <li class="nav-item"><a class="nav-link" href="categories.php">CATEGORIES</a></li>
                        <li class="nav-item"><a class="nav-link" href="admins.php">ADMINS</a></li>
                        <li class="nav-item"><a class="nav-link" href="contact.php">CONTACT</a></li>
                        <li class="nav-item"><a class="nav-link" href="about.php">ABOUT</a></li>
                        <li class="nav-item"><a class="nav-link" href="profile.php">PROFILE</a></li>
                    </ul>
                    <span class="navbar-text">
                        <form class="form-inline" action="signout.php" method="POST">
                            <button class="btn btn-link" id="signout" type="submit" name="signout">SIGN OUT <i class="fas fa-sign-out-alt"></i></button>
                        </form>
                    </span>
                    ');
                }
            }else{ # user is not signed in
                echo('
                <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                    <li class="nav-item"><a class="nav-link" href="home.php">HOME</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">CONTACT</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">ABOUT</a></li>
                </ul>
                <span class="navbar-text">
                    <a href="signin.php">SIGN IN <i class="fas fa-sign-in-alt"></i></a>
                </span>
                <span class="mx-3"></span>
                <span class="navbar-text">
                    <a href="signup.php">SIGN UP <i class="fas fa-user-plus"></i></a>
                </span>
                ');
            }
            ?>
        </div> <!-- End Toggle -->

    </div> <!-- End Container -->

</nav> <!-- End Navigation -->
<div class="horizontal-line"></div>
