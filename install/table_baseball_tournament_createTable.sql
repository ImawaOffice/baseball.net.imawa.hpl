-- mySQL用テーブル作成スクリプト
-- baseball_tournament テーブル作成
CREATE TABLE IF NOT EXISTS `baseball_tournament`
(
  `tournament_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY
, `tournament_title` VARCHAR(255) NOT NULL
, `tournament_text` VARCHAR(255) NOT NULL
, `tournament_start_date` DATE NOT NULL
, `tournament_end_date` DATE NOT NULL
, `tournament_parent_id` INT NOT NULL DEFAULT 0
, `tournament1_teams` INT NOT NULL DEFAULT 0
, `tournament1_text` VARCHAR(255) NOT NULL DEFAULT ''
, `tournament2_teams` INT NOT NULL DEFAULT 0
, `tournament2_text` VARCHAR(255) NOT NULL DEFAULT ''
, `tournament3_teams` INT NOT NULL DEFAULT 0
, `tournament3_text` VARCHAR(255) NOT NULL DEFAULT ''
, `tournament_attachment_id` INT NOT NULL DEFAULT 0
, `is_enabled` INT NOT NULL DEFAULT 0
, `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
, `created_by` VARCHAR(255)
, `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
, `updated_by` VARCHAR(255)
);
