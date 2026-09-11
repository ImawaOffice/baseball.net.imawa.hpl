-- SQLite用設定テーブル作成スクリプト
-- configテーブル作成
INSERT INTO `menu`
( `menu_id`, `parent_id`, `display_order`, `role_min`, `role_max`, `menu_name`, `menu_url`, `is_enabled`, `created_by`, `updated_by` )
VALUES
( 1000,    0, 1000,   0, 9999, 'HEADER', '', 1, 'system', 'system' ),
( 1010, 1000, 1010,   0, 9999, 'ホーム', './', 1, 'system', 'system' ),
( 1020, 1000, 1020,   0, 9999, '大会概要', './tournamentoverview', 1, 'system', 'system' ),
( 1030, 1000, 1030,   0, 9999, '試合結果', './tournamentresults', 1, 'system', 'system' ),
( 1040, 1000, 1040,   0, 9999, '参加申込', './teamentry', 1, 'system', 'system' ),
( 1050, 1000, 1050,   0, 9999, 'ルール', '#', 1, 'system', 'system' ),
( 1051, 1050, 1051,   0, 9999, '大会規定', './tournamentrules', 1, 'system', 'system' ),
( 1052, 1050, 1052,   0, 9999, '試合規定', './gamerules', 1, 'system', 'system' ),
( 1060, 1000, 1060,   0, 9999, 'お問合せ', './contact', 1, 'system', 'system' ),
( 1100, 1000, 1100,  10, 9999, '監督メニュー', '#', 1, 'system', 'system' ),
( 1101, 1100, 1101,  10, 9999, '試合結果登録', './gamemaintenance', 1, 'system', 'system' ),
( 1200, 1000, 1200, 100, 9999, '本部メニュー', '#', 1, 'system', 'system' ),
( 1201, 1200, 1201, 100, 9999, '大会登録', './tournamentmaintenance', 1, 'system', 'system' ),
( 1202, 1200, 1202, 100, 9999, '参加チーム登録', './teammaintenance', 1, 'system', 'system' ),
( 1203, 1200, 1203, 100, 9999, '試合結果登録', './gamemaintenance', 1, 'system', 'system' ),
( 1204, 1200, 1204, 100, 9999, '問合せ一覧', './contactlist', 1, 'system', 'system' ),
( 1300, 1000, 1300,   0,    0, 'サインイン', '#', 1, 'system', 'system' ),
( 1301, 1300, 1301,   0,    0, 'サインイン', './signin', 1, 'system', 'system' ),
( 1302, 1300, 1302,   0,    0, 'サインアップ', './signup', 1, 'system', 'system' ),
( 1400, 1000, 1400,   1, 9999, 'ユーザ情報', '#', 1, 'system', 'system' ),
( 1401, 1400, 1401,   1, 9999, 'プロフィール', './profile', 1, 'system', 'system' ),
( 1402, 1400, 1402,   1, 9999, 'サインアウト', './signout', 1, 'system', 'system' ),
( 2000,    0, 2000,   0, 9999, 'FOOTER', '', 1, 'system', 'system' ),
( 2010, 2000, 2010,   0, 9999, 'ホーム', './', 1, 'system', 'system' ),
( 2020, 2000, 2020,   0, 9999, '大会概要', './tournamentoverview', 1, 'system', 'system' ),
( 2030, 2000, 2030,   0, 9999, '試合結果', './tournamentresults', 1, 'system', 'system' ),
( 2040, 2000, 2040,   0, 9999, '参加申込', './teamentry', 1, 'system', 'system' ),
( 2050, 2000, 2050,   0, 9999, '大会規定', './tournamentrules', 1, 'system', 'system' ),
( 2060, 2000, 2060,   0, 9999, '試合規定', './gamerules', 1, 'system', 'system' ),
( 2070, 2000, 2070,   0, 9999, 'お問合せ', './contact', 1, 'system', 'system' ),
( 2100, 2000, 2100,  10, 9999, '試合結果登録', './gamemaintenance', 1, 'system', 'system' ),
( 2110, 2000, 2110, 100, 9999, '大会登録', './tournamentmaintenance', 1, 'system', 'system' ),
( 2120, 2000, 2120, 100, 9999, '参加チーム登録', './teammaintenance', 1, 'system', 'system' )
;