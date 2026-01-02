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
    $errorCode = $e->getCode();
    $errorMessage = $e->getMessage();
    
    // Specific error handling for different connection issues
    if ($errorCode == 1045) {
        // Access denied - wrong credentials
        die("Database Connection Error: Invalid username or password. Please check your database credentials in connect.php");
    } elseif ($errorCode == 1049) {
        // Unknown database
        die("Database Connection Error: Database '$dbname' does not exist. Please check if the database name is correct in connect.php");
    } elseif ($errorCode == 2002 || strpos($errorMessage, 'Connection refused') !== false || strpos($errorMessage, 'Unknown host') !== false) {
        // Host connection issue
        die("Database Connection Error: Cannot connect to database host '$dbhost'. Please check if MySQL server is running and the host is correct in connect.php");
    } else {
        // Generic error
        die("Database Connection Error: " . $errorMessage . " (Error Code: $errorCode)");
    }
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