<?php

require_once "../base.php";
require_once "../lib/Misc.php";

$data = json_decode($_POST['json'],true);

echo json_encode(["status"=>true,"data"=>Faculty::createFaculty($data["FACULTY_NAME"])]);
