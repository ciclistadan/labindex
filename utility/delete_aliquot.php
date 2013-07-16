<?php session_start();

if(isset($_SESSION['userid'])){
    require_once '../../secret/database2.php';

//check to make sure the function sent a value for the reagent row
    if (isset($_POST['aq_aqid'])) {

        $aq_aqid    = mysqli_real_escape_string($link, trim($_POST['aq_aqid']));
        $request   = "DELETE FROM labindex.aliquots WHERE aliquots.aq_aqid = ".$aq_aqid;

    //make the delete request
        mysqli_query($link, $request);

        $response = array(
            request => $request,
            rows => mysqli_affected_rows($link)
            );
        echo json_encode($response);
    }
//not properly POSTed
    else {
        $response = array(
            rows => 0,
            status => 0
            );
        echo json_encode($response);
    }

    mysqli_close($link);

} else {
    $response = array(status => '0');
    echo json_encode($response);
}
?>
