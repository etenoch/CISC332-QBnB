<?php
require "cgi/lib/Member.php";

$member_id = $page_args[0];
$mem = Member::getMember($member_id);


// set page template variables
$page = [];
$page['page_name'] = basename(__FILE__, '.php');
$page['title']= "-- member name here --";
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
    <h3><?=$mem['NAME']?></h3>
    <pre>
    <?=print_r($mem)?>
    </pre>
</div>

<?php
$page['body']= ob_get_contents();
ob_end_clean();
?>
