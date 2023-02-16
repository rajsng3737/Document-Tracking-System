<?php
$servername = "localhost";
$database ="dts";
$username = "root";
$password = "";
$conn = mysqli_connect($servername,$username,$password,$database);
if($conn == false){
    die("Unable to Connect");
}
?>