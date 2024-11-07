<?php
    ob_start();
    session_start();
    error_reporting(error_reporting() & ~E_NOTICE);
    error_reporting(E_ALL);
    date_default_timezone_set('America/Lima');
    $login = $_SESSION['login'];
    $dir = $_SERVER['HTTP_HOST'].substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT']));
    if (!$login) {
        echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=http://".$dir."'>";
        exit();
    }
    require("_database/db_tools.php");
?>