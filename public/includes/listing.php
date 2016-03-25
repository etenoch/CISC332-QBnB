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
$page['head']= "";

// JS
ob_start();
?>
<?php
$page['scripts']= ob_get_contents();
ob_clean();

// page content
ob_start();
?>
<div class="container under_top_bar" >
    <h3>Property: <?=$prop['PROPERTY_NAME']?></h3>

    <div class="row">
        <div class="col-md-5">



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
