<?php

require_once "../base.php";
require_once "../lib/Misc.php";

$data = json_decode($_POST['json'],true);

echo json_encode(["status"=>true,"data"=>DegreeType::createDegreeType($data["DEGREE_TYPE_NAME"])]);
