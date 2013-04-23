<?php
session_start();
require_once 'database2.php';

//check to make sure the function sent a value for the reagent row
if (isset($_POST['table']) & isset($_POST['col']) & isset($_POST['row']) & isset($_POST['new_value'])) {

    $table     = mysql_real_escape_string(trim($_POST['table']));
    $col     = mysql_real_escape_string(trim($_POST['col']));
    $row        = mysql_real_escape_string(trim($_POST['row']));
    $new_value = mysql_real_escape_string(trim($_POST['new_value']));

    if($table === 'reagent_detail'){
        $request   = "UPDATE reagents SET ".$col."='".$new_value."' WHERE r_rid='".$row."'";
    }
    elseif($table === 'aliquot'){
        $request   = "UPDATE aliquots SET ".$col."='".$new_value."' WHERE aq_aqid='".$row."'";
    }


//use the field_attributes table to get a list of fields to display for this reagent type
    mysqli_query($link, $request);
    
    $response = array(
        request => $request,
        rows => mysqli_affected_rows($link)
        );
    echo json_encode($response);
}
//else return rows=0
else {
    $response = array(rows => '0');
    echo json_encode($response);
}

mysqli_close($link);
?>
