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

	// 認証チェック
	if( ! isAuthenticated() ) {
		$g_Log->notice( "認証されていないためログインページへリダイレクト", __FUNCTION__, basename( __FILE__ ) );
		header( "Location: ./signin" );
		exit();
	}

		
	// セッション変数の初期化
	$sessionList = [
		'FROM_VIEW_STATE'  => 0,		// 表示ステータス(0: 初期画面)
		'FORM_ERR_MSG'     => '',		// エラーメッセージ
		'RES_SEND_BUTTON' => '更新',	// 送信ボタン
#		'RES_TOKEN'       => '',		// CSRFトークン
		'RES_USERCD'      => '',		// 名前
		'RES_USER_NAME'   => '',		// 電話番号
		'RES_ROLE_LEVEL'  => '',		// メールアドレス
		'RES_LAST_SIGNIN' => '',		// メールアドレス
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

	$_SESSION[ 'RES_USER_CD' ] = $_SESSION[ 'AUTH_USER_CD' ] ?? '';
	$_SESSION[ 'RES_USER_NAME' ] = $_SESSION[ 'AUTH_USER_NAME' ] ?? '';
	$_SESSION[ 'RES_ROLE_LEVEL' ] = $_SESSION[ 'AUTH_ROLE_NAME' ] ?? '';
	$_SESSION[ 'RES_LAST_SIGNIN' ] = $_SESSION[ 'AUTH_SIGNIN_AT' ] ?? '';

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
	
	// 名前の取得
	if( isset( $_POST[ 'user_cd' ] ) ) {
		$g_Log->notice( "POST : user_cd = " . $_POST[ 'user_cd' ], __FUNCTION__, basename( __FILE__ ) );
	}
	
	// ユーザー名の取得
	if( isset( $_POST[ 'user_name' ] ) ) {
		$g_Log->notice( "POST : user_name = " . $_POST[ 'user_name' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// ユーザ権限の取得
	if( isset( $_POST[ 'role_level' ] ) ) {
		$g_Log->notice( "POST : role_level = " . $_POST[ 'role_level' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// 最終ログインの取得
	if( isset( $_POST[ 'last_signin' ] ) ) {
		$g_Log->notice( "POST : last_signin = " . $_POST[ 'last_signin' ], __FUNCTION__, basename( __FILE__ ) );
	}

	return true;
}

# ==========================================================
# チェック処理
# ==========================================================
function checkProc() {

	global $g_Log;
	$g_Log->notice( "チェック処理", __FUNCTION__, basename( __FILE__ ) );

	/* 4) 通常の入力チェック */
	$userCd    = trim( ( string )( $_POST[ 'user_cd' ] ?? '' ) );
	$userName   = trim( ( string )( $_POST[ 'user_name' ] ?? '' ) );
	$roleLevel   = trim( ( string )( $_POST[ 'role_level' ] ?? '' ) );
	$lastSignin = trim( ( string )( $_POST[ 'last_signin' ] ?? '' ) );

	if ( $userName == '' ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = 'お名前を入力して下さい。';
		$g_Log->warning( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . $userCd, __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# セット処理
# ==========================================================
function setProc() {

	global $g_Log;
	$g_Log->notice( "セット処理", __FUNCTION__, basename( __FILE__ ) );

	$_SESSION[ 'FROM_VIEW_STATE' ] = trim( $_POST[ 'view_state' ] );
	$_SESSION[ 'RES_USER_NAME' ] = trim( $_POST[ 'user_name' ] );

	// 問合せ内容登録
	if( ! setUserProfile() ) {
		return false;
	}

	return true;
}

# ==========================================================
# 問合せ内容登録処理
# ==========================================================
function setUserProfile() {

	global $g_Log;
	$g_Log->notice( "ユーザ情報更新処理", __FUNCTION__, basename( __FILE__ ) );

	$userId	  = $_SESSION[ 'AUTH_USER_ID' ];
	$userName = $_SESSION[ 'RES_USER_NAME' ];

	// SQL文作成
	$SQL = "";
	$SQL .= " UPDATE baseball_user ";
	$SQL .= " SET username = :username ";
	$SQL .= " , updated_at = CURRENT_TIMESTAMP ";
	$SQL .= " , updated_by = :updated_by ";
	$SQL .= " WHERE user_id = :user_id ";

	$SQL_Parameters = array(
		"user_id"    => $userId,
		"username"   => $userName,
		"updated_by" => basename( __FILE__ ),
	);

	try {
		global $g_DB;
		$stmt = $g_DB->execute( $SQL, $SQL_Parameters );

		if( $stmt === false ){
			throw new PDOException( "SQLの実行に失敗しました。" );
		}

		$_SESSION[ 'AUTH_USER_NAME' ] = $userName;

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = 'ユーザ情報の更新処理でエラーが発生しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

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
	// プロフィールの読み込み
	$filename = __DIR__ . DIRECTORY_SEPARATOR . "article_profile.php";
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
