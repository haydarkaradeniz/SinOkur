<?php

include ("connection.php");

// Bağlantı oluşturma
$conn = new mysqli($servername, $username, $password, $database);

//post parameters
$userId = $_POST['userId'];

//return value
$success = FALSE;

// Bağlantıyı kontrol etme
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Bağlantı oluşturma
$conn = new mysqli($servername, $username, $password, $database);

$sql = "select t1.list_id, t1.imdb_id 
from (select min(tx.list_id) list_id, tx.imdb_id from (select * from imdb_user_list where user_id = ?) tx group by tx.imdb_id) t1 inner join imdb_list_data t2  
on t1.imdb_id = t2.imdb_id where t2.imdb_type = 'movie' and t2.movie_type = 'movie' and t2.title_tr is null order by t1.imdb_id";

$stmt = $conn->prepare($sql);
//i - integer, d - double, s - string, b - BLOB
$stmt->bind_param("i", $userId);
// set parameters and execute
$stmt->execute();
$result = $stmt->get_result(); 
$userRatingData = $result->fetch_all(MYSQLI_ASSOC);
$row_count = $result->num_rows;

$result->close();
$stmt->close();
$conn->close();

echo '{ "testData":[';
$current_row = 1;
foreach ($userRatingData as $key => $movieData) {
	echo '{';
	echo '"userId":"'.$userId.'",';
	echo '"listId":"'.$movieData['list_id'].'",';
	echo '"imdbId":"'.$movieData['imdb_id'].'"';
	echo '}';
	if($current_row != $row_count) {
		echo ',';
		$current_row++;
	}
}
echo '],';
echo '"rowCount":"'.$row_count.'"';
echo '}';
?>