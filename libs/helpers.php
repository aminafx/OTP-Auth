<?php

function dd($var)
{
    var_dump($var);
    die();
}

function site_url(string $uri='')
{
    return BASE_URL . $uri;
}

function assets($path)
{
    return site_url('assets/'. $path);
}