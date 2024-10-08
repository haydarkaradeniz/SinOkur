<?php

include ("connection.php");

// Bağlantı oluşturma
$conn = new mysqli($servername, $username, $password, $database);

//post parameters
$userId = $_POST['userId'];
$imdbId = $_POST['imdbId'];

// Bağlantıyı kontrol etme
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

$sql = "select * from imdb_user_list where user_id = ? and imdb_id = ? ";

$stmt = $conn->prepare($sql);
//i - integer, d - double, s - string, b - BLOB
$stmt->bind_param("is", $userId, $imdbId);
// set parameters and execute
$stmt->execute();
$result = $stmt->get_result(); 
$movieListData = $result->fetch_all(MYSQLI_ASSOC);
$row_count = $result->num_rows;

$result->close();
$stmt->close();
$conn->close();


echo '{ "listData":[';
$current_row = 1;
foreach ($movieListData as $key => $movieData) {
	echo '{';
	echo '"userId":"'.$movieData['user_id'].'",';
	echo '"listId":"'.$movieData['list_id'].'",';
	echo '"imdbId":"'.$movieData['imdb_id'].'"';
	echo '}';
	if($current_row != $row_count) {
		echo ',';
		$current_row++;
	}
	

}
echo ']}';


	
?>