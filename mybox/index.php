<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Training VueJs Test</title>
    <script src="scripts/axios.js"></script>
	<script src="scripts/vue.global.js"></script>
	<link type="text/css" rel="stylesheet" href="css/bootstrap.min.css"/>
    <link type="text/css" rel="stylesheet" href="css/style.css"/>
    <link rel="shortcut icon" href="#">
</head>
  <body>
    
    <div id="app">
		<div v-if="!profile.userId">
			<span class="font-bold">
				<h5>Bu sayfa üyelere özeldir, lütfen giriş yapınız.</h5>				
			</span>
		</div>
		<div v-else class="container" style="margin-left: 0px; margin-right: 0px;">
			<div v-if="profile.guestMode" class="btn-exit-profile" :style="{'left':(mainRowWidth-105) +'px'}">
				<button class="button-4"  @click="homePage">Geri dön</button>
			</div>
			<div class="row list-card user-card" :style="{'width':(mainRowWidth-30) +'px'}">
				<div class="col-12 col-md-6" >
					
					<table class="word-break" style="font-size: 12px; margin-left: -15px;">
						<tr>
							<td rowspan="4" style="vertical-align: top;">
								<img src="images/avatar.jpg" class="user-avatar"/>								
							</td>
							<td rowspan="4">
								<span style="display: block; width: 10px;">&nbsp;</span>
							</td>
							<td>
								<span class="profileHeader">dragon</span>
							</td>
						</tr>
						<tr>
							<td>
								<span>
									<span class="font-bold">E-posta : </span><span>dragon@sinokur.com</span>
								</span>
						</tr>
						<tr>
							<td>
								<span class="font-bold">Web sitesi : </span><span>www.iucoders.com</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="font-bold">Letterboxd : </span><span>https://letterboxd.com/dragon</span>
							</td>
						</tr>										
						<tr>
							<td colspan="2">
								<div v-if="!profile.guestMode" class="margin-t10 margin-b5">
									<img :src="allowOtherUser?'images/selected.svg':'images/unselected.svg'" @click="changePermission" class="pointer margin-r5"/>
									<span class="checkboxSpan">Arkadaşlarım Sayfamı Görüntüleyebilsin</span>
								</div>
							</td>
						</tr>
					</table>
					
			  	</div>
				<div v-if="!profile.guestMode" class="col-12 col-md-6">
					<div class="margin-t10">
						<span class="profileHeader">Arkadaş Listesi (rumuz - son ziyaret tarihi)</span>
					</div>
					<div class="friend-table-div">
						<table class="friend-table">
							<tr v-for="(friend, index) in friendList">
								<td>
									<span class="font-bold"> {{retrieve(friend.username)}}</span>
								</td>
								<td style="text-align: right;">
									<span>{{retrieve(friend.user_lastvisit, 'timestamp')}}</span>		
									<img src="images/preview.svg" @click="viewProfile(friend.user_id)"/>								
								</td>
							</tr>
						</table>
					</div>				
				</div>			  
			</div>
			
			<div class="row" id="mainRow">			


				<div class="col-12 col-md-6" v-for="(listKey, index) in Object.keys(userListInfo)">
					<div class="list-card">		
						<div class="margin-l10 margin-t5">
							<img src="images/folder.svg" class="margin-r5" style="width: 30px;"/>
							<span class="listHeader">{{userListInfo[listKey].header}}</span>	
							<span v-if="userList[listKey].length > 0" class="listHeaderSearch"><input class="input-search" type="text" v-model="userListFilter[listKey]"/></span>							
						</div>	
						<div v-if="userList[listKey].length == 0" class="cardInfo">
							Henüz listenizde film bulunmuyor, lütfen ekleyiniz.
						</div>	
						<div v-if="userList[listKey].length > 0">
							<table class="margin-t5">
								<template v-for="(movie, index) in getFilteredList(listKey)">
									<tr>									
										<td style="padding: 5px;">
											<img :src="retrieve(movie.Poster, 'poster')" style="width: 70px;"/>											
										</td>
										<td>
											<table>
												<tr>
													<td colspan="3">
														<img v-if="!profile.guestMode" class="btn-delete-movie" src="images/delete.svg" @click="removeMovie(listKey,movie.imdbID)"/> <span class="font-bold">{{retrieve(movie.Title)}}</span>
													</td>
												</tr>
												<tr>
													<td style="text-align: center;">
														{{retrieve(movie.Year)}}
													</td>
													<td>
														&nbsp;-&nbsp;
													</td>
													<td>
														{{retrieve(movie.Runtime,'runtime')}}
													</td>
												</tr>
												<tr>	
													<td>
														<span class="imdb-label">
															<a :href="'https://www.imdb.com/title/' + movie.imdbID +'/'" target="_blank">&nbsp;IMDb&nbsp;</a>
														</span>
													</td>	
													<td>
														&nbsp;:&nbsp;
													</td>										
													<td>
														<span class="font-bold">{{retrieve(movie.imdbRating)}}</span><span>{{'/10 (' + retrieve(movie.imdbVotes) + ' oy)'}}</span>
													</td>
												</tr>
												<tr>
													<td colspan="3" v-if="!movie.userScore" @click="showRateBox(movie)">
														<span v-if="!profile.guestMode" class="your-rate-label">
															<img src="images/blank_star.svg" style="padding-bottom: 5px; width: 30px;">
															Puanla&nbsp;													
														</span>
														<span v-if="movie.forumScore">
															<span>&nbsp;Forum&nbsp;:&nbsp;</span>
															<span class="font-bold">{{retrieve(movie.forumScore)}}</span>
															<span>{{'/10 (' + retrieve(movie.forumVoteCount) + ' oy)'}}</span>
														</span>
													</td>
													<td colspan="3" v-if="movie.userScore" @click="showRateBox(movie)">
														<span v-if="!profile.guestMode" class="your-rate-label">
															<img src="images/filled_star.svg" style="padding-bottom: 5px; width: 30px;">
															<span>{{retrieve(movie.userScore) +'/10'}}</span>																			
														</span>
														<span v-if="movie.forumScore">
															<span>&nbsp;Forum&nbsp;:&nbsp;</span>
															<span class="font-bold">{{retrieve(movie.forumScore)}}</span>
															<span>{{'/10 (' + retrieve(movie.forumVoteCount) + ' oy)'}}</span>
														</span>
													</td>
													
												</tr>											
											</table>
										</td>									
									</tr>
									<tr v-if="index != userList[listKey].length-1">
										<td colspan="3">
											<div class="seperator"></div>
										</td>
									</tr>
								</template>
							</table>
						</div>					
					</div>
					<div v-if="!profile.guestMode" class="cardAddBtn">
						<button class="button-3"  @click="showSearchBox(listKey)">+ Ekle</button>
					</div>
				</div>



			</div>




			<div class="row">
				<div class="col-12">


				</div>
			</div>
		</div>


		


		
		


	




		<!-- The Modal -->
		<div id="searchModal" class="modal" v-if="searchBox.visible" style="width: 100% !important;">
			<!-- Modal content -->
			<div class="modal-content" :style="{'width':(mainRowWidth*(0.80))+'px','margin-left': (mainRowWidth*(0.10))+'px'}">
				<div>
					<span @click="searchBox.searchType='title'; searchBox.showList=false" style="padding: 5px; cursor: pointer;" :style="{'border':searchBox.searchType=='title'?'1px solid #3462a0':'none', 'background':searchBox.searchType=='title'?'#3462a0':'white', 'color':searchBox.searchType=='title'?'white':'#3462a0'}">Film Adı</span>
					<span @click="searchBox.searchType='imdbid'; searchBox.showList=false" style="padding: 5px; cursor: pointer; margin-left: 5px;" :style="{'border':searchBox.searchType!='title'?'1px solid #3462a0':'none', 'background':searchBox.searchType!='title'?'#3462a0':'white', 'color':searchBox.searchType!='title'?'white':'#3462a0'}">IMDb ID</span>
					<span class="close-btn" @click="closeSearchBox">
						<img src="images/close.svg" style="width: 25px;"/> 
					</span>
				</div>
				<div>
					<table style="width:100%">
						<tr>
							<td><input type="text" v-on:keyup.enter="searchBox.searchType=='title'?searchMovie():addMovie(searchBox.movieName)" v-model="searchBox.movieName" placeholder="Lütfen aramak istdiğiniz film adını girin"></input></td>
							<td><button class="button-3" @click="searchBox.searchType=='title'?searchMovie():addMovie(searchBox.movieName)">{{searchBox.searchType=='title'?'Film Ara':'Film Ekle'}}</button></td>
						</tr>
					</table>
				</div>
				<div style="max-height: 500px; overflow: auto;" v-if="searchBox.showList">
					<table>
						<template v-for="(movie, index) in searchBox.movieList">
							<tr>							
								<td style="padding: 5px;">
									<img :src="retrieve(movie.Poster, 'poster')" style="width: 80px;"/> 									
								</td>
								<td style="vertical-align: top;">
									<table>
										<tr>							
											<td class="searchBoxTHeader">{{retrieve(movie.Title)}}</td>
										</tr>
										<tr v-if="movie.Type">									
											<td>{{(movie.Type == 'movie' ? 'Film' : 'Dizi') + ' ( ' + retrieve(movie.Year) + ' )'}}</td>						
										</tr>									
										<tr>
											<td>			
												<button v-if="!searchBox.movieIdData[movie.imdbID]" class="button-3" @click="addMovie(movie.imdbID)">Ekle</button>	
												<button v-if="searchBox.movieIdData[movie.imdbID]" class="button-4" @click="removeMovie(searchBox.listType,movie.imdbID)">Çıkar</button>	
											</td>										
										</tr>									
									</table>
								</td>					
							</tr>
							<tr v-if="index != searchBox.movieList.length-1">
								<td colspan="2">
									<div class="seperator"></div>
								</td>
							</tr>
						</template>
					</table>
				</div>
			</div>
		</div>

		<!-- The Modal -->
		<div id="alertModal" class="modal" v-if="alertBox.visible">
			<!-- Modal content -->
			<div class="modal-content" :style="{'width':(mainRowWidth*(0.60))+'px','margin-left': (mainRowWidth*(0.20))+'px'}">	
				<div style="width: 100%; text-align: center; color: darkred;">
					<h5>{{alertBox.message}}</h5>
				</div>
				<div style="width: 100%; text-align: center; margin-top: 20px;">
					<button class="button-3"  @click="hideModal(alertBox)">Tamam</button>
				</div>
			</div>
		</div>

		<!-- The Modal -->
		<div id="loadingModal" class="modal" v-if="loadingBox.visible">
			<!-- Modal content -->
			<div class="modal-content" style="border:none; background-color: rgba(0,0,0,0);"  :style="{'width':(mainRowWidth*(0.80))+'px','margin-left': (mainRowWidth*(0.10))+'px'}">		
				<div style="text-align: center;">
					<img src="images/loading.gif" style="width: 50px;"/>
				</div>
			</div>
		</div>

		<!-- The Modal -->
		<div id="rateModal" class="modal" v-if="rateBox.visible">
			<!-- Modal content -->
			<div class="modal-content"  :style="{'width':'300px','margin-left': ((mainRowWidth-300)*(0.5))+'px'}">		
				<div>
					<span class="close-btn" @click="hideRateBox">
						<img src="images/close.svg" style="width: 25px;"/> 
					</span>
				</div>
				<div class="rate-box-title">
					<span>{{retrieve(rateBox.movie.Title)}}</span>
				</div>
				<div class="rate-box-stars">
					<img class="pointer" :src="rateBox.hoverScore > 0?'images/filled_star.svg':'images/blank_star.svg'" @mouseover="rateBox.hoverScore = 1" @mouseleave="rateBox.hoverScore = rateBox.score" @click="rateBox.score=1"/>
					<img class="pointer" :src="rateBox.hoverScore > 1?'images/filled_star.svg':'images/blank_star.svg'" @mouseover="rateBox.hoverScore = 2" @mouseleave="rateBox.hoverScore = rateBox.score" @click="rateBox.score=2"/>
					<img class="pointer" :src="rateBox.hoverScore > 2?'images/filled_star.svg':'images/blank_star.svg'" @mouseover="rateBox.hoverScore = 3" @mouseleave="rateBox.hoverScore = rateBox.score" @click="rateBox.score=3"/>
					<img class="pointer" :src="rateBox.hoverScore > 3?'images/filled_star.svg':'images/blank_star.svg'" @mouseover="rateBox.hoverScore = 4" @mouseleave="rateBox.hoverScore = rateBox.score" @click="rateBox.score=4"/>
					<img class="pointer" :src="rateBox.hoverScore > 4?'images/filled_star.svg':'images/blank_star.svg'" @mouseover="rateBox.hoverScore = 5" @mouseleave="rateBox.hoverScore = rateBox.score" @click="rateBox.score=5"/>
					<img class="pointer" :src="rateBox.hoverScore > 5?'images/filled_star.svg':'images/blank_star.svg'" @mouseover="rateBox.hoverScore = 6" @mouseleave="rateBox.hoverScore = rateBox.score" @click="rateBox.score=6"/>
					<img class="pointer" :src="rateBox.hoverScore > 6?'images/filled_star.svg':'images/blank_star.svg'" @mouseover="rateBox.hoverScore = 7" @mouseleave="rateBox.hoverScore = rateBox.score" @click="rateBox.score=7"/>
					<img class="pointer" :src="rateBox.hoverScore > 7?'images/filled_star.svg':'images/blank_star.svg'" @mouseover="rateBox.hoverScore = 8" @mouseleave="rateBox.hoverScore = rateBox.score" @click="rateBox.score=8"/>
					<img class="pointer" :src="rateBox.hoverScore > 8?'images/filled_star.svg':'images/blank_star.svg'" @mouseover="rateBox.hoverScore = 9" @mouseleave="rateBox.hoverScore = rateBox.score" @click="rateBox.score=9"/>
					<img class="pointer" :src="rateBox.hoverScore > 9?'images/filled_star.svg':'images/blank_star.svg'" @mouseover="rateBox.hoverScore = 10" @mouseleave="rateBox.hoverScore = rateBox.score" @click="rateBox.score=10"/>
				</div>
				<div style="width: 100%; text-align: center; margin-top: 20px;">
					<span>{{'(' + retrieve(rateBox.hoverScore) + '/10)'}}&nbsp;&nbsp;&nbsp;</span> <button class="button-3" :class="{disable:rateBox.score==0}"  @click="rateMovie">Puanla</button>
				</div>				
			</div>
		</div>
	
    </div>
	

</div>

    <script src="scripts/app.js"></script>

  </body>
</html>