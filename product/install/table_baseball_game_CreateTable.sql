-- mySQL用テーブル作成スクリプト
-- baseball_game テーブル作成
CREATE TABLE IF NOT EXISTS `baseball_game`
(
  `game_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY
, `tournament_id` INT NOT NULL
, `game_class` INT NOT NULL DEFAULT 0
, `game_block` INT NOT NULL DEFAULT 1
, `game_count` INT NOT NULL DEFAULT 1
, `game_name` VARCHAR(255) NOT NULL
, `game_date` DATETIME NOT NULL
, `game_place` VARCHAR(255) NOT NULL
, `winner_id` INT NOT NULL
, `loser_id` INT NOT NULL
, `team1_id` INT NOT NULL
, `team2_id` INT NOT NULL
, `team1_name` VARCHAR(255) NOT NULL
, `team2_name` VARCHAR(255) NOT NULL
, `team1_score` INT NOT NULL
, `team2_score` INT NOT NULL
, `team1_last_game_id` INT NOT NULL
, `team2_last_game_id` INT NOT NULL
, `is_enabled` INT NOT NULL DEFAULT 0
, `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
, `created_by` VARCHAR(255)
, `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
, `updated_by` VARCHAR(255)
);
