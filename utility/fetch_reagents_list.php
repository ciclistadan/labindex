<?php session_start();
require_once '../../secret/database2.php';

// reagent searching for does not require authentication, this function inly returns systematicname of all reagents

if ( isset( $_POST['search'] ) ){
    // get a list of all columns, this can be hard-coded once we stabilize the database structure
    $query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='labindex' AND TABLE_NAME='reagents' OR TABLE_NAME='containers' OR TABLE_NAME='aliquots' LIMIT 0,100";
    $result = mysqli_query($link, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $columns[] = $row['COLUMN_NAME'];
    }
    mysqli_free_result($result);

    $sql_LOGIC = " FROM reagents" 
                ." LEFT JOIN aliquots ON reagents.r_rid = aliquots.aq_rid" 
                ." LEFT JOIN containers ON containers.c_cid = aliquots.aq_cid"
                ." WHERE 1=1"; //TODO, incorporate initial WHERE query into real logic below

    //parse the search fields and terms, append to the sql_LOGIC string
    foreach ( $_POST['search'] as $row )
    {
        // if the '$row['field']' is a column name, match only in that column
        if (in_array($row['field'], $columns)) {
        $sql_LOGIC .= " AND ". mysqli_real_escape_string($link,$row['field']) ." like '%". mysqli_real_escape_string($link,$row['text']) ."%'";
        }

        // if the '$row['field']' is 'everywhere' or there is no match, create an OR search string of all applicable columns
        else{
            $sql_LOGIC .= " AND (";
            foreach($columns as $column){
                $sql_LOGIC .= $column." like '%". mysqli_real_escape_string($link,$row['text']) ."%' OR ";
            }
            $sql_LOGIC = substr($sql_LOGIC,0,-4);
            $sql_LOGIC .= ")";
        }

    }
}


///////////////////////////////////////////////////////////////////////////////
// determine how many pages are needed
$sql_COUNT  = "SELECT DISTINCT count(*) AS totalCount ";
$query = $sql_COUNT . $sql_LOGIC;

// perform a query to retreive the total result count without limits

if ($result = mysqli_query($link, $query)) {
    //TODO incorporate check for rows>0
    // if total query count returns something continue

        //figure out how many $pages this query will need if you only allow $qty per page
        $row = mysqli_fetch_assoc($result);
        $totalRows = $row['totalCount'];

        //check for $_SESSION['qty'] and set quantity per page you want to return
        if (isset($_SESSION['qty'])) {$qty = mysqli_real_escape_string($link, trim($_SESSION['qty']));}
        else {$qty = 10;}

        $pages = ceil($totalRows / $qty);

        //determine which page we want to show this time
        // if this is a new search set this to 1, otherwise looks for $GET_['page']
        // $page = "";
        if (isset($_POST['page'])) { $page = $_POST['page']; }
        else { $page = 1; }
   
        mysqli_free_result($result);


        ///////////////////////////////////////////////////////////////////////////////
        //query #2 to SELECT the info
        //  set LIMIT now that we know which page we want to return
        $start = ($page*$qty)-$qty;
        $sql_LIMIT = " LIMIT " . $start . ", " . $qty;
        $sql_SELECT = "SELECT DISTINCT reagents.r_rid, reagents.r_systematicname, reagents.r_reagent_type";

        //Run the Query
        $query = $sql_SELECT . $sql_LOGIC . $sql_LIMIT;

// print($query);
        //fetch the desired set of reagent lines
        $result = mysqli_query($link, $query);
        if (mysqli_affected_rows($link)>0) {
            //TODO incorporate check for rows>0

            $reagents = array();
            // fetch associative array
            while ($row = mysqli_fetch_assoc($result)) {
            
                foreach ($row as $key => $value) {
                    $values[$key] = $value;
                }
                array_push($reagents, $values);
            }

            $response = array(
                query => $query,
                totalRows => $totalRows,
                pages     => $pages,
                page      => $page,
                rows      => mysqli_affected_rows($link),
                reagents  => $reagents
            );



            mysqli_free_result($result);

        }
    else{$response = array(query => $query,rows => '0');
    }
}
else{$response = array(rows => '-1');
}

    
    echo json_encode($response);

mysqli_close($link);


?>
