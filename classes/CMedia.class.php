<?php
/* ------------------------------------------------------------------
Class that represents TMedia in blog_cms_db

Author:     Andrin Weiler, IMS
Date:       2019-12-23

History:
Version    	Date            Changes				Changer
1.2         2020-01-30      Bug fix             Andrin
1.1         2020-01-26      Bug fix             Andrin
1.1         2020-01-22      CC                  Andrin
1.0        	2019-12-23      Creation			Andrin

Copyright ©2019 Andrin Weiler, Switzerland. All rights reserved.
------------------------------------------------------------------ */

class CMedia {
    // VAR
    private $id;
    private $location;
    private $isBanner;
    private $type;
    private $post;

    /**
    * CONSTRUCTOR
    *@param string $location as absolute path
    *@param int $isBanner
    *@param string $type img or vid
    *@param CPost $post
    */
    public function __construct($location, $isBanner, $type, $post, $id = null){
        $this->id = $id;
        $this->location = $location;
        $this->isBanner = $isBanner;
        $this->type = $type;
        $this->post = $post;
    }

    // METHODS

    /**
    *insert into database
    *@param myslqi $con database connection
    *@return bool wasSuccessful
    */
    public function insert($con){

        // VAR
        if (isset($this->post)){
            $postId = $this->post->getId();
        }else{
            $postId = null;
        }

        $sql = "INSERT INTO TMedia (MediaId, MediaLocation, MediaIsBanner, MediaType, PostId) VALUES(NULL, ?, ?, ?, ?)"; # questionmark as placeholder
        $stmt = mysqli_stmt_init($con); # statement prevents some sql injection vulnerabilities

        if (mysqli_stmt_prepare($stmt, $sql)){
            // insert media
            mysqli_stmt_bind_param($stmt, "sisi", $this->location, $this->isBanner, $this->type, $postId); # replace questionsmark with string, string and integer
            mysqli_stmt_execute($stmt); # execute sql with replaced question marks

            // update this->id, id is set to AI -> it is null while inserting
            if ($this->id == null){
                $sql = "SELECT MediaId FROM TMedia WHERE MediaLocation = ? AND PostId = ?"; # MediaLocation with PostId must be unique
                $stmt = mysqli_stmt_init($con);

                if (mysqli_stmt_prepare($stmt, $sql)){
                    mysqli_stmt_bind_param($stmt, "si", $this->location, $postId);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt); # gets results as array
                    $rows = mysqli_fetch_assoc($result);

                    // set id
                    if ($rows = mysqli_fetch_all($result, MYSQLI_ASSOC)){
                        foreach($rows as $row){
                            $this->id = $row['MediaId'];
                            mysqli_stmt_close($stmt);
                            return true;
                        }
                    }else{
                        mysqli_stmt_close($stmt);
                        return false;
                    }
                }else{
                    print "Notice: MediaId can not be set.";
                }
            }
            return true;

        }else{
            print "SQL Error occured while trying to insert Media " . $this->location . $this->type . $postId . ".";
            return false;
        }
    }

    /**
    *delete this entry in database
    *@param myslqi $con database connection
    *@return bool wasSuccessful
    */
    public function delete($con){
        $sql = "DELETE FROM TMedia WHERE MediaId = ?";
        $stmt = mysqli_stmt_init($con);

        if (mysqli_stmt_prepare($stmt, $sql)){
            mysqli_stmt_bind_param($stmt, "i", $this->id);
            mysqli_stmt_execute($stmt);

            // TODO: delete object and delete picture on server if exists
            return true;
        }else{
            print "SQL Error occured while trying to delete Media: " . $this->id . ".";
            return false;
        }
    }

    // PROPERTIES
    public function setLocation($con, $value){
        $this->location = $value;
        $sql = "UPDATE TMedia SET MediaLocation = ? WHERE MediaId = ?";
        $stmt = mysqli_stmt_init($con);

        if (mysqli_stmt_prepare($stmt, $sql)){
            mysqli_stmt_bind_param($stmt, "si", $this->location, $this->id);
            mysqli_stmt_execute($stmt);
            return true;
        }else{
            print "SQL Error occured while trying to alter Media->location.";
            return false;
        }
    }

    public function getId(){
        return $this->id;
    }

    public function getLocation(){
        return $this->location;
    }

    public function getIsBanner(){
        return $this->isBanner;
    }

    public function getType(){
        return $this->type;
    }

    public function getPost(){
        return $this->post;
    }
}
