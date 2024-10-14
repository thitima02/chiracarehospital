<?php 
function getConnection() {
    $host = 'localhost';
    $dbname = 'chiracare_follow_up_db';
    $username = 'root';
    $password = '';

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        // Handle error here or log the error
        return null; // or handle failure more specifically
    }
}
?>

