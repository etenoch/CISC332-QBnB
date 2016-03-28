<?php

require_once "../base.php";
require_once "../lib/Misc.php";

if (!isset($_SESSION['MEMBER_ID'])) die("Error: not logged in");

$data = json_decode($_POST['json'],true);

echo json_encode(["status"=>true,"data"=>Review::createReview($_SESSION['MEMBER_ID'],$data['BOOKING_ID'],$data['REPLY_COMMENT_ID'],$data['RATING'],$data['COMMENT'])]);
