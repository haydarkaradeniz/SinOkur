<?php

include ("connection.php");


// Bağlantı oluşturma
$conn = new mysqli($servername, $username, $password, $database);

//post parameters
$userId = $_POST['userId'];
$listId = $_POST['listId'];
$movieId= $_POST['movieId'];
$userRating= $_POST['userRating'];

//return value
$success = FALSE;

// Bağlantıyı kontrol etme
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}


//get mybox_user_movie_data
$sql = "select * from mybox_user_movie_data where user_id = ? and movie_id = ?";
$stmt = $conn->prepare($sql);
//i - integer, d - double, s - string, b - BLOB
$stmt->bind_param("is", $userId, $movieId);
$success = $stmt->execute();
$result = $stmt->get_result();
$row_count = $result->num_rows;

if($success === TRUE) {
	if($row_count > 0) {
		$sql = "update mybox_user_movie_data set user_rating = ? where user_id = ? and movie_id = ?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("iis", $userRating, $userId, $movieId);
		$success = $stmt->execute() === TRUE;
	} else {
		$sql = "insert into mybox_user_movie_data(user_id, movie_id, user_rating) values ( ?, ?, ? )";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("isi", $userId, $movieId, $userRating);
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
