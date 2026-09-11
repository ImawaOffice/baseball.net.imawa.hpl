TRUNCATE TABLE `baseball_role`;
INSERT INTO `baseball_role`
( `role_name`, `role_level`, `is_enabled`, `created_by`, `updated_by` )
VALUES
( '一般', 0, 1, 'system', 'system' ),
( '登録済み', 1, 1, 'system', 'system' ),
( '監督', 10, 1, 'system', 'system' ),
( '本部', 100, 1, 'system', 'system' ),
( 'システム管理者', 1000, 1, 'system', 'system' )
ON DUPLICATE KEY UPDATE
  role_name = VALUES( role_name )
, role_level = VALUES( role_level )
, is_enabled = VALUES( is_enabled )
, updated_by = VALUES( updated_by )
;
