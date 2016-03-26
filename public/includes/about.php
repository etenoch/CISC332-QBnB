<?php


// set page template variables
$page = [];
$page['page_name'] = basename(__FILE__, '.php');
$page['title']= "About";
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

<div class="container under_top_bar">
    <h2>About QBnB</h2>
    <p>The Queen’s Alumnae BnB service (QBnB) facilitates matching alumni intending to travel to a city with other alumnae offering accommodations to rent in that city. In addition to being able to see each other’s contact information, alumnae are able to see each other’s faculty, degree type and graduation year – allowing the alumnae to connect with each other on a more personal level.</p>
</div>

<?php
$page['body']= ob_get_contents();
ob_end_clean();
?>
