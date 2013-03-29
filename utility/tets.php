<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
require_once 'database.php';




$sql = "SELECT * FROM reagents WHERE r_rid = '20' LIMIT 0, 100";
$query = mysql_query($sql);

//should only return one matched row
if (mysql_num_rows($query) == 1) {
    $values = array();
    $row = mysql_fetch_assoc($query);

    foreach ($row as $key => $value) {
        $values[$key] = $value;
    }
//    print_r($values);

    echo $values[r_rid];
    
}
else{}
?>
