-- mySQL用テーブル作成スクリプト
-- baseball_attachment テーブル作成
-- attachment_type : 2 : 大会概要, 3 : 大会規定, 11 : 試合予定/結果, 91 : 記念品
CREATE TABLE IF NOT EXISTS `baseball_attachment`
(
  `attachment_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY
, `attachment_type` INT NOT NULL DEFAULT 0
, `physical_file_name` VARCHAR(255) NOT NULL
, `file_name` VARCHAR(255) NOT NULL
, `file_path` VARCHAR(255) NOT NULL
, `file_size` INT NOT NULL DEFAULT 0
, `mime_type` VARCHAR(255) NOT NULL
, `file_description` VARCHAR(255) NOT NULL
, `is_enabled` INT NOT NULL DEFAULT 0
, `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
, `created_by` VARCHAR(255)
, `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
, `updated_by` VARCHAR(255)
);
