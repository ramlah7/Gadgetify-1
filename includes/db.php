<?php

$servername="localhost";
$username = "root";
$password = "";
$dbname= "Gadgetify";

$conn = new mysqli($servername,$username,$password,$dbname, 3307);

if ( $conn -> connect_error){
    die("connection failed.. ".$conn->connect_error);

}

?>