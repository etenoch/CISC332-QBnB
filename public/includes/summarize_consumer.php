<?php
require_once "cgi/lib/Admin.php";
require_once "cgi/lib/Booking.php";
require_once "cgi/lib/Member.php";
require_once "cgi/lib/Property.php";


$logged_in=false;
if (isset($_SESSION['ADMINISTRATOR_ID'])){
    $logged_in = true;


}

// set page template variables
$page = [];
$page['page_name'] = basename(__FILE__, '.php');
$page['title']= "QBnB Administration";
$page['head']= "";


// page content
ob_start();

if ($logged_in){
$member_id = $page_args[0];
$mem = Member::getMember($member_id);
?>
<div class="container under_top_bar">
    <h3>Consumer Summary: <?=$mem['NAME']?></h3>

    <h5>Bookings</h5>
    <table class="table table-bordered">
        <tr><th>Property</th><th>Date</th></tr>
        <?php
        foreach (Booking::getMemberBookings($member_id) as $b) {
            echo '<tr>';
            echo '<td>'.$b['PROPERTY_NAME'].'</td>';
            echo '<td>'.$b['BOOKING_PERIOD'].'</td>';
            echo '</tr>';
        }
        ?>
    </table>

</div>

<?php
}

$page['body']= ob_get_contents();
ob_clean();

// JS
ob_start();
?>
<?php
$page['scripts']="";
ob_end_clean();
?>



