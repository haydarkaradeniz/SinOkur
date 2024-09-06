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
$stmt = $conn->prepare("INSERT INTO user (id, name, surname) VALUES (?, ?, ?)");
//i - integer, d - double, s - string, b - BLOB
$stmt->bind_param("sss", $id, $name, $surname);

// set parameters and execute
$id = $_POST['id'];
$name = $_POST['name'];
$surname = $_POST['surname'];
if ($stmt->execute() === TRUE) {
    echo 'Veri başarıyla eklendi.';
} else {
    echo 'Veri ekleme hatası: ' . $conn->error;
}

$stmt->close();
$conn->close();



