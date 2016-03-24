<?php
$page = [];
$page['title']= "Oops. An error occurred.";
$page['head']= "";
$page['scripts']= "";

// page content
ob_start();
?>
<div class="container">

    <h2>Oops. An error occurred. The page couldn't be loaded</h2>

</div>
<?php
$page['body']= ob_get_contents();
ob_end_clean();
?>
