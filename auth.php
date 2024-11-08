<?php

require "bootstrap/init.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_GET['action'];
    $params = $_POST;
    if ($action == 'register') {
        #validation data
        if (empty($params['name']) || empty($params['email']) || empty($params['phone'])) {
            setErrorAndRedirect('All input fields required!', 'auth.php?action=register');
        }
        if (!filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
            setErrorAndRedirect('Enter the valid email address!', 'auth.php?action=register');
        }
        if (isUSerExist($params['email'], $params['phone'])) {
            setErrorAndRedirect('user exist with this data', 'auth.php?action=register');
        }
        # Requested Data is OK

        if (createUser($params)) {
             $_SESSION['email'] = $params['email'];
             redirect('auth.php?action=verify');
        };
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'register') {
    include 'tpl/register.php';
} elseif(isset($_GET['action']) && $_GET['action'] == 'login') {
    include 'tpl/login.php';
}elseif (isset($_GET['action']) && $_GET['action'] == 'verify' && !empty($_SESSION['email'])){
    if(!isUSerExist($_SESSION['email'])){
        setErrorAndRedirect('this user is not exist','auth.php?action=login');
    }
    if(isset( $_SESSION['hash']) && isAliveToken($_SESSION['hash'])){
    #send old token

    }else{
        $tokenResult =createLoginToken();
        $_SESSION['hash'] = $tokenResult['hash'];
    }

    include 'tpl/verify.php';
}
