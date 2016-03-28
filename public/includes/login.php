<?php
require_once "cgi/lib/Member.php";
require_once "cgi/lib/Property.php";
require_once "cgi/lib/Misc.php";

// try to login is posted
$failedLogin = false;

if (isset($_POST['email'])) {
    $member_id = Member::login($_POST['email'],$_POST['password']);
    if ($member_id>-1){
        $_SESSION['MEMBER_ID'] =$member_id;
    }else{ // failed login
        $failedLogin = true;
    }
}

$logged_in=false;
if (isset($_SESSION['MEMBER_ID'])){
    $logged_in = true;
}

// set page template variables
$page = [];
$page['page_name'] = basename(__FILE__, '.php');
$page['title']= $logged_in?"Member":"Login";
$page['head']= "";

// JS
ob_start();
?>
<?php
$page['scripts']= ob_get_contents();
ob_clean();

// page content
ob_start();

if (!$logged_in){

?>
<div class="container under_top_bar">

    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4">

            <h3>Member Login</h3>
            <?php if ($failedLogin) echo '<div class="alert alert-danger" role="alert">Login failed. Please try again.</div>' ?>
            <form method="post" >

                <div class="form-group">
                    <label for="form_email_ch">Email</label>
                    <input type="text" class="form-control" name="email" id="form_email_ch">
                </div>

                <div class="form-group">
                    <label for="form_password_ch">Password</label>
                    <input type="password" class="form-control" name="password" id="form_password_ch">
                </div>

                <input type="submit" value="Login" class="btn btn-success">

            </form>
            <br/>
            <a href="?p=signup">Not a Member yet? Create an account now!</a>
        </div>
        <div class="col-md-4"></div>
    </div>

</div>

<?php
}else{
    $member_id = $_SESSION['MEMBER_ID'];
    $member = Member::getMember($member_id);
    ?>
    <div class="container under_top_bar">
        <h2>Welcome <?=$member['NAME']?></h2>

        <div class="row">
            <div class="col-md-4">
                <a href="?p=member_profile"><h4>Change Profile Details</h4></a>

            </div>
            <div class="col-md-4">
                <a href="?p=member_properties"><h4>Manage Properties</h4></a>

            </div>
            <div class="col-md-4">
                <a href="?p=member_bookings"><h4>Manage Bookings</h4></a>

            </div>
        </div>


    </div>
    <?php
}
$page['body']= ob_get_contents();
ob_end_clean();
?>
