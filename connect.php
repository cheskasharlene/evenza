<?php
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "evenza";

try {
    $pdo = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection Failed: " . $e->getMessage());
}

$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

function executeQuery($query)
{
    $conn = $GLOBALS['conn'];
    return mysqli_query($conn, $query);
}
?>