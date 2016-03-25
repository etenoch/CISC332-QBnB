<?php
require "cgi/lib/Admin.php";

// try to login is posted
$failedLogin = false;
if (isset($_POST['username'])) {
    $callLogin =Admin::login($_POST['username'],$_POST['password']);
    if ($callLogin>-1){
        $_SESSION['ADMINISTRATOR_ID'] =$callLogin;
    }else{ // failed login
        $failedLogin = true;
    }
}


$logged_in=false;
if (isset($_SESSION['ADMINISTRATOR_ID'])){
    $logged_in = true;


}

// set page template variables
$page = [];
$page['page_name'] = basename(__FILE__, '.php');
$page['title']= "QBnB Administration";
$page['head']= "";

// JS
ob_start();
?>
<?php
$page['scripts']="";

// page content
ob_start();

if (!$logged_in){

?>
<div class="container under_top_bar">

    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4">

            <h3>Admin Login</h3>

            <?php if ($failedLogin) echo '<div class="alert alert-danger" role="alert">Login failed. Please try again.</div>' ?>

            <form method="post" >

                <div class="form-group">
                    <label for="form_username_ch">Username</label>
                    <input type="text" class="form-control" name="username" id="form_username_ch">
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

    $admin = Admin::getAdmin($_SESSION['ADMINISTRATOR_ID']);

    ?>
    <div class="container under_top_bar">
        <h2>Welcome <?=$admin['USERNAME']?></h2>

    </div>
    <?php
}

$page['body']= ob_get_contents();
ob_end_clean();
?>
