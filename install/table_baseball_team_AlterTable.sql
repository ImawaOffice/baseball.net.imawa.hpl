-- mySQL用テーブル作成スクリプト
-- baseball_team テーブル作成
-- 複数の項目追加は逆順に記述する
ALTER TABLE `baseball_team`
  ADD COLUMN `tournament3_attend` INT NOT NULL DEFAULT 0 AFTER `tournament_id`
, ADD COLUMN `tournament2_attend` INT NOT NULL DEFAULT 0 AFTER `tournament_id`
, ADD COLUMN `tournament1_attend` INT NOT NULL DEFAULT 0 AFTER `tournament_id`
;
ALTER TABLE `baseball_team` ADD `team_meta` TEXT NULL AFTER `tournament3_attend` ;