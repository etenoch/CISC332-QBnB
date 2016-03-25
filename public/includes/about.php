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
    <p>Bacon ipsum dolor amet ribeye pork chop jerky, bresaola strip steak prosciutto shankle cow shoulder porchetta. Turkey meatball shoulder, short loin doner brisket pork loin beef drumstick capicola sausage landjaeger ground round porchetta. Pork bresaola ham hock, t-bone meatball shankle meatloaf cupim rump swine salami pork loin. Pancetta frankfurter venison beef sirloin fatback pork ground round prosciutto landjaeger filet mignon shank jerky. Turkey sirloin cow picanha ham, chuck tri-tip kielbasa swine drumstick t-bone pastrami fatback cupim. Andouille beef ribs meatball pancetta venison shank. Ham fatback pork belly salami pancetta, doner rump jowl cow.</p>
</div>

<?php
$page['body']= ob_get_contents();
ob_end_clean();
?>
