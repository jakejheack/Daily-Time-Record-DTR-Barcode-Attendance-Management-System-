<?php

    function conns(){
        
$host = "localhost";
$username = "root";
$password = "";
$database = "dtrdbs";

$con = new mysqli($host, $username, $password, $database);

if($con->connect_error){
    echo $con->connect_error;
}else{

    return $con;
}

    }


    // SET PASSWORD FOR 'root'@'localhost' = PASSWORD('D@nieL2023');