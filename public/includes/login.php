<?php
require "cgi/lib/Member.php";
require "cgi/lib/Property.php";
require "cgi/lib/Misc.php";

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
                <h4>Change Profile Details</h4>
                <form method="post" >

                    <div class="form-group">
                        <label for="form_name_ch">Name</label>
                        <input type="text" class="form-control" value="<?=$member['NAME']?>" name="name" id="form_name_ch">
                    </div>
                    <div class="form-group">
                        <label for="form_phone_ch">Phone</label>
                        <input type="text" class="form-control" value="<?=$member['PHONE_NUMBER']?>" name="phone" id="form_phone_ch">
                    </div>
                    <div class="form-group">
                        <label for="form_faculty_ch">Faculty</label>
                        <select class="form-control" name="faculty" id="form_faculty_ch">
                            <?php
                            foreach (Faculty::getFaculties() as $dt){
                                if ($member["FACULTY_ID"]==$dt['FACULTY_ID'])
                                    echo '<option selected value="'.$dt['FACULTY_ID'].'">'.$dt['FACULTY_NAME'].'</option>';
                                else echo '<option value="'.$dt['FACULTY_ID'].'">'.$dt['FACULTY_NAME'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="form_degree_type_ch">Degree</label>
                        <select class="form-control"  name="degree_type" id="form_degree_type_ch">
                            <?php
                            foreach (DegreeType::getDegreeTypes() as $dt){
                                if ($member["DEGREE_TYPE_ID"]==$dt['DEGREE_TYPE_ID'])
                                    echo '<option selected value="'.$dt['DEGREE_TYPE_ID'].'">'.$dt['DEGREE_TYPE_NAME'].'</option>';
                                else echo '<option value="'.$dt['DEGREE_TYPE_ID'].'">'.$dt['DEGREE_TYPE_NAME'].'</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="form_grad_year_ch">Graduation Year</label>
                        <input type="text" class="form-control" value="<?=$member['GRAD_YEAR']?>" name="grad_year" id="form_grad_year_ch">
                    </div>

                    <div class="form-group">
                        <label for="form_email_ch">Email</label>
                        <input type="text" class="form-control" value="<?=$member['EMAIL']?>" name="email" id="form_email_ch">
                    </div>
                    <input type="submit" value="Save Changes" class="btn btn-success"/>
                </form>

            </div>
            <div class="col-md-8">
                <h4>Manage Properties</h4>
                <table class="table table-bordered">
                    <tr>
                        <th>Name</th><th>Address</th><th>Type</th><th>Price</th><th>Edit</th><th>Delete</th>
                    </tr>
                    <?php
                    foreach (Property::getMemberProperties($member_id) as $prop){
                        ?>
                        <tr>
                            <td><?=$prop['PROPERTY_NAME']?></td>
                            <td><?=$prop['ADDRESS_1']?></td>
                            <td><?=$prop['PROPERTY_TYPE_NAME']?></td>
                            <td><?=$prop['PRICE']?></td>
                            <td><a href="?p=edit/<?=$prop['PROPERTY_ID']?>" class="btn btn-info btn-xs">Edit</a></td>
                            <td><a href="?p=delete/<?=$prop['PROPERTY_ID']?>" class="btn btn-danger btn-xs">Delete</a></td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
        </div>


    </div>
    <?php
}
$page['body']= ob_get_contents();
ob_end_clean();
?>
