<?php

include ("connection.php");


// Bağlantı oluşturma
$conn = new mysqli($servername, $username, $password, $database);

//post parameters
$userId = $_POST['userId'];
$listId = $_POST['listId'];
$imdbId = $_POST['imdbId'];

//return value
$success = FALSE;

// Bağlantıyı kontrol etme
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}


//get imdb_data
$sql = "select * from imdb_list_data where imdb_id = ?";
$stmt = $conn->prepare($sql);
//i - integer, d - double, s - string, b - BLOB
$stmt->bind_param("s", $imdbId);
$success = $stmt->execute();
$result = $stmt->get_result();
$row_count = $result->num_rows;

if($success === TRUE) {
	if($row_count > 0) {
		if($_POST['imdbType'] === 'movie') {
			$sql = "update imdb_list_data set poster = ? , title = ? , year = ? , runtime = ? , imdb_rating = ? , imdb_votes = ? , movie_type = ? , update_date = CURRENT_TIMESTAMP, title_tr = ? , director = ? where imdb_id = ? ";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("ssisssssss", $_POST['poster'], $_POST['title'], $_POST['year'], $_POST['runtime'], $_POST['imdbRating'], $_POST['imdbVotes'], $_POST['movieType'], $_POST['titleTr'], $_POST['director'], $_POST['imdbId']);
			$success = $stmt->execute() === TRUE;
		} else {
			$sql = "update imdb_list_data set person_type = ? , name = ? , place_of_birth = ? , birthday = ? , profile_path = ?  , update_date = CURRENT_TIMESTAMP where imdb_id = ? ";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("ssssss", $_POST['personType'], $_POST['name'], $_POST['placeOfBirth'], $_POST['birthday'], $_POST['profilePath'], $_POST['imdbId']);
			$success = $stmt->execute() === TRUE;
		}
	} else {
		if($_POST['imdbType'] === 'movie') {
			$sql = "insert into imdb_list_data(imdb_id,poster,title,year,runtime,imdb_rating,imdb_votes,imdb_type, movie_type, title_tr, director) values ( ? , ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("sssisssssss", $_POST['imdbId'], $_POST['poster'], $_POST['title'], $_POST['year'], $_POST['runtime'], $_POST['imdbRating'], $_POST['imdbVotes'], $_POST['imdbType'], $_POST['movieType'], $_POST['titleTr'], $_POST['director']);
			$success = $stmt->execute() === TRUE;
		} else {
			$sql = "insert into imdb_list_data(imdb_id,person_type,name,place_of_birth,birthday,profile_path,imdb_type) values ( ? , ?, ?, ?, ?, ?, ? )";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("sssssss", $_POST['imdbId'], $_POST['personType'], $_POST['name'], $_POST['placeOfBirth'], $_POST['birthday'], $_POST['profilePath'], $_POST['imdbType']);
			$success = $stmt->execute() === TRUE;
		}
	}
}



//get imdb_user_list
$sql = "select * from imdb_user_list where user_id = ? and list_id = ? and imdb_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $userId, $listId, $imdbId);
$success = $stmt->execute();
$result = $stmt->get_result();
$row_count = $result->num_rows;

if($success === TRUE && $row_count === 0) {
	$sql = "insert into imdb_user_list(user_id, list_id, imdb_id) values ( ? , ?, ? )";	
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("iss", $userId, $listId, $imdbId);
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