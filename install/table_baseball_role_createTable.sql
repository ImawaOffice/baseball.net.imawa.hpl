-- mySQL用テーブル作成スクリプト
-- baseball_role テーブル作成
CREATE TABLE IF NOT EXISTS `baseball_role` (
  `role_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY
, `role_name` VARCHAR( 255 ) NOT NULL
, `role_level` INT NOT NULL
, `is_enabled` INT NOT NULL DEFAULT 0
, `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
, `created_by` VARCHAR( 255 )
, `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
, `updated_by` VARCHAR( 255)
, UNIQUE ( `role_level` )
);
