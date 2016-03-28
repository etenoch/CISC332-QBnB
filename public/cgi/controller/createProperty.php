<?php

require_once "../base.php";
require_once "../lib/Property.php";
require_once "../lib/Member.php";

if (!isset($_SESSION['MEMBER_ID'])) die("Error: not logged in");


$data = json_decode($_POST['json'],true);
$data['SUPPLIER_MEMBER_ID'] = $_SESSION['MEMBER_ID'];


$pictures = json_decode($_POST['pictures'],true);
$features = json_decode($_POST['features'],true);
echo json_encode(["status"=>true,"data"=>Property::createNewProperty($data,$pictures,$features)]);

