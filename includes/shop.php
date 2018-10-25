<?php
/**
 * Created by PhpStorm.
 * User: KoDusk
 * Date: 4/14/2018
 * Time: 2:36 PM
 */
function getProducts() {
    include('db.settings.php');
    $products = $db->query("SELECT pid, title, description, price, image FROM product");
    return $products;
}

function showProduct($pid){
    include('db.settings.php');
    $user = $db->prepare("SELECT pid, title, description, price, image FROM product WHERE pid = ?");
    $user->bind_param("i", $pid);

    if($user->execute()){
        return $user->get_result()->fetch_object();
    }else{
        return false;
    }

    $user->close();
}

function productSearch($keyword){
    include('db.settings.php');
    $search = $db->prepare("SELECT pid, title, description, price, image FROM product WHERE CONCAT(title, ' ', description) LIKE CONCAT('%', ?, '%')");
    $search->bind_param("s", $keyword);

    if($search->execute()) {
        return $search->get_result();
    }

    $search->close();
}

function getCart($uid){
    include('db.settings.php');
    $cart = $db->prepare("SELECT C.pid, P.title, P.price, P.image, C.qty FROM cart AS C, product AS P WHERE C.uid = ? AND C.pid = P.pid");
    $cart->bind_param("i", $uid);

    if($cart->execute()) {
        return $cart->get_result();
    }

    $cart->close();
}

function addCart($uid, $pid){
    include('db.settings.php');
    $errors = array();


    // CHECK IF PRODUCT ID EXISTS //
    $checkPID = $db->prepare("SELECT pid FROM product WHERE pid = ?");
    $checkPID->bind_param("i", $pid);
    $checkPID->execute();
    $checkPID->store_result();
    if($checkPID->num_rows == 0) {
        $errors [] = "PID DOES NOT EXIST";
    }
    $checkPID->close();

    // CHECK IF PRODUCT ID ALREADY IN CART //
    $checkCart = $db->prepare("SELECT uid, pid FROM cart WHERE uid = ? AND pid = ?");
    $checkCart->bind_param("ii", $uid, $pid);
    $checkCart->execute();
    $checkCart->store_result();
    if($checkCart->num_rows > 0) {
        $errors [] = "PRODUCT IS ALREADY IN CART";
    }
    $checkCart->close();


    // IF COUNT ERRORS 0 ADD PRODUCT TO CART //
    if(count($errors) == 0){
        $addCart = $db->prepare("INSERT INTO cart (uid, pid, qty) VALUES (?, ?, 1)");
        $addCart->bind_param("ii", $uid, $pid);

        if($addCart->execute()){
            return true;
        }else{
            return $addCart->error;
        }

        $addCart->close();
    }else{
        return false;
    }
}


function updateCart($uid, $pid, $qty){
    include('db.settings.php');
    $updateCart = $db->prepare("UPDATE cart SET qty = ? WHERE uid = ? AND pid = ?");
    $updateCart->bind_param("iii",$qty,$uid,$pid);

    if($updateCart->execute()){
        return true;
    }else{
        return false;
    }

    $updateCart->close();
}

function removeCart($uid, $pid){
    include('db.settings.php');
    $delete_cart = $db->prepare("DELETE FROM cart WHERE uid = ? AND pid = ?");
    $delete_cart->bind_param("ii", $uid, $pid);

    if($delete_cart->execute()){
        return true;
    }else{
        return false;
    }

    $delete_cart->close();
}

function editProduct($pid, $title, $description, $price, $image) {
    include('db.settings.php');
    $success = array();

    $product = $db->prepare("SELECT pid, title, description, price, image FROM product WHERE pid = ?");
    $product->bind_param("i", $pid);
    $product->execute();
    $product_details = $product->get_result()->fetch_object();

    if(!empty($title)){
        if($product_details->title != $title){
            $update = $db->prepare("UPDATE product SET title = ? WHERE pid = ?");
            $update->bind_param("si", $title, $pid);
            if($update->execute()){
                $success [] = "Product Title Updated!";
            }
            $update->close();
        }
    }

    if(!empty($description)){
        if($product_details->description != $description){
            $update = $db->prepare("UPDATE product SET description = ? WHERE pid = ?");
            $update->bind_param("si", $description, $pid);
            if($update->execute()){
                $success [] = "Product Description Updated!";
            }
            $update->close();
        }
    }

    if(!empty($price)){
        if($product_details->price != $price){
            $update = $db->prepare("UPDATE product SET price = ? WHERE pid = ?");
            $update->bind_param("si", $price, $pid);
            if($update->execute()){
                $success [] = "Product Price Updated!";
            }
            $update->close();
        }
    }

    if(!empty($image)){
        if($product_details->image != $image){
            $update = $db->prepare("UPDATE product SET image = ? WHERE pid = ?");
            $update->bind_param("si", $image, $pid);
            if($update->execute()){
                $success [] = "Product Image Updated!";
            }
            $update->close();
        }
    }

    if(count($success)){
        return $success;
    }else{
        return false;
    }

    $product->close();
}

function deleteProduct($pid){
    include('db.settings.php');
    $delete_product = $db->prepare("DELETE FROM product WHERE pid = ?");
    $delete_product->bind_param("i", $pid);
    $delete_product->execute();
    $delete_product->close();
}

function addProduct($title, $description, $price, $image){
    include('db.settings.php');
    $addProduct = $db->prepare("INSERT INTO product (title, description, price, image) VALUES (?, ?, ?, ?)");
    $addProduct->bind_param("ssss", $title, $description, $price, $image);

    if($addProduct->execute()){
        return true;
    }else{
        return $addProduct->error;
    }

    $addProduct->close();
}