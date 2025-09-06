<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>IMDb Listeler</title>
    <script src="scripts/axios.js"></script>
	  <script src="scripts/vue.global.js"></script>
    <link type="text/css" rel="stylesheet" href="css/styleList.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"/>
    <link rel="shortcut icon" href="#">
</head>
  <body>
    
    <div id="app">


    <div class="toolbar-div" id="sineokur-mainRow">
      <div>
        <div v-if="!profile.guestMode" class="create-list-button pointer" @click="createList()">
          YENİ LİSTE +
        </div>
        <span class="toolbar-buttons">
          <img @click="editList()" v-if="selectedListIndex>7 && !profile.guestMode" class="img-edit-list pointer" :class="{'disabled':!(selectedListIndex>7)}" :src="getBaseUrl()+'images/duzenle.svg'"/>
          <!--img @click="selectIMDbList()" v-if="!(selectedListIndex&lt;8 && selectedListIndex>5) && !profile.guestMode" class="pointer" :src="getBaseUrl()+'images/Resim5.svg'"/-->
          <img @click="showSearchBox()" v-if="!(selectedListIndex&lt;8 && selectedListIndex>5) && !profile.guestMode" class="pointer" :src="getBaseUrl()+'images/Resim5.svg'"/>
          <img @click="deleteList()" v-if="selectedListIndex>7 && !profile.guestMode" class="img-list-delete pointer" :src="getBaseUrl()+'images/Resim6.svg'"/>
        </span>
        
        <select v-model="selectedListIndex" @change="changeList($event)" class="toolbar-select-list">
          <option v-for="(listData, index) in userListInfo" :value="index">{{listData.header + ' (' + listData.count + ')'}}</option>
        </select>
      </div>
     
        

    </div>
    <div class="toolbar-sort-div">
      <div class="toolbar-sort" v-if="userListDataInfo.length>0">
        <select v-model="sortItem.selected" @change="sortList($event)">
          <option value="createDateDesc">Ekleme Tarihine Göre Sırala (Azalan)</option>
          <option value="createDateAsc">Ekleme Tarihine Göre Sırala (Artan)</option>
          <option v-if="selectedListIndex &lt; 6 || selectedListIndex>7" value="trMovieNameDesc">Türkçe Film Adına Göre (Azalan)</option>
          <option v-if="selectedListIndex &lt; 6 || selectedListIndex>7" value="trMovieNameAsc">Türkçe Film Adına Göre (Artan)</option>
          <option v-if="selectedListIndex &lt; 6 || selectedListIndex>7" value="movieNameDesc">Orjinal Film Adına Göre (Azalan)</option>
          <option v-if="selectedListIndex &lt; 6 || selectedListIndex>7" value="movieNameAsc">Orjinal Film Adına Göre (Artan)</option>
          <option v-if="selectedListIndex &lt; 6 || selectedListIndex>7" value="directorDesc">Yönetmen Adına Göre (Azalan)</option>
          <option v-if="selectedListIndex &lt; 6 || selectedListIndex>7" value="directorAsc">Yönetmen Adına Göre (Artan)</option>
          <option v-if="selectedListIndex &lt; 6 || selectedListIndex>7" value="sinetayfaScoreDesc">Sinetayfa Puanına Göre (Azalan)</option>
          <option v-if="selectedListIndex &lt; 6 || selectedListIndex>7" value="sinetayfaScoreAsc">Sinetayfa Puanına Göre (Artan)</option>
          <option v-if="selectedListIndex == 6" value="directorNameDesc">Yönetmen Adına Göre (Azalan)</option>
          <option v-if="selectedListIndex == 6" value="directorNameAsc">Yönetmen Adına Göre (Artan)</option>
          <option v-if="selectedListIndex == 7" value="personNameDesc">Oyuncu Adına Göre (Azalan)</option>
          <option v-if="selectedListIndex == 7" value="personNameAsc">Oyuncu Adına Göre (Azalan)</option>
        </select>
      </div>


      <div class="pagination-div" style="float: none;">
        <span v-if="pageItem.pageIndex>2" @click="selectPage('first')" class="pagination-span">&lt;&lt;</span>
        <span v-if="pageItem.pageIndex>1" @click="selectPage('previous')" class="pagination-span">&lt;</span>
        <span @click="selectPage(pageNumber)" class="pagination-span" :class="{'page-selected':pageItem.pageIndex == pageNumber}" v-for="(pageNumber, index) in pageItem.pageNumbers"> {{pageNumber}}</span>
        <span v-if="pageItem.pageIndex&lt;pageItem.pageCount" @click="selectPage('next')" class="pagination-span">></span>
        <span v-if="pageItem.pageIndex&lt;pageItem.pageCount-1" @click="selectPage('last')" class="pagination-span">>></span>
      </div>
    </div>
    <div class="flex-wrap" style="margin-top: 10px;">
      <template v-for="(data, index) in userListDataInfo">
        <div v-if="data.imdbType == 'person'" class="person-card-xs">
          <div class="person-poster-div">
            <a :href="subPath+'/app.php/sanatcilar?detail=1&id='+data.imdbId" target="_parent"><img :src="data.profilePath" class="person-poster"/></a>
            <img v-if="data.userRating" class="movie-score-img" :src="getBaseUrl()+'images/' + parseInt(data.userRating) + '.svg'">
            <img class="person-remove-list-xs pointer" :src="getBaseUrl()+'images/cikar.svg'" @click="removeFromUserList(data.listId, data.imdbId)" v-if="!profile.guestMode">
          </div>
          <div class="inner-person-card">
            <div class="person-name font-bold">{{trim(data.name,27)}}</div>
            <div class="person-job">{{data.personType}}</div> 
            <div class="person-birth"><span class="font-bold">Doğum Günü </span><span>{{trim(data.birthday,41)}}</span></div>
            <div class="person-birth"><span class="font-bold">Doğum Yeri </span><span>{{trim(data.placeOfBirth,41)}}</span></div>
          </div>
        </div>
        <div v-if="data.imdbType == 'movie'" class="movie-card-xs">
          <div class="movie-poster-div">
            <a :href="subPath+'/app.php/filmler?detail=1&id='+data.imdbId" target="_parent"><img :src="data.poster" class="movie-poster"/></a>
            <img v-if="data.userRating" class="movie-score-img" :src="getBaseUrl()+'images/' + parseInt(data.userRating) + '.svg'">
            <img class="movie-remove-list-xs pointer" :src="getBaseUrl()+'images/cikar.svg'" @click="removeFromUserList(data.listId, data.imdbId)" v-if="!profile.guestMode">
          </div>
          <div class="inner-movie-card">
            <div v-if="data.titleTr" class="movie-title-tr font-bold">{{trim(data.titleTr,27)}}</div>
            <div class="movie-title margin-b10">{{trim(data.title,37)}}</div> 
            <div v-if="data.director" class="movie-director"><span class="font-bold">Yönetmen </span><span>{{trim(data.director,47)}}</span></div>
          </div>
        </div>

      </template>  
    </div>
    <div v-if="pageItem.pageNumbers.length>1 && userListDataInfo.length>0" class="pagination-div" style="margin-top:15px">
      <span v-if="pageItem.pageIndex>2" @click="selectPage('first')" class="pagination-span">&lt;&lt;</span>
      <span v-if="pageItem.pageIndex>1" @click="selectPage('previous')" class="pagination-span">&lt;</span>
      <span @click="selectPage(pageNumber)" class="pagination-span" :class="{'page-selected':pageItem.pageIndex == pageNumber}" v-for="(pageNumber, index) in pageItem.pageNumbers"> {{pageNumber}}</span>
      <span v-if="pageItem.pageIndex&lt;pageItem.pageCount" @click="selectPage('next')" class="pagination-span">></span>
      <span v-if="pageItem.pageIndex&lt;pageItem.pageCount-1" @click="selectPage('last')" class="pagination-span">>></span>
    </div>








    <div id="searchBox" class="modal" v-if="searchBox.visible">
      <div class="modal-content" :class="{'height-150':searchBox.viewType == 1 || searchBox.viewType == 2}" :style="{'width':searchBox.width+'px','margin-left': ((screenWidth-searchBox.width)/2)+'px'}">
        <div class="searchbox-header">
          <span>Film & Dizi Ara</span>
        </div>
        <div class="searchbox-radio-div">
          <span class="searchBox-radio-label-l">
            <span @click="selectRadio('withName')" class="searchBox-radio" :style="{'background':searchBox.radioCheck=='withName'?'#FF7F50':'#F8F6F2'}"></span>
            <span>Film/Dizi İsmi ile</span>
          </span>
          <span class="searchBox-radio-label-r">
            <span @click="selectRadio('withId')" class="searchBox-radio" :style="{'background':searchBox.radioCheck=='withId'?'#FF7F50':'#F8F6F2'}"></span>
            <span>IMDb ID ile</span>
          </span>
        </div>
        <div v-if="searchBox.viewType == 1 || searchBox.viewType == 3">
          <img class="searchBox-search-ico" :style="{'margin-left':(searchBox.width-40) + 'px'}" :src="getBaseUrl()+'images/ara.svg'" @click="searchMovie()">
          <input @click="searchBox.viewType=searchBox.searchedMovies.length>0?2:searchBox.viewType" class="input-3 searchBox-input"  :style="{'width':(searchBox.width-70) + 'px'}" type="text" v-on:keyup.enter="searchMovie()" v-model="searchBox.inputText">
        </div>
        <div v-if="searchBox.viewType == 2"> 
          <img class="searchBox-search-ico" :style="{'margin-left':(searchBox.width-40) + 'px'}" :src="getBaseUrl()+'images/ara.svg'" @click="searchMovie()">
          <input class="input-3 searchBox-input"  :style="{'width':(searchBox.width-70) + 'px'}" type="text" v-on:keyup.enter="searchMovie()" v-model="searchBox.inputText">
          <div class="searchBox-result-div searchBox-input" :style="{'width':(searchBox.width-55) + 'px'}">
            <div class="searchBox-result-overflow-panel">
              <template v-for="(data, index) in searchBox.searchedMovies">
                <div class="movie-card margin-l20" >
                  <div class="movie-poster-div">
                    <a :href="subPath+'/app.php/filmler?detail=1&id='+data.imdbId" target="_parent"><img :src="data.poster" class="movie-poster"/></a>
                    <img v-if="checkSelectedIndex(index)>-1" @click="selectMovie('remove', -1, checkSelectedIndex(index))" class="movie-remove-list pointer" :src="getBaseUrl()+'images/cikar.svg'">
                    <img v-else @click="selectMovie('add', index, -1)" class="movie-add-list pointer" :src="getBaseUrl()+'images/ekle.svg'">
                  </div>
                  <div class="inner-movie-card">
                    <div v-if="data.titleTr" class="movie-title-tr font-bold">{{trim(data.titleTr,27)}}</div>
                    <div class="movie-title margin-b10">{{trim(data.title,37)}}</div> 
                    <div v-if="data.director" class="movie-director"><span class="font-bold">Yönetmen </span><span>{{trim(data.director,47)}}</span></div>
                  </div>
                </div>
              </template>
              <div v-if="!searchBox.searchedMovies || searchBox.searchedMovies.length == 0" class="align-center margin-t10" >
                <span class="searchBox-noresult">
                  Sonuç bulunamadı
                </span>
              </div>

            </div>



            <div class="searchBox-cancel-div margin-l20">
              <button v-if="searchBox.selectedMovies.length>0" class="button-4 font-size-16 font-weight-normal"  @click="searchBox.viewType = 3">&nbsp;&nbsp;&nbsp;Geri Dön&nbsp;&nbsp;&nbsp;</button>
              <button v-else class="button-4 font-size-16 font-weight-normal"  @click="searchBox.visible = false">&nbsp;&nbsp;&nbsp;İptal&nbsp;&nbsp;&nbsp;</button>
            </div>

            
          </div>
        </div>
        <div class="searchBox-cancel-div"  v-if="searchBox.viewType == 1">
          <button class="button-4 font-size-16 font-weight-normal"  @click="searchBox.visible = false">&nbsp;&nbsp;&nbsp;İptal&nbsp;&nbsp;&nbsp;</button>
        </div>
        <div v-if="searchBox.viewType == 3">


          <template v-for="(data, index) in searchBox.selectedMovies">
            <div class="movie-card margin-l30" >
              <div class="movie-poster-div">
                <a :href="subPath+'/app.php/filmler?detail=1&id='+data.imdbId" target="_parent"><img :src="data.poster" class="movie-poster"/></a>
                <img @click="selectMovie('remove', -1, index)" class="movie-remove-list pointer" :src="getBaseUrl()+'images/cikar.svg'">
              </div>
              <div class="inner-movie-card">
                <div v-if="data.titleTr" class="movie-title-tr font-bold">{{trim(data.titleTr,27)}}</div>
                <div class="movie-title margin-b10">{{trim(data.title,37)}}</div> 
                <div v-if="data.director" class="movie-director"><span class="font-bold">Yönetmen </span><span>{{trim(data.director,47)}}</span></div>
              </div>
            </div>
          </template>
          
        </div>
        <div class="searchBox-cancel-div"  v-if="searchBox.viewType == 3">
          <button class="button-3 font-size-16 font-weight-normal margin-r10" @click="saveToUserlist()">&nbsp;&nbsp;&nbsp;Ekle({{searchBox.selectedMovies.length}}) &nbsp;&nbsp;&nbsp;</button>
          <button class="button-4 font-size-16 font-weight-normal"  @click="searchBox.visible = false">&nbsp;&nbsp;&nbsp;İptal&nbsp;&nbsp;&nbsp;</button>
        </div>
      </div>
    </div>





    <!-- The Modal -->
		<div id="alertModal" class="modal" v-if="modalBox.visible">
			<!-- Modal content -->
			<div class="modal-content" :style="{'width':modalBox.width+'px','margin-left': ((screenWidth-modalBox.width)/2)+'px'}">	
        <template v-if="modalBox.type == 'alert'">
          <div style="width: 100%; text-align: center;">
            <span class="modal-alert-msg">{{modalBox.message}}</span>
          </div>
          <div style="width: 100%; text-align: center; margin-top: 20px;">
            <button class="button-3"  @click="hideModal()">Tamam</button>
          </div>
      </template>
      <template v-if="modalBox.type == 'save-list'">
        <div style="width: 100%;">
          <span class="modal-list-header">Liste Adı:&nbsp;</span> <input class="input-3" :style="{'width':(modalBox.width-150) + 'px'}" type="text" v-on:keyup.enter="saveUserList()" v-model="modalBox.listHeader"></input>
        </div>
        <div style="width: 100%; text-align: center; margin-top: 20px;">
          <button class="button-3 margin-r10" :class="{'disabled':!(modalBox.listHeader && modalBox.listHeader.trim().length >0)}" @click="saveUserList()">{{modalBox.listId?'Güncelle':'Kaydet'}}</button>
          <button class="button-4"  @click="hideModal()">İptal</button>
        </div>
      </template>
      <template v-if="modalBox.type == 'delete-list'">
        <div style="width: 100%; text-align: center;">
          <span class="modal-alert-msg">{{modalBox.message}}</span>
        </div>
        <div style="width: 100%; text-align: center; margin-top: 20px;">
          <button class="button-3 margin-r10" @click="deleteUserList()">Sil</button>
          <button class="button-4"  @click="hideModal()">İptal</button>
        </div>
      </template>
			</div>
		</div>

		<!-- The Modal -->
		<div id="loadingModal" class="modal" v-if="loadingBox.visible">
			<!-- Modal content -->
			<div class="modal-content" style="border:none; background-color: rgba(0,0,0,0);"  :style="{'width':screenWidth+'px','margin-left':'0px'}">		
				<div style="text-align: center;">
					<img :src="getBaseUrl()+'images/loading.gif'" style="width: 50px;"/>
				</div>
			</div>
		</div>




    <!--input style="display: none;" type="file" ref="upload" id="uploadId" accept=".csv" @change="readIMDbList($event)"></a-->
	  </div>
</br>
    <script src="scripts/app_list.js"></script>
    <script src="scripts/veri_koruma.js"></script>
  </body>
</html>