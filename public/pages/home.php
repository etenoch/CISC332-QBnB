<?php
$page = [];
$page['title']= "title lol";
$page['head']= "";
$page['scripts']= "";

// page content
ob_start();
?>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">QBnB</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="active"><a href="#">Discover</a></li>
                <li><a href="?p=search">Search</a></li>
                <li><a href="?p=about">About</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="?p=login">Login</a></li>
                <li><a href="?p=admin">Admin</a></li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>

<div class="container under_top_bar">

    <h3>Hello World</h3>


</div>
<?php
$page['body']= ob_get_contents();
ob_end_clean();
?>
