<?php //File to Fill DB to Random Data
require_once ('db.php');
$ini = include('configs/config.php');
$db = new database();

$dbName = $ini['tableNameToGenerateData'];
$iterate = $ini['iterateToGenerateData'];
for ($i = 1; $i <= $iterate; $i++) {
    $date = date("Y-m-d H:i:s", mt_rand(1, 2147385600));
    $name = "Test$i";
    $quantity = mt_rand(1, 100);
    $distance = mt_rand(1, 5000);
    $db->dbInsert("INSERT INTO $dbName (Date, Name, Quantity, Distance) 
                            VALUES ('$date', '$name', '$quantity', '$distance')");
}
print("$iterate random rows created!");
