<?php
include './bootstrap/init.php';


var_dump($_POST);

if(isset($_GET['action']) && $_GET['action'] == 'register'){
    include BASE_PATH. 'tpl/register.php';
}else{
    include BASE_PATH. 'tpl/login.php';
}