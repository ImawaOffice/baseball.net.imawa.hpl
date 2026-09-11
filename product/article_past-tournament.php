<!-- Article Past Tournament Start -->
<?php
# ==========================================================
# 必要なファイル読み込み
# ==========================================================
require_once( __DIR__ . DIRECTORY_SEPARATOR . 'baseball_config.php' );

// 過去大会データの取得
$tournamentData = getPastTournaments();

# ==========================================================
# 過去大会データの取得
# ==========================================================
function getPastTournaments(){

	global $g_Log;
	$g_Log->debug( "過去大会データの取得", __FUNCTION__, basename( __FILE__ ) );

	// SQL文作成
	$SQL = "";
	$SQL .= "SELECT ";
	$SQL .= "  tournament_id ";
	$SQL .= ", tournament_title ";
	$SQL .= ", tournament_text ";
	$SQL .= "FROM baseball_tournament ";
	$SQL .= "WHERE is_enabled = 1 ";
	$SQL .= "AND tournament_end_date < CURRENT_DATE() ";
	$SQL .= "ORDER BY tournament_end_date DESC ";
	$SQL .= ", tournament_id ";

	$dataTable = array();

	try {
		global $g_DB;
		$dataTable = $g_DB->select( $SQL );

	} catch ( Exception $e ) {
		$g_Log->error( "過去大会データの取得でエラーが発生しました。" . "\n" . $e->getMessage(), __FUNCTION__, basename( __FILE__ ) );
		return $dataTable;
	}

	return $dataTable;
}

?>
<article class="past-tournament" id="past-tournament">
	<div class="container">
		<?php if( count( $tournamentData ) > 0 ) : ?>
			<h2>過去大会</h2>
			<div class="past-tournament-content">
				<?php
					foreach( $tournamentData as $tournament ){
						$tournamentId = htmlEscape( $tournament['tournament_id'] );
						$tournamentTitle = htmlEscape( $tournament['tournament_title'] );
						$tournamentText = htmlEscape( $tournament['tournament_text'] );
				?>
						<div class="tournament-item">
							<a class="tournament-link" href="./tournament?id=<?php echo $tournamentId; ?>"><?php echo "$tournamentTitle $tournamentText"; ?></a>
						</div>
				<?php
					}
				?>
			</div>
		<?php endif; ?>
	</div>
</article>
<!-- Article Past Tournament End -->
