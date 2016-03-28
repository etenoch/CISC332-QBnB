<?php

require_once "../base.php";
require_once "../lib/Booking.php";

echo json_encode(Booking::getBooking($_GET['BOOKING_ID']));