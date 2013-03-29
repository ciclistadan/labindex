<?php

session_start();
require_once 'database.php';

//check to make sure the function sent a value for the reagent id
if (isset($_POST['r_rid'])) {

//use the field_attributes table to get a list of fields to display for this reagent type
    $sql = "SELECT * FROM reagents WHERE r_rid = '" . mysql_real_escape_string(trim($_POST['r_rid'])) . "' LIMIT 0, 100";
    $query = mysql_query($sql);

//should only return one matched row
    if (mysql_num_rows($query) == 1) {
        $values = array();
        $row = mysql_fetch_assoc($query);

        foreach ($row as $key => $value) {
            $values[$key] = $value;
        }

        //return JSON, the variable $_GET["callback"] originally sent a "?"
        $response = array(
            rows => mysql_num_rows($query),
            values => $values);
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
