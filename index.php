<?php session_start(); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" 
    "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>LabIndex Home</title>
        <link rel="stylesheet" type="text/css" href="/root/labindex/css/main.css">

        <!--my separate js extensions -->
        <script type="text/javascript" src="/root/labindex/js/refresh_search.js"> </script>
<script type="text/javascript" src="/root/labindex/js/details.js"> </script>

        <!--jQuery library and UI extension -->
        <script type="text/javascript" src="/root/labindex/jquery-ui-1.10.0.custom/js/jquery-1.9.0.js" > </script>
        <script type="text/javascript" src="/root/labindex/jquery-ui-1.10.0.custom/js/jquery-ui-1.10.0.custom.js" > </script>
        <script type="text/javascript" src="/root/labindex/jquery-ui-1.10.0.custom/js/jquery-ui-1.10.0.custom.min.js" > </script>


        <script type="text/javascript">
            
            <!--on load refresh the main search results panel-->
            $(function(){
                var keyword = "<?php
if (isset($_GET["search"])) {
    $keyword = $_GET["search"];
    echo $keyword;
} else { echo ""; }
?>";
                            
        <!--populate search textbox-->              
        if(keyword.length > 0){	
            $("#search_input").attr("value",keyword); 
            refresh_search(keyword);
        }
                    
                         
        //                            <!--show browse by category fields if no keyword is present-->
        //                            else{ 
        //                                //$("#search_input").attr("placeholder","type your search here");
        //                                $("#content_container").children().remove();
        //                    
        //                        
        //                                $(document.createElement('div')).attr("id", "virus_browse").attr("class","list_header").text("Browse Viruses").button({
        //                                    icons: {
        //                                        primary: "ui-icon-carat-1-e"
        //                                    }})
        //                                .click(function(){
        //                                    <!--uncheck all filter elements except this one-->
        //                                    $(".field_checkbox").removeAttr('checked');
        //                                    $("#virus_checkbox").attr('checked','checked');
        //                                    refresh_search(keyword);
        //                                }).appendTo("#left");
        //                        
        //                        
        //                                $(document.createElement('div')).attr("id", "chemical_browse").attr("class","list_header").text("Browse Chemicals").button({
        //                                    icons: {
        //                                        primary: "ui-icon-carat-1-e"
        //                                    }})
        //                                .click(function(){
        //                                    <!--uncheck all filter elements except this one-->
        //                                    $(".field_checkbox").removeAttr('checked');
        //                                    $("#chemicals_checkbox").attr('checked','checked');
        //                                    refresh_search(keyword);
        //                                }).appendTo("#left");
        //                            }
        //            
        //           TODO: fix method for removing keyword, add clear "x" at and or search field
        <!--clear search textbox on focus-->      
        $("#search_input").focus(function(){
            $(this).val("");
        })
                
        <!--capture search function input-->   
        $("#search_input").keyup(function(){
            var search_input = $(this).val();
            keyword = search_input;
            if(search_input.length>2){refresh_search(search_input);}
        });
                            
    	        <!--assign page control functions --> 
        <?php $_SESSION['qty'] = 25; ?>
                
//        $("#next").click(function(){
//            if($("#start").text()>0){
//                var $current = parseInt($("#start").val(), 10);
//                $("#start").val($current+2);}
//            refresh_search(keyword);
//        });
    });
        </script>

    </head>
    <body>
        <div id="header"> 
            <input name="query" type="text" value="search" id="search_input">
            <!--            <a href="newitem.php">create a new entry</a>-->
        </div>

        <div id="main">
            <div id="top"></div>
            <div id="content_container"></div>
            <div id="bot">
            
            </div>
        </div>
    </body>
</html>
