<?php

include ("connection.php");

// Bağlantı oluşturma
$stmt = NULL;
$conn = new mysqli($servername, $username, $password, $database);

//post parameters
$userId = $_POST['userId'];

// Bağlantıyı kontrol etme
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

function getQueryResult($sql, $userId) {
	global $conn, $stmt;
	$stmt = $conn->prepare($sql);
	//i - integer, d - double, s - string, b - BLOB
	$stmt->bind_param("i", $userId);
	// set parameters and execute
	$stmt->execute();
	return $stmt->get_result();	
}

$result = getQueryResult("select * from phpbb_users t1 left join mybox_user_data t2 on t1.user_id = t2.user_id left join phpbb_profile_fields_data t3 on t1.user_id = t3.user_id where t1.user_id = ?", $userId);
$users = $result->fetch_all(MYSQLI_ASSOC);

$result = getQueryResult("select t2.user_id, t2.username, t2.user_lastvisit from phpbb_zebra t1 left join phpbb_users t2 on t1.zebra_id = t2.user_id where t1.friend = 1 and t1.user_id = ?", $userId);
$friends = $result->fetch_all(MYSQLI_ASSOC);
$row_count = $result->num_rows;

$result->close();
$stmt->close();
$conn->close();



echo '{ "userData":{';
foreach ($users as $key => $user) {
	echo '"username":"'.$user['username'].'",';
	echo '"email":"'.$user['user_email'].'",';
	echo '"avatar":"'.$user['user_avatar'].'",';
	echo '"website":"'.$user['pf_phpbb_website'].'",';
	echo '"letterboxd":"'.$user['pf_phpbb_letterboxd'].'",';
	echo '"publicprofile":"'.$user['public_profile'].'",';
	break;
}

	
echo '"friends":[';
$current_row = 1;
foreach ($friends as $key => $friend) {
	echo '{';
	echo '"user_id":"'.$friend['user_id'].'",';
	echo '"username":"'.$friend['username'].'",';
	echo '"lastvisit":"'.$friend['user_lastvisit'].'"';
	echo '}';
	if($current_row != $row_count) {
		echo ',';
		$current_row++;
	}
}
echo ']';
echo '}}';
?>