<?php
require_once "cgi/lib/Property.php";


// set page template variables
$page = [];
$page['page_name'] = basename(__FILE__, '.php');
$page['title']= "Queen's Alumni Rentals";
$page['head']= "<link rel=\"stylesheet\" href=\"css/home.css\">";


// page content
ob_start();
?>

<div class="row" id="map_column_container">
    <div class="col-md-5" id="map_column"><div id="map"></div></div>
    <div class="col-md-7" id="search_results_column">
        <div id="search_results_inner">
<!--            <input type="text" id="home_search_box" class="form-control"/>-->
            <h3 style="margin: 1rem;">Featured Properties</h3>
            <div id="results_container">
                <?php

                foreach(Property::getAllProperties() as $p){
                    $image_url = !empty($p['IMAGES']) ? $p['IMAGES'][0] : "https://s3.amazonaws.com/qbnb-uploads/property_placeholder.jpg";
                    ?>
                    <a href="?p=listing/<?=$p['PROPERTY_ID']?>">
                        <div class="property_card">
                            <div class="img_preview" style="background-image:url(<?=$image_url?>);">&nbsp;</div>
                            <div class="details">
                                <h5 class="name"><?=$p['PROPERTY_NAME']?></h5>
                                <span class="first_row"><?=$p['DISTRICT_NAME']?></span>
                                <span class="second_row">$<?=$p['PRICE']." - ".$p['PROPERTY_TYPE_NAME']?></span>
                            </div>

                        </div>
                    </a>
                    <?php
                }
                ?>
            </div>


        </div>

    </div>
</div>


<?php
$page['body']= ob_get_contents();
ob_clean();

// JS
ob_start();
?>
<script>
    var map;
    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: 56, lng: -80},
            zoom: 3,
            mapTypeControlOptions: {
                style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                position: google.maps.ControlPosition.BOTTOM_LEFT
            }
        });

        $.ajax({
            type:"get",
            dataType: "json",
            url: "cgi/controller/getAllProperties.php",
            success: function (props) {
                props.forEach(function(p){
                    if (p.LAT && p.LNG){
                        var loc = {lat:parseFloat(p.LAT) ,lng: parseFloat(p.LNG)};
                        console.log(p);
                        new google.maps.Marker({
                            position: loc,
                            map: map
                        });
                    }

                });
            }, error: function(re){
//                console.log(re);
            }
        });


    }// end init map


</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCvQjc_dNIaallkLt9Xe0PEaKSqsRPWEXQ&callback=initMap" async defer></script>
<?php
$page['scripts']= ob_get_contents();
ob_end_clean();
?>
