<?php
require "cgi/lib/Property.php";
require "cgi/lib/District.php";

// set page template variables
$page = [];
$page['page_name'] = basename(__FILE__, '.php');
$page['title']= "Manage Listing";
$page['head']= "<link rel=\"stylesheet\" href=\"css/create.css\">";

// JS
ob_start();
?>
<?php
$page['scripts']= ob_get_contents();
ob_clean();

// page content
ob_start();

if (!isset($_SESSION['MEMBER_ID'])){
    ?>
    <div class="container under_top_bar">
        <h3>Create a Listing on QBnB</h3>
        <a href="?p=login" class="btn btn-primary">Please login first</a>

    </div>
    <?php
}else{
    ?>
    <div class="container under_top_bar">
        <h3>Create a Listing on QBnB</h3>
        <div id="alert_container"></div>
        <div class="row">
            <div class="col-md-4">

                <div class="form-group">
                    <label for="form_name">Listing Name</label>
                    <input type="text" class="form-control" name="name" id="form_name">
                </div>
                <div class="form-group">
                    <label for="form_description">Description</label>
                    <textarea name="description" class="form-control" id="form_description" cols="4" rows="2"></textarea>
                </div>


                <div class="form-group">
                    <label for="form_address1">Address</label>
                    <input type="text" class="form-control" name="address1" id="form_address1">
                </div>
                <div class="form-group">
                    <label for="form_address2">City, State</label>
                    <input type="text" class="form-control" name="address2" id="form_address2">
                </div>

                <div class="form-group">
                    <label for="form_district">District</label>
                    <select class="form-control" name="district" id="form_district">
                        <?php
                        foreach (District::getDistricts() as $dt){
                            echo '<option value="'.$dt['DISTRICT_ID'].'">'.$dt['DISTRICT_NAME'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="form_property_type">Property Type</label>
                    <select class="form-control" name="property_type" id="form_property_type">
                        <?php
                        foreach (PropertyType::getPropertyTypes() as $pt){
                            echo '<option value="'.$pt['PROPERTY_TYPE_ID'].'">'.$pt['PROPERTY_TYPE_NAME'].'</option>';
                        }
                        ?>                    </select>
                </div>
                <div class="form-group">
                    <label for="form_price">Price (per week)</label>
                    <input type="number" class="form-control" name="price" id="form_price">
                </div>


                <button type="submit" id="create_listing_btn" class="btn btn-success">Create Listing</button>

            </div>
            <div class="col-md-8">
                <div id="map_location_picker" style="height: 300px"></div>
                <span style="float:right;">Correct the location by clicking on the map (if necessary)</span>
                <br>
                <div id="pictures_upload_container" style="height: 300px">
                    <h4>Add pictures</h4>
                    <input type="file" id="file-chooser" />
                    <button id="add_picture_button" class="btn btn-primary"> Upload Image</button>
                    <img src="img/loading.gif" id="picture_upload_loading_gif">
                    <div id="picture_row"></div>
                </div>
            </div>
        </div>



    </div>
    <?php
}
?>

<?php
$page['body']= ob_get_contents();
ob_clean();

// JS
ob_start();
?>
<script src="https://sdk.amazonaws.com/js/aws-sdk-2.1.12.min.js"></script>
<script>
    var alertContainer = $("#alert_container");
    var currentPictures = [];
    var map;
    var marker;
    var currentLocation;

    $("#create_listing_btn").click(function(){
        var name = $("#form_name");
        var description = $("#form_description");
        var address1 = $("#form_address1");
        var address2 = $("#form_address2");
        var district = $("#form_district");
        var property_type = $("#form_property_type");
        var price = $("#form_price");

        //TODO validate data

        var data = {
            'ADDRESS_1': address1.val(),
            'ADDRESS_2': address2.val(),
            'DISTRICT_ID': district.val(),
            'PROPERTY_TYPE_ID': property_type.val(),
            'PRICE': price.val(),
            'NAME': name.val(),
            'DESCRIPTION': description.val(),
            'LAT': currentLocation.lat,
            'LNG': currentLocation.lng
        };

        $.ajax({
            type:"post",
            dataType: "json",
            data:{"json":JSON.stringify(data), "pictures":JSON.stringify(currentPictures)},
            url: "cgi/controller/createProperty.php",
            success: function (jsonResponse) {
                console.log(jsonResponse);
            },
            error: function(re){
                console.log(re);
            }
        });


    });

    // aws upload stuff
    AWS.config.region = 'us-east-1'; // 1. Enter your region
    AWS.config.credentials = new AWS.CognitoIdentityCredentials({
        IdentityPoolId: 'us-east-1:e2342559-c670-4487-add6-6df788d051b9' // 2. Enter your identity pool
    });
    AWS.config.credentials.get(function(err) {
        if (err) alert(err);
        console.log(AWS.config.credentials);
    });
    var bucketName = 'qbnb-uploads'; // Enter your bucket name
    var bucket = new AWS.S3({
        params: {
            Bucket: bucketName
        }
    });
    $("#add_picture_button").click(function(){
        $("#picture_upload_loading_gif").show();
        var fileChooser = document.getElementById('file-chooser');
        var file = fileChooser.files[0];
        if (file) {
            var objKey = "img_"+Math.floor(Date.now() / 1000)+"_" + file.name;
            var params = {
                Key: objKey,
                ContentType: file.type,
                Body: file,
                ACL: 'public-read'
            };
            bucket.putObject(params, function(err, data) {
                if (err){
                     alert('ERROR: ' + err);
                }else{
                    var url = "https://qbnb-uploads.s3.amazonaws.com/"+encodeURIComponent(objKey);
                    currentPictures.push(url);
                    $("#picture_row").append("<div class='preview' style='background-image:url("+url+");'>&nbsp;</div>");
                    $("#picture_upload_loading_gif").hide();
                }
            });
        }else{
             alert('Nothing to upload.');
        }
    });



    // map stuff
    function initMap() {
        map = new google.maps.Map(document.getElementById('map_location_picker'), {
            center: {lat: 56, lng: -80},
            zoom: 3,
            mapTypeControlOptions: {
                style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                position: google.maps.ControlPosition.BOTTOM_LEFT
            }
        });
        google.maps.event.addListener(map, 'click', function(event) {
            placeMarker(event.latLng);
        });
    }// end init map


    $("#form_address1").change(function(){
        if ($("#form_address2").val()) {
            geocode($(this).val()+" "+$("#form_address2").val());
        }else {
            geocode($(this).val());
        }
    });

    $("#form_address2").change(function(){

        if ($("#form_address1").val()) geocode($("#form_address1").val()+" "+$(this).val());
    });


    function geocode(ser){
        var url = "https://maps.googleapis.com/maps/api/geocode/json?&address="+encodeURIComponent(ser);
        $.ajax({
            type:"get",
            dataType: "json",
            url: url,
            success: function (jsonResponse) {
                var results = jsonResponse.results;
                if (results.length>0){
                    var ll = {lat:results[0].geometry.location.lat,lng:results[0].geometry.location.lng};
                    map.setCenter(ll);
                    map.setZoom(12);
                    placeMarker(ll);
                }
            },
            error: function(re){
                console.log(re);
            }
        });
    }

    function placeMarker(location) {
        currentLocation = location;
        if ( marker ) {
            marker.setPosition(location);
        } else {
            marker = new google.maps.Marker({
                position: location,
                map: map
            });
        }
    }



</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCvQjc_dNIaallkLt9Xe0PEaKSqsRPWEXQ&callback=initMap" async defer></script>
<?php
$page['scripts']= ob_get_contents();
ob_end_clean();
?>
