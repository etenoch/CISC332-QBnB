<?php
require "cgi/lib/Member.php";
require "cgi/lib/Property.php";
require "cgi/lib/Misc.php";

$logged_in=false;
if (isset($_SESSION['MEMBER_ID'])){
    $logged_in = true;
}

// set page template variables
$page = [];
$page['page_name'] = basename(__FILE__, '.php');
$page['title']= "Manage Properties";
$page['head']= "";

// JS
ob_start();
?>
<?php
$page['scripts']= ob_get_contents();
ob_clean();

// page content
ob_start();

if ($logged_in){
    $member_id = $_SESSION['MEMBER_ID'];

    ?>
<div class="container under_top_bar">
    <h3>Manage Properties</h3>
    <table class="table table-bordered">
        <tr>
            <th>Name</th><th>Address</th><th>Type</th><th>Price</th><th>Reviews</th><th>Manage</th><th>Delete</th>
        </tr>
        <?php
        foreach (Property::getMemberProperties($member_id) as $prop){
            ?>
            <tr>
                <td><?=$prop['PROPERTY_NAME']?></td>
                <td><?=$prop['ADDRESS_1']?></td>
                <td><?=$prop['PROPERTY_TYPE_NAME']?></td>
                <td>$<?=$prop['PRICE']?></td>
                <td><a href="?p=reviews/<?=$prop['PROPERTY_ID']?>" class="btn btn-inverse btn-xs">Reviews</a></td>
                <td><a href="?p=create/<?=$prop['PROPERTY_ID']?>" class="btn btn-info btn-xs">Manage</a></td>
                <td><a href="?p=delete/<?=$prop['PROPERTY_ID']?>" class="btn btn-danger btn-xs">Delete</a></td>
            </tr>
            <?php
        }
        ?>
    </table>
</div>
<?php
}else{
    echo "<div class='container under_top_bar'><h4>Please login first <a href='?p=login'>here</h4></div>";
}
$page['body']= ob_get_contents();
ob_end_clean();
?>
