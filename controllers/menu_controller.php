<?php
/**
 * メニュー管理のコントローラ
 *
 * 役割:
 * - 入力受け取り
 * - CSRF検証
 * - action判定
 * - Model呼び出し
 * - Viewへ渡すデータ作成
 */
require_once( __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'menu_model.php' );

class MenuController {
    /**
     * @var MenuModel
     */
    private $model;

    /**
     * @var array
     */
    private $data;

    /**
     * @param Class_Database $db
     */
    public function __construct( $configDb, $roleDb ) {
        $this->model = new MenuModel( $configDb, $roleDb );
        $this->data = [
            'message' => '',
            'error' => '',
            'menuList' => [],
            'roleLevelOptions' => [],
        ];
    }

    /**
     * @return array
     */
    public function prepareViewData() {
        $this->data[ 'menuList' ] = $this->model->getMenuList();
        // 権限マスター(baseball_role)に存在する権限レベルを選択候補として返す。
        $this->data[ 'roleLevelOptions' ] = $this->model->getRoleLevelOptions();
        return $this->data;
    }

    /**
     * @param array $request
     * @param string $token
     * @return array
     */
    public function executeRequest( $request, $token = '' ) {
        $this->resetData();

        if( empty( $request ) ) {
            return $this->prepareViewData();
        }

        $requestToken = (string)( $request[ 'token' ] ?? '' );
        if( ! verifyCsrfOrFail( $requestToken, $token ) ) {
            $this->data[ 'error' ] = '不正なリクエストです。';
            return $this->prepareViewData();
        }

        $processingGuard = (string)( $request[ 'processing_guard' ] ?? '' );
        $sessionGuard = (string)( $_SESSION[ 'MENU_REQUEST_GUARD' ] ?? '' );
        $lockedGuard = (string)( $_SESSION[ 'MENU_REQUEST_LOCK' ] ?? '' );
        $lockExpiredAt = (int)( $_SESSION[ 'MENU_REQUEST_LOCK_EXPIRES_AT' ] ?? 0 );

        if( $processingGuard !== '' && $sessionGuard !== '' && $processingGuard === $sessionGuard && $lockedGuard === $processingGuard && $lockExpiredAt > time() ) {
            $this->data[ 'error' ] = '処理中のため、しばらく待ってから再度お試しください。';
            return $this->prepareViewData();
        }

        if( $processingGuard !== '' ) {
            $_SESSION[ 'MENU_REQUEST_LOCK' ] = $processingGuard;
            $_SESSION[ 'MENU_REQUEST_LOCK_EXPIRES_AT' ] = time() + 10;
        }

        try {
            $postData = $this->normalizePostData( $request );
            $this->validateInput( $postData );

            if( $this->data[ 'error' ] !== '' ) {
                return $this->prepareViewData();
            }

            switch( $postData[ 'action' ] ) {
                case 'add':
                    $this->handleAdd( $postData );
                    break;
                case 'update':
                    $this->handleUpdate( $postData );
                    break;
                default:
                    $this->data[ 'error' ] = '処理内容が不正です。';
                    break;
            }

            return $this->prepareViewData();
        } finally {
            unset( $_SESSION[ 'MENU_REQUEST_LOCK' ] );
            unset( $_SESSION[ 'MENU_REQUEST_LOCK_EXPIRES_AT' ] );
            $_SESSION[ 'MENU_REQUEST_GUARD' ] = bin2hex( random_bytes( 8 ) );
        }
    }

    /**
     * @return void
     */
    private function resetData() {
        $this->data[ 'message' ] = '';
        $this->data[ 'error' ] = '';
        $this->data[ 'menuList' ] = [];
        $this->data[ 'roleLevelOptions' ] = [];
    }

    /**
     * @param array $post
     * @return array
     */
    private function normalizePostData( $post ) {
        return [
            'action' => (string)( $post[ 'action' ] ?? '' ),
            'menu_id' => filter_var( $post[ 'menu_id' ] ?? '', FILTER_VALIDATE_INT ),
            // 編集時に元の主キーを保持し、menu_id変更可とする。
            'target_menu_id' => filter_var( $post[ 'target_menu_id' ] ?? '', FILTER_VALIDATE_INT ),
            'parent_id' => filter_var( $post[ 'parent_id' ] ?? '', FILTER_VALIDATE_INT ),
            'display_order' => trim( (string)( $post[ 'display_order' ] ?? '' ) ),
            'role_min' => trim( (string)( $post[ 'role_min' ] ?? '' ) ),
            'role_max' => trim( (string)( $post[ 'role_max' ] ?? '' ) ),
            'menu_name' => trim( (string)( $post[ 'menu_name' ] ?? '' ) ),
            'menu_url' => trim( (string)( $post[ 'menu_url' ] ?? '' ) ),
            'is_enabled' => isset( $post[ 'is_enabled' ] ) ? 1 : 0,
            'user_id' => (string)( $_SESSION[ 'AUTH_USER_ID' ] ?? '' ),
        ];
    }

    /**
     * @param array $postData
     * @return void
     */
    private function validateInput( $postData ) {
        $action = $postData[ 'action' ];
        $menuId = $postData[ 'menu_id' ];
        $targetMenuId = $postData[ 'target_menu_id' ];
        $parentId = $postData[ 'parent_id' ];
        $displayOrder = $postData[ 'display_order' ];
        $roleMin = $postData[ 'role_min' ];
        $roleMax = $postData[ 'role_max' ];
        $menuName = $postData[ 'menu_name' ];
        $menuUrl = $postData[ 'menu_url' ];

        if( ! in_array( $action, [ 'add', 'update' ], true ) ) {
            $this->data[ 'error' ] = '処理内容が不正です。';
        } elseif( $action === 'update' && ( $targetMenuId === false || $targetMenuId === null || (int)$targetMenuId <= 0 ) ) {
            $this->data[ 'error' ] = '更新対象のメニューIDが不正です。';
        } elseif( $menuId === false || $menuId === null || (int)$menuId <= 0 ) {
            $this->data[ 'error' ] = 'メニューIDは1以上の整数で入力してください。';
        } elseif( $parentId === false || $parentId === null || (int)$parentId < 0 ) {
            $this->data[ 'error' ] = '親メニューIDは0以上の整数で入力してください。';
        } elseif( $menuName === '' || mb_strlen( $menuName ) > 255 ) {
            $this->data[ 'error' ] = 'メニュー名は1～255文字で入力してください。';
        } elseif( mb_strlen( $menuUrl ) > 255 ) {
            $this->data[ 'error' ] = 'URLは255文字以内で入力してください。';
        } elseif( $displayOrder === '' || ! preg_match( '/^-?\d+$/', $displayOrder ) ) {
            $this->data[ 'error' ] = '表示順は整数で入力してください。';
        } elseif( $roleMin === '' || ! preg_match( '/^-?\d+$/', $roleMin ) ) {
            $this->data[ 'error' ] = '最小権限は整数で入力してください。';
        } elseif( $roleMax === '' || ! preg_match( '/^-?\d+$/', $roleMax ) ) {
            $this->data[ 'error' ] = '最大権限は整数で入力してください。';
        } elseif( (int)$roleMin > (int)$roleMax ) {
            $this->data[ 'error' ] = '最小権限は最大権限以下で入力してください。';
        } elseif( ! $this->model->existsRoleLevel( (int)$roleMin ) ) {
            $this->data[ 'error' ] = '指定された最小権限が存在しません。';
        } elseif( ! $this->model->existsRoleLevel( (int)$roleMax ) ) {
            $this->data[ 'error' ] = '指定された最大権限が存在しません。';
        }
    }

    /**
     * @param array $postData
     * @return bool
     */
    private function validateRelation( $menuId, $postData ) {
        $parentId = (int)$postData[ 'parent_id' ];

        // 親メニューIDは入力値として受け取り、0以外は実在チェックする。
        if( $parentId !== 0 && ! $this->model->existsMenuId( $parentId ) ) {
            $this->data[ 'error' ] = '指定された親メニューが存在しません。';
            return false;
        }

        if( $menuId === $parentId ) {
            $this->data[ 'error' ] = 'メニュー自身を親メニューに設定できません。';
            return false;
        }

        if( $this->model->hasCycle( $menuId, $parentId ) ) {
            $this->data[ 'error' ] = '循環参照となるため親メニューを設定できません。';
            return false;
        }

        return true;
    }

    /**
     * @param array $postData
     * @return void
     */
    private function handleAdd( $postData ) {
        $menuId = (int)$postData[ 'menu_id' ];

        if( $this->model->existsMenuId( $menuId ) ) {
            $this->data[ 'error' ] = '指定されたメニューIDは既に登録されています。';
            return;
        }

        if( ! $this->validateRelation( $menuId, $postData ) ) {
            return;
        }

        $result = $this->model->addMenu(
            $menuId,
            (int)$postData[ 'parent_id' ],
            (int)$postData[ 'display_order' ],
            (int)$postData[ 'role_min' ],
            (int)$postData[ 'role_max' ],
            $postData[ 'menu_name' ],
            $postData[ 'menu_url' ],
            (int)$postData[ 'is_enabled' ],
            $postData[ 'user_id' ]
        );

        if( $result === false ) {
            $this->data[ 'error' ] = 'メニューの登録に失敗しました。';
            return;
        }

        $this->data[ 'message' ] = 'メニューを登録しました。';
    }

    /**
     * @param array $postData
     * @return void
     */
    private function handleUpdate( $postData ) {
        $newMenuId = (int)$postData[ 'menu_id' ];
        $targetMenuId = (int)$postData[ 'target_menu_id' ];
        $current = $this->model->getMenuById( $targetMenuId );

        if( empty( $current ) ) {
            $this->data[ 'error' ] = '更新対象のメニューが見つかりません。';
            return;
        }

        if( $newMenuId !== $targetMenuId && $this->model->existsMenuId( $newMenuId ) ) {
            // 主キー変更時は重複を許可しない。
            $this->data[ 'error' ] = '指定されたメニューIDは既に登録されています。';
            return;
        }

        if( ! $this->validateRelation( $targetMenuId, $postData ) ) {
            return;
        }

        $result = $this->model->updateMenu(
            $targetMenuId,
            $newMenuId,
            (int)$postData[ 'parent_id' ],
            (int)$postData[ 'display_order' ],
            (int)$postData[ 'role_min' ],
            (int)$postData[ 'role_max' ],
            $postData[ 'menu_name' ],
            $postData[ 'menu_url' ],
            (int)$postData[ 'is_enabled' ],
            $postData[ 'user_id' ]
        );

        if( $result === false ) {
            $this->data[ 'error' ] = 'メニューの更新に失敗しました。';
            return;
        }

        $this->data[ 'message' ] = 'メニューを更新しました。';
    }
}
