<!-- Article Information Start -->
<?php
# **********************************************************
# お知らせページ
# **********************************************************

# ==========================================================
# 必要なファイル読み込み
# ==========================================================
require_once( __DIR__ . DIRECTORY_SEPARATOR . 'baseball_config.php' );

# ==========================================================
# 変数の定義
# ==========================================================

# ==========================================================
# セッションの開始
# ==========================================================
if( session_status() === PHP_SESSION_NONE ){
	$g_Log->notice( "セッション開始", __FUNCTION__, basename( __FILE__ ) );
	session_start();
}

foreach( $_SESSION as $key => $value ){
	if( is_array( $value ) || is_object( $value ) ){
		$value = print_r( $value, true );
	}
	$g_Log->notice( "SESSION : {$key} = {$value}", __FUNCTION__, basename( __FILE__ ) );
}

# ==========================================================
# お知らせ取得
# ==========================================================
function getInformation(){

	global $g_Log;
	$g_Log->notice( "お知らせ取得処理", __FUNCTION__, basename( __FILE__ ) );

	// SQL文作成
	$SQL = "";
	$SQL .= "SELECT ";
	$SQL .= "  information_id ";
	$SQL .= ", title ";
	$SQL .= ", content ";
	$SQL .= ", post_date ";
	$SQL .= ", IFNULL( reference_start_date, CURRENT_DATE ) AS reference_start_date ";
	$SQL .= ", IFNULL( reference_end_date, CURRENT_DATE ) AS reference_end_date ";
	$SQL .= "  FROM baseball_information ";
	$SQL .= " WHERE is_enabled = 1 ";
	$SQL .= "   AND IFNULL( reference_start_date, CURRENT_DATE ) <= CURRENT_DATE ";
	$SQL .= "   AND IFNULL( reference_end_date, CURRENT_DATE ) >= CURRENT_DATE ";
	$SQL .= " ORDER BY post_date DESC ";
	
	$SQL_Parameters = array(
	);

	$dataTable = array();

	try {

		global $g_DB;
		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );
		
		if( count( $dataTable ) === 0 ) {
			$g_Log->notice( "お知らせデータが存在しません。", __FUNCTION__, basename( __FILE__ ) );
			return $dataTable;
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'error_msg' ] = 'お知らせ取得処理でエラーが発生しました。';
		$g_Log->error( $_SESSION[ 'error_msg' ] . "\n" . $e->getMessage(), __FUNCTION__, basename( __FILE__ ) );
		return $dataTable;
	}

	return $dataTable;
}
?>
<article class="information">
	<div class="container">
		<h2>お知らせ</h2>
		<div class="information-content">

			<?php
				// お知らせの取得
				$informationList = getInformation();
			?>
			<?php if ( empty( $informationList ) ): ?>
				<p class="information-item"></p>
			<?php else: ?>
				<dl class="information-list">
					<?php foreach ( $informationList as $information ): ?>
						<dt class="information-date"><?php echo htmlspecialchars( $information[ 'post_date' ] ); ?></dt>
						<dd class="information-title"><?php echo htmlspecialchars( $information[ 'title' ] ); ?></dd>
					<?php endforeach; ?>
				</dl>
			<?php endif; ?>
			
			<video class="information-video" controls loop playsinline webkit-playsinline>
				<source src="images/1784030615736.mp4" type="video/mp4">
				Your browser does not support the video tag.
			</video>
		</div>
	</div>
</article>
<!-- Article Information End -->
