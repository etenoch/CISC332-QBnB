<?php

require "../base.php";
require "../lib/Property.php";
require "../lib/Member.php";

echo json_encode(Property::getAllProperties());