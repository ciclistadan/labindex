<?php
session_start();
require_once 'database2.php';

//check to make sure the function sent a value for the reagent row
if (isset($_POST['aq_rid'])) {

    $aq_rid    = mysql_real_escape_string(trim($_POST['aq_rid']));
    $request   = "INSERT INTO labindex.aliquots SET aq_rid = '".$aq_rid."'";

    //make the insert request
    mysqli_query($link, $request);

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
