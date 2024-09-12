CREATE TABLE sineokur_phpb.mybox_user_data (
	user_id INT UNSIGNED NOT NULL,
	public_profile INT UNSIGNED NULL,
	CONSTRAINT mybox_user_data_pk PRIMARY KEY (user_id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;


CREATE TABLE sineokur_phpb.mybox_user_list (
	user_id INT UNSIGNED NULL,
	list_id VARCHAR(100) NULL,
	movie_id VARCHAR(100) NULL,
	create_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;


CREATE TABLE sineokur_phpb.mybox_user_movie_data (
	user_id INT UNSIGNED NULL,
	movie_id varchar(100) NULL,
    user_rating INT NULL,
	create_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE sineokur_phpb.mybox_movie_data (
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
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;



