<?php session_start();

if(isset($_SESSION['userid'])){
    require_once '../../secret/database2.php';

//check to make sure the function sent a value for the reagent row
    if (isset($_POST['r_reagent_type'])) {

        $r_reagent_type    = mysqli_real_escape_string($link, trim($_POST['r_reagent_type']));
        $query   = "INSERT INTO reagents SET  r_reagent_type = '".$r_reagent_type."', r_systematicname='new ".$r_reagent_type."'";

    //make the insert query
        mysqli_query($link, $query);

        $response = array(
            query   => $query,
            rows    => mysqli_affected_rows($link),
            new_rid => mysqli_insert_id($link)
            );
        echo json_encode($response);
    }
//else return rows=0
    else {
        $response = array(rows => '0');
        echo json_encode($response);
    }

    mysqli_close($link);
} else {
    $response = array(status => '0');
    echo json_encode($response);
}
?>
