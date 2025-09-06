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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="shortcut icon" href="#">
    <style>
        body {
            background-color: transparent;
            position: relative; /* [cite: 20] */
            min-height: 100vh; /* [cite: 21] */
            margin: 0; /* [cite: 22] */
            padding: 0; /* [cite: 22] */
        }

        #app {
            position: relative; /* [cite: 23] */
            padding-top: 60px; /* [cite: 24] */
        }

        /* --- Rastgele ve Arama Tuşları Stilleri (from Filmler.txt) --- */
        .top-controls {
            position: absolute;
            top: 10px; /* [cite: 25] */
            right: 10px;
            z-index: 99;
            display: flex;
            gap: 10px; /* [cite: 26] */
        }

        .control-button {
            background-color: #FF7F50; /* [cite: 27] */
            color: white;
            border: none;
            border-radius: 50%; /* [cite: 28] */
            width: 40px;
            height: 40px;
            font-size: 20px;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2); /* [cite: 29] */
            transition: background-color 0.2s ease, transform 0.2s ease; /* [cite: 30] */
        }

        .control-button:hover {
            background-color: #e66a3d; /* [cite: 31] */
            transform: scale(1.05); /* [cite: 32] */
        }

        .control-button:disabled {
            background-color: #ccc; /* [cite: 33] */
            cursor: not-allowed; /* [cite: 33] */
        }

        .search-container {
            position: relative;
            display: inline-block; /* [cite: 34] */
        }

        .search-input-wrapper {
            position: absolute;
            top: 0; /* [cite: 35] */
            right: 50px;
            background-color: white; /* [cite: 36] */
            border-radius: 20px; /* [cite: 36] */
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            padding: 0; /* [cite: 37] */
            width: 0; /* [cite: 38] */
            overflow: hidden; /* [cite: 39] */
            transition: width 0.3s ease, padding 0.3s ease; /* [cite: 39] */
            white-space: nowrap; /* [cite: 40] */
        }

        .search-input-wrapper.expanded {
            width: 260px; /* [cite: 41] */
            padding: 5px 15px; /* [cite: 42] */
        }

        .search-input {
            flex-grow: 1; /* [cite: 43] */
            border: none; /* [cite: 43] */
            outline: none;
            padding: 0;
            font-size: 14px; /* [cite: 44] */
            background-color: transparent; /* [cite: 44] */
        }

        .search-results-dropdown {
            position: absolute;
            top: 50px; /* [cite: 45] */
            right: 0; /* [cite: 46] */
            background-color: white; /* [cite: 46] */
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            max-height: 300px;
            overflow-y: auto;
            width: 340px; /* [cite: 47] */
            z-index: 98; /* [cite: 48] */
        }

        .search-result-item {
            padding: 10px; /* [cite: 49] */
            border-bottom: 1px solid #eee; /* [cite: 49] */
            cursor: pointer;
            display: flex;
            align-items: center;
            transition: background-color 0.2s ease; /* [cite: 50] */
        }

        .search-result-item:hover {
            background-color: #f0f0f0; /* [cite: 51] */
        }

        .search-result-item:last-child {
            border-bottom: none; /* [cite: 52] */
        }

        .search-result-poster {
            width: 50px; /* [cite: 53] */
            height: 75px; /* [cite: 53] */
            border-radius: 5px;
            margin-right: 10px;
            object-fit: cover;
        }

        .search-result-title {
            font-weight: bold; /* [cite: 54] */
        }

        .search-result-original-title, .search-result-year { /* .search-result-year might not be relevant for person */
            font-size: 12px; /* [cite: 55] */
            color: #666; /* [cite: 55] */
        }

        /* Stiller from Filmler.txt / styledetail.css (relevant parts) */
        .main-card {
            width: 100%; /* [cite: 56] */
        }
        .main-poster {
            width: 160px; /* [cite: 57] */
            height: 240px; /* [cite: 57] */
            margin-right: 7px;
            border-radius: 5px;
            margin-left: 5px;
        }
        .main-poster-tr { height: 50px; /* [cite: 58] */ }
        .main-poster-td { vertical-align: top; /* [cite: 59] */ }
        .rate-box-img-div { text-align: left; padding-left: 20px; /* [cite: 60] */ }

        .person-favorite-ico { /* From Sanatçılar.txt, kept for person */
            position: absolute; /* [cite: 61] */
            margin-left: -100px; /* Adjusted if necessary, original: -100px [cite: 61] */
            margin-top: 225px; /* [cite: 61] */
            cursor: pointer;
        }
        .main-name { font-size: 24pt; font-weight: bold; white-space: nowrap; padding-top: 5px; padding-bottom: 13px; /* [cite: 62] */ }
        .main-job { font-size: 16pt; white-space: nowrap; /* [cite: 63] */ }
        .person-birth-day { font-size: 12pt; white-space: nowrap; /* [cite: 65] */ }
        .person-birth-place { font-size: 12pt; /* [cite: 66] */ }
        .main-default { font-size: 12pt; /* [cite: 69] */ }
        .overview-td { vertical-align: top; /* [cite: 70] */ }
        .overview-header { font-size: 16pt; font-weight: bold; margin-top: 5px; margin-bottom: 20px; /* [cite: 71] */ }
        .main-info-td { vertical-align: top; /* [cite: 72] */ }
        .rate-star{ margin: 0px; padding: 0px; padding-bottom: 3px; padding-top: 3px; /* [cite: 73] */ }
        .vertical-seperator { width: 0.5px; height: 275px; background-color: #CCCCCC; margin-left: 20px; margin-right: 20px; /* [cite: 75] */ }
        .vertical-seperator-td { vertical-align: top; padding-top: 10px; /* [cite: 76] */ }

        .movie-list-title { font-size: 12pt; font-weight: bold; /* [cite: 92] */ }
        .movie-list-org-title { font-size: 12pt; /* [cite: 93] */ }
        .movie-list-poster {
            width: 100px; /* [cite: 94] */ /* Adjusted for person movie list, can be different for person images */
            border-radius: 5px;
            margin-bottom: 5px;
            transition: transform 0.2s ease-in-out;
        }
        .movie-list-poster:hover { transform: scale(1.05); /* [cite: 95] */ }
        .movie-list-main-div { display: flex; flex-wrap: wrap; flex-direction: row; /* [cite: 96] */ }
        .movie-list-item-div {
            width: 150px; /* [cite: 97] */ /* For items in tabs */
            text-align: center;
            margin-top: 15px;
            margin: 15px; /* As in Filmler.txt tabs [cite: 116] */
        }
        .main-imdb-score { font-size: 16pt; font-weight: bold; /* [cite: 98] */ }
        .main-imdb-vote { font-size: 10pt; /* [cite: 99] */ }
        .rate-box-div { margin-top: 10px; margin-bottom: 20px; width: -webkit-fit-content; text-align: center; /* [cite: 100] */ }
        .rate-box-td { vertical-align: top; /* [cite: 101] */ }
        .font-bold { font-weight: bold; /* [cite: 102] */ }
        .margin-bt-10 { margin-bottom: 10px; /* [cite: 103] */ }
        .margin-bt-20 { margin-bottom: 20px; /* [cite: 104] */ }

        /* --- Sekme Menüsü Stilleri (from Filmler.txt) --- */
        .tabs-container { width: 100%; margin-top: 30px; border: none; border-radius: 8px; background-color: transparent; overflow: hidden; box-shadow: none; /* [cite: 106, 107] */ }
        .tab-headers { display: flex; border-bottom: none; background-color: transparent; padding-left: 10px; /* [cite: 108] */ }
        .tab-header { padding: 15px 25px; cursor: pointer; font-weight: bold; color: #auto; transition: background-color 0.2s ease, color 0.2s ease, border-bottom 0.2s ease; user-select: none; font-size: 14pt; background-color: transparent; /* [cite: 109, 110] */ }
        .tab-header:hover { background-color: transparent; color: #FF7F50; /* [cite: 111] */ }
        .tab-header.active { background-color: transparent; color: #FF7F50; border-bottom: 5px solid #FF7F50; /* [cite: 112] */ }
        .tab-content-wrapper { padding: 20px; background-color: transparent; /* [cite: 113] */ }
        .tab-content { display: none; /* [cite: 114] */ }
        .tab-content.active { display: block; /* [cite: 115] */ }
        .tab-content .movie-list-poster { /* For images inside tabs */
            width: 100%; /* [cite: 117] */
            height: auto;
            border-radius: 5px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            cursor: pointer; /* [cite: 118] */
        }
        .backdrop-image { /* For person images, to control height better */
            width: 100%; /* [cite: 119] */
            max-height: 225px; /* Adjusted from 150px for potentially taller profile images */
            object-fit: cover; /* [cite: 119] */
        }
        .no-content-message { color: #888; font-style: italic; padding: 20px; text-align: center; /* [cite: 120] */ }

        /* --- Zoom Modal Stilleri (from Filmler.txt) --- */
        .zoom-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.8); display: flex; justify-content: center; align-items: center; z-index: 1000; opacity: 0; visibility: hidden; transition: opacity 0.3s ease, visibility 0.3s ease; /* [cite: 121, 122, 123] */ }
        .zoom-modal-overlay.visible { opacity: 1; visibility: visible; /* [cite: 124] */ }
        .zoom-modal-content { position: relative; max-width: 70vw; max-height: 70vh; display: flex; justify-content: center; align-items: center; background-color: transparent; transform: scale(0.8); transition: transform 0.3s ease; /* [cite: 125, 126] */ }
        .zoom-modal-overlay.visible .zoom-modal-content { transform: scale(1); /* [cite: 127] */ }
        .zoomed-image { max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 8px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5); /* [cite: 128, 129] */ }
        .zoom-close-button { position: absolute; top: 15px; right: 20px; font-size: 30px; color: #fff; cursor: pointer; z-index: 1001; background: none; border: none; padding: 0; line-height: 1; text-shadow: 0 0 5px rgba(0,0,0,0.5); /* [cite: 130, 131] */ }
        .zoom-nav-button { position: absolute; top: 50%; transform: translateY(-50%); font-size: 50px; color: #fff; cursor: pointer; z-index: 1001; background: none; border: none; padding: 10px; text-shadow: 0 0 10px rgba(0,0,0,0.7); transition: color 0.2s ease; /* [cite: 132, 133] */ }
        .zoom-nav-button:hover { color: #FF7F50; /* [cite: 134] */ }
        .zoom-nav-button.left { left: 20px; /* [cite: 135] */ }
        .zoom-nav-button.right { right: 20px; /* [cite: 136] */ }
        .zoom-image-counter { position: absolute; bottom: 20px; color: #fff; font-size: 18px; text-shadow: 0 0 5px rgba(0,0,0,0.5); z-index: 1001; /* [cite: 137, 138] */ }

    </style>
</head>
  <body>
    <div id="app">
        <div class="top-controls" v-if="viewType == 'person'">
            <div class="search-container">
                <button @click="togglePersonSearch()" class="control-button" style="background-color: #007bff;">
                    <i class="fa fa-search"></i>
                </button>
                <div :class="{'expanded': isPersonSearchVisible}" class="search-input-wrapper">
                    <input type="text" v-model="personSearchQuery" @input="searchPersons" 
                           placeholder="Sanatçı ara (Ad veya IMDb ID ör: nm...)" 
                           class="search-input" ref="personSearchInput"/>
                </div>
                <div v-if="isPersonSearchVisible && personSearchResults.length > 0" class="search-results-dropdown">
                    <div v-for="person in personSearchResults" :key="person.id" @click="selectPersonSearchResult(person)" class="search-result-item">
                        <img :src="retrieve(person.profile_path, 'tmdb-poster-detail-min')" class="search-result-poster"/>
                        <div>
                            <div class="search-result-title">{{ person.name }}</div>
                            <div class="search-result-original-title" v-if="person.known_for_department">{{ retrieve(person.known_for_department, 'person-type') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <button @click="loadRandomPerson()" :disabled="randomPersonLoading" class="control-button">
                <i class="fa fa-refresh"></i>
            </button>
        </div>

      <div v-if="viewType == 'person'" class="main-card" id="sinetayfa-mainRow"> <table>
          <tr class="main-poster-tr"> <td class="main-poster-td"> <img :src="personData.profile_path" class="main-poster"/> <img v-if="profile.loginId" :src="basicListData[getFavoriteListId()]?getBaseUrl()+'images/favori_aktif.svg':getBaseUrl()+'images/favori_pasif.svg'" class="person-favorite-ico" @click="favorite(getFavoriteListId())"/> </td>
            <td class="main-info-td">
              <div class="main-name">{{personData.name}}</div> <div class="main-job margin-bt-20">{{personData.type}}</div> <div class="person-birth-day"><span>{{personData.birthday}}</span></div> <div class="person-birth-place margin-bt-10"><span>{{personData.place_of_birth}}</span></div> </td>
            <td rowspan="2" class="vertical-seperator-td">
              <div class="vertical-seperator"></div>
            </td>
            <td rowspan="2" class="overview-td">
              <div class="overview-header">Biyografi:</div> <div class="main-default">{{personData.biography}}</div> </td>
          </tr>
          <tr>
            <td colspan="2" class="rate-box-td"> <div v-if="profile.loginId" class="rate-box-div"> <div class="rate-box-img-div">
                  <img class="rate-star" :src="rateBoxStarUrl(1)" @mousemove="rateBoxMouseMove($event,1)" @mouseleave="rateBox.hoverScore = rateBox.score" @click="rateBoxClick()"/> <img class="rate-star" :src="rateBoxStarUrl(2)"
@mousemove="rateBoxMouseMove($event,2)" @mouseleave="rateBox.hoverScore = rateBox.score" @click="rateBoxClick()"/> <img class="rate-star" :src="rateBoxStarUrl(3)" @mousemove="rateBoxMouseMove($event,3)" @mouseleave="rateBox.hoverScore = rateBox.score"
@click="rateBoxClick()"/> <img class="rate-star" :src="rateBoxStarUrl(4)" @mousemove="rateBoxMouseMove($event,4)" @mouseleave="rateBox.hoverScore = rateBox.score" @click="rateBoxClick()"/> <img class="rate-star" :src="rateBoxStarUrl(5)"
@mousemove="rateBoxMouseMove($event,5)" @mouseleave="rateBox.hoverScore = rateBox.score" @click="rateBoxClick()"/></div>
                <div>
                  <span class="main-default">SİNETAYFA puanı:</span><span class="main-imdb-score">&nbsp;{{rateBox.forumScore}}&nbsp;</span><span class="main-imdb-vote">( {{rateBox.forumVote}}&nbsp;oy)</span> </div>
              </div>
              <div v-else>&nbsp;</div> </td>
          </tr>
        </table>

        <div class="tabs-container" v-if="viewType == 'person'">
            <div class="tab-headers">
                <div class="tab-header active" data-tab="person-movies-tab">Filmleri</div>
                <div class="tab-header" data-tab="person-images-tab">Görseller</div>
            </div>
            <div class="tab-content-wrapper">
                <div class="tab-content active" id="person-movies-tab">
                    <div class="movie-list-main-div" v-if="personData.movieList && personData.movieList.length > 0">
                        <div class="movie-list-item-div" v-for="(movie, index) in personData.movieList" :key="'person-movie-' + index"> <div>
                                <a :href="subPath+'/app.php/filmler?detail=1&viewType=movie&id='+movie.id" target="_parent"><img :src="movie.poster" class="movie-list-poster"/></a> </div>
                            <div>
                                <span class="movie-list-title">{{movie.original_title}}</span> </div>
                            <div v-if="movie.title != movie.original_title">
                                <span class="movie-list-org-title">({{movie.title}})</span> </div>
                        </div>
                    </div>
                    <div v-else class="no-content-message">Bu sanatçıya ait film bilgisi bulunamadı.</div>
                </div>

                <div class="tab-content" id="person-images-tab">
                    <div class="movie-list-main-div" v-if="personData.images && personData.images.length > 0">
                        <div class="movie-list-item-div" v-for="(imageSrc, index) in personData.images" :key="'person-image-' + index">
                            <div>
                                <img :src="imageSrc" class="movie-list-poster backdrop-image" @click="openZoomModal(imageSrc, personData.images, index)"/>
                            </div>
                        </div>
                    </div>
                    <div v-if="!personData.images || personData.images.length === 0" class="no-content-message">Sanatçıya ait görsel bulunamadı.</div>
                </div>
            </div>
        </div>
      </div>

      <div class="zoom-modal-overlay" :class="{ 'visible': isZoomModalVisible }" @click.self="closeZoomModal()"> <div class="zoom-modal-content"> <button class="zoom-close-button" @click="closeZoomModal()">&times;</button> <img :src="currentZoomedImageSrc" class="zoomed-image"/> <button class="zoom-nav-button left" @click="navigateZoomedImage(-1)">&#10094;</button> <button class="zoom-nav-button right" @click="navigateZoomedImage(1)">&#10095;</button> <div class="zoom-image-counter" v-if="currentZoomedImageList.length > 0"> {{ currentZoomedImageIndex + 1 }} / {{ currentZoomedImageList.length }}
              </div>
          </div>
      </div>
	  </div>

    <div id="customAlertOverlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); display: flex; justify-content: center; align-items: center; z-index: 1000; opacity: 0; visibility: hidden; transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;"> <div id="customAlertBox" style="background-color: white; padding: 30px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2); text-align: center; max-width: 400px; width: 90%; transform: scale(0.9); transition: transform 0.3s ease-in-out;"> <h2 style="color: #dc2626; font-size: 1.5rem; font-weight: bold; margin-bottom: 1rem;">Uyarı!</h2> <p style="color: #374151; margin-bottom: 1.5rem;">Geliştirici araçlarını açtınız. Lütfen bu sayfada değişiklik yapmamaya özen gösterin.</p> <button id="customAlertCloseBtn" style="background-color: #ef4444; color: white; font-weight: bold; padding: 0.5rem 1.5rem; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: none; cursor: pointer;">Kapat</button> </div>
    </div>

    <script src="../imdb/scripts/app.js"></script>
    <script src="../imdb/scripts/veri_koruma.js"></script>
  </body>
</html>