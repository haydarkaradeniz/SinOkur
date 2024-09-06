
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Uygulaması</title>
</head>
<body>
<div style="background-color:lightcyan;  border:1px solid red; display: block; width: 98vw; height: 98vh;">
<?php





define('IN_PHPBB', true);
//$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '/home/sineokur/public_html/test/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/functions_user.'.$phpEx);
// Start session management
$user->session_begin();
$auth->acl($user->data);
//$user->setup('mods/template');
echo "<br>";
echo "Kullanıcı Id: ".$user->data['user_id'];

echo "<br>";
echo "Kullanıcı Adı: ".$user->data['username'];
echo "<br>";
echo "E-posta: ".$user->data['user_email'];
echo "<br>";
echo "Ip adresi: ".$user->data['user_ip'];
echo "<br>";

//echo '<pre>' . print_r($user->data, TRUE) . '</pre>';
?>
<?php
$servername = "localhost";
$username = "sineokur_dragon";
$password = "Dragon1983*";
$database = "sineokur_phpbbtest";

// Bağlantı oluşturma
$conn = new mysqli($servername, $username, $password, $database);
// Bağlantıyı kontrol etme
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}
// SELECT sorgusu oluşturma
$sql = "SELECT * FROM phpbbtest_users";
// Sorguyu çalıştırma
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	echo '{ "userList":['.'<br>';
	$row_count = $result->num_rows;
	$current_row = 1;	
	while ($row = $result->fetch_assoc()) {
		echo '{'.'<br>';
		echo '"id":' . $row["user_id"] . ','.'<br>';
		echo '"username":"' . $row["username"] . '",'.'<br>';
		echo '"username clean":"' . $row["username_clean"] . '",'.'<br>';
		echo '"email":"' . $row["user_email"] . '"'.'<br>';
		echo '}'.'<br>';
		if($current_row != $row_count) {
			echo ',';
			$current_row++;
		}
	}
echo ']}'.'<br>';
} else {
	echo '{"userList":[]}'.'<br>';
}

$conn->close();
?>

</div>

</body>
</html>