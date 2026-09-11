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
		'FROM_VIEW_STATE'       => 0,		// 表示ステータス(0: 初期画面)
		'FORM_ERR_MSG'          => '',		// エラーメッセージ
		'FORM_BUTTON_SEND'      => 'SEND',	// 送信ボタン
		'FROM_USER_ID'          => '',		// ユーザーID
		'FORM_PASSWORD'         => '',		// パスワード
		'FORM_NEW_PASSWORD'     => '',		// 新しいパスワード
		'FORM_CONFIRM_PASSWORD' => '',	// 新しいパスワード（確認）
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

	return true;
}

# ==========================================================
# チェック処理
# ==========================================================
function checkProc() {

	global $g_Log;
	$g_Log->notice( "チェック処理", __FUNCTION__, basename( __FILE__ ) );

	// Formのチェック
	if( ! checkForm() ) {
		return false;
	}

	return true;
}

# ==========================================================
# チェック処理
# ==========================================================
function checkForm() {

	global $g_Log;
	$g_Log->notice( "チェック処理", __FUNCTION__, basename( __FILE__ ) );

	if( ! checkRequest( 'view_state', false ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '参加する大会を選択して下さい。';
		$g_Log->warning( $_SESSION[ 'FORM_ERR_MSG' ], __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 大会IDのチェック
	if( ! checkRequest( 'user_password' ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '現在のパスワードを入力して下さい。';
		$g_Log->warning( $_SESSION[ 'FORM_ERR_MSG' ], __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	if( ! checkText( $_POST[ 'user_password' ] ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '現在のパスワードの入力が不正です。';
		$g_Log->warning( $_SESSION[ 'FORM_ERR_MSG' ], __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 新しいパスワードのチェック
	if( ! checkRequest( 'new_password' ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '新しいパスワードを入力して下さい。';
		$g_Log->warning( $_SESSION[ 'FORM_ERR_MSG' ], __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	if( ! checkText( $_POST[ 'new_password' ] ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '新しいパスワードの入力が不正です。';
		$g_Log->warning( $_SESSION[ 'FORM_ERR_MSG' ], __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 新しいパスワード（確認）のチェック
	if( ! checkRequest( 'confirm_password' ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '新しいパスワード（確認）を入力して下さい。';
		$g_Log->warning( $_SESSION[ 'FORM_ERR_MSG' ], __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	if( ! checkText( $_POST[ 'confirm_password' ] ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '新しいパスワード（確認）の入力が不正です。';
		$g_Log->warning( $_SESSION[ 'FORM_ERR_MSG' ], __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 新しいパスワードと新しいパスワード（確認）が一致するかのチェック
	if( $_POST[ 'new_password' ] !== $_POST[ 'confirm_password' ] ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '新しいパスワードと新しいパスワード（確認）が一致しません。';
		$g_Log->warning( $_SESSION[ 'FORM_ERR_MSG' ], __FUNCTION__, basename( __FILE__ ) );
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
	$_SESSION[ 'FORM_PASSWORD' ] = trim( $_POST[ 'user_password' ] );
	$_SESSION[ 'FORM_NEW_PASSWORD' ] = trim( $_POST[ 'new_password' ] );
	$_SESSION[ 'FORM_CONFIRM_PASSWORD' ] = trim( $_POST[ 'confirm_password' ] );

	// チーム情報登録
	if( ! setPassword() ) {
		return false;
	}

	// 登録完了後はセッション変数をクリア
	unset( $_SESSION[ 'FORM_PASSWORD' ] );
	unset( $_SESSION[ 'FORM_NEW_PASSWORD' ] );
	unset( $_SESSION[ 'FORM_CONFIRM_PASSWORD' ] );

	
	$g_Log->notice( "セッション情報をクリアします。", __FUNCTION__, basename( __FILE__ ) );
	$_SESSION = array();

	$g_Log->notice( "セッション情報を破棄します。", __FUNCTION__, basename( __FILE__ ) );
	session_destroy();

	$g_Log->notice( "パスワードが変更されたためログインページへリダイレクト", __FUNCTION__, basename( __FILE__ ) );
	header( "Location: ./signin" );
	exit();

	return true;
}

# ==========================================================
# パスワード変更処理
# ==========================================================
function setPassword() {

	global $g_Log;
	$g_Log->notice( "パスワード変更処理", __FUNCTION__, basename( __FILE__ ) );

	// セッション変数から値を取得
	$user_id  = $_SESSION[ 'AUTH_USER_ID' ];
	$user_password      = $_SESSION[ 'FORM_PASSWORD' ];
	$new_password       = $_SESSION[ 'FORM_NEW_PASSWORD' ];
	$confirm_password   = $_SESSION[ 'FORM_CONFIRM_PASSWORD' ];

	// パスワードの検証
	if( ! password_verify( $user_password, $_SESSION[ 'AUTH_PWD_HASH' ] ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '現在のパスワードが正しくありません。';
		$g_Log->warning( $_SESSION[ 'FORM_ERR_MSG' ], __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// SQL文作成
	$SQL = "";
	$SQL .= " UPDATE baseball_user ";
	$SQL .= " SET password = :new_password ";
	$SQL .= "   , is_enabled = 1";
	$SQL .= "   , updated_at = CURRENT_TIMESTAMP ";
	$SQL .= "   , updated_by = :updated_by ";
	$SQL .= " WHERE user_id = :user_id ";

	$SQL_Parameters = array(
		"user_id" => $user_id,
		"new_password" => password_hash( $new_password, PASSWORD_DEFAULT ),
		"updated_by" => basename( __FILE__ )
	);


	try {
		global $g_DB;
		$stmt = $g_DB->execute( $SQL, $SQL_Parameters );

		if( $stmt === false ){
			throw new PDOException( "SQLの実行に失敗しました。" );
		}

		if( $stmt === 0 ){
			$_SESSION[ 'FORM_ERR_MSG' ] = '現在のパスワードが正しくありません。';
			$g_Log->warning( $_SESSION[ 'FORM_ERR_MSG' ], __FUNCTION__, basename( __FILE__ ) );
			return false;
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = 'パスワード変更処理でエラーが発生しました';
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
	// パスワード変更の読み込み
	$filename = __DIR__ . DIRECTORY_SEPARATOR . "article_passwordchange.php";
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
