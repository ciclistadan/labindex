<?php session_start();

if(isset($_SESSION['userid'])){
    require_once '../../secret/database2.php';

    $request   = "SELECT c_cid, c_cname, c_temp FROM containers ORDER BY c_cname";

//make the query and hash the results if it returns without error
    if($result = mysqli_query($link, $request)){
        $values = array();

        // initialize array with rows array and empty containers array

        $containers = array();

        while ($row = mysqli_fetch_assoc($result)) {

            // set multiple values for each aliquot
            foreach ($row as $key => $value) {
                $values[$key] = $value;
            }
            array_push($containers, $values);

        }

        $response = array(
            rows => mysqli_num_rows($result),
            containers => $containers
            );
        echo json_encode($response);
    }
    else {
        $response = array(rows => '0');
        echo json_encode($response);
    }

} else {
    $response = array(status => '0');
    echo json_encode($response);
}
?>