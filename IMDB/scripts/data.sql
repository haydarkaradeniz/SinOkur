
CREATE TABLE imdb_user_list (
	user_id INT UNSIGNED NULL,
	list_id VARCHAR(100) NULL,
	imdb_id VARCHAR(100) NULL,
	create_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4;


CREATE TABLE imdb_list_data (
	imdb_id varchar(100) NULL,
	imdb_type varchar(100) NULL,
	poster varchar(2000) NULL,
	title varchar(1000) NULL,
	`year` INT NULL,
	runtime varchar(100) NULL,
	imdb_rating varchar(100) NULL,
	imdb_votes varchar(100) NULL,
	movie_type varchar(100) NULL,
	person_type varchar(100),
	`name` varchar(1000) NULL,
	place_of_birth varchar(100) NULL,
	birthday varchar(100) NULL,
	deathday varchar(100) NULL,
	profile_path varchar(2000) NULL,
	create_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL,
	update_date TIMESTAMP NULL
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4;


CREATE TABLE imdb_list_data_property (
	user_id INT UNSIGNED NULL,
	imdb_id varchar(100) NULL,
    user_rating INT NULL,
	update_date TIMESTAMP NULL,
	create_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4;

----------------------------------------


CREATE TABLE sineokur_phpbtest.mybox_user_data (
	user_id INT UNSIGNED NOT NULL,
	public_profile INT UNSIGNED NULL,
	CONSTRAINT mybox_user_data_pk PRIMARY KEY (user_id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4;


CREATE TABLE sineokur_phpbtest.mybox_user_movie_data (
	user_id INT UNSIGNED NULL,
	movie_id varchar(100) NULL,
    user_rating INT NULL,
	create_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4;

CREATE TABLE sineokur_phpbtest.mybox_movie_data (
	movie_id varchar(100) NULL,
	poster varchar(2000) NULL,
	title varchar(1000) NULL,
	`year` INT NULL,
	runtime varchar(100) NULL,
	director varchar(1000) NULL,
	writer varchar(1000) NULL,
	actors varchar(2000) NULL,
	imdb_rating varchar(100) NULL,
	imdb_votes varchar(100) NULL,
	create_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL,
	update_date TIMESTAMP NULL
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4;



