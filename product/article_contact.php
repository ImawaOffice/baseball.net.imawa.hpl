<!-- Article Contact Start -->
<article class="contact" id="contact">
	<div class="container">
		<h2>お問合せ</h2>
		<div class="contact-content">
			<!-- 1. メールアドレス入力・認証コード送信 -->
			<form method="post" action=""  >
				<input type="hidden" name="view_state" value="<?php echo $_SESSION[ 'RES_VIEW_STATE' ]; ?>">
				<input type="hidden" name="token" value="<?php echo isset( $_SESSION[ 'RES_TOKEN' ] ) ? htmlspecialchars( $_SESSION[ 'RES_TOKEN' ], ENT_QUOTES, 'UTF-8' ) : ''; ?>">

				<div id="errMsg" class="errMsg"><?php echo $_SESSION[ 'RES_ERR_MSG' ]; ?></div>

				<div class=form-group>
					<label for="name">お名前</label>
					<?php $name_readonly = ""; ?>
					<?php $name_autofocus = "autofocus"; ?>
					<input type="text" id="name" name="name" value="<?php echo isset( $_SESSION[ 'RES_NAME' ] ) ? htmlspecialchars( $_SESSION[ 'RES_NAME' ] ) : ''; ?>" <?php echo $name_readonly; ?> <?php echo $name_autofocus; ?> required>
				</div>

				<div class=form-group>
					<label for="phone">電話番号</label>
					<?php $phone_readonly = ""; ?>
					<?php $phone_autofocus = ""; ?>
					<input type="tel" id="phone" name="phone" value="<?php echo isset( $_SESSION[ 'RES_PHONE' ] ) ? htmlspecialchars( $_SESSION[ 'RES_PHONE' ] ) : ''; ?>" <?php echo $phone_readonly; ?> <?php echo $phone_autofocus; ?> >
				</div>

				<div class=form-group>
					<label for="email">メールアドレス</label>
					<?php $email_readonly = ""; ?>
					<?php $email_autofocus = ""; ?>
					<input type="email" id="email" name="email" value="<?php echo isset( $_SESSION[ 'RES_EMAIL' ] ) ? htmlspecialchars( $_SESSION[ 'RES_EMAIL' ] ) : ''; ?>" <?php echo $email_readonly; ?> <?php echo $email_autofocus; ?> >
				</div>

				<div class=form-group>
					<label for="inquiry">問合せ内容</label>
					<?php $inquiry_readonly = ""; ?>
					<?php $inquiry_autofocus = ""; ?>
					<textarea id="inquiry" name="inquiry" rows="5" <?php echo $inquiry_readonly; ?> <?php echo $inquiry_autofocus; ?> required><?php echo isset( $_SESSION[ 'RES_INQUIRY' ] ) ? htmlspecialchars( $_SESSION[ 'RES_INQUIRY' ] ) : ''; ?></textarea>
				</div>

				<div style="display: none;" aria-hidden="true">
					<label for="agree">同意</label>
					<input type="text" id="agree" name="agree" tabindex="-1" autocomplete="off" value="" >
				</div>

				<div class=form-group>
					<label></label>
					<button type="submit" name="send_button"><?php echo "送信"; ?></button>
				</div>
			</form>
		</div>
	</div>
</article>
<!-- Article Contact End -->