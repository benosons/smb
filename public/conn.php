<?php

    $host     = getenv('HOST');
    $username = getenv('UNAME');
    $password = getenv('PWD');
    $dbname   = getenv('DB');

    $connection = new mysqli("$host", "$username", "$password", "$dbname");

    if($connection) {
       echo 'connected';
    } else {
        echo 'there has been an error connecting';
    }
?>
