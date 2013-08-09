<?php session_start(); 

    require('../secret/janrain_apikey.php');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
    "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>LabIndex Database</title>

    <!-- style  -->
    <link rel="stylesheet" type="text/css" href="./css/main.css">
     <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
    <link rel="stylesheet" type="text/css" href='http://fonts.googleapis.com/css?family=Sue+Ellen+Francisco|Pathway+Gothic+One|Kite+One'>
    <link href='http://fonts.googleapis.com/css?family=Ubuntu&subset=latin,greek' rel='stylesheet' type='text/css'>

    <!--jQuery library and UI extension -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" ></script>
    <script type="text/javascript" src="./jquery-ui-1.10.0.custom/js/jquery-ui-1.10.0.custom.js" > </script>
   
    <!--javascript and jQuery extensions -->
    <script type="text/javascript" src="./js/refresh_search.js"> </script>
    <script type="text/javascript" src="./js/details.js"> </script>
    <script type="text/javascript" src="./js/aliquots.js"> </script>
    <script type="text/javascript" src="./js/authentication.js"> </script>
    <script type="text/javascript" src="./js/email.js"> </script>


 <script type="text/javascript">
    //Global Variables
    var query_string = '<? echo $_SERVER['QUERY_STRING'] ?>';


    $(function(){
        //get keyword from url
        var keyword = "<?php
        if (isset($_GET["search"])) {
            $keyword = $_GET["search"];
            echo $keyword;
        } else { echo ""; }
        ?>";

        // insert keyword into search textbox and call the search
        if(keyword.length > 0){
            $("#search_input").attr("value",keyword);
        }

        //run the function onload in case there are any url search parameters
        refresh_search(keyword);

        // TODO: fix method for removing keyword, add clear "x" at and or search field
        // clear search textbox on focus
        $("#search_input").focus(function(){
            $(this).val("");
        });

        // capture dynamic search function input, verify length and prevent multiple searches by forcing a delay
        
        var delay = (function(){
          var timer = 0;
          return function(callback, ms){
            clearTimeout (timer);
            timer = setTimeout(callback, ms);
            };
        })();

        $('#search_input').keyup(function() {
            delay(function(){
                var search_input = $('#search_input').val();
                refresh_search(search_input);
            }, 500 );
        });


        // assign page control functions
        <?php $_SESSION['qty'] = 10; ?>

        $('.new_reagent_button').change(function(){
            var type = $(this).val();
            new_reagent(type);
        });

$( "#dialog-locations" ).dialog({ 
    autoOpen: false,
    width: 450
     });
$( "#opener" ).click(function() {
    $( "#quick-locations" ).dialog( "open" );
});

    });






    </script>
</head>

<body>
    <div id="container">
        <div id="navbar">
            <a href="http://connorlab.com/">Connor Lab</a>
            <a href="http://connorlab.com/labindex/index.php">LabIndex Database</a>
            <select class="new_reagent_button">
                <option val="default">add a new reagent</>
                <option val="antibody">antibody</>
                <option val="virus">virus</>
                <option val="chemical">chemical</>
                <option val="supply">supply</>
            </select>
            <button id="email_comments">Email Comments</button>
            <button id="opener">Quick Locations</button>

            <span class="user_panel">
            <? if($_SESSION['userid']){
                    echo '<a class="" >Hi '.$_SESSION['name'].'</a>';
                    echo '<a class="" href="utility/destroy_session.php">(logout)</a>';
                }
                else{
                    echo '<a class="janrainEngage" href="#">Sign-In</a>';
                }
            ?>
            </span>
        </div>
        <div id="top">
            <div id="search"><input id="search_input" class="search_input" type="text" value="search"></div>
            <div id="intro"></div>
        </div>
        <div id="main">
            <div id="content_container"></div>
            <div id="side" class="<? if($_SESSION['userid']){echo 'verified';} ?>">
                <div id:"col1"></div>
                <div id:"col2"></div>
            </div>
        </div>
        <div id="bottom">
            <a href="http://localhost/labindex/locations.pdf">Location Details and Maps PDF</a>
        </div>
        <div id="footer">
            

        </div>
    </div>



<div class="updates">
        <ul>Important Notes
            <li>do not use quotation marks (single ' or double ") in your reagent names, I'll fix this later but it currently causes errors</li>
            <li>make sure you search several possibile names before adding a new reagent</li>
        </br>
        Recent Updates
            <li>20130803</li>added the container 'viruses from don' to the database
            <li>20130801</li>added delay in search function to prevent duplicates
            <li>20130801</li>website goes live on <a href="www.connorlab.com/labindex/">connorlab.com</a>!
        </div>

</body>


<?php include './utility/email_form.php'; ?>
<?php include './utility/location_list.php'; ?>

