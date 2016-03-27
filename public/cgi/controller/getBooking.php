<?php

require "../base.php";
require "../lib/Booking.php";

echo json_encode(Booking::getBooking($_GET['BOOKING_ID']));