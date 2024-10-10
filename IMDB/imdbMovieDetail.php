<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Filmler</title>
    <script src="../imdb/scripts/axios.js"></script>
	<script src="../imdb/scripts/vue.global.js"></script>
    <link type="text/css" rel="stylesheet" href="../imdb/css/styledetail.css"/>
    <link rel="shortcut icon" href="#">
</head>
  <body>
    
    <div id="app">
		
      <div v-if="viewType == 'movie'" class="main-card">
        <table>
          <tr class="main-poster-tr">
            <td class="main-poster-td">
              <img :src="movieData.poster" class="main-poster"/>
              <img v-if="profile.loginId" :src="basicListData[getFavoriteListId()]?getBaseUrl()+'images/favori_aktif.svg':getBaseUrl()+'images/favori_pasif.svg'" class="movie-favorite-ico" @click="favorite(getFavoriteListId(), undefined)"/>
              <img v-if="profile.loginId" :src="basicListData['watched']?getBaseUrl()+'images/izledim.svg':getBaseUrl()+'images/izlemedim.svg'" class="movie-watched-ico" @click="favorite('watched',!basicListData['watched']?'planned':undefined)"/>
              <img v-if="profile.loginId" :src="basicListData['movieListIcon']?getBaseUrl()+'images/liste_aktif.svg':getBaseUrl()+'images/liste_pasif.svg'" class="movie-list-ico" @click="showListMenu()"/>		
              <img :src="videoBox.iframe?getBaseUrl()+'images/fragman_aktif.svg':getBaseUrl()+'images/fragman_pasif.svg'" @click.stop="showVideo()" :class="{'movie-video-not-logined-ico':!profile.loginId, 'movie-video-logined-ico':profile.loginId}"/>

              
              <div v-if="favoriteListCardVisible" class="fovorite-list-card">
                <div v-for="(listKey, index) in Object.keys(userListInfo)">
                  <img :src="basicListData[listKey]?getBaseUrl()+'images/tick_aktif.svg':getBaseUrl()+'images/tick_pasif.svg'" class="movie-list-header-ico" @click="favorite(listKey, !basicListData[listKey] && listKey=='planned'?'watched':undefined)"/>
                  <span class="movie-favorite-list-header">{{userListInfo[listKey].header}}</span>
                </div>
              </div>		
            </td>
            <td class="main-info-td">
              <div class="main-name">{{movieData.title_tr}}</div>
              <div class="main-job margin-bt-20">{{movieData.title}}</div> 

              <div class="main-director"><span class="font-bold">Yönetmen </span><span>{{movieData.director}}</span></div>
              <div class="main-default margin-bt-20">{{movieData.country + ", " + movieData.year}}</div>
              <div class="main-default margin-bt-20">{{movieData.genre}}</div>

              <div><span class="imdb-rating">{{movieData.imdbRating}}</span><span class="main-default">/10&nbsp;</span> <span class="imdb-vote">{{"(" + movieData.imdbVotes + " oy)"}}</span></div>
              <div class="main-default">Süre: {{movieData.runtime}}</div>

            </td>
            <td rowspan="2" class="vertical-seperator-td">
              <div class="vertical-seperator"></div>
            </td>
            <td rowspan="2" class="overview-td">
              <div class="overview-header">Konusu:</div>
              <div class="main-default">{{movieData.overviewFull}}</div>
            </td>
          </tr>          
          <tr>
            <td colspan="2" class="rate-box-td">
              
              <div v-if="profile.loginId" class="rate-box-div">
                <div class="rate-box-img-div">
                  <img class="rate-star" :src="rateBoxStarUrl(1)" @mousemove="rateBoxMouseMove($event,1)" @mouseleave="rateBox.hoverScore = rateBox.score" @click="rateBoxClick()"/>
                  <img class="rate-star" :src="rateBoxStarUrl(2)" @mousemove="rateBoxMouseMove($event,2)" @mouseleave="rateBox.hoverScore = rateBox.score" @click="rateBoxClick()"/>
                  <img class="rate-star" :src="rateBoxStarUrl(3)" @mousemove="rateBoxMouseMove($event,3)" @mouseleave="rateBox.hoverScore = rateBox.score" @click="rateBoxClick()"/>
                  <img class="rate-star" :src="rateBoxStarUrl(4)" @mousemove="rateBoxMouseMove($event,4)" @mouseleave="rateBox.hoverScore = rateBox.score" @click="rateBoxClick()"/>
                  <img class="rate-star" :src="rateBoxStarUrl(5)" @mousemove="rateBoxMouseMove($event,5)" @mouseleave="rateBox.hoverScore = rateBox.score" @click="rateBoxClick()"/>
                </div>
                <div>
                  <span class="main-default">SineOkur puanı:</span><span class="main-imdb-score">&nbsp;{{rateBox.forumScore}}&nbsp;</span><span class="main-imdb-vote">( {{rateBox.forumVote}}&nbsp;oy)</span>
                </div>
              </div>
              <div v-else>
                &nbsp;
              </div>

            </td>
          </tr>
        </table>
        <div class="movies-header-div" v-if="movieData.personList && movieData.personList.length > 0">
          <span class="movies-header">Kadro</span>
        </div>
        <div class="movie-list-main-div">
          <div class="movie-list-item-div" v-for="(person, index) in movieData.personList">
            <div>
              <a :href="subPath+'/app.php/sanatcilar?detail=1&viewType=person&id='+person.id" target="_parent"><img :src="person.poster" class="movie-list-poster"/></a>
            </div>
            <div>
              <span class="movie-list-title">{{person.name}}</span>
            </div>
            <div>
              <span class="movie-list-org-title">({{person.role}})</span>
            </div>
          </div>

        </div>
      </div>

      <iframe id="sineokur-detail-video-iframe" style="position: absolute; display: none; z-index: 5; overflow: auto; resize: both;" allowfullscreen>
      </iframe>

	  </div>

    

    <script src="../imdb/scripts/app.js"></script>
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