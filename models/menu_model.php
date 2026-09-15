<?php
/**
 * メニュー管理のモデル
 *
 * 役割:
 * - menu テーブルへのDBアクセス
 * - 階層整合性チェックに必要なデータ提供
 */
class MenuModel {
    /**
     * @var Class_Database
     */
    // menuテーブル(設定DB/SQLite)用
    private $configDb;

    /**
     * @var Class_Database
     */
    // baseball_role(業務DB/MySQL)用
    private $roleDb;

    /**
     * @param Class_Database $db
     */
    public function __construct( $configDb, $roleDb ) {
        $this->configDb = $configDb;
        $this->roleDb = $roleDb;
    }

    /**
     * メニュー一覧を取得する
     *
     * @return array
     */
    public function getMenuList() {
        $sql = 'SELECT m.menu_id,
                       m.parent_id,
                       p.menu_name AS parent_name,
                       m.display_order,
                       m.role_min,
                       m.role_max,
                       m.menu_name,
                       m.menu_url,
                       m.is_enabled,
                       m.updated_at
                FROM menu m
                LEFT JOIN menu p ON p.menu_id = m.parent_id
                ORDER BY m.parent_id ASC, m.display_order ASC, m.menu_id ASC';

        $result = $this->configDb->select( $sql );
        return $result === false ? [] : $result;
    }

    /**
     * 権限レベル選択肢を取得する
     *
     * @return array
     */
    public function getRoleLevelOptions() {
        // 権限マスターに存在する権限レベルをそのまま候補として返す。
        $sql = 'SELECT role_level, role_name
                FROM baseball_role
                ORDER BY role_level ASC, role_id ASC';

        $result = $this->roleDb->select( $sql );
        return $result === false ? [] : $result;
    }

    /**
     * 指定権限レベルが存在するか
     *
     * @param int $roleLevel
     * @return bool
     */
    public function existsRoleLevel( $roleLevel ) {
        $sql = 'SELECT role_level
                FROM baseball_role
                                WHERE role_level = :role_level
                LIMIT 1';

        $params = [
            'role_level' => (int)$roleLevel,
        ];

        $result = $this->roleDb->select( $sql, $params );
        return $result !== false && count( $result ) > 0;
    }

    /**
     * 指定メニューを1件取得する
     *
     * @param int $menuId
     * @return array
     */
    public function getMenuById( $menuId ) {
        $sql = 'SELECT menu_id, parent_id, display_order, role_min, role_max, menu_name, menu_url, is_enabled
                FROM menu
                WHERE menu_id = :menu_id
                LIMIT 1';

        $params = [
            'menu_id' => (int)$menuId,
        ];

        $result = $this->configDb->select( $sql, $params );
        return $result === false || count( $result ) === 0 ? [] : $result[0];
    }

    /**
     * 指定メニューIDが存在するか
     *
     * @param int $menuId
     * @return bool
     */
    public function existsMenuId( $menuId ) {
        $sql = 'SELECT menu_id
                FROM menu
                WHERE menu_id = :menu_id
                LIMIT 1';

        $params = [
            'menu_id' => (int)$menuId,
        ];

        $result = $this->configDb->select( $sql, $params );
        return $result !== false && count( $result ) > 0;
    }

    /**
     * 親メニューIDを取得する
     *
     * @param int $menuId
     * @return int|null
     */
    public function getParentId( $menuId ) {
        $sql = 'SELECT parent_id
                FROM menu
                WHERE menu_id = :menu_id
                LIMIT 1';

        $params = [
            'menu_id' => (int)$menuId,
        ];

        $result = $this->configDb->select( $sql, $params );
        if( $result === false || count( $result ) === 0 ) {
            return null;
        }

        return (int)( $result[0][ 'parent_id' ] ?? 0 );
    }

    /**
     * 循環参照が発生するかを判定する
     *
     * @param int $menuId
     * @param int $parentId
     * @return bool
     */
    public function hasCycle( $menuId, $parentId ) {
        $targetId = (int)$menuId;
        $currentParent = (int)$parentId;
        $limit = 1000;

        while( $currentParent !== 0 && $limit > 0 ) {
            if( $currentParent === $targetId ) {
                return true;
            }

            $nextParent = $this->getParentId( $currentParent );
            if( $nextParent === null ) {
                return false;
            }

            $currentParent = (int)$nextParent;
            $limit--;
        }

        return false;
    }

    /**
     * メニューを追加する
     *
     * @param int $menuId
     * @param int $parentId
     * @param int $displayOrder
     * @param int $roleMin
     * @param int $roleMax
     * @param string $menuName
     * @param string $menuUrl
     * @param int $isEnabled
     * @param string $userId
     * @return bool
     */
    public function addMenu( $menuId, $parentId, $displayOrder, $roleMin, $roleMax, $menuName, $menuUrl, $isEnabled, $userId ) {
        $sql = 'INSERT INTO menu (menu_id, parent_id, display_order, role_min, role_max, menu_name, menu_url, is_enabled, created_by, updated_by)
                VALUES (:menu_id, :parent_id, :display_order, :role_min, :role_max, :menu_name, :menu_url, :is_enabled, :created_by, :updated_by)';

        $params = [
            'menu_id' => (int)$menuId,
            'parent_id' => (int)$parentId,
            'display_order' => (int)$displayOrder,
            'role_min' => (int)$roleMin,
            'role_max' => (int)$roleMax,
            'menu_name' => trim( (string)$menuName ),
            'menu_url' => trim( (string)$menuUrl ),
            'is_enabled' => (int)$isEnabled,
            'created_by' => (string)$userId,
            'updated_by' => (string)$userId,
        ];

        $result = $this->configDb->execute( $sql, $params );
        return $result !== false;
    }

    /**
     * メニューを更新する
     *
     * @param int $menuId
     * @param int $parentId
     * @param int $displayOrder
     * @param int $roleMin
     * @param int $roleMax
     * @param string $menuName
     * @param string $menuUrl
     * @param int $isEnabled
     * @param string $userId
     * @return bool
     */
    public function updateMenu( $targetMenuId, $newMenuId, $parentId, $displayOrder, $roleMin, $roleMax, $menuName, $menuUrl, $isEnabled, $userId ) {
        $sql = 'UPDATE menu
                SET menu_id = :new_menu_id,
                    parent_id = :parent_id,
                    display_order = :display_order,
                    role_min = :role_min,
                    role_max = :role_max,
                    menu_name = :menu_name,
                    menu_url = :menu_url,
                    is_enabled = :is_enabled,
                    updated_by = :updated_by
                WHERE menu_id = :target_menu_id';

        $params = [
            'target_menu_id' => (int)$targetMenuId,
            'new_menu_id' => (int)$newMenuId,
            'parent_id' => (int)$parentId,
            'display_order' => (int)$displayOrder,
            'role_min' => (int)$roleMin,
            'role_max' => (int)$roleMax,
            'menu_name' => trim( (string)$menuName ),
            'menu_url' => trim( (string)$menuUrl ),
            'is_enabled' => (int)$isEnabled,
            'updated_by' => (string)$userId,
        ];

        $result = $this->configDb->execute( $sql, $params );
        if( $result === false ) {
            return false;
        }

        if( (int)$targetMenuId !== (int)$newMenuId ) {
            // 親子関係を維持するため、ID変更時は子のparent_idも追従更新する。
            $childSql = 'UPDATE menu
                         SET parent_id = :new_menu_id,
                             updated_by = :updated_by
                         WHERE parent_id = :target_menu_id';
            $childParams = [
                'new_menu_id' => (int)$newMenuId,
                'target_menu_id' => (int)$targetMenuId,
                'updated_by' => (string)$userId,
            ];

            $childResult = $this->configDb->execute( $childSql, $childParams );
            if( $childResult === false ) {
                return false;
            }
        }

        return true;
    }
}
