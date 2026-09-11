<!-- Article Contact Start -->
<article class="contact" id="contact">
	<div class="container">
		<h2>ユーザ情報</h2>
		<div class="contact-content">
			<!-- 1. メールアドレス入力・認証コード送信 -->
			<form method="post" action=""  >
				<input type="hidden" name="view_state" value="<?php echo $_SESSION[ 'FROM_VIEW_STATE' ]; ?>">

				<div id="errMsg" class="errMsg"><?php echo $_SESSION[ 'FORM_ERR_MSG' ]; ?></div>

				<div class=form-group>
					<label for="user_cd">メールアドレス</label>
					<?php $userCd_readonly = "disabled"; ?>
					<?php $userCd_autofocus = ""; ?>
					<?php $userCd_required = ""; ?>
					<input type="text" id="user_cd" name="user_cd" value="<?php echo isset( $_SESSION[ 'RES_USER_CD' ] ) ? htmlspecialchars( $_SESSION[ 'RES_USER_CD' ] ) : ''; ?>" <?php echo $userCd_readonly; ?> <?php echo $userCd_autofocus; ?> <?php echo $userCd_required; ?> >
				</div>

				<div class=form-group>
					<label for="user_name">ユーザー名</label>
					<?php $username_readonly = ""; ?>
					<?php $username_autofocus = "autofocus"; ?>
					<?php $username_required = "required"; ?>
					<input type="text" id="user_name" name="user_name" value="<?php echo isset( $_SESSION[ 'RES_USER_NAME' ] ) ? htmlspecialchars( $_SESSION[ 'RES_USER_NAME' ] ) : ''; ?>" <?php echo $username_readonly; ?> <?php echo $username_autofocus; ?> <?php echo $username_required; ?> >
				</div>

				<div class=form-group>
					<label for="role_level">ユーザ権限</label>
					<?php $roleLevel_readonly = "disabled"; ?>
					<?php $roleLevel_autofocus = ""; ?>
					<?php $roleLevel_required = ""; ?>
					<input type="text" id="role_level" name="role_level" value="<?php echo isset( $_SESSION[ 'RES_ROLE_LEVEL' ] ) ? htmlspecialchars( $_SESSION[ 'RES_ROLE_LEVEL' ] ) : ''; ?>" <?php echo $roleLevel_readonly; ?> <?php echo $roleLevel_autofocus; ?> <?php echo $roleLevel_required; ?> >
				</div>

				<div class=form-group>
					<label for="last_signin">最終ログイン</label>
					<?php $lastSignin_readonly = "disabled"; ?>
					<?php $lastSignin_autofocus = ""; ?>
					<?php $lastSignin_required = ""; ?>
					<input type="text" id="last_signin" name="last_signin" value="<?php echo isset( $_SESSION[ 'RES_LAST_SIGNIN' ] ) ? htmlspecialchars( $_SESSION[ 'RES_LAST_SIGNIN' ] ) : ''; ?>" <?php echo $lastSignin_readonly; ?> <?php echo $lastSignin_autofocus; ?> <?php echo $lastSignin_required; ?>>
				</div>

				<div class=form-group>
					<label></label>
					<button type="submit" name="send_button"><?php echo "更新"; ?></button>
				</div>
			</form>
		</div>
	</div>
</article>
<!-- Article Contact End -->