CREATE DATABASE IF NOT EXISTS KnightsTalk;
USE KnightsTalk;

CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    profile_photo VARCHAR(255),
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100),
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE puzzles (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    initial_fen VARCHAR(255) NOT NULL,
    difficulty ENUM('easy', 'medium', 'hard') NOT NULL,
    solution JSON NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE solutions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    puzzle_id BIGINT UNSIGNED NOT NULL,
    move VARCHAR(255) NOT NULL,
    `order` INT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (puzzle_id) REFERENCES puzzles(id) ON DELETE CASCADE
);

CREATE TABLE courses (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED,
    name VARCHAR(255),
    title VARCHAR(255),
    description TEXT,
    image VARCHAR(255),
    level ENUM('beginner', 'intermediate', 'pro') NOT NULL DEFAULT 'beginner',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE lessons (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    course_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    moves_sequence TEXT NOT NULL,
    move_descriptions JSON,
    `order` INT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

CREATE TABLE lesson_user (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    lesson_id BIGINT UNSIGNED NOT NULL,
    completed TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE
);

CREATE TABLE progress (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    course_id BIGINT UNSIGNED,
    puzzle_id BIGINT UNSIGNED,
    progress TINYINT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (puzzle_id) REFERENCES puzzles(id) ON DELETE CASCADE
);

CREATE TABLE analyses (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED,
    title VARCHAR(255),
    analysis TEXT,
    media_url VARCHAR(255),
    moves_data JSON,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE messages (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE puzzle_user (
    user_id BIGINT UNSIGNED NOT NULL,
    puzzle_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (user_id, puzzle_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (puzzle_id) REFERENCES puzzles(id) ON DELETE CASCADE
);


-- 1

select users.name  , puzzles.title , count(puzzles.id)
from users
join puzzle_user on users.id = puzzle_user.user_id
join puzzles on puzzle_user.puzzle_id = puzzles.id


group by puzzles.title , users.name;

