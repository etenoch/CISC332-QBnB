<?php

require_once "../base.php";
require_once "../lib/Booking.php";

//if (!isset($_SESSION['MEMBER_ID'])) die("Error: not logged in");

$property_id = $_GET['PROPERTY_ID'];
$pivot_date = $_GET['pivot_date'];

echo json_encode(["status"=>true,"data"=>Booking::getAvailability($property_id, $pivot_date)]);

