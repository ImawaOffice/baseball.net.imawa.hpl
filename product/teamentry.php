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
$g_Log->debug( "S : " . basename( __FILE__ ), __FUNCTION__, basename( __FILE__ ) );

foreach( $_POST as $key => $value ){
	if( is_array( $value ) || is_object( $value ) ){
		$value = print_r( $value, true );
	}
	$g_Log->debug( "POST : {$key} = {$value}", __FUNCTION__, basename( __FILE__ ) );
}

foreach( $_SESSION as $key => $value ){
	if( is_array( $value ) || is_object( $value ) ){
		$value = print_r( $value, true );
	}
	$g_Log->debug( "SESSION : {$key} = {$value}", __FUNCTION__, basename( __FILE__ ) );
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
# 初期設定
# ==========================================================
function initProc() {

	global $g_Log;
	$g_Log->debug( "初期処理", __FUNCTION__, basename( __FILE__ ) );

		
	// セッション変数の初期化
	$sessionList = [
		'RES_VIEW_STATE'    => 0,		// 表示ステータス(0: 初期画面)
		'RES_ERR_MSG'       => '',		// エラーメッセージ
		'RES_PAGE_ROWS'     => 10,		// 表示行数
		'RES_PAGE_CURRENT'  => 1,		// ページ番号
		'RES_PAGE_OFFSET'   => 0,		// 表示オフセット
		'RES_PAGE_TOTAL'    => 0,		// 総ページ数
		'RES_PAGE_TOP'      => 1,		// ページの最初
		'RES_PAGE_LAST'     => 1,		// ページの最後
		'RES_PAGE_PREVIOUS' => 1,		// 前のページ
		'RES_PAGE_NEXT'     => 1,		// 次のページ
		'RES_BUTTON_ADD'    => 'ADD',	// 追加ボタン
		'RES_BUTTON_UPDATE' => 'UPDATE',	// 更新ボタン
		'RES_BUTTON_DELETE' => 'DELETE',	// 削除ボタン
		'RES_BUTTON_SEND'   => 'SEND',	// 送信ボタン
		'RES_TOURNAMENT_ID' => '',		// トーナメントID
		'RES_TEAM_NAME'     => '',		// チーム名
		'RES_TEAM_MANAGER'  => '',		// 責任者名
		'RES_TEAM_CONTACT'  => '',		// 連絡先
		'RES_AGREE'         => '',		// 同意（ハニーポット）
	];

	// セッション変数の設定
	foreach ( $sessionList as $name => $value ) {
		$_SESSION[ $name ] = $value;
	}
	
	// CSRFトークン生成（なければ作る）
	if ( empty( $_SESSION[ 'RES_TOKEN' ] ) ) {
		$_SESSION[ 'RES_TOKEN' ] = bin2hex( random_bytes( 32 ) );
		$g_Log->debug( "CSRFトークンを生成しました : " . $_SESSION[ 'RES_TOKEN' ], __FUNCTION__, basename( __FILE__ ) );
	}

	return true;
}

# ==========================================================
# リクエスト処理
# ==========================================================
function requestProc() {

	global $g_Log;
	$g_Log->debug( "リクエスト処理", __FUNCTION__, basename( __FILE__ ) );
	$g_Log->debug( "REQUEST_METHOD : " . $_SERVER[ 'REQUEST_METHOD' ], __FUNCTION__, basename( __FILE__ ) );

	// GETリクエストの場合は処理なし
	if ( $_SERVER[ 'REQUEST_METHOD' ] === 'GET' ) {
		$g_Log->debug( "GETリクエストのため以降の処理なし", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// GET, POSTリクエスト以外の場合
	if ( $_SERVER[ 'REQUEST_METHOD' ] !== 'POST' ) {
		$g_Log->debug( "GET/POSTリクエスト以外のため以降の処理なし", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// POSTリクエストの場合
	// view_stateの取得
	if( isset( $_POST[ 'view_state' ] ) ) {
		$g_Log->debug( "POST : view_state = " . $_POST[ 'view_state' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// CSRFトークンの取得
	if( isset( $_POST[ 'token' ] ) ) {
		$g_Log->debug( "POST : token = " . $_POST[ 'token' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// 同意の取得
	if( isset( $_POST[ 'agree' ] ) ) {
		$g_Log->debug( "POST : agree = " . $_POST[ 'agree' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// 大会IDの取得
	if( isset( $_POST[ 'tournament_id' ] ) ) {
		$g_Log->debug( "POST : tournament_id = " . $_POST[ 'tournament_id' ], __FUNCTION__, basename( __FILE__ ) );
	}
	
	// チーム名の取得
	if( isset( $_POST[ 'team_name' ] ) ) {
		$g_Log->debug( "POST : team_name = " . $_POST[ 'team_name' ], __FUNCTION__, basename( __FILE__ ) );
	}
	
	// 責任者名の取得
	if( isset( $_POST[ 'team_manager' ] ) ) {
		$g_Log->debug( "POST : team_manager = " . $_POST[ 'team_manager' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// 連絡先の取得
	if( isset( $_POST[ 'team_contact' ] ) ) {
		$g_Log->debug( "POST : team_contact = " . $_POST[ 'team_contact' ], __FUNCTION__, basename( __FILE__ ) );
	}

	return true;
}

# ==========================================================
# チェック処理
# ==========================================================
function checkProc() {

	global $g_Log;
	$g_Log->debug( "チェック処理", __FUNCTION__, basename( __FILE__ ) );

	// 連続投稿の制限チェック
	if( ! rateLimitOrFail( pathinfo( __FILE__, PATHINFO_FILENAME ) ) ) {
		return false;
	}

	// ハニーポットでスパムチェック
	if( ! verifyHoneypotOrFail( $_POST[ 'agree' ] ?? '' ) ) {
		return false;
	}

	// CSRFトークンでスパムチェック
	if( ! verifyCsrfOrFail( $_POST[ 'token' ] ?? '', $_SESSION[ 'RES_TOKEN' ] ?? '' ) ) {
		return false;
	}

	// 入力値のチェック
	$tournament_id = trim( ( string )( $_POST[ 'tournament_id' ] ?? '' ) );
	$team_name     = trim( ( string )( $_POST[ 'team_name' ] ?? '' ) );
	$manager_name  = trim( ( string )( $_POST[ 'team_manager' ] ?? '' ) );
	$contact       = trim( ( string )( $_POST[ 'team_contact' ] ?? '' ) );

	// チーム名のチェック
	if ( $team_name == '' ) {
		$_SESSION[ 'RES_ERR_MSG' ] = 'チーム名を入力して下さい。';
		$g_Log->warning( $_SESSION[ 'RES_ERR_MSG' ] . " : " . $team_name, __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 責任者名のチェック
	if ( $manager_name == '' ) {
		$_SESSION[ 'RES_ERR_MSG' ] = '責任者名を入力して下さい。';
		$g_Log->warning( $_SESSION[ 'RES_ERR_MSG' ] . " : " . $manager_name, __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 連絡先のチェック
	if ( $contact == '' ) {
		$_SESSION[ 'RES_ERR_MSG' ] = '連絡先を入力して下さい。';
		$g_Log->warning( $_SESSION[ 'RES_ERR_MSG' ] . " : " . $contact, __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# セット処理
# ==========================================================
function setProc() {

	global $g_Log;
	$g_Log->debug( "セット処理", __FUNCTION__, basename( __FILE__ ) );

	$_SESSION[ 'RES_VIEW_STATE' ] = trim( $_POST[ 'view_state' ] );
	$_SESSION[ 'RES_TOURNAMENT_ID' ] = trim( $_POST[ 'tournament_id' ] );
	$_SESSION[ 'RES_TEAM_NAME' ] = trim( $_POST[ 'team_name' ] );
	$_SESSION[ 'RES_TEAM_MANAGER' ] = trim( $_POST[ 'team_manager' ] );
	$_SESSION[ 'RES_TEAM_CONTACT' ] = trim( $_POST[ 'team_contact' ] );

	// チーム情報登録
	if( ! setTeamInfo() ) {
		return false;
	}

	// 登録完了後はセッション変数をクリア
	$_SESSION[ 'RES_TOKEN' ] = bin2hex( random_bytes( 32 ) );
	unset( $_SESSION[ 'RES_TEAM_NAME' ] );
	unset( $_SESSION[ 'RES_TEAM_MANAGER' ] );
	unset( $_SESSION[ 'RES_TEAM_CONTACT' ] );

	return true;
}

# ==========================================================
# チーム情報登録処理
# ==========================================================
function setTeamInfo() {

	global $g_Log;
	$g_Log->debug( "チーム情報登録処理", __FUNCTION__, basename( __FILE__ ) );

	// 6桁までの数字をランダム生成
	$randomNumber = random_int( 0, 999999 );

	// 6桁の認証コード生成
	$randomCode = str_pad( strval( $randomNumber ), 6, '0', STR_PAD_LEFT );

	$g_Log->debug( "チームアクセスコードを作成しました : " . $randomCode, __FUNCTION__, basename( __FILE__ ) );

	// セッション変数から値を取得
	$tournament_id  = $_SESSION[ 'RES_TOURNAMENT_ID' ];
	$team_name      = $_SESSION[ 'RES_TEAM_NAME' ];
	$manager_name   = $_SESSION[ 'RES_TEAM_MANAGER' ];
	$contact        = $_SESSION[ 'RES_TEAM_CONTACT' ];
	$team_access_cd = $randomCode;


	// SQL文作成
	$SQL = "";
	$SQL .= "INSERT INTO baseball_team ";
	$SQL .= "( team_name ";
	$SQL .= ", team_manager ";
	$SQL .= ", team_contact ";
	$SQL .= ", team_access_cd ";
	$SQL .= ", tournament_id ";
	$SQL .= ", is_enabled ";
	$SQL .= ", created_at ";
	$SQL .= ", created_by ";
	$SQL .= ", updated_at ";
	$SQL .= ", updated_by ";
	$SQL .= ") ";
	$SQL .= "VALUES ";
	$SQL .= "( :team_name ";
	$SQL .= ", :team_manager ";
	$SQL .= ", :team_contact ";
	$SQL .= ", :team_access_cd ";
	$SQL .= ", :tournament_id ";
	$SQL .= ", :is_enabled ";
	$SQL .= ", CURRENT_TIMESTAMP ";
	$SQL .= ", :created_by ";
	$SQL .= ", CURRENT_TIMESTAMP ";
	$SQL .= ", :updated_by ";
	$SQL .= ") ";

	$SQL_Parameters = array(
		"team_name"      => $team_name,
		"team_manager"   => $manager_name,
		"team_contact"   => $contact,
		"team_access_cd" => $team_access_cd,
		"tournament_id"  => $tournament_id,
		"is_enabled"     => 1,
		"created_by"     => basename( __FILE__ ),
		"updated_by"     => basename( __FILE__ )
	);

	try {
		global $g_DB;
		$stmt = $g_DB->execute( $SQL, $SQL_Parameters );

		if( $stmt === false ){
			throw new PDOException( "SQLの実行に失敗しました。" );
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'RES_ERR_MSG' ] = 'チーム情報の登録処理でエラーが発生しました';
		$g_Log->error( $_SESSION[ 'RES_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# 終了処理
# ==========================================================
function endProc() {

	global $g_Log;
	$g_Log->debug( "終了処理", __FUNCTION__, basename( __FILE__ ) );

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
	$g_Log->debug( "データベース切断処理", __FUNCTION__, basename( __FILE__ ) );

	global $g_SQLite, $g_DB1_DbType, $g_DB1_HostName;
	
	if ( $g_SQLite !== null ) {
		$g_SQLite = null;
		$g_Log->debug( "データベースから切断しました： $g_DB1_DbType:$g_DB1_HostName", __FUNCTION__, basename( __FILE__ ) );
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
	// 大会参加申し込みの読み込み
	$filename = __DIR__ . DIRECTORY_SEPARATOR . "article_team-entry.php";
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
