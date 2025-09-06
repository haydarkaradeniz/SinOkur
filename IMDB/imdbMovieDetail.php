<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Filmler</title>
    <script src="../imdb/scripts/axios.js"></script>
	<script src="../imdb/scripts/vue.global.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> <style>
        body {
            background-color: transparent;
            position: relative;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        #app {
            position: relative;
            padding-top: 60px;
        }

        /* --- Rastgele Film ve Arama Tuşları Stilleri --- */
        .top-controls {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            gap: 10px;
        }

        .control-button {
            background-color: #FF7F50;
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            font-size: 20px;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            transition: background-color 0.2s ease, transform 0.2s ease;
        }

        .control-button:hover {
            background-color: #e66a3d;
            transform: scale(1.05);
        }

        .control-button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .search-container {
            position: relative;
            display: inline-block;
        }

        .search-input-wrapper {
            position: absolute;
            top: 0;
            right: 50px;
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            padding: 0;
            width: 0;
            overflow: hidden;
            transition: width 0.3s ease, padding 0.3s ease;
            white-space: nowrap;
        }

        .search-input-wrapper.expanded {
            width: 260px;
            padding: 5px 15px;
        }

        .search-input {
            flex-grow: 1;
            border: none;
            outline: none;
            padding: 0;
            font-size: 14px;
            background-color: transparent !important;
        }

        .search-results-dropdown {
            position: absolute;
            top: 50px;
            right: 0;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            max-height: 300px;
            overflow-y: auto;
            width: 340px;
        }

        .search-result-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            display: flex;
            align-items: center;
            transition: background-color 0.2s ease;
        }

        .search-result-item:hover {
            background-color: #f0f0f0;
        }

        .search-result-item:last-child {
            border-bottom: none;
        }

        .search-result-poster {
            width: 50px;
            height: 75px;
            border-radius: 5px;
            margin-right: 10px;
            object-fit: cover;
        }

        .search-result-title {
            font-weight: bold;
        }

        .search-result-original-title, .search-result-year {
            font-size: 12px;
            color: #666;
        }

        /* Mevcut stiller */
        .main-card {
            width: 100%;
        }

        .main-poster {
            width: 160px;
            height: 240px;
            margin-right: 7px;
            border-radius: 5px;
            margin-left: 5px;
        }

        .main-poster-tr {
            height: 50px;
        }

        .main-poster-td {
            vertical-align: top;
        }

        .rate-box-img-div {
            text-align: left;
            padding-left: 20px;
        }

        .person-favorite-ico {
            position: absolute;
            margin-left: -100px;
            margin-top: 225px;
            cursor: pointer;
        }

        .main-name {
            font-size: 24pt;
            font-weight: bold;
            white-space: nowrap;
            padding-top: 5px;
            padding-bottom: 13px;
        }

        .main-job {
            font-size: 16pt;
            white-space: nowrap;
        }

        .main-director {
            font-size: 12pt;
            white-space: nowrap;
        }

        .person-birth-day {
            font-size: 12pt;
            white-space: nowrap;
        }

        .person-birth-place {
            font-size: 12pt;
        }

        .imdb-rating {
            font-size: 16pt;
            font-weight: bold;
        }

        .imdb-vote {
            font-size: 10pt;
        }

        .main-default {
            font-size: 12pt;
        }

        .overview-td {
            vertical-align: top;
        }

        .overview-header {
            font-size: 16pt;
            font-weight: bold;
            margin-top: 5px;
            margin-bottom: 20px;
        }

        .main-info-td {
            vertical-align: top;
        }

        .rate-star{
            margin: 0px;
            padding: 0px;
            padding-bottom: 3px;
            padding-top: 3px;
        }

        .movies-header-div {
          margin-top: 5px;
        }


        .vertical-seperator {
            width: 0.5px;
            height: 275px;
            background-color: #CCCCCC;
            margin-left: 20px;
            margin-right: 20px;
        }


        .vertical-seperator-td {
            vertical-align: top;
            padding-top: 10px;
        }


        .movie-favorite-ico {
            position: absolute;
            margin-left: -145px;
            margin-top: 225px;
            cursor: pointer;
        }

        .movie-watched-ico {
            position: absolute;
            margin-left: -114px;
            margin-top: 228px;
            cursor: pointer;
        }

        .movie-list-ico {
            position: absolute;
            margin-left: -89px;
            margin-top: 224px;
            cursor: pointer;
            z-index: 1;
        }

        .movie-video-logined-ico {
            position: absolute;
            margin-left: -56px;
            margin-top: 228px;
            cursor: pointer;
        }


        .movie-video-not-logined-ico {
            position: absolute;
            margin-left: -100px;
            margin-top: 228px;
            cursor: pointer;
        }

        .fovorite-list-card {
            border: 1px solid #FF7F50;
            border-radius: 5px;
            width: 395px;
            height: 170px;
            background-color: #F8F6F2;
            min-width: 250px;
            min-height: 120px;
            position: absolute;
            margin-top: -2px;
            margin-left: 98px;
            padding-top: 5px;
            padding-left: 8px;
            backdrop-filter: blur(5px);
            overflow-y: auto;
            background-color: rgba(255,227,217,0.70);
            box-shadow: rgba(255, 127, 80, 0.45) 0px 25px 20px -20px;
        }
        .fovorite-list-card::-webkit-scrollbar {
            width: 3px;
            background-color: #none;
            border-left: 1px solid #none;
        }
        .fovorite-list-card::-webkit-scrollbar-thumb {
            background-color: #FF7F50;
        }
        .fovorite-list-card::-webkit-scrollbar-thumb:hover {
            background-color: #FF7F50;
        }

        .movie-favorite-list-header {
            font-size: 10pt;
            vertical-align: top;
            position: relative;
            top: 8px;
            padding-left: 5px;
            color: #444;
        }

        .movie-list-header-ico {
            cursor: pointer;
            padding-left: 5px;
        }



        .movies-header {
            font-size: 16pt;
            font-weight: bold;
            margin-left: 5px;
        }

        .movie-list-title {
            font-size: 12pt;
            font-weight: bold;
        }

        .movie-list-org-title {
            font-size: 12pt;
        }

        .movie-list-poster {
            width: 100px;
            border-radius: 5px;
            margin-bottom: 5px;
            transition: transform 0.2s ease-in-out;
        }

        .movie-list-poster:hover {
            transform: scale(1.05);
        }

        .movie-list-main-div {
            display: flex;
            flex-wrap: wrap;
            flex-direction: row;
        }

        .movie-list-item-div {
            width: 150px;
            text-align: center;
            margin-top: 15px;
        }

        .main-imdb-score {
            font-size: 16pt;
            font-weight: bold;
        }

        .main-imdb-vote {
            font-size: 10pt;
        }

        .rate-box-div {
            margin-top: 10px;
            margin-bottom: 20px;
            width: -webkit-fit-content;
            text-align: center;
        }

        .rate-box-td {
            vertical-align: top;
        }

        .font-bold {
            font-weight: bold;
        }

        .margin-bt-10 {
            margin-bottom: 10px;
        }

        .margin-bt-20 {
            margin-bottom: 20px;
        }

        .pointer {
            cursor: pointer;
        }


        /* --- Sekme Menüsü Stilleri --- */

        .tabs-container {
            width: 100%;
            margin-top: 30px;
            border: none;
            border-radius: 8px;
            background-color: transparent;
            overflow: hidden;
            box-shadow: none;
        }

        .tab-headers {
            display: flex;
            border-bottom: none;
            background-color: transparent;
            padding-left: 10px;
        }

        .tab-header {
            padding: 15px 25px;
            cursor: pointer;
            font-weight: bold;
            color: #auto;
            transition: background-color 0.2s ease, color 0.2s ease, border-bottom 0.2s ease;
            user-select: none;
            font-size: 14pt;
            background-color: transparent;
        }

        .tab-header:hover {
            background-color: transparent;
            color: #FF7F50;
        }

        .tab-header.active {
            background-color: transparent;
            color: #FF7F50;
            border-bottom: 4px solid #FF7F50;
        }

        .tab-content-wrapper {
            padding: 20px;
            background-color: transparent;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .tab-content .movie-list-item-div {
            width: 150px;
            margin: 15px;
        }

        .tab-content .movie-list-poster {
            width: 100%;
            height: auto;
            border-radius: 5px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            cursor: pointer;
        }

        .backdrop-image {
            width: 100%;
            max-height: 150px;
            object-fit: cover;
        }

        .no-content-message {
            color: #888;
            font-style: italic;
            padding: 20px;
            text-align: center;
        }

        /* --- Zoom Modal Stilleri --- */
        .zoom-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .zoom-modal-overlay.visible {
            opacity: 1;
            visibility: visible;
        }

        .zoom-modal-content {
            position: relative;
            max-width: 70vw;
            max-height: 70vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: transparent;
            transform: scale(0.8);
            transition: transform 0.3s ease;
        }

        .zoom-modal-overlay.visible .zoom-modal-content {
            transform: scale(1);
        }

        .zoomed-image {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
        }

        .zoom-close-button {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 30px;
            color: #fff;
            cursor: pointer;
            z-index: 1001;
            background: none;
            border: none;
            padding: 0;
            line-height: 1;
            text-shadow: 0 0 5px rgba(0,0,0,0.5);
        }

        .zoom-nav-button {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 50px;
            color: #fff;
            cursor: pointer;
            z-index: 1001;
            background: none !important;
            border: none;
            padding: 10px;
            text-shadow: 0 0 10px rgba(0,0,0,0.7);
            transition: color 0.2s ease;
            opacity: 0;
            transition: opacity 0.3s ease;
            outline: none;
        }

.zoom-modal-content:hover .zoom-nav-button {
    opacity: 1;
}

.zoom-nav-button:focus {
    outline: none;
}

        .zoom-nav-button:hover {
            color: #FF7F50;
        }

        .zoom-nav-button.left {
            left: 20px;
        }

        .zoom-nav-button.right {
            right: 20px;
        }

        .zoom-image-counter {
            position: absolute;
            bottom: 20px;
            color: #fff;
            font-size: 18px;
            text-shadow: 0 0 5px rgba(0,0,0,0.5);
            z-index: 1001;
        }
        /* Yeni eklenecek altyazı bölümü için stiller */
        .subtitle-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
            transition: background-color 0.2s ease;
        }
        .subtitle-item:last-child {
            border-bottom: none;
        }
        .subtitle-item:hover {
            background-color: #f9f9f9;
        }
        .subtitle-details {
            flex-grow: 1;
        }
        .subtitle-name {
            font-weight: bold;
            color: #333;
        }
        .subtitle-lang, .subtitle-uploader {
            font-size: 12px;
            color: #666;
        }
        .subtitle-download-btn {
            background-color: #5cb85c;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.2s ease;
        }
        .subtitle-download-btn:hover {
            background-color: #4cae4c;
        }
        .subtitle-download-btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
    </style>
    <link rel="shortcut icon" href="#">
</head>
  <body>
    
    <div id="app">
        <div class="top-controls" >
            <div class="search-container">
                <button @click="toggleSearch()" class="control-button" style="background-color: #007bff;">
                    <i class="fa fa-search"></i>
                </button>
                <div :class="{'expanded': isSearchVisible}" class="search-input-wrapper">
                    <input type="text" v-model="searchQuery" @input="searchMovies" placeholder="Film ara (Türkçe, Orijinal & IMDb ID)" class="search-input" ref="searchInput"/>
                </div>
                <div v-if="isSearchVisible && searchResults.length > 0" class="search-results-dropdown">
                    <div v-for="movie in searchResults" :key="movie.id" @click="selectSearchResult(movie)" class="search-result-item">
                        <img :src="retrieve(movie.poster_path, 'tmdb-poster-detail-min')" class="search-result-poster"/>
                        <div>
                            <div class="search-result-title">{{ movie.title }}</div>
                            <div class="search-result-original-title">({{ movie.original_title }})</div>
                            <div class="search-result-year">{{ movie.release_date ? movie.release_date.substring(0, 4) : '' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <button @click="loadRandomMovie()" :disabled="randomMovieLoading" class="control-button">
                <i class="fa fa-refresh"></i>
            </button>
        </div>
		
      <div v-if="viewType == 'movie'" class="main-card" id="sinetayfa-mainRow">
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
                  <span class="main-default">SİNETAYFA puanı:</span><span class="main-imdb-score">&nbsp;{{rateBox.forumScore}}&nbsp;</span><span class="main-imdb-vote">( {{rateBox.forumVote}}&nbsp;oy)</span>
                </div>
              </div>
              <div v-else>
                &nbsp;
              </div>

            </td>
          </tr>
        </table>
        </div>

      <div class="tabs-container" v-if="viewType == 'movie'">
          <div class="tab-headers">
              <div class="tab-header active" data-tab="cast-tab">Kadro</div> 
              <div class="tab-header" data-tab="posters-tab">Afişler</div>
              <div class="tab-header" data-tab="backdrops-tab">Arka Planlar</div>
              <div class="tab-header" data-tab="comments-tab">Yorumlar</div>
              <div class="tab-header" data-tab="subtitles-tab">Altyazılar</div>
          </div>

          <div class="tab-content-wrapper">
              <div class="tab-content active" id="cast-tab">
                  <div class="movie-list-main-div" v-if="movieData.personList && movieData.personList.length > 0">
                      <div class="movie-list-item-div" v-for="(person, index) in movieData.personList" :key="'person-' + index">
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
                  <div v-else class="no-content-message">Kadro bilgisi bulunamadı.</div>
              </div>

              <div class="tab-content" id="posters-tab">
                  <div class="movie-list-main-div">
                      <div class="movie-list-item-div" v-for="(poster, index) in movieData.posters" :key="'poster-' + index">
                          <div>
                              <img :src="poster" class="movie-list-poster" @click="openZoomModal(poster, movieData.posters, index)"/>
                          </div>
                      </div>
                  </div>
                  <div v-if="!movieData.posters || movieData.posters.length === 0" class="no-content-message">Afiş bulunamadı.</div>
              </div>

              <div class="tab-content" id="backdrops-tab">
                  <div class="movie-list-main-div">
                      <div class="movie-list-item-div" v-for="(backdrop, index) in movieData.backdrops" :key="'backdrop-' + index">
                          <div>
                              <img :src="backdrop" class="movie-list-poster backdrop-image" @click="openZoomModal(backdrop, movieData.backdrops, index)"/>
                          </div>
                      </div>
                  </div>
                  <div v-if="!movieData.backdrops || movieData.backdrops.length === 0" class="no-content-message">Arka plan görseli bulunamadı.</div>
              </div>

              <div class="tab-content" id="comments-tab">
                  <div class="no-content-message">Geliştirme aşamasında...</div>
              </div>

              <div class="tab-content" id="subtitles-tab">
                  <div v-if="subtitlesLoading" class="no-content-message">Altyazılar yükleniyor...</div>
                  <div v-else-if="subtitles && subtitles.length > 0">
                      <div v-for="subtitle in subtitles" :key="subtitle.id" class="subtitle-item">
                          <div class="subtitle-details">
                              <div class="subtitle-name">{{ subtitle.fileName }}</div>
                              <div class="subtitle-lang">{{ subtitle.language }}</div>
                              <div class="subtitle-uploader">Yükleyen: {{ subtitle.uploader.name }}</div>
                              <div class="subtitle-date">Yüklenme Tarihi: {{ new Date(subtitle.uploadDate).toLocaleDateString() }}</div>
                          </div>
                          <div>
                              <button class="subtitle-download-btn" @click="downloadSubtitle(subtitle.downloadLink)" :disabled="!profile.loginId">
                                  {{ profile.loginId ? 'İndir' : 'Giriş Yapın' }}
                              </button>
                          </div>
                      </div>
                  </div>
                  <div v-else class="no-content-message">Bu film için Türkçe altyazı bulunamadı.</div>
              </div>
          </div>
      </div>
      <iframe id="sinetayfa-detail-video-iframe" style="position: absolute; display: none; z-index: 5; overflow: auto; resize: both;" allowfullscreen>
      </iframe>

      <div class="zoom-modal-overlay" :class="{ 'visible': isZoomModalVisible }" @click.self="closeZoomModal()">
          <div class="zoom-modal-content">
              <button class="zoom-close-button" @click="closeZoomModal()">&times;</button>
              <img :src="currentZoomedImageSrc" class="zoomed-image"/>
              <button class="zoom-nav-button left" @click="navigateZoomedImage(-1)">&#10094;</button> 
              <button class="zoom-nav-button right" @click="navigateZoomedImage(1)">&#10095;</button> 
              <div class="zoom-image-counter" v-if="currentZoomedImageList.length > 0">
                  {{ currentZoomedImageIndex + 1 }} / {{ currentZoomedImageList.length }}
              </div>
          </div>
      </div>
	  </div>

<div id="customAlertOverlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); display: flex; justify-content: center; align-items: center; z-index: 1000; opacity: 0; visibility: hidden; transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;">
        <div id="customAlertBox" style="background-color: white; padding: 30px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2); text-align: center; max-width: 400px; width: 90%; transform: scale(0.9); transition: transform 0.3s ease-in-out;">
            <h2 style="color: #dc2626; font-size: 1.5rem; font-weight: bold; margin-bottom: 1rem;">Uyarı!</h2>
            <p style="color: #374151; margin-bottom: 1.5rem;">
                Geliştirici araçlarını açtınız. Lütfen bu sayfada değişiklik yapmamaya özen gösterin.
            </p>
            <button id="customAlertCloseBtn" style="background-color: #ef4444; color: white; font-weight: bold; padding: 0.5rem 1.5rem; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: none; cursor: pointer;">
                Kapat
            </button>
        </div>
    </div>

    <script src="../imdb/scripts/app.js"></script>
    <script src="../imdb/scripts/veri_koruma.js"></script>
  </body>
</html>