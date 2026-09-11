<!-- Article Game Match Start -->
<article class="userlist" id="userlist">
	<div class="container">
		<h2>ユーザ一覧</h2>
		<div class="userlist-content">
			<!-- Form -->
			<form method="post" action=""  >
				<input type="hidden" name="view_state" value="<?php echo $_SESSION[ 'FROM_VIEW_STATE' ]; ?>">
				<input type="hidden" name="page_current" value="<?php echo $_SESSION[ 'FORM_PAGE_CURRENT' ]; ?>">

				<div id="errMsg" class="errMsg"><?php echo $_SESSION[ 'FORM_ERR_MSG' ]; ?></div>

				<div>
					<div class="page-navi">
						<?php $page_top_disabled = ""; ?>
						<?php $page_previous_disabled = ""; ?>
						<?php $page_current_disabled = "disabled"; ?>
						<?php $page_next_disabled = ""; ?>
						<?php $page_last_disabled = ""; ?>
						<button type="submit" name="page_top" value="<?php echo isset( $_SESSION[ 'FORM_PAGE_TOP' ] ) ? htmlspecialchars( $_SESSION[ 'FORM_PAGE_TOP' ], ENT_QUOTES, 'UTF-8' ) : '0'; ?>" <?php echo $page_top_disabled; ?>><?php echo "<<"; ?></button>
						<button type="submit" name="page_previous" value="<?php echo isset( $_SESSION[ 'FORM_PAGE_PREVIOUS' ] ) ? htmlspecialchars( $_SESSION[ 'FORM_PAGE_PREVIOUS' ], ENT_QUOTES, 'UTF-8' ) : '0'; ?>" <?php echo $page_previous_disabled; ?>><?php echo "<"; ?></button>
						<button type="button" name="page_current" value="<?php echo isset( $_SESSION[ 'FORM_PAGE_CURRENT' ] ) ? htmlspecialchars( $_SESSION[ 'FORM_PAGE_CURRENT' ], ENT_QUOTES, 'UTF-8' ) : '0'; ?>" <?php echo $page_current_disabled; ?>><?php echo isset( $_SESSION[ 'FORM_PAGE_CURRENT' ] ) ? htmlspecialchars( $_SESSION[ 'FORM_PAGE_CURRENT' ], ENT_QUOTES, 'UTF-8' ) : '0'; ?></button>
						<button type="submit" name="page_next" value="<?php echo isset( $_SESSION[ 'FORM_PAGE_NEXT' ] ) ? htmlspecialchars( $_SESSION[ 'FORM_PAGE_NEXT' ], ENT_QUOTES, 'UTF-8' ) : '0'; ?>" <?php echo $page_next_disabled; ?>><?php echo ">"; ?></button>
						<button type="submit" name="page_last" value="<?php echo isset( $_SESSION[ 'FORM_PAGE_LAST' ] ) ? htmlspecialchars( $_SESSION[ 'FORM_PAGE_LAST' ], ENT_QUOTES, 'UTF-8' ) : '0'; ?>" <?php echo $page_last_disabled; ?>><?php echo ">>"; ?></button>
						<label>表示件数</label>
						<select name="page_rows" id="page_rows" onchange="this.form.submit()">
							<option value="10" <?php echo ( isset( $_SESSION[ 'FORM_PAGE_ROWS' ] ) && $_SESSION[ 'FORM_PAGE_ROWS' ] == 10 ) ? 'selected' : ''; ?>>10</option>
							<option value="25" <?php echo ( isset( $_SESSION[ 'FORM_PAGE_ROWS' ] ) && $_SESSION[ 'FORM_PAGE_ROWS' ] == 25 ) ? 'selected' : ''; ?>>25</option>
							<option value="50" <?php echo ( isset( $_SESSION[ 'FORM_PAGE_ROWS' ] ) && $_SESSION[ 'FORM_PAGE_ROWS' ] == 50 ) ? 'selected' : ''; ?>>50</option>
							<option value="100" <?php echo ( isset( $_SESSION[ 'FORM_PAGE_ROWS' ] ) && $_SESSION[ 'FORM_PAGE_ROWS' ] == 100 ) ? 'selected' : ''; ?>>100</option>
						</select>
					</div>
				</div>
				<div class="maintenanceForm">
					<input type="hidden" id="user_id" name="user_id" value="">

					<div class=form-group>
						<label for="user_cd">ユーザCD</label>
						<input type="text" id="user_cd" name="user_cd" value="" readonly >
					</div>

					<div class=form-group>
						<label for="username">ユーザ名</label>
						<input type="text" id="username" name="username" value=""  >
					</div>

					<div class=form-group>
						<label for="email">メールアドレス</label>
						<input type="text" id="email" name="email" value=""  >
					</div>
	
					<div class=form-group>
						<label for="role_level">ユーザ権限</label>
						<select id="role_level" name="role_level">
							<?php foreach ( getRoleList() as $role ) : ?>
								<option 
									value="<?php echo htmlspecialchars( $role[ 'role_level' ], ENT_QUOTES, 'UTF-8' ); ?>"
									<?php echo ( isset( $_SESSION[ 'RES_ROLE_LEVEL' ] ) && $_SESSION[ 'RES_ROLE_LEVEL' ] == $role[ 'role_id' ] ) ? 'selected' : ''; ?>
								>
									<?php echo htmlspecialchars( $role[ 'role_name' ], ENT_QUOTES, 'UTF-8' ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class=form-group>
						<label for="signin_at">最終サインイン日時</label>
						<input type="text" id="signin_at" name="signin_at" value="" disabled >
					</div>

					<div class=form-group>
						<label for="is_enabled">有効フラグ</label>
						<input type="text" id="is_enabled" name="is_enabled" value=""  >
					</div>
								
					<div class=form-group>
						<label></label>
						<button type="submit" id="update_button" name="update_button" value="update"><?php echo "更新"; ?></button>
					</div>
				</div>

				<div class="teamdetail">
					<hr style="margin: 12px 0; border: none; border-top: 1px solid #ccc;">

					<table class="table" id="userTable">
						<thead>
							<tr 
								data-truserid=""
								data-trtournamentid=""
								data-trgameclass=""
								data-trgameblock=""
								data-trgamecount=""
								data-trgamename=""
								data-trgamedate=""
								data-trgameplace=""
								data-trwinnerid=""
								data-trloserid=""
								data-trteam1id=""
								data-trteam2id=""
								data-trteam1name=""
								data-trteam2name=""
								data-trteam1score=""
								data-trteam2score=""
								data-trteam1lastgameid=""
								data-trteam2lastgameid=""
							>
								<th class="text-center"></th>
								<th>ユーザー権限</th>
								<th>ユーザCD</th>
								<th>ユーザ名</th>
								<th>メールアドレス</th>
								<th>最終サインイン日時</th>
								<th>有効フラグ</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( getUserList( $_SESSION[ 'FORM_PAGE_CURRENT' ], $_SESSION[ 'FORM_PAGE_ROWS' ] ) as $game ) : ?>
								<tr 
									data-truserid="<?php echo $game[ 'user_id' ]; ?>"
									data-trusercd="<?php echo $game[ 'user_cd' ]; ?>"
									data-trpassword="<?php echo $game[ 'password' ]; ?>"
									data-trusername="<?php echo $game[ 'username' ]; ?>"
									data-tremail="<?php echo $game[ 'email' ]; ?>"
									data-trrolelevel="<?php echo $game[ 'role_level' ]; ?>"
									data-trsigninat="<?php echo $game[ 'signin_at' ]; ?>"
									data-trisenabled="<?php echo $game[ 'is_enabled' ]; ?>"
								>
									<td class="text-center"><button type="button" class="detail_button" value="UPDATE">更新</button></td>
									<td><?php echo htmlspecialchars( $game[ 'role_name' ], ENT_QUOTES, 'UTF-8' ); ?></td>
									<td><?php echo htmlspecialchars( $game[ 'user_cd' ], ENT_QUOTES, 'UTF-8' ); ?></td>
									<td><?php echo htmlspecialchars( $game[ 'username' ], ENT_QUOTES, 'UTF-8' ); ?></td>
									<td><?php echo htmlspecialchars( $game[ 'email' ], ENT_QUOTES, 'UTF-8' ); ?></td>
									<td><?php echo $game[ 'signin_at' ]; ?></td>
									<td><?php echo $game[ 'is_enabled' ]; ?></td>
									<td class="text-center"><button type="submit" class="delete_button" name="delete_button" value="<?php echo $game[ 'user_id' ]; ?>"><?php echo "削除"; ?></button></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</form>
		</div>
	</div>
</article>

<script>
	const user_id            = document.getElementById( "user_id" );
	const user_cd            = document.getElementById( "user_cd" );
	const password           = document.getElementById( "password" );
	const username           = document.getElementById( "username" );
	const email              = document.getElementById( "email" );
	const role_level         = document.getElementById( "role_level" );
	const signin_at          = document.getElementById( "signin_at" );
	const is_enabled         = document.getElementById( "is_enabled" );
	const add_button         = document.getElementById( "add_button" );
	const update_button      = document.getElementById( "update_button" );

	document.getElementById( "userTable" ).addEventListener( "click", ( e ) => {

		const btn = e.target.closest( ".detail_button" );

		if ( !btn ) return;

		if( btn.value === "ADD" ){
			game_id.value = "";
			tournament_id.value = tournament_id.value;
			tournament_id.required = false;
			game_class.value = game_class.value;
			game_class.required = false;
			game_name.value = "";
			game_name.required = true;
			game_date.value = "";
			game_date.required = true;
			game_place.value = "";
			game_place.required = true;
			winner_id.value = "";
			winner_id.required = true;
			loser_id.value = "";
			loser_id.required = true;
			team1_id.value = "";
			team1_id.required = true;
			team2_id.value = "";
			team2_id.required = true;
			team1_name.value = "";
			team1_name.required = true;
			team2_name.value = "";
			team2_name.required = true;
			team1_score.value = "";
			team1_score.required = true;
			team2_score.value = "";
			team2_score.required = true;
			team1_last_game_id.value = "";
			team1_last_game_id.required = true;
			team2_last_game_id.value = "";
			team2_last_game_id.required = true;
			update_button.style.display = "none";
			add_button.style.display = "block";

			const el = document.querySelector( '.maintenanceForm' );
			el.style.display = 'block';

			team1_id.focus();

			return;

		}

		const tr = btn.closest( "tr" );

		const tr_user_id = tr.dataset.truserid;
		const tr_user_cd = tr.dataset.trusercd;
		const tr_password = tr.dataset.trpassword;
		const tr_username = tr.dataset.trusername;
		const tr_email = tr.dataset.tremail;
		const tr_role_level = tr.dataset.trrolelevel;
		const tr_verification_cd = tr.dataset.trverificationcd;
		const tr_verification_period = tr.dataset.trverificationperiod;
		const tr_signin_at = tr.dataset.trsigninat;
		const tr_signin_by = tr.dataset.trsigninby;
		const tr_is_enabled = tr.dataset.trisenabled;

		user_id.value = tr_user_id;

		user_cd.value = tr_user_cd;
		user_cd.required = false;

		username.value = tr_username;
		username.required = false;

		email.value = tr_email;
		email.required = false;

		role_level.value = tr_role_level;
		role_level.required = false;

		signin_at.value = tr_signin_at;
		signin_at.required = false;
		
		is_enabled.value = tr_is_enabled;
		is_enabled.required = false;

		update_button.style.display = "block";

		const el = document.querySelector( '.maintenanceForm' );
		el.style.display = 'block';

		username.focus();

	} );
</script>
<!-- Article Game Match End -->