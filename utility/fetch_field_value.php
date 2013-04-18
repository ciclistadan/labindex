<?php

session_start();
require_once 'database.php';

//check to make sure the function sent a value for the reagent id
if (isset($_POST['r_rid']) & isset($_POST['r_field'])) {

$field = mysql_real_escape_string(trim($_POST['r_field']));

//use the field_attributes table to get a list of fields to display for this reagent type
    $sql = "SELECT " . $field . " FROM reagents WHERE r_rid = '" . mysql_real_escape_string(trim($_POST['r_rid'])) . "' LIMIT 0, 100";
    $query = mysql_query($sql);

//should only return one matched row with one value
    if (mysql_num_rows($query) == 1) {
        $row = mysql_fetch_assoc($query);

        $value = $row[$field];
        
        //return JSON, the variable $_GET["callback"] originally sent a "?"
        $response = array(
            rows => mysql_num_rows($query),
            return_value => $value);
        echo json_encode($response);
    }
    //else return rows =0
    else {
        $response = array(rows => '-1');
        echo json_encode($response);
    }
}
//else return rows=0
else {
    $response = array(rows => '0');
    echo json_encode($response);
}
?>
