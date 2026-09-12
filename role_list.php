<?php
# **********************************************************
# 権限マスタ管理
# **********************************************************

# ==========================================================
# 必要なファイル読み込み
# ==========================================================
require_once( __DIR__ . DIRECTORY_SEPARATOR . 'baseball_function.php' );

# ==========================================================
# 初期設定
# ==========================================================
$g_Log->notice( "S : " . basename( __FILE__ ), __FUNCTION__, basename( __FILE__ ) );

if( ! isAuthenticated() ) {
    $g_Log->notice( "認証されていないためログインページへリダイレクト", __FUNCTION__, basename( __FILE__ ) );
    header( "Location: ./signin" );
    exit();
}

// 権限マスタはシステム管理者のみ操作可能
if( (int)( $_SESSION[ 'AUTH_ROLE_LEVEL' ] ?? 0 ) !== 1000 ) {
    $g_Log->notice( "権限不足のためエラーページへリダイレクト", __FUNCTION__, basename( __FILE__ ) );
    header( "Location: ./error" );
    exit();
}

if( empty( $_SESSION[ 'ROLE_LIST_TOKEN' ] ) ) {
    $_SESSION[ 'ROLE_LIST_TOKEN' ] = bin2hex( random_bytes( 32 ) );
}

$message = '';
$error = '';

# ==========================================================
# POST処理
# ==========================================================
if( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {

    if( ! verifyCsrfOrFail( $_POST[ 'token' ] ?? '', $_SESSION[ 'ROLE_LIST_TOKEN' ] ) ) {
        $error = '不正なリクエストです。';
    } else {
        $action = (string)( $_POST[ 'action' ] ?? '' );
        $roleId = filter_var( $_POST[ 'role_id' ] ?? '', FILTER_VALIDATE_INT );
        $roleName = trim( (string)( $_POST[ 'role_name' ] ?? '' ) );
        $roleLevelRaw = trim( (string)( $_POST[ 'role_level' ] ?? '' ) );
        $isEnabled = isset( $_POST[ 'is_enabled' ] ) ? 1 : 0;
        $userId = (string)( $_SESSION[ 'AUTH_USER_ID' ] ?? '' );

        if( $roleName === '' || mb_strlen( $roleName ) > 255 ) {
            $error = '権限名は1～255文字で入力してください。';
        } elseif( $action === 'add' && ( $roleLevelRaw === '' || ! preg_match( '/^-?\d+$/', $roleLevelRaw ) ) ) {
            $error = '権限レベルは整数で入力してください。';
        } elseif( $action === 'update' && ( $roleId === false || $roleId === null ) ) {
            $error = '更新対象の権限が不正です。';
        } elseif( ! in_array( $action, [ 'add', 'update' ], true ) ) {
            $error = '処理内容が不正です。';
        }

        // --------------------------------------------------
        // 新規登録
        // --------------------------------------------------
        if( $error === '' && $action === 'add' ) {
            $roleLevel = (int)$roleLevelRaw;

            $duplicate = $g_DB->select(
                'SELECT role_id FROM baseball_role WHERE role_level = :role_level OR role_name = :role_name LIMIT 1',
                [
                    'role_level' => $roleLevel,
                    'role_name'  => $roleName
                ]
            );

            if( $duplicate === false ) {
                $error = '既存権限の確認に失敗しました。';
            } elseif( count( $duplicate ) > 0 ) {
                $error = '権限名または権限レベルが既に登録されています。';
            } else {
                $result = $g_DB->execute2(
                    'INSERT INTO baseball_role (role_name, role_level, is_enabled, created_by, updated_by) VALUES (:role_name, :role_level, 1, :created_by, :updated_by)',
                    [
                        'role_name'  => $roleName,
                        'role_level' => $roleLevel,
                        'created_by' => $userId,
                        'updated_by' => $userId
                    ]
                );

                if( $result === false ) {
                    $error = '権限の登録に失敗しました。';
                } else {
                    $message = '権限を登録しました。';
                }
            }
        }

        // --------------------------------------------------
        // 更新
        // --------------------------------------------------
        if( $error === '' && $action === 'update' ) {
            $current = $g_DB->select(
                'SELECT role_id, role_name, role_level, is_enabled FROM baseball_role WHERE role_id = :role_id LIMIT 1',
                [ 'role_id' => (int)$roleId ]
            );

            if( $current === false || count( $current ) !== 1 ) {
                $error = '更新対象の権限が見つかりません。';
            } else {
                $row = $current[ 0 ];
                $currentLevel = (int)$row[ 'role_level' ];

                // 権限名の重複確認
                $duplicate = $g_DB->select(
                    'SELECT role_id FROM baseball_role WHERE role_name = :role_name AND role_id <> :role_id LIMIT 1',
                    [
                        'role_name' => $roleName,
                        'role_id'   => (int)$roleId
                    ]
                );

                // 利用中ユーザー数を確認
                $userCount = $g_DB->select(
                    'SELECT COUNT(*) AS cnt FROM baseball_user WHERE role_level = :role_level AND is_enabled = 1',
                    [ 'role_level' => $currentLevel ]
                );
                $activeUsers = (int)( $userCount[ 0 ][ 'cnt' ] ?? 0 );

                if( $duplicate === false ) {
                    $error = '権限名の確認に失敗しました。';
                } elseif( count( $duplicate ) > 0 ) {
                    $error = '権限名が既に登録されています。';
                } elseif( $currentLevel === 1000 && $isEnabled === 0 && $activeUsers > 0 ) {
                    $error = '利用中のシステム管理者権限は無効化できません。';
                } elseif( $currentLevel === 1000 && $isEnabled === 0 ) {
                    // システム管理者権限を最後の1件として無効化できないようにする
                    $adminRole = $g_DB->select(
                        'SELECT COUNT(*) AS cnt FROM baseball_role WHERE role_level = 1000 AND is_enabled = 1 AND role_id <> :role_id',
                        [ 'role_id' => (int)$roleId ]
                    );
                    if( $adminRole === false || (int)( $adminRole[ 0 ][ 'cnt' ] ?? 0 ) === 0 ) {
                        $error = '最後のシステム管理者権限は無効化できません。';
                    }
                }

                if( $error === '' ) {
                    $result = $g_DB->execute(
                        'UPDATE baseball_role SET role_name = :role_name, is_enabled = :is_enabled, updated_by = :updated_by WHERE role_id = :role_id',
                        [
                            'role_name'  => $roleName,
                            'is_enabled' => $isEnabled,
                            'updated_by' => $userId,
                            'role_id'    => (int)$roleId
                        ]
                    );

                    if( $result === false ) {
                        $error = '権限の更新に失敗しました。';
                    } else {
                        $message = '権限を更新しました。';
                    }
                }
            }
        }
    }
}

# ==========================================================
# 権限一覧取得
# ==========================================================
$roleList = $g_DB->select(
    'SELECT role_id, role_name, role_level, is_enabled, created_at, updated_at FROM baseball_role ORDER BY role_level DESC, role_id ASC'
);
if( $roleList === false ) {
    $roleList = [];
    if( $error === '' ) {
        $error = '権限一覧の取得に失敗しました。';
    }
}

# ==========================================================
# HTMLヘッダ
# ==========================================================
$filename = __DIR__ . DIRECTORY_SEPARATOR . 'html_head.php';
if( file_exists( $filename ) ) {
    require_once( $filename );
}
?>

<article class="role-list" id="role-list">
    <div class="container">
        <h2>権限マスタ管理</h2>

        <?php if( $message !== '' ) : ?>
            <div class="message"><?php echo htmlspecialchars( $message, ENT_QUOTES, 'UTF-8' ); ?></div>
        <?php endif; ?>

        <?php if( $error !== '' ) : ?>
            <div class="errMsg"><?php echo htmlspecialchars( $error, ENT_QUOTES, 'UTF-8' ); ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars( $_SESSION[ 'ROLE_LIST_TOKEN' ], ENT_QUOTES, 'UTF-8' ); ?>">
            <input type="hidden" id="action" name="action" value="add">
            <input type="hidden" id="role_id" name="role_id" value="">

            <div class="maintenanceForm">
                <div class="form-group">
                    <label for="role_name">権限名</label>
                    <input type="text" id="role_name" name="role_name" maxlength="255" required>
                </div>

                <div class="form-group">
                    <label for="role_level">権限レベル</label>
                    <input type="number" id="role_level" name="role_level" required>
                </div>

                <div class="form-group">
                    <label for="is_enabled">有効</label>
                    <input type="checkbox" id="is_enabled" name="is_enabled" value="1" checked>
                </div>

                <div class="form-group">
                    <label></label>
                    <button type="submit" id="save_button">登録</button>
                    <button type="button" id="cancel_button">新規</button>
                </div>
            </div>

            <table class="table" id="roleTable">
                <thead>
                    <tr>
                        <th>操作</th>
                        <th>権限名</th>
                        <th>権限レベル</th>
                        <th>状態</th>
                        <th>更新日時</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach( $roleList as $role ) : ?>
                    <tr
                        data-role-id="<?php echo (int)$role[ 'role_id' ]; ?>"
                        data-role-name="<?php echo htmlspecialchars( $role[ 'role_name' ], ENT_QUOTES, 'UTF-8' ); ?>"
                        data-role-level="<?php echo (int)$role[ 'role_level' ]; ?>"
                        data-is-enabled="<?php echo (int)$role[ 'is_enabled' ]; ?>"
                    >
                        <td><button type="button" class="edit-button">編集</button></td>
                        <td><?php echo htmlspecialchars( $role[ 'role_name' ], ENT_QUOTES, 'UTF-8' ); ?></td>
                        <td><?php echo (int)$role[ 'role_level' ]; ?></td>
                        <td><?php echo ( (int)$role[ 'is_enabled' ] === 1 ) ? '有効' : '無効'; ?></td>
                        <td><?php echo htmlspecialchars( (string)$role[ 'updated_at' ], ENT_QUOTES, 'UTF-8' ); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </form>
    </div>
</article>

<script>
document.getElementById('roleTable').addEventListener('click', function(e) {
    const button = e.target.closest('.edit-button');
    if (!button) return;

    const row = button.closest('tr');
    document.getElementById('action').value = 'update';
    document.getElementById('role_id').value = row.dataset.roleId;
    document.getElementById('role_name').value = row.dataset.roleName;
    document.getElementById('role_level').value = row.dataset.roleLevel;
    document.getElementById('role_level').readOnly = true;
    document.getElementById('is_enabled').checked = row.dataset.isEnabled === '1';
    document.getElementById('save_button').textContent = '更新';
    document.getElementById('role_name').focus();
});

document.getElementById('cancel_button').addEventListener('click', function() {
    document.getElementById('action').value = 'add';
    document.getElementById('role_id').value = '';
    document.getElementById('role_name').value = '';
    document.getElementById('role_level').value = '';
    document.getElementById('role_level').readOnly = false;
    document.getElementById('is_enabled').checked = true;
    document.getElementById('save_button').textContent = '登録';
    document.getElementById('role_name').focus();
});
</script>

<?php
$filename = __DIR__ . DIRECTORY_SEPARATOR . 'html_footer.php';
if( file_exists( $filename ) ) {
    require_once( $filename );
}
?>
