<?php
require "cgi/lib/Member.php";
require "cgi/lib/Property.php";
require "cgi/lib/Misc.php";
require "cgi/lib/Booking.php";
require "cgi/lib/District.php";

$property_id = $page_args[0];
$prop = Property::getProperty($property_id);



$logged_in=false;
if (isset($_SESSION['MEMBER_ID'])){
    $logged_in = true;
}

// set page template variables
$page = [];
$page['page_name'] = basename(__FILE__, '.php');
$page['title']= "Manage Properties";
$page['head']= "";

// JS
ob_start();
?>
<?php
$page['scripts']= ob_get_contents();
ob_clean();

// page content
ob_start();

if ($logged_in){
    $member_id = $_SESSION['MEMBER_ID'];

    ?>
<div class="container under_top_bar">
    <h3>Manage: <?=$prop['PROPERTY_NAME']?></h3>





</div>
<?php
}else{
    echo "<div class='container under_top_bar'><h4>Please login first <a href='?p=login'>here</h4></div>";
}
$page['body']= ob_get_contents();
ob_end_clean();
?>
