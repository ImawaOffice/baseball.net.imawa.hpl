<?php
// ----------------------------------------------------------
// View: メニュー管理画面
// ----------------------------------------------------------
$message = $viewData[ 'message' ] ?? '';
$error = $viewData[ 'error' ] ?? '';
$menuList = $viewData[ 'menuList' ] ?? [];
$roleLevelOptions = $viewData[ 'roleLevelOptions' ] ?? [];
$requestGuard = $viewData[ 'requestGuard' ] ?? '';
?>
<article class="role-list menu-list" id="menu-list">
    <div class="container">
        <h2>メニュー管理</h2>

        <?php if( $message !== '' ) : ?>
            <div class="message"><?php echo htmlspecialchars( $message, ENT_QUOTES, 'UTF-8' ); ?></div>
        <?php endif; ?>

        <?php if( $error !== '' ) : ?>
            <div class="errMsg"><?php echo htmlspecialchars( $error, ENT_QUOTES, 'UTF-8' ); ?></div>
        <?php endif; ?>

        <div class="role-toolbar">
            <button type="button" id="new_menu_button" class="primary-button">新規追加</button>
        </div>

        <dialog id="menuDialog" class="dialog">
            <form method="post" action="" id="menuForm" class="modal-form">
                <div class="dialog-header">
                    <h3 id="menuDialogTitle">メニュー登録</h3>
                    <button type="button" class="close-dialog" aria-label="閉じる">×</button>
                </div>

                <input type="hidden" name="token" value="<?php echo htmlspecialchars( $_SESSION[ 'MENU_LIST_TOKEN' ], ENT_QUOTES, 'UTF-8' ); ?>">
                <input type="hidden" name="processing_guard" id="menu_processing_guard" value="<?php echo htmlspecialchars( $requestGuard, ENT_QUOTES, 'UTF-8' ); ?>">
                <input type="hidden" name="action" id="menu_action" value="add">
                <!-- 編集時は元のmenu_idを保持し、入力されたmenu_idを変更後IDとして扱う。 -->
                <input type="hidden" name="target_menu_id" id="target_menu_id" value="">

                <div class="form-group">
                    <label for="menu_id">メニューID</label>
                    <input type="number" name="menu_id" id="menu_id" required>
                </div>

                <div class="form-group">
                    <label for="menu_parent_id">親メニューID</label>
                    <!-- 親メニューIDは手入力とし、サーバー側で存在チェックを行う。 -->
                    <input type="number" name="parent_id" id="menu_parent_id" min="0" required>
                </div>

                <div class="form-group">
                    <label for="menu_display_order">表示順</label>
                    <input type="number" name="display_order" id="menu_display_order" required>
                </div>

                <div class="form-group">
                    <label for="menu_role_min">最小権限</label>
                    <!-- 権限マスターに存在する権限レベルから選択する。 -->
                    <select name="role_min" id="menu_role_min" required>
                        <option value="">選択してください</option>
                        <?php foreach( $roleLevelOptions as $roleOption ) : ?>
                            <?php $optionRoleLevel = (int)( $roleOption[ 'role_level' ] ?? 0 ); ?>
                            <?php $optionRoleName = (string)( $roleOption[ 'role_name' ] ?? '' ); ?>
                            <option value="<?php echo $optionRoleLevel; ?>"><?php echo $optionRoleLevel; ?> : <?php echo htmlspecialchars( $optionRoleName, ENT_QUOTES, 'UTF-8' ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="menu_role_max">最大権限</label>
                    <!-- 権限マスターに存在する権限レベルから選択する。 -->
                    <select name="role_max" id="menu_role_max" required>
                        <option value="">選択してください</option>
                        <?php foreach( $roleLevelOptions as $roleOption ) : ?>
                            <?php $optionRoleLevel = (int)( $roleOption[ 'role_level' ] ?? 0 ); ?>
                            <?php $optionRoleName = (string)( $roleOption[ 'role_name' ] ?? '' ); ?>
                            <option value="<?php echo $optionRoleLevel; ?>"><?php echo $optionRoleLevel; ?> : <?php echo htmlspecialchars( $optionRoleName, ENT_QUOTES, 'UTF-8' ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="menu_name">メニュー名</label>
                    <input type="text" name="menu_name" id="menu_name" maxlength="255" required>
                </div>

                <div class="form-group">
                    <label for="menu_url">URL</label>
                    <input type="text" name="menu_url" id="menu_url" maxlength="255">
                </div>

                <div class="form-group checkbox-group">
                    <label for="menu_is_enabled">有効</label>
                    <input type="checkbox" name="is_enabled" id="menu_is_enabled" value="1" checked>
                </div>

                <div class="modal-actions">
                    <button type="submit" id="menu_save_button">登録</button>
                    <button type="button" id="menu_cancel_button" class="secondary-button">キャンセル</button>
                </div>
            </form>
        </dialog>

        <table class="table" id="menuTable">
            <thead>
                <tr>
                    <th>操作</th>
                    <th>メニューID</th>
                    <th>親メニューID</th>
                    <th>親メニュー名</th>
                    <th>表示順</th>
                    <th>権限範囲</th>
                    <th>メニュー名</th>
                    <th>URL</th>
                    <th>状態</th>
                    <th>更新日時</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach( $menuList as $menu ) : ?>
                <tr
                    data-menu-id="<?php echo (int)$menu[ 'menu_id' ]; ?>"
                    data-parent-id="<?php echo (int)$menu[ 'parent_id' ]; ?>"
                    data-display-order="<?php echo (int)$menu[ 'display_order' ]; ?>"
                    data-role-min="<?php echo (int)$menu[ 'role_min' ]; ?>"
                    data-role-max="<?php echo (int)$menu[ 'role_max' ]; ?>"
                    data-menu-name="<?php echo htmlspecialchars( $menu[ 'menu_name' ], ENT_QUOTES, 'UTF-8' ); ?>"
                    data-menu-url="<?php echo htmlspecialchars( $menu[ 'menu_url' ], ENT_QUOTES, 'UTF-8' ); ?>"
                    data-is-enabled="<?php echo (int)$menu[ 'is_enabled' ]; ?>"
                >
                    <td data-label="操作"><button type="button" class="edit-button">編集</button></td>
                    <td data-label="メニューID"><?php echo (int)$menu[ 'menu_id' ]; ?></td>
                    <td data-label="親メニューID"><?php echo (int)$menu[ 'parent_id' ]; ?></td>
                    <td data-label="親メニュー名"><?php echo htmlspecialchars( (string)( $menu[ 'parent_name' ] ?? '' ), ENT_QUOTES, 'UTF-8' ); ?></td>
                    <td data-label="表示順"><?php echo (int)$menu[ 'display_order' ]; ?></td>
                    <td data-label="権限範囲"><?php echo (int)$menu[ 'role_min' ]; ?> ～ <?php echo (int)$menu[ 'role_max' ]; ?></td>
                    <td data-label="メニュー名"><?php echo htmlspecialchars( $menu[ 'menu_name' ], ENT_QUOTES, 'UTF-8' ); ?></td>
                    <td data-label="URL"><?php echo htmlspecialchars( $menu[ 'menu_url' ], ENT_QUOTES, 'UTF-8' ); ?></td>
                    <td data-label="状態"><?php echo ( (int)$menu[ 'is_enabled' ] === 1 ) ? '有効' : '無効'; ?></td>
                    <td data-label="更新日時"><?php echo htmlspecialchars( (string)$menu[ 'updated_at' ], ENT_QUOTES, 'UTF-8' ); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</article>
