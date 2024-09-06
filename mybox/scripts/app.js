

const app = Vue.createApp({
  data() {
    return {
      apiKey: "apikey 4rmqruXG0tfPrDPWGcN2ZM:3Bf9phydrqtDp44KzC4kdu",
      userList : {
        watched: [
          {
            "imdbID": "tt1375666",
            "Poster": "https://m.media-amazon.com/images/M/MV5BMjAxMzY3NjcxNF5BMl5BanBnXkFtZTcwNTI5OTM0Mw@@._V1_SX300.jpg",
            "Title": "Inception",
            "Year": "2010",
            "Runtime": "148 min",
            "imdbRating": "8.8",
            "imdbVotes": "2,581,982",
            "Director": "Christopher Nolan",
            "Writer": "Christopher Nolan",
            "Actors": "Leonardo DiCaprio, Joseph Gordon-Levitt, Elliot Page",
            "userScore": undefined,
            "forumVoteCount":5,
            "forumScore":7.9,
          }
  
        ],
        notWatched: []
      },     
      userListFilter: {
        watched: "",
        notWatched: ""
      },
      userListInfo : {
        watched: {
          header:"İzlediklerim",
        },
        notWatched: {
          header:"İzleyeceklerim",
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
      allowOtherUser:false,
      loadingBox: {
        visible: false,
        title: "",
      },
      mainRowWidth:0,
      friendList : [
        {
          user_id:"2",
          username:"svsknr",
          user_lastvisit:"1725628618",

        },
        {
          user_id:"3",
          username:"gezgins",         

        },

      ],
      profile : {
        loginId: "60",   
        userId: "2",
        guestMode: false,
      }
        
      
    };
  },
  mounted() {    
   //not yet implemented
    //TODO
   this.allowOtherUser = true;
   this.mainRowWidth = document.getElementById('mainRow').getBoundingClientRect().width;
   console.log(Object.keys(this.userListInfo));
  },
  created() {
    window.addEventListener("resize",this.resizeEventHandler);
  },
  destroyed() {
    window.removeEventListener("resize",this.resizeEventHandler);
  },

  methods: {
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
      //TODO not yet implemented
      console.log(listType + " listesinden " + imdbID + " filmi çıkarıldı");
    },

    addMovie(imdbID) {
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
              var data = {
                "imdbID": result.imdbID,
                "Poster": result.Poster,
                "Title": result.Title,
                "Year": result.Year,
                "Runtime": result.Runtime,
                "imdbRating": result.imdbRating,
                "imdbVotes" : result.imdbVotes,
                "Director" : result.Director,
                "Writer": result.Writer,
                "Actors": result.Actors,
              }

              this.userList[this.searchBox.listType].push(data);

              //TODO
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
      //TODO
      this.hideRateBox();
      this.rateBox.movie.userScore = this.rateBox.score;
    },

    changePermission() {
      this.allowOtherUser = !this.allowOtherUser;
      console.log("permission set to -> " + this.allowOtherUser);
       //TODO
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
      //TODO
      this.profile.userId = viewId;
      this.profile.guestMode = true;
    },

    homePage() {
      //TODO
      this.profile.userId = this.profile.loginId;
      this.profile.guestMode = false;
    }
	
  },
  computed: {
   
  },

});

app.mount("#app");
