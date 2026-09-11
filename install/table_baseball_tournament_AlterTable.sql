-- mySQL用テーブル作成スクリプト
-- baseball_tournament テーブル作成
-- 複数の項目追加は逆順に記述する
ALTER TABLE `baseball_tournament`
  ADD COLUMN `tournament_attachment_id`	INT 			NOT NULL DEFAULT 0	AFTER `tournament_parent_id`
, ADD COLUMN `tournament3_text` 		VARCHAR(255) 	NOT NULL DEFAULT ''	AFTER `tournament_parent_id`
, ADD COLUMN `tournament3_teams` 		INT 			NOT NULL DEFAULT 0 	AFTER `tournament_parent_id`
, ADD COLUMN `tournament2_text` 		VARCHAR(255) 	NOT NULL DEFAULT ''	AFTER `tournament_parent_id`
, ADD COLUMN `tournament2_teams` 		INT 			NOT NULL DEFAULT 0 	AFTER `tournament_parent_id`
, ADD COLUMN `tournament1_text` 		VARCHAR(255) 	NOT NULL DEFAULT ''	AFTER `tournament_parent_id`
, ADD COLUMN `tournament1_teams` 		INT 			NOT NULL DEFAULT 0	AFTER `tournament_parent_id`
;
