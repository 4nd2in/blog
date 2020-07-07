<?php
/* ------------------------------------------------------------------
Class that represents TPost in blog_cms_db

Author:     Andrin Weiler, IMS
Date:       2019-12-23

History:
Version    	Date            Changes				    Changer
1.4         2020-02-11      Bug fix                 Andrin
1.3         2020-01-30      Bug fix                 Andrin
1.2         2020-01-28      Added set for
                            media, comments, cat    Andrin
1.1         2020-01-26      Bug fix                 Andrin
1.0        	2019-12-23      Creation			    Andrin

Copyright ©2019 Andrin Weiler, Switzerland. All rights reserved.
------------------------------------------------------------------ */

include_once('CClient.class.php');
include_once('CComment.class.php');
include_once('CMedia.class.php');
include_once('CTopicContribution.class.php');

class CPost {
    // VAR
    private $id;
    private $title;
    private $header;
    private $content;
    private $date;
    private $isDeleted;
    private $user;
    private $media = array();
    private $comments = array();
    private $topicCons = array();

    /**
    * CONSTRUCTOR
    *@param string $title
    *@param string $header
    *@param string $content
    *@param CClient $user
    */
    public function __construct($con, $title, $header, $content, $user, $date = null, $id = null){
        $this->id = $id;
        $this->title = $title;
        $this->header = $header;
        $this->content = $content;
        $this->isDeleted = 0;
        $this->user = $user;

        if ($date == null){
            $this->date = date("Y-m-d H:i");
        }else{
            $this->date = $date;
        }

        if ($id != null){
            $this->setMedia($con);
            $this->setComments($con);
            $this->setCategories($con);
        }
    }

    /**
    *insert into database
    *@param myslqi $con database connection
    *@return bool wasSuccessful
    */
    public function insert($con){

        // VAR
        $username = $this->user->getUsername();

        $sql = "INSERT INTO TPosts (PostTitle, PostHeader, PostContent, PostDate, PostIsDeleted, UserUsername) VALUES(?, ?, ?, ?, ?, ?)"; #questionmark as placeholder
        $stmt = mysqli_stmt_init($con); # statement prevents some sql vulnerabilities

        if (mysqli_stmt_prepare($stmt, $sql)){
            // insert media
            mysqli_stmt_bind_param($stmt, "ssssis", $this->title, $this->header, $this->content, $this->date, $this->isDeleted, $username); # replace questionsmark
            mysqli_stmt_execute($stmt); # execute sql with replaced question marks

            // update this->id, id is set to AI -> it is null while inserting
            if ($this->id == null){
                $sql = "SELECT PostId FROM TPosts WHERE UserUsername = ? AND PostDate = ?"; # UserUsername with PostDate must be unique
                $stmt = mysqli_stmt_init($con);

                if (mysqli_stmt_prepare($stmt, $sql)){
                    mysqli_stmt_bind_param($stmt, "ss", $username, $this->date);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt); # gets results as array

                    // set id
                    if ($rows = mysqli_fetch_all($result, MYSQLI_ASSOC)){
                        foreach($rows as $row){
                            $this->id = $row['PostId'];
                            mysqli_stmt_close($stmt);
                            return true;
                        }
                    }else{
                        mysqli_stmt_close($stmt);
                        return false;
                    }
                }else{
                    print "Notice: PostId can not be set.";
                    return false;
                }
            }
            return true;

        }else{
            print "SQL Error occured while trying to insert Post: " . $this->title .  $this->date . $username . ".";
            return false;
        }
    }

    // SET
    public function setTitle($con, $value){
        $this->title = $value;
        $sql = "UPDATE TPosts SET PostTitle = ? WHERE PostId = ?";
        $stmt = mysqli_stmt_init($con);

        if (mysqli_stmt_prepare($stmt, $sql)){
            mysqli_stmt_bind_param($stmt, "si", $this->title, $this->id);
            mysqli_stmt_execute($stmt);
            return true;
        }else{
            print "SQL Error occured while trying to alter Post->title.";
            return false;
        }
    }

    public function setHeader($con, $value){
        $this->header = $value;
        $sql = "UPDATE TPosts SET PostHeader = ? WHERE PostId = ?";
        $stmt = mysqli_stmt_init($con);

        if (mysqli_stmt_prepare($stmt, $sql)){
            mysqli_stmt_bind_param($stmt, "si", $this->header, $this->id);
            mysqli_stmt_execute($stmt);
            return true;
        }else{
            print "SQL Error occured while trying to alter Post->header.";
            return false;
        }
    }

    public function setContent($con, $value){
        $this->content = $value;
        $sql = "UPDATE TPosts SET PostContent = ? WHERE PostId = ?";
        $stmt = mysqli_stmt_init($con);

        if (mysqli_stmt_prepare($stmt, $sql)){
            mysqli_stmt_bind_param($stmt, "si", $this->content, $this->id);
            mysqli_stmt_execute($stmt);
            return true;
        }else{
            print "SQL Error occured while trying to alter Post->content.";
            return false;
        }
    }

    public function setIsDeleted($con, $value){
        $this->isDeleted = $value;
        $sql = "UPDATE TPosts SET PostIsDeleted = ? WHERE PostId = ?";
        $stmt = mysqli_stmt_init($con);

        if (mysqli_stmt_prepare($stmt, $sql)){
            mysqli_stmt_bind_param($stmt, "ii", $this->isDeleted, $this->id);
            mysqli_stmt_execute($stmt);
            return true;
        }else{
            print "SQL Error occured while trying to alter Post->isDeleted.";
            return false;
        }
    }

    public function setMedia($con){
        $sql = "SELECT * FROM TPosts p NATURAL JOIN TMedia WHERE p.PostId = ?";
        $stmt = mysqli_stmt_init($con);

        if (mysqli_stmt_prepare($stmt, $sql)){
            mysqli_stmt_bind_param($stmt, 'i', $this->id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

            $media = array();

            foreach($rows as $row){
                $m = new CMedia($row['MediaLocation'], $row['MediaIsBanner'], $row['MediaType'], $this, $row['MediaId']);
                array_push($media, $m);
            }
            mysqli_stmt_close($stmt);
            $this->media = $media;

        }else{
            $this->media = false;
        }
    }

    public function setComments($con){
        $sql = "SELECT * FROM TPosts p NATURAL JOIN TComments WHERE p.PostId = ? ORDER BY ComDate";
        $stmt = mysqli_stmt_init($con);

        if (mysqli_stmt_prepare($stmt, $sql)){
            mysqli_stmt_bind_param($stmt, 'i', $this->id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

            $comments = array();

            foreach($rows as $row){
                $c = new CComment($row['ComContent'], $this, $this->user, $row['ComDate'], $row['ComId']);
                array_push($comments, $c);
            }
            mysqli_stmt_close($stmt);
            $this->comments = $comments;

        }else{
            $this->comments = false;
        }
    }

    public function setCategories($con){
        $sql = "SELECT * FROM TPosts p NATURAL JOIN TTopicContributions WHERE p.PostId = ? ORDER BY TagName";
        $stmt = mysqli_stmt_init($con);

        if (mysqli_stmt_prepare($stmt, $sql)){
            mysqli_stmt_bind_param($stmt, 'i', $this->id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

            $categories = array();

            foreach($rows as $row){

                $c = new CTopicContribution($row['TagName'], $this, $this->user, $row['ToConId']);
                array_push($categories, $c);
            }
            mysqli_stmt_close($stmt);
            $this->topicCons = $categories;

        }else{
            $this->topicCons = false;
        }
    }

    // GET
    public function getId(){
        return $this->id;
    }

    public function getTitle(){
        return $this->title;
    }

    public function getHeader(){
        return $this->header;
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

    public function getUser(){
        return $this->user;
    }

    public function getMedia(){
        return $this->media;
    }

    public function getComments(){
        return $this->comments;
    }

    public function getTopicCons(){
        return $this->topicCons;
    }

}
