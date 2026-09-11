-- mySQL用テーブル作成スクリプト
-- baseball_user テーブル作成
CREATE TABLE IF NOT EXISTS `baseball_user` (
  `user_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_cd` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `username` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `role_level` INT NOT NULL DEFAULT 0,
  `verification_cd` VARCHAR(255) NOT NULL DEFAULT '',
  `verification_period` INT NOT NULL DEFAULT 0,
  `signin_at` DATETIME NULL,
  `signin_by` VARCHAR(255) NULL,
  `is_enabled` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` VARCHAR(255) NULL,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` VARCHAR(255) NULL,
  UNIQUE ( `user_cd` )
);