const app = Vue.createApp({
  data() {
    return {
      subPath:"",//"/test"
      tmdb_api_key: "691a740114fe7dc34fbbe9f1a464cc5e",
      omdb_api_key: "68d73d3c",
      imdbID: "",
      tmdbID: "",
      viewType: "",
      movieData : { // movieData yapısı daha belirgin hale getirildi
        Title: "",
        Year: "",
        Rated: "",
        Released: "",
        Runtime: "",
        Genre: "",
        Director: "",
        Writer: "",
        Actors: "",
        Plot: "",
        Language: "",
        Country: "",
        Awards: "",
        Poster: "", // OMDb'den gelecek ana poster
        Ratings: [],
        Metascore: "",
        imdbRating: "",
        imdbVotes: "",
        imdbID: "",
        Type: "",
        DVD: "",
        BoxOffice: "",
        Production: "",
        Website: "",
        Response: "False",
        posters: [], // TMDB'den gelecek ek posterler
        backdrops: [], // TMDB'den gelecek arka planlar
        trailerUrl: null // TMDB'den gelecek fragman URL'si
      },
      personData: { // personData objesi ve içeriği
        personDetails: {},
        movieCredits: [],
        tvCredits: [],
        images: [],
        combinedCredits: [],
      },
      apiData: {},
      profile : {
        loginId: undefined,
        userId: undefined
      },
      basicListData : {},
      favoriteListCardVisible : false,
      userListInfo : {},
      userListInfoBase : {
        planned: {
          header:"İzleyeceklerim",
        }
      },
      detailMode : false,
      rateBox: {
        score: 0,
        hoverScore: 0,
        visible: false,
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
      // Yeni eklenen zoom modal verileri
      isZoomModalVisible: false,
      currentZoomedImageSrc: '',
      currentZoomedImageList: [],
      currentZoomedImageIndex: 0,
      // Yeni eklenen rastgele film ve arama verileri
      randomMovieLoading: false,
      isSearchVisible: false,
      searchQuery: '',
      searchResults: [],
      searchType: 'movie', // 'movie' veya 'person' olabilir
      // Sanatçılar sayfası için eklendi
      randomPersonLoading: false,
      isPersonSearchVisible: false,
      personSearchQuery: '',
      personSearchResults: [],
      lastSearchQuery: '', // Son yapılan arama sorgusunu tutar
      currentSearchType: 'person',

    };
  },
  methods: {
    init() {
      // url'deki parametreleri ayrıştır
      const params = new URLSearchParams(window.location.search);
      this.viewType = params.get('view') || 'home';
      this.imdbID = params.get('imdbID');
      this.tmdbID = params.get('tmdbID');

      if (this.viewType === 'detail' && this.imdbID) {
        this.fetchMovieDetail(this.imdbID);
      } else if (this.viewType === 'personDetail' && this.tmdbID) { // personDetail görünümü eklendi
        this.fetchPersonDetail(this.tmdbID);
      }
      this.setupEventListeners();
    },

    setupEventListeners() {
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          this.closeZoomModal();
          this.closeVideoBox(); // Video kutusunu da kapat
        }
      });
    },

    // Yeni eklenen zoom modal metodları
    openZoomModal(imageUrl, imageList) {
      this.currentZoomedImageSrc = imageUrl;
      this.currentZoomedImageList = imageList;
      this.currentZoomedImageIndex = imageList.indexOf(imageUrl);
      this.isZoomModalVisible = true;
    },

    closeZoomModal() {
      this.isZoomModalVisible = false;
      this.currentZoomedImageSrc = '';
      this.currentZoomedImageList = [];
      this.currentZoomedImageIndex = 0;
    },

    navigateZoomedImage(direction) {
      const newIndex = this.currentZoomedImageIndex + direction;
      if (newIndex >= 0 && newIndex < this.currentZoomedImageList.length) {
        this.currentZoomedImageIndex = newIndex;
        this.currentZoomedImageSrc = this.currentZoomedImageList[newIndex];
      }
    },

    // Rastgele Film getirme
    async fetchRandomMovie() {
      this.randomMovieLoading = true;
      try {
        let randomMovieId = await this.getRandomMovieId();
        if (randomMovieId) {
          window.location.href = `${this.subPath}detail.html?view=detail&imdbID=${randomMovieId}`;
        } else {
          this.showAlert("Rastgele film bulunamadı. Lütfen tekrar deneyin.");
        }
      } catch (error) {
        console.error("Rastgele film getirilirken hata oluştu:", error);
        this.showAlert("Rastgele film getirilirken bir hata oluştu.");
      } finally {
        this.randomMovieLoading = false;
      }
    },

    async getRandomMovieId() {
      const page = Math.floor(Math.random() * 500) + 1; // İlk 500 sayfadan rastgele bir sayfa seç
      const url = `https://api.themoviedb.org/3/discover/movie?api_key=${this.tmdb_api_key}&language=tr-TR&sort_by=popularity.desc&include_adult=false&include_video=false&page=${page}`;
      const response = await axios.get(url);
      const movies = response.data.results;
      if (movies.length > 0) {
        const randomIndex = Math.floor(Math.random() * movies.length);
        const randomTmdbId = movies[randomIndex].id;
        // TMDB ID'den IMDB ID'ye çevirme
        const externalIdsUrl = `https://api.themoviedb.org/3/movie/${randomTmdbId}/external_ids?api_key=${this.tmdb_api_key}`;
        const externalIdsResponse = await axios.get(externalIdsUrl);
        return externalIdsResponse.data.imdb_id;
      }
      return null;
    },

    // Film arama işlevleri
    toggleSearch() {
      this.isSearchVisible = !this.isSearchVisible;
      if (this.isSearchVisible) {
        this.$nextTick(() => this.$refs.searchInput.focus());
      } else {
        this.searchQuery = '';
        this.searchResults = [];
      }
    },

    async searchMovies() {
      if (this.searchQuery.length < 3) {
        this.searchResults = [];
        return;
      }
      try {
        const url = `https://api.themoviedb.org/3/search/movie?api_key=${this.tmdb_api_key}&language=tr-TR&query=${encodeURIComponent(this.searchQuery)}`;
        const response = await axios.get(url);
        this.searchResults = response.data.results.slice(0, 10); // İlk 10 sonucu göster
      } catch (error) {
        console.error("Film araması yapılırken hata oluştu:", error);
        this.searchResults = [];
      }
    },

    goToMovieDetail(tmdbId) {
      // TMDB ID'den IMDB ID'ye çevirme ve detay sayfasına yönlendirme
      axios.get(`https://api.themoviedb.org/3/movie/${tmdbId}/external_ids?api_key=${this.tmdb_api_key}`)
        .then(response => {
          const imdbId = response.data.imdb_id;
          if (imdbId) {
            window.location.href = `${this.subPath}detail.html?view=detail&imdbID=${imdbId}`;
          } else {
            this.showAlert("Bu film için IMDb ID bulunamadı.");
          }
        })
        .catch(error => {
          console.error("IMDb ID getirilirken hata oluştu:", error);
          this.showAlert("Film detayına gidilirken bir hata oluştu.");
        });
    },

    // Sanatçı arama ve rastgele sanatçı getirme işlevleri
    togglePersonSearch() {
      this.isPersonSearchVisible = !this.isPersonSearchVisible;
      if (this.isPersonSearchVisible) {
        this.$nextTick(() => this.$refs.personSearchInput.focus());
      } else {
        this.personSearchQuery = '';
        this.personSearchResults = [];
      }
    },

    async searchPersons() {
      if (this.personSearchQuery.length < 3) {
        this.personSearchResults = [];
        return;
      }
      try {
        const url = `https://api.themoviedb.org/3/search/person?api_key=${this.tmdb_api_key}&language=tr-TR&query=${encodeURIComponent(this.personSearchQuery)}`;
        const response = await axios.get(url);
        this.personSearchResults = response.data.results.filter(person => person.profile_path).slice(0, 10); // Sadece profil fotoğrafı olanları göster ve ilk 10 sonuç
        this.lastSearchQuery = this.personSearchQuery; // Son başarılı arama sorgusunu kaydet
      } catch (error) {
        console.error("Sanatçı araması yapılırken hata oluştu:", error);
        this.personSearchResults = [];
      }
    },

    async fetchRandomPerson() {
      this.randomPersonLoading = true;
      try {
        let randomPersonId = await this.getRandomPersonId();
        if (randomPersonId) {
          window.location.href = `${this.subPath}detail.html?view=personDetail&tmdbID=${randomPersonId}`;
        } else {
          this.showAlert("Rastgele sanatçı bulunamadı. Lütfen tekrar deneyin.");
        }
      } catch (error) {
        console.error("Rastgele sanatçı getirilirken hata oluştu:", error);
        this.showAlert("Rastgele sanatçı getirilirken bir hata oluştu.");
      } finally {
        this.randomPersonLoading = false;
      }
    },

    async getRandomPersonId() {
      const page = Math.floor(Math.random() * 100) + 1; // İlk 100 sayfadan rastgele bir sayfa seç
      const url = `https://api.themoviedb.org/3/person/popular?api_key=${this.tmdb_api_key}&language=tr-TR&page=${page}`;
      const response = await axios.get(url);
      const persons = response.data.results;
      if (persons.length > 0) {
        const randomIndex = Math.floor(Math.random() * persons.length);
        return persons[randomIndex].id;
      }
      return null;
    },

    goToPersonDetail(tmdbId) {
      window.location.href = `${this.subPath}detail.html?view=personDetail&tmdbID=${tmdbId}`;
    },

    // Sanatçı Detay bilgileri (Tamamen TMDB)
    async fetchPersonDetail(tmdbID) {
      this.detailMode = true;
      try {
        const personDetailsUrl = `https://api.themoviedb.org/3/person/${tmdbID}?api_key=${this.tmdb_api_key}&language=tr-TR`;
        const personCreditsUrl = `https://api.themoviedb.org/3/person/${tmdbID}/combined_credits?api_key=${this.tmdb_api_key}&language=tr-TR`;
        const personImagesUrl = `https://api.themoviedb.org/3/person/${tmdbID}/images?api_key=${this.tmdb_api_key}`;

        const [detailsResponse, creditsResponse, imagesResponse] = await Promise.all([
          axios.get(personDetailsUrl),
          axios.get(personCreditsUrl),
          axios.get(personImagesUrl)
        ]);

        this.personData.personDetails = detailsResponse.data;
        // Sadece popülerliğe göre sırala, filmler ve diziler aynı listede olsun
        this.personData.combinedCredits = creditsResponse.data.cast
          .filter(credit => credit.poster_path) // Sadece posteri olanları filtrele
          .sort((a, b) => b.popularity - a.popularity);

        // Resimleri filtreleyerek sadece geçerli olanları al
        this.personData.images = imagesResponse.data.profiles
          .filter(image => image.file_path) // Sadece dosya yolu olanları al
          .map(image => `https://image.tmdb.org/t/p/original${image.file_path}`);

        // Doğum tarihi ve ölüm tarihi formatlama
        if (this.personData.personDetails.birthday) {
          this.personData.personDetails.birthday = this.formatDate(this.personData.personDetails.birthday);
        }
        if (this.personData.personDetails.deathday) {
          this.personData.personDetails.deathday = this.formatDate(this.personData.personDetails.deathday);
        }

      } catch (error) {
        console.error("Sanatçı detayları getirilirken hata oluştu:", error);
        this.showAlert("Sanatçı detayları getirilirken bir hata oluştu.");
      }
    },


    async fetchMovieDetail(imdbID) {
      this.detailMode = true;
      try {
        // OMDb'den film detaylarını al
        const omdbResponse = await axios.get(`https://www.omdbapi.com/?i=${imdbID}&apikey=${this.omdb_api_key}&plot=full`);

        // movieData'nın tüm özelliklerini OMDb'den gelen verilerle üzerine yaz
        // Bu, OMDb'den gelen tüm temel bilgileri doğru şekilde ayarlayacaktır.
        Object.assign(this.movieData, omdbResponse.data);
        this.imdbID = imdbID; // imdbID'yi güncelle

        // TMDB'den ilgili film detaylarını (poster, arka plan, fragman vb.) al
        const tmdbID = await this.getTmdbIdFromImdbId(imdbID);
        if (tmdbID) {
          this.tmdbID = tmdbID; // tmdbID'yi güncelle
          const tmdbDetailResponse = await axios.get(`https://api.themoviedb.org/3/movie/${tmdbID}?api_key=${this.tmdb_api_key}&append_to_response=images,videos&language=tr-TR`);
          const tmdbData = tmdbDetailResponse.data;

          // TMDB'den gelen poster ve arka planları movieData'ya ata
          this.movieData.posters = tmdbData.images.posters
            .filter(img => img.iso_639_1 === 'tr' || img.iso_639_1 === null) // Türkçe veya dil belirtilmemiş posterler
            .map(img => `https://image.tmdb.org/t/p/original${img.file_path}`);

          this.movieData.backdrops = tmdbData.images.backdrops
            .filter(img => img.iso_639_1 === 'tr' || img.iso_639_1 === null) // Türkçe veya dil belirtilmemiş arka planlar
            .map(img => `https://image.tmdb.org/t/p/original${img.file_path}`);

          // Fragmanları al
          const trailer = tmdbData.videos.results.find(
            video => video.type === "Trailer" && video.site === "YouTube" && (video.iso_639_1 === "tr" || video.iso_639_1 === "en")
          );
          if (trailer) {
            this.movieData.trailerUrl = `https://www.youtube.com/embed/${trailer.key}`; // Doğru YouTube embed URL formatı
          } else {
            this.movieData.trailerUrl = null;
          }

          // Türleri ve ülkeleri çevir (OMDb'den gelen veriye uygulanır)
          if (this.movieData.Genre) {
            this.movieData.Genre = this.movieData.Genre.split(', ').map(g => this.convertGenre(g)).join(', ');
          }
          if (this.movieData.Country) {
            this.movieData.Country = this.movieData.Country.split(', ').map(c => this.convertCountry(c)).join(', ');
          }
        } else {
          console.warn("TMDB ID bulunamadı, TMDB bilgileri yüklenemeyecek.");
        }
      } catch (error) {
        console.error("Film detayları getirilirken hata oluştu:", error);
        this.showAlert("Film detayları getirilirken bir hata oluştu.");
      }
    },


    async getTmdbIdFromImdbId(imdbID) {
      try {
        const response = await axios.get(`https://api.themoviedb.org/3/find/${imdbID}?api_key=${this.tmdb_api_key}&external_source=imdb_id`);
        if (response.data.movie_results && response.data.movie_results.length > 0) {
          return response.data.movie_results[0].id;
        }
        return null;
      } catch (error) {
        console.error("IMDb ID'den TMDB ID alınırken hata oluştu:", error);
        return null;
      }
    },

    // Yardımcı Metodlar
    multiReplace(str, mapObj) {
      var re = new RegExp(Object.keys(mapObj).join("|"), "gi");
      return str.replace(re, function(matched) {
        return mapObj[matched];
      });
    },

    convertCountry(country) {
      var mapObj = {
      "Afghanistan":"Afganistan", "Albania":"Arnavutluk", "Algeria":"Cezayir", "Andorra":"Andorra", "Angola":"Angola", "Antigua and Barbuda":"Antigua ve Barbuda", "Argentina":"Arjantin", "Armenia":"Ermenistan", "Australia":"Avustralya", "Austria":"Avusturya", "Azerbaijan":"Azerbaycan", "Bahamas":"Bahamalar", "Bahrain":"Bahreyn", "Bangladesh":"Bangladeş", "Barbados":"Barbados", "Belarus":"Belarus", "Belgium":"Belçika", "Belize":"Belize", "Benin":"Benin", "Bhutan":"Butan", "Bolivia":"Bolivya", "Bosnia and Herzegovina":"Bosna Hersek", "Botswana":"Botsvana", "Brazil":"Brezilya", "Brunei":"Brunei", "Bulgaria":"Bulgaristan", "Burkina Faso":"Burkina Faso", "Burundi":"Burundi", "Cabo Verde":"Cabo Verde", "Cambodia":"Kamboçya", "Cameroon":"Kamerun", "Canada":"Kanada", "Central African Republic":"Orta Afrika Cumhuriyeti", "Chad":"Çad", "Chile":"Şili", "China":"Çin", "Colombia":"Kolombiya", "Comoros":"Komorlar", "Congo (Brazzaville)":"Kongo (Brazzaville)", "Congo (Kinshasa)":"Kongo (Kinşasa)", "Costa Rica":"Kosta Rika", "Croatia":"Hırvatistan", "Cuba":"Küba", "Cyprus":"Kıbrıs", "Czech Republic":"Çek Cumhuriyeti", "Denmark":"Danimarka", "Djibouti":"Cibuti", "Dominica":"Dominika", "Dominican Republic":"Dominik Cumhuriyeti", "East Timor (Timor-Leste)":"Doğu Timor (Timor-Leste)", "Ecuador":"Ekvador", "Egypt":"Mısır", "El Salvador":"El Salvador", "Equatorial Guinea":"Ekvator Ginesi", "Eritrea":"Eritre", "Estonia":"Estonya", "Eswatini (formerly Swaziland)":"Eswatini (eski Svaziland)", "Ethiopia":"Etiyopya", "Fiji":"Fiji", "Finland":"Finlandiya", "France":"Fransa", "Gabon":"Gabon", "Gambia":"Gambiya", "Georgia":"Gürcistan", "Germany":"Almanya", "Ghana":"Gana", "Greece":"Yunanistan", "Grenada":"Grenada", "Guatemala":"Guatemala", "Guinea":"Gine", "Guinea-Bissau":"Gine-Bissau", "Guyana":"Guyana", "Haiti":"Haiti", "Honduras":"Honduras", "Hungary":"Macaristan", "Iceland":"İzlanda", "India":"Hindistan", "Indonesia":"Endonezya", "Iran":"İran", "Iraq":"Irak", "Ireland":"İrlanda", "Israel":"İsrail", "Italy":"İtalya", "Ivory Coast":"Fildişi Sahili", "Jamaica":"Jamaika", "Japan":"Japonya", "Jordan":"Ürdün", "Kazakhstan":"Kazakistan", "Kenya":"Kenya", "Kiribati":"Kiribati", "Korea, North":"Kuzey Kore", "Korea, South":"Güney Kore", "Kosovo":"Kosova", "Kuwait":"Kuveyt", "Kyrgyzstan":"Kırgızistan", "Laos":"Laos", "Latvia":"Letonya", "Lebanon":"Lübnan", "Liberia":"Liberya", "Libya":"Libya", "Liechtenstein":"Lihtenştayn", "Lithuania":"Litvanya", "Luxembourg":"Lüksemburg", "Madagascar":"Madagaskar", "Malawi":"Malavi", "Malaysia":"Malezya", "Maldives":"Maldivler", "Mali":"Mali", "Malta":"Malta", "Marshall Islands":"Marshall Adaları", "Mauritania":"Moritanya", "Mauritius":"Mauritius", "Mexico":"Meksika", "Micronesia":"Mikronezya", "Moldova":"Moldova", "Monaco":"Monako", "Mongolia":"Moğolistan", "Montenegro":"Karadağ", "Morocco":"Fas", "Mozambique":"Mozambik", "Myanmar (Burma)":"Myanmar (Burma)", "Namibia":"Namibya", "Nauru":"Nauru", "Nepal":"Nepal", "Netherlands":"Hollanda", "New Zealand":"Yeni Zelanda", "Nicaragua":"Nikaragua", "Niger":"Nijer", "Nigeria":"Nijerya", "North Macedonia (formerly Macedonia)":"Kuzey Makedonya (eski Makedonya)", "Norway":"Norveç", "Oman":"Umman", "Pakistan":"Pakistan", "Palau":"Palau", "Palestine":"Filistin", "Panama":"Panama", "Papua New Guinea":"Papua Yeni Gine", "Paraguay":"Paraguay", "Peru":"Peru", "Philippines":"Filipinler", "Poland":"Polonya", "Portugal":"Portekiz", "Qatar":"Katar", "Romania":"Romanya", "Russia":"Rusya", "Rwanda":"Ruanda", "Saint Kitts and Nevis":"Saint Kitts ve Nevis", "Saint Lucia":"Saint Lucia", "Saint Vincent and the Grenadines":"Saint Vincent ve Grenadinler", "Samoa":"Samoa", "San Marino":"San Marino", "Sao Tome and Principe":"Sao Tome ve Principe", "Saudi Arabia":"Suudi Arabistan", "Senegal":"Senegal", "Serbia":"Sırbistan", "Seychelles":"Seyşeller", "Sierra Leone":"Sierra Leone", "Singapore":"Singapur", "Slovakia":"Slovakya", "Slovenia":"Slovenya", "Solomon Islands":"Solomon Adaları", "Somalia":"Somali", "South Africa":"Güney Afrika", "South Sudan":"Güney Sudan", "Spain":"İspanya", "Sri Lanka":"Sri Lanka", "Sudan":"Sudan", "Suriname":"Surinam", "Sweden":"İsveç", "Switzerland":"İsviçre", "Syria":"Suriye", "Taiwan":"Tayvan", "Tajikistan":"Tacikistan", "Tanzania":"Tanzanya", "Thailand":"Tayland", "Togo":"Togo", "Tonga":"Tonga", "Trinidad and Tobago":"Trinidad ve Tobago", "Tunisia":"Tunus", "Turkey":"Türkiye", "Turkmenistan":"Türkmenistan", "Tuvalu":"Tuvalu", "Uganda":"Uganda", "Ukraine":"Ukrayna", "United Arab Emirates":"Birleşik Arap Emirlikleri", "United Kingdom":"Birleşik Krallık", "United States":"Amerika Birleşik Devletleri", "Uruguay":"Uruguay", "Uzbekistan":"Özbekistan", "Vanuatu":"Vanuatu", "Vatican City":"Vatikan", "Venezuela":"Venezuela", "Vietnam":"Vietnam", "Yemen":"Yemen", "Zambia":"Zambiya", "Zimbabwe":"Zimbabve"
      };
      return this.multiReplace(country,mapObj);
    },

    convertGenre (genre) {
      var mapObj = {
         "Drama":"Dram", "War":"Savaş", "Comedy":"Komedi", "Sci-Fi":"Bilim-Kurgu", "Fantasy":"Fantastik", "Adventure":"Macera", "Romance":"Romantik", "Action":"Aksiyon", "Mystery":"Gizem", "Family":"Aile", "Crime":"Suç", "Documentary":"Belgesel", "Biography":"Biyografi", "Music":"Müzikal", "Animation":"Animasyon", "Horror":"Korku", "Thriller":"Gerilim", "Sport":"Spor", "History":"Tarihi", "Western":"Western", "Musical":"Müzikal", "Short":"Kısa Film", "News":"Haber"
      };
      return this.multiReplace(genre,mapObj);
    },

    formatDate(dateString) {
      if (!dateString) return '';
      const [year, month, day] = dateString.split('-');
      return `${day}.${month}.${year}`;
    },

    showAlert(message) {
      const alertOverlay = document.getElementById('customAlertOverlay');
      const alertBox = document.getElementById('customAlertBox');
      const alertMessage = document.getElementById('customAlertMessage');

      alertMessage.textContent = message;
      alertOverlay.style.visibility = 'visible';
      alertOverlay.style.opacity = '1';
      alertBox.style.transform = 'scale(1)';

      const closeButton = document.getElementById('customAlertCloseButton');
      closeButton.onclick = () => {
        alertBox.style.transform = 'scale(0.9)';
        alertOverlay.style.opacity = '0';
        alertOverlay.style.visibility = 'hidden';
      };
    },

    // Video Box Metodları
    openVideoBox(src) {
      this.videoBox.src = src;
      this.videoBox.visible = true;
      this.setVideoBoxPosition();
    },

    closeVideoBox() {
      this.videoBox.visible = false;
      this.videoBox.src = "#"; // Videonun durması için src'yi sıfırla
    },

    setVideoBoxPosition() {
      // Ekran ortalaması için basit bir hesaplama
      const windowWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
      const windowHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;

      this.videoBox.left = (windowWidth - this.videoBox.width) / 2;
      this.videoBox.top = (windowHeight - this.videoBox.height) / 2;
    }
  },
  mounted() {
    this.init();
    window.addEventListener('resize', this.setVideoBoxPosition); // Ekran boyutu değiştiğinde pozisyonu güncelle
  },
  beforeUnmount() {
    window.removeEventListener('resize', this.setVideoBoxPosition);
  }
});

app.mount('#app');