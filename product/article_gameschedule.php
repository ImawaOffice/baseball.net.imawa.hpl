<!-- Article Game Match Start -->
<article class="gamematch" id="gamematch">
	<div class="container">
		<h2>試合日程登録</h2>
		<div class="gamematch-content">
			<!-- Form -->
			<form method="post" action=""  >
				<input type="hidden" name="view_state" value="<?php echo $_SESSION[ 'RES_VIEW_STATE' ]; ?>">
				<input type="hidden" name="page_current" value="<?php echo $_SESSION[ 'RES_PAGE_CURRENT' ]; ?>">

				<div id="errMsg" class="errMsg"><?php echo $_SESSION[ 'RES_ERR_MSG' ]; ?></div>

				<div>
					<div class="page-navi">
						<?php $page_top_disabled = ""; ?>
						<?php $page_previous_disabled = ""; ?>
						<?php $page_current_disabled = "disabled"; ?>
						<?php $page_next_disabled = ""; ?>
						<?php $page_last_disabled = ""; ?>
						<button type="submit" name="page_top" value="<?php echo isset( $_SESSION[ 'RES_PAGE_TOP' ] ) ? htmlspecialchars( $_SESSION[ 'RES_PAGE_TOP' ], ENT_QUOTES, 'UTF-8' ) : '0'; ?>" <?php echo $page_top_disabled; ?>><?php echo "<<"; ?></button>
						<button type="submit" name="page_previous" value="<?php echo isset( $_SESSION[ 'RES_PAGE_PREVIOUS' ] ) ? htmlspecialchars( $_SESSION[ 'RES_PAGE_PREVIOUS' ], ENT_QUOTES, 'UTF-8' ) : '0'; ?>" <?php echo $page_previous_disabled; ?>><?php echo "<"; ?></button>
						<button type="button" name="page_current" value="<?php echo isset( $_SESSION[ 'RES_PAGE_CURRENT' ] ) ? htmlspecialchars( $_SESSION[ 'RES_PAGE_CURRENT' ], ENT_QUOTES, 'UTF-8' ) : '0'; ?>" <?php echo $page_current_disabled; ?>><?php echo isset( $_SESSION[ 'RES_PAGE_CURRENT' ] ) ? htmlspecialchars( $_SESSION[ 'RES_PAGE_CURRENT' ], ENT_QUOTES, 'UTF-8' ) : '0'; ?></button>
						<button type="submit" name="page_next" value="<?php echo isset( $_SESSION[ 'RES_PAGE_NEXT' ] ) ? htmlspecialchars( $_SESSION[ 'RES_PAGE_NEXT' ], ENT_QUOTES, 'UTF-8' ) : '0'; ?>" <?php echo $page_next_disabled; ?>><?php echo ">"; ?></button>
						<button type="submit" name="page_last" value="<?php echo isset( $_SESSION[ 'RES_PAGE_LAST' ] ) ? htmlspecialchars( $_SESSION[ 'RES_PAGE_LAST' ], ENT_QUOTES, 'UTF-8' ) : '0'; ?>" <?php echo $page_last_disabled; ?>><?php echo ">>"; ?></button>
						<label>表示件数</label>
						<select name="page_rows" id="page_rows" onchange="this.form.submit()">
							<option value="10" <?php echo ( isset( $_SESSION[ 'RES_PAGE_ROWS' ] ) && $_SESSION[ 'RES_PAGE_ROWS' ] == 10 ) ? 'selected' : ''; ?>>10</option>
							<option value="25" <?php echo ( isset( $_SESSION[ 'RES_PAGE_ROWS' ] ) && $_SESSION[ 'RES_PAGE_ROWS' ] == 25 ) ? 'selected' : ''; ?>>25</option>
							<option value="50" <?php echo ( isset( $_SESSION[ 'RES_PAGE_ROWS' ] ) && $_SESSION[ 'RES_PAGE_ROWS' ] == 50 ) ? 'selected' : ''; ?>>50</option>
							<option value="100" <?php echo ( isset( $_SESSION[ 'RES_PAGE_ROWS' ] ) && $_SESSION[ 'RES_PAGE_ROWS' ] == 100 ) ? 'selected' : ''; ?>>100</option>
						</select>
					</div>
				</div>
				<div class="maintenanceForm">
					
					<input type="hidden" id="game_id" name="game_id" value="">
					<input type="hidden" id="game_class" name="game_class" value="">
					<input type="hidden" id="game_block" name="game_block" value="">
					<input type="hidden" id="game_count" name="game_count" value="">
					<input type="hidden" id="loser_id" name="loser_id" value="">
					<input type="hidden" id="team1_last_game_id" name="team1_last_game_id" value="">
					<input type="hidden" id="team2_last_game_id" name="team2_last_game_id" value="">

					<div class=form-group>
						<label for="game_name">試合</label>
						<input type="text" id="game_name" name="game_name" value="" disabled >
					</div>

					<div class=form-group>
						<label for="game_place">試合場所</label>
						<input type="text" id="game_place" name="game_place" value=""  >
						<label for="game_date">試合日</label>
						<input type="date" id="game_date" name="game_date" value=""  >
					</div>

					<div class=form-group>
						<input type="hidden" id="team1_id" name="team1_id" value="" >
						<label for="team1_name">チーム名１</label>
						<input type="text" id="team1_name" name="team1_name" value="" disabled>
						<label for="team1_score">得点</label>
						<input type="number" id="team1_score" name="team1_score" value="">
					</div>

					<div class=form-group>
						<input type="hidden" id="team2_id" name="team2_id" value="" >
						<label for="team2_name">チーム名２</label>
						<input type="text" id="team2_name" name="team2_name" value="" disabled>
						<label for="team2_score">得点</label>
						<input type="number" id="team2_score" name="team2_score" value="" >
					</div>

					<div class=form-group>
						<label for="winner_id">勝者</label>
						<select id="winner_id" name="winner_id">
						</select>
					</div>

					<div class=form-group>
						<label></label>
						<button type="submit" id="update_button" name="update_button" value="update"><?php echo "更新"; ?></button>
					</div>
				</div>

				<div class="teamdetail">
					<hr style="margin: 12px 0; border: none; border-top: 1px solid #ccc;">

					<div class=form-group>

						<select id="tournament_id" name="tournament_id" onchange="this.form.submit()">
							<?php foreach ( getTournamentList() as $tournament ) : ?>
								<option 
									value="<?php echo htmlspecialchars( $tournament[ 'tournament_id' ], ENT_QUOTES, 'UTF-8' ); ?>"
									<?php echo ( isset( $_SESSION[ 'RES_TOURNAMENT_ID' ] ) && $_SESSION[ 'RES_TOURNAMENT_ID' ] == $tournament[ 'tournament_id' ] ) ? 'selected' : ''; ?>
								>
									<?php echo htmlspecialchars( $tournament[ 'tournament_title' ] . ' ' . $tournament[ 'tournament_text' ], ENT_QUOTES, 'UTF-8' ); ?>
								</option>
							<?php endforeach; ?>
						</select>

							<label for="access_code">アクセスコード</label>
							<input type="text" id="access_code" name="access_code" value="<?php echo ( isset( $_SESSION[ 'RES_ACCESS_CODE' ] ) ) ? $_SESSION[ 'RES_ACCESS_CODE' ] : ''; ?>" required >

							<button type="submit" class="search_button" value="SEARCH">検索</button>

					</div>

					<table class="table" id="gameTable">
						<thead>
							<tr 
								data-trgameid=""
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
								<th>試合</th>
								<th>場所</th>
								<th>日付</th>
								<th>チーム名1</th>
								<th>得点1</th>
								<th>勝敗1</th>
								<th>勝敗2</th>
								<th>得点2</th>
								<th>チーム名2</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( getGameList2( $_SESSION[ 'RES_TOURNAMENT_ID' ], isset( $_SESSION[ 'RES_ACCESS_CODE' ] ) ? $_SESSION[ 'RES_ACCESS_CODE' ] : '', isset( $_SESSION[ 'RES_PAGE_CURRENT' ] ) ? $_SESSION[ 'RES_PAGE_CURRENT' ] : 1, isset( $_SESSION[ 'RES_PAGE_ROWS' ] ) ? $_SESSION[ 'RES_PAGE_ROWS' ] : 10 ) as $game ) : ?>
								<tr 
									data-trgameid="<?php echo $game[ 'game_id' ]; ?>"
									data-trtournamentid="<?php echo $game[ 'tournament_id' ]; ?>"
									data-trgameclass="<?php echo $game[ 'game_class' ]; ?>"
									data-trgameblock="<?php echo $game[ 'game_block' ]; ?>"
									data-trgamecount="<?php echo $game[ 'game_count' ]; ?>"
									data-trgamename="<?php echo htmlspecialchars( $game[ 'game_name' ], ENT_QUOTES, 'UTF-8' ); ?>"
									data-trgamedate="<?php echo ( new DateTime( $game[ 'game_date' ] ) )->format( 'Y-m-d' ); ?>"
									data-trgameplace="<?php echo htmlspecialchars( $game[ 'game_place' ], ENT_QUOTES, 'UTF-8' ); ?>"
									data-trwinnerid="<?php echo $game[ 'winner_id' ]; ?>"
									data-trloserid="<?php echo $game[ 'loser_id' ]; ?>"
									data-trteam1id="<?php echo $game[ 'team1_id' ]; ?>"
									data-trteam2id="<?php echo $game[ 'team2_id' ]; ?>"
									data-trteam1name="<?php echo htmlspecialchars( $game[ 'team1_name' ], ENT_QUOTES, 'UTF-8' ); ?>"
									data-trteam2name="<?php echo htmlspecialchars( $game[ 'team2_name' ], ENT_QUOTES, 'UTF-8' ); ?>"
									data-trteam1score="<?php echo $game[ 'team1_score' ]; ?>"
									data-trteam2score="<?php echo $game[ 'team2_score' ]; ?>"
									data-trteam1lastgameid="<?php echo $game[ 'team1_last_game_id' ]; ?>"
									data-trteam2lastgameid="<?php echo $game[ 'team2_last_game_id' ]; ?>"
								>
									<td class="text-center"><button type="button" class="detail_button" value="UPDATE">更新</button></td>
									<td><?php echo htmlspecialchars( $game[ 'game_name' ], ENT_QUOTES, 'UTF-8' ); ?></td>
									<td><?php echo htmlspecialchars( $game[ 'game_place' ], ENT_QUOTES, 'UTF-8' ); ?></td>
									<td><?php echo htmlspecialchars( ( new DateTime( $game[ 'game_date' ] ) )->format( 'Y-m-d' ), ENT_QUOTES, 'UTF-8' ); ?></td>
									<td><?php echo htmlspecialchars( $game[ 'team1_name' ], ENT_QUOTES, 'UTF-8' ); ?></td>
									<td><?php echo htmlspecialchars( $game[ 'team1_score' ], ENT_QUOTES, 'UTF-8' ); ?></td>
									<?php 
										$team1_result = "-";
										$team2_result = "-";

										if( $game[ 'team1_id' ] == 0 || $game[ 'team2_id' ] == 0 ){
											$team1_result = "-";
											$team2_result = "-";
										}else{
											switch ( $game[ 'winner_id' ] ) {
												case $game[ 'team1_id' ]:
													$team1_result = "勝";
													break;
												case $game[ 'team2_id' ]:
													$team1_result = "敗";
													break;
												default:
													$team1_result = "-";
													break;
											}
											switch ( $game[ 'winner_id' ] ) {
												case $game[ 'team1_id' ]:
													$team2_result = "敗";
													break;
												case $game[ 'team2_id' ]:
													$team2_result = "勝";
													break;
												default:
													$team2_result = "-";
													break;
											}
										}
									?>
									<td><?php echo htmlspecialchars( $team1_result, ENT_QUOTES, 'UTF-8' ); ?></td>
									<td><?php echo htmlspecialchars( $team2_result, ENT_QUOTES, 'UTF-8' ); ?></td>
									<td><?php echo htmlspecialchars( $game[ 'team2_score' ], ENT_QUOTES, 'UTF-8' ); ?></td>
									<td><?php echo htmlspecialchars( $game[ 'team2_name' ], ENT_QUOTES, 'UTF-8' ); ?></td>
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
	const game_id            = document.getElementById( "game_id" );
	const tournament_id      = document.getElementById( "tournament_id" );
	const game_class         = document.getElementById( "game_class" );
	const game_block         = document.getElementById( "game_block" );
	const game_count         = document.getElementById( "game_count" );
	const game_name          = document.getElementById( "game_name" );
	const game_date          = document.getElementById( "game_date" );
	const game_place         = document.getElementById( "game_place" );
	const winner_id          = document.getElementById( "winner_id" );
	const loser_id           = document.getElementById( "loser_id" );
	const team1_id           = document.getElementById( "team1_id" );
	const team2_id           = document.getElementById( "team2_id" );
	const team1_name         = document.getElementById( "team1_name" );
	const team2_name         = document.getElementById( "team2_name" );
	const team1_score        = document.getElementById( "team1_score" );
	const team2_score        = document.getElementById( "team2_score" );
	const team1_last_game_id = document.getElementById( "team1_last_game_id" );
	const team2_last_game_id = document.getElementById( "team2_last_game_id" );
	const update_button      = document.getElementById( "update_button" );

	document.getElementById( "gameTable" ).addEventListener( "click", ( e ) => {

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

			team1_score.focus();

			return;

		}

		const tr = btn.closest( "tr" );

		const tr_game_id = tr.dataset.trgameid;
		const tr_tournament_id = tr.dataset.trtournamentid;
		const tr_game_class = tr.dataset.trgameclass;
		const tr_game_block = tr.dataset.trgameblock;
		const tr_game_count = tr.dataset.trgamecount;
		const tr_game_name = tr.dataset.trgamename;
		const tr_game_date = tr.dataset.trgamedate;
		const tr_game_place = tr.dataset.trgameplace;
		const tr_winner_id = tr.dataset.trwinnerid;
		const tr_loser_id = tr.dataset.trloserid;
		const tr_team1_id = tr.dataset.trteam1id;
		const tr_team2_id = tr.dataset.trteam2id;
		const tr_team1_name = tr.dataset.trteam1name;
		const tr_team2_name = tr.dataset.trteam2name;
		const tr_team1_score = tr.dataset.trteam1score;
		const tr_team2_score = tr.dataset.trteam2score;
		const tr_team1_last_game_id = tr.dataset.trteam1lastgameid;
		const tr_team2_last_game_id = tr.dataset.trteam2lastgameid;

		game_id.value = tr_game_id;
		game_id.required = false;

		tournament_id.value = tr_tournament_id;
		tournament_id.required = false;
		
		game_class.value = tr_game_class;
		game_class.required = false;
		
		game_count.value = tr_game_count;
		game_count.required = false;
		
		game_block.value = tr_game_block;
		game_block.required = false;

		game_name.value = tr_game_name;
		game_name.required = false;
		game_name.disabled = true;

		game_date.value = tr_game_date;
		game_date.required = true;
		game_date.disabled = false;

		game_place.value = tr_game_place;
		game_place.required = true;
		game_place.disabled = false;

		winner_id.value = tr_winner_id;
		winner_id.required = false;
		loser_id.value = tr_loser_id;
		loser_id.required = false;

		team1_id.value = tr_team1_id;
		team1_id.required = true;

		team2_id.value = tr_team2_id;
		team2_id.required = true;

		team1_name.value = tr_team1_name;
		team1_name.disabled = true;

		team2_name.value = tr_team2_name;
		team2_name.disabled = true;

		team1_score.value = tr_team1_score;
		team1_score.required = false;
		team1_score.disabled = true;

		team2_score.value = tr_team2_score;
		team2_score.required = false;
		team2_score.disabled = true;

		team1_last_game_id.value = tr_team1_last_game_id;
		team1_last_game_id.required = true;

		team2_last_game_id.value = tr_team2_last_game_id;
		team2_last_game_id.required = true;

		update_button.style.display = "block";

		const select_winner = document.getElementById( 'winner_id' );

		select_winner.add( new Option( '-', 0 ) );
		select_winner.add( new Option( tr_team1_name, tr_team1_id ) );
		select_winner.add( new Option( tr_team2_name, tr_team2_id ) );

		switch ( tr_winner_id ) {
			case tr_team1_id:
				select_winner.options[1].selected = true;
				break;
			case tr_team2_id:
				select_winner.options[2].selected = true;
				break;
			default:
				select_winner.options[0].selected = true;
				break;
		}

		const el = document.querySelector( '.maintenanceForm' );
		el.style.display = 'block';

		game_place.focus();

	} );
</script>
<!-- Article Game Match End -->