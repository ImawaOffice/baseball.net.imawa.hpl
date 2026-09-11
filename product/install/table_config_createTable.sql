-- SQLite用設定テーブル作成スクリプト
-- configテーブル作成
CREATE TABLE IF NOT EXISTS config
(
  config_key TEXT NOT NULL
, config_value TEXT NOT NULL
, config_type TEXT NOT NULL
, is_enabled INTEGER NOT NULL DEFAULT 1
, created_at TEXT NOT NULL DEFAULT ( datetime( 'now', 'localtime' ) )
, created_by TEXT
, updated_at TEXT NOT NULL DEFAULT ( datetime( 'now', 'localtime' ) )
, updated_by TEXT
, PRIMARY KEY( config_key )
);

-- updated_atを自動更新するトリガー
CREATE TRIGGER IF NOT EXISTS trigger_config_updated_at_after_update
AFTER UPDATE ON config
FOR EACH ROW
BEGIN
  UPDATE config SET updated_at = datetime( 'now', 'localtime' ) WHERE config_key = OLD.config_key;
END;
