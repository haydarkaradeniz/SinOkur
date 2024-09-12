<?php

include ("connection.php");

// Bağlantı oluşturma
$conn = new mysqli($servername, $username, $password, $database);

//post parameters
$userId = $_POST['userId'];

// Bağlantıyı kontrol etme
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

$sql = "select t1.list_id, t1.movie_id, t2.poster, t2.title, t2.year, t2.runtime, t2.director, t2.writer, t2.actors, t2.imdb_rating, t2.imdb_votes, t3.user_rating, t4.forum_score, t4.forum_vote_count  
from mybox_user_list t1 inner join mybox_movie_data t2 on t1.movie_id = t2.movie_id left JOIN mybox_user_movie_data t3 on t1.user_id = t3.user_id and t1.movie_id = t3.movie_id  
left join (select movie_id, avg(user_rating) forum_score, count(movie_id) forum_vote_count from mybox_user_movie_data group by movie_id) t4 on t2.movie_id = t4.movie_id 
 where t1.user_id = ? order by t1.create_date desc";
$stmt = $conn->prepare($sql);
//i - integer, d - double, s - string, b - BLOB
$stmt->bind_param("i", $userId);
// set parameters and execute
$stmt->execute();
$result = $stmt->get_result(); 
$movieListData = $result->fetch_all(MYSQLI_ASSOC);
$row_count = $result->num_rows;

$result->close();
$stmt->close();
$conn->close();


echo '{ "userMovieList":[';
$current_row = 1;
foreach ($movieListData as $key => $movieData) {
	echo '{';
	echo '"listId":"'.$movieData['list_id'].'",';
	echo '"movieId":"'.$movieData['movie_id'].'",';
	echo '"poster":"'.$movieData['poster'].'",';
	echo '"title":"'.$movieData['title'].'",';
	echo '"year":"'.$movieData['year'].'",';
	echo '"runtime":"'.$movieData['runtime'].'",';
	echo '"director":"'.$movieData['director'].'",';
	echo '"writer":"'.$movieData['writer'].'",';
	echo '"actors":"'.$movieData['actors'].'",';
	echo '"imdbRating":"'.$movieData['imdb_rating'].'",';
	echo '"imdbVotes":"'.$movieData['imdb_votes'].'",';
	if($movieData['forum_score']) {
		echo '"forumScore":"'.round($movieData['forum_score'],1).'",';
	}
	echo '"forumVoteCount":"'.$movieData['forum_vote_count'].'",';
	echo '"userRating":"'.$movieData['user_rating'].'"';
	echo '}';
	if($current_row != $row_count) {
		echo ',';
		$current_row++;
	}
	

}
echo ']}';


	
?>