<?php
$host = 'localhost';
$db = 'shopping_list_db'; 
$user = 'root'; 
$pass = ''; 

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Koneksi gagal: " . $e->getMessage();
}
?>
