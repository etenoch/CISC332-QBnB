<?php
require "cgi/lib/Property.php";
require "cgi/lib/District.php";


// fetch correct property
$property_id = $page_args[0];
$prop = Property::getProperty($property_id);

// set page template variables
$page = [];
$page['page_name'] = basename(__FILE__, '.php');
$page['title']= "-- listing name here --";
$page['head']= "<link rel=\"stylesheet\" href=\"css/vendor/clndr.css\">";

// JS
ob_start();
?>
<script src="js/vendor/underscore.js"></script>
<script src="js/vendor/moment.js"></script>
<script src="js/vendor/clndr.js"></script>
<script>
//    $('#cal_container').clndr();

</script>
<?php
$page['scripts']= ob_get_contents();
ob_clean();

// page content
ob_start();
?>
<div class="container under_top_bar" >
    <h3>Property: <?=$prop['PROPERTY_NAME']?></h3>
    <h6><?=$prop['PROPERTY_TYPE_NAME']?></h6>

    <div class="row">
        <div class="col-md-5">

            <div class="booking_container">
                <h5>Book this property - <?=$prop['PRICE']?></h5>

                <div class="form-group">
                    <label for="form_date">Date</label>
                    <input type="text" class="form-control" name="date" id="form_date">
                </div>
                <div id="cal_container">

                </div>


                <button class="book_property_btn btn btn-info btn-block">Book Now</button>
            </div>


            <div class="prop_description">
                <?=$prop['DESCRIPTION']?>
            </div>

            <div class="prop_address">
                <h5>Location</h5>
                <?=$prop['DISTRICT_NAME']?><br/>
                <?=$prop['ADDRESS_1']?><br/>
                <?=$prop['ADDRESS_2']?>
            </div>



        </div>
        <div class="col-md-7">

            <div id="carousel_property_pictures" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                    <?php
                    $i = 0;
                    while($i < count($prop['IMAGES'])) {
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
                    <div class="item <?php if ($first)echo 'active'?>">
                        <img src="<?=$i?>" />
                    </div>
                    <?php
                    $first=false;
                    }
                    ?>
                </div>

                <a class="left carousel-control" href="#carousel_property_pictures" role="button" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#carousel_property_pictures" role="button" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>

        </div>
    </div>

</div>
<?php
$page['body']= ob_get_contents();
ob_end_clean();
?>
