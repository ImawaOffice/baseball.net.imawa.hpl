<?php
# **********************************************************
# トップページ
# **********************************************************

# ==========================================================
# 必要なファイル読み込み
# ==========================================================
require_once( __DIR__ . DIRECTORY_SEPARATOR . 'baseball_function.php' );

# ==========================================================
# 変数の定義
# ==========================================================
$g_Log->notice( "S : " . basename( __FILE__ ), __FUNCTION__, basename( __FILE__ ) );

foreach( $_POST as $key => $value ){
	if( is_array( $value ) || is_object( $value ) ){
		$value = print_r( $value, true );
	}
	$g_Log->notice( "POST : {$key} = {$value}", __FUNCTION__, basename( __FILE__ ) );
}

foreach( $_SESSION as $key => $value ){
	if( is_array( $value ) || is_object( $value ) ){
		$value = print_r( $value, true );
	}
	$g_Log->notice( "SESSION : {$key} = {$value}", __FUNCTION__, basename( __FILE__ ) );
}

# ==========================================================
# 主処理
# ==========================================================
if( initProc() ){
	// リクエスト処理
	if( requestProc() ) {
		// チェック処理
		if( checkProc() ) {
			// セット処理
			if( setProc() ) {
			}
		}
	}
}
// 終了処理
if( endProc() ) {
}

# ==========================================================
# 主処理
# ==========================================================

# ==========================================================
# 初期設定
# ==========================================================
function initProc() {

	global $g_Log;
	$g_Log->notice( "初期処理", __FUNCTION__, basename( __FILE__ ) );

		
	// セッション変数の初期化
	$sessionList = [
		'FROM_VIEW_STATE'     => 0,		// 表示ステータス(0: 初期画面)
		'FORM_ERR_MSG'       => '',		// エラーメッセージ
		'RES_SEND_BUTTON'   => '送信',	// 送信ボタン
		'RES_NAME'          => '',		// 名前
		'RES_PHONE'         => '',		// 電話番号
		'RES_EMAIL'         => '',		// メールアドレス
		'RES_INQUIRY'       => '',		// 問合せ内容
		'FORM_PAGE_ROWS'     => 10,		// 表示行数
		'FORM_PAGE_CURRENT'  => 1,		// ページ番号
		'FORM_PAGE_OFFSET'   => 0,		// 表示オフセット
		'FORM_PAGE_TOTAL'    => 0,		// 総ページ数
		'FORM_PAGE_TOP'      => 1,		// ページの最初
		'FORM_PAGE_LAST'     => 1,		// ページの最後
		'FORM_PAGE_PREVIOUS' => 1,		// 前のページ
		'FORM_PAGE_NEXT'     => 1,		// 次のページ
	];

	// セッション変数の設定
	foreach ( $sessionList as $name => $value ) {
		$_SESSION[ $name ] = $value;
	}

	return true;
}

# ==========================================================
# リクエスト処理
# ==========================================================
function requestProc() {

	global $g_Log;
	$g_Log->notice( "リクエスト処理", __FUNCTION__, basename( __FILE__ ) );
	$g_Log->notice( "REQUEST_METHOD : " . $_SERVER[ 'REQUEST_METHOD' ], __FUNCTION__, basename( __FILE__ ) );

	// GETリクエストの場合は処理なし
	if ( $_SERVER[ 'REQUEST_METHOD' ] === 'GET' ) {
		$g_Log->notice( "GETリクエストのため以降の処理なし", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// GET, POSTリクエスト以外の場合
	if ( $_SERVER[ 'REQUEST_METHOD' ] !== 'POST' ) {
		$g_Log->notice( "GET/POSTリクエスト以外のため以降の処理なし", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// POSTリクエストの場合
	// view_stateの取得
	if( isset( $_POST[ 'view_state' ] ) ) {
		$g_Log->notice( "POST : view_state = " . $_POST[ 'view_state' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// ページトップボタンの取得
	if( isset( $_POST[ 'page_top' ] ) ) {
		$g_Log->notice( "POST : page_top = " . $_POST[ 'page_top' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// ページ前へボタンの取得
	if( isset( $_POST[ 'page_previous' ] ) ) {
		$g_Log->notice( "POST : page_previous = " . $_POST[ 'page_previous' ], __FUNCTION__, basename( __FILE__ ) );
	}
	
	// 現在のページ番号の取得
	if( isset( $_POST[ 'page_current' ] ) ) {
		$g_Log->notice( "POST : page_current = " . $_POST[ 'page_current' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// ページ次へボタンの取得
	if( isset( $_POST[ 'page_next' ] ) ) {
		$g_Log->notice( "POST : page_next = " . $_POST[ 'page_next' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// ページ最後へボタンの取得
	if( isset( $_POST[ 'page_last' ] ) ) {
		$g_Log->notice( "POST : page_last = " . $_POST[ 'page_last' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// 表示件数の取得
	if( isset( $_POST[ 'page_rows' ] ) ) {
		$g_Log->notice( "POST : page_rows = " . $_POST[ 'page_rows' ], __FUNCTION__, basename( __FILE__ ) );
	}

	return true;
}

# ==========================================================
# チェック処理
# ==========================================================
function checkProc() {

	global $g_Log;
	$g_Log->notice( "チェック処理", __FUNCTION__, basename( __FILE__ ) );

	// 削除対象のID
	if( isset( $_POST[ 'delete_button' ] ) ) {
		$g_Log->notice( "POST : delete_button = " . $_POST[ 'delete_button' ], __FUNCTION__, basename( __FILE__ ) );
		if ( deleteInquiryId( $_POST[ 'delete_button' ] ) ) {
			$g_Log->notice( "問合せを削除しました： delete_button = " . $_POST[ 'delete_button' ], __FUNCTION__, basename( __FILE__ ) );
		} else {
			$g_Log->notice( "問合せの削除に失敗しました： delete_button = " . $_POST[ 'delete_button' ], __FUNCTION__, basename( __FILE__ ) );
			$_SESSION[ 'FORM_ERR_MSG' ] = "問合せの削除に失敗しました。";
			return false;
		}
	}

	// 表示行数
	$pageRows	  = ( int )( $_POST[ 'page_rows' ] ?? $_SESSION[ 'FORM_PAGE_ROWS' ] ?? 2 );
	$_SESSION[ 'FORM_PAGE_ROWS' ]     = $pageRows;
	$g_Log->notice( "[ FORM_PAGE_ROWS ] : $pageRows => " . $_SESSION[ 'FORM_PAGE_ROWS' ], __FUNCTION__, basename( __FILE__ ) );

	// 総件数
	$recordsCount = getInquiryCount();
	$pageTotal    = ( int )ceil( $recordsCount / $pageRows );
	$_SESSION[ 'FORM_PAGE_TOTAL' ]    = $pageTotal;
	$g_Log->notice( "[ FORM_PAGE_TOTAL ] : $pageTotal => " . $_SESSION[ 'FORM_PAGE_TOTAL' ], __FUNCTION__, basename( __FILE__ ) );
	
	// 現在ページ
	$pageCurrent  = ( int )( $_POST[ 'page_current' ] ?? $_SESSION[ 'FORM_PAGE_CURRENT' ] ?? 1 );
	$_SESSION[ 'FORM_PAGE_CURRENT' ]  = $pageCurrent;
	$g_Log->notice( "[ FORM_PAGE_CURRENT ] : $pageCurrent => " . $_SESSION[ 'FORM_PAGE_CURRENT' ], __FUNCTION__, basename( __FILE__ ) );

	// ページの最初
	if ( isset( $_POST[ 'page_top' ] ) ) {
		$pageCurrent = 1;
		$_SESSION[ 'FORM_PAGE_CURRENT' ] = $pageCurrent;
		$g_Log->notice( "[ FORM_PAGE_CURRENT ] : $pageCurrent => " . $_SESSION[ 'FORM_PAGE_CURRENT' ], __FUNCTION__, basename( __FILE__ ) );
	}

	$pageTop      = 1;
	$_SESSION[ 'FORM_PAGE_TOP' ]      = $pageTop;
	$g_Log->notice( "[ FORM_PAGE_TOP ] : $pageTop => " . $_SESSION[ 'FORM_PAGE_TOP' ], __FUNCTION__, basename( __FILE__ ) );

	// ページの最後
	if ( isset( $_POST[ 'page_last' ] ) ) {
		$pageCurrent = $pageTotal;
		$_SESSION[ 'FORM_PAGE_CURRENT' ] = $pageCurrent;
		$g_Log->notice( "[ FORM_PAGE_CURRENT ] : $pageCurrent => " . $_SESSION[ 'FORM_PAGE_CURRENT' ], __FUNCTION__, basename( __FILE__ ) );
	}

	$pageLast     = $pageTotal;
	$_SESSION[ 'FORM_PAGE_LAST' ]     = $pageLast;
	$g_Log->notice( "[ FORM_PAGE_LAST ] : $pageLast => " . $_SESSION[ 'FORM_PAGE_LAST' ], __FUNCTION__, basename( __FILE__ ) );

	// ページ前へ
	if ( isset( $_POST[ 'page_previous' ] ) ) {
		$pageCurrent = max( 1, $pageCurrent - 1 );
		$_SESSION[ 'FORM_PAGE_CURRENT' ] = $pageCurrent;
		$g_Log->notice( "[ FORM_PAGE_CURRENT ] : $pageCurrent => " . $_SESSION[ 'FORM_PAGE_CURRENT' ], __FUNCTION__, basename( __FILE__ ) );
	}

	if ( isset( $_POST[ 'page_next' ] ) ) {
		$pageCurrent = min( $pageTotal, $pageCurrent + 1 );
		$_SESSION[ 'FORM_PAGE_CURRENT' ] = $pageCurrent;
		$g_Log->notice( "[ FORM_PAGE_CURRENT ] : $pageCurrent => " . $_SESSION[ 'FORM_PAGE_CURRENT' ], __FUNCTION__, basename( __FILE__ ) );
	}

	$pagePrevious = max( 1, $pageCurrent - 1 );
	$_SESSION[ 'FORM_PAGE_PREVIOUS' ] = $pagePrevious;
	$g_Log->notice( "[ FORM_PAGE_PREVIOUS ] : $pagePrevious => " . $_SESSION[ 'FORM_PAGE_PREVIOUS' ], __FUNCTION__, basename( __FILE__ ) );

	$pageNext     = min( $pageTotal, $pageCurrent + 1 );
	$_SESSION[ 'FORM_PAGE_NEXT' ]     = $pageNext;
	$g_Log->notice( "[ FORM_PAGE_NEXT ] : $pageNext => " . $_SESSION[ 'FORM_PAGE_NEXT' ], __FUNCTION__, basename( __FILE__ ) );

	// 表示オフセット
	$pageOffset = ( $pageCurrent - 1 ) * $pageRows;
	$_SESSION[ 'FORM_PAGE_OFFSET' ]   = $pageOffset;
	$g_Log->notice( "[ FORM_PAGE_OFFSET ] : $pageOffset => " . $_SESSION[ 'FORM_PAGE_OFFSET' ], __FUNCTION__, basename( __FILE__ ) );

	return true;
}

# ==========================================================
# セット処理
# ==========================================================
function setProc() {

	global $g_Log;
	$g_Log->notice( "セット処理", __FUNCTION__, basename( __FILE__ ) );

	$_SESSION[ 'FROM_VIEW_STATE' ] = trim( $_POST[ 'view_state' ] );
	$g_Log->notice( "[ FROM_VIEW_STATE ] : " . $_SESSION[ 'FROM_VIEW_STATE' ], __FUNCTION__, basename( __FILE__ ) );

	return true;
}

# ==========================================================
# 終了処理
# ==========================================================
function endProc() {

	global $g_Log;
	$g_Log->notice( "終了処理", __FUNCTION__, basename( __FILE__ ) );

	// データベース切断
	if( ! dbDisConnect() ) {
		return false;
	}

	return true;
}

# ==========================================================
# データベース切断
# ==========================================================
function dbDisConnect() {

	global $g_Log;
	$g_Log->notice( "データベース切断処理", __FUNCTION__, basename( __FILE__ ) );

	global $g_SQLite, $g_DB1_DbType, $g_DB1_HostName;
	
	if ( $g_SQLite !== null ) {
		$g_SQLite = null;
		$g_Log->notice( "データベースから切断しました： $g_DB1_DbType:$g_DB1_HostName", __FUNCTION__, basename( __FILE__ ) );
	}

	return true;
}
?>

<?php
	// HTMLヘッダーの読み込み
	$filename = __DIR__ . DIRECTORY_SEPARATOR . "html_head.php";
	if ( file_exists( $filename ) ){
		require_once( $filename );
	}
?>
<body>

<?php
	// ページヘッダーの読み込み
	$filename = __DIR__ . DIRECTORY_SEPARATOR . "page_header.php";
	if ( file_exists( $filename ) ){
		require_once( $filename );
	}
?>

<main class="page-main">

<?php
	// お問合せ一覧の読み込み
	$filename = __DIR__ . DIRECTORY_SEPARATOR . "article_contactlist.php";
	if ( file_exists( $filename ) ){
		require_once( $filename );
	}
?>

</main>

<?php
	// ページフッターの読み込み
	$filename = __DIR__ . DIRECTORY_SEPARATOR . "page_footer.php";
	if ( file_exists( $filename ) ){
		require_once( $filename );
	}
?>

<?php
	// スクロールトップボタンの読み込み
	$filename = __DIR__ . DIRECTORY_SEPARATOR . "scrolltop.php";
	if ( file_exists( $filename ) ){
		require_once( $filename );
	}
?>
</body>
</html>
