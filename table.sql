CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    firstName VARCHAR(50),
    lastName VARCHAR(50),
    email VARCHAR(255),
    password VARCHAR(255),
    dark_mode TINYINT(1) DEFAULT 0
);

CREATE TABLE notess (
    note_id INT AUTO_INCREMENT PRIMARY KEY,
    title TEXT,
    content TEXT,
    user_id INT,
    currentDate DATE,
    pinned TINYINT(1) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);