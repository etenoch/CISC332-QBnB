<?php
require "cgi/lib/Member.php";
require "cgi/lib/Property.php";
require "cgi/lib/Misc.php";

$logged_in=false;
if (isset($_SESSION['MEMBER_ID'])){
    $logged_in = true;
}

// set page template variables
$page = [];
$page['page_name'] = basename(__FILE__, '.php');
$page['title']= "Manage Bookings";
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
?>
<div class="container under_top_bar">
    <h3>Manage Bookings</h3>

</div>
<?php
}else{
    echo "<div class='container under_top_bar'><h4>Please login first <a href='?p=login'>here</h4></div>";
}
$page['body']= ob_get_contents();
ob_end_clean();
?>
