<?php
require_once "cgi/lib/Member.php";
require_once "cgi/lib/Property.php";
require_once "cgi/lib/Misc.php";

$logged_in=false;
if (isset($_SESSION['MEMBER_ID'])){
    $logged_in = true;
}

// set page template variables
$page = [];
$page['page_name'] = basename(__FILE__, '.php');
$page['title']= "Manage Properties";
$page['head']= "";



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
                <td><a href="#" data-id="<?=$prop['PROPERTY_ID']?>" class="btn btn-danger btn-xs delete_property_btn">Delete</a></td>
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
ob_clean();

// JS
ob_start();
?>
<script>
    $(".delete_property_btn").click(function(){
        var prop_id = $(this).data('id');
        var tr = $(this).parent().parent();

        var c = confirm("Are you sure?");
        if (c){
            $.ajax({
                type:"post",
                dataType: "json",
                data:{"PROPERTY_ID":prop_id},
                url: "cgi/controller/deleteProperty.php",
                success: function (jsonResponse) {
                    tr.remove();
                },
                error: function(re){
                    console.log(re);
                }
            });
        }
    });
</script>
<?php
$page['scripts']= ob_get_contents();
ob_end_clean();
?>


