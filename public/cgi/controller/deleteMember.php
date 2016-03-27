<?php

require "../base.php";
require "../lib/Property.php";
require "../lib/Member.php";

if (!isset($_SESSION['MEMBER_ID'])) die("Error: not logged in");

echo json_encode(["status"=>Member::deleteMember($_SESSION['MEMBER_ID'])]);

