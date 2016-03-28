<?php

require_once "../base.php";
require_once "../lib/Property.php";
require_once "../lib/Member.php";

echo json_encode(Property::getAllProperties());