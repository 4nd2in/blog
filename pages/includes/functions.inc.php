<?php
/* ------------------------------------------------------------------
Script that includes all needed functions of blog_cms

Author:     Andrin Weiler, IMS
Date:       2019-12-23

History:
Version    	Date            Changes				                 Changer
1.12        2020-02-23      Clickable Images in PostPrev         Andrin
1.11        2020-02-20      beauty update & bug fix              Andrin
1.10        2020-02-18      added createPost                     Andrin
1.9         2020-02-16      beauty update                        Andrin
1.8         2020-02-13      bug fix                              Andrin
1.7         2020-02-11      added showPost                       Andrin
1.6         2020-01-22      CC removed showCategories            Andrin
1.5         2020-01-11      added createClient                   Andrin
1.4         2020-01-03      added showCategories                 Andrin
1.3         2019-12-29      removed pw-length-wrong case         Andrin
1.2         2019-12-27      added reset-pw alerts                Andrin
1.1         2019-12-26      changed login to signin              Andrin
1.0        	2019-12-24      creation (encrypt, decrypt, alert)	 Andrin

Copyright ©2020 Andrin Weiler, Switzerland. All rights reserved.
------------------------------------------------------------------ */

// BACKEND

/**
 * write logs in a file
 * @param string $msg message
 */
function writeLog($msg){
    $file = "../../../../data/4nd2in/log.txt";
    file_put_content($file, file_get_content($file).$msg."\n");
}

/**
 * create a client subclass based on UserGroup
 * @param mysqli $con database connection
 * @param string $username as unique identifier
 * @return CClient user object in right subclass
 */
function createClient($con, $username){
    $sql = "SELECT * FROM TUsers WHERE UserUsername = ?";
    $stmt = mysqli_stmt_init($con);

    if (mysqli_stmt_prepare($stmt, $sql)){
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }else{
        mysqli_stmt_close($stmt);
        return false;
    }

    // check if there is a result -> return false
    if($result->num_rows < 1){
        mysqli_stmt_close($stmt);
        return false;
    }

    // create CClient
    if ($row = mysqli_fetch_assoc($result)){
        if ($row['UserGroup'] == "User"){
            $user = new CUser($con, $row['UserUsername'], $row['UserEmail'], $row['UserFirstname'], $row['UserLastname'], $row['UserPassword'],
                    $row['UserToken'], $row['UserDesc'], $row['UserGroup']);
        }

        if ($row['UserGroup'] == "BlogAdmin"){
            $user = new CBlogAdmin($con, $row['UserUsername'], $row['UserEmail'], $row['UserFirstname'], $row['UserLastname'], $row['UserPassword'],
                    $row['UserToken'], $row['UserDesc'], $row['UserGroup']);
        }

        if ($row['UserGroup'] == "Owner"){
            $user = new COwner($con, $row['UserUsername'], $row['UserEmail'], $row['UserFirstname'], $row['UserLastname'], $row['UserPassword'],
                    $row['UserToken'], $row['UserDesc'], $row['UserGroup']);
        }

        return $user;
    }else{
        mysqli_stmt_close($stmt);
        return false;
    }
}

/**
 * create a client subclass based on UserGroup
 * @param mysqli $con database connection
 * @param string $username as unique identifier
 * @return CClient user object in right subclass
 */
function createPost($con, $id){
    $sql = "SELECT * FROM TPosts WHERE PostId = ?";
    $stmt = mysqli_stmt_init($con);

    if (mysqli_stmt_prepare($stmt, $sql)){
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }else{
        mysqli_stmt_close($stmt);
        return false;
    }

    // check if there is a result -> return false
    if($result->num_rows < 1){
        mysqli_stmt_close($stmt);
        return false;
    }

    // create post
    if ($row = mysqli_fetch_assoc($result)){
        $username = $row['UserUsername'];
        $postId = $row['PostId'];
        $title = $row['PostTitle'];
        $header = $row['PostHeader'];
        $content = html_entity_decode($row['PostContent'], ENT_QUOTES, "UTF-8");
        $date = date("D, d M Y, H:i", strtotime($row['PostDate']));

        $user = createClient($con, $username);
        $post = new CPost($con, $title, $header, $content, $user, $date, $postId);

        mysqli_stmt_close($stmt);
        return $post;

    }else{
        mysqli_stmt_close($stmt);
        return false;
    }
}

/**
 * encrypt a plaintext with given cihper method and key
 * @param string $plaintext to encrypt
 * @param string $cipher method for encryption
 * @param string $key for encrypption (openssl_random_pseudo_bytes)
 * @return string ciphertext iv.hmac.ciphertext_raw in base64
 */
function encryptData($plaintext, $cipher, $key){
    $cipher = strtolower($cipher);
    if (in_array($cipher, openssl_get_cipher_methods())){
        // get length of initialization vector
        $ivlen = openssl_cipher_iv_length($cipher);
        // create pseudorandom initialization vector
        $iv = openssl_random_pseudo_bytes($ivlen);
        // encrypt with openssl
        $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
        // hash encryted data with key
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
        // encode in base64
        $ciphertext = base64_encode($iv.$hmac.$ciphertext_raw);
        return $ciphertext;
    }
}

/**
 * decrypt an encrypted ciphertext
 * @param string $ciphertext to decrypt
 * @param string $cipher method for decryption
 * @param string $key previously generated key for decryption (openssl_random_pseudo_bytes)
 * @return string plaintext
 */
function decryptData($ciphertext, $cipher, $key){
    $cipher = strtolower($cipher);
    if (in_array($cipher, openssl_get_cipher_methods())){
        // decode from base64
        $c = base64_decode($ciphertext);
        // get length initialization vector
        $ivlen = openssl_cipher_iv_length($cipher);
        // get initialization vector of ciphertext
        $iv = substr($c, 0, $ivlen);
        // get hash of ciphertext
        $hmac = substr($c, $ivlen, $sha2len=32);
        // get raw ciphertext
        $ciphertext_raw = substr($c, $ivlen+$sha2len);
        // decrypt ciphertext
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
        // create hash
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
        // check if hash is equal to hmac
        if (hash_equals($hmac, $calcmac))
        {
            return $original_plaintext;
        }
        return null;
    }
}


// FRONTEND


/**
 * sweet alert with different messages and colors
 * @param string $alertCase
 * @return string sweet alert in html
 */
function showAlert($alertCase){
    // get content from alert.json
    $output = "";
    $jsonstr = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/pages/includes/alerts.json");
    $jsonobj = json_decode($jsonstr, true);

    // ouput alert as html
    foreach ($jsonobj["alerts"] as $alert) {
        if ($alert['case'] == $alertCase){
            $title = $alert['title'];
            $content = $alert['content'];
            $appearance = $alert['appearance'];

            $output = "
            <script>
            swal('$title', '$content', '$appearance');
            </script>
            ";
        }
    }
    return $output;
}

/**
 * shows all posts with title, banner and header
 * @param mysqli $con
 * @param string $query parameter for search
 * @return string post preview in html
 */
function showPostPrev($con, $query){

    // get all posts from database
    if (! empty($query)){ # if a query exists, use query as filter
        $sql = "SELECT PostId FROM TPosts WHERE PostIsDeleted = 0 AND (PostTitle LIKE ? OR PostHeader LIKE ?) ORDER BY PostDate DESC";
        $stmt = mysqli_stmt_init($con);

        $query = '%' . mysqli_real_escape_string($con, $query) .  '%';
        if (!mysqli_stmt_prepare($stmt, $sql)){
            mysqli_stmt_close($stmt);
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'ss', $query, $query);

    }else{ # show all posts
        $sql = "SELECT PostId FROM TPosts WHERE PostIsDeleted = 0 ORDER BY PostDate DESC";
        $stmt = mysqli_stmt_init($con);

        if (!mysqli_stmt_prepare($stmt, $sql)){
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if(! $rows = mysqli_fetch_all($result, MYSQLI_ASSOC)){
        return "<h1>No posts have been found.</h1>";
    }

    $out = "";
    foreach($rows as $row){

        if ($post = createPost($con, $row['PostId'])){
            // get banner
            $banner = "";
            foreach ($post->getMedia() as $m){
                if ($m->getIsBanner() == 1){
                    $banner = $m->getLocation(); # get the media, which is banner
                    break;
                }
            }

            $out .= "
            <div class=\"card mb-3\">
                <div class=\"card-body\">
                    <h1 class=\"card-title\">" . $post->getTitle() . "</h1><hr />
                    <a href=\"post.php?id=" . $post->getId() . "\"><img src=\"$banner\" alt=\"$title\"></a>
                    <h4 class=\"mt-2 card-text text-justify\">" . $post->getHeader() . "</h4>
                    <a href=\"post.php?id=" . $post->getId() . "\" class=\"btn btn-primary float-right\">Read more &gt;&gt;</a>
                    <p class=\"card-text\"><small class=\"text-muted\">Written By <strong>" . $post->getUser()->getUsername() . "</strong> On <strong>" . $post->getDate() . "</strong></small></p><br />";

            // get categories
            foreach ($post->getTopicCons() as $cat){
                $c = $cat->getName();
                $out .= "<h4 class=\"badge badge-secondary mr-1 py-1 px-2\">$c</h4>";
            }
            $out .= "</div></div>"; # close card divs
        }else{
            mysqli_stmt_close($stmt);
            return false;
        }
    }
    return $out;
}

/**
 * shows full post
 * @param mysqli $con
 * @param CPost $post
 * @return string full post in html
 */
function showPostFull($con, $post){

    $id = $post->getId();
    $banner = "";

    // get banenr
    foreach ($post->getMedia() as $m){
        if ($m->getIsBanner() == 1){
            $banner = $m->getLocation(); # get the media, which is banner
            break;
        }
    }

    $out = "
    <div class=\"card border-0\">
        <div class=\"card-body\">
            <h1 class=\"card-title\">" . $post->getTitle() . "</h1><hr />
            <img src=\"$banner\"/><hr />
            <h2 class=\"card-text mt-3\">" . $post->getHeader() . "</h2>
            <p class=\"card-text\">" . $post->getContent() . "</p><br />
        </div>
        <div class=\"card-footer border-0 bg-white\">
            <p class=\"card-text\"><small class=\"text-muted\">Written By <strong>" . $post->getUser()->getUsername() . "</strong> On <strong>" . $post->getDate() . "</strong></small></p>";

    // get categories
    foreach ($post->getTopicCons() as $cat){
        $c = $cat->getName();
            $out .= "<h5 class=\"badge badge-secondary mr-1 py-1 px-2\">$c</h5>";
    }
    $out .= "</div></div>";
    return $out;
}

/**
 * shows all posts with title, banner and header
 * @param mysqli $con
 * @return string post preview in html
 */
function showPostUser($con, $username){

    // get all posts from database
    $sql = "SELECT PostId FROM TPosts WHERE UserUsername = ? AND PostIsDeleted = 0 ORDER BY PostDate DESC";
    $stmt = mysqli_stmt_init($con);

    if (!mysqli_stmt_prepare($stmt, $sql)){
        mysqli_stmt_close($stmt);
        return false;
    }

    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if(! $rows = mysqli_fetch_all($result, MYSQLI_ASSOC)){
        return "<h1>No posts have been written yet.</h1>";
    }

    $out = "";

    foreach($rows as $row){
        if ($post = createPost($con, $row['PostId'])){

            // get banner
            $banner = "";
            foreach ($post->getMedia() as $m){
                if ($m->getIsBanner() == 1){
                    $banner = $m->getLocation(); # get the media, which is banner
                    break;
                }
            }

            // outputs the post as html
            $out .= "
            <div class=\"card mb-3\">
                <div class=\"card-body\">
                    <h1 class=\"card-title float-left\" style=\"max-width: 75%;\">" . $post->getTitle() . "</h1>
                    <div class=\"d-flex flex-row-reverse mt-2\">
                        <a href=\"includes/post-del.inc.php?id=" . $post->getId() . "\" class=\"btn btn-danger\"><i class=\"fas fa-trash-alt\"></i></a>
                        <a href=\"post-edit.php?id=" . $post->getId() . "\" class=\"btn btn-secondary mr-2\"><i class=\"fas fa-edit\"></i></a>
                    </div><hr />
                    <a href=\"post.php?id=" . $post->getId() . "\"><img src=\"$banner\" alt=\"$title\"></a>
                    <h4 class=\"mt-2 card-text text-justify\">" . $post->getHeader() . "</h4>
                    <a href=\"post.php?id=" . $post->getId() . "\" class=\"btn btn-primary float-right\">Read more &gt;&gt;</a>
                    <p class=\"card-text\"><small class=\"text-muted\">Written By <strong>" . $post->getUser()->getUsername() . "</strong> On <strong>" . $post->getDate() . "</strong></small></p><br />";

            // get categories
            foreach ($post->getTopicCons() as $cat){
                $c = $cat->getName();
                $out .= "<h5 class=\"badge badge-secondary mr-1 py-1 px-2\">$c</h5>";
            }
            $out .= "</div></div>";
        }else{
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    return $out;
}

/**
* shows all comments of a post
* @param mysqli $con
* @param CPost $post
* @return string comments as html
*/
function showComments($con, $post){
    $out = "";

    // get the comments
    foreach($post->getComments() as $comment){
        $username = $comment->getUser()->getUsername();
        $date = date("d M Y", strtotime($comment->getDate()));
        $content = $comment->getContent();

        // output all comments as html
        $out .= "
        <div class=\"my-2\">
            <span><strong>$username</strong><span class=\"text-muted\"> on $date</span></span>
            <p class=\"ml-3\">$content</p>
        </div>
        ";
    }

    return $out;
}

/**
* shows top ten categories
* @param mysqli $con
* @param CPost $post
* @return string comments as html
*/
function showCategories($con){

    // get all categories
    $sql = "SELECT TagName FROM TTopicContributions WHERE PostId IS NULL";
    $stmt = mysqli_stmt_init($con);

    if (!mysqli_stmt_prepare($stmt, $sql)){
        mysqli_stmt_close($stmt);
        return false;
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if(! $rows = mysqli_fetch_all($result, MYSQLI_ASSOC)){
        return "<p>No categories have been found.</p>";
    }

    // get a list with all TagNames (with duplicates)
    $arr = array();
    foreach($rows as $row) { array_push($arr, $row['TagName']); }

    // get the most popular categories
    $values = array_count_values($arr);
    arsort($values);
    $popular = array_slice(array_keys($values), 0, 11, true);

    // output categories as html
    $out = "";
    foreach($popular as $cat){
        $out .= "
        <a href=\"#\">
            <h5 class=\"badge badge-secondary mx-1 py-1 px-2\">$cat</h5>
        </a>
        ";
    }

    return $out;
}

/**
* shows all users
* @param mysqli $con
* @return string users as html
*/
function showUsers($con){
    $sql = "SELECT UserUsername FROM TUsers WHERE UserIsDeleted = 0 ORDER BY UserUsername";
    $stmt = mysqli_stmt_init($con);

    if (! mysqli_stmt_prepare($stmt, $sql)){
        mysqli_stmt_close($stmt);
        return false;
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if(! $rows = mysqli_fetch_all($result, MYSQLI_ASSOC)){
        return "<p>No users have been found.</p>";
    }

    $out = '<form method="POST" action="includes/admins.inc.php">
                <div class="table-responsive-lg">
                    <table class="table table-bordered table-striped">
                        <thead>
                          <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Group</th>
                          </tr>
                        </thead>';

    foreach($rows as $row){
        $user = createClient($con, $row['UserUsername']);
        // select the right
        switch ($user->getGroup()) {
            case 'User':
                $opt = '<option value="User" selected>User</option>
                        <option value="BlogAdmin">Admin</option>
                        <option value="Owner">Owner</option>';
                break;
            case 'BlogAdmin':
                $opt = '
                        <option value="User">User</option>
                        <option value="BlogAdmin" selected>Admin</option>
                        <option value="Owner">Owner</option>';
                break;
            case 'Owner':
                $opt = '
                        <option value="User">User</option>
                        <option value="BlogAdmin">Admin</option>
                        <option value="Owner" selected>Owner</option>';
                break;
            default:
                $opt = 1;
                break;
        }

        $out .=         '<tbody id="userTable">
                            <tr>
                                <td>' . $user->getUsername() . '</td>
                                <td>' . $user->getEmail() . '</td>
                                <td>
                                <select class="form-control" name="userGroups[]">
                                    ' . $opt . '
                                </select>
                                <input class="border-0" style="color:black;background-color:transparent;" name="usernames[]" hidden value="' . $user->getUsername() . '" />
                                </td>
                            </tr>';
    }
    $out .= '           </tbody>
                    </table>
                </div>
                <button class="btn btn-primary float-right" type="submit" name="update"><i class="fas fa-sync-alt"></i> Update</button>
            </form>';
    return $out;
}
