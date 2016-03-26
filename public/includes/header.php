
<?php

function generateHeader($page_name){
    $loggedIn = "";
    if (isset($_SESSION['ADMINISTRATOR_ID']) || isset($_SESSION['MEMBER_ID'])){
        $loggedIn = '<li><a href="logout.php">Logout</a></li>';
    }

    $profileText = "Login";
    if (isset($_SESSION['ADMINISTRATOR_ID']) || isset($_SESSION['MEMBER_ID'])){
        $me = Member::getMember($_SESSION['MEMBER_ID']);
        $profileText = $me['NAME'];
    }

    $signupItem = !isset($_SESSION['MEMBER_ID']) ? "<li ".yesActive($page_name,"signup")." ><a href=\"?p=signup\">Sign Up</a></li>" : "";

    return "
        <nav class=\"navbar navbar-inverse navbar-fixed-top\">
            <div class=\"container\">
                <div class=\"navbar-header\">
                    <button type=\"button\" class=\"navbar-toggle collapsed\" data-toggle=\"collapse\" data-target=\"#navbar\" aria-expanded=\"false\" aria-controls=\"navbar\">
                        <span class=\"sr-only\">Toggle navigation</span>
                        <span class=\"icon-bar\"></span>
                        <span class=\"icon-bar\"></span>
                        <span class=\"icon-bar\"></span>
                    </button>
                    <a class=\"navbar-brand\" href=\"?\">QBnB</a>
                </div>
                <div id=\"navbar\" class=\"collapse navbar-collapse\">
                    <ul class=\"nav navbar-nav\">
                        <li ".yesActive($page_name,"home")." ><a href=\"?\">Discover</a></li>
                        <li ".yesActive($page_name,"create")."><a href=\"?p=create\">Add Listing</a></li>
                        <li ".yesActive($page_name,"about")." ><a href=\"?p=about\">About</a></li>
                    </ul>
                    <ul class=\"nav navbar-nav navbar-right\">
                        ".$signupItem."
                        <li ".yesActive($page_name,"login")." ><a href=\"?p=login\">".$profileText."</a></li>
                        <li ".yesActive($page_name,"admin")." ><a href=\"?p=admin\">Admin</a></li>
                        ".$loggedIn ."
                    </ul>
                </div>
            </div>
        </nav>";
}

function yesActive($one, $two){

    if (trim($one) == trim($two)) return 'class="active"';
    else return "";
}
