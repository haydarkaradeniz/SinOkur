<?php
$servername = "localhost";
$username = "root";
$password = "admin";
$database = "ccsowner";

// Bağlantı oluşturma
$conn = new mysqli($servername, $username, $password, $database);
// Bağlantıyı kontrol etme
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}
// SELECT sorgusu oluşturma
$sql = "SELECT * FROM user";
// Sorguyu çalıştırma
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	echo '{ "userList":[';
	$row_count = $result->num_rows;
	$current_row = 1;	
	while ($row = $result->fetch_assoc()) {
		echo '{';
		echo '"id":' . $row["id"] . ',';
		echo '"name":"' . $row["name"] . '",';
		echo '"surname":"' . $row["surname"] . '"';
		echo '}';
		if($current_row != $row_count) {
			echo ',';
			$current_row++;
		}
	}
echo ']}';
} else {
	echo '{"userList":[]}';
}
?>