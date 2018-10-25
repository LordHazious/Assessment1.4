<?php
/**
 * Created by PhpStorm.
 * User: KoDusk
 * Date: 2/15/2018
 * Time: 11:22 AM
 */
if (isset($_GET['logout'])) {
    logout();
}

// The logout function can be called from any page that includes functions
function logout(){
    session_start();
    session_unset();
    session_destroy();
}

function login($email, $pass){
    include('db.settings.php');

    $login = $db->prepare("SELECT uid, email, pass FROM users WHERE email = ? AND pass = ?");
    $login->bind_param("ss", $email, $pass);
    $login->execute();
    $user = $login->get_result()->fetch_object();

    if(!empty($user->uid) && $user->email == $email && $user->pass == $pass){
        session_start();
        $_SESSION['LoggedIn'] = true;
        $_SESSION['uid'] = $user->uid;
        return true;
    }else{
        return false;
    }

    $login->close();
}

// Check Admin allows restriction to functions reserved for admins
function check_admin($uid){
    include('db.settings.php');

    $check_admin = $db->prepare("SELECT * FROM users WHERE admin = 5 AND uid = ?");
    $check_admin->bind_param("i", $uid);
    $check_admin->execute();
    $check_admin->store_result();

    if($check_admin->num_rows >= 1){
        return true;
    }else{
        return false;
    }

    $check_admin->close();
}

function showAllUsers(){
    include('db.settings.php');
    $users = $db->query("SELECT uid, email, first_name, last_name, admin FROM users");
    return $users;
}

function showUser($uid){
    include('db.settings.php');
    $user = $db->prepare("SELECT uid, email, first_name, last_name, admin FROM users WHERE uid = ?");
    $user->bind_param("i", $uid);

    if($user->execute()){
        return $user->get_result()->fetch_object();
    }else{
        return false;
    }

    $user->close();
}

function addUser($email, $first_name, $last_name, $password){
    include('db.settings.php');
    $check_email = $db->prepare("SELECT email FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();

    if($check_email->num_rows == 0){
        $insert_user = $db->prepare("INSERT INTO users (email, pass, first_name, last_name, admin) VALUES (?, ?, ?, ?, 1)");
        $insert_user->bind_param("ssss", $email, $password, $first_name, $last_name);
        if($insert_user->execute()){
            return true;
        }
        $insert_user->close();
    }else{
        return false;
    }
    $check_email->close();
}

function deleteUser($uid){
    include('db.settings.php');
    $delete_user = $db->prepare("DELETE FROM users WHERE uid = ?");
    $delete_user->bind_param("i", $uid);
    $delete_user->execute();
    $delete_user->close();
}

function updateAvatar($uid, $file_name) {
    include('db.settings.php');
    $updateAvatar = $db->prepare("UPDATE users SET avatar = ? WHERE uid = ?");
    $updateAvatar->bind_param("si", $file_name, $uid);

    if($updateAvatar->execute()){
        return true;
    }else{
        return $updateAvatar->error;
    }

    $updateAvatar->close();

}

// This function is used to retrieve the users profile picture
function getAvatar($uid) {
    include('db.settings.php');
    $getAvatar = $db->prepare("SELECT avatar FROM users WHERE uid = ?");
    $getAvatar->bind_param("i", $uid);
    $getAvatar->execute();

    $dbAvatar = $getAvatar->get_result()->fetch_object()->avatar;
    $getAvatar->close();

    if(empty($dbAvatar)){
        $avatar = "avatar.png";
    }else{
        $avatar = "uploads/".$dbAvatar;
    }

    return $avatar;
}

// This function allows users to edit their profiles and admins to make changes to user details
function editUser($uid, $email, $first_name, $last_name, $admin, $pass, $confirm_pass) {
    include('db.settings.php');
    $success = array();

    $user = $db->prepare("SELECT uid, email, first_name, last_name, admin FROM users WHERE uid = ?");
    $user->bind_param("i", $uid);
    $user->execute();
    $user_details = $user->get_result()->fetch_object();

    if(!empty($email)){
        if($user_details->email != $email){
            $check_email = $db->prepare("SELECT email FROM users WHERE email = ?");
            $check_email->bind_param("s", $email);
            $check_email->execute();
            $check_email->store_result();

            if($check_email->num_rows == 0){
                $update = $db->prepare("UPDATE users SET email = ? WHERE uid = ?");
                $update->bind_param("si", $email, $uid);
                if($update->execute()){
                    $success [] = "Email updated!";
                }
                $update->close();
            }
            $check_email->close();
        }
    }

    if(!empty($first_name)){
        if($user_details->first_name != $first_name){
            $update = $db->prepare("UPDATE users SET first_name = ? WHERE uid = ?");
            $update->bind_param("si", $first_name, $uid);
            if($update->execute()){
                $success [] = "First Name updated!";
            }
            $update->close();
        }
    }

    if(!empty($last_name)){
        if($user_details->last_name != $last_name){
            $update = $db->prepare("UPDATE users SET last_name = ? WHERE uid = ?");
            $update->bind_param("si", $last_name, $uid);
            if($update->execute()){
                $success [] = "Last Name updated!";
            }
            $update->close();
        }
    }

    if(!empty($admin)){
        if($user_details->admin != $admin){
            $update = $db->prepare("UPDATE users SET admin = ? WHERE uid = ?");
            $update->bind_param("ii", $admin, $uid);
            if($update->execute()){
                $success [] = "Admin Status updated!";
            }
            $update->close();
        }
    }

    if(!empty($pass)){
        if($pass == $confirm_pass){
            if($user_details->pass != $pass) {
                $update = $db->prepare("UPDATE users SET pass = ? WHERE uid = ?");
                $update->bind_param("si", $pass, $uid);
                if($update->execute()){
                    $success [] = "Password updated!";
                }
                $update->close();
            }
        }
    }

    if(count($success)){
        return $success;
    }else{
        return false;
    }

    $user->close();
}