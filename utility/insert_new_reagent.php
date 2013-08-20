<?php session_start();

$url = '../index.php';
if(isset($_SESSION['userid'])){
    require_once '../../secret/database2.php';

//check to make sure the function sent a value for the reagent row
    if (isset($_POST['r_reagent_type'])) {

        $r_reagent_type    = mysqli_real_escape_string($link, trim($_POST['r_reagent_type']));
        $query   = "INSERT INTO reagents SET  r_reagent_type = '".$r_reagent_type."', r_systematicname='new ".$r_reagent_type."'";

    //make the insert query
        mysqli_query($link, $query);


        $page = $url.'?r_rid='.mysqli_insert_id($link);
        header("Location: ".$page);
        exit;
    }
//else return rows=0
    else {
        // not posted correctly, unclear what type of reagent to insert
       header("Location: ".$url);
       exit;
   }

   mysqli_close($link);
} else {
    // not authenticated
    header("Location: ".$url);
    exit;
}

?>
