
const app = Vue.createApp({
  data() {
    return {
      subPath:"",//""
      tmdb_api_key: "691a740114fe7dc34fbbe9f1a464cc5e",   
      omdb_api_key: "68d73d3c",
      apiData: {},
      movieData: {},
      profile : {
        loginId: undefined,
        userId: undefined,
        guestMode: false,
        userName: undefined,
      },  
      userListInfoBase : [
        {
          listId: 'watched',
          header:"İzlediğim Filmler",
          imdbType:"movie",
          movieType:"movie",
          personType:undefined,
          count:0
        },
        {
          listId: 'planned',
          header:"İzleyeceğim Filmler",
          imdbType:"movie",
          movieType:"movie",
          personType:undefined,
          count:0
        },
        {
          listId: 'watched',
          header:"İzlediğim Diziler",
          imdbType:"movie",
          movieType:"series",
          personType:undefined,
          count:0
        },
        {
          listId: 'planned',
          header:"İzleyeceğim Diziler",
          imdbType:"movie",
          movieType:"series",
          personType:undefined,
          count:0
        },
        {
          listId: 'liked-movie',
          header:"Favori Filmlerim",
          imdbType:"movie",
          movieType:"movie",
          personType:undefined,
          count:0
        },
        {
          listId: 'liked-movie',
          header:"Favori Dizilerim",
          imdbType:"movie",
          movieType:"series",
          personType:undefined,
          count:0
        },
        {
          listId: 'liked-person',
          header:"Favori Yönetmenler",
          imdbType:"person",
          movieType:undefined,
          personType:"YÖNETMEN",
          count:0
        },
        {
          listId: 'liked-person',
          header:"Favori Oyuncular",
          imdbType:"person",
          movieType:undefined,
          personType:"OYUNCU",
          count:0
        },
      ],
      userListInfo : [],
      userListDataInfo : [],
      selectedListIndex: 0,
      mainRowWidth:500,
      screenWidth:1024,
      screenLeftOffset:0,
      modalBox: {
        visible: false,
        message: "",
        type : "alert",
        width :500,
        listId : undefined,
        listHeader : undefined,
      },
      loadingBox: {
        visible: false,
      },
      searchBox: {
        visible: false,
        width :415,
        radioCheck: "withName",
        inputText : "",
        viewType : 1,
        searchedMovies: [],
        selectedMovies: [],
      },
      pageItem: {
        numberOfPageIndex:5,//must be odd number
        pageSize:30, 
        pageIndex:1,
        pageCount:0,
        pageNumbers:[],
        currentListIndex:0,
      },
      sortItem: {
        selected:"createDateDesc",
        sortColumn:"t1.create_date",
        sortPosition:"desc"
      }
     
    };
  },
  mounted() {   
    this.initSizeVariables();
  
  },
  
  created() {
    let uri = window.location.search.substring(1); 
    let params = new URLSearchParams(uri);
    this.profile.userId = params.get("u");
    window.addEventListener("resize", this.resizeEventHandler);
    this.getSessionUser();
  },

  destroyed() {
    window.removeEventListener("resize", this.resizeEventHandler);
  },
  
  methods: {


    getBaseUrl() {
      return "../imdb/";
    },

    initSizeVariables() {
      this.mainRowWidth = document.getElementById('sineokur-mainRow').getBoundingClientRect().width;
      this.screenWidth = window.innerWidth;
      this.screenLeftOffset = parseInt((this.screenWidth-this.mainRowWidth)/2);
    },

    resizeEventHandler(e) {
      this.initSizeVariables();
    },
    

    getSessionUser() {
      var fd = new FormData();  
      axios.get(this.getBaseUrl() + 'services/getSessionUser.php', fd).then(
        resp => {	
          if(resp.data.userId && parseInt(resp.data.userId) > 1) {
            this.profile.userId = this.profile.userId ? this.profile.userId : resp.data.userId;
            this.profile.loginId = resp.data.userId;
            this.profile.userName = resp.data.userName;
            this.refresh();
          }
        }).catch((err) => {
          console.error(err);
          this.profile.userId = 2;
          this.profile.loginId = 2;
          this.refresh();
        }
      ).finally(()=> {
        this.profile.guestMode = this.profile.userId != this.profile.loginId;
      });
    },

    refresh(pageIndex=1) {
      this.getUserList(pageIndex);
    },

    getUserList(pageIndex) {
      //initiliaze userListInfo 
      this.userListInfo = [];
      for(var i=0; i<this.userListInfoBase.length; i++) {
        this.userListInfo.push(this.userListInfoBase[i]);
      }
      var fd = new FormData();  
      fd.append('userId', this.profile.userId);
      axios.post(this.getBaseUrl() + 'services/getUserListMetaList.php', fd).then(
        resp => {	 
          if(resp.data.listData) {
            for(var i=0; i< resp.data.listData.length; i++) {
              this.userListInfo.push({
                listId : resp.data.listData[i].listId,
                header : resp.data.listData[i].header,
                count : 0
              });
            }
          }
          if(resp.data.listMovieData) {
            this.fillUserListInfoCount(resp.data.listMovieData);
          }
          this.selectPage(pageIndex);
        }
      ).catch((err) => {
        console.error(err);
      }).finally(()=> {
        //
      });

    },

    nvl(data) {
      return data?data:"";
    },

    trim(str, size) {
      return str.length<=size ? str : str.substring(0,size) + '...';
   },

    
    checkListEquility(movieData1, movieData2) {
      return movieData1.listId == movieData2.listId && ((movieData1.imdbType && movieData1.imdbType == movieData2.imdbType) || !movieData1.imdbType) &&
      ((movieData1.movieType && movieData1.movieType == movieData2.movieType) || !movieData1.movieType) &&
      ((movieData1.personType && movieData1.personType == movieData2.personType) || !movieData1.personType);
    },

    fillUserListInfoCount(listMovieData) {
      for(var i=0; i<this.userListInfo.length; i++) {
        this.userListInfo[i].count = 0;
        for(var j=0; j<listMovieData.length; j++) {
          if(this.checkListEquility(this.userListInfo[i],listMovieData[j])) {
            this.userListInfo[i].count = this.userListInfo[i].count + 1; 
          }
        }
      }
    },

    saveUserList() {
      if(this.modalBox.listHeader && this.modalBox.listHeader.trim().length >0) {
        var fd = new FormData();  
        fd.append('userId', this.profile.userId);
        fd.append('listId', this.modalBox.listId?this.modalBox.listId:"-1");  
        fd.append('header', this.modalBox.listHeader);
        axios.post(this.getBaseUrl() + 'services/insertListMeta.php', fd).then(
          resp => {	 
            this.showAlert("Listeniz " + (this.modalBox.listId?"güncellenmiştir.":"oluşturulmuştur"));
            if(!this.modalBox.listId) {
              this.selectedListIndex = this.userListInfo.length;
            } 
            this.refresh();
          }
        ).catch((err) => {
          console.error(err);
        }).finally(()=> {
          //this.hideModal();
        });
      }
    },

    deleteUserList() {
      var fd = new FormData();  
      fd.append('userId', this.profile.userId);
      fd.append('listId', this.modalBox.listId);  
      axios.post(this.getBaseUrl() + 'services/deleteUserListMeta.php', fd).then(
        resp => {	 
          this.showAlert("Listeniz silinmiştir");
          if(this.selectedListIndex > 0) {
            this.selectedListIndex = this.selectedListIndex-1; 
          } else {
            this.selectedListIndex = 0;
          }
          this.refresh();
        }
      ).catch((err) => {
        console.error(err);
      }).finally(()=> {
        //this.hideModal();
      });
    },

    getUserListData() {
      this.userListDataInfo = [];
      var fd = new FormData();  
      fd.append('userId', this.profile.userId);
      fd.append('listId', this.userListInfo[this.selectedListIndex].listId);  
      fd.append('pageSize', this.pageItem.pageSize);
      fd.append('pageIndex', this.pageItem.pageIndex);
      fd.append('sortColumn', this.sortItem.sortColumn);
      fd.append('sortPosition', this.sortItem.sortPosition);

      if(this.userListInfo[this.selectedListIndex].imdbType) {
        fd.append('imdbType', this.userListInfo[this.selectedListIndex].imdbType);
      }
      if(this.userListInfo[this.selectedListIndex].movieType) {
        fd.append('movieType', this.userListInfo[this.selectedListIndex].movieType);
      }
      if(this.userListInfo[this.selectedListIndex].personType) {
        fd.append('personType', this.userListInfo[this.selectedListIndex].personType);
      }
      axios.post(this.getBaseUrl() + 'services/getExtListData.php', fd).then(
        resp => {	 
          if(resp.data.listData) {
            for(var i=0; i< resp.data.listData.length; i++) {
              this.userListDataInfo.push(resp.data.listData[i]); 
            }
          }
        }
      ).catch((err) => {
        console.error(err);
      }).finally(()=> {
        //this.hideModal();
      });
    },

    hideModal() {
      this.modalBox.visible =  false;
    },

    showAlert(message) {
      this.modalBox.type = "alert";
      this.modalBox.width = 300;
      this.modalBox.message = message;
      this.modalBox.visible = true;
    },

    selectList(index) {
      this.selectedListIndex = index;
      this.pageItem.currentListIndex = this.selectedListIndex;     
      this.sortItem.selected = "createDateDesc";
      this.sortItem.sortColumn = "t1.create_date";
      this.sortItem.sortPosition = "desc";
      this.selectPage(1);
    },

    selectPage(page) {
      this.pageItem.pageCount = Math.ceil(this.userListInfo[this.selectedListIndex].count/this.pageItem.pageSize);
      if(page == "first") {
        this.pageItem.pageIndex = 1;
      } else if(page == "previous") {
        this.pageItem.pageIndex -= 1;
      } else if(page == "last") {
        this.pageItem.pageIndex = this.pageItem.pageCount;
      } else if(page == "next") {
        this.pageItem.pageIndex += 1;
      } else {
        this.pageItem.pageIndex = parseInt(page);
      }
      this.pageItem.pageNumbers = [];
      if(this.pageItem.pageCount>this.pageItem.numberOfPageIndex) {
        var offset = (this.pageItem.numberOfPageIndex-1)/2;
        var min = this.pageItem.pageIndex - offset<1?1:this.pageItem.pageIndex - offset;
        var max = this.pageItem.pageIndex + offset > this.pageItem.pageCount ? this.pageItem.pageCount : this.pageItem.pageIndex + offset;
        for(var i=min; i<=max; i++) {
          this.pageItem.pageNumbers.push(i);
        }
      } else {
        for(var i=0; i<this.pageItem.pageCount; i++) {
          this.pageItem.pageNumbers.push(i+1);
        }
      }  
      this.getUserListData();
    },

    


    editList() {
      if(this.selectedListIndex>7) {
        this.modalBox.type = 'save-list';
        this.modalBox.listId = this.userListInfo[this.selectedListIndex].listId;
        this.modalBox.listHeader = this.userListInfo[this.selectedListIndex].header;
        this.modalBox.width = 500;
        this.modalBox.visible = true;
      }
    },

    createList() {
      this.modalBox.type = 'save-list';
      this.modalBox.listId = undefined;
      this.modalBox.listHeader = undefined;
      this.modalBox.width = 500;
      this.modalBox.visible = true;
    },

    deleteList() {
      if(this.selectedListIndex>7) {
        this.modalBox.type = 'delete-list';
        this.modalBox.listId = this.userListInfo[this.selectedListIndex].listId;
        this.modalBox.message = '"' + this.userListInfo[this.selectedListIndex].header +'" listesini silmek istediğinize emin misiniz?';
        this.modalBox.width = 500;
        this.modalBox.visible = true;
      }
    },

    showSearchBox() {
      if(!(this.selectedListIndex<8 && this.selectedListIndex>5)) {
        this.searchBox.selectedMovies = [];
        this.searchBox.searchedMovies = [];
        this.searchBox.radioCheck = "withName";
        this.searchBox.inputText = "";
        this.searchBox.viewType = 1;
        this.searchBox.visible = true;
      }
    },

    selectRadio(selected) {
      this.searchBox.radioCheck = selected;
      this.searchBox.viewType = selected == 'withName' ? 1 : 2;
      this.searchBox.viewType = this.searchBox.selectedMovies.length >0?3:1;
      this.searchBox.inputText = "";
      this.searchBox.searchedMovies = [];
    },

    selectMovie(action, searchedIndex, selectedIndex) {
      if(action == 'add') {
        if(this.searchBox.radioCheck == "withId") {
          this.searchBox.selectedMovies.push(this.searchBox.searchedMovies[searchedIndex]);
          this.searchBox.viewType = 3;
        } else { 
          this.loadingBox.visible = true;
          this.findIMDBMovieId(this.searchBox.searchedMovies[searchedIndex].apiData);
        }
      } else {
        this.searchBox.selectedMovies.splice(selectedIndex, 1);
        this.searchBox.viewType = this.searchBox.selectedMovies.length > 0 ? 3 : 1;
      }
      
    },
    

    checkSelectedIndex(index) {
      for(var i=0; i< this.searchBox.selectedMovies.length; i++) {
        if(this.searchBox.selectedMovies[i].id == this.searchBox.searchedMovies[index].id) {
          return i;
        }
      }
      return -1;
    },

    
    checkNaN(text) {
      return !text || text.toString().indexOf("N/A") > -1 || text === 'NaN.NaN.NaN';
    },

    retrieve(text, type) {
      if(type == 'poster') {
        return !this.checkNaN(text)?text:this.getDefaultImage(type);
      } else if(type == 'title' && !this.checkNaN(text)) {
        return text.toLocaleUpperCase('tr');
      } else if(type == 'tmdb-poster' || type == 'tmdb-poster-detail-min') {
        return !this.checkNaN(text)?"http://image.tmdb.org/t/p/w500" + text:this.getDefaultImage(type);
      } else if(type == 'genre' && !this.checkNaN(text)) {
        return this.convertGenre(text);
      } else if(type == 'runtime' && !this.checkNaN(text)) {
        return text.replace('min','dk');
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
  
    fillMovieData(source) {
      if(source == 'omdb') {
        this.movieData.imdbID = this.apiData.imdbID;
        this.movieData.omdb_poster = this.apiData.Poster;
        this.movieData.title = this.retrieve(this.apiData.Title, 'title');
        this.movieData.omdb_director = this.apiData.Director;
        this.movieData.director = this.retrieve(this.apiData.Director);
        this.movieData.type = this.apiData.Type;
        this.movieData.year = this.retrieve(this.apiData.Year);
        this.movieData.genre = this.retrieve(this.apiData.Genre, 'genre');
        this.movieData.imdbRating = this.retrieve(this.apiData.imdbRating);
        this.movieData.imdbVotes = this.retrieve(this.apiData.imdbVotes);
        this.movieData.runtime = this.retrieve(this.apiData.Runtime, 'runtime');
      } else if(source == 'tmdb') {
        this.movieData.title_tr = this.retrieve(this.apiData.title, 'title');
        this.movieData.poster = this.retrieve(this.apiData.poster_path, 'tmdb-poster');
        this.movieData.id = this.apiData.id;
     } else if(source == 'tmdb-error') {
        this.movieData.title_tr = this.movieData.title;
        this.movieData.poster = this.retrieve(this.movieData.omdb_poster, 'poster');
        this.movieData.director = this.checkNaN(this.movieData.omdb_director) && this.movieData.type == 'series' ? 'Farklı yönetmenler' : this.movieData.director; 
        this.movieData.id = this.movieData.imdbID;
      }
    },


    createMovieData(data) {
      return !data ?
      {
        "id": this.movieData.id,
        "imdbId": this.movieData.imdbID,
        "poster": this.movieData.poster,
        "title": this.movieData.title,
        "year": this.movieData.year,
        "runtime": this.movieData.runtime,
        "imdbRating": this.movieData.imdbRating,
        "imdbVotes": this.movieData.imdbVotes,
        "imdbType": "movie",
        "type": this.movieData.type,
        "titleTr": this.movieData.title_tr,
        "director" :this.movieData.director
      }
      :
      {
        "apiData": data,
        "id": data.id,
        "title": this.retrieve(data.original_title, 'title'),
        "poster": this.retrieve(data.poster_path, 'tmdb-poster'),
        "titleTr": this.retrieve(data.title, 'title'),
      };
    },


    findIMDBMovieId(tmdbData) {
      axios.get('https://api.themoviedb.org/3/movie/' + tmdbData.id, {
        params: {
          "language":"tr",
          "api_key":this.tmdb_api_key
        },
        headers: {
          "content-type":"application/json"
        }
      }).then(response => {	
        this.getOMDBMovieDetail(response.data.imdb_id, tmdbData);
      }).catch((err) => {
        this.loadingBox.visible = false;
        console.error(err);
      });
    },

    getOMDBMovieDetail(imdbID, tmdbData = undefined) {
      this.apiData = {};
      this.movieData = {};
      axios.get('https://www.omdbapi.com', {
        params: {
          "plot": "full",
          "r": "json",
          "i" : imdbID,
          "apikey": this.omdb_api_key
        }
      }).then(response => {	
        if(response.data.Title) {
          this.apiData = response.data;
          this.fillMovieData('omdb');
          this.getTMDBMovieDetail(imdbID, tmdbData);
        } else {
          this.loadingBox.visible = false;
          this.showAlert("Bir hata oluştu.");
        }
      }).catch((err) => {
        this.loadingBox.visible = false;
        this.showAlert("Bir hata oluştu.");
        console.error(err);
      });
    },

    getTMDBMovieDetail(imdbID, tmdbData) {
      if(tmdbData) {
        this.apiData = tmdbData;
        this.fillMovieData('tmdb');
        this.searchBox.selectedMovies.push(this.createMovieData(undefined));
        this.loadingBox.visible = false;
        this.searchBox.viewType = 3;
      } else {
        axios.get('https://api.themoviedb.org/3/movie/' + imdbID, {
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
          this.searchBox.searchedMovies.push(this.createMovieData(undefined));
          this.loadingBox.visible = false;
          this.searchBox.viewType = 2;
        });
      }
    },


    searchTMDBMovie(query) {
      axios.get('https://api.themoviedb.org/3/search/movie', {
        params: {
          "language":"tr",
          "api_key":this.tmdb_api_key,
          "query":query
        },
        headers: {
          "content-type":"application/json"
        }
      }).then(response => {	
        for(var i=0; i<response.data.results.length; i++) {
          this.searchBox.searchedMovies.push(this.createMovieData(response.data.results[i]));
        }
      }).catch((err) => {
        console.error(err);
      }).finally(()=> {
        this.loadingBox.visible = false;
      });
    },

    




    
    
    searchMovie() {
      this.searchBox.searchedMovies = [];
      if(this.searchBox.radioCheck == "withId") {
        if(this.searchBox.inputText.trim().substring(0,2).toLowerCase() == "tt") {
          this.loadingBox.visible = true;
          this.getOMDBMovieDetail(this.searchBox.inputText.trim());
        } else {
          this.showAlert("Hatalı IMDb Id girdiniz, Lütfen tt0000000 formatında giriniz.")
        }
      } else {
        this.loadingBox.visible = true;
        this.searchTMDBMovie(this.searchBox.inputText.trim());
      }
      this.searchBox.viewType = 2;

    },

   


    addToUserList(userId, listId, movieData) {
      var fd = new FormData();  
      fd.append('userId', userId);
      fd.append('listId', listId);  
      fd.append('imdbId', movieData.imdbId);
      fd.append('imdbType', 'movie');
      fd.append('poster', movieData.poster);     
      fd.append('title', movieData.title);     
      fd.append('year', movieData.year);     
      fd.append('runtime', movieData.runtime);     
      fd.append('imdbRating', movieData.imdbRating);     
      fd.append('imdbVotes', movieData.imdbVotes);   
      fd.append('movieType', movieData.type);   
      fd.append('titleTr', movieData.titleTr);     
      fd.append('director', movieData.director);     
      return axios.post(this.getBaseUrl()+'services/insertListData.php', fd);
    },




    saveToUserlist() {
      this.searchBox.visible = false;
      this.loadingBox.visible = true;
      const promises = [];
      for(var i=0; i<this.searchBox.selectedMovies.length; i++) {
        promises.push(this.addToUserList(this.profile.userId, this.userListInfo[this.selectedListIndex].listId, this.searchBox.selectedMovies[i]));
      }
      Promise.all(promises).then(()=> {
          if(this.userListInfo[this.selectedListIndex].listId == 'watched' || this.userListInfo[this.selectedListIndex].listId == 'planned') {
            const removedPromises = [];
            for(var i=0; i<this.searchBox.selectedMovies.length; i++) {
              removedPromises.push(this.deleteFromUserList(this.userListInfo[this.selectedListIndex].listId == 'watched'?'planned':'watched', this.searchBox.selectedMovies[i].imdbId));
            }
            Promise.all(removedPromises).then(()=> {
              this.loadingBox.visible = false;
              this.refresh();
            });
          } else {
            this.loadingBox.visible = false;
            this.refresh();
          }
      }).catch((e)=>{
        console.error(err);
      });

    },


    deleteFromUserList(listId, imdbId) {
      var fd = new FormData();  
      fd.append('userId', this.profile.userId);
      fd.append('listId', listId);  
      fd.append('imdbId', imdbId);
      return axios.post(this.getBaseUrl() + 'services/deleteListData.php', fd);
    },


    removeFromUserList(listId, imdbId) {
      this.loadingBox.visible = true;
      this.deleteFromUserList(listId, imdbId).catch((err) => {
          console.error(err);
        }).finally(()=> {
          this.loadingBox.visible = false;
          var pageIndex = 1;
          if(this.pageItem.pageIndex>1) {
            pageIndex = this.userListDataInfo.length>1 ? this.pageItem.pageIndex : this.pageItem.pageIndex-1;
          }
          this.refresh(pageIndex);
        });
    },

    sortList(event) {
      //console.log("haydarrr " + event.target.value);
      //console.log("rexxxxx " + this.sortItem.selected);
      
      switch(this.sortItem.selected) {
        case "createDateDesc" : {
          this.sortItem.sortColumn = "t1.create_date";
          this.sortItem.sortPosition = "desc";
          break;
        }
        case "createDateAsc" : {
          this.sortItem.sortColumn = "t1.create_date";
          this.sortItem.sortPosition = "asc";
          break;
        }
        case "trMovieNameDesc" : {
          this.sortItem.sortColumn = "coalesce(title_tr,title)";
          this.sortItem.sortPosition = "desc";
          break;
        }
        case "trMovieNameAsc" : {
          this.sortItem.sortColumn = "coalesce(title_tr,title)";
          this.sortItem.sortPosition = "asc";
          break;
        }
        case "movieNameDesc" : {
          this.sortItem.sortColumn = "title";
          this.sortItem.sortPosition = "desc";
          break;
        }
        case "movieNameAsc" : {
          this.sortItem.sortColumn = "title";
          this.sortItem.sortPosition = "asc";
          break;
        }
        case "directorDesc" : {
          this.sortItem.sortColumn = "director";
          this.sortItem.sortPosition = "desc";
          break;
        }
        case "directorAsc" : {
          this.sortItem.sortColumn = "director";
          this.sortItem.sortPosition = "asc";
          break;
        }
        case "sinetayfaScoreDesc" : {
          this.sortItem.sortColumn = "coalesce(user_rating,0)";
          this.sortItem.sortPosition = "desc";
          break;
        }
        case "sinetayfaScoreAsc" : {
          this.sortItem.sortColumn = "coalesce(user_rating,0)";
          this.sortItem.sortPosition = "asc";
          break;
        }
        case "personNameDesc" :
        case "directorNameDesc" : {
          this.sortItem.sortColumn = "name";
          this.sortItem.sortPosition = "desc";
          break;
        }
        case "personNameAsc" :
        case "directorNameAsc" : {
          this.sortItem.sortColumn = "name";
          this.sortItem.sortPosition = "asc";
          break;
        } 
      }  
      
      this.selectPage(this.pageItem.pageIndex);


    },




    test0(imdbIdList) {
      if(imdbIdList && imdbIdList.length>0) {
        this.movieData = {};
        this.apiData = {};
        axios.get('https://www.omdbapi.com', {
          params: {
            "plot": "full",
            "r": "json",
            "i" : imdbIdList[0].imdbId,
            "apikey": this.omdb_api_key
          }
        }).then(response => {	
          if(response.data.Title) {
            this.apiData = response.data;
            this.fillMovieData('omdb');

            axios.get('https://api.themoviedb.org/3/movie/' + imdbIdList[0].imdbId, {
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
              var data = this.createMovieData(undefined);
              data.userId = imdbIdList[0].userId;
              data.listId = imdbIdList[0].listId;
              this.searchBox.searchedMovies.push(data);
              imdbIdList.splice(0, 1);
              this.test0(imdbIdList);
            });
          }
        }).catch((err) => {
          console.error(err);
        });

      }
    },


    test1() {
      this.tmdb_api_key = "2f849adef10b5a4e537d9cbe2ef02f66";  
      this.omdb_api_key = "278e784c";
      var imdbList = [];
      var fd = new FormData();  
      fd.append('userId', this.profile.userId);
      axios.post(this.getBaseUrl() + 'services/test.php', fd).then(
        resp => {	 
          if(resp.data.testData) {
            var max = resp.data.testData.length>5?5:resp.data.testData.length;
            for(var i=0; i< max; i++) {
              imdbList.push(resp.data.testData[i]);
            }
            this.test0(imdbList);
          }
        }
      ).catch((err) => {
        console.error(err);
      }).finally(()=> {
        console.log("test 1 done");
      });
    },

    test2() {
      const promises = [];
      for(var i=0; i<this.searchBox.searchedMovies.length; i++) {
        promises.push(this.addToUserList(this.searchBox.searchedMovies[i].userId, this.searchBox.searchedMovies[i].listId, this.searchBox.searchedMovies[i]));
            
      }
      Promise.all(promises).then(()=> {
        console.log(" haydar done");
    }).catch((err)=>{
      console.error(err);
    }).finally(()=> {
      console.log("test 2 done");
    });



    },

    selectIMDbList() {
      var selectBtn = this.$refs.upload;
      selectBtn.click();

    },

    async readIMDbList(event) {
      const file = event.target.files.item(0);
      const text = await file.text();
      const lines = text.split("\n");
      console.log("hellooo " +  lines.length);
    },



    multiReplace(data, mapObj) {
      var re = new RegExp(Object.keys(mapObj).join("|"),"gi");
      return data.replace(re, function(matched){return mapObj[matched];});

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
