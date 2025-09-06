<?php

include ("connection.php");

// Bağlantı oluşturma
$conn = new mysqli($servername, $username, $password, $database);

//post parameters
$userId = $_POST['userId'];
$listId = $_POST['listId'];
$pageSize = $_POST['pageSize'];
$pageIndex = $_POST['pageIndex'];
$imdbType = $_POST['imdbType'];
$movieType = $_POST['movieType'];
$personType = $_POST['personType'];
$sortColumn = $_POST['sortColumn'];
$sortPosition = $_POST['sortPosition'];

$bind_param_values = array();
array_push($bind_param_values,$userId);
array_push($bind_param_values,$listId);
$bind_param_str = "is";
$sql = "select t1.list_id, t1.user_id, t2.*, t3.user_rating from imdb_user_list t1 inner join imdb_list_data t2 on t1.imdb_id = t2.imdb_id 
left join imdb_list_data_property t3 on t1.imdb_id = t3.imdb_id and t1.user_id  = t3.user_id 
where t1.user_id = ? and t1.list_id = ?";
if($imdbType) {
	$sql = $sql." and imdb_type = ?";
	$bind_param_str=$bind_param_str."s";
	array_push($bind_param_values,$imdbType);
}
if($personType) {
	$sql = $sql." and person_type = ?";
	$bind_param_str=$bind_param_str."s";
	array_push($bind_param_values,$personType);
}
if($movieType) {
	$sql = $sql." and movie_type = ?";
	$bind_param_str=$bind_param_str."s";
	array_push($bind_param_values,$movieType);
}
$sql = $sql." order by ".$sortColumn." ".$sortPosition." LIMIT ".$pageSize." OFFSET ".(((int)$pageIndex)-1)*((int)$pageSize);

$stmt = $conn->prepare($sql);
//i - integer, d - double, s - string, b - BLOB
$stmt->bind_param($bind_param_str, ...$bind_param_values);
// set parameters and execute
$stmt->execute();
$result = $stmt->get_result(); 
$imdbListData = $result->fetch_all(MYSQLI_ASSOC);
$row_count = $result->num_rows;

$result->close();
$stmt->close();
$conn->close();


echo '{ "listData":[';
$current_row = 1;
foreach ($imdbListData as $key => $imdbData) {
	echo '{';
	echo '"listId":"'.$imdbData['list_id'].'",';
	echo '"imdbId":"'.$imdbData['imdb_id'].'",';
	echo '"imdbType":"'.$imdbData['imdb_type'].'",';
	echo '"poster":"'.$imdbData['poster'].'",';
	echo '"title":"'.$imdbData['title'].'",';
	echo '"personType":"'.$imdbData['person_type'].'",';
	echo '"movieType":"'.$imdbData['movie_type'].'",';
	echo '"name":"'.$imdbData['name'].'",';
	echo '"placeOfBirth":"'.$imdbData['place_of_birth'].'",';
	echo '"birthday":"'.$imdbData['birthday'].'",';
	echo '"titleTr":"'.$imdbData['title_tr'].'",';
	echo '"director":"'.$imdbData['director'].'",';
	echo '"userRating":"'.$imdbData['user_rating'].'",';
	echo '"profilePath":"'.$imdbData['profile_path'].'"';
	echo '}';
	if($current_row != $row_count) {
		echo ',';
		$current_row++;
	}
	

}
echo ']}';


	
?>