<?php

session_start();
require_once 'database.php';

//If the url contains the parameter "keyword" clean it up and search
if (isset($_GET['keyword'])) {

    $keyword = mysql_real_escape_string(trim($_GET['keyword']));

    $sql_SELECT = 'SELECT reagents.r_rid, reagents.r_systematicname, r_reagent_type ';
    $sql_COUNT = 'SELECT count(*) AS totalCount ';
    $sql_FROM = 'FROM reagents ';
    $sql_WHERE = 'WHERE reagents.r_systematicname like "%' . $keyword . '%" 
        OR reagents.r_altname like "%' . $keyword . '%"
        OR reagents.r_mfrname like "%' . $keyword . '%"
        OR reagents.r_source like "%' . $keyword . '%"
        OR reagents.a_ab_modification like "%' . $keyword . '%"
        OR reagents.a_ab_ifa like "%' . $keyword . '%"
        OR reagents.a_ab_wb like "%' . $keyword . '%"
        OR reagents.a_ab_other like "%' . $keyword . '%"
        OR reagents.r_notes like "%' . $keyword . '%"';


    //check for $_SESSION['qty'] and set quantity per page
    if (isset($_SESSION['qty'])) {
        $qty = mysql_real_escape_string(trim($_SESSION['qty']));
    } else {
        $qty = 20;
    }

    // determine how many pages are needed
    // retreive the total query count, without limits
    $sql1 = $sql_COUNT . $sql_FROM . $sql_WHERE;
    $query1 = mysql_query($sql1);


    // if total query count returns something, continue
    if (mysql_num_rows($query1) > 0) {

        $row = mysql_fetch_assoc($query1);
        //define the number of pages needed
        $pages = ceil($row['totalCount'] / $qty);
        $json = "({pages:'" . $pages . "'";


        //determine which page we want to show this time
        // if this is a new search set this to 1, otherwise looks for $GET_['page'];
        $page = "";

        // check for current page argument
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
        } else {
            $page = 1;
        }

        $json .= " , page:'".$page."'";

        //set limits now that we know which page we want to return
        $start = ($page*$qty)-$qty;
        $sql_LIMIT = " LIMIT " . $start . ", " . $qty;

        //Run the Query
        $sql = $sql_SELECT . $sql_FROM . $sql_WHERE . $sql_LIMIT;
        $query = mysql_query($sql);
        //verify that the query returned results
        if (mysql_num_rows($query) > 0) {
            //make an array of json objects
            $json .= ", rows:'" . mysql_num_rows($query) . "', reagents:[";
            //fetch each query row and append it to the json string
            for ($x = 0; $x < mysql_num_rows($query); $x++) {
                $row = mysql_fetch_assoc($query);

                //append each key:value pair to from this row to the array
                $json .= "{                r_rid:'" . $row["r_rid"]
                        . "',   r_systematicname:'" . $row["r_systematicname"]
                        . "',   r_reagent_type:'"   . $row["r_reagent_type"]
                        . "'}";

                //add comma if not last row, closing brackets if is
                if ($x < mysql_num_rows($query) - 1) {
                    $json .= ",";
                } else {
                    $json .= "]})";
                }
            }
            //return JSON, the variable $_GET["callback"] originally sent a "?"
            $response = $_GET["callback"] . $json;
            echo $response;

        }
        //else return rows =0
        else {
            $json = "({rows:'-1'})";
            $response = $_GET["callback"] . $json;
            echo $response;
        }
    }
    //else return rows=0
    else {
        echo $_GET["callback"] . "({rows:'0'})" ;
    }
}
// no keyword was supplied, return error
else {
    echo 'Parameter Missing in the URL; no keyword supplied';
}
?>