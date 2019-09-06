<?php

if (!isset($_SESSION["lang"]))
{
    $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    require("lang/{$lang}.php");
}
else 
{
    require("lang/".$_SESSION["lang"].".php");
}

?>