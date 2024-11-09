<?php

require "bootstrap/init.php";
if (isLoggedIn()) {
    redirect();
}


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
    if ($action == 'verify_token_action') {
        $result = findTokenByHash($_SESSION['hash']);
        if ($params['token'] == $result->token) {
            $session = bin2hex(random_bytes(32));
            changeLoginSession( $_SESSION['email'],$session);
            setcookie('auth', $session, time() + 1728000, '/');
            deleteTokenByHash($_SESSION['hash']);
            unset($_SESSION['hash'], $_SESSION['email']);
            redirect();
        } else {
            setErrorAndRedirect('token is not valid', 'auth.php?action=verify');

        }
    }
    if ($action == 'login') {
        if ( empty($params['email'])) {
            setErrorAndRedirect('email fields required!', 'auth.php?action=login');
        }
        if (!filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
            setErrorAndRedirect('Enter the valid email address!', 'auth.php?action=login');
        }
        if (isUSerExist($params['email'], $params['phone'])) {
            $session = bin2hex(random_bytes(32));
            changeLoginSession($params['email'],$session);
            setcookie('auth', $session, time() + 1728000, '/');
            redirect();
        }else{
            setErrorAndRedirect('user dose not exist with this email', 'auth.php?action=login');

        }

    }
}

    if (isset($_GET['action']) && $_GET['action'] == 'register') {
        include 'tpl/register.php';
    } elseif (isset($_GET['action']) && $_GET['action'] == 'login') {
        include 'tpl/login.php';
    } elseif (isset($_GET['action']) && $_GET['action'] == 'verify' && !empty($_SESSION['email'])) {
        if (!isUserExist($_SESSION['email'])) {
            setErrorAndRedirect('this user is not exist', 'auth.php?action=login');
        }
        if (isset($_SESSION['hash']) && isAliveToken($_SESSION['hash'])) {
            #send old token
            sendTokenByMail($_SESSION['email'], findTokenByHash($_SESSION['hash'])->token);

        } else {
            $tokenResult = createLoginToken();
            sendTokenByMail($_SESSION['email'], $tokenResult['token']);
            $_SESSION['hash'] = $tokenResult['hash'];
        }

        include 'tpl/verify.php';
    }else{
        include 'tpl/login.php';
    }
