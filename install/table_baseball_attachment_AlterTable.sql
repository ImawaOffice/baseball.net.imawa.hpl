-- mySQL用テーブル作成スクリプト
-- baseball_attachment テーブル作成
ALTER TABLE `baseball_attachment`
   ADD COLUMN `attachment_type` INT NOT NULL DEFAULT 0 AFTER `attachment_id`
;
