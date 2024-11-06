<?php

function dd($var)
{
    var_dump($var);
    die();
}

function site_url(string $uri = '')
{
    return BASE_URL . $uri;
}

function assets($path)
{
    return site_url('assets/' . $path);
}

function redirect($path = ''): void
{
    header('location: ' . BASE_URL . $path);
    die();

}

function setErrorAndRedirect(string $message, string $target): void
{
    $_SESSION['error'] = $message;
    redirect($target);

}