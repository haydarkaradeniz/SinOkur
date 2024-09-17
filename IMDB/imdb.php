<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Training VueJs Test</title>
    <script src="scripts/axios.js"></script>
	<script src="scripts/vue.global.js"></script>
    <link type="text/css" rel="stylesheet" href="css/style.css"/>
    <link rel="shortcut icon" href="#">
</head>
  <body>
    
    <div id="app">
		
      <div class="movie-card">
        <table>
          <tr>
            <td class="movie-poster-td" rowspan="3">
              <img :src="movieData.poster" class="movie-poster"/>
              <img :src="detailData.favorite?'images/favori_aktif.svg':'images/favori_pasif.svg'" class="movie-favorite-ico" @click="favorite('movie')"/>
              <img :src="detailData.movieList?'images/liste_aktif.svg':'images/liste_pasif.svg'" class="movie-list-ico" @click="openListMenu('movie')"/>								
            </td>
            <td>
              <div class="movie-title">{{movieData.title_tr}}</div>
              <div class="movie-title-tr">{{movieData.title}}</div> 
            </td>
          </tr>
          <tr>
            <td>
              <div class="movie-director"><span class="font-bold">Yönetmen </span><span>{{movieData.director}}</span></div>
              <div class="movie-default">{{movieData.country + ", " + movieData.year}}</div>
              <div class="movie-default">{{movieData.genre}}</div>
              <div><span class="movie-imdb-rating">{{movieData.imdbRating + "/"}}</span><span class="movie-default">{{"10 (" + movieData.imdbVotes + " oy)"}}</span></div>
              <div class="movie-default">Süre: {{movieData.runtime}}</div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="movie-overview-header">Özet</div>
              <div class="movie-default">{{movieData.overview}}<a v-if="movieData.overviewLink" href="#" target="_blnk">>>daha fazlası için tıklayın</a></div>
            </td>
          </tr>

        </table>


      </div>


	  </div>

    <script src="scripts/app.js"></script>

  </body>
</html>