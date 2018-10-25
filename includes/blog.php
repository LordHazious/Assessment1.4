<?php
/**
 * Created by PhpStorm.
 * User: KoDusk
 * Date: 3/17/2018
 * Time: 5:07 PM
 */

function getPosts(){
    include('db.settings.php');
    $posts = $db->query("SELECT pid, uid, post, date_created, IF(TIMEDIFF(CURRENT_TIMESTAMP, date_created)/60 > 60, FALSE, ROUND(TIMEDIFF(CURRENT_TIMESTAMP, date_created)/60)) as minutes FROM post ORDER BY date_created DESC");
    return $posts;
}

function getComments($pid){
    include('db.settings.php');
    $comments = $db->prepare("SELECT cid, pid, uid, comment, date_created, IF(TIMEDIFF(CURRENT_TIMESTAMP, date_created)/60 > 60, FALSE, ROUND(TIMEDIFF(CURRENT_TIMESTAMP, date_created)/60)) as minutes FROM post_comment WHERE pid = ? ORDER BY date_created DESC");
    $comments->bind_param("i", $pid);

    if($comments->execute()){
        return $comments->get_result();
    }else{
        return false;
    }

    $comments->close();
}

function newPost($uid,$post){
    include('db.settings.php');
    $newPost = $db->prepare("INSERT INTO post (uid, post, date_created) VALUES (?, ?, CURRENT_TIMESTAMP)");
    $newPost->bind_param("is", $uid, $post);

    if($newPost->execute()){
        return true;
    }else{
        return $newPost->error;
    }

    $newPost->close();
}

function newComment($uid, $pid,$comment){
    include('db.settings.php');
    $newComment = $db->prepare("INSERT INTO post_comment (uid, pid, comment, date_created) VALUES (?, ?, ?, CURRENT_TIMESTAMP)");
    $newComment->bind_param("iis", $uid, $pid, $comment);

    if($newComment->execute()){
        return true;
    }else{
        return $newComment->error;
    }

    $newComment->close();
}

function deletePost($pid){
    include('db.settings.php');
    $deletePost = $db->prepare("DELETE FROM post WHERE pid = ?");
    $deletePost->bind_param("i", $pid);

    $deleteComment = $db->prepare("DELETE FROM post_comment WHERE pid = ?");
    $deleteComment->bind_param("i", $pid);


    if($deletePost->execute() && $deleteComment->execute()){
        return true;
    }else{
        return $deletePost->error + $deleteComment->error;
    }

    $deletePost->close();
    $deleteComment->close();
}

function deleteComment($cid){
    include('db.settings.php');
    $deleteComment = $db->prepare("DELETE FROM post_comment WHERE cid = ?");
    $deleteComment->bind_param("i", $cid);

    if($deleteComment->execute()){
        return true;
    }else{
        return $deleteComment->error;
    }

    $deleteComment->close();
}