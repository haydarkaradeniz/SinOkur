<?php

include ("connection.php");


// Bağlantı oluşturma
$conn = new mysqli($servername, $username, $password, $database);

//post parameters
$userId = $_POST['userId'];
$imdbId= $_POST['imdbId'];
$userRating= $_POST['userRating'];

//return value
$success = FALSE;

// Bağlantıyı kontrol etme
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}


//get imdb_list_data_property
$sql = "select * from imdb_list_data_property where user_id = ? and imdb_id = ?";
$stmt = $conn->prepare($sql);
//i - integer, d - double, s - string, b - BLOB
$stmt->bind_param("is", $userId, $imdbId);
$success = $stmt->execute();
$result = $stmt->get_result();
$row_count = $result->num_rows;

if($success === TRUE) {
	if($row_count > 0) {
		$sql = "update imdb_list_data_property set user_rating = ? , update_date = CURRENT_TIMESTAMP where user_id = ? and imdb_id = ?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("iis", $userRating, $userId, $imdbId);
		$success = $stmt->execute() === TRUE;
	} else {
		$sql = "insert into imdb_list_data_property(user_id, imdb_id, user_rating) values ( ?, ?, ? )";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("isi", $userId, $imdbId, $userRating);
		$success = $stmt->execute() === TRUE;
	}
}

$result->close();
$stmt->close();
$conn->close();

if($success === TRUE) {
	echo 'Film puanı başarı ile güncellenmiştir.';
} else {
	echo 'Bir hata oluştu, lütfen tekrar deneyiniz.';
}
?>