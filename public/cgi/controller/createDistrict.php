<?php

require_once "../base.php";
require_once "../lib/Misc.php";
require_once "../lib/District.php";

if (!isset($_SESSION['MEMBER_ID'])) die("Error: not logged in");

$data = json_decode($_POST['json'],true);

echo json_encode(["status"=>true,"data"=>District::createDistrict($data["DISTRICT_NAME"])]);
