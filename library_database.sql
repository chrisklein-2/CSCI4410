DROP DATABASE IF EXISTS library_database;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE Database library_database;
USE library_database;

-- Table structure for table `books`
CREATE TABLE `books` (
  `book_id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `author` VARCHAR(255) NOT NULL,
  `description` varchar(1027),
  `length` int(5),
  `genre` varchar(255),
  `image` varchar(1027),
  `copies_available` INT(11) NOT NULL,
  `checkedout_count` INT(11) DEFAULT 0,
  PRIMARY KEY (`book_id`)
);

CREATE TABLE `users` (
  `user_id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `book_reviews` (
  `review_id` INT(11) NOT NULL AUTO_INCREMENT,
  `book_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `review_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`review_id`),
  FOREIGN KEY (`book_id`) REFERENCES `books`(`book_id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
);

-- Table structure for table `checked_out`
CREATE TABLE `checked_out` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `book_id` INT(11) NOT NULL,
  `checkout_date` DATETIME DEFAULT current_timestamp(),
  `due_date` DATETIME DEFAULT (current_timestamp() + INTERVAL 21 DAY),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `book_id` (`book_id`),
  CONSTRAINT `checked_out_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `checked_out_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- To populate table `users`
INSERT INTO `users` (`user_id`, `username`, `email`, `password`) VALUES
(1, 'jamietest', 'jamietest@test.com', '$2y$10$Ufu/lnvO3613FE4Lapnfj.0eUSkduuHUQLckpsVYaKzRKf5TC0oZG'),
(2, 'usertest', 'usertest@testing.com', '$2y$10$5/ZfutWCCemhYpnOjroG..Mz02nhXJqVzosOyTGmBorgWfInwJqhW'),
(3, 'alice123', 'alice@example.com', '$2y$10$kPz/uKnxZ7uT5W7VtIkQnOaJZsxCOeNHDf4e6qBhi1MEKblmPbHo2'),
(4, 'bobster', 'bobster@example.com', '$2y$10$9QdH/F6OxS19KrV18QYqruKQH3DPSG./nU9ERa0qY3Xq20UYxNyoe'),
(5, 'charlie_dev', 'charlie@devmail.com', '$2y$10$ItkFlFbmN.MbnPPIh4Lj6etA0h5kZ/6OwhswZ8TiThZybwktK2ybi'),
(6, 'daisy456', 'daisy456@example.com', '$2y$10$GxCz6zX1Db26sSChFZz7OeLzXReMfN7iOHgG9MtxbHPYt6UuW3VKq'),
(7, 'erictest', 'erictest@mail.com', '$2y$10$YhXoX3eI4nHTsy9aIXpxq.YE2gwbHtAHlWnm3Izr3fG06cchLMrlm');

-- To populate table `books`
INSERT INTO `books` ( `book_id`, `title`, `author`, `description`, `length`, `genre`, `image`,`copies_available`) VALUES
(1, 'A Game Of Thrones', 'George R. R. Martin', 'The first book in A Song of Ice and Fire', 720, 'Fantasy', 'https://m.media-amazon.com/images/I/714ExofeKJL._AC_UF1000,1000_QL80_.jpg', 1),
(2, 'A Clash of Kings', 'George R. R. Martin', 'The second book in A Song of Ice and Fire', 1040, 'Fantasy', 'https://m.media-amazon.com/images/I/71R9pRtC6AL._AC_UF1000,1000_QL80_.jpg', 3),
(3, 'A Storm of Swords', 'George R. R. Martin', 'The third book in A Song of Ice and Fire', 1008, 'Fantasy', 'https://m.media-amazon.com/images/I/71hzYSMbvZL._AC_UF1000,1000_QL80_.jpg', 2),
(4, 'A Feast for Crows', 'George R. R. Martin', 'The fourth book in A Song of Ice and Fire', 1104, 'Fantasy', 'https://m.media-amazon.com/images/I/71wX5JhAkYL._AC_UF1000,1000_QL80_.jpg', 1),
(5, 'A Dance with Dragons', 'George R. R. Martin', 'The fifth book in A Song of Ice and Fire', 1152, 'Fantasy', 'https://m.media-amazon.com/images/I/71CrMiWhcDL._AC_UF1000,1000_QL80_.jpg', 4),
(6, 'Dune', 'Frank Herbert', 'The frist book in the Dune series', 896, 'Science Fiction', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQJsO6s_O3oX-H6QDpdo5oafiXNvySAZ-z-PdoznbEYZA&s', 7),
(7, 'Dune Messiah', 'Frank Herbert', 'The second book in the Dune series', 352, 'Science Fiction', 'https://m.media-amazon.com/images/I/817Xh+bqwOL._AC_UF1000,1000_QL80_.jpg', 0),
(8, 'Children of Dune', 'Frank Herbert', 'The third book in the Dune series', 416, 'Science Fiction', 'https://m.media-amazon.com/images/I/817pAbUYBpL._AC_UF1000,1000_QL80_.jpg', 2),
(9, 'The Lord of the Rings', 'J. R. R. Tolkien', "The first book in The Lord of the Rings series", 1216, 'Fantasy', 'https://m.media-amazon.com/images/I/7125+5E40JL._AC_UF1000,1000_QL80_.jpg', 1),
(10, 'The Two Towers', 'J. R. R. Tolkien', "The second book in The Lord of the Rings series", 416, 'Fantasy', 'https://m.media-amazon.com/images/I/61JO1okOIHL._AC_UF1000,1000_QL80_.jpg', 5),
(11, 'The Return of the King', 'J. R. R. Tolkien', "The third book in The Lord of the Rings series", 512, 'Fantasy', 'https://m.media-amazon.com/images/I/71tDovoHA+L._AC_UF1000,1000_QL80_.jpg', 9),
(12, 'The Hobbit', 'J. R. R. Tolkien', "A prequel to The Lord of the Rings", 300, 'Fantasy', 'https://m.media-amazon.com/images/I/712cDO7d73L._AC_UF1000,1000_QL80_.jpg', 1),
(13, 'The Design of Everyday Things', 'Don Norman', 'This book explores the complex interactions between humans and everyday objects', 368, 'Technical', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRYD_w4aVsG2IymZQUpYqSsR6KpSScrXQhfh7jTSHAmqg&s', 2),
(14, "Don't Make Me Think", 'Steve Krug', 'Guide to help understand the principles of intuitive design', 216, 'Technical', 'https://m.media-amazon.com/images/I/51sdCuqMwWL._AC_UF1000,1000_QL80_.jpg', 1),
(15, '1984', 'George Orwell', 'A dystopian social science fiction novel and cautionary tale about the future', 328, 'Science Fiction', 'https://m.media-amazon.com/images/I/71kxa1-0mfL._AC_UF1000,1000_QL80_.jpg', 4),
(16, 'Brave New World', 'Aldous Huxley', 'A futuristic world where humans are genetically engineered', 288, 'Science Fiction', 'https://m.media-amazon.com/images/I/81A-mvlo+QL._AC_UF1000,1000_QL80_.jpg', 3),
(17, 'Fahrenheit 451', 'Ray Bradbury', 'A dystopian novel about a future American society where books are outlawed', 194, 'Science Fiction', 'https://m.media-amazon.com/images/I/81OthjkJBuL._AC_UF1000,1000_QL80_.jpg', 5),
(18, 'The Catcher in the Rye', 'J.D. Salinger', 'The story of Holden Caulfield and his experiences in New York City', 277, 'Literary Fiction', 'https://m.media-amazon.com/images/I/71bXKtbmWlL._AC_UF1000,1000_QL80_.jpg', 2),
(19, 'To Kill a Mockingbird', 'Harper Lee', 'A novel of warmth and humor despite dealing with serious issues of rape and racial inequality', 336, 'Classic', 'https://m.media-amazon.com/images/I/81OtwFxo8fL._AC_UF1000,1000_QL80_.jpg', 6),
(20, 'The Great Gatsby', 'F. Scott Fitzgerald', 'A novel about the American dream and tragic romance in the 1920s', 180, 'Classic', 'https://m.media-amazon.com/images/I/81xXAyfc9OL._AC_UF1000,1000_QL80_.jpg', 2),
(21, 'Thinking, Fast and Slow', 'Daniel Kahneman', 'Explores the dual systems that drive the way we think', 512, 'Psychology', 'https://m.media-amazon.com/images/I/71p7rb1fYcL._AC_UF1000,1000_QL80_.jpg', 3),
(22, 'Atomic Habits', 'James Clear', 'An easy & proven way to build good habits and break bad ones', 320, 'Self-Help', 'https://m.media-amazon.com/images/I/91bYsX41DVL._AC_UF1000,1000_QL80_.jpg', 5),
(23, 'Sapiens: A Brief History of Humankind', 'Yuval Noah Harari', 'A thought-provoking journey through the history of human evolution', 498, 'History', 'https://m.media-amazon.com/images/I/713jIoMO3UL._AC_UF1000,1000_QL80_.jpg', 4),
(24, 'The Name of the Wind', 'Patrick Rothfuss', 'A high fantasy novel about a magically gifted young man', 662, 'Fantasy', 'https://m.media-amazon.com/images/I/81RjzKnQOEL._AC_UF1000,1000_QL80_.jpg', 3);
COMMIT;

-- To populate reviews 
INSERT INTO `book_reviews` (`book_id`, `user_id`, `title`, `content`) VALUES
(1, 1, 'An Epic Start', 'Absolutely loved the world-building. Took a bit to get into but totally worth it.'),
(1, 2, 'Complex but Brilliant', 'A bit slow at first but the characters and plot are phenomenal. Highly recommend!'),
(2, 1, 'Bigger and Better', 'The stakes are higher, and I couldn’t put it down. Great follow-up.'),
(3, 3, 'Masterpiece', 'The Red Wedding... I have no words. This book was intense and amazing.'),
(3, 4, 'So many twists!', 'The emotional rollercoaster in this book broke me. 10/10.'),
(4, 2, 'Slightly Slower', 'Not as exciting as the previous ones, but still a must-read.'),
(5, 1, 'Back to Form', 'Dragons, politics, betrayals... everything you want from this series.'),
(5, 3, 'Loved it', 'It felt like the story really expanded. Some slow parts but overall great.'),
(6, 4, 'Mind-blowing Sci-Fi', 'Dune is unlike anything else. The themes, the depth, the setting — legendary.'),
(6, 2, 'A bit heavy but worth it', 'Took some time to understand everything, but what a story. Beautifully written.');
