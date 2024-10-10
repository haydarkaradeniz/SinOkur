<?php

include ("connection.php");

// Bağlantı oluşturma
$conn = new mysqli($servername, $username, $password, $database);

//post parameters
$userId = $_POST['userId'];
$imdbId= $_POST['imdbId'];

//return value
$success = FALSE;

// Bağlantıyı kontrol etme
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Bağlantı oluşturma
$conn = new mysqli($servername, $username, $password, $database);

$sql = "select t1.imdb_id, t2.user_rating, t1.score, t1.vote
from (select imdb_id,AVG(user_rating) score, count(user_rating) vote from imdb_list_data_property group by imdb_id) t1 left JOIN 
(select * from imdb_list_data_property where user_id = ? and imdb_id = ?) t2
on t1.imdb_id = t2.imdb_id where t1.imdb_id = ?";

$stmt = $conn->prepare($sql);
//i - integer, d - double, s - string, b - BLOB
$stmt->bind_param("iss", $userId, $imdbId, $imdbId);
// set parameters and execute
$stmt->execute();
$result = $stmt->get_result(); 
$userRatingData = $result->fetch_all(MYSQLI_ASSOC);
$row_count = $result->num_rows;

$result->close();
$stmt->close();
$conn->close();

echo '{ "userRatingData":{';
if($row_count > 0) {
	foreach ($userRatingData as $key => $data) {
		echo '"userRating":"'.$data['user_rating'].'",';
		echo '"score":"'.round($data['score'],1).'",';
		echo '"vote":"'.$data['vote'].'"';
		break;
	}
}

echo '}}';
?>