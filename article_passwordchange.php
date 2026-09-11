<!-- Article Team Entry Start -->
<article class="team-entry" id="team-entry">
	<div class="container">
		<h2>パスワード変更</h2>
		<div class="team-entry-content">
			<!-- 1. メールアドレス入力・認証コード送信 -->
			<form method="post" action=""  >
				<input type="hidden" name="view_state" value="<?php echo $_SESSION[ 'FROM_VIEW_STATE' ]; ?>">

				<div id="errMsg" class="errMsg"><?php echo $_SESSION[ 'FORM_ERR_MSG' ]; ?></div>

				<div class=form-group>
					<label for="user_password">現在のパスワード</label>
					<?php $userPassword_autofocus = "autofocus"; ?>
					<input type="password" id="user_password" name="user_password" value="<?php echo isset( $_SESSION[ 'FORM_USER_PASSWORD' ] ) ? htmlspecialchars( $_SESSION[ 'FORM_USER_PASSWORD' ] ) : ''; ?>" <?php echo $userPassword_autofocus; ?> required>
				</div>

				<div class=form-group>
					<label for="new_password">新しいパスワード</label>
					<?php $newPassword_autofocus = ""; ?>
					<input type="password" id="new_password" name="new_password" value="<?php echo isset( $_SESSION[ 'FORM_NEW_PASSWORD' ] ) ? htmlspecialchars( $_SESSION[ 'FORM_NEW_PASSWORD' ] ) : ''; ?>" <?php echo $newPassword_autofocus; ?> required>
				</div>
				
				<div class=form-group>
					<label for="confirm_password">新しいパスワード（確認）</label>
					<?php $confirmPassword_autofocus = ""; ?>
					<input type="password" id="confirm_password" name="confirm_password" value="<?php echo isset( $_SESSION[ 'FORM_CONFIRM_PASSWORD' ] ) ? htmlspecialchars( $_SESSION[ 'FORM_CONFIRM_PASSWORD' ] ) : ''; ?>" <?php echo $confirmPassword_autofocus; ?> required>
				</div>

				<div class=form-group>
					<label></label>
					<button type="submit" name="send_button" value="<?php echo $_SESSION[ 'FORM_BUTTON_SEND' ]; ?>"><?php echo "パスワード変更"; ?></button>
				</div>
			</form>
		</div>
	</div>
</article>
<!-- Article Team Entry End -->