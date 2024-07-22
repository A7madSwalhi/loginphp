<?php

$database_info = include __DIR__ . "/database.php";

$connect = mysqli_connect(
    $database_info['servername'],
    $database_info['username'],
    $database_info['password'],
    $database_info['database']
);
