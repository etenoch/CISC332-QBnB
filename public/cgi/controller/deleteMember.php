<?php

require_once "../base.php";
require_once "../lib/Property.php";
require_once "../lib/Member.php";

if (!isset($_SESSION['MEMBER_ID'])) die("Error: not logged in");

echo json_encode(["status"=>Member::deleteMember($_SESSION['MEMBER_ID'])]);

