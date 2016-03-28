<?php
require_once "cgi/lib/Property.php";
require_once "cgi/lib/District.php";
require_once "cgi/lib/Member.php";
require_once "cgi/lib/Misc.php";


// fetch correct property
$property_id = $page_args[0];
$prop = Property::getProperty($property_id);
$mem = Member::getMember($prop['MEMBER_ID']);

// set page template variables
$page = [];
$page['page_name'] = basename(__FILE__, '.php');
$page['title']= $prop["PROPERTY_NAME"];
$page['head']= "<link rel=\"stylesheet\" href=\"css/vendor/jquery-ui.min.css\">
<link rel=\"stylesheet\" href=\"css/vendor/jquery-ui.structure.min.css\">
<link rel=\"stylesheet\" href=\"css/vendor/jquery-ui.theme.min.css\">";

// page content
ob_start();
?>
<div class="container under_top_bar" >
    <h3>Property: <?=$prop['PROPERTY_NAME']?></h3>
    <h6><?=$prop['PROPERTY_TYPE_NAME']?></h6>

    <div id="alert_container"></div>

    <div class="row">
        <div class="col-md-4">

            <div class="booking_container">
                <h5>Property Description</h5>
                <div class="prop_description">
                    <?=$prop['DESCRIPTION']?>
                </div>
            </div>

            <div class="booking_container">
                <div class="prop_address">
                    <h5>Location</h5>
                    <?=$prop['DISTRICT_NAME']?><br/>
                    <?=$prop['ADDRESS_1']?><br/>
                    <?=$prop['ADDRESS_2']?>
                </div>
            </div>

            <div class="booking_container">
                <h5>Features</h5>
                <ul class="list-group" id="added_features">
                <?php
                foreach (Feature::getForProperty($property_id) as $ft){
                    echo '<li class="list-group-item">'.$ft['FEATURE_NAME'].'</li>';
                }
                ?>
                </ul>
            </div>


        </div>
        <div class="col-md-4">
            <div class="booking_container">
                <h5>Book this property - $<?=$prop['PRICE']?></h5>
                <div id="cal_container" style="font-size:70%;">
                    <div id="datepicker"></div>
                </div>
                <div id="booking_message">

                </div>

                <button class="action_button btn btn-info btn-block disabled" id="booknow_btn">Request Booking</button>
            </div>

            <div class="booking_container">
                <h5>Hosted By: <?=$prop['NAME']?></h5>
                <span class="member_info"><?=$mem['FACULTY_NAME']?> - <?=$mem['DEGREE_TYPE_NAME']?> <?=$mem['GRAD_YEAR']?></span><br/>
                <span class="member_info">Email: <a href="mailto:<?= $prop['EMAIL'] ?>"><?=$prop['EMAIL']?></a></span>
                <a href="?p=member/<?=$prop['MEMBER_ID']?>" class="action_button btn btn-info btn-block">View member profile</a>
            </div>
        </div>
        <div class="col-md-4">
            <div id="carousel_property_pictures" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                    <?php
                    $i = 0;
                    while($i < count($prop['IMAGES']) && count($prop['IMAGES'])>1 ) {
                        ?><li data-target="#carousel_property_pictures" data-slide-to="<?=$i?>" class="<?php if ($i==0)echo 'active'?>"></li><?php
                        $i++;
                    }
                    ?>
                </ol>

                <div class="carousel-inner" role="listbox">
                    <?php
                    $first = true;
                    foreach($prop['IMAGES'] as $i) {
                        ?>
                        <div class="item <?php if ($first) echo 'active'?>">
                            <img src="<?=$i?>" />
                        </div>
                        <?php
                        $first=false;
                    }
                    ?>
                </div>
                <?php
                if(count($prop['IMAGES'])>1) {
                    ?>
                    <a class="left carousel-control" href="#carousel_property_pictures" role="button" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="right carousel-control" href="#carousel_property_pictures" role="button"
                       data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                    <?php
                }
                ?>
            </div>
            <div id="listing_map" style="height: 300px;"></div>

        </div>
    </div>


    <br/>

    <div class="row">

        <div class="col-md-6">
            <h3>Nearby</h3>
            <h4><?=$prop['DISTRICT_NAME']?></h4>
            <ul>
            <?php
            foreach (PointOfInterest::getForProperty($prop['PROPERTY_ID']) as $poi){
                echo "<li>".$poi['POINT_OF_INTEREST_NAME']."</li>";
            }
            ?>
            </ul>
        </div>
        <div class="col-md-6">
            <h3>Reviews</h3>
            <?php
            foreach (Review::getPropertyTopLevelReviews($prop['PROPERTY_ID']) as $re){
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


    </div>

</div>
<?php
$page['body']= ob_get_contents();
ob_clean();


// JS
ob_start();
?>
<script src="js/vendor/jquery-ui.min.js"></script>
<script>
    var property_id=<?=$property_id?>;

    var alertContainer = $("#alert_container");

    var currentDate;

    $(function() {
        $( "#datepicker" ).datepicker({
            dateFormat: 'yy-m-d',
            onSelect: function(selectedDate) {
                currentDate = selectedDate;
                var date = selectedDate.split("-");
                var uri = "cgi/controller/checkDate.php?year="+date[0]+"&month="+date[1]+"&day="+date[2]+"&property_id="+property_id;
                console.log(uri);
                $.ajax({
                    type:"get",
                    dataType: "json",
                    url: uri,
                    success: function (jsonResponse) {
                        console.log(jsonResponse);
                        var goodDate = jsonResponse.data;
                        if (!goodDate){
                            $("#booking_message").text("Looks like someone has already booked that date.");
                            $("#booknow_btn").addClass("disabled");
                        }else{
                            $("#booking_message").text("Booking for 7 days starting "+selectedDate);
                            $("#booknow_btn").removeClass("disabled");
                        }
                    },
                    error: function(re){
//                console.log(re);
                    }
                });
                $("#booking_message").text(selectedDate);
            }
        });
    });


    $("#booknow_btn").click(function(){
        var data = {
            PROPERTY_ID:property_id,
            BOOKING_PERIOD:currentDate
        };
        $.ajax({
            type:"post",
            dataType: "json",
            data:{"json":JSON.stringify(data)},
            url: "cgi/controller/createBooking.php",
            success: function (jsonResponse) {
                alertContainer.append('<div class="alert alert-success" role="alert">Booking has been requested</div>');
                $("#booking_message").text("");
                $("#booknow_btn").addClass("disabled");
            },
            error: function(re){
//                console.log(re);
            }
        });

    });

    var loc = null;
    <?php if($prop['LAT'] && $prop['LNG']) {?>
        var lat = <?=$prop['LAT']?>;
        var lng = <?=$prop['LNG']?>;
        loc = {lat:lat,lng:lng};
    <?php }?>

    function initMap() {
        if (loc){

            var map = new google.maps.Map(document.getElementById('listing_map'), {
                center: loc,
                zoom: 12
            });
            new google.maps.Marker({
                position: loc,
                map: map
            });
        }

    }// end init map

</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCvQjc_dNIaallkLt9Xe0PEaKSqsRPWEXQ&callback=initMap" async defer></script>

<?php
$page['scripts']= ob_get_contents();
ob_end_clean();
?>
