<?php
session_start();
            ini_set('display_errors',1);
require_once "app/libs/vecni/Autoloader.php";
use libs\vecni\Vecni as app;
app::init(__FILE__);
include "main.ini.php";
app::get_route();
?>
