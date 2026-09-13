<?php
/**
 * 権限管理のモデル
 *
 * 役割:
 * - DBアクセス
 * - 権限関連の業務判定
 * - 画面に依存しない処理を担当
 */
class RoleModel {
    /**
     * @var Class_Database
     */
    private $db;

    /**
     * コンストラクタ
     *
     * @param Class_Database $db
     */
    public function __construct( $db ) {
        $this->db = $db;
    }

    /**
     * 権限一覧を取得する
     *
     * @return array
     */
    public function getRoleList() {
        $sql = 'SELECT role_id, role_name, role_level, is_enabled, created_at, updated_at
                FROM baseball_role
                ORDER BY role_level DESC, role_id ASC';

        $result = $this->db->select( $sql );
        return $result === false ? [] : $result;
    }

    /**
     * 指定された権限IDの権限を1件取得する
     *
     * @param int $roleId
     * @return array
     */
    public function getRoleById( $roleId ) {
        $sql = 'SELECT role_id, role_name, role_level, is_enabled
                FROM baseball_role
                WHERE role_id = :role_id
                LIMIT 1';

        $params = [
            'role_id' => (int)$roleId,
        ];

        $result = $this->db->select( $sql, $params );
        return $result === false || count( $result ) === 0 ? [] : $result[0];
    }

    /**
     * 権限名の重複チェック
     *
     * @param string $roleName
     * @param int|null $excludeRoleId
     * @return bool
     */
    public function isDuplicateRoleName( $roleName, $excludeRoleId = null ) {
        $sql = 'SELECT role_id
                FROM baseball_role
                WHERE role_name = :role_name';
        $params = [
            'role_name' => trim( (string)$roleName ),
        ];

        if( $excludeRoleId !== null ) {
            $sql .= ' AND role_id <> :role_id';
            $params[ 'role_id' ] = (int)$excludeRoleId;
        }

        $sql .= ' LIMIT 1';

        $result = $this->db->select( $sql, $params );
        return $result !== false && count( $result ) > 0;
    }

    /**
     * 権限レベルの重複チェック
     *
     * @param int $roleLevel
     * @param int|null $excludeRoleId
     * @return bool
     */
    public function isDuplicateRoleLevel( $roleLevel, $excludeRoleId = null ) {
        $sql = 'SELECT role_id
                FROM baseball_role
                WHERE role_level = :role_level';
        $params = [
            'role_level' => (int)$roleLevel,
        ];

        if( $excludeRoleId !== null ) {
            $sql .= ' AND role_id <> :role_id';
            $params[ 'role_id' ] = (int)$excludeRoleId;
        }

        $sql .= ' LIMIT 1';

        $result = $this->db->select( $sql, $params );
        return $result !== false && count( $result ) > 0;
    }

    /**
     * 権限を追加する
     *
     * @param string $roleName
     * @param int $roleLevel
     * @param string $userId
     * @return bool
     */
    public function addRole( $roleName, $roleLevel, $userId ) {
        $sql = 'INSERT INTO baseball_role (role_name, role_level, is_enabled, created_by, updated_by)
                VALUES (:role_name, :role_level, 1, :created_by, :updated_by)';

        $params = [
            'role_name'  => trim( (string)$roleName ),
            'role_level' => (int)$roleLevel,
            'created_by' => (string)$userId,
            'updated_by' => (string)$userId,
        ];

        $result = $this->db->execute2( $sql, $params );
        return $result !== false;
    }

    /**
     * 権限を更新する
     *
     * @param int $roleId
     * @param string $roleName
     * @param int $isEnabled
     * @param string $userId
     * @return bool
     */
    public function updateRole( $roleId, $roleName, $roleLevel, $isEnabled, $userId ) {
        $sql = 'UPDATE baseball_role
                SET role_name = :role_name,
                    role_level = :role_level,
                    is_enabled = :is_enabled,
                    updated_by = :updated_by
                WHERE role_id = :role_id';

        $params = [
            'role_name'  => trim( (string)$roleName ),
            'role_level' => (int)$roleLevel,
            'is_enabled' => (int)$isEnabled,
            'updated_by' => (string)$userId,
            'role_id'    => (int)$roleId,
        ];

        $result = $this->db->execute( $sql, $params );
        return $result !== false;
    }

    /**
     * 指定された権限レベルを持つ利用中ユーザー数を取得する
     *
     * @param int $roleLevel
     * @return int
     */
    public function getActiveUserCountByRoleLevel( $roleLevel ) {
        $sql = 'SELECT COUNT(*) AS cnt
                FROM baseball_user
                WHERE role_level = :role_level
                  AND is_enabled = 1';

        $params = [
            'role_level' => (int)$roleLevel,
        ];

        $result = $this->db->select( $sql, $params );
        if( $result === false || count( $result ) === 0 ) {
            return 0;
        }

        return (int)( $result[0][ 'cnt' ] ?? 0 );
    }

    /**
     * 指定されたロールIDを除いた管理者権限の件数を取得する
     *
     * @param int $roleId
     * @return int
     */
    public function getAdminRoleCountExcept( $roleId ) {
        $sql = 'SELECT COUNT(*) AS cnt
                FROM baseball_role
                WHERE role_level = 1000
                  AND is_enabled = 1
                  AND role_id <> :role_id';

        $params = [
            'role_id' => (int)$roleId,
        ];

        $result = $this->db->select( $sql, $params );
        if( $result === false || count( $result ) === 0 ) {
            return 0;
        }

        return (int)( $result[0][ 'cnt' ] ?? 0 );
    }
}
