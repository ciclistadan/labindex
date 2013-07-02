<?php

session_start();
require_once 'database2.php';

//check to make sure the function sent a value for the reagent id
if (isset($_POST['aq_rid'])) {
    $id        = mysql_real_escape_string(trim($_POST['aq_rid']));


    $request   = "SELECT aq_aqid, aq_amount, aq_conc, aq_lot, c_cid, c_temp, c_cname
                    FROM aliquots, containers
                    WHERE containers.c_cid = aliquots.aq_cid
                    AND aliquots.aq_rid = '".$id."'";

    if($result = mysqli_query($link, $request)){
        $values = array();

        // initialize array with rows array and empty aliquots array

        $aliquots = array();

        while ($row = mysqli_fetch_assoc($result)) {
            
            // set multiple values for each aliquot
            foreach ($row as $key => $value) {
                $values[$key] = $value;
            }
            array_push($aliquots, $values);

        }
        
        $response = array(
            rows => mysqli_num_rows($result),
            aliquots => $aliquots
            );
        echo json_encode($response);
    }
    else {
        $response = array(rows => '0');
        echo json_encode($response);
    }
}    
else {
    $response = array(rows => '-1');
    echo json_encode($response);
}
?>