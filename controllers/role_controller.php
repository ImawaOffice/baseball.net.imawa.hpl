<?php
/**
 * 権限管理のコントローラ
 *
 * 役割:
 * - 入力受け取り
 * - CSRF検証
 * - action判定
 * - Model呼び出し
 * - Viewへ渡すデータ作成
 */
require_once( __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'role_model.php' );

class RoleController {
    /**
     * @var RoleModel
     */
    private $model;

    /**
     * @var array
     */
    private $data;

    /**
     * コンストラクタ
     *
     * Model を保持して、画面ごとの処理を統括する。
     * ここでの役割は「業務実行の入口」であり、
     * 画面の表示を直接持たないようにする。
     *
     * @param Class_Database $db
     */
    public function __construct( $db ) {
        $this->model = new RoleModel( $db );
        $this->data = [
            'message' => '',
            'error' => '',
            'roleList' => [],
            'token' => '',
        ];
    }

    /**
     * 画面表示用データを準備する
     *
     * 画面初期表示時に一覧を取得して、View に渡すための準備を行う。
     * このメソッド自体は「表示の下準備」であって、DBの詳細処理は Model に委譲する。
     *
     * @return array
     */
    public function prepareViewData() {
        $this->data[ 'roleList' ] = $this->model->getRoleList();
        return $this->data;
    }

    /**
     * HTTP リクエストを処理して、View に渡すデータを返す
     *
     * 画面入口として使いやすい形に統一する。
     * ここでは「method」よりも「request」＋「token」のセットを受け取り、
     * 実行内容を判断する。これにより呼び出し側が自然に読みやすくなる。
     *
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
            $this->data[ 'roleList' ] = $this->model->getRoleList();
            return $this->data;
        }

        $processingGuard = (string)( $request[ 'processing_guard' ] ?? '' );
        $sessionGuard = (string)( $_SESSION[ 'ROLE_REQUEST_GUARD' ] ?? '' );
        $lockedGuard = (string)( $_SESSION[ 'ROLE_REQUEST_LOCK' ] ?? '' );
        $lockExpiredAt = (int)( $_SESSION[ 'ROLE_REQUEST_LOCK_EXPIRES_AT' ] ?? 0 );

        if( $processingGuard !== '' && $sessionGuard !== '' && $processingGuard === $sessionGuard && $lockedGuard === $processingGuard && $lockExpiredAt > time() ) {
            $this->data[ 'error' ] = '処理中のため、しばらく待ってから再度お試しください。';
            $this->data[ 'roleList' ] = $this->model->getRoleList();
            return $this->data;
        }

        if( $processingGuard !== '' ) {
            $_SESSION[ 'ROLE_REQUEST_LOCK' ] = $processingGuard;
            $_SESSION[ 'ROLE_REQUEST_LOCK_EXPIRES_AT' ] = time() + 10;
        }

        try {
            $postData = $this->normalizePostData( $request );
            $this->validateInput( $postData );

            if( $this->data[ 'error' ] !== '' ) {
                $this->data[ 'roleList' ] = $this->model->getRoleList();
                return $this->data;
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

            $this->data[ 'roleList' ] = $this->model->getRoleList();
            return $this->data;
        } finally {
            unset( $_SESSION[ 'ROLE_REQUEST_LOCK' ] );
            unset( $_SESSION[ 'ROLE_REQUEST_LOCK_EXPIRES_AT' ] );
            $_SESSION[ 'ROLE_REQUEST_GUARD' ] = bin2hex( random_bytes( 8 ) );
        }
    }

    /**
     * データを初期化する
     *
     * リクエストごとにメッセージとエラーを初期状態へ戻す。
     * これにより、直前のリクエストの状態が混ざらないようにする。
     */
    private function resetData() {
        $this->data[ 'message' ] = '';
        $this->data[ 'error' ] = '';
        $this->data[ 'roleList' ] = [];
    }

    /**
     * POST から必要な値を取り出して、
     * 画面入力として扱いやすい形へ整形する。
     *
     * @param array $post
     * @return array
     */
    private function normalizePostData( $post ) {
        return [
            'action' => (string)( $post[ 'action' ] ?? '' ),
            'role_id' => filter_var( $post[ 'role_id' ] ?? '', FILTER_VALIDATE_INT ),
            'role_name' => trim( (string)( $post[ 'role_name' ] ?? '' ) ),
            'role_level' => trim( (string)( $post[ 'role_level' ] ?? '' ) ),
            'is_enabled' => isset( $post[ 'is_enabled' ] ) ? 1 : 0,
            'user_id' => (string)( $_SESSION[ 'AUTH_USER_ID' ] ?? '' ),
        ];
    }

    /**
     * 入力値の基本チェックを行う
     *
     * ここでは画面入力の許容範囲を確認し、
     * ビジネスロジックへ進む前に不正値を防ぐ。
     *
     * @param array $postData
     */
    private function validateInput( $postData ) {
        $action = $postData[ 'action' ];
        $roleName = $postData[ 'role_name' ];
        $roleLevel = $postData[ 'role_level' ];
        $roleId = $postData[ 'role_id' ];

        if( ! in_array( $action, [ 'add', 'update' ], true ) ) {
            $this->data[ 'error' ] = '処理内容が不正です。';
        } elseif( $roleName === '' || mb_strlen( $roleName ) > 255 ) {
            $this->data[ 'error' ] = '権限名は1～255文字で入力してください。';
        } elseif( $roleLevel === '' || ! preg_match( '/^-?\d+$/', $roleLevel ) ) {
            $this->data[ 'error' ] = '権限レベルは整数で入力してください。';
        } elseif( $action === 'update' && ( $roleId === false || $roleId === null ) ) {
            $this->data[ 'error' ] = '更新対象の権限が不正です。';
        }
    }

    /**
     * 新規追加処理を実行する
     *
     * 重複チェックとINSERT処理を分離しており、
     * コントローラは「どこで何を判定するか」の流れを担う。
     *
     * @param array $postData
     */
    private function handleAdd( $postData ) {
        $roleLevel = (int)$postData[ 'role_level' ];
        $roleName = $postData[ 'role_name' ];
        $userId = $postData[ 'user_id' ];

        if( $this->model->isDuplicateRoleLevel( $roleLevel ) || $this->model->isDuplicateRoleName( $roleName ) ) {
            $this->data[ 'error' ] = '権限名または権限レベルが既に登録されています。';
            return;
        }

        $result = $this->model->addRole( $roleName, $roleLevel, $userId );
        if( $result === false ) {
            $this->data[ 'error' ] = '権限の登録に失敗しました。';
            return;
        }

        $this->data[ 'message' ] = '権限を登録しました。';
    }

    /**
     * 更新処理を実行する
     *
     * 権限の存在確認、重複禁止、管理者保護などの業務判定を行い、
     * 最後に Model の updateRole を呼び出す。
     *
     * @param array $postData
     */
    private function handleUpdate( $postData ) {
        $roleId = (int)$postData[ 'role_id' ];
        $roleName = $postData[ 'role_name' ];
        $roleLevel = (int)$postData[ 'role_level' ];
        $isEnabled = (int)$postData[ 'is_enabled' ];
        $userId = $postData[ 'user_id' ];

        $current = $this->model->getRoleById( $roleId );
        if( empty( $current ) ) {
            $this->data[ 'error' ] = '更新対象の権限が見つかりません。';
            return;
        }

        $currentRoleLevel = (int)( $current[ 'role_level' ] ?? 0 );
        $activeUsers = $this->model->getActiveUserCountByRoleLevel( $currentRoleLevel );

        if( $this->model->isDuplicateRoleName( $roleName, $roleId ) ) {
            $this->data[ 'error' ] = '権限名が既に登録されています。';
            return;
        }

        if( $this->model->isDuplicateRoleLevel( $roleLevel, $roleId ) ) {
            $this->data[ 'error' ] = '権限レベルが既に登録されています。';
            return;
        }

        if( $currentRoleLevel === 1000 && $isEnabled === 0 && $activeUsers > 0 ) {
            $this->data[ 'error' ] = '利用中のシステム管理者権限は無効化できません。';
            return;
        }

        if( $currentRoleLevel === 1000 && $isEnabled === 0 ) {
            $adminRoleCount = $this->model->getAdminRoleCountExcept( $roleId );
            if( $adminRoleCount === 0 ) {
                $this->data[ 'error' ] = '最後のシステム管理者権限は無効化できません。';
                return;
            }
        }

        $result = $this->model->updateRole( $roleId, $roleName, $roleLevel, $isEnabled, $userId );
        if( $result === false ) {
            $this->data[ 'error' ] = '権限の更新に失敗しました。';
            return;
        }

        $this->data[ 'message' ] = '権限を更新しました。';
    }

    /**
     * トークンを返す
     *
     * 画面側で hidden フィールドに埋め込むために使う。
     * 将来的には view 側で直接セットできるように整理する余地がある。
     *
     * @return string
     */
    public function getToken() {
        return $this->data[ 'token' ];
    }
}
