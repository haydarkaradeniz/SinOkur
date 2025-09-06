<?php

include ("connection.php");


// Bağlantı oluşturma
$conn = new mysqli($servername, $username, $password, $database);

//post parameters
$userId = $_POST['userId'];
$listId = $_POST['listId'];
$header = $_POST['header'];

//return value
$success = FALSE;

// Bağlantıyı kontrol etme
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}


//get imdb_user_list_meta
$row_count = 0;
$sql = "select * from imdb_user_list_meta where user_id = ? and list_id = ?";
$stmt = $conn->prepare($sql);
//i - integer, d - double, s - string, b - BLOB
$stmt->bind_param("is", $userId, $listId);
$success = $stmt->execute();
$result = $stmt->get_result();
$row_count = $result->num_rows;

if($success === TRUE) {
	if($row_count > 0) {	
		$sql = "update imdb_user_list_meta set header = ? , update_date = CURRENT_TIMESTAMP where user_id = ? and list_id = ? ";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("sis", $header, $userId, $listId); 
		$success = $stmt->execute() === TRUE;
	} else {
		$sql = "insert into imdb_user_list_meta(user_id, list_id, header) values ( ? ,  UUID(), ?)";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("is", $userId, $header);
		$success = $stmt->execute() === TRUE;
	}
}


$result->close();
$stmt->close();
$conn->close();

if($success === TRUE) {
	echo 'Listeniz güncellenmiştir';
} else {
	echo 'Bir hata oluştu, lütfen tekrar deneyiniz.';
}
?>