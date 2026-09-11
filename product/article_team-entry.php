<!-- Article Team Entry Start -->
<article class="team-entry" id="team-entry">
	<div class="container">
		<h2>大会参加申し込み</h2>
		<div class="team-entry-content">
			<!-- 1. メールアドレス入力・認証コード送信 -->
			<form method="post" action=""  >
				<input type="hidden" name="view_state" value="<?php echo $_SESSION[ 'RES_VIEW_STATE' ]; ?>">
				<input type="hidden" name="token" value="<?php echo isset( $_SESSION[ 'RES_TOKEN' ] ) ? htmlspecialchars( $_SESSION[ 'RES_TOKEN' ], ENT_QUOTES, 'UTF-8' ) : ''; ?>">

				<div id="errMsg" class="errMsg"><?php echo $_SESSION[ 'RES_ERR_MSG' ]; ?></div>

				<div class=form-group>
					<label for="tournament_id">参加する大会</label>
					<select id="tournament_id" name="tournament_id">
						<?php foreach ( getTournamentList() as $tournament ) : ?>
							<option 
								value="<?php echo htmlspecialchars( $tournament[ 'tournament_id' ], ENT_QUOTES, 'UTF-8' ); ?>"
								<?php echo ( isset( $_SESSION[ 'RES_TOURNAMENT_ID' ] ) && $_SESSION[ 'RES_TOURNAMENT_ID' ] == $tournament[ 'tournament_id' ] ) ? 'selected' : ''; ?>
							>
								<?php echo htmlspecialchars( $tournament[ 'tournament_title' ] . ' ' . $tournament[ 'tournament_text' ], ENT_QUOTES, 'UTF-8' ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class=form-group>
					<label for="team_name">チーム名</label>
					<?php $teamName_autofocus = "autofocus"; ?>
					<input type="text" id="team_name" name="team_name" value="<?php echo isset( $_SESSION[ 'RES_TEAM_NAME' ] ) ? htmlspecialchars( $_SESSION[ 'RES_TEAM_NAME' ] ) : ''; ?>" <?php echo $teamName_autofocus; ?> required>
				</div>

				<div class=form-group>
					<label for="team_manager">責任者名</label>
					<?php $manager_autofocus = ""; ?>
					<input type="text" id="team_manager" name="team_manager" value="<?php echo isset( $_SESSION[ 'RES_TEAM_MANAGER' ] ) ? htmlspecialchars( $_SESSION[ 'RES_TEAM_MANAGER' ] ) : ''; ?>" <?php echo $manager_autofocus; ?> required>
				</div>

				<div class=form-group>
					<label for="team_contact">連絡先</label>
					<?php $contact_autofocus = ""; ?>
					<input type="text" id="team_contact" name="team_contact" value="<?php echo isset( $_SESSION[ 'RES_TEAM_CONTACT' ] ) ? htmlspecialchars( $_SESSION[ 'RES_TEAM_CONTACT' ] ) : ''; ?>" <?php echo $contact_autofocus; ?> required>
				</div>

				<div style="display: none;" aria-hidden="true">
					<label for="agree">同意</label>
					<input type="text" id="agree" name="agree" tabindex="-1" autocomplete="off" value="" >
				</div>

				<div class=form-group>
					<label></label>
					<button type="submit" name="send_button" value="<?php echo $_SESSION[ 'RES_BUTTON_SEND' ]; ?>"><?php echo "参加申し込み"; ?></button>
				</div>
			</form>
		</div>
	</div>
</article>
<!-- Article Team Entry End -->