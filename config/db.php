<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "futurepath_db"; // අලුත් database නම මෙතනට දැම්මා

try {
    // Database connection එක සාදනවා
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    
    // Errors පෙන්නන්න මේක දානවා
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully"; 
} catch(PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
?>