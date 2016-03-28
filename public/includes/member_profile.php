<?php
require_once "cgi/lib/Member.php";
require_once "cgi/lib/Property.php";
require_once "cgi/lib/Misc.php";

$showSavedMsg = false;
if(isset($_POST['save_changes'])){
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $faculty = $_POST['faculty'];
    $degree_type = $_POST['degree_type'];
    $email = $_POST['email'];
    $grad_year = $_POST['grad_year'];
    if (Member::update($_SESSION['MEMBER_ID'],$name,$phone,$faculty,$degree_type,$email,$grad_year)){
        $showSavedMsg = true;
    }
}



$logged_in=false;
if (isset($_SESSION['MEMBER_ID'])){
    $logged_in = true;
}

// set page template variables
$page = [];
$page['page_name'] = basename(__FILE__, '.php');
$page['title']= "Manage Properties";
$page['head']= "";


// page content
ob_start();

if ($logged_in){
    $member_id = $_SESSION['MEMBER_ID'];
    $member = Member::getMember($member_id);
?>
<div class="container under_top_bar">
    <h3>Manage Profile</h3>
    <?php
    if ($showSavedMsg){
    ?>
    <div class="alert alert-success" role="alert">Changed Saved</div>
    <?php } ?>
    <div class="row">

        <div class="col-md-4"></div>
        <div class="col-md-4">
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

                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="form_email_ch">Email</label>
                    <input type="text" class="form-control" value="<?=$member['EMAIL']?>" name="email" id="form_email_ch">
                </div>
                <input type="submit" value="Save Changes" name="save_changes" class="btn btn-success"/>
                <a href="#" id="delete_account_btn" class="btn btn-danger">Delete Account</a>
            </form>
            <br/>


        </div>
        <div class="col-md-4"></div>

    </div>

</div>
<?php
}else{
    echo "<div class='container under_top_bar'><h4>Please login first <a href='?p=login'>here</h4></div>";
}
$page['body']= ob_get_contents();
ob_clean();

// JS
ob_start();
?>
<script>
    $("#delete_account_btn").click(function(){

        var c = confirm("Are you sure?");
        if (c){
            $.ajax({
                type:"post",
                dataType: "json",
                url: "cgi/controller/deleteMember.php",
                success: function (jsonResponse) {
                    window.location="logout.php";
                },
                error: function(re){
                    console.log(re);
                }
            });
        }
    });
</script>
<?php
$page['scripts']= ob_get_contents();
ob_end_clean();
?>
