<?php
// ----------------------------------------------------------
// View: 権限管理画面
// ----------------------------------------------------------
// このファイルは表示だけを担当する。
// Controller から渡された $viewData を使って、
// テーブルやフォームの描画だけに集中させる。
//
// 役割:
// - フォーム描画
// - メッセージ表示
// - 一覧テーブル表示
// - 画面固有のHTML構造の定義

$message = $viewData[ 'message' ] ?? '';
$error = $viewData[ 'error' ] ?? '';
$roleList = $viewData[ 'roleList' ] ?? [];
$requestGuard = $viewData[ 'requestGuard' ] ?? '';
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

        <div class="role-toolbar">
            <button type="button" id="new_role_button" class="primary-button">新規追加</button>
        </div>

        <dialog id="roleDialog" class="dialog">
            <form method="post" action="" id="roleForm" class="modal-form">
                <div class="dialog-header">
                    <h3 id="roleDialogTitle">権限登録</h3>
                    <button type="button" class="close-dialog" aria-label="閉じる">×</button>
                </div>

                <input type="hidden" name="token" value="<?php echo htmlspecialchars( $_SESSION[ 'ROLE_LIST_TOKEN' ], ENT_QUOTES, 'UTF-8' ); ?>">
                <input type="hidden" name="processing_guard" id="processing_guard" value="<?php echo htmlspecialchars( $requestGuard, ENT_QUOTES, 'UTF-8' ); ?>">
                <input type="hidden" name="action" id="action" value="add">
                <input type="hidden" name="role_id" id="role_id" value="">

                <div class="form-group">
                    <label for="role_name">権限名</label>
                    <input type="text" name="role_name" id="role_name" maxlength="255" required>
                </div>

                <div class="form-group">
                    <label for="role_level">権限レベル</label>
                    <input type="number" name="role_level" id="role_level" required>
                </div>

                <div class="form-group checkbox-group">
                    <label for="is_enabled">有効</label>
                    <input type="checkbox" name="is_enabled" id="is_enabled" value="1" checked>
                </div>

                <div class="modal-actions">
                    <button type="submit" id="save_button">登録</button>
                    <button type="button" id="cancel_button" class="secondary-button">キャンセル</button>
                </div>
            </form>
        </dialog>

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
                    <td data-label="操作"><button type="button" class="edit-button">編集</button></td>
                    <td data-label="権限名"><?php echo htmlspecialchars( $role[ 'role_name' ], ENT_QUOTES, 'UTF-8' ); ?></td>
                    <td data-label="権限レベル"><?php echo (int)$role[ 'role_level' ]; ?></td>
                    <td data-label="状態"><?php echo ( (int)$role[ 'is_enabled' ] === 1 ) ? '有効' : '無効'; ?></td>
                    <td data-label="更新日時"><?php echo htmlspecialchars( (string)$role[ 'updated_at' ], ENT_QUOTES, 'UTF-8' ); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</article>
