

const app2 = Vue.createApp({
  data() {
    return {
      count : "3",
      userList : [],
      name:"",
      surname:"",
      userId:"",
      formVisible:false,
      //baseURL:'http://localhost:8080/movie-database/'
      baseURL: 'https://hydrtech.jvmhost.net/movie-database/'
    };
  },
  mounted() {    
   this.count = 7;
   this.getUserList() ;
  },
  methods: {
	increase() {
		this.count ++;
	},
		
	getUserList() {		
		axios.get('list_user.php').then(resp => {	
		console.log(resp.data.userList);
	    this.userList = resp.data.userList;
	  });
    },
    
	clearForm() {
		this.userId = "";
		this.name = "";
		this.surname = "";
	},

   userSaved() {
	   this.getUserList();
	   this.clearForm();
	   this.formVisible = false;
	   alert("Kullanıcı Eklendi/Güncellendi.");
   },
    
    saveUser() {
		if( this.userId.toString().trim().length>0 && 
		 	this.name.trim().length>0 &&
			this.surname.trim().length>0) {		

			var fd = new FormData();  
			fd.append('id', this.userId.toString().trim());
			fd.append('name', this.name.trim());
			fd.append('surname', this.surname.trim());
			
			axios.post('upsert_user.php', fd).then(this.userSaved);				
		} else {
			alert("Alanlar Boş Geçilemez !");
		}		
	},
	
	userRemoved(id) {
		this.getUserList();		
		if(id == this.userId) {
			this.clearForm();
			this.formVisible = false;
		}
		alert("Kullanıcı Silindi");
	 },
	 
	removeUser(id) { 
		var fd = new FormData();  
		fd.append('id', id);
		axios.post('delete_user.php', fd).then(this.userRemoved(id));				
	},
	
	deleteUser(id) {	
		this.removeUser(id);
		//Promise.all([this.removeUser(id)]).then(this.userRemoved(id));		
	},
	
	getUser(id) {    
	  this.formVisible = true;
	  axios.get(this.baseURL + 'user/' + id).then(resp => {	
	    if(resp.data) {
			this.userId = resp.data.id;
		 	this.name = resp.data.name;
		 	this.surname = resp.data.surname; 		
		}
	  });
    },
	
  },

});

app2.mount("#app");
