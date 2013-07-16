<?php session_start();

if(isset($_SESSION['userid'])){
    require_once '../../secret/database2.php';

//TODO edit this function and the associated js to fetch all field values at once

//check to make sure the function sent a value for the reagent id
    if (isset($_POST['r_rid']) & isset($_POST['r_field'])) {

        $field = mysqli_real_escape_string($link, trim($_POST['r_field']));

//use the field_attributes table to get a list of fields to display for this reagent type
        $query = "SELECT " . $field . " FROM reagents WHERE r_rid = '" . mysqli_real_escape_string($link, trim($_POST['r_rid'])) . "' LIMIT 0, 100";
        $result = mysqli_query($link, $query);

//should only return one matched row with one value
        if (mysqli_affected_rows($link)==1) {
            $row = mysqli_fetch_assoc($result);
            $value = $row[$field];

        //return JSON, the variable $_GET["callback"] originally sent a "?"
            $response = array(
                rows => mysqli_affected_rows($link),
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
} else {
    $response = array(status => '0');
    echo json_encode($response);
}
?>
