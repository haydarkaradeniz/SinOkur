

const app = Vue.createApp({
  data() {
    return {
      apiKey: "apikey 4rmqruXG0tfPrDPWGcN2ZM:3Bf9phydrqtDp44KzC4kdu",
      userList : {
        watched: [],
        notWatched: [],
        favoriteMovies : [],
        favoriteSeries: [],
      },     
      userListFilter: {
        watched: "",
        notWatched: "",
        favoriteMovies: "",
        favoriteSeries: "",
      },
      userListInfo : {
        watched: {
          header:"İzlediklerim",
        },
        notWatched: {
          header:"İzleyeceklerim",
        },
        favoriteMovies: {
          header:"Favori Filmlerim",
        },
        favoriteSeries: {
          header:"Favori Dizilerim",
        }
      },
      searchBox: {
        visible: false,
        movieName: "",
        movieList: [],
        showList: false,
        listType:"",
        searchType:"title",
        movieIdData: {},
      },
      
      alertBox: {
        visible: false,
        message: ""
      },
      rateBox: {
        visible: false,
        score: 0,
        hoverScore: 0,
        movie:undefined,
      },
      loadingBox: {
        visible: false,
        title: "",
      },
      mainRowWidth:0,
      profile : {
        avatar:"",
        email:"",
        letterboxd:"",
        username:"",
        website:"",
        loginId: "",   
        userId: "",
        guestMode: false,
        allowOtherUser:false,
      },      
      friendList : [],
        
  
    };
  },
  mounted() {    
   //not yet implemented
   this.getSessionUser();
   this.mainRowWidth = document.getElementById('mainRow').getBoundingClientRect().width;
   console.log(Object.keys(this.userListInfo));
   this.refresh();


  },
  created() {
    window.addEventListener("resize",this.resizeEventHandler);
  },
  destroyed() {
    window.removeEventListener("resize",this.resizeEventHandler);
  },

  methods: {

    refresh() {
      this.fillProfileData();
      this.fillMovieListData();
    },

    getSessionUser() {
      var fd = new FormData();  
      axios.post('services/getSessionUser.php', fd).then(
        resp => {	
          this.profile.userId = resp.data.userId;
          this.profile.loginId = resp.data.userId;
          }
        ).catch((err) => {
          console.error(err);
          this.profile.userId = 61;
          this.profile.loginId = 61;
        }
      );
    },
      

    fillMovieListData() {
      var fd = new FormData();  
      fd.append('userId', this.profile.userId);
      axios.post('services/getMovieListData.php', fd).then(
        resp => {	
          console.log("haydar" + resp);
          for(var i=0; i<Object.keys(this.userListInfo).length; i++) {
            this.userList[Object.keys(this.userListInfo)[i]] = [];
          }
          for(var i=0; i<resp.data.userMovieList.length; i++) {
            var movieData = resp.data.userMovieList[i];

            var data = {
              "imdbID": movieData.movieId,
              "Poster": movieData.poster,
              "Title": movieData.title,
              "Year": movieData.year,
              "Runtime": movieData.runtime,
              "imdbRating": movieData.imdbRating,
              "imdbVotes" : movieData.imdbVotes,
              "Director" : movieData.director,
              "Writer": movieData.writer,
              "Actors": movieData.actors,
              "userScore": movieData.userRating,
              "forumVoteCount" : movieData.forumVoteCount,
              "forumScore" : movieData.forumScore,
            }
            this.userList[movieData.listId].push(data);
          }
        }
      );	


    },

    fillProfileData() {
      var fd = new FormData();  
      fd.append('userId', this.profile.userId);
      axios.post('services/getUserData.php', fd).then(
        resp => {	
          this.profile.avatar = resp.data.userData.avatar;
          this.profile.email = resp.data.userData.email;
          this.profile.letterboxd = resp.data.userData.letterboxd;
          this.profile.username = resp.data.userData.username;
          this.profile.website = resp.data.userData.website;
          this.profile.allowOtherUser = resp.data.userData.publicprofile && resp.data.userData.publicprofile == 0 ? false:true;
          var friends = resp.data.userData.friends;
          this.friendList = [];
          for(var i=0; i<friends.length; i++) {
            this.friendList.push( {
              "user_id":friends[i].user_id,
              "username":friends[i].username,
              "user_lastvisit":friends[i].lastvisit
            });
          }
        }
      ).catch((err) => {
        console.error(err);
        this.showAlert("Bir hata oluştu, listeleriniz yüklenemedi, lütfen tekrar deneyiniz");
      });	
    },





    resizeEventHandler(e) {
      this.mainRowWidth = document.getElementById('mainRow').getBoundingClientRect().width;
    },
    showModal(item) {
      item.visible =  true;
    },
    hideModal(item) {
      item.visible =  false;
    },
    showAlert(message) {
      this.alertBox.message = message;
      this.showModal(this.alertBox);
    },
    closeSearchBox() {
      this.hideModal(this.searchBox);
    },
    showSearchBox(listType) {
      this.searchBox.listType = listType;
      this.searchBox.movieName = "";
      this.searchBox.showList = false;
      this.searchBox.movieIdData = {};
      for(var i=0; i<this.userList[listType].length; i++) {
        this.searchBox.movieIdData[this.userList[listType][i].imdbID] = 1;
      }
      this.showModal(this.searchBox);
    },

    searchMovie() {
      if(!this.searchBox.movieName || this.searchBox.movieName.length == 0) {
         this.showAlert("Lütfen film adını girerek arama yapın");       
      } else {
        this.showModal(this.loadingBox);
        axios.get('https://api.collectapi.com/imdb/imdbSearchByName', {
          params: {
            "query" : this.searchBox.movieName,
          },
          headers: {
            "content-type":"application/json",
            "authorization":this.apiKey
          }
        }).then(response => {	
          var resp = response.data;
          if(resp.success) {
            this.searchBox.movieList = resp.result;
            this.searchBox.showList = true;
          } else {
            this.showAlert("Sonuç bulunamadı");
          }
        }).catch((err) => {
          console.error(err);
          this.showAlert("Bir hata oluştu, tekrar deneyin");
        }).finally(()=> this.hideModal(this.loadingBox));
      }
    },


    removeMovie(listType, imdbID) {
      var fd = new FormData();  
      fd.append('userId', this.profile.userId);
      fd.append('listId', listType);
      fd.append('movieId', imdbID);
      axios.post('services/deleteMovieListData.php', fd).then(
        resp => {	
          this.showAlert(resp.data);
          this.hideModal(this.searchBox);
          this.fillMovieListData();
        }
      ).catch((err) => {
        console.error(err);
        this.showAlert("Bir hata oluştu, tekrar deneyin");
      });	
    },

    addMovie(listType, imdbID) {
      imdbID = imdbID.toString().trim();
      if(this.searchBox.movieIdData[imdbID]) {
        this.showAlert("Bu Film zaten listenizde bulunuyor");
      } else {
        this.showModal(this.loadingBox);
          axios.get('https://api.collectapi.com/imdb/imdbSearchById', {
            params: {
              "movieId" : imdbID,
            },
            headers: {
              "content-type":"application/json",
              "authorization":this.apiKey
            }
          }).then(response => {	
            var resp = response.data;
            if(resp.success) { 
              
              var result = resp.result;
              var fd = new FormData();  
              fd.append('userId', this.profile.userId);    
              fd.append('movieId', imdbID);     
              fd.append('listId', listType);     
              fd.append('poster', result.Poster);     
              fd.append('title', result.Title);     
              fd.append('year', result.Year);     
              fd.append('runtime', result.Runtime);     
              fd.append('imdbRating', result.imdbRating);     
              fd.append('imdbVotes', result.imdbVotes);     
              fd.append('director', result.Director);     
              fd.append('writer', result.Writer);     
              fd.append('actors', result.Actors);  
              axios.post('services/insertMovieListData.php', fd).then(
                resp => {
                  this.showAlert(resp.data);
                  this.fillMovieListData();
                }
              ).catch((err) => {
                console.error(err);
                this.showAlert("Bir hata oluştu, tekrar deneyin");
              });
	
              this.hideModal(this.searchBox);
            } else {
              this.showAlert("Sonuç bulunamadı");
            }
          }).catch((err) => {
            console.error(err);
            this.showAlert("Bir hata oluştu, tekrar deneyin");
          }).finally(()=> this.hideModal(this.loadingBox));
        }
    },


    showRateBox(movie) {
      //TODO 
      this.rateBox.movie = movie;
      this.rateBox.score = movie.userScore;
      this.rateBox.hoverScore = movie.userScore;
      this.showModal(this.rateBox);
    },

    hideRateBox() {
      this.hideModal(this.rateBox);
    },

    rateMovie() {
      var fd = new FormData();  
      fd.append('userId', this.profile.userId);
      fd.append('movieId', this.rateBox.movie.imdbID);
      fd.append('userRating', this.rateBox.score);
      axios.post('services/updateUserRating.php', fd).then(
        resp => {	
          this.fillMovieListData();
          this.hideRateBox(); 
        }
      ).catch((err) => {
        console.error(err);
        this.showAlert("Bir hata oluştu, listeleriniz yüklenemedi, lütfen tekrar deneyiniz");
      });
    },

    changePermission() {
      this.profile.allowOtherUser = !this.profile.allowOtherUser;
      console.log("permission set to -> " + this.profile.allowOtherUser);
      var fd = new FormData();  
      fd.append('userId', this.profile.userId);
      fd.append('publicProfile', this.profile.allowOtherUser?1:0);
      axios.post('services/updateUserData.php', fd).then(
        resp => {	
          console.log(resp.data);
        }
      ).catch((err) => {
        console.error(err);
        this.showAlert("Bir hata oluştu, tekrar deneyin");
      });	
    },

    checkNaN(text) {
      return !text || text.toString().indexOf("N/A") > -1 || text === 'NaN.NaN.NaN';
    },

    retrieve(text, type) {
      if(type == 'poster') {
        return text!='N/A'?text:'images/default.gif';
      } else if(type == 'runtime' && !this.checkNaN(text)) {
        return text.replace('min','dk');
      } else  if(type == 'timestamp') {
        return this.checkNaN(text) || text === 0 ?"--.--.--":(new Date(text*1000)).toLocaleDateString("tr-TR");   
      } else if(type == 'avatar') {
        return this.checkNaN(text) ? 'images/default_avatar.svg':'./download/file.php?avatar=' + text;
      } else {
        return this.checkNaN(text) ? "-" : text; 
      }

    },

    getFilteredList(listType) {
      return this.userList[listType].filter(item => {
        return item.Title.toString().toLowerCase().indexOf(this.userListFilter[listType].toString().toLowerCase()) > -1
      });
    },

    viewProfile(viewId) {
      this.profile.userId = viewId;
      this.refresh();
      this.profile.guestMode = true;
    },

    homePage() {
      //TODO
      this.profile.userId = this.profile.loginId;
      this.refresh();
      this.profile.guestMode = false;
    }
	
  },
  computed: {
   
  },

});

app.mount("#app");
