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
$property_id = $page_args[0];
$prop = Property::getProperty($property_id);
?>
<div class="container under_top_bar">
    <h3>Property Summary: <?=$prop['PROPERTY_NAME']?></h3>

    <h5>Bookings</h5>
    <table class="table table-bordered">
        <tr><th>Consumer</th><th>Date</th></tr>
        <?php
        foreach (Booking::getPropertyBookings($property_id) as $b) {
            echo '<tr>';
            echo '<td>'.$b['NAME'].'</td>';
            echo '<td>'.$b['BOOKING_PERIOD'].'</td>';
            echo '</tr>';
        }
        ?>
    </table>

    <h5>Ratings and Comments</h5>
    <?php
    foreach (Review::getPropertyTopLevelReviews($property_id) as $re){
        echo "<div class='review_card'><h6>".$re['COMENT_TEXT']."</h6>".$re['NAME']." - ".$re['RATING']." stars";

        echo "<div class='reply_container'>";
        foreach(Review::getReplies($re['COMMENT_ID']) as $rp){
            echo "<div class='review_card'><h6>".$rp['COMENT_TEXT']."</h6>".$rp['NAME']."</div>";
        }
        echo "</div>";
        echo "</div>";
    }
    ?>

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



