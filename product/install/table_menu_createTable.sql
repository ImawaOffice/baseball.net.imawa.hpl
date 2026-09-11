-- SQLite用設定テーブル作成スクリプト
-- configテーブル作成
CREATE TABLE IF NOT EXISTS `menu`
(
  `menu_id` INTEGER NOT NULL
, `menu_name` TEXT NOT NULL
, `menu_url` TEXT NOT NULL
, `parent_id` INTEGER NOT NULL DEFAULT 0
, `display_order` INTEGER NOT NULL DEFAULT 0
, `role_min` INTEGER NOT NULL DEFAULT 0
, `role_max` INTEGER NOT NULL DEFAULT 0
, `is_enabled` INTEGER NOT NULL DEFAULT 0
, `created_at` TEXT NOT NULL DEFAULT ( datetime( 'now', 'localtime' ) )
, `created_by` TEXT
, `updated_at` TEXT NOT NULL DEFAULT ( datetime( 'now', 'localtime' ) )
, `updated_by` TEXT
, PRIMARY KEY( `menu_id` )
);

-- updated_atを自動更新するトリガー
CREATE TRIGGER IF NOT EXISTS trigger_menu_updated_at_after_update
AFTER UPDATE ON menu
FOR EACH ROW
BEGIN
  UPDATE menu SET updated_at = datetime( 'now', 'localtime' ) WHERE menu_id = OLD.menu_id;
END;
