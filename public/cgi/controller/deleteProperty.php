<?php

require_once "../base.php";
require_once "../lib/Property.php";
require_once "../lib/Member.php";

if (!isset($_SESSION['MEMBER_ID'])) die("Error: not logged in");

$property_id = $_POST['PROPERTY_ID'];

echo json_encode(["status"=>Property::deleteProperty($property_id)]);

