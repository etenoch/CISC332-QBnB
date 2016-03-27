<?php
require "cgi/lib/Member.php";
require "cgi/lib/Property.php";
require "cgi/lib/Misc.php";
require "cgi/lib/Booking.php";

$member_id = $_SESSION['MEMBER_ID'];

$logged_in=false;
if (isset($_SESSION['MEMBER_ID'])){
    $logged_in = true;
}

// set page template variables
$page = [];
$page['page_name'] = basename(__FILE__, '.php');
$page['title']= "Manage Bookings";
$page['head']= "";


// page content
ob_start();

if ($logged_in){
?>
<div class="container under_top_bar">
    <h3>Manage Bookings</h3>
    <h6>Your bookings at other accommodations</h6>
    <table class="table table-bordered">
        <tr><th>Name</th><th>Address</th><th>Type</th><th>Booking Period</th><th>Status</th><th>Listing</th></th></tr>
        <?php
        foreach(Booking::getMemberBookings($member_id) as $b){
            if ($b['BOOKING_STATUS']==REQUESTED) $label_type = "primary";
            if ($b['BOOKING_STATUS']==CONFIRMED) $label_type = "success";
            if ($b['BOOKING_STATUS']==REJECTED) $label_type = "danger";
            echo '<tr><td>'.$b['PROPERTY_NAME'].'</td><td>'.$b['ADDRESS_1'].'</td><td>'.$b['PROPERTY_TYPE_NAME'].'</td><td>'.$b['BOOKING_PERIOD'].'</td><td><span class="label label-'.$label_type.'">'.$b['BOOKING_STATUS'].'</span></td><td><a href="?p=listing/'.$b['PROPERTY_ID'].'" class="btn btn-xs btn-info">View Listing</a></td></tr>';
        }
        ?>
    </table>

    <h6>Other members's bookings at your accommodations</h6>
    <table class="table table-bordered">
        <tr><th>Name</th><th>Address</th><th>Type</th><th>Booking Period</th><th>Status</th><th>Listing</th></th></tr>
        <?php
        foreach(Booking::getSupplierBookings($member_id) as $b){
            $statues = [REQUESTED,CONFIRMED,REJECTED];
            $status_select = "<select data-id='".$b['BOOKING_ID']."' class='status_setter' >";
            foreach($statues as $s){
                $status_select.= '<option '.($s==$b['BOOKING_STATUS']?"selected":"").'>'.$s.'</option>';
            }
            $status_select.="</select>";

            echo '<tr><td>'.$b['PROPERTY_NAME'].'</td><td>'.$b['ADDRESS_1'].'</td><td>'.$b['PROPERTY_TYPE_NAME'].'</td><td>'.$b['BOOKING_PERIOD'].'</td><td>'.$status_select.'</td><td><a href="?p=listing/'.$b['PROPERTY_ID'].'" class="btn btn-xs btn-info">View Listing</a></td></tr>';
        }
        ?>
    </table>


</div>
<?php
}else{
    echo "<div class='container under_top_bar'><h4>Please login first <a href='?p=login'>here</h4></div>";
}
$page['body']= ob_get_contents();
ob_clean();

// JS
ob_start();
?>
<script>
    $(".status_setter").change(function(){
        var booking_id = $(this).attr("data-id");

        $.ajax({
            type:"post",
            dataType: "json",
            data:{"BOOKING_ID":booking_id, "BOOKING_STATUS": $(this).find("option:selected").val()},
            url: "cgi/controller/updateBookingStatus.php",
            success: function (jsonResponse) {
                console.log(jsonResponse)
            },
            error: function(re){
                console.log(re);
            }
        });

    });
</script>

<?php
$page['scripts']= ob_get_contents();
ob_end_clean();
?>