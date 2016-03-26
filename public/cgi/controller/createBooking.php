<?php

require "../base.php";
require "../lib/Misc.php";
require "../lib/Booking.php";

if (!isset($_SESSION['MEMBER_ID'])){
    die(json_encode(["status"=>true,"message"=>"Member not logged in"]));
}

$data = json_decode($_POST['json'],true);
echo json_encode(["status"=>true,"data"=>Booking::createBooking($_SESSION['MEMBER_ID'],$data['PROPERTY_ID'],$data['BOOKING_PERIOD'])]);
