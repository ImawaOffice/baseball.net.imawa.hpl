<?php
getTournamentOverviewFile();
# ==========================================================
# 大会登録内容追加処理
# @param int $p_tournamentId 更新対象の大会ID
# @return boolean 更新成功すればtrue、失敗すればfalse
# ==========================================================
function getTournamentOverviewFile(){

	global $g_Log;
	$g_Log->notice( "大会概要ファイル取得処理", __FUNCTION__, basename( __FILE__ ) );

	// SQL文作成
	// アップロードしたファイルの書込み
	$SQL = "";
	$SQL .= " SELECT ";
	$SQL .= "   attachment_id ";
	$SQL .= " , attachment_type ";
	$SQL .= " , physical_file_name ";
	$SQL .= " , file_name ";
	$SQL .= " , file_path ";
	$SQL .= " , file_size ";
	$SQL .= " , mime_type ";
	$SQL .= " , file_description ";
	$SQL .= "   FROM baseball_attachment ";
	$SQL .= "  WHERE is_enabled = 1 ";
	$SQL .= "    AND attachment_type = 2 ";	// attachment_typeが2のレコードを大会概要ファイルとして扱う
	$SQL .= "  ORDER BY attachment_id DESC ";	// 取得したレコードのうち、attachment_idが最大のレコードを大会規定ファイルとして扱う
	$SQL .= "  LIMIT 1 ";	// 取得件数を1件に限定
	
	$SQL_Parameters = array(
	);
	
	$_SESSION[ 'FORM_TOURNAMENT_OVERVIEW_ID' ]       = 0;		// 大会概要ファイルID
	$_SESSION[ 'FORM_TOURNAMENT_OVERVIEW_FILENAME' ] = '';		// 大会概要ファイル名
	$_SESSION[ 'FORM_TOURNAMENT_OVERVIEW_FILEPATH' ] = '';	// 大会概要ファイルパス

	try {
		global $g_DB;
		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );

		foreach( $dataTable as $row ){
			$_SESSION[ 'FORM_TOURNAMENT_OVERVIEW_ID' ] = $row[ 'attachment_id' ];
			$_SESSION[ 'FORM_TOURNAMENT_OVERVIEW_FILENAME' ] = $row[ 'file_name' ];
			$_SESSION[ 'FORM_TOURNAMENT_OVERVIEW_FILEPATH' ] = $row[ 'file_path' ] . $row[ 'physical_file_name' ];
			$g_Log->notice( "SESSION[ 'FORM_TOURNAMENT_OVERVIEW_ID' ] : {$row[ 'attachment_id' ]} セットしました", __FUNCTION__, basename( __FILE__ ) );
			$g_Log->notice( "SESSION[ 'FORM_TOURNAMENT_OVERVIEW_FILENAME' ] : {$row[ 'file_name' ]} セットしました", __FUNCTION__, basename( __FILE__ ) );
			$g_Log->notice( "SESSION[ 'FORM_TOURNAMENT_OVERVIEW_FILEPATH' ] : {$_SESSION[ 'FORM_TOURNAMENT_OVERVIEW_FILEPATH' ]} セットしました", __FUNCTION__, basename( __FILE__ ) );
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '大会概要ファイルの取得に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}
?>
<!-- Article Tournament Overview Start -->
<article class="tournament-overview" id="tournament-overview">
	<div class="container">
		<h2>大会概要</h2>
		<div class="tournament-overview-content">

			<div id="tournament-overview-pdf-container" style="width:100%;">
				<div id="tournament-overview-pdf-images" style="max-width:100%; height:auto; display:block;"></div>
			</div>

			<canvas id="tournament-overview-pdf-canvas" style="display:none;"></canvas>
		</div>
	</div>
</article>

<script defer>
// BODY読み込み時処理
window.addEventListener( 'load', () => {
//	onload_Body();
//	create_Tornament();
//	renderTournamentTable();
	PDFtoImage( '<?php echo $_SESSION[ 'FORM_TOURNAMENT_OVERVIEW_FILEPATH' ]; ?>', 'tournament-overview-pdf-canvas', 'tournament-overview-pdf-images' );
});
</script>
<!-- Article Tournament Overview End -->