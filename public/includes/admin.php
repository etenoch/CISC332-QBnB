<?php
require_once "cgi/lib/Admin.php";
require_once "cgi/lib/Booking.php";
require_once "cgi/lib/Member.php";
require_once "cgi/lib/Property.php";


if (isset($_POST['delete_property_btn'])){
    Property::deleteProperty($_POST['delete_property']);
}
if (isset($_POST['delete_member_btn'])){
    Member::deleteMember($_POST['delete_member']);
}
if (isset($_POST['delete_admin_btn'])){
    Admin::deleteAdmin($_POST['delete_admin']);
}
if (isset($_POST['add_admin_btn'])){
    Admin::createAdmin($_POST['admin_username'],$_POST['admin_password']);
}



// try to login is posted
$failedLogin = false;
if (isset($_POST['username'])) {
    $admin_id =Admin::login($_POST['username'],$_POST['password']);
    if ($admin_id>-1){
        $_SESSION['ADMINISTRATOR_ID'] =$admin_id;
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
        <div class="well well-sm">
        <div class="row">
            <div class="col-md-3">
                <h5>Delete Property</h5>
                <form method="post">
                    <select name="delete_property" id="delete_property" class="form-control">
                        <option value="-1" disabled selected>Select property to delete</option>
                        <?php
                        foreach (Property::getAllProperties() as $property) {
                            echo '<option value="'.$property['PROPERTY_ID'].'">'.$property['PROPERTY_NAME'].' - '.$property['NAME'].'</option>';
                        }
                        ?>
                    </select>
                    <input type="submit" name="delete_property_btn" value="Delete" class="btn btn-danger">
                </form>
            </div>
            <div class="col-md-3">
                <h5>Delete a member</h5>
                <b>This will also delete all accommodations owned by the member</b>
                <form method="post">
                    <select name="delete_member" id="delete_member" class="form-control">
                        <option value="-1" disabled selected>Select member to delete</option>
                        <?php
                        foreach (Member::getAllMembers() as $member) {
                            echo '<option value="'.$member['MEMBER_ID'].'">'.$member['NAME'].'</option>';
                        }
                        ?>
                    </select>
                    <input type="submit" name="delete_member_btn" value="Delete" class="btn btn-danger">
                </form>
            </div>
            <div class="col-md-3">
                <h5>Delete an admin</h5>
                <form method="post">
                    <select name="delete_admin" id="delete_admin" class="form-control">
                        <option value="-1" disabled selected>Select member to delete</option>
                        <?php
                        foreach (Admin::getAllAdmins() as $admin) {
                            echo '<option value="'.$admin['ADMINISTRATOR_ID'].'">'.$admin['USERNAME'].'</option>';
                        }
                        ?>
                    </select>
                    <input type="submit" name="delete_admin_btn" value="Delete" class="btn btn-danger">
                </form>

            </div>
            <div class="col-md-3">
                <h5>Add an Admin</h5>
                <form method="post">
                    Username
                    <input type="text" name="admin_username" class="form-control">
                    Password
                    <input type="password" name="admin_password" class="form-control">
                    <input type="submit" name="add_admin_btn" value="Create User" class="btn btn-success">
                </form>

            </div>
        </div>
        </div>
        <h4>Reports</h4>
        <h6>Properties</h6>
        <table class="table table-bordered">
            <tr><th>Property</th><th>Summarize</th></tr>
            <?php
            foreach (Property::getAllProperties() as $pt) {
                echo '<tr>';
                echo '<td>'.$pt['PROPERTY_NAME'].'</td>';
                echo '<td><a href="?p=summarize_property/'.$pt['PROPERTY_ID'].'" class="btn btn-info btn-xs" target="_blank">Summarize</a></td>';
                echo '</tr>';
            }
            ?>
        </table>

        <h6>Suppliers</h6>
        <table class="table table-bordered">
            <tr><th>Supplier</th><th>Summarize</th></tr>
            <?php
            foreach (Member::getALlSuppliers() as $pt) {
                echo '<tr>';
                echo '<td>'.$pt['NAME'].'</td>';
                echo '<td><a href="?p=summarize_supplier/'.$pt['MEMBER_ID'].'" class="btn btn-info btn-xs" target="_blank">Summarize</a></td>';
                echo '</tr>';
            }
            ?>
        </table>


        <h6>Consumers</h6>
        <table class="table table-bordered">
            <tr><th>Consumer</th><th>Summarize</th></tr>
            <?php
            foreach (Member::getALlConsumers() as $pt) {
                echo '<tr>';
                echo '<td>'.$pt['NAME'].'</td>';
                echo '<td><a href="?p=summarize_consumer/'.$pt['MEMBER_ID'].'" class="btn btn-info btn-xs" target="_blank">Summarize</a></td>';
                echo '</tr>';
            }
            ?>
        </table>




    </div>
    <?php
}

$page['body']= ob_get_contents();
ob_clean();

// JS
ob_start();
?>
<?php
$page['scripts']="";
ob_end_clean();
?>



