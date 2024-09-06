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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Uygulaması</title>
	<link type="text/css" rel="stylesheet" href="style.css"/> 
	<script src="imdb.js"></script>
	<script>
		var userList = [];
		$( document ).ready(function() {
			var fd = new FormData();    
			fd.append('userId', '96092');
			$.ajax({
			  type: "POST",
			  url: 'list_user.php',
			  data : fd,
			  processData: false,
			  contentType: false,
			  success: function(data) { 
				//alert(data);
				//alert(JSON.parse("{'result':true, 'count':[{'id':42}]}"));
				userData = JSON.parse(data).userList;
				}
			});
		});
		

		
		var saveUser = function() {
			var fd = new FormData();    
			fd.append('id', $("#id").val());
			fd.append('name', $("#name").val());
			fd.append('surname', $("#surname").val());
			
			$.ajax({
			  type: "POST",
			  url: 'upsert_user.php',
			  data : fd,
			  processData: false,
			  contentType: false,
			  success: function(data) { alert(data);}
			});
			
		}
	</script>
</head>
<body>
	<div>
        <h2>Kullanıcı Ekle / Güncelle</h2>
		
		<div style="margin-left: 25%;">
			<table style="width: 50%">
				<tr>
					<td>ID:</td>
					<td><input type="text" id="id" value="5"></input></td>
				</tr>
				<tr>
					<td>NAME:</td>
					<td><input type="text" id="name" value="Ahmet"></input></td>
				</tr>
				<tr>
					<td>SURNAME:</td>
					<td><input type="text" id="surname" value="Hakan"></input></td>
				</tr>
				<tr style="text-align:right">				
					<td colspan="2"><button onclick="saveUser()">EKLE/GÜNCELLE</button></td>
				</tr>
			
			</table>
	
		</div>
		
        
    </div>
    <div class="container">
        <h2>Kullanıcılar</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ad</th>
                    <th>Soyad</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["name"] . "</td>";
                        echo "<td>" . $row["surname"] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo '<tr><td colspan="3">Veri bulunamadı.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
 
</body>
</html>
<?php
$conn->close();

/*

			  //data: {
				//  id : $("#id").val(),
				 // name : $("#name").val(),
				  //surname : $("#surname").val()				  
			  //},

 <form action="upsert_user.phpx" method="POST">
            <div class="form-group">
                <label for="id">Id:</label>
                <input type="text" class="form-control" id="id" name="id" required>
            </div>
            <div class="form-group">
                <label for="name">Ad:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
			 <div class="form-group">
                <label for="surname">Soyad:</label>
                <input type="text" class="form-control" id="surname" name="surname" required>
            </div>
            <button type="xubmitx" onclick="ekle()" class="btn btn-primary">Ekle</button>
        </form>
*/
?>


