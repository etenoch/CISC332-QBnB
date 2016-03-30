<?php
require_once "cgi/lib/Member.php";
require_once "cgi/lib/Property.php";
require_once "cgi/lib/Misc.php";
require_once "cgi/lib/Booking.php";

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
        <tr><th>Name</th><th>Address</th><th>Type</th><th>Booking Period</th><th>Status</th><th>Review</th><th>Cancel</th><th>Listing</th></th></tr>
        <?php
        foreach(Booking::getMemberBookings($member_id) as $b){
            if ($b['BOOKING_STATUS']==REQUESTED) $label_type = "primary";
            if ($b['BOOKING_STATUS']==CONFIRMED) $label_type = "success";
            if ($b['BOOKING_STATUS']==REJECTED) $label_type = "danger";
            echo '<tr><td>'.$b['PROPERTY_NAME'].'</td><td>'.$b['ADDRESS_1'].'</td><td>'.$b['PROPERTY_TYPE_NAME'].'</td><td>'.$b['BOOKING_PERIOD'].'</td><td><span class="label label-'.$label_type.'">'.$b['BOOKING_STATUS'].'</span></td><td><a href="#" data-toggle="modal" data-target="#reviewModal" data-id="'.$b['BOOKING_ID'].'" class="review_btn btn btn-xs btn-info">Write Review</a></td><td><a href="#" data-id="'.$b['BOOKING_ID'].'" class="cancel_btn btn btn-xs btn-info">Cancel</a></td><td><a href="?p=listing/'.$b['PROPERTY_ID'].'" class="btn btn-xs btn-info">View Listing</a></td></tr>';
        }
        ?>
    </table>
    <br>
    <h6>Other members's bookings at your accommodations</h6>
    <table class="table table-bordered">
        <tr><th>Member Name</th><th>Property Name</th><th>Address</th><th>Type</th><th>Booking Period</th><th>Status</th><th>Listing</th></th></tr>
        <?php
        foreach(Booking::getSupplierBookings($member_id) as $b){
            $statues = [REQUESTED,CONFIRMED,REJECTED];
            $status_select = "<select data-id='".$b['BOOKING_ID']."' class='status_setter' >";
            foreach($statues as $s){
                $status_select.= '<option '.($s==$b['BOOKING_STATUS']?"selected":"").'>'.$s.'</option>';
            }
            $status_select.="</select>";

            echo '<tr><td>'.$b['NAME'].'</td><td>'.$b['PROPERTY_NAME'].'</td><td>'.$b['ADDRESS_1'].'</td><td>'.$b['PROPERTY_TYPE_NAME'].'</td><td>'.$b['BOOKING_PERIOD'].'</td><td>'.$status_select.'</td><td><a href="?p=listing/'.$b['PROPERTY_ID'].'" class="btn btn-xs btn-info">View Listing</a></td></tr>';
        }
        ?>
    </table>


</div>

<!-- Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Leave review on: <span class="data_property_name"></span></h4>
            </div>
            <div class="modal-body">
                <h6 style="margin-top: 0px;">You booked this property for <span class="data_booking_period"></span></h6>
                <b>Leave a comment on the property</b>
                <textarea  id="review_comment" cols="10" rows="3" class="form-control"></textarea>
                <b>Leave a rating on the property</b>
                <select id="review_rating">
                    <option value="1">1 star</option>
                    <option value="2">2 star</option>
                    <option value="3">3 star</option>
                    <option value="4">4 star</option>
                    <option value="5">5 star</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary  review_save_btn" >Save review</button>
            </div>
        </div>
    </div>
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

    $(".cancel_btn").click(function(){
        var c = confirm("Are you sure?");
        if (c){
            var booking_id = $(this).data("id");
            $.ajax({
                type:"post",
                dataType: "json",
                data:{"BOOKING_ID":booking_id},
                url: "cgi/controller/deleteBooking.php",
                success: function (jsonResponse) {
                    console.log(jsonResponse)
                },
                error: function(re){
                    console.log(re);
                }
            });
            $(this).closest('tr').remove();
        }
    });

    $('#reviewModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var booking_id = button.data('id');
        var modal = $(this);

        $.ajax({
            type:"get",
            dataType: "json",
            data:{"BOOKING_ID":booking_id},
            url: "cgi/controller/getBooking.php",
            success: function (book) {
                modal.find('.data_property_name').text(book.PROPERTY_NAME);
                modal.find('.data_booking_period').text(book.BOOKING_PERIOD);

            },
            error: function(re){
                console.log(re);
            }
        });


        modal.find(".review_save_btn").click(function(){
            var data = {"BOOKING_ID":booking_id,"REPLY_COMMENT_ID":null, "RATING":modal.find("#review_rating > option:selected").val(), "COMMENT": modal.find("#review_comment").val()};
            $.ajax({
                type:"post",
                dataType: "json",
                data:{"json":JSON.stringify(data)},
                url: "cgi/controller/createReview.php",
                success: function (jsonResponse) {

                    modal.modal('hide');
                },
                error: function(re){
                    console.log(re);
                }
            });
        });

    });

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