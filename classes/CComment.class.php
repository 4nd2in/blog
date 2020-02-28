<?php
/* ------------------------------------------------------------------
Class that represents TComments in blog_cms_db

Author:     Andrin Weiler, IMS
Date:       2019-12-23

History:
Version    	Date            Changes				Changer
1.2         2020-01-30      Bug fix             Andrin
1.1         2020-01-22      CC                  Andrin
1.0        	2019-12-23      Creation			Andrin

Copyright ©2019 Andrin Weiler, Switzerland. All rights reserved.
------------------------------------------------------------------ */

class CComment {
    // VAR
    private $id;
    private $content;
    private $date;
    private $isDeleted;
    private $post;
    private $user;

    /** CONSTRUCTOR
    *@param string $content
    *@param CPost $post
    *@param CClient $user
    */
    public function __construct($content, $post, $user, $date = null, $id = null){
        $this->id = $id;
        $this->content = $content;

        if ($date == null){
            $this->date = date("Y-m-d H:i");
        }else{
            $this->date = $date;
        }

        $this->isDeleted = 0;
        $this->post = $post;
        $this->user = $user;
    }

    /**
    *insert into database
    *@param myslqi $con database connection
    *@return bool wasSuccessful
    */
    public function insert($con){

        // VAR
        $username = $this->user->getUsername();

        if (isset($this->post)){
            $postId = $this->post->getId();
        }else{
            $postId = null;
        }

        $sql = "INSERT INTO TComments (ComId, ComDate, ComContent, ComIsDeleted, PostId, UserUsername) VALUES(null, ?, ?, ?, ?, ?)"; #questionmark as placeholder
        $stmt = mysqli_stmt_init($con); # statement prevents some sql vulnerabilities

        if (mysqli_stmt_prepare($stmt, $sql)){
            // insert media
            mysqli_stmt_bind_param($stmt, "ssiis", $this->date, $this->content, $this->isDeleted, $postId, $username); # replace questionsmark
            mysqli_stmt_execute($stmt); # execute sql with replaced question marks

            // update this->id, id is set to AI -> it is null while inserting
            if ($this->id == null){
                $sql = "SELECT ComId FROM TComments WHERE UserUsername = ? AND ComDate = ?"; # UserUsername with ComDate must be unique
                $stmt = mysqli_stmt_init($con);

                if (mysqli_stmt_prepare($stmt, $sql)){
                    mysqli_stmt_bind_param($stmt, "ss", $username, $this->date);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt); # gets results as array

                    // set id
                    if ($row = mysqli_fetch_assoc($result)){
                        $this->id = $row['ComId'];
                        mysqli_stmt_close($stmt);
                        return true;
                    }else{
                        mysqli_stmt_close($stmt);
                        return false;
                    }
                }else{
                    print "Notice: ComId can not be set.";
                }
            }
            return true;

        }else{
            print "SQL Error occured while trying to insert Comment: " . $this->date . $this->content . $this->isDeleted . $postId . $username . ".";
            return false;
        }
    }

    // SET
    public function setContent($con, $value){
        $this->content = $value;
        $sql = "UPDATE TComments SET ComContent = ? WHERE ComId = ?";
        $stmt = mysqli_stmt_init($con);

        if (mysqli_stmt_prepare($stmt, $sql)){
            mysqli_stmt_bind_param($stmt, "si", $this->content, $this->id);
            mysqli_stmt_execute($stmt);
            return true;
        }else{
            print "SQL Error occured while trying to alter Comment->content.";
            return false;
        }
    }

    public function setIsDeleted($con, $value){
        $this->isDeleted = $value;
        $sql = "UPDATE TComments SET ComIdDeleted = ? WHERE ComId = ?";
        $stmt = mysqli_stmt_init($con);

        if (mysqli_stmt_prepare($stmt, $sql)){
            mysqli_stmt_bind_param($stmt, "ii", $this->isDeleted, $this->id);
            mysqli_stmt_execute($stmt);
            return true;
        }else{
            print "SQL Error occured while trying to alter Comment->isDeleted.";
            return false;
        }
    }

    // GET
    public function getId(){
        return $this->id;
    }

    public function getContent(){
        return $this->content;
    }

    public function getDate(){
        return $this->date;
    }

    public function getIsDeleted(){
        return $this->isDeleted;
    }

    public function getPost(){
        return $this->post;
    }

    public function getUser(){
        return $this->user;
    }
}
