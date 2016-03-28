<?php
require_once "cgi/lib/Member.php";
require_once "cgi/lib/Misc.php";

$failedSignup = false;
if (isset($_POST['submit'])) {

    if (isset($_POST["name"])
        && isset($_POST["phone"])
        && isset($_POST["faculty"])
        && isset($_POST["degree_type"])
        && isset($_POST["email"])
        && isset($_POST["grad_year"])
        && isset($_POST["password"])
        && isset($_POST["password_again"])
        && !empty($_POST["name"])
        && !empty($_POST["phone"])
        && !empty($_POST["faculty"])
        && !empty($_POST["degree_type"])
        && !empty($_POST["email"])
        && !empty($_POST["grad_year"])
        && !empty($_POST["password"])
        && !empty($_POST["password_again"])
        && $_POST["password"] == $_POST["password_again"]
    ){
        $newMemberID = Member::signup($_POST["name"],$_POST["phone"],$_POST["faculty"],$_POST["degree_type"],$_POST["email"],$_POST["grad_year"],$_POST["password"]);
        if ($newMemberID>-1){
            $_SESSION['MEMBER_ID'] = $newMemberID;
            header("Location: ?p=login");
        }else{ // failed signup
            $failedSignup = true;
        }
    }else{
        $failedSignup = true;

    }

}

// set page template variables
$page = [];
$page['page_name'] = basename(__FILE__, '.php');
$page['title']= "Create an account";
$page['head']= "";



// page content
ob_start();
?>

<div class="container under_top_bar">
    <form method="post" >
    <h3>Create an Account</h3>
    <?php if ($failedSignup) echo '<div class="alert alert-danger" role="alert">Please fill out all fields correctly.</div>' ?>

    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-4">

            <div class="form-group">
                <label for="form_name_ch">Name</label>
                <input type="text" class="form-control" name="name" id="form_name_ch">
            </div>
            <div class="form-group">
                <label for="form_phone_ch">Phone</label>
                <input type="text" class="form-control" name="phone" id="form_phone_ch">
            </div>
            <div class="form-group">
                <label for="form_faculty_ch">Faculty</label>
                <select class="form-control" name="faculty" id="form_faculty_ch">
                    <?php
                    foreach (Faculty::getFaculties() as $dt){
                        echo '<option value="'.$dt['FACULTY_ID'].'">'.$dt['FACULTY_NAME'].'</option>';
                    }
                    ?>
                    <option value="-1" >Add a custom faculty</option>
                </select>
            </div>
            <div class="form-group">
                <label for="form_degree_type_ch">Degree</label>
                <select class="form-control" name="degree_type" id="form_degree_type_ch">
                    <?php
                    foreach (DegreeType::getDegreeTypes() as $dt){
                        echo '<option value="'.$dt['DEGREE_TYPE_ID'].'">'.$dt['DEGREE_TYPE_NAME'].'</option>';
                    }
                    ?>
                    <option value="-1" >Add a custom degree</option>
                </select>
            </div>

            <div class="form-group">
                <label for="form_grad_year_ch">Graduation Year</label>
                <input type="text" class="form-control" name="grad_year" id="form_grad_year_ch">
            </div>

        </div>
        <div class="col-md-4">

                <div class="form-group">
                    <label for="form_email_ch">Email</label>
                    <input type="text" class="form-control" name="email" id="form_email_ch">
                </div>

                <div class="form-group">
                    <label for="form_password_ch">Password</label>
                    <input type="password" class="form-control" name="password" id="form_password_ch">
                </div>

                <div class="form-group">
                    <label for="form_password_again_ch">Password Again</label>
                    <input type="password" class="form-control" name="password_again" id="form_password_again_ch">
                </div>


        </div>
        <div class="col-md-2"></div>
    </div>
        <div style="text-align:center;margin-top:10px;">
            <input type="submit" name = "submit" value="Sign Up" class="btn btn-success">
        </div>
    </form>

</div>

<?php
$page['body']= ob_get_contents();
ob_clean();

// JS
ob_start();
?>
<script>
    $("#form_faculty_ch").change(function(){
        var ft_id = $('#form_faculty_ch :selected').val();

        if (parseInt(ft_id) === -1){
            var dis = prompt("Enter a faculty name", "");
            if (dis != null) {
                var data = {"FACULTY_NAME":dis};
                $.ajax({
                    type:"post",
                    dataType: "json",
                    data:{"json":JSON.stringify(data)},
                    url: "cgi/controller/createFaculty.php",
                    success: function (jsonResponse) {
                        var newID = jsonResponse.data;
                        $("#form_faculty_ch").prepend("<option value='"+newID+"'>"+dis+"</option>");
                        $("#form_faculty_ch").val(newID);
                    }, error: function(re){
                        console.log(re);
                    }
                });

            }
        }
    });

    $("#form_degree_type_ch").change(function(){
        var ft_id = $('#form_degree_type_ch :selected').val();

        if (parseInt(ft_id) === -1){
            var dis = prompt("Enter a degree type", "");
            if (dis != null) {
                var data = {"DEGREE_TYPE_NAME":dis};
                $.ajax({
                    type:"post",
                    dataType: "json",
                    data:{"json":JSON.stringify(data)},
                    url: "cgi/controller/createDegreeType.php",
                    success: function (jsonResponse) {
                        var newID = jsonResponse.data;
                        $("#form_degree_type_ch").prepend("<option value='"+newID+"'>"+dis+"</option>");
                        $("#form_degree_type_ch").val(newID);
                    }, error: function(re){
                        console.log(re);
                    }
                });

            }
        }
    });

</script>
<?php
$page['scripts']= ob_get_contents();
ob_end_clean();
?>
