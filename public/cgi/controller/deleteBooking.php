<?php

require_once "../base.php";
require_once "../lib/Booking.php";
require_once "../lib/Member.php";

if (!isset($_SESSION['MEMBER_ID'])) die("Error: not logged in");

$booking_id = $_POST['BOOKING_ID'];

echo json_encode(["status"=>Booking::deleteBooking($booking_id)]);

