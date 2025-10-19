<?php
$db_server="localhost";
$db_user="root";
$db_pass="";
$db_name="language_learning";
$conn="";

$conn= mysqli_connect($db_server,
                        $db_user,
                        $db_pass,
                        $db_name);
 
if($conn){
    echo"✅ You are connected to the database!" . "<br>";
}
else{
     echo"❌ Could not connect to the database!" . "<br>";
}
    
?>