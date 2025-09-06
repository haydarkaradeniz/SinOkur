<?php

include ("connection.php");

// Bağlantı oluşturma
$conn = new mysqli($servername, $username, $password, $database);

//post parameters
$userId = $_POST['userId'];

// Bağlantıyı kontrol etme
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

$sql = "select * from imdb_user_list_meta where user_id = ?";

$stmt = $conn->prepare($sql);
//i - integer, d - double, s - string, b - BLOB
$stmt->bind_param("i", $userId);
// set parameters and execute
$stmt->execute();
$result = $stmt->get_result(); 
$movieListData = $result->fetch_all(MYSQLI_ASSOC);
$row_count = $result->num_rows;




$sql = "select t1.list_id, t2.imdb_type, t2.movie_type, t2.person_type from imdb_user_list t1 left join imdb_list_data t2 on t1.imdb_id = t2.imdb_id where t1.user_id  = ?";
$stmt = $conn->prepare($sql);
//i - integer, d - double, s - string, b - BLOB
$stmt->bind_param("i", $userId);
// set parameters and execute
$stmt->execute();
$result = $stmt->get_result(); 
$userMovieListData = $result->fetch_all(MYSQLI_ASSOC);
$row_count2 = $result->num_rows;


$result->close();
$stmt->close();
$conn->close();


echo '{ "listData":[';
$current_row = 1;
foreach ($movieListData as $key => $movieData) {
	echo '{';
	echo '"userId":"'.$movieData['user_id'].'",';
	echo '"listId":"'.$movieData['list_id'].'",';
	echo '"header":"'.$movieData['header'].'"';
	echo '}';
	if($current_row != $row_count) {
		echo ',';
		$current_row++;
	}
}
echo '], "listMovieData":[';

$current_row = 1;
foreach ($userMovieListData as $key => $movieData) {
	echo '{';
	echo '"listId":"'.$movieData['list_id'].'",';
	echo '"imdbType":"'.$movieData['imdb_type'].'",';
	echo '"movieType":"'.$movieData['movie_type'].'",';
	echo '"personType":"'.$movieData['person_type'].'"';
	echo '}';
	if($current_row != $row_count2) {
		echo ',';
		$current_row++;
	}
}
echo ']}';

?>