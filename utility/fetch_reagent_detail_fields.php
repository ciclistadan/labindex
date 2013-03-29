<?php

session_start();
require_once 'database.php';

//check to make sure the function sent a value for the type of reagent
// currently this only checks that there is a value, if a strings is sent that is not a valid type the 'all' fields are returned
// TODO: further validation that $_POST['r_reagent_type'] is an acceptable reagent type 
if (isset($_POST['r_reagent_type'])) {

//use the field_attributes table to get a list of fields to display for this reagent type
    $sql_fields = "SELECT * FROM field_attributes WHERE field_attr_tags = '" . mysql_real_escape_string(trim($_POST['r_reagent_type'])) . "' OR field_attr_tags = 'all' LIMIT 0, 100";
    $query = mysql_query($sql_fields);

    if (mysql_num_rows($query) > 0) {
        $fields = array();

        //fetch each query row and push it into the $fields object
        for ($x = 0; $x < mysql_num_rows($query); $x++) {
            $row = mysql_fetch_assoc($query);

            $new = array(
                field_attr_column_name => $row['field_attr_column_name'],
                field_attr_full_name => $row['field_attr_full_name']);

            array_push($fields, $new);
        }




        //return JSON, the variable $_GET["callback"] originally sent a "?"
        $response = array(
            rows   => mysql_num_rows($query),
            fields =>$fields);
        echo json_encode($response);
    }
    //else return rows =0
    else {
        $response = array(rows   => '-1');
        echo json_encode($response);
    }
}
//else return rows=0
else {
        $response = array(rows   => '0');
        echo json_encode($response);
}
?>
