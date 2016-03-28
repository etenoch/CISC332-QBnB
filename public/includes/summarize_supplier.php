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
    <h3>Supplier Summary: <?=$mem['NAME']?></h3>

    <h5>Properties</h5>
    <table class="table table-bordered">
        <tr><th>Property</th><th>Avg Rating</th></tr>
        <?php
        foreach (Property::getMemberProperties($member_id) as $p) {
            echo '<tr>';
            echo '<td>'.$p['PROPERTY_NAME'].'</td>';
            echo '<td>'.Review::getAvgRating($p['PROPERTY_ID']).'</td>';
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



