-- SQLite用設定テーブル作成スクリプト
-- configテーブル作成
INSERT INTO config
( config_key, config_value, config_type )
VALUES
  ( 'CopyRight', '&copy; HPL All Rights Reserved.', 'TEXT' )
, ( 'SMTP_Host', 'smtp.lolipop.jp', 'TEXT' )
, ( 'SMTP_Username', 'hpl@imawa.net', 'TEXT' )
, ( 'SMTP_Password', 'Guinsaga-098', 'TEXT' )
, ( 'SMTP_Port', '465', 'INTEGER' )
, ( 'SMTP_Secure', 'ssl', 'TEXT' )
, ( 'Mail_FromAddress', 'hpl@imawa.net', 'TEXT' )
, ( 'Mail_FromName', 'HPL Baseball', 'TEXT' )
, ( 'DbType', 'mysql', 'TEXT' )
, ( 'DbHostName', '127.0.0.1', 'TEXT' )
, ( 'DbPort', '3306', 'INTEGER' )
, ( 'DbName', 'LAA0938815-hpl', 'TEXT' )
, ( 'DbUsername', 'LAA0938815', 'TEXT' )
, ( 'DbPassword', 'Guinsaga-098', 'TEXT' )


