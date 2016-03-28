<?php

require_once "../base.php";
require_once "../lib/Booking.php";

echo json_encode(["status"=>true,"data"=>Booking::checkDate($_GET['year'],$_GET['month'],$_GET['day'],$_GET['property_id'])]);