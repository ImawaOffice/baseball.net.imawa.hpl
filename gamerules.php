<?php
# **********************************************************
# トップページ
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
		$_SESSION[ 'view_state' ] = $_POST[ 'view_state' ];
		$g_Log->notice( "SESSION : view_state = " . $_POST[ 'view_state' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// 戻るボタンの取得
	if( isset( $_POST[ 'backto' ] ) ) {
		$_SESSION[ 'backto' ] = $_POST[ 'backto' ];
		$g_Log->notice( "SESSION : backto = " . $_POST[ 'backto' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// メールアドレスの取得
	if( isset( $_POST[ 'email' ] ) ) {
		$_SESSION[ 'email' ] = trim( $_POST[ 'email' ] );
		$g_Log->notice( "SESSION : email = " . $_POST[ 'email' ], __FUNCTION__, basename( __FILE__ ) );
	}
	
	// 認証コードの取得
	if( isset( $_POST[ 'code' ] ) ) {
		$_SESSION[ 'code' ] = trim( $_POST[ 'code' ] );
		$g_Log->notice( "SESSION : code = " . $_POST[ 'code' ], __FUNCTION__, basename( __FILE__ ) );
	}
	
	// パスワードの取得
	if( isset( $_POST[ 'password' ] ) ) {
		$_SESSION[ 'password' ] = trim( $_POST[ 'password' ] );
		$g_Log->notice( "SESSION : password = " . $_POST[ 'password' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// 前のステップに戻る
	$_SESSION[ 'view_state' ] = $_SESSION[ 'view_state' ] - $_SESSION[ 'backto' ];
	$g_Log->notice( "SESSION : view_state = " . $_SESSION[ 'view_state' ], __FUNCTION__, basename( __FILE__ ) );

	return true;
}

# ==========================================================
# チェック処理
# ==========================================================
function checkProc() {

	global $g_Log;
	$g_Log->notice( "チェック処理", __FUNCTION__, basename( __FILE__ ) );

	// 表示ステータスの範囲チェック
	if( ! checkViewState() ) {
		return false;
	}

	// メールアドレス入力
	if( $_SESSION[ 'view_state' ] == 0 ) {
		// メールアドレスのチェック
		if( ! checkMailAddress() ) {
			return false;
		}
		return true;
	}

	// 認証コード入力
	if( $_SESSION[ 'view_state' ] == 1 ) {
		// メールアドレスのチェック
		if( ! checkVerificationCode() ) {
			$_SESSION[ 'error_msg' ] = '認証コードが正しくありません。';
			$g_Log->warning( $_SESSION[ 'error_msg' ], __FUNCTION__, basename( __FILE__ ) );
			return false;
		}
		return true;
	}

	// パスワード入力
	if( $_SESSION[ 'view_state' ] == 2 ) {
		// パスワードのチェック
		if( ! checkPassword() ) {
			$_SESSION[ 'error_msg' ] = 'パスワードが正しくありません。';
		$g_Log->warning( $_SESSION[ 'error_msg' ], __FUNCTION__, basename( __FILE__ ) );
			return false;
		}
		return true;
	}

	return true;
}

# ==========================================================
# 表示ステータスチェック処理
# ==========================================================
function checkViewState() {

	global $g_Log;
	$g_Log->notice( "表示ステータスチェック処理 : " . $_SESSION[ 'view_state' ], __FUNCTION__, basename( __FILE__ ) );

	if( ! is_numeric( $_SESSION[ 'view_state' ] ) ) {
		$_SESSION[ 'view_state' ] = 0;
	}

	if( $_SESSION[ 'view_state' ] < 0 ) {
		$_SESSION[ 'view_state' ] = 0;
	}

	if( $_SESSION[ 'view_state' ] > 2 ) {
		$_SESSION[ 'view_state' ] = 2;
	}

	return true;
}

# ==========================================================
# メールアドレスチェック処理
# ==========================================================
function checkMailAddress() {

	global $g_Log;
	$g_Log->notice( "メールアドレスチェック処理 : " . $_SESSION[ 'email' ], __FUNCTION__, basename( __FILE__ ) );

	$email = $_SESSION[ 'email' ];

	// NULLチェック
	if ( is_null( $email ) ) {
		$_SESSION[ 'error_msg' ] = 'メールアドレスを入力して下さい。';
		$g_Log->warning( "IS NULL : " . $_SESSION[ 'error_msg' ] . " : " . $_SESSION[ 'email' ], __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 空文字チェック
	if( empty( $email ) ) {
		$_SESSION[ 'error_msg' ] = 'メールアドレスを入力して下さい。';
		$g_Log->warning( "IS EMPTY : " . $_SESSION[ 'error_msg' ] . " : " . $_SESSION[ 'email' ], __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// フォーマットチェック
	if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
		$_SESSION[ 'error_msg' ] = 'メールアドレスの形式が正しくありません';
		$g_Log->warning( "INVALID FORMAT : " . $_SESSION[ 'error_msg' ] . " : " . $_SESSION[ 'email' ], __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# 認証コードチェック処理
# ==========================================================
function checkVerificationCode(){

	global $g_Log;
	$g_Log->notice( "認証コードチェック処理 : " . $_SESSION[ 'code' ], __FUNCTION__, basename( __FILE__ ) );

	$email = $_SESSION[ 'email' ];
	$inputCode = $_SESSION[ 'code' ];
	$period = time() - ( 60 * 60 );

	// SQL文作成
	$SQL = "";
	$SQL .= "SELECT ";
	$SQL .= "  verification_cd ";
	$SQL .= "  FROM baseball_user ";
	$SQL .= " WHERE email = :email ";
	$SQL .= "   AND verification_period >= :period ";
	
	$SQL_Parameters = array(
		"email" => $email,
		"period" => $period
	);
	try {

		global $g_DB;
		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );

		if( count( $dataTable ) == 0 ){
			$_SESSION[ 'error_msg' ] = '登録されていないメールアドレスです。';
			$g_Log->warning( $_SESSION[ 'error_msg' ] . " : " . $email, __FUNCTION__, basename( __FILE__ ) );
			return false;
		}

		$verification_cd = $dataTable[ 0 ][ 'verification_cd' ];

		if( $inputCode !== $verification_cd ) {
			$_SESSION[ 'error_msg' ] = '認証コードが正しくありません。';
			$g_Log->warning( $_SESSION[ 'error_msg' ] . " : InputCode=" . $inputCode . ", StoredCode=" . $verification_cd, __FUNCTION__, basename( __FILE__ ) );
			return false;
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'error_msg' ] = '認証コードチェック処理でエラーが発生しました。';
		$g_Log->error( $_SESSION[ 'error_msg' ] . "\n" . $e->getMessage(), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# パスワードチェック処理
# ==========================================================
function checkPassword() {

	global $g_Log;
	$g_Log->notice( "パスワードチェック処理 : " . $_SESSION[ 'password' ], __FUNCTION__, basename( __FILE__ ) );

	$password = $_SESSION[ 'password' ];

	// NULLチェック
	if ( is_null( $password ) ) {
		$_SESSION[ 'error_msg' ] = 'パスワードを入力して下さい。';
		$g_Log->warning( "IS NULL : " . $_SESSION[ 'error_msg' ] . " : " . $_SESSION[ 'password' ], __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 空文字チェック
	if( empty( $password ) ) {
		$_SESSION[ 'error_msg' ] = 'パスワードを入力して下さい。';
		$g_Log->warning( "IS EMPTY : " . $_SESSION[ 'error_msg' ] . " : " . $_SESSION[ 'password' ], __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 長さチェック
	if ( strlen( $password ) < 6 ) {
		$_SESSION[ 'error_msg' ] = 'パスワードは6文字以上で入力して下さい。';
		$g_Log->warning( "INVALID LENGTH : " . $_SESSION[ 'error_msg' ] . " : " . $_SESSION[ 'password' ], __FUNCTION__, basename( __FILE__ ) );
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

	if( $_SESSION[ "backto" ] > 0 ) {
		$g_Log->notice( "前のステップに戻るため以降の処理なし", __FUNCTION__, basename( __FILE__ ) );
		return true;
	}

	// メール送信
	if( $_SESSION[ 'view_state' ] == 0 ) {
		// 認証コード作成処理
		if( ! setVerificationCode() ) {
			return false;
		}

		// 認証有効期限設定処理
		if( ! setVerificationPeriod() ) {
			return false;
		}

		// メールアドレス登録処理
		if( ! setMailAddress() ) {
			return false;
		}
		
		// メール送信処理
		if( ! sendMail() ) {
			return false;
		}
		
		// 表示ステータス更新処理
		if( ! setViewState( 1 ) ) {
			return false;
		}
		return true;
	}

	// 認証コードチェック
	if( $_SESSION[ 'view_state' ] == 1 ) {
		
		// 表示ステータス更新処理
		if( ! setViewState( 2 ) ) {
			return false;
		}
		return true;
	}

	// パスワードチェック
	if( $_SESSION[ 'view_state' ] == 2 ) {

		// パスワード登録処理
		if( ! setPassword() ) {
			return false;
		}
		
		// 表示ステータス更新処理
		if( ! setViewState( 0 ) ) {
			return false;
		}
		return true;
	}

	return true;
}

# ==========================================================
# 認証コード作成処理
# ==========================================================
function setVerificationCode() {

	global $g_Log;
	$g_Log->notice( "認証コード作成処理 : " . $_SESSION[ 'email' ], __FUNCTION__, basename( __FILE__ ) );

	// 6桁までの数字をランダム生成
	$randomNumber = random_int( 0, 999999 );

	// 6桁の認証コード生成
	$randomCode = str_pad( strval( $randomNumber ), 6, '0', STR_PAD_LEFT );

	$g_Log->notice( "認証コードを作成しました : " . $randomCode, __FUNCTION__, basename( __FILE__ ) );

	$_SESSION[ 'VerificationCode' ] = $randomCode;

	return true;
}

# ==========================================================
# 認証有効期限設定処理
# ==========================================================
function setVerificationPeriod() {

	global $g_Log;
	$g_Log->notice( "認証有効期限設定処理 : " . $_SESSION[ 'email' ], __FUNCTION__, basename( __FILE__ ) );

	// 60分後までを有効期限とする
	$period = time() + ( 60 * 60 );

	$g_Log->notice( "認証有効期限を設定しました : " . $period, __FUNCTION__, basename( __FILE__ ) );

	$_SESSION[ 'verificationPeriod' ] = $period;

	return true;
}

# ==========================================================
# メールアドレス登録処理
# ==========================================================
function setMailAddress() {

	global $g_Log;
	$g_Log->notice( "メールアドレス登録処理 : " . $_SESSION[ 'email' ], __FUNCTION__, basename( __FILE__ ) );

	$email      = $_SESSION[ 'email' ];
	$randomCode = $_SESSION[ 'VerificationCode' ];
	$period     = $_SESSION[ 'verificationPeriod' ];

	// SQL文作成
	$SQL = "";
	$SQL .= "INSERT INTO baseball_user ";
	$SQL .= "( user_cd ";
	$SQL .= ", password ";
	$SQL .= ", username ";
	$SQL .= ", email ";
	$SQL .= ", role_id ";
	$SQL .= ", verification_cd ";
	$SQL .= ", verification_period ";
	$SQL .= ", is_enabled ";
	$SQL .= ", created_at ";
	$SQL .= ", created_by ";
	$SQL .= ", updated_at ";
	$SQL .= ", updated_by ";
	$SQL .= ") ";
	$SQL .= "VALUES ";
	$SQL .= "( :user_cd ";
	$SQL .= ", :password ";
	$SQL .= ", :username ";
	$SQL .= ", :email ";
	$SQL .= ", :role_id ";
	$SQL .= ", :verification_cd ";
	$SQL .= ", :verification_period ";
	$SQL .= ", :is_enabled ";
	$SQL .= ", CURRENT_TIMESTAMP ";
	$SQL .= ", :created_by ";
	$SQL .= ", CURRENT_TIMESTAMP ";
	$SQL .= ", :updated_by ";
	$SQL .= ") ";
	$SQL .= "ON DUPLICATE KEY UPDATE ";
	$SQL .= "  verification_cd     = :verification_cd ";
	$SQL .= ", verification_period = :verification_period ";
	$SQL .= ", is_enabled          = :is_enabled ";
	$SQL .= ", updated_at          = CURRENT_TIMESTAMP ";
	$SQL .= ", updated_by          = :updated_by ";

	$SQL_Parameters = array(
		"user_cd" => $email,
		"password" => "",
		"username" => "",
		"email" => $email,
		"role_id" => 0,
		"verification_cd" => $randomCode,
		"verification_period" => $period,
		"is_enabled" => 0,
		"created_by" => basename( __FILE__ ),
		"updated_by" => basename( __FILE__ )
	);

	try {
		global $g_DB;
		$stmt = $g_DB->execute( $SQL, $SQL_Parameters );

		if( $stmt === false ){
			throw new PDOException( "SQLの実行に失敗しました。" );
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'error_msg' ] = 'メールアドレス登録処理でエラーが発生しました: ' . htmlspecialchars( $e->getMessage() );
		$g_Log->error( $_SESSION[ 'error_msg' ], __FUNCTION__, basename( __FILE__ ) );
		return false;
	}
	
	$_SESSION[ "error_msg" ] = "登録されたメールアドレスに認証コードを送付しました。";
	$g_Log->notice( "メールアドレス登録処理が完了しました : " . $_SESSION[ 'email' ], __FUNCTION__, basename( __FILE__ ) );

	return true;
}

# ==========================================================
# メール送信処理
# ==========================================================
function sendMail() {

	global $g_Log;
	$g_Log->notice( "メール送信処理 : " . $_SESSION[ 'email' ], __FUNCTION__, basename( __FILE__ ) );

	$email      = $_SESSION[ 'email' ];
	$randomCode = $_SESSION[ 'VerificationCode' ];
	$period     = $_SESSION[ 'verificationPeriod' ];

	// メール送信処理
	$mailer = new Class_PHPMailer();
	$subject = '【HPL】メールアドレス認証コードのお知らせ';
	$message = "";
	$message .= "<p>いつもHPLをご利用いただき、誠にありがとうございます。</p>";
	$message .= "<p>以下の認証コードを、サインアップ画面に入力してください。</p>";
	$message .= "<br>";
	$message .= "<p>認証コード: $randomCode</p>";
	$message .= "<br>";
	$message .= "<p>なお、セキュリティのため60分以内に入力してください。</p>";

	if ( $mailer->sendMail( $email, $subject, $message ) ) {
		$g_Log->notice( "認証コードを送信しました : " . $email, __FUNCTION__, basename( __FILE__ ) );
	} else {
		$_SESSION[ 'error_msg' ] = '認証コードの送信に失敗しました。';
		$g_Log->error( $_SESSION[ 'error_msg' ] . " : " . $email, __FUNCTION__, basename( __FILE__ ) );
		return false;
	}
	
	$g_Log->notice( "メールアドレス登録処理が完了しました : " . $_SESSION[ 'email' ], __FUNCTION__, basename( __FILE__ ) );

	return true;
}

# ==========================================================
# パスワード登録処理
# ==========================================================
function setPassword() {

	global $g_Log;
	$g_Log->notice( "パスワード登録処理 : " . $_SESSION[ 'email' ], __FUNCTION__, basename( __FILE__ ) );

	$email    = $_SESSION[ 'email' ];
	$password = $_SESSION[ 'password' ];

	// SQL文作成
	$SQL = "";
	$SQL .= "UPDATE baseball_user ";
	$SQL .= "SET password = :password ";
	$SQL .= "  , is_enabled = 1";
	$SQL .= "  , updated_at = CURRENT_TIMESTAMP ";
	$SQL .= "  , updated_by = :updated_by ";
	$SQL .= "WHERE user_cd = :user_cd ";

	$SQL_Parameters = array(
		"user_cd" => $email,
		"password" => password_hash( $password, PASSWORD_DEFAULT ),
		"updated_by" => basename( __FILE__ )
	);

	try {
		global $g_DB;
		$stmt = $g_DB->execute( $SQL, $SQL_Parameters );

		if( $stmt === false ){
			throw new PDOException( "SQLの実行に失敗しました。" );
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'error_msg' ] = 'パスワードの登録処理でエラーが発生しました: ' . htmlspecialchars( $e->getMessage() );
		$g_Log->error( $_SESSION[ 'error_msg' ], __FUNCTION__, basename( __FILE__ ) );
		return false;
	}
	
	return true;
}

# ==========================================================
# 表示ステータス更新処理
# ==========================================================
function setViewState( $p_viewState = 0) {

	global $g_Log;
	$g_Log->notice( "表示ステータス更新処理 : " . $_SESSION[ 'view_state' ] . " -> " . $p_viewState, __FUNCTION__, basename( __FILE__ ) );

	$_SESSION[ 'view_state' ] = $p_viewState;

	switch( $_SESSION[ 'view_state' ] ) {
		case '0':
			$_SESSION[ 'view_style' ] = array( "background-color:yellow;", "background-color:transparent;", "background-color:transparent;" );
			$_SESSION[ 'send_button' ] = "認証コード送信";
			break;
		case '1':
			$_SESSION[ 'view_style' ] = array( "background-color:gray;", "background-color:yellow;", "background-color:transparent;" );
			$_SESSION[ 'send_button' ] = "認証";
			break;
		case '2':
			$_SESSION[ 'view_style' ] = array( "background-color:gray;", "background-color:gray;", "background-color:yellow;" );
			$_SESSION[ 'send_button' ] = "パスワード変更";
			break;
		default:
			$_SESSION[ 'view_style' ] = array( "background-color:transparent;", "background-color:transparent;", "background-color:transparent;" );
			break;
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
	// 試合規定の読み込み
	$filename = __DIR__ . DIRECTORY_SEPARATOR . "article_game-rules.php";
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
