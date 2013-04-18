<?php

session_start();
require_once 'database2.php';

//check to make sure the function sent a value for the reagent id
if (isset($_POST['field']) & isset($_POST['id']) & isset($_POST['new_value'])) {

    $field     = mysql_real_escape_string(trim($_POST['field']));
    $id        = mysql_real_escape_string(trim($_POST['id']));
    $new_value = mysql_real_escape_string(trim($_POST['new_value']));
    $request   = "UPDATE reagents SET ".$field."='".$new_value."' WHERE r_rid='".$id."'";
//use the field_attributes table to get a list of fields to display for this reagent type
    mysqli_query($link, $request);
    // mysqli_query($link,"update reagents set r_rating='46' WHERE r_rid='64'");

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
