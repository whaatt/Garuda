/* MySQL Database Schema */

CREATE TABLE users (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	name VARCHAR(100),
	username VARCHAR(50) NOT NULL,
	password CHAR(128) NOT NULL, /* Whirlpool Hash */
	created TIMESTAMP DEFAULT '0000-00-00 00:00:00',
	updated TIMESTAMP DEFAULT NOW() ON UPDATE NOW() /* Initialize with NULL. */	
);

CREATE TABLE psets(
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	title VARCHAR(200) NOT NULL,
	info TEXT,
	director_users_id INT NOT NULL,
	admin_access_code INT NOT NULL,
	manager_access_code INT NOT NULL,
	editor_access_code INT NOT NULL,
	created TIMESTAMP DEFAULT '0000-00-00 00:00:00',
	target TIMESTAMP /* Initialize with target. */	
);

CREATE TABLE psets_allocations(
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	psets_id INT NOT NULL,
	subject VARCHAR(100) NOT NULL
);

CREATE TABLE permissions (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	users_id INT NOT NULL,
	psets_id INT NOT NULL,
	psets_allocations_id TEXT, /* Only use for managers. */
	role CHAR(1) NOT NULL /* Roles: d = director, a = administrator, m = manager, and e = editor. */
);

CREATE TABLE tossups(
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	psets_id INT NOT NULL,
	creator_users_id INT NOT NULL,
	editor_users_id INT,
	psets_allocations_id INT, /* Subject */
	tossup TEXT NOT NULL,
	answer TEXT NOT NULL,
	duplicate_tossups_id INT,
	difficulty VARCHAR(1) NOT NULL DEFAULT 'm', /* Difficulties: e = easy, m = medium, and h = hard. */
	approved TINYINT(1) NOT NULL, /* Initialize with zero. */
	promoted TINYINT(1) NOT NULL, /* Initialize with zero. */
	round_id TEXT,
	round_num TEXT,
	created TIMESTAMP DEFAULT '0000-00-00 00:00:00'
);

CREATE TABLE bonuses(
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	psets_id INT NOT NULL,
	creator_users_id INT NOT NULL,
	editor_users_id INT,
	psets_allocations_id INT, /* Subject */
	leadin TEXT NOT NULL,
	question1 TEXT NOT NULL,
	answer1 TEXT NOT NULL,
	question2 TEXT,
	answer2 TEXT,
	question3 TEXT,
	answer3 TEXT,
	question4 TEXT,
	answer4 TEXT,
	duplicate_bonuses_id INT,
	difficulty VARCHAR(1) NOT NULL DEFAULT 'm', /* Difficulties: e = easy, m = medium, and h = hard. */
	approved TINYINT(1) NOT NULL, /* Initialize with zero. */
	promoted TINYINT(1) NOT NULL, /* Initialize with zero. */
	round_id TEXT,
	round_num TEXT,
	created TIMESTAMP DEFAULT '0000-00-00 00:00:00'
);

CREATE TABLE messages(
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	tossup_or_bonus TINYINT(1) NOT NULL, /* Zero for TU, One for Bonus */
	tub_id INT NOT NULL,
	users_id INT NOT NULL,
	message TEXT NOT NULL
);