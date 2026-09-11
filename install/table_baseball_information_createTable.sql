-- mySQL用テーブル作成スクリプト
-- baseball_information テーブル作成
CREATE TABLE IF NOT EXISTS `baseball_information`
(
  `information_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY
, `title` VARCHAR(255) NOT NULL
, `content` TEXT NOT NULL
, `post_date` DATE NOT NULL
, `reference_start_date` DATE
, `reference_end_date` DATE
, `is_enabled` INT NOT NULL DEFAULT 0
, `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
, `created_by` VARCHAR(255)
, `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
, `updated_by` VARCHAR(255)
);
