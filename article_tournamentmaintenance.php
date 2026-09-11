<!-- Article Tournament Maintenance Start -->
<article class="tournamentmaintenance" id="tournamentmaintenance">
	<div class="container">
		<h2>大会登録</h2>
		<div class="tournamentmaintenance-content">
			<!-- Form -->
			<form method="post" action="" enctype="multipart/form-data" >
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
				<div class="tournamentForm">
					
					<input type="hidden" id="tournament_id" name="tournament_id" value="">	
					<input type="hidden" id="tournament_attachment_id" name="tournament_attachment_id" value="">	

					<div class=form-group>
						<label for="tournament_startDate">大会開始日</label>
						<input type="date" id="tournament_startDate" name="tournament_startDate" value="" >
					</div>

					<div class=form-group>
						<label for="tournament_endDate">大会終了日</label>
						<input type="date" id="tournament_endDate" name="tournament_endDate" value="" >
					</div>

					<div class=form-group>
						<label for="tournament_title">大会名１</label>
						<input type="text" id="tournament_title" name="tournament_title" value="" >
					</div>

					<div class=form-group>
						<label for="tournament_text">大会名２</label>
						<input type="text" id="tournament_text" name="tournament_text" value="" >
					</div>

					<div class=form-group>
						<label for="tournament1_teams">総当たり戦参加チーム数</label>
						<input type="number" id="tournament1_teams" name="tournament1_teams" value="" >
					</div>

					<div class=form-group>
						<label for="tournament1_text">総当たり戦名称</label>
						<input type="text" id="tournament1_text" name="tournament1_text" value="" >
					</div>

					<div class=form-group>
						<label for="tournament2_teams">勝ち抜き戦参加チーム数</label>
						<input type="number" id="tournament2_teams" name="tournament2_teams" value="" >
					</div>

					<div class=form-group>
						<label for="tournament2_text">勝ち抜き戦名称</label>
						<input type="text" id="tournament2_text" name="tournament2_text" value="" >
					</div>

					<div class=form-group>
						<label for="tournament3_teams">敗者復活戦参加チーム数</label>
						<input type="number" id="tournament3_teams" name="tournament3_teams" value="" >
					</div>

					<div class=form-group>
						<label for="tournament3_text">敗者復活戦名称</label>
						<input type="text" id="tournament3_text" name="tournament3_text" value="" >
					</div>

					<div class=form-group>
						<label for="file_name">添付ファイル</label>
						<input type="file" id="file_name" name="file_name" value="" accept=".pdf" >
					</div>

					<div class=form-group>
						<label></label>
						<button type="submit" id="update_button" name="update_button" value="update"><?php echo "更新"; ?></button>
						<button type="submit" id="add_button" name="add_button" value="add"><?php echo "追加"; ?></button>
					</div>
				</div>

				<div class="tournamentdetail">
					<hr style="margin: 12px 0; border: none; border-top: 1px solid #ccc;">
					<table class="table" id="tournamentTable">
						<thead>
							<tr data-trid=""
								data-trstartdate=""
								data-trenddate=""
								data-trtitle=""
								data-trtext=""
								data-trtournament1teams=""
								data-trtournament1text=""
								data-trtournament2teams=""
								data-trtournament2text=""
								data-trtournament3teams=""
								data-trtournament3text=""
								data-trattachmentid=""
								data-trattachmentfile=""
							>
								<th class="text-center"><button type="button" class="detail_button" value="ADD">追加</button></th>
								<th>大会開始日</th>
								<th>大会終了日</th>
								<th>大会名１</th>
								<th>大会名２</th>
								<th>総当たり戦参加チーム数</th>
								<th>総当たり戦名称</th>
								<th>勝ち抜き戦参加チーム数</th>
								<th>勝ち抜き戦名称</th>
								<th>敗者復活戦参加チーム数</th>
								<th>敗者復活戦名称</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( getTournamentList( $_SESSION[ 'FORM_PAGE_CURRENT' ], $_SESSION[ 'FORM_PAGE_ROWS' ] ) as $tournament ) : ?>
								<?php
									// 日本語曜日配列（日曜=0）
									$week = [ '日', '月', '火', '水', '木', '金', '土' ];
									$tournamentStartAt = $tournament[ 'tournament_start_date' ];
									$dt = new DateTime( $tournamentStartAt );
									// 曜日番号を取得
									$w = $dt->format( 'w' );
									// フォーマットして表示
									$formattedStartDate = $dt->format( "Y-m-d ({$week[ $w ]})" );
									$tournamentEndAt = $tournament[ 'tournament_end_date' ];
									$dt = new DateTime( $tournamentEndAt );
									// 曜日番号を取得
									$w = $dt->format( 'w' );
									// フォーマットして表示
									$formattedEndDate = $dt->format( "Y-m-d ({$week[ $w ]})" );
								?>
								<tr data-trid="<?php echo $tournament[ 'tournament_id' ]; ?>"
									data-trstartdate="<?php echo htmlspecialchars( $tournament[ 'tournament_start_date' ] ); ?>"
									data-trenddate="<?php echo htmlspecialchars( $tournament[ 'tournament_end_date' ] ); ?>"
									data-trtitle="<?php echo htmlspecialchars( $tournament[ 'tournament_title' ] ); ?>"
									data-trtext="<?php echo htmlspecialchars( $tournament[ 'tournament_text' ] ); ?>"
									data-trtournament1teams="<?php echo htmlspecialchars( $tournament[ 'tournament1_teams' ] ); ?>"
									data-trtournament1text="<?php echo htmlspecialchars( $tournament[ 'tournament1_text' ] ); ?>"
									data-trtournament2teams="<?php echo htmlspecialchars( $tournament[ 'tournament2_teams' ] ); ?>"
									data-trtournament2text="<?php echo htmlspecialchars( $tournament[ 'tournament2_text' ] ); ?>"
									data-trtournament3teams="<?php echo htmlspecialchars( $tournament[ 'tournament3_teams' ] ); ?>"
									data-trtournament3text="<?php echo htmlspecialchars( $tournament[ 'tournament3_text' ] ); ?>"
									data-trattachmentid="<?php echo htmlspecialchars( $tournament[ 'tournament_attachment_id' ] ); ?>"
									data-trattachmentfile="<?php echo htmlspecialchars( $tournament[ 'file_name' ] ); ?>"
								>
									<td class="text-center"><button type="button" class="detail_button" value="UPDATE">更新</button></td>
									<td><?php echo htmlspecialchars( $formattedStartDate ); ?></td>
									<td><?php echo htmlspecialchars( $formattedEndDate ); ?></td>
									<td><?php echo htmlspecialchars( $tournament[ 'tournament_title' ] ); ?></td>
									<td><?php echo htmlspecialchars( $tournament[ 'tournament_text' ] ); ?></td>
									<td><?php echo htmlspecialchars( $tournament[ 'tournament1_teams' ] ); ?></td>
									<td><?php echo htmlspecialchars( $tournament[ 'tournament1_text' ] ); ?></td>
									<td><?php echo htmlspecialchars( $tournament[ 'tournament2_teams' ] ); ?></td>
									<td><?php echo htmlspecialchars( $tournament[ 'tournament2_text' ] ); ?></td>
									<td><?php echo htmlspecialchars( $tournament[ 'tournament3_teams' ] ); ?></td>
									<td><?php echo htmlspecialchars( $tournament[ 'tournament3_text' ] ); ?></td>
									<td class="text-center"><button type="submit" class="delete_button" name="delete_button" value="<?php echo $tournament[ 'tournament_id' ]; ?>"><?php echo "削除"; ?></button></td>
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
	const tournament_id        = document.getElementById( "tournament_id" );
	const tournament_startDate = document.getElementById( "tournament_startDate" );
	const tournament_endDate   = document.getElementById( "tournament_endDate" );
	const tournament_title     = document.getElementById( "tournament_title" );
	const tournament_text      = document.getElementById( "tournament_text" );
	const tournament1_teams    = document.getElementById( "tournament1_teams" );
	const tournament1_text     = document.getElementById( "tournament1_text" );
	const tournament2_teams    = document.getElementById( "tournament2_teams" );
	const tournament2_text     = document.getElementById( "tournament2_text" );
	const tournament3_teams    = document.getElementById( "tournament3_teams" );
	const tournament3_text     = document.getElementById( "tournament3_text" );
	const tournament_attachment_id = document.getElementById( "tournament_attachment_id" );
	const tournament_attachment_file = document.getElementById( "file_name" );
	const add_button           = document.getElementById( "add_button" );
	const update_button        = document.getElementById( "update_button" );

	document.getElementById( "tournamentTable" ).addEventListener( "click", ( e ) => {

		const btn = e.target.closest( ".detail_button" );

		if ( !btn ) return;

		if( btn.value === "ADD" ){
			tournament_id.value = "";
			tournament_id.required = false;

			tournament_startDate.value = "";
			tournament_startDate.required = true;

			tournament_endDate.value = "";
			tournament_endDate.required = true;

			tournament_title.value = "";
			tournament_title.required = true;

			tournament_text.value = "";
			tournament_text.required = true;

			tournament1_teams.value = "";
			tournament1_teams.required = true;
			tournament1_text.value = "";
			tournament1_text.required = false;
			
			tournament2_teams.value = "";
			tournament2_teams.required = true;
			
			tournament2_text.value = "";
			tournament2_text.required = false;
			
			tournament3_teams.value = "";
			tournament3_teams.required = true;
			
			tournament3_text.value = "";
			tournament3_text.required = false;

			tournament_attachment_id.value = "0";
			tournament_attachment_id.required = false;

			update_button.style.display = "none";
			add_button.style.display = "block";

			const el = document.querySelector( '.tournamentForm' );
			el.style.display = 'block';

			tournament_startDate.focus();

			return;

		}

		const tr = btn.closest( "tr" );

		const tr_id = tr.dataset.trid;
		const tr_startdate = tr.dataset.trstartdate;
		const tr_enddate = tr.dataset.trenddate;
		const tr_title = tr.dataset.trtitle;
		const tr_text = tr.dataset.trtext;
		const tr_tournament1teams = tr.dataset.trtournament1teams;
		const tr_tournament1text = tr.dataset.trtournament1text;
		const tr_tournament2teams = tr.dataset.trtournament2teams;
		const tr_tournament2text = tr.dataset.trtournament2text;
		const tr_tournament3teams = tr.dataset.trtournament3teams;
		const tr_tournament3text = tr.dataset.trtournament3text;
		const tr_tournamentattachmentid = tr.dataset.trattachmentid;
		const tr_tournamentattachmentfile = tr.dataset.trattachmentfile;

		tournament_id.value = tr_id;
		tournament_id.required = true;

		tournament_startDate.value = tr_startdate;
		tournament_startDate.required = true;
		
		tournament_endDate.value = tr_enddate;
		tournament_endDate.required = true;
		
		tournament_title.value = tr_title;
		tournament_title.required = true;
		
		tournament_text.value = tr_text;
		tournament_text.required = true;
		
		tournament1_teams.value = tr_tournament1teams;
		tournament1_teams.required = true;	
		
		tournament1_text.value = tr_tournament1text;
		tournament1_text.required = false;
		
		tournament2_teams.value = tr_tournament2teams;
		tournament2_teams.required = true;
		
		tournament2_text.value = tr_tournament2text;
		tournament2_text.required = false;
		
		tournament3_teams.value = tr_tournament3teams;
		tournament3_teams.required = true;
		
		tournament3_text.value = tr_tournament3text;
		tournament3_text.required = false;
		
		tournament_attachment_id.value = tr_tournamentattachmentid;
		tournament_attachment_id.required = false;

		document.querySelector( '.remove-file-name' )?.remove();

		if( tr_tournamentattachmentfile !== "" ){
			const ElemFileName = document.createElement( 'span' );
			ElemFileName.textContent = tr_tournamentattachmentfile;
			ElemFileName.className = "remove-file-name";
			tournament_attachment_file.after( ElemFileName );
		}

		update_button.style.display = "block";
		add_button.style.display = "block";

		const el = document.querySelector( '.tournamentForm' );
		el.style.display = 'block';

		tournament_startDate.focus();

	} );
</script>
<!-- Article Tournament Maintenance End -->