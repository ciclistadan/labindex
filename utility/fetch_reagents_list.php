<?php session_start();
require_once '../../secret/database2.php';

//create the keyword search if there is a defined keyword
if (isset($_GET['keyword'])) {
    $keyword = mysqli_real_escape_string($link, trim($_GET['keyword']));
    $sql_keyword_WHERE = "reagents.r_systematicname like '%" . $keyword . "%'
                       OR reagents.r_altname like '%" . $keyword . "%'
                       OR reagents.r_mfrname like '%" . $keyword . "%'
                       OR reagents.r_source like '%" . $keyword . "%'
                       OR reagents.a_ab_modification like '%" . $keyword . "%'
                       OR reagents.a_ab_ifa like '%" . $keyword . "%'
                       OR reagents.a_ab_wb like '%" . $keyword . "%'
                       OR reagents.a_ab_other like '%" . $keyword . "%'
                       OR reagents.r_notes like '%" . $keyword . "%'";
}

//count and concatenate additional filter key=values pairs
$field_count = 0;
$sql_filter_WHERE = "";
$fields = array('r_rid','r_reagent_type','r_systematicname','r_altname','r_rating','r_link','r_notes','r_mfrname','r_mfrid','r_source','r_flag','r_ab_antigen','a_ab_clone','a_ab_targetspecies','a_ab_clonality','a_ab_hostspecies','a_ab_modification','a_ab_ifa','a_ab_wb','a_ab_other');

// print_r($fields);
foreach ($fields as $field){
    if(isset($_GET[$field])){
        $field_count++;
        $sql_filter_WHERE .= $field."='".$_GET[$field]."' AND ";
    }
}

//combine the keyword and filter WHERE statements
if(strlen($sql_keyword_WHERE) > 0 && strlen($sql_filter_WHERE) > 0){
    $sql_WHERE = "WHERE (".substr(trim($sql_filter_WHERE),0,-3)." AND ".$sql_keyword_WHERE.")";
}
else if(strlen($sql_keyword_WHERE) > 0){
    $sql_WHERE = "WHERE ".$sql_keyword_WHERE;
}
else if(strlen($sql_filter_WHERE) > 0){
    $sql_WHERE = "WHERE ".substr(trim($sql_filter_WHERE),0,-3);
}
else {return -1;}


//form a query

$sql_SELECT = 'SELECT reagents.r_rid, reagents.r_systematicname, reagents.r_reagent_type ';
$sql_COUNT = 'SELECT count(*) AS totalCount ';
$sql_FROM = 'FROM reagents ';


///////////////////////////////////////////////////////////////////////////////
// determine how many pages are needed

$query1 = $sql_COUNT . $sql_FROM . $sql_WHERE;

// echo $query1;
// perform a query to retreive the total result1 count without limits

if ($result1 = mysqli_query($link, $query1)) {
    //TODO incorporate check for rows>0
    // if total query count returns something continue, else callback rows:0

        //figure out how many $pages this query will need if you only allow $qty per page
        $row = mysqli_fetch_assoc($result1);
        $totalRows = $row['totalCount'];

        //check for $_SESSION['qty'] and set quantity per page you want to return
        if (isset($_SESSION['qty'])) {$qty = mysqli_real_escape_string($link, trim($_SESSION['qty']));}
        else {$qty = 10;}

        $pages = ceil($totalRows / $qty);

        //create your json response object
        $json  = "({totalRows:'" . $totalRows . "'";
        $json .= " , pages:'"  . $pages . "'";

        //determine which page we want to show this time
        // if this is a new search set this to 1, otherwise looks for $GET_['page']
        $page = "";
        if (isset($_GET['page'])) { $page = $_GET['page']; }
        else { $page = 1; }
        $json .= " , page:'".$page."'";


        ///////////////////////////////////////////////////////////////////////////////
        //query #2 to SELECT the info
        //  set LIMIT now that we know which page we want to return
        $start = ($page*$qty)-$qty;
        $sql_LIMIT = " LIMIT " . $start . ", " . $qty;

        //Run the Query
        $query2 = $sql_SELECT . $sql_FROM . $sql_WHERE . $sql_LIMIT;

// print($query2);
        //fetch the desired set of reagent lines
        $result2 = mysqli_query($link, $query2);
        if (mysqli_affected_rows($link)>0) {
                //TODO incorporate check for rows>0
            //make an array of json objects
            $json .= ", rows:'" . mysqli_affected_rows($link) . "'";
            $json .= ", reagents:[";

            // fetch associative array
            while ($row = mysqli_fetch_assoc($result2)) {

                //append each key:value pair to from this row to the array
                $json .= "{                r_rid:'" . $row["r_rid"]
                        . "',   r_systematicname:'" . $row["r_systematicname"]
                        . "',   r_reagent_type:'"   . $row["r_reagent_type"]
                        . "'},";
            }

            //trim the last comma and close the JSON array
            // print($json);
            $json = substr($json, 0,-1);
            // print($json);
            $json .= "]})";


        }
        else{
            //no results found
            $json = "({rows:'0'})";
        }
}
else{
    //no results found
    $json = "({rows:'-1'})";
}

//return JSON, the variable $_GET["callback"] originally sent a "?"
$response = $_GET["callback"] . $json;
echo $response;

mysqli_close($link);


?>