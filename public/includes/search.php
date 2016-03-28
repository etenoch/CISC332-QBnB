<?php


// set page template variables
$page = [];
$page['page_name'] = basename(__FILE__, '.php');
$page['title']= "Search Listings";
$page['head']= "";


// page content
ob_start();
?>
<!-- a) Search accommodations by district, type, features, price. -->
<div class="container under_top_bar">
    <h3>Search & Filter Listings</h3>
    <div class="row">
        <div class="col-md-3">

            <select id="search_select" class="form-control">
                <option value="-1" selected disabled>Add a filter</option>
                <option value="district">District</option>
                <option value="property_type">Type</option>
                <option value="features">Features</option>
                <option value="price">Price</option>
            </select>
            <img src="img/loading.gif" id="loading_gif">
            <br/>
            <a href="?p=search" class="btn btn-info" style="margin-top:6px;">Reset Filters</a>
            <div id="filter_container">

            </div>
        </div>
        <div class="col-md-9">
            <div id="prop_container">
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
    var allProperties = [];
    var propertyMap = {};
    var allFeatures = [];
    var allDistricts = [];
    var allPropertyTypes = [];
    var allPrices = [];

    var propertiesContainer = $("#prop_container");

    var loadingGif = $("#loading_gif");
    var filterContainer = $("#filter_container");

    // get and populate all properties
    $.ajax({
        type:"get",
        dataType: "json",
        url: "cgi/controller/getAllProperties.php",
        success: function (properties) {
            allProperties = properties;
            allProperties.forEach(function(property){
                propertyMap[parseInt(property.PROPERTY_ID)] = property;
                console.log(property);

                property.FEATURES.forEach(function(feature){
                    allFeatures.push(feature.FEATURE_NAME);
                });
                allDistricts.push(property.DISTRICT_NAME);
                allPropertyTypes.push(property.PROPERTY_TYPE_NAME);
                allPrices.push(property.PRICE);

                allFeatures = removeDuplicates(allFeatures);
                allDistricts = removeDuplicates(allDistricts);
                allPropertyTypes = removeDuplicates(allPropertyTypes);
                allPrices = removeDuplicates(allPrices);

                propertiesContainer.append(createListingItem(property));
            });
            loadingGif.hide();
        },
        error: function(re){
            //console.log(re);
        }
    });

    function createListingItem(property){
        var image = property.IMAGES.length >0 ? property.IMAGES[0]:"https://s3.amazonaws.com/qbnb-uploads/property_placeholder.jpg";

        return $('<a href="?p=listing/'+property.PROPERTY_ID+'"> \
            <div data-id="'+property.PROPERTY_ID+'" class="property_card"> \
            <div class="img_preview" style="background-image:url('+image+');">&nbsp;</div> \
            <div class="details"> \
            <h5 class="name">'+property.PROPERTY_NAME+'</h5> \
            <span class="first_row">'+property.DISTRICT_NAME+'</span> \
            <span class="second_row">$'+property.PRICE+' - '+property.PROPERTY_TYPE_NAME+'</span> \
            </div> \
            </div> \
            </a>');
    }


    // filtering
    $("#search_select").change(function(){
        var selected = $(this).find("option:selected").val();
        if(selected == -1){
        }else if (selected=="district"){
            filterContainer.append(createDistrictFilter());
        }else if (selected=="property_type"){
            filterContainer.append(createPropertyTypeFilter());
        }else if (selected=="features"){
            filterContainer.append(createFeaturesFilter());
        }else if (selected=="price"){
            filterContainer.append(createPriceFilter());
        }
        $(this).val(-1);
    });

    function createDistrictFilter(){
        var ele = $('<div class="filter_item"> \
        <h6><span class="filter_type">District</span> Filter<br/></h6> \
        <select data-filter-type="district" class="filter_data"> \
            <option value="-1" selected disabled>Select Item</option> \
        </select> \
        </div>');
        var sl = ele.find('.filter_data');
        allDistricts.forEach(function(district){
            console.log(district);
            sl.append("<option value='"+district+"'>"+district+"</option>");
        });
        sl.change(function(){
            var dis = sl.find("option:selected").val();
            propertiesContainer.find(".property_card").each(function(){
                var id = parseInt($(this).data("id"));
                if(!filterby.district(propertyMap[id],dis)) $(this).remove();
            });
        });
        return ele;
    }

    function createPropertyTypeFilter(){
        var ele = $('<div class="filter_item"> \
        <h6><span class="filter_type">Property Type</span> Filter<br/></h6> \
        <select data-filter-type="propertyType" class="filter_data"> \
            <option value="-1" selected disabled>Select Item</option> \
        </select> \
        </div>');
        var sl = ele.find('.filter_data');
        allPropertyTypes.forEach(function(pt){
            console.log(pt);
            sl.append("<option value='"+pt+"'>"+pt+"</option>");
        });
        sl.change(function(){
            var dis = sl.find("option:selected").val();
            propertiesContainer.find(".property_card").each(function(){
                var id = parseInt($(this).data("id"));
                if(!filterby.propertyType(propertyMap[id],dis)) $(this).remove();
            });
        });
        return ele;
    }

    function createFeaturesFilter(){
        var ele = $('<div class="filter_item"> \
        <h6><span class="filter_type">Feature</span> Filter<br/></h6> \
        <select data-filter-type="feat" class="filter_data"> \
            <option value="-1" selected disabled>Select Item</option> \
        </select> \
        </div>');
        var sl = ele.find('.filter_data');
        allFeatures.forEach(function(feat){
            console.log(feat);
            sl.append("<option value='"+feat+"'>"+feat+"</option>");
        });
        sl.change(function(){
            var dis = sl.find("option:selected").val();
            propertiesContainer.find(".property_card").each(function(){
                var id = parseInt($(this).data("id"));
                if(!filterby.feature(propertyMap[id],dis)) $(this).remove();
            });
        });
        return ele;
    }

    function createPriceFilter(){
        var ele = $('<div class="filter_item"> \
        <h6><span class="filter_type">District</span> Filter<br/></h6> \
        Low <input type="text" class="filter_low" value="0">\
        High <input type="text" class="filter_high" value="1000"> \
        <button class="btn btn-info go_btn">Filter</button>\
        </div>');
        ele.find(".go_btn").click(function(){
            var low = ele.find(".filter_low").val();
            var high = ele.find(".filter_high").val();
            console.log(low,high);
            propertiesContainer.find(".property_card").each(function(){
                var id = parseInt($(this).data("id"));
                if(!filterby.price(propertyMap[id],low,high)) $(this).remove();
            });
        });
        return ele;
    }

    var filterby = {
        district: function(property, district){
            return property.DISTRICT_NAME == district;
        },
        propertyType: function(property, propertyType){
            return property.PROPERTY_TYPE_NAME == propertyType;
        },
        feature: function(property, feature){
            var found = false;
            property.FEATURES.forEach(function(ft){
                 if (ft.FEATURE_NAME == feature) found = true;
            });
            return found;
        },
        price: function(property, price_low,price_high){
            return parseInt(price_low)<=parseInt(property.PRICE) && parseInt(property.PRICE)<=parseInt(price_high);
        }
    };


    function removeDuplicates(names){
        var uniqueNames = [];
        $.each(names, function(i, el){
            if($.inArray(el, uniqueNames) === -1) uniqueNames.push(el);
        });
        return uniqueNames;
    }

</script>
<?php
$page['scripts']= ob_get_contents();
ob_end_clean();
?>





