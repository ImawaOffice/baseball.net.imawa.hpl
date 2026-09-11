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
# 主処理
# ==========================================================

# ==========================================================
# 初期設定
# ==========================================================
function initProc() {

	global $g_Log;
	$g_Log->debug( "初期処理", __FUNCTION__, basename( __FILE__ ) );

		
	// セッション変数の初期化
	$sessionList = [
		'RES_VIEW_STATE'     => 0,		// 表示ステータス(0: 初期画面)
		'RES_ERR_MSG'       => '',		// エラーメッセージ
		'RES_SEND_BUTTON'   => '送信',	// 送信ボタン
#		'RES_TOKEN'          => '',		// CSRFトークン
		'RES_NAME'          => '',		// 名前
		'RES_PHONE'         => '',		// 電話番号
		'RES_EMAIL'         => '',		// メールアドレス
		'RES_INQUIRY'       => '',		// 問合せ内容
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
	
	// 名前の取得
	if( isset( $_POST[ 'name' ] ) ) {
		$g_Log->debug( "POST : name = " . $_POST[ 'name' ], __FUNCTION__, basename( __FILE__ ) );
	}
	
	// 電話番号の取得
	if( isset( $_POST[ 'phone' ] ) ) {
		$g_Log->debug( "POST : phone = " . $_POST[ 'phone' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// メールアドレスの取得
	if( isset( $_POST[ 'email' ] ) ) {
		$g_Log->debug( "POST : email = " . $_POST[ 'email' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// 問合せ内容の取得
	if( isset( $_POST[ 'inquiry' ] ) ) {
		$g_Log->debug( "POST : inquiry = " . $_POST[ 'inquiry' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// 同意の取得
	if( isset( $_POST[ 'agree' ] ) ) {
		$g_Log->debug( "POST : agree = " . $_POST[ 'agree' ], __FUNCTION__, basename( __FILE__ ) );
	}

	return true;
}

# ==========================================================
# チェック処理
# ==========================================================
function checkProc() {

	global $g_Log;
	$g_Log->debug( "チェック処理", __FUNCTION__, basename( __FILE__ ) );

	/* 1) レート制限（最初に） */
	if( ! rateLimitOrFail( basename( __FILE__ ) ) ) {
		return false;
	}

	/* 2) ハニーポット */
	if( ! verifyHoneypotOrFail( $_POST[ 'agree' ] ?? '' ) ) {
		return false;
	}

	/* 3) CSRF */
	if( ! verifyCsrfOrFail( $_POST[ 'token' ] ?? '', $_SESSION[ 'RES_TOKEN' ] ?? '' ) ) {
		return false;
	}

	/* 4) 通常の入力チェック */
	$name    = trim( ( string )( $_POST[ 'name' ] ?? '' ) );
	$phone   = trim( ( string )( $_POST[ 'phone' ] ?? '' ) );
	$email   = trim( ( string )( $_POST[ 'email' ] ?? '' ) );
	$inquiry = trim( ( string )( $_POST[ 'inquiry' ] ?? '' ) );

	if ( $name == '' ) {
		$_SESSION[ 'RES_ERR_MSG' ] = 'お名前を入力して下さい。';
		$g_Log->warning( $_SESSION[ 'RES_ERR_MSG' ] . " : " . $name, __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// フォーマットチェック
	if( $email != '' ){
		if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			$_SESSION[ 'RES_ERR_MSG' ] = 'メールアドレスの形式が正しくありません';
			$g_Log->warning( "INVALID FORMAT : " . $_SESSION[ 'RES_ERR_MSG' ] . " : " . $_SESSION[ 'email' ], __FUNCTION__, basename( __FILE__ ) );
			return false;
		}
	}

	if ( $inquiry == '' ) {
		$_SESSION[ 'RES_ERR_MSG' ] = 'お問い合わせ内容を入力して下さい。';
		$g_Log->warning( $_SESSION[ 'RES_ERR_MSG' ] . " : " . $inquiry, __FUNCTION__, basename( __FILE__ ) );
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
	$_SESSION[ 'RES_NAME' ] = trim( $_POST[ 'name' ] );
	$_SESSION[ 'RES_PHONE' ] = trim( $_POST[ 'phone' ] );
	$_SESSION[ 'RES_EMAIL' ] = trim( $_POST[ 'email' ] );
	$_SESSION[ 'RES_INQUIRY' ] = trim( $_POST[ 'inquiry' ] );

	// 問合せ内容登録
	if( ! setInquiry() ) {
		return false;
	}
		
	// メール送信処理
	if( ! sendMail() ) {
		return false;
	}

	return true;
}

# ==========================================================
# 問合せ内容登録処理
# ==========================================================
function setInquiry() {

	global $g_Log;
	$g_Log->debug( "問合せ内容登録処理", __FUNCTION__, basename( __FILE__ ) );

	$name    = $_SESSION[ 'RES_NAME' ];
	$phone   = $_SESSION[ 'RES_PHONE' ];
	$email   = $_SESSION[ 'RES_EMAIL' ];
	$inquiry = $_SESSION[ 'RES_INQUIRY' ];

	// SQL文作成
	$SQL = "";
	$SQL .= "INSERT INTO baseball_inquiry ";
	$SQL .= "( inquiry_at ";
	$SQL .= ", name ";
	$SQL .= ", phone ";
	$SQL .= ", email ";
	$SQL .= ", message ";
	$SQL .= ", is_enabled ";
	$SQL .= ", created_at ";
	$SQL .= ", created_by ";
	$SQL .= ", updated_at ";
	$SQL .= ", updated_by ";
	$SQL .= ") ";
	$SQL .= "VALUES ";
	$SQL .= "( CURRENT_TIMESTAMP ";
	$SQL .= ", :name ";
	$SQL .= ", :phone ";
	$SQL .= ", :email ";
	$SQL .= ", :message ";
	$SQL .= ", :is_enabled ";
	$SQL .= ", CURRENT_TIMESTAMP ";
	$SQL .= ", :created_by ";
	$SQL .= ", CURRENT_TIMESTAMP ";
	$SQL .= ", :updated_by ";
	$SQL .= ") ";

	$SQL_Parameters = array(
		"name"       => $name,
		"phone"      => $phone,
		"email"      => $email,
		"message"    => $inquiry,
		"is_enabled" => 1,
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
		$_SESSION[ 'RES_ERR_MSG' ] = '問合せ内容の登録処理でエラーが発生しました';
		$g_Log->error( $_SESSION[ 'RES_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# メール送信処理
# ==========================================================
function sendMail() {

	global $g_Log;
	$g_Log->debug( "メール送信処理", __FUNCTION__, basename( __FILE__ ) );
	$name     = $_SESSION[ 'RES_NAME' ];
	$email    = $_SESSION[ 'RES_EMAIL' ];
	$phone    = $_SESSION[ 'RES_PHONE' ];
	$inquiry  = $_SESSION[ 'RES_INQUIRY' ];
	$postedAt = date( 'Y-m-d H:i:s' );

	// メールアドレス取得
	// SQL文作成
	$SQL = "";
	$SQL .= "SELECT ";
	$SQL .= "  email ";
	$SQL .= ", role_level ";
	$SQL .= "  FROM baseball_user ";
	$SQL .= " WHERE is_enabled = 1 ";
	$SQL .= "   AND role_level IN ( 100, 1000 ) ";
	
	$SQL_Parameters = array();
	
	$toArray = array();
	$bccArray = array();

	try {

		global $g_DB;
		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );

		if( count( $dataTable ) == 0 ){
			$_SESSION[ 'RES_ERR_MSG' ] = 'メールアドレスが登録されていません。';
			$g_Log->warning( $_SESSION[ 'RES_ERR_MSG' ] . " : " . $email, __FUNCTION__, basename( __FILE__ ) );
			return false;
		}

		foreach ( $dataTable as $row ) {
			switch( $row[ 'role_level' ] ) {
				case 100:
					$toArray[] = $row[ 'email' ];
					break;
				case 1000:
					$bccArray[] = $row[ 'email' ];
					break;
			}
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'error_msg' ] = '認証コードチェック処理でエラーが発生しました。';
		$g_Log->error( $_SESSION[ 'error_msg' ] . "\n" . $e->getMessage(), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	$toAddress = implode( ',', $toArray );
	$ccAddress = '';
	$bccAddress = implode( ',', $bccArray );

	// メール送信処理
	$mailer = new Class_PHPMailer();
	$subject = '【HPL】お問合せに投稿がありました。' . $postedAt;
	$message = "";
	$message .= "<p>投稿日時 : " . $postedAt . "</p>";
	$message .= "<br>";
	$message .= "<p>お名前 : " . $name . "</p>";
	$message .= "<p>電話番号 : " . $phone . "</p>";
	$message .= "<p>メールアドレス : " . $email . "</p>";
	$message .= "<p>お問合せ内容</p>";
	$message .= "<br>";
	$message .= "<p>$inquiry</p>";

	if ( $mailer->sendMail( $toAddress, $subject, $message, $ccAddress, $bccAddress ) ) {
		$g_Log->debug( "問合せ内容を送信しました", __FUNCTION__, basename( __FILE__ ) );
	} else {
		$_SESSION[ 'RES_ERR_MSG' ] = '問合せ内容の送信に失敗しました。';
		$g_Log->error( $_SESSION[ 'RES_ERR_MSG' ] . " : " . $email, __FUNCTION__, basename( __FILE__ ) );
		return false;
	}
	
	$g_Log->debug( "メール送信処理が完了しました : " . $email, __FUNCTION__, basename( __FILE__ ) );

	// 送信完了後はセッション変数をクリア
	unset( $_SESSION[ 'RES_TOKEN' ] );
	unset( $_SESSION[ 'RES_NAME' ] );
	unset( $_SESSION[ 'RES_PHONE' ] );
	unset( $_SESSION[ 'RES_EMAIL' ] );
	unset( $_SESSION[ 'RES_INQUIRY' ] );

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
	// お問合せの読み込み
	$filename = __DIR__ . DIRECTORY_SEPARATOR . "article_contact.php";
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
