<?php

require "../base.php";
require "../lib/Property.php";
require "../lib/Member.php";

if (!isset($_SESSION['MEMBER_ID'])) die("Error: not logged in");

$property_id = $_POST['PROPERTY_ID'];
$data = json_decode($_POST['json'],true);
$pictures = json_decode($_POST['pictures'],true);
$features = json_decode($_POST['features'],true);

echo json_encode(["status"=>Property::updateProperty($property_id,$data,$pictures,$features)]);

