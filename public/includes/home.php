<?php
require "cgi/lib/Property.php";


// set page template variables
$page = [];
$page['page_name'] = basename(__FILE__, '.php');
$page['title']= "Queen's Alumni Rentals";
$page['head']= "<link rel=\"stylesheet\" href=\"css/home.css\">";

// JS
ob_start();
?>
<script>
    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: 56, lng: -80},
            zoom: 3,
            mapTypeControlOptions: {
                style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                position: google.maps.ControlPosition.BOTTOM_LEFT
            }
        });

    }// end init map
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCvQjc_dNIaallkLt9Xe0PEaKSqsRPWEXQ&callback=initMap" async defer></script>
<?php
$page['scripts']= ob_get_contents();
ob_clean();

// page content
ob_start();
?>

<div class="row" id="map_column_container">
    <div class="col-md-5" id="map_column"><div id="map"></div></div>
    <div class="col-md-7" id="search_results_column">
        <div id="search_results_inner">
            <input type="text" id="home_search_box" class="form-control"/>
            <?=json_encode(Property::getAllProperties())?>

        </div>

    </div>
</div>


<?php
$page['body']= ob_get_contents();
ob_end_clean();
?>
