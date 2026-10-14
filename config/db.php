<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "futurepath_db"; 

try {
    // Create Database connection 
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    
    // To Show Errors 
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully"; 
} catch(PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
?>