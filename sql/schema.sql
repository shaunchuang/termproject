-- SkillSwap 資料表建立語法

CREATE TABLE `User` (
  `user_id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `SkillPost` (
  `post_id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `title` VARCHAR(100) NOT NULL,
  `description` TEXT NOT NULL,
  `point_cost` INT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `User`(`user_id`) ON DELETE CASCADE
);

CREATE TABLE `ExchangeBooking` (
  `booking_id` INT AUTO_INCREMENT PRIMARY KEY,
  `post_id` INT NOT NULL,
  `requester_id` INT NOT NULL,
  `status` ENUM('待確認','已完成','已取消') NOT NULL DEFAULT '待確認',
  `scheduled_at` DATETIME NOT NULL,
  FOREIGN KEY (`post_id`) REFERENCES `SkillPost`(`post_id`) ON DELETE CASCADE,
  FOREIGN KEY (`requester_id`) REFERENCES `User`(`user_id`) ON DELETE CASCADE
);

CREATE TABLE `PointTransaction` (
  `tx_id` INT AUTO_INCREMENT PRIMARY KEY,
  `from_user_id` INT NOT NULL,
  `to_user_id` INT,
  `amount` INT NOT NULL,
  `reason` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`from_user_id`) REFERENCES `User`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`to_user_id`) REFERENCES `User`(`user_id`) ON DELETE SET NULL
);

CREATE TABLE `Comment` (
  `comment_id` INT AUTO_INCREMENT PRIMARY KEY,
  `post_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `content` TEXT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`post_id`) REFERENCES `SkillPost`(`post_id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `User`(`user_id`) ON DELETE CASCADE
);