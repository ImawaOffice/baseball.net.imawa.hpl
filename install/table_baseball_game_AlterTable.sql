-- mySQL用テーブル作成スクリプト
-- baseball_game テーブル作成
ALTER TABLE `baseball_game`
  CHANGE COLUMN `team1_next_game_id` `team1_last_game_id` INT NOT NULL DEFAULT 0
, CHANGE COLUMN `team2_next_game_id` `team2_last_game_id` INT NOT NULL DEFAULT 0
;