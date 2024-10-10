<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>IMDb</title>
    <script src="scripts/axios.js"></script>
	<script src="scripts/vue.global.js"></script>
    <link type="text/css" rel="stylesheet" href="css/style.css"/>
    <link rel="shortcut icon" href="#">
</head>
  <body>
    
    <div id="app">
		
      <div v-if="viewType == 'movie'" class="movie-card">
        <table>
          <tr>
            <td class="movie-poster-td" rowspan="3">
              <a :href="subPath+'/app.php/filmler?detail=1&id='+imdbID" target="_parent"><img :src="movieData.poster" class="movie-poster"/></a>
              <img v-if="profile.loginId" :src="basicListData[getFavoriteListId()]?'images/favori_aktif.svg':'images/favori_pasif.svg'" class="movie-favorite-ico" @click="favorite(getFavoriteListId(), undefined)"/>
              <img v-if="profile.loginId" :src="basicListData['watched']?'images/izledim.svg':'images/izlemedim.svg'" class="movie-watched-ico" @click="favorite('watched',!basicListData['watched']?'planned':undefined)"/>
              <img v-if="profile.loginId" :src="basicListData['movieListIcon']?'images/liste_aktif.svg':'images/liste_pasif.svg'" class="movie-list-ico" @click="showListMenu()"/>		
              
              <div v-if="favoriteListCardVisible" class="fovorite-list-card">
                <div v-for="(listKey, index) in Object.keys(userListInfo)">
                  <img :src="basicListData[listKey]?'images/tick_aktif.svg':'images/tick_pasif.svg'" class="movie-list-header-ico" @click="favorite(listKey, !basicListData[listKey] && listKey=='planned'?'watched':undefined)"/>
                  <span class="movie-favorite-list-header">{{userListInfo[listKey].header}}</span>
                </div>


              </div>						
            </td>
            <td>
              <div class="movie-title-tr">{{movieData.title_tr}}</div>
              <div class="movie-title margin-bt-10">{{movieData.title}}</div> 
            </td>
          </tr>
          <tr>
            <td>
              <div class="movie-director"><span class="font-bold">Yönetmen </span><span>{{movieData.director}}</span></div>
              <div class="movie-default">{{movieData.country + ", " + movieData.year}}</div>
              <div class="movie-default">{{movieData.genre}}</div>
              <div><span class="movie-imdb-rating">{{movieData.imdbRating + "/"}}</span><span class="movie-default">{{"10 (" + movieData.imdbVotes + " oy)"}}</span></div>
              <div class="movie-default margin-bt-10">Süre: {{movieData.runtime}}</div>
            </td>
          </tr>
          <tr>
            <td>
              <div>
                <span class="movie-overview-header">Özet&nbsp;</span>
                <span class="movie-default">{{movieData.overview}}<a v-if="movieData.overviewLink" :href="subPath+'/app.php/filmler?detail=1&id='+imdbID" target="_parent">&nbsp;&lt;devamı&gt;</a></span>
              </div>
            </td>
          </tr>

        </table>


      </div>

      <div v-if="viewType == 'person'" class="person-card">
        <table>
          <tr>
            <td class="person-poster-td" rowspan="3">
              <a :href="subPath+'/app.php/sanatcilar?detail=1&id='+imdbID" target="_parent"><img :src="personData.profile_path" class="person-poster"/></a>
              <img v-if="profile.loginId" :src="basicListData[getFavoriteListId()]?'images/favori_aktif.svg':'images/favori_pasif.svg'" class="person-favorite-ico" @click="favorite(getFavoriteListId())"/>							
            </td>
            <td>
              <div class="person-name">{{personData.name}}</div>
              <div class="person-job margin-bt-10">{{personData.type}}</div> 
            </td>
          </tr>
          <tr>
            <td>
              <div class="person-birth"><span class="font-bold">Doğum Günü </span><span>{{personData.birthday}}</span></div>
              <div class="person-birth margin-bt-10"><span class="font-bold">Doğum Yeri </span><span>{{personData.place_of_birth}}</span></div>
            </td>
          </tr>
          <tr>
            <td>
              <div>
                <div>
                  <span v-if="personData.type=='YÖNETMEN'" class="person-movies-header">Filmleri</span>
                  <span v-else class="person-movies-header">Filmleri ve Rolleri</span>
                </div>
                <template v-for="(movie, index) in personData.movies">
                  <span class="person-default" :class="{'font-bold':personData.type!='YÖNETMEN'}">{{movie.original_title}}</span>
                  <span v-if="personData.type!='YÖNETMEN' && movie.character.trim()!=='-'" class="person-default">{{' (' + movie.character + ')'}}</span>
                  <span v-if="index != personData.movies.length-1" class="person-default">,&nbsp;</span>
                </template>
                <span v-if="personData.moviesOverflowLink" class="person-default">
                  <a :href="subPath+'/app.php/sanatcilar?detail=1&id='+imdbID" target="_parent">&nbsp;&lt;devamı&gt;</a>
                </span>
              </div>
            </td>
          </tr>



        </table>
      </div>

    
	  </div>

    <script src="scripts/app.js"></script>
    <script language="Javascript">
      var isNS = (navigator.appName == "Netscape") ? 1 : 0;
      var EnableRightClick = 0;
      if(isNS)
      document.captureEvents(Event.MOUSEDOWN||Event.MOUSEUP);
      function mischandler(){
      if(EnableRightClick==1){ return true; }
      else {return false; }
      }
      function mousehandler(e){
      if(EnableRightClick==1){ return true; }
      var myevent = (isNS) ? e : event;
      var eventbutton = (isNS) ? myevent.which : myevent.button;
      if((eventbutton==2)||(eventbutton==3)) return false;
      }
      function keyhandler(e) {
      var myevent = (isNS) ? e : window.event;
      if (myevent.keyCode==96)
      EnableRightClick = 1;
      return;
      }
      document.oncontextmenu = mischandler;
      document.onkeypress = keyhandler;
      document.onmousedown = mousehandler;
      document.onmouseup = mousehandler;
    </script>
  </body>
</html>