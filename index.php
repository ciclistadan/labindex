<?php session_start(); 

    require('../secret/janrain_apikey.php');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
    "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>LabIndex Database</title>
    <link rel="stylesheet" type="text/css" href="./css/main.css">
     <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
    <link rel="stylesheet" type="text/css" href='http://fonts.googleapis.com/css?family=Sue+Ellen+Francisco|Pathway+Gothic+One|Kite+One'>
    <link href='http://fonts.googleapis.com/css?family=Ubuntu&subset=latin,greek' rel='stylesheet' type='text/css'>

    <!--my separate js extensions -->
    <script type="text/javascript" src="./js/refresh_search.js"> </script>
    <script type="text/javascript" src="./js/details.js"> </script>
    <script type="text/javascript" src="./js/aliquots.js"> </script>
    <script type="text/javascript" src="./js/authentication.js"> </script>
    <script type="text/javascript" src="./js/email_form.js"> </script>

    <!--jQuery library and UI extension -->
    <script type="text/javascript" src="./jquery-ui-1.10.0.custom/js/jquery-1.9.0.js" > </script>
    <script type="text/javascript" src="./jquery-ui-1.10.0.custom/js/jquery-ui-1.10.0.custom.js" > </script>
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
    $( "#dialog-locations" ).dialog( "open" );
});

    });


$(function() {
    var name = $( "#name" ),
    email = $( "#email" ),
    message = $( "#message" ),
    allFields = $( [] ).add( name ).add( email ).add( message ),
    tips = $( ".validateTips" );
    function updateTips( t ) {
        tips
        .text( t )
        .addClass( "ui-state-highlight" );
        setTimeout(function() {
            tips.removeClass( "ui-state-highlight", 1500 );
        }, 500 );
    }
    function checkLength( o, n, min, max ) {
        if ( o.val().length > max || o.val().length < min ) {
            o.addClass( "ui-state-error" );
            updateTips( "Length of " + n + " must be between " +
                min + " and " + max + "." );
            return false;
        } else {
            return true;
        }
    }
    function checkRegexp( o, regexp, n ) {
        if ( !( regexp.test( o.val() ) ) ) {
            o.addClass( "ui-state-error" );
            updateTips( n );
            return false;
        } else {
            return true;
        }
    }
    $( "#dialog-form" ).dialog({
        autoOpen: false,
        // height: 300,
        width: 450,
        modal: true,
        buttons: {
            "Email comments": function() {
                var bValid = true;
                allFields.removeClass( "ui-state-error" );
                bValid = bValid && checkLength( name, "username", 3, 16 );
                bValid = bValid && checkLength( email, "email", 6, 80 );
                bValid = bValid && checkRegexp( name, /^[a-z]([a-z_])+$/i, "Your name may only include letters." );
// From jquery.validate.js (by joern), contributed by Scott Gonzalez: http://projects.scottsplayground.com/email_address_validation/
bValid = bValid && checkRegexp( email, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "eg. ui@jquery.com" );
if ( bValid ) {
     $.ajax({
        async: false,
        type: "POST",
        url: "utility/email.php",
        dataType: 'json',
        data: {
            name: name.val(),
            email: email.val(),
            message: message.val()
        }
    })
    .done(function(data){
        $('.email_comments').animate({
            backgroundColor: "green"});
            
    })
    .fail(function(){});

    $( this ).dialog( "close" );
}
},
Cancel: function() {
    $( this ).dialog( "close" );
}
},
close: function() {
    allFields.val( "" ).removeClass( "ui-state-error" );
}
});

    $( "#email_comments" )
    .button()
    .click(function() {
        $( "#dialog-form" ).dialog( "open" );
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
        <div id="footer"></div>
    </div>

    <div id="dialog-form" class="dialog-box" title="Email Comments">
        <p class="validateTips">All form fields are required.</p>
        <form>
            <fieldset>
                <label for="name">Name</label>
                <input type="text" name="name" id="name" class="text ui-widget-content ui-corner-all" />
                <label for="email">Email</label>
                <input type="text" name="email" id="email" value="" class="text ui-widget-content ui-corner-all" />
                <label for="message">Comments</label>
                <textarea type="text" name="message" id="message" value="" class="text ui-widget-content ui-corner-all" /></textarea>
            </fieldset>
        </form>
    </div>

    <div id="dialog-locations" class="dialog-box" title="Reagent Locations">
        <ul>
            <li>Zone A R507: Nacho’s Desk</li>
            <li>Zone B R507: Natalia’s Desk</li>
            <li>Zone C R507: Judy’s old Desk</li>
            <li>Zone D R507: Kassi’s old Desk</li>
            <li>Zone E R509/Splinter Cell: Claire-Marie’s Desk</li>
            <li>Zone F R509/Splinter Cell: Dan’s Desk</li>
            <li>Zone G R507: Sink opposite microfuges</li>
            <li>CN-A R507 Chemical Nook left side</li>
            <li>CN-B R507 Chemical Nook center</li>
            <li>CN-C R507 Chemical Nook right side</li>
            <li>TCR-A R507 Tissue Culture Room left </li>
            <li>TCR-B R507 Tissue Culture Room center</li>
            <li>TCR-C R507 Tissue Culture Room right</li>
            <li>TCL-A L503 Tissue Culture Room left</li>
            <li>TCL-B L503 Tissue Culture Room center</li>
            <li>TCL-C L503 Tissue Culture Room right</li>
            <li>TCL-D L503 Tissue Culture Room sink</li>
        </ul>
    </div>

</body>