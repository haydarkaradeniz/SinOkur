<?php

include ("connection.php");


// Bağlantı oluşturma
$conn = new mysqli($servername, $username, $password, $database);

//post parameters
$userId = $_POST['userId'];
$listId = $_POST['listId'];

//return value
$success = FALSE;

// Bağlantıyı kontrol etme
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}


//delete imdb_user_list_meta
$sql = "delete from imdb_user_list_meta where user_id = ? and list_id = ?";
$stmt = $conn->prepare($sql);
//i - integer, d - double, s - string, b - BLOB
$stmt->bind_param("is", $userId, $listId);
$success = $stmt->execute();


if($success) {
	//delete imdb_user_list
	$sql = "delete from imdb_user_list where user_id = ? and list_id = ?";
	$stmt = $conn->prepare($sql);
	//i - integer, d - double, s - string, b - BLOB
	$stmt->bind_param("is", $userId, $listId);
	$success = $stmt->execute();
}

$stmt->close();
$conn->close();

if($success === TRUE) {
	echo 'Listeniz güncellenmiştir';
} else {
	echo 'Bir hata oluştu, lütfen tekrar deneyiniz.';
}
?>