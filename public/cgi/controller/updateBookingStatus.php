<?php

require "../base.php";
require "../lib/Booking.php";

if (!isset($_SESSION['MEMBER_ID'])) die("Error: not logged in");

$booking_id = $_POST['BOOKING_ID'];
$status = $_POST['BOOKING_STATUS'];

echo json_encode(["status"=>Booking::updateBookingStatus($booking_id,$status)]);

