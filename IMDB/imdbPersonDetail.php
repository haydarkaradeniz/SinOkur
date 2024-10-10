<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sanatçılar</title>
    <script src="../imdb/scripts/axios.js"></script>
	<script src="../imdb/scripts/vue.global.js"></script>
    <link type="text/css" rel="stylesheet" href="../imdb/css/styledetail.css"/>
    <link rel="shortcut icon" href="#">
</head>
  <body>
    
    <div id="app">

      <div v-if="viewType == 'person'" class="main-card">
        <table>
          <tr class="main-poster-tr">
            <td class="main-poster-td">
              <img :src="personData.profile_path" class="main-poster"/>
              <img v-if="profile.loginId" :src="basicListData[getFavoriteListId()]?getBaseUrl()+'images/favori_aktif.svg':getBaseUrl()+'images/favori_pasif.svg'" class="person-favorite-ico" @click="favorite(getFavoriteListId())"/>		
            </td>
            <td class="main-info-td">
              <div class="main-name">{{personData.name}}</div>
              <div class="main-job margin-bt-20">{{personData.type}}</div> 
              <div class="person-birth-day"><span>{{personData.birthday}}</span></div>
              <div class="person-birth-place margin-bt-10"><span>{{personData.place_of_birth}}</span></div>
            </td>
            <td rowspan="2" class="vertical-seperator-td">
              <div class="vertical-seperator"></div>
            </td>
            <td rowspan="2" class="overview-td">
              <div class="overview-header">Biyografi:</div>
              <div class="main-default">{{personData.biography}}</div>
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
        
        <div class="movies-header-div">
          <span class="movies-header">Filmleri</span>
        </div>
        <div class="movie-list-main-div">
          <div class="movie-list-item-div" v-for="(movie, index) in personData.movieList">
            <div>
            <a :href="subPath+'/app.php/filmler?detail=1&viewType=movie&id='+movie.id" target="_parent"><img :src="movie.poster" class="movie-list-poster"/></a>
            </div>
            <div>
              <span class="movie-list-title">{{movie.original_title}}</span>
            </div>
            <div v-if="movie.title != movie.original_title">
              <span class="movie-list-org-title">({{movie.title}})</span>
            </div>
          </div>

        </div>
          

      </div>



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