<?php
require "cgi/lib/Property.php";
require "cgi/lib/District.php";
require "cgi/lib/Misc.php";

$update = false;
$prop = null;
if( count($page_args) == 1  ){
    $update = true;
    $prop = Property::getProperty($page_args[0]);
}


// set page template variables
$page = [];
$page['page_name'] = basename(__FILE__, '.php');
$page['title']= $update? "Update Property" : "Add a new property";
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
        <h3><?=$update?"Edit Property":"Create a Listing on QBnB"?></h3>
        <div id="alert_container"></div>
        <div class="row">
            <div class="col-md-4">
                <h6 style="margin-top:0px;">Property Details</h6>
                <div class="form-group">
                    <label for="form_name">Listing Name</label>
                    <input type="text" class="form-control" value="<?=$update?$prop['NAME']:""?>" name="name" id="form_name">
                </div>
                <div class="form-group">
                    <label for="form_description">Description</label>
                    <textarea name="description" class="form-control" id="form_description" cols="4" rows="2"><?=$update?$prop['DESCRIPTION']:""?></textarea>
                </div>


                <div class="form-group">
                    <label for="form_address1">Address</label>
                    <input type="text" class="form-control" value="<?=$update?$prop['ADDRESS_1']:""?>" name="address1" id="form_address1">
                </div>
                <div class="form-group">
                    <label for="form_address2">City, State</label>
                    <input type="text" class="form-control" value="<?=$update?$prop['ADDRESS_2']:""?>" name="address2" id="form_address2">
                </div>

                <div class="form-group">
                    <label for="form_district">District</label>
                    <select class="form-control" name="district" id="form_district">
                        <?php
                        foreach (District::getDistricts() as $dt){
                            if ($prop["DISTRICT_ID"]==$dt['DISTRICT_ID'])
                                echo '<option selected value="'.$dt['DISTRICT_ID'].'">'.$dt['DISTRICT_NAME'].'</option>';
                            else echo '<option value="'.$dt['DISTRICT_ID'].'">'.$dt['DISTRICT_NAME'].'</option>';

                        }
                        ?>
                        <option value="-1" >Add a custom district</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="form_property_type">Property Type</label>
                    <select class="form-control" name="property_type" id="form_property_type">
                        <?php
                        foreach (PropertyType::getPropertyTypes() as $pt){
                            if ($prop["PROPERTY_TYPE_ID"]==$pt['PROPERTY_TYPE_ID'])
                                echo '<option selected value="'.$pt['PROPERTY_TYPE_ID'].'">'.$pt['PROPERTY_TYPE_NAME'].'</option>';
                            else echo '<option value="'.$pt['PROPERTY_TYPE_ID'].'">'.$pt['PROPERTY_TYPE_NAME'].'</option>';
                        }
                        ?>
                        <option value="-1" >Add a custom property type</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="form_price">Price (per week)</label>
                    <input type="number" class="form-control"  value="<?=$update?$prop['PRICE']:""?>" name="price" id="form_price">
                </div>



            </div>
            <div class="col-md-5">
                <h6 style="margin-top:0px;">Location</h6>
                <div id="map_location_picker" style="height: 300px"></div>
                <span style="float:right;">Correct the location by clicking on the map (if necessary)</span>
                <?php if ($update){ ?>
                    <input type="hidden" id="data_lat" value="<?=$prop['LAT']?>">
                    <input type="hidden" id="data_lng" value="<?=$prop['LNG']?>">
                <?php } ?>
                <br>
                <div id="pictures_upload_container" style="height: 300px">
                    <h6>Add pictures</h6>
                    <input type="file" id="file-chooser" />
                    <button id="add_picture_button" class="btn btn-primary"> Upload Image</button>
                    <img src="img/loading.gif" id="picture_upload_loading_gif">
                    <div id="picture_row">
                        <?php
                        if ($update) {
                            foreach ($prop['IMAGES'] as $i) {
                                ?>
                                <div class="preview" style='background-image:url(<?=$i?>);'></div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <h6 style="margin-top:0px;">Property Features</h6>
                <select class="form-control" id="add_feature_select">
                    <option value="-1" disabled selected>Choose a feature to add</option>
                    <option value="-2" >Add a custom feature</option>
                    <?php
                    foreach (Feature::getFeatures() as $ft){
                        echo '<option value="'.$ft['FEATURE_ID'].'">'.$ft['FEATURE_NAME'].'</option>';
                    }
                    ?>
                </select>
                <ul class="list-group" id="added_features">
                    <?php
                    if ($update){
                        foreach (Feature::getForProperty($page_args[0]) as $ft){
                            echo '<li class="list-group-item" data-id="'.$ft['FEATURE_ID'].'">'.$ft['FEATURE_NAME'].'</li>';
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>

        <div style="text-align: center;">
            <button type="submit" id="create_listing_btn" class="btn btn-success btn-lg"><?=$update?"Edit Listing":"Create Listing"?></button>
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
    var update = <?=$update?"true":"false"?>;
    <?php if ($update){ ?>
    var property_id = <?=$page_args[0]?>;
    <?php } ?>

    var alertContainer = $("#alert_container");
    var currentPictures = [];
    var map;
    var marker;
    var currentLocation;
    var currentFeatures = [];


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
            'LAT': currentLocation ? currentLocation.lat : null,
            'LNG': currentLocation ? currentLocation.lng : null
        };

        if (update){
//            console.log(currentLocation)
            $.ajax({
                type:"post",
                dataType: "json",
                data:{"json":JSON.stringify(data), PROPERTY_ID:property_id,"pictures":JSON.stringify(currentPictures), "features":JSON.stringify(currentFeatures)},
                url: "cgi/controller/updateProperty.php",
                success: function (jsonResponse) {
                    window.history.back();
                },
                error: function(re){
                    console.log(re);
                }
            });
        }else{
            $.ajax({
                type:"post",
                dataType: "json",
                data:{"json":JSON.stringify(data), "pictures":JSON.stringify(currentPictures), "features":JSON.stringify(currentFeatures)},
                url: "cgi/controller/createProperty.php",
                success: function (jsonResponse) {
                    var newID = jsonResponse.data;
                    window.location = "?p=listing/"+newID;
                },
                error: function(re){
                    //console.log(re);
                }
            });
        }


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
        var fileChooser = document.getElementById('file-chooser');
        var file = fileChooser.files[0];
        if (file && file.type.indexOf("image")>-1) {
            $("#picture_upload_loading_gif").show();
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
                    var pic_ele = $("<div class='preview' style='background-image:url("+url+");'></div>");
                    pic_ele.click(function(){
                        var c = confirm("Remove this image?");
                        if (c){
                            var index = $.inArray(url, currentPictures);
                            currentPictures.splice(index, 1);
                            pic_ele.remove();
                        }
                    });
                    $("#picture_row").append(pic_ele);
                    $("#picture_upload_loading_gif").hide();
                }
            });
        }else{
             alert('Please select an image file');
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
            var loc = {lat:event.latLng.lat(),lng:event.latLng.lng()};
            placeMarker(loc);
        });


        // setup stuff if updating
        if (update){
            $("#picture_row").children(".preview").each(function(){
                var pic_ele = $(this);
                var bg = pic_ele.css('background-image');
                bg = bg.replace('url(','').replace(')','');
                bg  = bg.replace(/^"/, '');
                bg  = bg.replace(/\"$/, '');
                currentPictures.push(bg);
                pic_ele.click(function(){
                    var c = confirm("Remove this image?");
                    if (c){
                        var index = $.inArray(bg, currentPictures);
                        currentPictures.splice(index, 1);
                        pic_ele.remove();
                    }
                });
            });

            $("#added_features").children(".list-group-item").each(function(){
                var li_ele = $(this);
                currentFeatures.push(li_ele.attr("data-id"));
                li_ele.click(function(){
                    var c = confirm("Remove this feature?");
                    if (c){
                        var index = $.inArray(li_ele.attr("data-id"), currentFeatures);
                        currentFeatures.splice(index, 1);
                        li_ele.remove();
                    }
                });
            });

            var loc = { lat:parseFloat($("#data_lat").val()), lng:parseFloat($("#data_lng").val())};
            console.log(loc);
            map.setCenter(loc);
            map.setZoom(12);
            placeMarker(loc);
        }


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



    $("#add_feature_select").change(function(){
        var ft_id = $('#add_feature_select :selected').val();

        if (parseInt(ft_id) === -2){
            var feature = prompt("Enter a custom property feature", "");
            if (feature != null) {
                var data = {"FEATURE_NAME":feature};
                $.ajax({
                    type:"post",
                    dataType: "json",
                    data:{"json":JSON.stringify(data)},
                    url: "cgi/controller/createFeature.php",
                    success: function (jsonResponse) {
                        console.log("Done");
                        console.log(jsonResponse);
                        var newID = jsonResponse.data;
                        ft_id = newID;
                    }, error: function(re){
                        console.log(re);
                    }
                });

            }
        }

        var ft_name = $('#add_feature_select :selected').text();
        var ele = $('<li class="list-group-item" data-id="'+ft_id+'">'+ft_name+'</li>');
        ele.click(function(){
            var c = confirm("Remove this feature?");
            if (c){
                var index = $.inArray(ft_id, currentFeatures);
                currentFeatures.splice(index, 1);
                ele.remove();
            }
            console.log(currentFeatures);
        });
        $("#added_features").append(ele);
        currentFeatures.push(ft_id);

        $(this).val(-1);
    });


    $("#form_district").change(function(){
        var ft_id = $('#form_district :selected').val();

        if (parseInt(ft_id) === -1){
            var dis = prompt("Enter a district", "");
            if (dis != null) {
                var data = {"DISTRICT_NAME":dis};
                $.ajax({
                    type:"post",
                    dataType: "json",
                    data:{"json":JSON.stringify(data)},
                    url: "cgi/controller/createDistrict.php",
                    success: function (jsonResponse) {
                        console.log("Done");
                        console.log(jsonResponse);
                        var newID = jsonResponse.data;
                        $("#form_district").prepend("<option value='"+newID+"'>"+dis+"</option>");
                        $("#form_district").val(newID);
                    }, error: function(re){
                        console.log(re);
                    }
                });

            }
        }
    });

    $("#form_property_type").change(function(){
        var ft_id = $('#form_property_type :selected').val();

        if (parseInt(ft_id) === -1){
            var dis = prompt("Enter a new property type", "");
            if (dis != null) {
                var data = {"PROPERTY_TYPE_NAME":dis};
                $.ajax({
                    type:"post",
                    dataType: "json",
                    data:{"json":JSON.stringify(data)},
                    url: "cgi/controller/createPropertyType.php",
                    success: function (jsonResponse) {
                        console.log("Done");
                        console.log(jsonResponse);
                        var newID = jsonResponse.data;
                        $("#form_property_type").prepend("<option value='"+newID+"'>"+dis+"</option>");
                        $("#form_property_type").val(newID);
                    }, error: function(re){
                        console.log(re);
                    }
                });

            }
        }
    });



</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCvQjc_dNIaallkLt9Xe0PEaKSqsRPWEXQ&callback=initMap" async defer></script>
<?php
$page['scripts']= ob_get_contents();
ob_end_clean();
?>
