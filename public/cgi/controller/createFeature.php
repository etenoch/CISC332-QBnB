<?php

require "../base.php";
require "../lib/Misc.php";

if (!isset($_SESSION['MEMBER_ID'])) die("Error: not logged in");

$data = json_decode($_POST['json'],true);

echo json_encode(["status"=>true,"data"=>Feature::createNewFeature($data["FEATURE_NAME"])]);
