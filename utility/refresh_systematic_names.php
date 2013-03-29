<?php

session_start();
require_once 'database.php';

for ($i = 0; $i <= 99999; $i++) {
    
    $sql = 'SELECT
     reagents.r_mfrname,
     reagents.a_ab_clonality,
     reagents.a_ab_hostspecies,
     reagents.a_ab_targetspecies,
     reagents.r_ab_antigen,
     reagents.a_ab_clone

     FROM reagents 
     WHERE reagents.r_rid="' . $i . '" ';

    $query = mysql_query($sql);
    
    if (mysql_num_rows($query) === 1) {
        $row = mysql_fetch_assoc($query);
        
        //TODO incorporate logic to remove inappropriate spaces in systematicname generation
        $name = $row["r_mfrname"] . ' ' . $row["a_ab_hostspecies"]. ' ' . $row["a_ab_clonality"]  . ' anti ' . $row["a_ab_targetspecies"] . ' ' . $row["r_ab_antigen"] . ' (' . $row["a_ab_clone"].')';
    
        $out = mysql_query('UPDATE reagents SET reagents.r_systematicname = "'.$name.'" WHERE reagents.r_rid="'.$i.'"');
        echo($out);
        }
}
?>
