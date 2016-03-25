<?php
require "cgi/lib/Property.php";

// set page template variables
$page = [];
$page['page_name'] = basename(__FILE__, '.php');
$page['title']= "Manage Listing";
$page['head']= "";

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



    </div>
    <?php
}
?>

<?php
$page['body']= ob_get_contents();
ob_end_clean();
?>
