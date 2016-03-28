<?php
require_once "cgi/lib/Member.php";
require_once "cgi/lib/Property.php";
require_once "cgi/lib/Misc.php";
require_once "cgi/lib/Booking.php";
require_once "cgi/lib/District.php";

$property_id = $page_args[0];
$prop = Property::getProperty($property_id);



$logged_in=false;
if (isset($_SESSION['MEMBER_ID'])){
    $logged_in = true;
}

// set page template variables
$page = [];
$page['page_name'] = basename(__FILE__, '.php');
$page['title']= "Reviews";
$page['head']= "";

// page content
ob_start();

if ($logged_in){
    $member_id = $_SESSION['MEMBER_ID'];

    ?>
<div class="container under_top_bar">
    <h3>Manage reviews for property: <?=$prop['PROPERTY_NAME']?></h3>
    <div class="row">
        <div class="col-md-5">
        <?php
        foreach (Review::getPropertyTopLevelReviews($prop['PROPERTY_ID']) as $re){
            echo "<div class='review_card'><h6>".$re['COMENT_TEXT']."</h6>".$re['NAME']." - ".$re['RATING']." stars";

            echo "<div class='reply_container'>";
            foreach(Review::getReplies($re['COMMENT_ID']) as $rp){
                echo "<div class='review_card'><h6>".$rp['COMENT_TEXT']."</h6>".$rp['NAME']."</div>";
            }
            echo "</div><br/><button class='btn btn-info reply_button' data-toggle='modal' data-target='#reviewModal' data-id='".$re['COMMENT_ID']."' data-booking-id='".$re['BOOKING_ID']."'>Reply</button>";
            echo "</div>";
        }
        ?>
        </div>
    </div>

</div>

<!-- Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Reply to a review</h4>
            </div>
            <div class="modal-body">
                <textarea  id="review_comment" cols="10" rows="3" class="form-control"></textarea>
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

    $('#reviewModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var reply_comment_id = button.data('id');
        var booking_id = button.data('booking-id');
        var modal = $(this);

        modal.find(".review_save_btn").click(function(){
            var data = {"BOOKING_ID":booking_id,"REPLY_COMMENT_ID":reply_comment_id, "RATING":null, "COMMENT": modal.find("#review_comment").val()};
            $.ajax({
                type:"post",
                dataType: "json",
                data:{"json":JSON.stringify(data)},
                url: "cgi/controller/createReview.php",
                success: function (jsonResponse) {
                    button.parent().find(".reply_container").append("<div class='review_card'><h6>"+modal.find("#review_comment").val()+"</h6></div>");
                    modal.modal('hide');
                },
                error: function(re){
                    console.log(re);
                }
            });
        });

    });
</script>

<?php
$page['scripts']= ob_get_contents();
ob_end_clean();
?>