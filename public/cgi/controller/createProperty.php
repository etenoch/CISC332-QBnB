<?php

require "../base.php";
require "../lib/Property.php";
require "../lib/Member.php";

if (!isset($_SESSION['MEMBER_ID'])) die("Error: not logged in");


$data = json_decode($_POST['json'],true);
$data['SUPPLIER_MEMBER_ID'] = $_SESSION['MEMBER_ID'];


if (isset($_POST['pictures'])){
    $pictures = json_decode($_POST['pictures'],true);
    if (!empty($pictures)) echo json_encode(["status"=>true,"data"=>Property::createNewProperty($data,$pictures)]);
    else echo json_encode(["status"=>true,"data"=>Property::createNewProperty($data)]);
}
