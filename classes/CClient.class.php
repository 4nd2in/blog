<?php
/* ------------------------------------------------------------------
Classes that represents TUsers in blog_cms_db

Author:     Andrin Weiler, IMS
Date:       2019-12-23

History:
Version    	Date            Changes				       Changer
1.7         2020-02-13      Bug fix                    Andrin
1.6         2020-01-28      added set for post         Andrin
1.5         2020-01-26      CC                         Andrin
1.4         2020-01-23      Bug fix                    Andrin
1.3         2020-01-22      Added categories           Andrin
1.2         2019-12-27      Added all Client funcs     Andirn
1.1         2019-12-26      Added funcs, WIP           Andrin
1.0        	2019-12-23      Creation, WIP    	       Andrin

Copyright ©2019 Andrin Weiler, Switzerland. All rights reserved.
------------------------------------------------------------------ */

include_once('CComment.class.php');
include_once('CMedia.class.php');
include_once('CTopicContribution.class.php');
include_once('CPost.class.php');

abstract class CClient{
    // VAR
    protected $username;
    protected $email;
    protected $firstname;
    protected $lastname;
    protected $password;
    protected $token;
    protected $desc;
    protected $group;
    protected $isDeleted;
    protected $posts = array();
    protected $comments = array();
    protected $categories = array();

    /**
     * CONSTRUCTOR
     *@param mysqli $con
     *@param string $username
     *@param string $email
     *@param string $firstname
     *@param string $lastname
     *@param string $password hashed password maxlen 512, not null
     *@param string $token
     *@param string $desc
     *@param string $group
     */
    public function __construct($con, $username, $email, $firstname, $lastname, $password, $token, $desc, $group = "User"){
        $this->username = $username;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->password = $password;
        $this->token = $token;
        $this->desc = $desc;
        $this->group = $group;
        $this->isDeleted = 0;

        $this->setPosts($con);
        $this->setComments($con);
        $this->setCategories($con);
    }

    // METHODS
    abstract function writeComment($con);
    abstract function editComment($con);
    abstract function deleteComment($con);

    // SET
    # setUsername not yet implemented because it is a primary key.

    public function setEmail($con, $value){
        $this->email = $value;
        $sql = "UPDATE TUsers SET UserEmail = ? WHERE UserUsername = ?"; # questionmark as placeholder
        $stmt = mysqli_stmt_init($con); # statement prevents some sql injection vulnerabilities

        if (mysqli_stmt_prepare($stmt, $sql)){
            mysqli_stmt_bind_param($stmt, "ss", $this->email, $this->username); # replace questionsmark
            mysqli_stmt_execute($stmt); # execute sql with replaced question marks
            mysqli_stmt_close($stmt);
            return true;
        }else{
            print "SQL Error occured while trying to alter Post->email.";
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    public function setFirstname($con, $value){
        $this->firstname = $value;
        $sql = "UPDATE TUsers SET UserFirstname = ? WHERE UserUsername = ?";
        $stmt = mysqli_stmt_init($con);

        if (mysqli_stmt_prepare($stmt, $sql)){
            mysqli_stmt_bind_param($stmt, "ss", $this->firstname, $this->username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return true;
        }else{
            print "SQL Error occured while trying to alter Post->firstname.";
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    public function setLastname($con, $value){
        $this->lastname = $value;
        $sql = "UPDATE TUsers SET UserLastname = ? WHERE UserUsername = ?";
        $stmt = mysqli_stmt_init($con);

        if (mysqli_stmt_prepare($stmt, $sql)){
            mysqli_stmt_bind_param($stmt, "ss", $this->lastname, $this->username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return true;
        }else{
            print "SQL Error occured while trying to alter Post->lastname.";
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    public function setPassword($con, $value){
        $this->password = $value;
        $sql = "UPDATE TUsers SET UserPassword = ? WHERE UserUsername = ?";
        $stmt = mysqli_stmt_init($con);

        if (mysqli_stmt_prepare($stmt, $sql)){
            mysqli_stmt_bind_param($stmt, "ss", $this->password, $this->username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return true;
        }else{
            print "SQL Error occured while trying to alter Post->password.";
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    public function setToken($con, $value){
        $this->token = $value;
        $sql = "UPDATE TUsers SET UserToken = ? WHERE UserUsername = ?";
        $stmt = mysqli_stmt_init($con);

        if (mysqli_stmt_prepare($stmt, $sql)){
            mysqli_stmt_bind_param($stmt, "ss", $this->token, $this->username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return true;
        }else{
            print "SQL Error occured while trying to alter User->token.";
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    public function setDesc($con, $value){
        $this->lastname = $value;
        $sql = "UPDATE TUsers SET UserDesc = ? WHERE UserUsername = ?";
        $stmt = mysqli_stmt_init($con);

        if (mysqli_stmt_prepare($stmt, $sql)){
            mysqli_stmt_bind_param($stmt, "ss", $this->desc, $this->username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return true;
        }else{
            print "SQL Error occured while trying to alter Post->desc.";
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    public function setGroup($con, $value){
        $this->group = $value;
        $sql = "UPDATE TUsers SET UserGroup = ? WHERE UserUsername = ?";
        $stmt = mysqli_stmt_init($con);

        if (mysqli_stmt_prepare($stmt, $sql)){
            mysqli_stmt_bind_param($stmt, "ss", $this->group, $this->username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return true;
        }else{
            print "SQL Error occured while trying to alter Post->group. UserGroup must be 'User', 'Admin', 'Owner'.";
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    public function setIsDeleted($con, $value){
        $this->isDeleted = $value;
        $sql = "UPDATE TUsers SET UserIsDeleted = ? WHERE UserUsername = ?";
        $stmt = mysqli_stmt_init($con);

        if (mysqli_stmt_prepare($stmt, $sql)){
            mysqli_stmt_bind_param($stmt, "ss", $this->isDeleted, $this->username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return true;
        }else{
            print "SQL Error occured while trying to alter Post->isDeleted.";
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    public function setPosts($con){
        // get posts from databases that match with client
        $sql = "SELECT * FROM TUsers u NATURAL JOIN TPosts WHERE u.UserUsername = ? ORDER BY PostDate";
        $stmt = mysqli_stmt_init($con);

        if (mysqli_stmt_prepare($stmt, $sql)){
            mysqli_stmt_bind_param($stmt, 's', $this->username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

            $posts = array();

            foreach ($rows as $row) {
                $p = new CPost($con, $row['PostTitle'], $row['PostHeader'], $row['PostContent'], $this, date("Y-m-d H:i"), $row['PostId']);
                array_push($posts, $p);
            }

            mysqli_stmt_close($stmt);
            $this->posts = $posts;
        }else{
            $this->$posts = false;
        }
    }

    public function setComments($con){
        // get comments from databases that match with client
        $sql = "SELECT * FROM TUsers u NATURAL JOIN TComments WHERE u.UserUsername = ? ORDER BY ComDate";
        $stmt = mysqli_stmt_init($con); # could not be set for whatever reason, I have no fucking clue tbh

        if (mysqli_stmt_prepare($stmt, $sql)){
            mysqli_stmt_bind_param($stmt, 's', $this->username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

            $comments = array();

            foreach($rows as $row){
                // get post that matches with comment
                $post = null;
                foreach($this->posts as $p){
                    if ($p->getId() == $row['PostId']){
                        $post = $p;
                    }
                }

                $c = new CComment($row['ComContent'], $post, $this, $row['ComId']);
                array_push($comments, $c);
            }
            mysqli_stmt_close($stmt);
            $this->comments = $comments;
        }else{
            $this->comments = false;
        }
    }

    public function setCategories($con){
        // get categories from database that match with client
        $sql = "SELECT * FROM TUsers u NATURAL JOIN TTopicContributions WHERE u.UserUsername = ? ORDER BY TagName";
        $stmt = mysqli_stmt_init($con);

        if (mysqli_stmt_prepare($stmt, $sql)){
            mysqli_stmt_bind_param($stmt, 's', $this->username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

            $categories = array();

            foreach($rows as $row){

                // get post that matches with category
                $post = null;
                foreach($this->posts as $p){
                    // set post if foreignkeys are the same
                    if ($p->getId() == $row['PostId']){
                        $post = $p;
                    }
                }

                $c = new CTopicContribution($row['TagName'], $post, $this, $row['ToConId']);
                array_push($categories, $c);
            }
            mysqli_stmt_close($stmt);
            $this->categories = $categories;

        }else{
            $this->categories = false;
        }
    }

    // GET
    public function getUsername(){
        return $this->username;
    }

    public function getEmail(){
        return  $this->email;
    }

    public function getFirstname(){
        return $this->firstname;
    }

    public function getLastname(){
        return $this->lastname;
    }

    public function getPassword(){
        return $this->password;
    }

    public function getToken(){
        return $this->token;
    }

    public function getDesc(){
        return  $this->desc;
    }

    public function getGroup(){
        return $this->group;
    }

    public function getIsDeleted(){
        return $this->username;
    }

    public function getComments(){
        return $this->comments;
    }

    public function getCategories(){
        return $this->categories;
    }
}

class CUser extends CClient{

    // comments
    public function writeComment($con){

    }

    public function editComment($con){

    }

    public function deleteComment($con){

    }
}

class CBlogAdmin extends CClient{

    // comments
    public function writeComment($con){

    }

    public function editComment($con){

    }

    public function deleteComment($con){

    }

    // posts
    public function writePost($con){

    }

    public function editPost($con){

    }

    public function deletePost($con){

    }

    // categories
    public function addCat($con, $name, $post){
        $cat = new CTopicContribution($name, $post, $this);
        $cat->insert($con);
        $this->setCategories($con);
    }

    public function deleteCat($con, $name, $post, $id){
        $cat = new CTopicContribution($name, $post, $this, $id);
        $cat->delete($con);
    }
}

class COwner extends CClient{

    // comments
    public function writeComment($con){

    }

    public function editComment($con){

    }

    public function deleteComment($con){

    }

    // posts
    public function writePost($con){

    }

    public function editPost($con){

    }

    public function deletePost($con){

    }

    // categories
    public function addCat($con, $name, $post){
        $cat = new CTopicContribution($name, $post, $this);
        $cat->insert($con);
        $this->setCategories($con);
    }

    public function deleteCat($con, $name, $post, $id){
        $cat = new CTopicContribution($name, $post, $this, $id);
        $cat->delete($con);
    }

    // admins
    public function addAdmin($con){

    }

    public function deleteAdmin($con){

    }
}
