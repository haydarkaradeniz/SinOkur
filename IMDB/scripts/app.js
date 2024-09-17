
const app = Vue.createApp({
  data() {
    return {
      tmdb_api_key: "691a740114fe7dc34fbbe9f1a464cc5e",   
      omdb_api_key: "68d73d3c",   
      imdbID: "",
      viewType: "",
      movieData : {},
      apiData: {},
      detailData: {
        fovorite : false,
        movieList : false,
        listData : [],
      }
    };
  },
  mounted() {    
   //not yet implemented
  
  },
  created() {
    let uri = window.location.search.substring(1); 
    let params = new URLSearchParams(uri);
    this.imdbID = params.get("id");
    this.viewType = this.imdbID[0] == 't' ? 'movie':'people';
    if(this.viewType == 'movie') {
      this.getOMDBMovieDetail();
    }
  },
  destroyed() {
  },

  methods: {

    favorite(type) {
      //TODO NOT YET IMPLEMENTED
      this.detailData.favorite = !this.detailData.favorite;
    },

    openListMenu(type) {
      //TODO NOT YET IMPLEMENTED
      this.detailData.movieList = !this.detailData.movieList;
    },

    checkNaN(text) {
      return !text || text.toString().indexOf("N/A") > -1 || text === 'NaN.NaN.NaN';
    },

    retrieve(text, type) {
      if(type == 'poster') {
        return !this.checkNaN(text)?text:'images/default.gif';
      } else if(type == 'runtime' && !this.checkNaN(text)) {
        return text.replace('min','dk');
      } else if(type == 'country' && !this.checkNaN(text)) {
        return this.convertCountry(text);
      } else if(type == 'genre' && !this.checkNaN(text)) {
        return this.convertGenre(text);
      } else if(type == 'title' && !this.checkNaN(text)) {
        return text.toUpperCase();
      } else if(type == 'overview' && !this.checkNaN(text)) {
        var maxLength = 4000;
        this.movieData.overviewLink = text.length>maxLength;
        return this.movieData.overviewLink ? text.substring(0, maxLength) : text;
      } 
      
      
      else {
        return !this.checkNaN(text) ? text : "- "; 
      }
    },

    fillMovieData(source) {
      if(source == 'omdb') {
        this.movieData.omdb_poster = this.apiData.Poster;
        this.movieData.title = this.retrieve(this.apiData.Title, 'title');
        this.movieData.omdb_director = this.apiData.Director;
        this.movieData.director = this.retrieve(this.apiData.Director);
        this.movieData.country = this.retrieve(this.apiData.Country, 'country');
        this.movieData.year = this.retrieve(this.apiData.Year);
        this.movieData.genre = this.retrieve(this.apiData.Genre, 'genre');
        this.movieData.imdbRating = this.retrieve(this.apiData.imdbRating);
        this.movieData.imdbVotes = this.retrieve(this.apiData.imdbVotes);
        this.movieData.runtime = this.retrieve(this.apiData.Runtime, 'runtime');
        this.movieData.type = this.apiData.Type;
      } else if(source == 'tmdb') {
        this.movieData.title_tr = this.retrieve(this.apiData.title, 'title');
        this.movieData.poster = "http://image.tmdb.org/t/p/w500" + this.apiData.poster_path;
        this.movieData.overview = this.retrieve(this.apiData.overview, 'overview');
      } else if(source == 'tmdb-error') {
        this.movieData.title_tr = this.movieData.title;
        this.movieData.poster = this.movieData.omdb_poster;
        this.movieData.director = this.checkNaN(this.movieData.omdb_director) && this.movieData.type == 'series' ? 'Farklı yönetmenler' : this.movieData.director; 
      }
    },


    getOMDBMovieDetail() {
      axios.get('https://www.omdbapi.com', {
        params: {
          "plot": "full",
          "r": "json",
          "i" : this.imdbID,
          "apikey": this.omdb_api_key
        }
      }).then(response => {	
        this.apiData = response.data;
        this.fillMovieData('omdb');
      }).catch((err) => {
        console.error(err);
      }).finally(()=> {
        this.getTMDBMovieDetail();
      });
    },

    getTMDBMovieDetail() {
      axios.get('https://api.themoviedb.org/3/movie/' + this.imdbID, {
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
      }).finally(()=> {});
    

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
