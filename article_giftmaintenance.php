<!-- Article Game Match Start -->
<article class="gamematch" id="gamematch">
	<div class="container">
		<h2>記念品</h2>
		<div class="gamematch-content">
			<!-- Form -->
			<form method="post" action="" enctype="multipart/form-data" >
				<input type="hidden" name="view_state" value="<?php echo $_SESSION[ 'FROM_VIEW_STATE' ]; ?>">
				<input type="hidden" name="page_current" value="<?php echo $_SESSION[ 'FORM_PAGE_CURRENT' ]; ?>">

				<div id="errMsg" class="errMsg"><?php echo $_SESSION[ 'FORM_ERR_MSG' ]; ?></div>

				<div class="maintenanceForm">
					
					<input type="hidden" id="game_id" name="game_id" value="">
					<input type="hidden" id="game_class" name="game_class" value="">
					<input type="hidden" id="game_block" name="game_block" value="">
					<input type="hidden" id="game_count" name="game_count" value="">
					<input type="hidden" id="loser_id" name="loser_id" value="">
					<input type="hidden" id="team1_last_game_id" name="team1_last_game_id" value="">
					<input type="hidden" id="team2_last_game_id" name="team2_last_game_id" value="">
				</div>

				<div class="teamdetail">
					<hr style="margin: 12px 0; border: none; border-top: 1px solid #ccc;">

					<div class=form-group>

						<select id="tournament_id" name="tournament_id" onchange="this.form.submit()">
							<?php foreach ( getTournamentList() as $tournament ) : ?>
								<option 
									value="<?php echo htmlspecialchars( $tournament[ 'tournament_id' ], ENT_QUOTES, 'UTF-8' ); ?>"
									<?php echo ( isset( $_SESSION[ 'FROM_TOURNAMENT_ID' ] ) && $_SESSION[ 'FROM_TOURNAMENT_ID' ] == $tournament[ 'tournament_id' ] ) ? 'selected' : ''; ?>
								>
									<?php echo htmlspecialchars( $tournament[ 'tournament_title' ] . ' ' . $tournament[ 'tournament_text' ], ENT_QUOTES, 'UTF-8' ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="ruledetail">
						<hr style="margin: 12px 0; border: none; border-top: 1px solid #ccc;">
						<table class="table" id="ruleTable">
							<thead>
								<tr>
									<th>ルール</th>
									<th>ファイルアップロード</th>
									<th>ファイル名</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>記念品</td>
									<td><input type="file" id="filename_plan" name="filename_plan" value="" accept=".pdf" ></td>
									<td><?php echo htmlspecialchars( getAttachmentFile( 91 ) ); ?></td>
									<td class="text-center"><button type="submit" class="update_button" name="upload_button" value="91">アップロード</button></td>
								</tr>
							</tbody>
						</table>
					</div>
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