<?php session_start();

if(isset($_SESSION['userid'])){
    require_once '../../secret/database2.php';

//check to make sure the function sent a value for the type of reagent
// currently this only checks that there is a value, if a strings is sent that is not a valid type the 'all' fields are still returned
// TODO: further validation that $_POST['r_reagent_type'] is an acceptable reagent type
    if (isset($_POST['r_reagent_type'])) {

        $colname = "field_order_". mysqli_real_escape_string($link, trim($_POST['r_reagent_type']));
//use the field_attributes table to get a list of fields to display for this reagent type
        $query = "SELECT * FROM field_attributes WHERE ".$colname." > 0  ORDER BY ".$colname." LIMIT 0, 100";

        $result = mysqli_query($link, $query);

        if (mysqli_affected_rows($link) > 0) {
            $fields = array();

        //fetch each result row, since this is a POST you need to  push it into the $fields object and return an object

            while ($row = mysqli_fetch_assoc($result)) {
                $new = array(
                    field_attr_column_name => $row['field_attr_column_name'],
                    field_attr_full_name => $row['field_attr_full_name'],
                    field_class  => $row['field_class'],
                    field_column => $row['field_column']);

                array_push($fields, $new);
            }

        //return JSON, the variable $_GET["callback"] originally sent a "?"
            $response = array(
                rows   => mysqli_affected_rows($link),
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
} else {
    $response = array(status => '0');
    echo json_encode($response);
}
?>
