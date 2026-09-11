<!-- Article Tournament Maintenance Start -->
<article class="tournamentmaintenance" id="tournamentmaintenance">
	<div class="container">
		<h2>大会登録</h2>
		<div class="tournamentmaintenance-content">
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
				<div class="tournamentForm">
					
					<input type="hidden" id="tournament_id" name="tournament_id" value="">	

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
							>
								<th class="text-center"><button type="button" class="detail_button" value="ADD">追加</button></th>
								<th>大会開始日</th>
								<th>大会終了日</th>
								<th>大会名１</th>
								<th>大会名２</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( getTournamentList( $_SESSION[ 'RES_PAGE_CURRENT' ], $_SESSION[ 'RES_PAGE_ROWS' ] ) as $tournament ) : ?>
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
								>
									<td class="text-center"><button type="button" class="detail_button" value="UPDATE">更新</button></td>
									<td><?php echo htmlspecialchars( $formattedStartDate ); ?></td>
									<td><?php echo htmlspecialchars( $formattedEndDate ); ?></td>
									<td><?php echo htmlspecialchars( $tournament[ 'tournament_title' ] ); ?></td>
									<td><?php echo htmlspecialchars( $tournament[ 'tournament_text' ] ); ?></td>
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
		update_button.style.display = "block";
		add_button.style.display = "block";

		const el = document.querySelector( '.tournamentForm' );
		el.style.display = 'block';

		tournament_startDate.focus();

	} );
</script>
<!-- Article Tournament Maintenance End -->