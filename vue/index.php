<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Training VueJs Test</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.js"></script>
    <script src="scripts/vue.global.js"></script>
    <link type="text/css" rel="stylesheet" href="css/style.css"/> 
    <link rel="shortcut icon" href="#">
  </head>
  <body>
    
    <div id="app">



	<div style="margin-left: 25%;" v-if="formVisible">
		<table style="width: 50%">
			<tr>
				<td>ID:</td>
				<td><input type="text" v-model="userId"></input></td>
			</tr>
			<tr>
				<td>NAME:</td>
				<td><input type="text" v-model="name"></input></td>
			</tr>
			<tr>
				<td>SURNAME:</td>
				<td><input type="text" v-model="surname"></input></td>
			</tr>
			<tr style="text-align:right">				
				<td colspan="2"><button @click="saveUser">EKLE/GÜNCELLE</button></td>
			</tr>
		
		</table>
	
	</div>
	<div v-if="!formVisible">
		<button @click="formVisible=!formVisible">Kullanıcı Ekle</button>
	</div>
	

		<div>
		<table class="hoverTable" style="margin-top: 20px;border: solid 1px;  width: 100%; text-align: left;">
			<thead>
				<tr style="background : darkorange">
					<th>
						ID
					</th>
					<th>
						NAME
					</th>
					<th colspan="2">
						SURNAME
					</th>
				</tr>			
			</thead>
			
			<tbody>
			
			
				<tr v-for="(user, index) in userList">
					<td @click="getUser(user.id)">					
					{{user.id}}
					</td>
					<td @click="getUser(user.id)">					
					{{user.name}}
					</td>
					<td @click="getUser(user.id)">					
					{{user.surname}}
					</td>
					<td style="text-align:right">
						<button @click="deleteUser(user.id)">SİL</button>
					</td>
				</tr>
			
			</tbody>
		
		</table>

		</div>
		

    </div>

    <script src="scripts/app.js"></script>

  </body>
</html>