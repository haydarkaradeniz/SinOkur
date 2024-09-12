<?php

include ("connection.php");


// Bağlantı oluşturma
$conn = new mysqli($servername, $username, $password, $database);

//post parameters
$userId = $_POST['userId'];
$listId = $_POST['listId'];
$movieId = $_POST['movieId'];

//return value
$success = FALSE;

// Bağlantıyı kontrol etme
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}


//get mybox_movie_data
$sql = "select * from mybox_movie_data where movie_id = ?";
$stmt = $conn->prepare($sql);
//i - integer, d - double, s - string, b - BLOB
$stmt->bind_param("s", $movieId);
$success = $stmt->execute();
$result = $stmt->get_result();
$row_count = $result->num_rows;

if($success === TRUE) {
	if($row_count > 0) {
		$sql = "update mybox_movie_data set poster = ? , title = ? , year = ? , runtime = ? , director = ? , writer = ? , actors  = ? , imdb_rating = ? , imdb_votes = ? where movie_id = ? ";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("ssisssssss", $_POST['poster'], $_POST['title'], $_POST['year'], $_POST['runtime'], $_POST['director'], $_POST['writer'], $_POST['actors'], $_POST['imdbRating'], $_POST['imdbVotes'], $_POST['movieId']);
		$success = $stmt->execute() === TRUE;
	} else {
		$sql = "insert into mybox_movie_data(movie_id,poster,title,year,runtime,director,writer,actors,imdb_rating,imdb_votes) values ( ? , ?, ?, ?, ?, ?, ?, ?, ?, ? )";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("sssissssss", $_POST['movieId'], $_POST['poster'], $_POST['title'], $_POST['year'], $_POST['runtime'], $_POST['director'], $_POST['writer'], $_POST['actors'], $_POST['imdbRating'], $_POST['imdbVotes']);
		$success = $stmt->execute() === TRUE;
	}
}


//get mybox_user_list
$sql = "select * from mybox_user_list where user_id = ? and list_id = ? and movie_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $userId, $listId, $movieId);
$success = $stmt->execute();
$result = $stmt->get_result();
$row_count = $result->num_rows;

if($success === TRUE && $row_count === 0) {
	$sql = "insert into mybox_user_list(user_id, list_id, movie_id) values ( ? , ?, ? )";	
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("iss", $userId, $listId, $movieId);
	$success = $stmt->execute() === TRUE;
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