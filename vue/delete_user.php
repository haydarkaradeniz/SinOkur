<?php
$servername = "localhost";
$username = "root";
$password = "admin";
$database = "ccsowner";


// Bağlantı oluşturma
$conn = new mysqli($servername, $username, $password, $database);
// Bağlantıyı kontrol etme
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// prepare and bind
$stmt = $conn->prepare("DELETE FROM user WHERE ID = ?");
//i - integer, d - double, s - string, b - BLOB
$stmt->bind_param("s", $id);

// set parameters and execute
$id = $_POST['id'];
if ($stmt->execute() === TRUE) {
    echo 'Veri başarıyla silindi.';
} else {
    echo 'Veri silme hatası: ' . $conn->error;
}

$stmt->close();
$conn->close();



