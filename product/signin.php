<?php
# **********************************************************
# サインアップページ
# **********************************************************
# ==========================================================
# 必要なファイル読み込み
# ==========================================================
require_once( __DIR__ . DIRECTORY_SEPARATOR . 'baseball_function.php' );

# ==========================================================
# 変数の定義
# ==========================================================

# ==========================================================
# 主処理
# ==========================================================
$g_Log->debug( "S : " . basename( __FILE__ ), __FUNCTION__, basename( __FILE__ ) );

foreach( $_POST as $key => $value ){
	$g_Log->debug( "POST : {$key} = {$value}", __FUNCTION__, basename( __FILE__ ) );
}

foreach( $_SESSION as $key => $value ){
	$g_Log->debug( "SESSION : {$key} = {$value}", __FUNCTION__, basename( __FILE__ ) );
}

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
	$g_Log->debug( "S : 初期処理", __FUNCTION__, basename( __FILE__ ) );
	
	// サインイン済みの場合はホームへリダイレクト
	if( isAuthenticated() ) {
		$g_Log->notice( "サインイン済みのためホームへリダイレクトします。", __FUNCTION__, basename( __FILE__ ) );
		$g_Log->debug( "E : 初期処理", __FUNCTION__, basename( __FILE__ ) );
		header( "Location: ./" );
		exit();
	}

	// セッション変数の初期化
	$sessionList = [
		'error_msg'   => '',	// エラーメッセージ
		'view_state'  => 0,	// 表示ステータス
		'send_button' => 'サインイン',	// 送信ボタン
	];

	// セッション変数の設定
	foreach ( $sessionList as $name => $value ) {
		$_SESSION[ $name ] = $value;
	}

	$g_Log->debug( "E : 初期処理", __FUNCTION__, basename( __FILE__ ) );

	return true;
}

# ==========================================================
# リクエスト処理
# ==========================================================
function requestProc() {

	global $g_Log;
	$g_Log->debug( "S : リクエスト処理", __FUNCTION__, basename( __FILE__ ) );
	$g_Log->debug( "REQUEST_METHOD : " . $_SERVER[ 'REQUEST_METHOD' ], __FUNCTION__, basename( __FILE__ ) );

	// GETリクエストの場合は処理なし
	if ( $_SERVER[ 'REQUEST_METHOD' ] === 'GET' ) {
		$g_Log->debug( "GETリクエストのため以降の処理なし", __FUNCTION__, basename( __FILE__ ) );
		$g_Log->debug( "E : リクエスト処理", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// GET, POSTリクエスト以外の場合
	if ( $_SERVER[ 'REQUEST_METHOD' ] !== 'POST' ) {
		$g_Log->debug( "GET/POSTリクエスト以外のため以降の処理なし", __FUNCTION__, basename( __FILE__ ) );
		$g_Log->debug( "E : リクエスト処理", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	$g_Log->debug( "E : リクエスト処理", __FUNCTION__, basename( __FILE__ ) );
	return true;
}

# ==========================================================
# チェック処理
# ==========================================================
function checkProc() {

	global $g_Log;
	$g_Log->debug( "S : チェック処理", __FUNCTION__, basename( __FILE__ ) );

	// ユーザーコードのチェック
	if( ! checkUserCd( $_POST[ 'usercd' ] ) ) {
		$g_Log->debug( "E : チェック処理", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// パスワードのチェック
	if( ! checkPassword( $_POST[ 'password' ] ) ) {
		$g_Log->debug( "E : チェック処理", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	if( ! checkAuthenticate( $_POST[ 'usercd' ], $_POST[ 'password' ] ) ) {
		$g_Log->debug( "E : チェック処理", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}
	
	// サインイン済みの場合はホームへリダイレクト
	if( isAuthenticated() ) {
		$g_Log->notice( "サインイン済みのためホームへリダイレクトします。", __FUNCTION__, basename( __FILE__ ) );
		$g_Log->debug( "E : 初期処理", __FUNCTION__, basename( __FILE__ ) );
		header( "Location: ./" );
		exit();
	}

	$g_Log->debug( "E : チェック処理", __FUNCTION__, basename( __FILE__ ) );
	return true;
}


# ==========================================================
# セット処理
# ==========================================================
function setProc() {

	global $g_Log;
	$g_Log->debug( "セット処理", __FUNCTION__, basename( __FILE__ ) );

	return true;
}

# ==========================================================
# 認証有効期限設定処理
# ==========================================================
function setVerificationPeriod() {

	global $g_Log;
	$g_Log->debug( "認証有効期限設定処理 : " . $_SESSION[ 'email' ], __FUNCTION__, basename( __FILE__ ) );

	// 60分後までを有効期限とする
	$period = time() + ( 60 * 60 );

	$g_Log->debug( "認証有効期限を設定しました : " . $period, __FUNCTION__, basename( __FILE__ ) );

	$_SESSION[ 'verificationPeriod' ] = $period;

	return true;
}

# ==========================================================
# メール送信処理
# ==========================================================
function sendMail() {

	global $g_Log;
	$g_Log->debug( "メール送信処理 : " . $_SESSION[ 'email' ], __FUNCTION__, basename( __FILE__ ) );

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
		$g_Log->debug( "認証コードを送信しました : " . $email, __FUNCTION__, basename( __FILE__ ) );
	} else {
		$_SESSION[ 'error_msg' ] = '認証コードの送信に失敗しました。';
		$g_Log->error( $_SESSION[ 'error_msg' ] . " : " . $email, __FUNCTION__, basename( __FILE__ ) );
		return false;
	}
	
	$g_Log->debug( "メールアドレス登録処理が完了しました : " . $_SESSION[ 'email' ], __FUNCTION__, basename( __FILE__ ) );

	return true;
}

# ==========================================================
# パスワード登録処理
# ==========================================================
function setPassword() {

	global $g_Log;
	$g_Log->debug( "パスワード登録処理 : " . $_SESSION[ 'email' ], __FUNCTION__, basename( __FILE__ ) );

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
	$g_Log->debug( "表示ステータス更新処理 : " . $_SESSION[ 'view_state' ] . " -> " . $p_viewState, __FUNCTION__, basename( __FILE__ ) );

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
	<div class="container">
		<h2>サインイン</h2>
		<div class="signin-container">
			<form method="post" action="./signin" novalidate >
				<input type="hidden" name="view_state" value="<?php echo $_SESSION[ 'view_state' ]; ?>">

				<div id="errMsg" class="errMsg"><?php echo $_SESSION[ 'error_msg' ]; ?></div>

				<div class=form-group>
					<label for="usercd">メールアドレス</label>
					<?php $usercd_readonly = ""; ?>
					<?php $usercd_autofocus = "autofocus"; ?>
					<input type="email" id="usercd" name="usercd" value="<?php echo isset( $_SESSION[ 'user_cd' ] ) ? htmlspecialchars( $_SESSION[ 'user_cd' ] ) : ''; ?>" <?php echo $usercd_readonly; ?> <?php echo $usercd_autofocus; ?> required>
				</div>

				<div class=form-group>
					<?php $password_autofocus = ""; ?>
					<label for="password">パスワード</label>
					<input type="password" id="password" name="password" <?php echo $password_autofocus; ?> required>
				</div>

				<div class=form-group>
					<label></label>
					<button type="submit" name="send_button"><?php echo $_SESSION[ 'send_button' ]; ?></button>
				</div>
			</form>
		</div>
	</div>
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
