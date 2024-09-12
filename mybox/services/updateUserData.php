<?php

include ("connection.php");


// Bağlantı oluşturma
$conn = new mysqli($servername, $username, $password, $database);

//post parameters
$userId = $_POST['userId'];
$public_profile = $_POST['publicProfile'];

//return value
$success = FALSE;

// Bağlantıyı kontrol etme
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}


//get
$sql = "select * from mybox_user_data where user_id = ?";
$stmt = $conn->prepare($sql);
//i - integer, d - double, s - string, b - BLOB
$stmt->bind_param("i", $userId);
$success = $stmt->execute();
$result = $stmt->get_result();
$row_count = $result->num_rows;

if($success === TRUE) {
	if($row_count > 0) {
		$sql = "update mybox_user_data set public_profile = ? where user_id = ?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("ii", $public_profile, $userId);
		$success = $stmt->execute() === TRUE;
	} else {
		$sql = "insert into mybox_user_data(user_id, public_profile) values ( ? , ? )";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("ii", $userId, $public_profile);
		$success = $stmt->execute() === TRUE;
	}
}

$result->close();
$stmt->close();
$conn->close();

if($success === TRUE) {
	echo 'Sayfa görüntüleme seçeneğiniz başarıyla güncellenmiştir.';
} else {
	echo 'Bir hata oluştu, lütfen tekrar deneyiniz.';
}
?>