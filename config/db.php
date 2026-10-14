<?php
// ==========================================
// LIVE DATABASE CONFIG (InfinityFree)
// ==========================================
$host = "sql110.infinityfree.com";               // ✅ Hostname (Screenshot එකෙන්)
$user = "if0_43063754";                          // ✅ Username (Screenshot එකෙන්)
$password = "noni5xpx9Apu";                      // ⚠️ ඔබේ password එක දාන්න!
$dbname = "if0_43063754_futurepath_db";          // ✅ Database Name

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
?>