<?php
require "cgi/base.php";
include "includes/header.php";
$home = true;
if (!empty($_GET['p'])) {
    $args = $_GET['p'];
    $args = explode("/",$args);

    if (empty($args) || $args[0]== "home") include "includes/home.php";
    else {
        $home = false;
        $page_name = $args[0];
        $page_args = array_slice($args, 1);
        $success = include "includes/".$page_name.".php";
        if (!$success) include "includes/error.php";
    }
}else include "includes/home.php";
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?=$page['title']?> | QBnB</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Loading Bootstrap -->
    <link href="css/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Loading Flat UI -->
    <link href="css/flat-ui.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">

    <link rel="shortcut icon" href="img/favicon.ico">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
    <!--[if lt IE 9]><script src="js/vendor/html5shiv.js"></script><script src="js/vendor/respond.min.js"></script><![endif]-->
    <?=$page['head']?>
</head>
<body>
    <?php
    echo generateHeader($page['page_name']);
    ?>
    <?=$page['body']?>


    <?php if (!$home){ ?>

    <div id="footer">
        <div class="container">
            QBnB - Made by Enoch Tam, Anastasiya Tarnouskaya, Chris Thomas
        </div>
    </div>
    <?php } ?>
    <!-- jQuery (necessary for Flat UI's JavaScript plugins) -->
    <script src="js/vendor/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/vendor/video.js"></script>
    <script src="js/flat-ui.min.js"></script>
    <?=$page['scripts']?>
</body>
</html>



