<?php
require 'bootstrap/init.php';
if (!isLoggedIn()) {
    redirect('auth.php?action=login');
}
$userData = getAuthenticateUserBySession($_COOKIE['auth']);

if(isset($_GET['action']) && $_GET['action']== 'logout'){
    logout($userData->email);
}

include 'tpl/index.php';