<?php


// set page template variables
$page = [];
$page['page_name'] = basename(__FILE__, '.php');
$page['title']= "Search Listings";
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

lol page content

<?php
$page['body']= ob_get_contents();
ob_end_clean();
?>
