<!-- Article Team Maintenance Start -->
<article class="teammaintenance" id="teammaintenance">
	<div class="container">
		<h2>参加チーム登録</h2>
		<div class="teammaintenance-content">
			<!-- Form -->
			<form method="post" action=""  >
				<input type="hidden" name="view_state" value="<?php echo $_SESSION[ 'FORM_VIEW_STATE' ]; ?>">
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
					
					<input type="hidden" id="team_id" name="team_id" value="0">
					<input type="hidden" id="team_tournament_id" name="team_tournament_id" value="0">

					<div class=form-group>
						<label for="team_name">チーム名</label>
						<input type="text" id="team_name" name="team_name" value="" >
					</div>

					<div class=form-group>
						<label for="team_manager">責任者名</label>
						<input type="text" id="team_manager" name="team_manager" value="" >
					</div>

					<div class=form-group>
						<label for="team_tel">電話番号</label>
						<input type="tel" id="team_tel" name="team_tel" value="" >
					</div>

					<div class=form-group>
						<label for="team_email">メールアドレス</label>
						<input type="email" id="team_email" name="team_email" value="" >
					</div>

					<div class=form-group>
						<label for="team_access_code">アクセスコード</label>
						<input type="text" id="team_access_code" name="team_access_code" value="" >
					</div>

					<div class=form-group>
						<label for="team_tournament1_attend">総当たり戦参加可否</label>
						<input type="checkbox" id="team_tournament1_attend" name="team_tournament1_attend" value="1" >
					</div>

					<div class=form-group>
						<label></label>
						<button type="submit" id="update_button" name="update_button" value="update"><?php echo "更新"; ?></button>
						<button type="submit" id="add_button" name="add_button" value="add"><?php echo "追加"; ?></button>
					</div>
				</div>

				<div class="teamdetail">
					<hr style="margin: 12px 0; border: none; border-top: 1px solid #ccc;">

					<div class=form-group>
						<select id="tournament_id" name="tournament_id" onchange="this.form.submit()">
							<?php foreach ( getTournamentList() as $tournament ) : ?>
								<option 
									value="<?php echo htmlspecialchars( $tournament[ 'tournament_id' ], ENT_QUOTES, 'UTF-8' ); ?>"
									<?php echo ( isset( $_SESSION[ 'FORM_TOURNAMENT_ID' ] ) && $_SESSION[ 'FORM_TOURNAMENT_ID' ] == $tournament[ 'tournament_id' ] ) ? 'selected' : ''; ?>
								>
									<?php echo htmlspecialchars( $tournament[ 'tournament_title' ] . ' ' . $tournament[ 'tournament_text' ], ENT_QUOTES, 'UTF-8' ); ?>
								</option>
							<?php endforeach; ?>
						</select>
						<p><?php echo htmlspecialchars( $_SESSION[ 'FORM_TOURNAMENT1_TEXT' ], ENT_QUOTES, 'UTF-8' ); ?>： <?php echo htmlspecialchars( $_SESSION[ 'FORM_TEAM_ALLOW_COUNT' ], ENT_QUOTES, 'UTF-8' ); ?> / <?php echo htmlspecialchars( $_SESSION[ 'FORM_TOURNAMENT1_TEAMS' ], ENT_QUOTES, 'UTF-8' ); ?></p>
						<button type="submit" name="download_entry" value="<?php echo isset( $_SESSION[ 'FORM_DOWNLOAD_ENTRY' ] ) ? htmlspecialchars( $_SESSION[ 'FORM_DOWNLOAD_ENTRY' ], ENT_QUOTES, 'UTF-8' ) : '0'; ?>"><?php echo isset( $_SESSION[ 'FORM_DOWNLOAD_ENTRY' ] ) ? htmlspecialchars( $_SESSION[ 'FORM_DOWNLOAD_ENTRY' ], ENT_QUOTES, 'UTF-8' ) : 'DOWNLOAD'; ?></button>
					</div>

					<table class="table" id="teamTable">
						<thead>
							<tr 
								data-trteamid=""
								data-trteamtournamentid=""
								data-trteamname=""
								data-trteammanager=""
								data-trteamcontact=""
								data-trteamaccesscode=""
								data-trteamtel=""
								data-trteamemail=""
								data-trtournament1attend=""
							>
								<th class="text-center"><button type="button" class="detail_button" value="ADD">追加</button></th>
								<th>チーム名</th>
								<th>責任者名</th>
								<th>電話番号</th>
								<th>メールアドレス</th>
								<th>アクセスコード</th>
								<th>総当たり戦参加可否</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( getTeamList( $_SESSION[ 'FORM_TOURNAMENT_ID' ], $_SESSION[ 'FORM_PAGE_CURRENT' ], $_SESSION[ 'FORM_PAGE_ROWS' ] ) as $team ) : ?>
								<tr 
									data-trteamid="<?php echo $team[ 'team_id' ]; ?>"
									data-trteamtournamentid="<?php echo $team[ 'tournament_id' ]; ?>"
									data-trteamname="<?php echo htmlspecialchars( $team[ 'team_name' ], ENT_QUOTES, 'UTF-8' ); ?>"
									data-trteammanager="<?php echo htmlspecialchars( $team[ 'team_manager' ], ENT_QUOTES, 'UTF-8' ); ?>"
									data-trteamtel="<?php echo htmlspecialchars( $team[ 'team_tel' ], ENT_QUOTES, 'UTF-8' ); ?>"
									data-trteamemail="<?php echo htmlspecialchars( $team[ 'team_email' ], ENT_QUOTES, 'UTF-8' ); ?>"
									data-trteamcontact="<?php echo htmlspecialchars( $team[ 'team_contact' ], ENT_QUOTES, 'UTF-8' ); ?>"
									data-trteamaccesscode="<?php echo htmlspecialchars( $team[ 'team_access_cd' ], ENT_QUOTES, 'UTF-8' ); ?>"
									data-trtournament1attend="<?php echo htmlspecialchars( $team[ 'tournament1_attend' ], ENT_QUOTES, 'UTF-8' ); ?>"
								>
									<td class="text-center"><button type="button" class="detail_button" value="UPDATE">更新</button></td>
									<td><?php echo htmlspecialchars( $team[ 'team_name' ], ENT_QUOTES, 'UTF-8' ); ?></td>
									<td><?php echo htmlspecialchars( $team[ 'team_manager' ], ENT_QUOTES, 'UTF-8' ); ?></td>
									<td><?php echo htmlspecialchars( $team[ 'team_tel' ], ENT_QUOTES, 'UTF-8' ); ?></td>
									<td><?php echo htmlspecialchars( $team[ 'team_email' ], ENT_QUOTES, 'UTF-8' ); ?></td>
									<td><?php echo htmlspecialchars( $team[ 'team_access_cd' ], ENT_QUOTES, 'UTF-8' ); ?></td>
									<td><?php echo htmlspecialchars( $team[ 'tournament1_attend_name' ], ENT_QUOTES, 'UTF-8' ); ?></td>
									<td class="text-center"><button type="submit" class="delete_button" name="delete_button" value="<?php echo $team[ 'team_id' ]; ?>"><?php echo "削除"; ?></button></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>

					<div class=form-group>
						<p><?php echo htmlspecialchars( $_SESSION[ 'FORM_TOURNAMENT1_TEXT' ], ENT_QUOTES, 'UTF-8' ); ?>： <?php echo htmlspecialchars( $_SESSION[ 'FORM_TEAM_ALLOW_COUNT' ], ENT_QUOTES, 'UTF-8' ); ?> / <?php echo htmlspecialchars( $_SESSION[ 'FORM_TOURNAMENT1_TEAMS' ], ENT_QUOTES, 'UTF-8' ); ?></p>
						<button type="submit" id="button_mail_allow" name="button_mail_allow" value="attend">参加チームメール送信</button>
						<button type="submit" id="button_mail_deny" name="button_mail_deny" value="deny">不参加チームメール送信</button>
					</div>

				</div>
			</form>
		</div>
	</div>
</article>

<script>
	const tournament_id      = document.getElementById( "tournament_id" );
	const team_id            = document.getElementById( "team_id" );
	const team_tournament_id = document.getElementById( "team_tournament_id" );
	const team_name          = document.getElementById( "team_name" );
	const team_manager       = document.getElementById( "team_manager" );
	const team_tel           = document.getElementById( "team_tel" );
	const team_email         = document.getElementById( "team_email" );
	const team_access_code   = document.getElementById( "team_access_code" );
	const team_tournament1_attend = document.getElementById( "team_tournament1_attend" );
	const add_button         = document.getElementById( "add_button" );
	const update_button      = document.getElementById( "update_button" );

	document.getElementById( "teamTable" ).addEventListener( "click", ( e ) => {

		const btn = e.target.closest( ".detail_button" );

		if ( !btn ) return;

		if( btn.value === "ADD" ){
			team_id.value = "";
			team_id.required = false;
			team_tournament_id.value = tournament_id.value;
			team_tournament_id.required = false;
			team_name.value = "";
			team_name.required = true;
			team_manager.value = "";
			team_manager.required = true;
			team_tel.value = "";
			team_tel.required = true;
			team_email.value = "";
			team_email.required = true;
			team_access_code.value = "";
			team_access_code.required = true;
			team_tournament1_attend.checked = false;
			update_button.style.display = "none";
			add_button.style.display = "block";

			const el = document.querySelector( '.maintenanceForm' );
			el.style.display = 'block';

			team_name.focus();

			return;

		}

		const tr = btn.closest( "tr" );

		const tr_team_id = tr.dataset.trteamid;
		const tr_team_tournament_id = tr.dataset.trteamtournamentid;
		const tr_team_name = tr.dataset.trteamname;
		const tr_team_manager = tr.dataset.trteammanager;
		const tr_team_tel = tr.dataset.trteamtel;
		const tr_team_email = tr.dataset.trteamemail;
		const tr_team_access_code = tr.dataset.trteamaccesscode;
		const tr_team_tournament1_attend = tr.dataset.trtournament1attend;

		team_id.value = tr_team_id;
		team_id.required = false;
		team_tournament_id.value = tr_team_tournament_id;
		team_tournament_id.required = false;
		team_name.value = tr_team_name;
		team_name.required = true;
		team_manager.value = tr_team_manager;
		team_manager.required = true;
		team_tel.value = tr_team_tel;
		team_tel.required = true;
		team_email.value = tr_team_email;
		team_email.required = true;
		team_access_code.value = tr_team_access_code;
		team_access_code.required = true;
		team_tournament1_attend.checked = tr_team_tournament1_attend === "1" ? true : false;
		update_button.style.display = "block";
		add_button.style.display = "block";

		const el = document.querySelector( '.maintenanceForm' );
		el.style.display = 'block';

		team_name.focus();

	} );
</script>
<!-- Article Team Maintenance End -->