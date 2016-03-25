<?php
$logged_in=false;
if (isset($_SESSION['MEMBER_ID'])){
    $logged_in = true;


}

// set page template variables
$page = [];
$page['page_name'] = basename(__FILE__, '.php');
$page['title']= "Login";
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

        </div>
        <div class="col-md-4"></div>
    </div>

</div>

<?php
}else{
?>
    <div class="container under_top_bar">
        <h2>Welcome Member</h2>

    </div>
<?php
}
$page['body']= ob_get_contents();
ob_end_clean();
?>
