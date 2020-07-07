<?php
/* ------------------------------------------------------------------
Class that represents TTopicContributions in blog_cms_db

Author:     Andrin Weiler, IMS
Date:       2019-12-23

History:
Version    	Date            Changes				    Changer
1.5         2020-01-30      Bug fix                 Andrin
1.4         2020-01-28      Bug fix                 Andrin
1.3         2020-01-23      CC & bug fix            Andrin
1.2         2020-01-22      CC & username added     Andrin
1.1         2020-01-19      Bug fixing              Andrin
1.0        	2019-12-23      Creation			    Andrin

Copyright ©2019 Andrin Weiler, Switzerland. All rights reserved.
------------------------------------------------------------------ */

class CTopicContribution{
    // VAR
    private $id;
    private $name;
    private $post;
    private $user;

    /**
    * CONSTRUCTOR
    *@param string $name
    *@param CPost $post
    *@param CClient $user
    *@param int $id
    */
    public function __construct($name, $post, $user, $id = null){
        $this->id = $id;
        $this->name = $name;
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

        $sql = "INSERT INTO TTopicContributions (ToConId, TagName, PostId, UserUsername) VALUES(null, ?, ?, ?)"; #questionmark as placeholder
        $stmt = mysqli_stmt_init($con); # statement prevents some sql vulnerabilities

        if (mysqli_stmt_prepare($stmt, $sql)){
            // insert TopicContribution
            mysqli_stmt_bind_param($stmt, "sis", $this->name, $postId, $username); # replace questionsmark
            mysqli_stmt_execute($stmt); # execute sql with replaced question marks
            mysqli_stmt_close($stmt);

            // update this->id, id is set to AI -> it is null while inserting
            if ($this->id == null){

                $sql = "SELECT ToConId FROM TTopicContributions WHERE TagName = ? AND UserUsername = ? ORDER BY ToConId DESC";
                $stmt = mysqli_stmt_init($con);

                if (mysqli_stmt_prepare($stmt, $sql)){
                    mysqli_stmt_bind_param($stmt, "ss", $this->name, $username);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt); # gets results as array

                    // set id
                    if ($rows = mysqli_fetch_all($result, MYSQLI_ASSOC)){
                        foreach($rows as $row){
                            $this->id = $row['ToConId'];
                            mysqli_stmt_close($stmt);
                            return true;
                        }
                    }else{
                        mysqli_stmt_close($stmt);
                        return false;
                    }
                }else{
                    print "Notice: ToConId can not be set.";
                    mysqli_stmt_close($stmt);
                    return false;
                }
            }

        }else{
            print "SQL Error occured while trying to insert TopicContribution: " . $this->name . $postId . ".";
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    /**
    *delete from database
    *@param myslqi $con database connection
    *@return bool wasSuccessful
    */
    function delete($con){
        $sql = "DELETE FROM TTopicContributions WHERE ToConId = ?";
        $stmt = mysqli_stmt_init($con);

        if (mysqli_stmt_prepare($stmt, $sql)){
            mysqli_stmt_bind_param($stmt, "i", $this->id);
            mysqli_stmt_execute($stmt);

            return true;
        }else{ # error while deleting ToCon
            return false;
        }
    }

    // GET
    public function getId(){
        return $this->id;
    }

    public function getName(){
        return $this->name;
    }

    public function getPost(){
        return $this->post;
    }

    public function getUser(){
        return $this->user;
    }
}
