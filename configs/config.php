<?php
return [
    'db' => [
        'host' => '',
        'port' => '',
        'dbname' => '',
        'username' => '',
        'password' => '',
        'dbtype' => ''], //e.g mysql
    'excludedColumns' => ['Date'], //Its can many of them, list with comas
    'dataPerPage' => 30, //For Pagination
    'tableNameToGenerateData' => '', //For Create Random Data in Table
    'iterateToGenerateData' => 100 //For Create Random Data in Table
]
?>