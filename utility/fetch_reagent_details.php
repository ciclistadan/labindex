<?php session_start();

if(isset($_SESSION['userid'])){
    require_once '../../secret/database2.php';

//If the url contains the parameter "keyword" clean it up and search
    if (isset($_GET['r_rid']) & isset($_GET['r_reagent_type'])) {

//use the field_attributes table to get a list of field names for this reagent type
        $sql_fields = "SELECT field_attr_column_name FROM field_attributes WHERE field_attr_tags = '" . mysqli_real_escape_string($link, trim($_GET['r_reagent_type'])) . "' OR field_attr_tags = 'all'";
        $query_fields = mysql_query($sql_fields);
        $field_array = array();

        if (mysql_num_rows($query_fields) > 0) {
            for ($x = 0; $x < mysql_num_rows($query_fields); $x++) {
                $row = mysql_fetch_assoc($query_fields);
            //create a php aray of fields to return for this reagent type
                array_push($field_array, $row['field_attr_column_name']);
            }
    } else {//field lookup failed
    }
//TODO
    $json = "({field_array:'". implode(', ',$field_array)."'";


//retrive details
        $sql = "SELECT *  FROM reagents WHERE r_rid = " . mysqli_real_escape_string($link, trim($_GET['r_rid']));
        $query = mysql_query($sql);

//verify that the query returned a single reagent
        if (mysql_num_rows($query) === 1) {
        //make an array of json objects
            $json .= ",rows:'" . mysql_num_rows($query) . "', fields:[";

        //should only be one row so we don't need to loop
            $row = mysql_fetch_assoc($query);

        //append each key:value pair of each field for this reagent
            $json .= "{";
            foreach ($field_array as $item) {
            //append each key:value pair to from this row to the array
                $json .= $item . ":'" . $row[$item] . "', ";
            }

            $json = trim($json, ", ");
            $json .= $json . "}";

//return JSON, the variable $_GET["callback"] originally sent a "?"
            $response = $_GET["callback"] . $json;
            echo $response;
        }
//else return rows =0
        else {
            $json = "({rows:'0'})";
            $response = $_GET["callback"] . $json;
            echo $response;
        }
    } else {
        echo "request was not posted properly";
    }
} else {
    $response = array(status => '0');
    echo json_encode($response);
}
?>