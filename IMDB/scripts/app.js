
const app = Vue.createApp({
  data() {
    return {
      subPath:"/test",//"/"
      tmdb_api_key: "691a740114fe7dc34fbbe9f1a464cc5e",   
      omdb_api_key: "68d73d3c",   
      imdbID: "",
      tmdbID: "",
      viewType: "",
      movieData : {},
      personData: {},
      apiData: {},
      profile : {
        loginId: undefined,
        userId: undefined
      },  
      basicListData : {},
      favoriteListCardVisible : false,
      userListInfo : {
        planned: {
          header:"İzleyeceklerim",
        }
      },
      detailMode : false,
      rateBox: {
        score: 0,
        hoverScore: 0,
      },
      videoBox : {
        iframe:undefined,
        visible:false,
        src:"#",
        width:580,
        height:300,
        top:100,
        left:100
      },
    };
  },
  mounted() {   
    //TODO NOT YET IMPLEMENTED
  
  },
  created() {
    let uri = window.location.search.substring(1); 
    let params = new URLSearchParams(uri);
    this.imdbID = params.get("id");
    this.detailMode = params.get("detail")  ? true : false;
    if(this.imdbID[0] == 't' || this.imdbID[0] == 'n') {
      this.viewType = this.imdbID[0] == 't' ? 'movie':'person';
    } else {
      this.viewType = params.get("viewType");
      this.imdbID = undefined;
      this.tmdbID = params.get("id");
    }
    this.getSessionUser();
    if(this.viewType == 'movie') {
      if(this.imdbID) {
        this.getOMDBMovieDetail();
      } else {
        this.findIMDBMovieId();
      }
    } else if(this.viewType == 'person') {
      if(this.imdbID) {
        this.findTMDBPersonId();
      } else {
        this.findTMDBPerson();
      }
    } 
    if(this.detailMode) {
      window.top.addEventListener("click", this.clickEventHandler);
    }
  },
  destroyed() {
    if(this.detailMode) {
      window.top.removeEventListener("click", this.clickEventHandler);
    }
  },

  methods: {
    clickEventHandler(e) {
      if(this.videoBox.visible) {
        if(e.target != this.videoBox.iframe) {
         this.showVideo();
        }
      }
  },

    initVideoBox() {
      var iframe = window.document.getElementById('sineokur-detail-video-iframe');
      if(iframe) {
          this.videoBox.iframe = iframe;
          this.videoBox.iframe.src = "https://www.youtube.com/embed/" + this.movieData.videoKey + "?controls=1&enablejsapi=1";
      }
      
    },

    resizeVideo() {
      var screenHeight = window.top.innerHeight;
      var screenWidth = window.top.innerWidth;
      this.videoBox.width = screenWidth-200;
      if(this.videoBox.width > 720) {
        this.videoBox.width = 720;
      }
      this.videoBox.left = parseInt((screenWidth-this.videoBox.width)/2);
      this.videoBox.height = parseInt(this.videoBox.width/2);
      this.videoBox.top =  window.top.scrollY + parseInt((screenHeight-this.videoBox.height)/2);
    },

    showVideo() {
      if(!this.videoBox.iframe) {
        return; 
      }
      this.resizeVideo();
      if(!this.videoBox.visible) {
        this.videoBox.iframe.style.display = "block";
        this.videoBox.iframe.style.width = this.videoBox.width + "px";
        this.videoBox.iframe.style.height = this.videoBox.height + "px";
        this.videoBox.iframe.style.top = this.videoBox.top + "px";
        this.videoBox.iframe.style.left = this.videoBox.left + "px";
      } else {
        this.videoBox.iframe.contentWindow.postMessage( '{"event":"command", "func":"pauseVideo", "args":""}', '*');
        this.videoBox.iframe.style.display = "none";
        this.videoBox.iframe.style.width = "0px";
        this.videoBox.iframe.style.height = "0px";
        this.videoBox.iframe.style.top = "0px";
        this.videoBox.iframe.style.left = "0px";
      }
      this.videoBox.visible = !this.videoBox.visible;
    },

    getBaseUrl() {
      return this.detailMode  ? "../imdb/":"";
    },

    rateBoxStarUrl(startIndex) {
      if(this.rateBox.hoverScore >= (startIndex*2)) {
        return  this.getBaseUrl() + "images/puan_dolu.svg";
      } else if(this.rateBox.hoverScore <= ((startIndex-1)*2)) {
        return this.getBaseUrl() + "images/puan_bos.svg";
      } else {
        return this.getBaseUrl() + "images/puan_yarim.svg";
      }
    },

    rateBoxMouseMove(event, startIndex) {
      this.rateBox.hoverScore = ((startIndex-1)*2) + (event.offsetX<13?1:2);
    },

    rateBoxClick() {
      var rollbackScore = this.rateBox.score;
      this.rateBox.score = this.rateBox.hoverScore;
      var fd = new FormData();  
      fd.append('userId', this.profile.userId);
      fd.append('imdbId', this.imdbID);
      fd.append('userRating', this.rateBox.score);
      axios.post(this.getBaseUrl() + 'services/updateUserRating.php', fd).then(
        resp => {	 
          this.fillRateBoxData();
        }
      ).catch((err) => {
        console.error(err);
        this.rateBox.score = rollbackScore;
      }).finally(()=> {
        //
      });
    },

    getSessionUser() {
      var fd = new FormData();  
      axios.get(this.getBaseUrl() + 'services/getSessionUser.php', fd).then(
        resp => {	
          if(resp.data.userId && parseInt(resp.data.userId) > 1) {
            this.profile.userId = resp.data.userId;
            this.profile.loginId = resp.data.userId;
            this.refresh();
          }
        }).catch((err) => {
          console.error(err);
          //this.profile.userId = 2;
          //this.profile.loginId = 2;
          this.refresh();
        }
      )
    },

    refresh() {
      if(this.viewType == 'movie' || this.viewType == 'person') {
        this.fillBasicListData();
      }
      if(this.detailMode) {
        this.fillRateBoxData();
      }
    },

    fillBasicListData() {
      var fd = new FormData();  
      fd.append('userId', this.profile.userId);
      fd.append('imdbId', this.imdbID);
      axios.post(this.getBaseUrl() + 'services/getListDataById.php', fd).then(
        resp => {	
          this.basicListData = {};
          for(var i=0; i<resp.data.listData.length; i++) {
            this.basicListData[resp.data.listData[i].listId] = true;
          }
          this.fillMovieListIcon();
        }
      ).catch((err) => {
        console.error(err);
      });	
    },

    fillRateBoxData() {
      var fd = new FormData();  
      fd.append('userId', this.profile.userId);
      fd.append('imdbId', this.imdbID);
      axios.post(this.getBaseUrl() + 'services/getUserRating.php', fd).then(
        resp => {	
          this.rateBox.score = resp.data.userRatingData.userRating ? resp.data.userRatingData.userRating : 0;
          this.rateBox.hoverScore = this.rateBox.score;
          this.rateBox.forumScore = resp.data.userRatingData.score ? resp.data.userRatingData.score : 0;
          this.rateBox.forumVote = resp.data.userRatingData.vote ? resp.data.userRatingData.vote : 0;
        }
      ).catch((err) => {
        console.error(err);
      });	
    },

    fillMovieListIcon() {
      this.basicListData['movieListIcon'] = false;
      for(var i=0; i<Object.keys(this.userListInfo).length; i++) {
        if(this.basicListData[Object.keys(this.userListInfo)[i]]) {
          this.basicListData['movieListIcon'] = true;
          break;
        }
      }
    },

    getFavoriteListId() {
      return this.viewType == 'movie' ? 'liked-movie':'liked-person';
    },

    favorite(listId, listId2, cycle=1) {
      this.basicListData[listId] = cycle > 1 ? false : !this.basicListData[listId];
      var fd = new FormData();  
      fd.append('userId', this.profile.userId);
      fd.append('imdbId', this.imdbID);
      fd.append('listId', listId);
      fd.append('imdbType', this.viewType);
      if(this.viewType == 'movie') {
        fd.append('poster', this.movieData.poster);     
        fd.append('title', this.movieData.title);     
        fd.append('year', this.movieData.year);     
        fd.append('runtime', this.movieData.runtime);     
        fd.append('imdbRating', this.movieData.imdbRating);     
        fd.append('imdbVotes', this.movieData.imdbVotes);   
        fd.append('movieType', this.movieData.type);   
      } else {
        fd.append('personType', this.personData.type);     
        fd.append('name', this.personData.name);     
        fd.append('placeOfBirth', this.personData.place_of_birth);     
        fd.append('birthday', this.personData.birthday);    
        fd.append('profilePath', this.personData.profile_path);    
      }
      axios.post(cycle > 1 ? this.getBaseUrl()+'services/deleteListData.php':(this.basicListData[listId]?this.getBaseUrl()+'services/insertListData.php':this.getBaseUrl()+'services/deleteListData.php'), fd).then(
        resp => {	 
          this.fillMovieListIcon();
        }
      ).catch((err) => {
        console.error(err);
        //this.basicListData[listId] = !this.basicListData[listId];
      }).finally(()=> {
        if(listId2) {
          this.favorite(listId2, undefined, 2);
        }
      });
     
    },

    showListMenu() {
      this.favoriteListCardVisible = !this.favoriteListCardVisible;
    },
    
    
    checkNaN(text) {
      return !text || text.toString().indexOf("N/A") > -1 || text === 'NaN.NaN.NaN';
    },

    retrieve(text, type) {
      if(type == 'poster') {
        return !this.checkNaN(text)?text:this.getDefaultImage(type);
      } else if(type == 'runtime' && !this.checkNaN(text)) {
        return text.replace('min','dk');
      } else if(type == 'country' && !this.checkNaN(text)) {
        return this.convertCountry(text);
      } else if(type == 'genre' && !this.checkNaN(text)) {
        return this.convertGenre(text);
      } else if(type == 'title' && !this.checkNaN(text)) {
        return text.toLocaleUpperCase('tr');
      } else if(type == 'overview' && !this.checkNaN(text)) {
        var maxLength = 250;
        this.movieData.overviewLink = text.length>maxLength;
        return this.movieData.overviewLink ? text.substring(0, maxLength) : text;
      } else if(type == 'person-name' && !this.checkNaN(text)) {
        return text.toLocaleUpperCase('tr');
      } else if(type == 'person-type' && !this.checkNaN(text)) {
        if(text === 'Acting') {
          return 'OYUNCU';
        } else if(text === 'Directing') {
          return 'YÖNETMEN';
        } else {
          return text;
        }
      } else if(type == 'tmdb-birthday' && !this.checkNaN(text)) {
        if(text.length == 10) {
          var str = text.substring(8,10) + "." + text.substring(5,7) + "." + text.substring(0,4);
          var birthday = new Date(text);
          var ageDifMs = Date.now() - birthday.getTime();
          var ageDate = new Date(ageDifMs);
          return str + " (" + Math.abs(ageDate.getUTCFullYear()-1970) + " yaşında)";
        } else {
          return text;
        }
      } else if(type == 'tmdb-poster' || type == 'tmdb-poster-detail-min') {
        return !this.checkNaN(text)?"http://image.tmdb.org/t/p/w500" + text:this.getDefaultImage(type);
      }
      
      
      else {
        return !this.checkNaN(text) ? text : "- "; 
      }
    },

    getDefaultImage(type) {
      if(type == 'tmdb-poster-detail-min') {
        return this.getBaseUrl() + 'images/default_100x150.svg';
      } else {
        return this.getBaseUrl() + 'images/default_160x240.svg'
      }
    },
  
    retrieveBirthAndDeathDay(birthday, deathday, type) {
      if(!this.checkNaN(birthday)) {
        if(birthday.length == 10) {
          var str = birthday.substring(8,10) + "." + birthday.substring(5,7) + "." + birthday.substring(0,4);
          if(this.checkNaN(deathday)) {
            var birthdate = new Date(birthday);
            var ageDifMs = Date.now() - birthdate.getTime();
            var ageDate = new Date(ageDifMs);
            str = str + " (" + Math.abs(ageDate.getUTCFullYear()-1970) + " yaşında)";
          } else {
            if(birthday.length == 10) {
              str = str + " (" + deathday.substring(8,10) + "." + deathday.substring(5,7) + "." + deathday.substring(0,4);
              var lastDigitOfYear = deathday.substring(3,4);
              if(lastDigitOfYear==1 || lastDigitOfYear==2 || lastDigitOfYear==7 || lastDigitOfYear==8) {
                str = str + "'de";
              } else if(lastDigitOfYear==3 || lastDigitOfYear==4 || lastDigitOfYear==5) {
                str = str + "'te";
              } else {
                str = str + "'da";
              }
              var birthdate = new Date(birthday);
              var deathdate = new Date(deathday);
              var ageDifMs = deathdate.getTime() - birthdate.getTime();
              var ageDate = new Date(ageDifMs);
              str =  str + " " + Math.abs(ageDate.getUTCFullYear()-1970) + " yaşında vefat etmiştir)";
            } else {
              str = str + " (Vefat " + deathdate + ")";
            }
          }

          return str;

        } else {
          return birthday;
        }
      } else {
        return this.retrieve(birthday);
      }


    },

    fillMovieData(source) {
      if(source == 'omdb') {
        this.movieData.omdb_poster = this.apiData.Poster;
        this.movieData.title = this.retrieve(this.apiData.Title, 'title');
        this.movieData.omdb_director = this.apiData.Director;
        this.movieData.director = this.retrieve(this.apiData.Director);
        this.movieData.country = this.retrieve(this.apiData.Country, 'country');
        this.movieData.year = this.retrieve(this.apiData.Year);
        this.movieData.genre = this.retrieve(this.apiData.Genre, 'genre');
        this.movieData.imdbRating = this.retrieve(this.apiData.imdbRating);
        this.movieData.imdbVotes = this.retrieve(this.apiData.imdbVotes);
        this.movieData.runtime = this.retrieve(this.apiData.Runtime, 'runtime');
        this.movieData.type = this.apiData.Type;
      } else if(source == 'tmdb') {
        this.movieData.title_tr = this.retrieve(this.apiData.title, 'title');
        this.movieData.poster = this.retrieve(this.apiData.poster_path, 'tmdb-poster');
        this.movieData.overview = this.retrieve(this.apiData.overview, 'overview');
        this.movieData.overviewFull = this.retrieve(this.apiData.overview);
        this.tmdbID = this.apiData.id;
     } else if(source == 'tmdb-error') {
        this.movieData.title_tr = this.movieData.title;
        this.movieData.poster = this.retrieve(this.movieData.omdb_poster, 'poster');
        this.movieData.director = this.checkNaN(this.movieData.omdb_director) && this.movieData.type == 'series' ? 'Farklı yönetmenler' : this.movieData.director; 
      } else if(source == 'tmdb-movie-cast') {
        this.movieData.personList = [];
        for(var i=0; i<this.apiData.crew.length; i++) {
          var person = this.apiData.crew[i];
          if(person.job && person.job == "Director") {
            this.movieData.personList.push({
              id:person.id,
              name:person.name,
              role:"YÖNETMEN",
              poster:this.retrieve(person.profile_path, 'tmdb-poster-detail-min')
            });
          }
        }
        for(var i=0; i<this.apiData.cast.length; i++) {
          var person = this.apiData.cast[i];
          if(person.known_for_department == "Acting") {
            this.movieData.personList.push({
              id:person.id,
              name:person.name,
              role:person.character,
              poster:this.retrieve(person.profile_path, 'tmdb-poster-detail-min')
            });
          }
        }

      } else if(source == 'tmdb-person') {
          this.imdbID = this.apiData.imdb_id;
          this.personData.type = this.retrieve(this.apiData.known_for_department, 'person-type');
          this.personData.name = this.retrieve(this.apiData.name, 'person-name');
          this.personData.place_of_birth = this.retrieve(this.apiData.place_of_birth);
          this.personData.birthday = this.retrieveBirthAndDeathDay(this.apiData.birthday, this.apiData.deathday);
          this.personData.profile_path = this.retrieve(this.apiData.profile_path, 'tmdb-poster');
          this.personData.biography = this.retrieve(this.apiData.biography);
        } else if(source == 'tmdb-person-movies') {
          if(this.apiData.cast && this.apiData.cast.length > 0) {
            var maxLength = 400;
            var currentLength = 0;
            var movies = [];
            var index = 0;
            for(index=0; index<this.apiData.cast.length; index++) {
              var moviesData = {
                original_title : this.retrieve(this.apiData.cast[index].original_title),
                character : this.retrieve(this.apiData.cast[index].character)
              };
              movies.push(moviesData);
              // () ,
              currentLength = currentLength + moviesData.original_title.length + 
                moviesData.character.length + 
                (moviesData.character.trim() === '-'?0:moviesData.character.length) +
                (this.personData.type==='YÖNETMEN' || moviesData.character.trim() === '-'?2:5);
              if(currentLength>maxLength) {
                break;
              }
            }
            this.personData.movies = movies;
            this.personData.moviesOverflowLink = index != this.apiData.cast.length;

            //all movies
            if(this.detailMode) {
              this.personData.movieList = [];
              for(index=0; index<this.apiData.cast.length; index++) {
                var movieData = {
                  title : this.retrieve(this.apiData.cast[index].title), 
                  original_title : this.retrieve(this.apiData.cast[index].original_title), 
                  poster : this.retrieve(this.apiData.cast[index].poster_path, 'tmdb-poster-detail-min'),
                  id : this.apiData.cast[index].id,
                }
                this.personData.movieList.push(movieData);
              }
            }
          } else {
            return this.retrieve(undefined);
          }
        } else if(source == 'tmdb-movie-videos') {
          if(this.apiData.results && this.apiData.results.length>0) {
            if(this.apiData.results.length == 1 && this.apiData.results[0].site === 'YouTube') {
              this.movieData.videoKey = this.apiData.results[0].key;
            } else {
              var found = undefined;
              //check dublaj
              for(var i=0; i<this.apiData.results.length; i++) {
                if(this.apiData.results[i].site === 'YouTube' && this.apiData.results[i].name.toLocaleUpperCase('tr').indexOf('DUBLAJ')>-1) {
                  found = this.apiData.results[i].key;
                  break;
                }
              }
              if(!found) {
                for(var i=0; i<this.apiData.results.length; i++) {
                  if(this.apiData.results[i].site === 'YouTube') {
                    found = this.apiData.results[i].key;
                    break;
                  }
                }
              }
              this.movieData.videoKey = found; 
            }
            this.initVideoBox();
          }
        }
    },


    getOMDBMovieDetail() {
      axios.get('https://www.omdbapi.com', {
        params: {
          "plot": "full",
          "r": "json",
          "i" : this.imdbID,
          "apikey": this.omdb_api_key
        }
      }).then(response => {	
        this.apiData = response.data;
        this.fillMovieData('omdb');
      }).catch((err) => {
        console.error(err);
      }).finally(()=> {
        this.getTMDBMovieDetail();
      });
    },

    getTMDBMovieDetail() {
      axios.get('https://api.themoviedb.org/3/movie/' + this.imdbID, {
        params: {
          "language":"tr",
          "api_key":this.tmdb_api_key
        },
        headers: {
          "content-type":"application/json"
        }
      }).then(response => {	
        this.apiData = response.data;
        this.fillMovieData('tmdb');
      }).catch((err) => {
        this.fillMovieData('tmdb-error');
        console.error(err);
      }).finally(()=> {
        if(this.tmdbID && this.detailMode) {
          this.getTMDBMovieCasts();
        } else {
          this.resizeIFrame();
        }
        if(this.tmdbID && this.viewType == 'movie' && this.detailMode) {
          this.getTMDBMovieVideos();
        }
      });
    },

    findTMDBPersonId() {
      axios.get('https://api.themoviedb.org/3/find/' + this.imdbID, {
        params: {
          "language":"tr",
          "api_key":this.tmdb_api_key,
          "external_source": "imdb_id"
        },
        headers: {
          "content-type":"application/json"
        }
      }).then(response => {	
        if(response.data.person_results && response.data.person_results.length>0) {
          this.tmdbID = response.data.person_results[0].id;
          this.findTMDBPerson();
        }
      }).catch((err) => {
        console.error(err);
      }).finally(()=> {});
    },

    
    findIMDBMovieId() {
      axios.get('https://api.themoviedb.org/3/movie/' + this.tmdbID, {
        params: {
          "language":"tr",
          "api_key":this.tmdb_api_key
        },
        headers: {
          "content-type":"application/json"
        }
      }).then(response => {	
        this.imdbID = response.data.imdb_id;
        this.getOMDBMovieDetail();
      }).catch((err) => {
        console.error(err);
      }).finally(()=> {});
    },
    

    findTMDBPerson() {
      axios.get('https://api.themoviedb.org/3/person/' + this.tmdbID, {
        params: {
          "language":"tr",
          "api_key":this.tmdb_api_key
        },
        headers: {
          "content-type":"application/json"
        }
      }).then(response => {	
        this.apiData = response.data;
        this.fillMovieData('tmdb-person');
        this.findTMDBPersonMovies();
      }).catch((err) => {
        console.error(err);
      }).finally(()=> {});
    },

    findTMDBPersonMovies() {
      axios.get('https://api.themoviedb.org/3/person/' + this.tmdbID + '/movie_credits',  {
        params: {
          "language":"tr",
          "api_key":this.tmdb_api_key
        },
        headers: {
          "content-type":"application/json"
        }
      }).then(response => {	
        this.apiData = response.data;
        this.fillMovieData('tmdb-person-movies');
      }).catch((err) => {
        console.error(err);
      }).finally(()=> {
        this.resizeIFrame();
      });
    },

    getTMDBMovieCasts() {
      axios.get('https://api.themoviedb.org/3/movie/' + this.imdbID + '/credits',  {
        params: {
          "language":"tr",
          "api_key":this.tmdb_api_key
        },
        headers: {
          "content-type":"application/json"
        }
      }).then(response => {	
        this.apiData = response.data;
        this.fillMovieData('tmdb-movie-cast');
      }).catch((err) => {
        console.error(err);
      });
    },

    getTMDBMovieVideos() {
      axios.get('https://api.themoviedb.org/3/movie/' + this.tmdbID + '/videos',  {
        params: {
          "language":"tr",
          "api_key":this.tmdb_api_key
        },
        headers: {
          "content-type":"application/json"
        }
      }).then(response => {	
        this.apiData = response.data;
        this.fillMovieData('tmdb-movie-videos');
      }).catch((err) => {
        console.error(err);
      });
    },

    multiReplace(data, mapObj) {
      var re = new RegExp(Object.keys(mapObj).join("|"),"gi");
      return data.replace(re, function(matched){return mapObj[matched];});

    },

    resizeIFrame() {
      //var iframe = window.parent.document.getElementById('iframe-'+this.imdbID);
      //iframe.style.height =  (iframe.contentWindow.document.body.scrollHeight + 30) + 'px';
      //iframe.style.width =  (iframe.contentWindow.document.body.scrollWidth + 30) + 'px';
      var iframeList = window.parent.document.getElementsByClassName('iframe-'+this.imdbID);
      if(iframeList && iframeList.length>0) {
        for(var i=0; i<iframeList.length; i++) {
          var scrollHeight = iframeList[i].contentWindow.document.body.scrollHeight;
          var scrollWidth = iframeList[i].contentWindow.document.body.scrollWidth;
          console.log("haydar " + scrollHeight + " , " + scrollWidth );
          iframeList[i].style.height =  (scrollHeight<=240?270:scrollHeight + 30) + 'px';
          iframeList[i].style.width =  ((scrollWidth<=540?570:scrollWidth) + 30) + 'px';
        }
      }
      
    },

    convertCountry(country) {
      var mapObj = {
      "Afghanistan":"Afganistan",
      "Albania":"Arnavutluk",
      "Algeria":"Cezayir",
      "Andorra":"Andorra",
      "Angola":"Angola",
      "Antigua and Barbuda":"Antigua ve Barbuda",
      "Argentina":"Arjantin",
      "Armenia":"Ermenistan",
      "Australia":"Avustralya",
      "Austria":"Avusturya",
      "Azerbaijan":"Azerbaycan",
      "Bahamas":"Bahamalar",
      "Bahrain":"Bahreyn",
      "Bangladesh":"Bangladeş",
      "Barbados":"Barbados",
      "Belarus":"Belarus",
      "Belgium":"Belçika",
      "Belize":"Belize",
      "Benin":"Benin",
      "Bhutan":"Butan",
      "Bolivia":"Bolivya",
      "Bosnia and Herzegovina":"Bosna Hersek",
      "Botswana":"Botsvana",
      "Brazil":"Brezilya",
      "Brunei":"Brunei",
      "Bulgaria":"Bulgaristan",
      "Burkina Faso":"Burkina Faso",
      "Burundi":"Burundi",
      "Cabo Verde":"Yeşil Burun Adaları",
      "Cambodia":"Kamboçya",
      "Cameroon":"Kamerun",
      "Canada":"Kanada",
      "Central African Republic":"Orta Afrika Cumhuriyeti",
      "Chad":"Çad",
      "Chile":"Şili",
      "China":"Çin",
      "Colombia":"Kolombiya",
      "Comoros":"Komorlar",
      "Congo":"Kongo",
      "Costa Rica":"Kosta Rika",
      "Croatia":"Hırvatistan",
      "Cuba":"Küba",
      "Cyprus":"Kıbrıs",
      "Czech Republic":"Çek Cumhuriyeti",
      "Denmark":"Danimarka",
      "Djibouti":"Cibuti",
      "Dominica":"Dominika",
      "Dominican Republic":"Dominik Cumhuriyeti",
      "East Timor":"Doğu Timor",
      "Ecuador":"Ekvador",
      "Egypt":"Mısır",
      "El Salvador":"El Salvador",
      "Equatorial Guinea":"Ekvator Ginesi",
      "Eritrea":"Eritre",
      "Estonia":"Estonya",
      "Eswatini":"Esvatini",
      "Ethiopia":"Etiyopya",
      "Fiji":"Fiji",
      "Finland":"Finlandiya",
      "France":"Fransa",
      "Gabon":"Gabon",
      "Gambia":"Gambiya",
      "Georgia":"Gürcistan",
      "Germany":"Almanya",
      "Ghana":"Gana",
      "Greece":"Yunanistan",
      "Grenada":"Grenada",
      "Guatemala":"Guatemala",
      "Guinea":"Gine",
      "Guinea-Bissau":"Gine-Bissau",
      "Guyana":"Guyana",
      "Haiti":"Haiti",
      "Honduras":"Honduras",
      "Hungary":"Macaristan",
      "Iceland":"İzlanda",
      "India":"Hindistan",
      "Indonesia":"Endonezya",
      "Iran":"İran",
      "Iraq":"Irak",
      "Ireland":"İrlanda",
      "Israel":"İsrail",
      "Italy":"İtalya",
      "Ivory Coast":"Fildişi Sahili",
      "Jamaica":"Jamaika",
      "Japan":"Japonya",
      "Jordan":"Ürdün",
      "Kazakhstan":"Kazakistan",
      "Kenya":"Kenya",
      "Kiribati":"Kiribati",
      "Kosovo":"Kosova",
      "Kuwait":"Kuveyt",
      "Kyrgyzstan":"Kırgızistan",
      "Laos":"Laos",
      "Latvia":"Letonya",
      "Lebanon":"Lübnan",
      "Lesotho":"Lesotho",
      "Liberia":"Liberya",
      "Libya":"Libya",
      "Liechtenstein":"Lihtenştayn",
      "Lithuania":"Litvanya",
      "Luxembourg":"Lüksemburg",
      "Madagascar":"Madagaskar",
      "Malawi":"Malavi",
      "Malaysia":"Malezya",
      "Maldives":"Maldivler",
      "Mali":"Mali",
      "Malta":"Malta",
      "Marshall Islands":"Marshall Adaları",
      "Mauritania":"Moritanya",
      "Mauritius":"Mauritius",
      "Mexico":"Meksika",
      "Micronesia":"Mikronezya",
      "Moldova":"Moldova",
      "Monaco":"Monako",
      "Mongolia":"Moğolistan",
      "Montenegro":"Karadağ",
      "Morocco":"Fas",
      "Mozambique":"Mozambik",
      "Myanmar":"Myanmar",
      "Namibia":"Namibya",
      "Nauru":"Nauru",
      "Nepal":"Nepal",
      "Netherlands":"Hollanda",
      "New Zealand":"Yeni Zelanda",
      "Nicaragua":"Nikaragua",
      "Niger":"Nijer",
      "Nigeria":"Nijerya",
      "North Korea":"Kuzey Kore",
      "North Macedonia":"Kuzey Makedonya",
      "Norway":"Norveç",
      "Oman":"Umman",
      "Pakistan":"Pakistan",
      "Palau":"Palau",
      "Panama":"Panama",
      "Papua New Guinea":"Papua Yeni Gine",
      "Paraguay":"Paraguay",
      "Peru":"Peru",
      "Philippines":"Filipinler",
      "Poland":"Polonya",
      "Portugal":"Portekiz",
      "Qatar":"Katar",
      "Romania":"Romanya",
      "Russia":"Rusya",
      "Rwanda":"Ruanda",
      "Saint Kitts and Nevis":"Saint Kitts ve Nevis",
      "Saint Lucia":"Saint Lucia",
      "Saint Vincent and the Grenadines":"Saint Vincent ve Grenadinler",
      "Samoa":"Samoa",
      "San Marino":"San Marino",
      "Sao Tome and Principe":"São Tomé ve Príncipe",
      "Saudi Arabia":"Suudi Arabistan",
      "Senegal":"Senegal",
      "Serbia":"Sırbistan",
      "Seychelles":"Seyşeller",
      "Sierra Leone":"Sierra Leone",
      "Singapore":"Singapur",
      "Slovakia":"Slovakya",
      "Slovenia":"Slovenya",
      "Solomon Islands":"Solomon Adaları",
      "Somalia":"Somali",
      "South Africa":"Güney Afrika",
      "South Korea":"Güney Kore",
      "South Sudan":"Güney Sudan",
      "Spain":"İspanya",
      "Sri Lanka":"Sri Lanka",
      "Sudan":"Sudan",
      "Suriname":"Surinam",
      "Sweden":"İsveç",
      "Switzerland":"İsviçre",
      "Syria":"Suriye",
      "Taiwan":"Tayvan",
      "Tajikistan":"Tacikistan",
      "Tanzania":"Tanzanya",
      "Thailand":"Tayland",
      "Togo":"Togo",
      "Tonga":"Tonga",
      "Trinidad and Tobago":"Trinidad ve Tobago",
      "Tunisia":"Tunus",
      "Turkey":"Türkiye",
      "Turkmenistan":"Türkmenistan",
      "Tuvalu":"Tuvalu",
      "Uganda":"Uganda",
      "Ukraine":"Ukrayna",
      "United Arab Emirates":"Birleşik Arap Emirlikleri",
      "United Kingdom":"Birleşik Krallık",
      "United States":"Amerika Birleşik Devletleri",
      "Uruguay":"Uruguay",
      "Uzbekistan":"Özbekistan",
      "Vanuatu":"Vanuatu",
      "Vatican City":"Vatikan",
      "Venezuela":"Venezuela",
      "Vietnam":"Vietnam",
      "Yemen":"Yemen",
      "Zambia":"Zambiya",
      "Zimbabwe":"Zimbabve"
      };
      return this.multiReplace(country,mapObj);
    }, 

    convertGenre (genre) {
      var mapObj = {
         "Drama":"Dram",
         "War":"Savaş",
        "Comedy":"Komedi",
         "Sci-Fi":"Bilim-Kurgu",
         "Fantasy":"Fantastik",
         "Adventure":"Macera",
         "Romance":"Romantik",
         "Action":"Aksiyon",
         "Mystery":"Gizem",
         "Family":"Aile",
         "Crime":"Suç",
         "Documentary":"Belgesel",
         "Biography":"Biyografi",
         "Music":"Müzikal",
         "Animation":"Animasyon",
         "Horror":"Korku",
         "Thriller":"Gerilim",
         "Short":"Kısa",
         "Western":"Batı",
         "Sport":"Spor",
      };
      return this.multiReplace(genre,mapObj);
    },

    
  },
  computed: {
   
  },

});

app.mount("#app");
