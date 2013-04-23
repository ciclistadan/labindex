<?php session_start(); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" 
    "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>LabIndex Database</title>
    <link rel="stylesheet" type="text/css" href="./css/main.css">

    <!--my separate js extensions -->
    <script type="text/javascript" src="./js/refresh_search.js"> </script>
    <script type="text/javascript" src="./js/details.js"> </script>
    <script type="text/javascript" src="./js/aliquots.js"> </script>

    <!--jQuery library and UI extension -->
    <script type="text/javascript" src="./jquery-ui-1.10.0.custom/js/jquery-1.9.0.js" > </script>
    <script type="text/javascript" src="./jquery-ui-1.10.0.custom/js/jquery-ui-1.10.0.custom.js" > </script>
    <script type="text/javascript">

    $(function(){
        var keyword = "<?php
        if (isset($_GET["search"])) {
            $keyword = $_GET["search"];
            echo $keyword;
        } else { echo ""; }
        ?>";

    // populate search textbox              
    if(keyword.length > 0){ 
        $("#search_input").attr("value",keyword); 
        refresh_search(keyword);
    }

        // TODO: fix method for removing keyword, add clear "x" at and or search field
        // clear search textbox on focus
        $("#search_input").focus(function(){
            $(this).val("");
        });

        // capture search function input
        $("#search_input").keyup(function(){
            var search_input = $(this).val();
            keyword = search_input;
            if(search_input.length>2){refresh_search(search_input);}
        });

        // assign page control functions
        <?php $_SESSION['qty'] = 18; ?>

        //test function here
        $('#test').click(function(){
            var test = $('.titlebar_value').closest('[id]').attr('id');
            alert(test);
        });


    });
    </script>
</head>

<body>
    <div id="container">
        <div id="navbar">
            <a href="http://connorlab.com/">Connor Lab</a>
            <a href="http://localhost/labindex/index.php?search=tw2/">LabIndex Database</a>
            <a id="test">Test Button</>
        </div>
        <div id="top">
            <div id="search"><input id="search_input" type="text" value="search"></div>
            <div id="intro"></div>
        </div>
        <div id="main">
            <div id="content_container"></div>
            <div id="side"></div>
        </div>
        <div id="bottom">
            <div id="landing">this is the bottom landing</div>
        </div>
        <div id="footer">and here is the footer</div>
    </div>
</body>