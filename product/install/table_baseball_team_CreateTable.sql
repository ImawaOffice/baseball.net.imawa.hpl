-- mySQL用テーブル作成スクリプト
-- baseball_team テーブル作成
CREATE TABLE IF NOT EXISTS `baseball_team`
(
  `team_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY
, `team_name` VARCHAR(255) NOT NULL
, `team_manager` VARCHAR(255) NOT NULL
, `team_contact` VARCHAR(255) NOT NULL
, `team_access_cd` VARCHAR(255) NOT NULL
, `tournament_id` INT NOT NULL
, `is_enabled` INT NOT NULL DEFAULT 0
, `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
, `created_by` VARCHAR(255)
, `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
, `updated_by` VARCHAR(255)
);
