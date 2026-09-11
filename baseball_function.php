<?php
# **********************************************************
#  baseball_function.php
# **********************************************************
# ==========================================================
# 必要なファイル読み込み
# ==========================================================
require_once( __DIR__ . DIRECTORY_SEPARATOR . 'baseball_config.php' );
require_once( __DIR__ . DIRECTORY_SEPARATOR . 'Class_PHPMailer.php');

# ==========================================================
# 変数の定義
# ==========================================================

# ==========================================================
# セッションの開始
# ==========================================================
$g_Log->notice( "S : " . basename( __FILE__ ), __FUNCTION__, basename( __FILE__ ) );

if( session_status() === PHP_SESSION_NONE ){
	$g_Log->notice( "セッション開始", __FUNCTION__, basename( __FILE__ ) );
	session_start();
}

# ==========================================================
# リクエストチェック処理
# @param string $p_name チェック対象のリクエスト名
# @param boolean $p_isEmptyCheck 空文字も許容するかどうか (true:空文字はNG / false:空文字はOK)
# @param string $p_request リクエスト方法 (POST/GET)
# @return boolean チェック成功すればtrue、失敗すればfalse
# ==========================================================
function checkRequest( $p_name, $p_isEmptyCheck = true, $p_request = 'POST' ){

	global $g_Log;
	$g_Log->notice( "リクエストチェック処理 : name={$p_name}, isEmptyCheck={$p_isEmptyCheck}, request={$p_request}", __FUNCTION__, basename( __FILE__ ) );

	$request = null;
	switch ( strtoupper( $p_request ) ) {
		case 'POST':
			$request = $_POST;
			break;
		case 'GET':
			$request = $_GET;
			break;
		default:
			$g_Log->warning( "リクエスト[ {$p_request} ]が不正です", __FUNCTION__, basename( __FILE__ ) );
			return false;
	}

	// リクエストチェック
	if( ! isset( $request[ $p_name ] ) ) {
		$g_Log->notice( "リクエスト[ {$p_request} : {$p_name} ]が未設定です", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	$g_Log->notice( "リクエスト[ {$p_request} : value={$request[ $p_name ]} ]", __FUNCTION__, basename( __FILE__ ) );

	// 空文字チェック
	if( $p_isEmptyCheck ){
		if( empty( $request[ $p_name ] ) ) {
			$g_Log->notice( "リクエスト[ {$p_request} : {$p_name} ]が空( {$request[ $p_name ]} )です", __FUNCTION__, basename( __FILE__ ) );
			return false;
		}
	}

	return true;
}

# ==========================================================
# 日付チェック処理
# @param string $p_value チェック対象の値
# @return boolean チェック成功すればtrue、失敗すればfalse
# ==========================================================
function isValidDate( $p_date ){

	global $g_Log;
	$g_Log->notice( "日付チェック処理 : value={$p_date}", __FUNCTION__, basename( __FILE__ ) );

	// チェックする日付フォーマットのリスト
	$formats = [ 'Y-m-d', 'Y/m/d', 'Ymd', ];

	foreach ( $formats as $format ) {
		$dt = DateTime::createFromFormat( $format, $p_date );

		if ( $dt && $dt->format( $format ) === $p_date) {
			return $dt->format( 'Y-m-d' ); // ← フォーマット + 有効日付
		}
	}
	return false;
}

# ==========================================================
# テキストチェック処理
# @param string $p_text チェック対象の値
# @param boolean $p_checkEmpty 空文字も許容するかどうか (true:空文字はNG / false:空文字はOK)
# @return boolean チェック成功すればtrue、失敗すればfalse
# ==========================================================
function checkText( $p_text, $p_checkEmpty = true ){

	global $g_Log;
	$g_Log->notice( "テキストチェック処理 : value={$p_text}", __FUNCTION__, basename( __FILE__ ) );


	if ( $p_text === null ) {
		$g_Log->notice( "IS NULL : value={$p_text}", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 前後空白を除去
	$text = trim( $p_text );

	// 空文字チェック
	if( $p_checkEmpty ){
		if ( $text === '' ) {
			$g_Log->notice( "IS EMPTY : value={$p_text}", __FUNCTION__, basename( __FILE__ ) );
			return false;
		}
	}

	return $text;
}

# ==========================================================
# 数値チェック処理
# @param string $p_text チェック対象の値
# @param int $p_min 最小値
# @param int $p_max 最大値
# @param boolean $p_checkEmpty 空文字も許容するかどうか (true:空文字はNG / false:空文字はOK)
# @return boolean チェック成功すればtrue、失敗すればfalse
# ==========================================================
function checkNumeric( $p_text, $p_min = 0, $p_max = PHP_INT_MAX, $p_checkEmpty = true ){

	global $g_Log;
	$g_Log->notice( "数値チェック処理 : value={$p_text}", __FUNCTION__, basename( __FILE__ ) );

	if ( $p_text === null ) {
		$g_Log->notice( "IS NULL : value={$p_text}", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 前後空白を除去
	$text = trim( $p_text );

	// 空文字チェック
	if( $p_checkEmpty ){
		if ( $text === '' ) {
			$g_Log->notice( "IS EMPTY : value={$p_text}", __FUNCTION__, basename( __FILE__ ) );
			return false;
		}
	}

	// 数値チェック
	if( ! is_numeric( $text ) ) {
		$g_Log->notice( "IS NOT NUMERIC : value={$p_text}", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 範囲チェック
	$number = (int) $text;
	if( $number < $p_min || $number > $p_max ) {
		$g_Log->notice( "OUT OF RANGE : value={$p_text}, min={$p_min}, max={$p_max}", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return $number;
}

# ==========================================================
# 認証チェック
# ==========================================================
function isAuthenticated(){

	global $g_Log;
	$g_Log->notice( "S : 認証チェック", __FUNCTION__, basename( __FILE__ ) );

	if( ! isset( $_SESSION[ 'AUTH_USER_ID' ] ) ) {
		$g_Log->notice( "セッション変数[ AUTH_USER_ID ]が未設定", __FUNCTION__, basename( __FILE__ ) );
		$g_Log->notice( "E : 認証チェック", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	if( ! isset( $_SESSION[ 'AUTH_ROLE_LEVEL' ] ) ) {
		$g_Log->notice( "セッション変数[ role_level ]が未設定", __FUNCTION__, basename( __FILE__ ) );
		$g_Log->notice( "E : 認証チェック", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	if( empty( $_SESSION[ 'AUTH_USER_ID' ] ) ) {
		$g_Log->notice( "ユーザー未認証 : [ AUTH_USER_ID ] = " . $_SESSION[ 'AUTH_USER_ID' ], __FUNCTION__, basename( __FILE__ ) );
		$g_Log->notice( "E : 認証チェック", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	if( $_SESSION[ 'AUTH_ROLE_LEVEL' ] == 0 ) {
		$g_Log->notice( "ユーザー権限 : [ role_level ] = " . $_SESSION[ 'AUTH_ROLE_LEVEL' ], __FUNCTION__, basename( __FILE__ ) );
		$g_Log->notice( "E : 認証チェック", __FUNCTION__, basename( __FILE__ ) );
		return true;
	}

	if( empty( $_SESSION[ 'AUTH_ROLE_LEVEL' ] ) ) {
		$g_Log->notice( "ユーザー権限未設定 : [ role_level ] = " . $_SESSION[ 'AUTH_ROLE_LEVEL' ], __FUNCTION__, basename( __FILE__ ) );
		$g_Log->notice( "E : 認証チェック", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	$g_Log->notice( "E : 認証チェックが完了しました", __FUNCTION__, basename( __FILE__ ) );
	return true;
}


# ==========================================================
# メールアドレスチェック処理
# ==========================================================
function checkUserCd( $p_userCd = null ) {

	global $g_Log;
	$g_Log->notice( "S : ユーザコードチェック処理 : " . $p_userCd, __FUNCTION__, basename( __FILE__ ) );

	$userCd = empty( $p_userCd ) ? null : trim( $p_userCd );

	// NULLチェック
	if ( is_null( $userCd ) ) {
		$_SESSION[ 'error_msg' ] = 'ユーザコードを入力して下さい。';
		$g_Log->warning( "IS NULL : " . $userCd . " : " . $_SESSION[ 'error_msg' ], __FUNCTION__, basename( __FILE__ ) );
		$g_Log->notice( "E : ユーザコードチェック処理 : " . $userCd, __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 空文字チェック
	if( empty( $userCd ) ) {
		$_SESSION[ 'error_msg' ] = 'ユーザコードを入力して下さい。';
		$g_Log->warning( "IS EMPTY : " . $userCd . " : " . $_SESSION[ 'error_msg' ], __FUNCTION__, basename( __FILE__ ) );
		$g_Log->notice( "E : ユーザコードチェック処理 : " . $userCd, __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	$g_Log->notice( "E : ユーザコードチェック処理 : " . $p_userCd, __FUNCTION__, basename( __FILE__ ) );
	return true;
}

# ==========================================================
# パスワードチェック処理
# ==========================================================
function checkPassword( $p_password = null ) {

	global $g_Log;
	$g_Log->notice( "S : パスワードチェック処理 : " . $p_password, __FUNCTION__, basename( __FILE__ ) );

	$password = empty( $p_password ) ? null : trim( $p_password );

	// NULLチェック
	if ( is_null( $password ) ) {
		$_SESSION[ 'error_msg' ] = 'パスワードを入力して下さい。';
		$g_Log->warning( "IS NULL : " . $password . " : " . $_SESSION[ 'error_msg' ], __FUNCTION__, basename( __FILE__ ) );
		$g_Log->notice( "E : パスワードチェック処理 : " . $password, __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 空文字チェック
	if( empty( $password ) ) {
		$_SESSION[ 'error_msg' ] = 'パスワードを入力して下さい。';
		$g_Log->warning( "IS EMPTY : " . $password . " : " . $_SESSION[ 'error_msg' ], __FUNCTION__, basename( __FILE__ ) );
		$g_Log->notice( "E : パスワードチェック処理 : " . $password, __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 長さチェック
	if ( strlen( $password ) < 6 ) {
		$_SESSION[ 'error_msg' ] = 'パスワードは6文字以上で入力して下さい。';
		$g_Log->warning( "INVALID LENGTH : " . $password . " : " . $_SESSION[ 'error_msg' ], __FUNCTION__, basename( __FILE__ ) );
		$g_Log->notice( "E : パスワードチェック処理 : " . $password, __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	$g_Log->notice( "E : パスワードチェック処理 : " . $password, __FUNCTION__, basename( __FILE__ ) );

	return true;
}

# ==========================================================
# 認証チェック処理
# ==========================================================
function checkAuthenticate( $p_userCd = null, $p_password = null ) {

	global $g_Log;
	$g_Log->notice( "S : 認証チェック処理 : {$p_userCd} : {$p_password}", __FUNCTION__, basename( __FILE__ ) );

	$userCd = empty( $p_userCd ) ? null : trim( $p_userCd );
	$password = empty( $p_password ) ? null : trim( $p_password );

	// 存在チェック
	try {
		global $g_DB;

		// SQL文作成
		$SQL = "";
		$SQL .= "SELECT ";
		$SQL .= "  user_id ";
		$SQL .= ", user_cd ";
		$SQL .= ", password ";
		$SQL .= ", username ";
		$SQL .= ", email ";
		$SQL .= ", baseball_user.role_level ";
		$SQL .= ", signin_at ";
		$SQL .= ", signin_by ";
		$SQL .= ", baseball_user.is_enabled ";
		$SQL .= ", IFNULL( role_name, '不正な権限' ) AS role_name ";
		$SQL .= "  FROM baseball_user ";
		$SQL .= "  LEFT JOIN baseball_role ON baseball_user.role_level = baseball_role.role_level ";
		$SQL .= " WHERE baseball_user.is_enabled = 1 ";
		$SQL .= "   AND user_cd = :user_cd ";

		$SQL_Parameters = array(
			"user_cd" => $userCd,
		);

		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );
		if ($dataTable === false) {
			$_SESSION['error_msg'] = 'データベースエラーが発生しました。';
			$g_Log->error("DB SELECT失敗 : SQL={$SQL} PARAMS=" . json_encode($SQL_Parameters), __FUNCTION__, basename(__FILE__));
			$g_Log->notice( "E : 認証チェック処理 : {$p_userCd} : {$p_password}", __FUNCTION__, basename( __FILE__ ) );
			return false;
		}
		if (empty($dataTable)) {
			$_SESSION['error_msg'] = 'ユーザコードまたはパスワードが正しくありません。';
			$g_Log->warning("NOT FOUND : {$userCd} : {$_SESSION['error_msg']}", __FUNCTION__, basename(__FILE__));
			$g_Log->notice( "E : 認証チェック処理 : {$p_userCd} : {$p_password}", __FUNCTION__, basename( __FILE__ ) );
			return false;
		}

		foreach( $dataTable as $row ){
			$_SESSION[ 'AUTH_USER_ID' ] = $row[ 'user_id' ];
			$_SESSION[ 'AUTH_USER_CD' ] = $row[ 'user_cd' ];
			$_SESSION[ 'AUTH_USER_NAME' ] = $row[ 'username' ];
			$_SESSION[ 'AUTH_PWD_HASH' ] = $row[ 'password' ];
			$_SESSION[ 'AUTH_EMAIL' ] = $row[ 'email' ];
			$_SESSION[ 'AUTH_ROLE_LEVEL' ] = $row[ 'role_level' ];
			$_SESSION[ 'AUTH_ROLE_NAME' ] = $row[ 'role_name' ];
			$_SESSION[ 'AUTH_SIGNIN_AT' ] = $row[ 'signin_at' ];
			$_SESSION[ 'AUTH_SIGNIN_BY' ] = $row[ 'signin_by' ];
			$g_Log->notice( "SESSION[ 'AUTH_USER_ID' ] : {$row[ 'user_id' ]} セットしました", __FUNCTION__, basename( __FILE__ ) );
			$g_Log->notice( "SESSION[ 'AUTH_USER_CD' ] : {$row[ 'user_cd' ]} セットしました", __FUNCTION__, basename( __FILE__ ) );
			$g_Log->notice( "SESSION[ 'AUTH_USER_NAME' ] : {$row[ 'username' ]} セットしました", __FUNCTION__, basename( __FILE__ ) );
			$g_Log->notice( "SESSION[ 'AUTH_PWD_HASH' ] : {$row[ 'password' ]} セットしました", __FUNCTION__, basename( __FILE__ ) );
			$g_Log->notice( "SESSION[ 'AUTH_EMAIL' ] : {$row[ 'email' ]} セットしました", __FUNCTION__, basename( __FILE__ ) );
			$g_Log->notice( "SESSION[ 'AUTH_ROLE_LEVEL' ] : {$row[ 'role_level' ]} セットしました", __FUNCTION__, basename( __FILE__ ) );
			$g_Log->notice( "SESSION[ 'AUTH_ROLE_NAME' ] : {$row[ 'role_name' ]} セットしました", __FUNCTION__, basename( __FILE__ ) );
			$g_Log->notice( "SESSION[ 'AUTH_SIGNIN_AT' ] : {$row[ 'signin_at' ]} セットしました", __FUNCTION__, basename( __FILE__ ) );
			$g_Log->notice( "SESSION[ 'AUTH_SIGNIN_BY' ] : {$row[ 'signin_by' ]} セットしました", __FUNCTION__, basename( __FILE__ ) );
		}
	} catch ( Exception $e ) {
		$_SESSION['error_msg'] = "システムエラーが発生しました。管理者にご連絡ください。";
		$g_Log->error("EXCEPTION : " . $e->getMessage(), __FUNCTION__, basename(__FILE__));
		$g_Log->notice( "E : 認証チェック処理 : {$p_userCd} : {$p_password}", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	try {
		// パスワードの検証
		if( ! password_verify( $password, $_SESSION[ 'AUTH_PWD_HASH' ] ) ) {
			throw new PDOException( "パスワードが正しくありません。" );
		}

		global $g_DB;
		// SQL文作成
		$SQL = "";
		$SQL .= "UPDATE baseball_user ";
		$SQL .= "SET signin_at = CURRENT_TIMESTAMP ";
		$SQL .= "  , signin_by = :signin_by ";
		$SQL .= " WHERE is_enabled = 1 ";
		$SQL .= "   AND user_id = :user_id ";

		$SQL_Parameters = array(
			"user_id" => $_SESSION[ 'AUTH_USER_ID' ],
			"signin_by" => basename( __FILE__ )
		);

		$stmt = $g_DB->execute( $SQL, $SQL_Parameters );
		if ($stmt === false) {
			$_SESSION['error_msg'] = 'データベース更新に失敗しました。';
			$g_Log->error("DB EXECUTE失敗 : SQL={$SQL} PARAMS=" . json_encode($SQL_Parameters), __FUNCTION__, basename(__FILE__));
			$g_Log->notice( "E : 認証チェック処理 : {$p_userCd} : {$p_password}", __FUNCTION__, basename( __FILE__ ) );
			return false;
		}

	} catch ( Exception $e ) {
		$_SESSION['error_msg'] = "パスワードが正しくありません。";
		$g_Log->error("EXCEPTION : " . $e->getMessage(), __FUNCTION__, basename(__FILE__));
	
		$g_Log->notice( "セッション情報をクリアします。", __FUNCTION__, basename( __FILE__ ) );
		$_SESSION[ 'AUTH_USER_ID' ] = null;
		$_SESSION[ 'AUTH_USER_CD' ] = null;
		$_SESSION[ 'AUTH_USER_NAME' ] = null;
		$_SESSION[ 'AUTH_PWD_HASH' ] = null;
		$_SESSION[ 'AUTH_EMAIL' ] = null;
		$_SESSION[ 'AUTH_ROLE_LEVEL' ] = null;
		$_SESSION[ 'AUTH_ROLE_NAME' ] = null;
		$_SESSION[ 'AUTH_SIGNIN_AT' ] = null;
		$_SESSION[ 'AUTH_SIGNIN_BY' ] = null;
		
		$g_Log->notice( "E : 認証チェック処理 : {$p_userCd} : {$p_password}", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	$g_Log->notice( "E : 認証チェック処理 : {$p_userCd} : {$p_password}", __FUNCTION__, basename( __FILE__ ) );
	return true;
}
# ==========================================================
# クライアントIPアドレス取得処理
# プロキシ配下ならX-Real-IP等も検討
# ※信頼できるプロキシ環境でのみヘッダを採用してください
# @return string クライアントIPアドレス
# ==========================================================
function getClientIp() {

	global $g_Log;
	$g_Log->notice( "S : クライアントIP取得処理", __FUNCTION__, basename( __FILE__ ) );

	$ip = $_SERVER[ 'HTTP_X_REAL_IP' ] ?? $_SERVER[ 'REMOTE_ADDR' ] ?? '0.0.0.0';

	$g_Log->notice( "E : クライアントIP取得処理 : {$ip}", __FUNCTION__, basename( __FILE__ ) );

	return $ip;
}

# ==========================================================
# ファイル保存の簡易レート制限
# （固定ウィンドウではなく「直近window秒のログ」方式）
# 例: window秒以内に maxRequests を超えたら 429
# // 例：1分5回まで
# @return boolean レート制限に引っかからなければtrue、false
# ==========================================================
function rateLimitOrFail( $p_FormName ): bool {
	
	$maxRequests = 5;
	$windowSeconds = 60;
	
	global $g_Log;
	$g_Log->notice( "S : レート制限チェック : Form={$p_FormName} : maxRequests={$maxRequests} : windowSeconds={$windowSeconds}", __FUNCTION__, basename( __FILE__ ) );

	$now = microtime(true);
    $now_datetime = date('Y-m-d H:i:s');
    $ip = getClientIp();

	$g_Log->notice( "現在時刻 : {$now_datetime} : IP={$ip}", __FUNCTION__, basename( __FILE__ ) );

	// IPごとにファイルを作成
	global $g_Log_Path;
	$file = $g_Log_Path . DIRECTORY_SEPARATOR . $p_FormName . '_form_' . md5( $ip ) . '.json'; 

	$g_Log->notice( "ファイル名 : {$file}", __FUNCTION__, basename( __FILE__ ) );

	// ファイルロックして処理(c+は存在しない場合は作成、存在する場合は読み書き両方可能)
	$fp = fopen( $file, 'c+' );
	if ( ! $fp ) {	//	ファイルを開けないときはリターン
		$g_Log->notice( "ファイルを開けません : {$file}", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 排他（同時投稿で壊れないように書込み用ロック）
	flock( $fp, LOCK_EX );

	$data = stream_get_contents( $fp );
	$records = [];
	if ( $data ) {
		$decoded = json_decode( $data, true );
		if ( is_array( $decoded ) ) $records = $decoded;
	}

	// window外を削除
	$records = array_values( array_filter( $records, fn( $rec ) => isset($rec['timestamp']) && ( $now - (float)$rec['timestamp'] ) < $windowSeconds ) );

	// 今回分を追加
    $records[] = [
        'ip' => $ip,
        'datetime' => $now_datetime,
        'timestamp' => $now
    ];

	if ( count( $records ) > $maxRequests ) {
		// 保存してから返す（連打に強くする）
		ftruncate( $fp, 0 );	//	ファイルを空にする
		rewind( $fp );	//	ファイルポインタを先頭に戻す
		fwrite( $fp, json_encode( $records, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) );
		fflush( $fp );	//	出力バッファをフラッシュしてからロック解除
		flock( $fp, LOCK_UN );	//	ロック解除
		fclose( $fp );	//	ファイルを閉じる

		$_SESSION[ 'error_msg' ] = "送信回数が多すぎます。しばらく時間をおいてから再度お試しください。";
		$g_Log->warning( "送信回数が多すぎます : IP={$ip} : {$windowSeconds}秒で{$maxRequests}回まで。", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 更新保存
	ftruncate( $fp, 0 );	//	ファイルを空にする
	rewind( $fp );	//	ファイルポインタを先頭に戻す
	fwrite( $fp, json_encode( $records, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) );
	fflush( $fp );	//	出力バッファをフラッシュしてからロック解除

	flock( $fp, LOCK_UN );	//	ロック解除
	fclose( $fp );	//	ファイルを閉じる
	return true;
}

# ==========================================================
# ハニーポット検証
# @return boolean ハニーポットが埋まっていなければtrue、埋まっていればfalse
# ==========================================================
function verifyHoneypotOrFail( string $value ): bool {

	global $g_Log;
	$g_Log->notice( "S : ハニーポット検証 : value={$value}", __FUNCTION__, basename( __FILE__ ) );

	$v = trim( ( string )( $value ?? '' ) );
	
	if ( $v !== '' ) {
		$ip = getClientIp();
		$g_Log->warning( "ハニーポットが埋まっているためボットとみなします : IP={$ip}", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	$g_Log->notice( "E : ハニーポット検証終了 : value={$value}", __FUNCTION__, basename( __FILE__ ) );

	return true;
}

# ==========================================================
# CSRF検証
# @return boolean CSRFトークンが一致すればtrue、そうでなければfalse
# ==========================================================
function verifyCsrfOrFail( $post_token, $session_token ): bool {
	
	global $g_Log;
	$g_Log->notice( "S : CSRF検証 : post_token={$post_token}, session_token={$session_token}", __FUNCTION__, basename( __FILE__ ) );

	$posted  = $post_token ?? '';
	$session = $session_token ?? '';

	// トークンがない／不一致なら拒否（hash_equals推奨例あり）
	if ( ! $posted || ! $session || ! hash_equals( $session, $posted )) {
		$ip = getClientIp();
		$g_Log->warning( "CSRFトークンが一致しないため不正リクエストとみなします : IP={$ip}", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	$g_Log->notice( "E : CSRF検証終了 : post_token={$post_token}, session_token={$session_token}", __FUNCTION__, basename( __FILE__ ) );

	return true;
}

# ==========================================================
# 問合せ内容件数取得処理
# @return int 取得成功すれば件数、失敗すればfalse
# ==========================================================
function getInquiryCount(){

	global $g_Log;
	$g_Log->notice( "問合せ内容件数取得処理", __FUNCTION__, basename( __FILE__ ) );
	
	// SQL文作成
	$SQL = "";
	$SQL .= "SELECT ";
	$SQL .= "  count( * ) AS counter ";
	$SQL .= "FROM baseball_inquiry ";
	$SQL .= "WHERE is_enabled = 1 ";
	
	$SQL_Parameters = array(
	);

	$counter = 0;

	try {
		global $g_DB;
		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );

		foreach( $dataTable as $row ){
			$counter = $row[ 'counter' ];
			$g_Log->notice( "問合せ内容件数 : {$counter} 件", __FUNCTION__, basename( __FILE__ ) );
		}
	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '問合せ内容の取得に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return $counter;
}

# ==========================================================
# 問合せ内容取得処理
# @param int $p_pageCurrent 現在のページ番号
# @param int $p_pageRows 1ページの表示行数
# @return array 取得成功すればデータ配列、失敗すればfalse
# ==========================================================
function getInquiryList( $p_pageCurrent = 1, $p_pageRows = 10 ){

	global $g_Log;
	$g_Log->notice( "問合せ内容取得処理 : pageCurrent={$p_pageCurrent}, pageRows={$p_pageRows}", __FUNCTION__, basename( __FILE__ ) );

	if( is_numeric( $p_pageCurrent ) ){
		$pageCurrent = (int)$p_pageCurrent;
	} else {
		$pageCurrent = 1;
	}

	if( $pageCurrent < 1 ){
		$pageCurrent = 1;
	}

	if( is_numeric( $p_pageRows ) ){
		$pageRows = (int)$p_pageRows;
	} else {
		$pageRows = 10;
	}

	if( $pageRows < 1 ){
		$pageRows = 10;
	}
	
	$pageOffset  = ( $pageCurrent - 1 ) * $pageRows;
	
	// SQL文作成
	$SQL = "";
	$SQL .= "SELECT ";
	$SQL .= "  inquiry_id ";
	$SQL .= ", inquiry_at ";
	$SQL .= ", name ";
	$SQL .= ", phone ";
	$SQL .= ", email ";
	$SQL .= ", message ";
	$SQL .= "FROM baseball_inquiry ";
	$SQL .= "WHERE is_enabled = 1 ";
	$SQL .= "ORDER BY inquiry_at DESC ";
	$SQL .= "LIMIT $pageRows OFFSET $pageOffset ";
	
	$SQL_Parameters = array(
	);

	$dataTable = array();

	try {
		global $g_DB;
		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '問合せ内容の取得に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
	}

	return $dataTable;
}

# ==========================================================
# 問合せ内容削除処理
# @param int $p_inquiryId 削除対象の問合せID
# @return boolean 削除成功すればtrue、失敗すればfalse
# ==========================================================
function deleteInquiryId( $p_inquiryId ){

	global $g_Log;
	$g_Log->notice( "問合せ内容削除処理 : inquiryId={$p_inquiryId}", __FUNCTION__, basename( __FILE__ ) );

	$inquiryId = 0;

	if( is_numeric( $p_inquiryId ) ){
		$inquiryId = (int)$p_inquiryId;
	} else {
		return false;
	}
	
	// SQL文作成
	$SQL = "";
	$SQL .= "UPDATE baseball_inquiry ";
	$SQL .= "SET is_enabled = 0 ";
	$SQL .= "WHERE is_enabled = 1 ";
	$SQL .= "  AND inquiry_id = :inquiry_id ";
	
	$SQL_Parameters = array(
		'inquiry_id' => $inquiryId,
	);

	try {
		global $g_DB;
		$stmt = $g_DB->execute( $SQL, $SQL_Parameters );

		if( $stmt === false ){
			throw new PDOException( "SQLの実行に失敗しました。" );
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '問合せ内容の削除に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# 大会登録件数取得処理
# @return int 取得成功すれば件数、失敗すればfalse
# ==========================================================
function getTournamentCount(){

	global $g_Log;
	$g_Log->notice( "大会登録件数取得処理", __FUNCTION__, basename( __FILE__ ) );
	
	// SQL文作成
	$SQL = "";
	$SQL .= "SELECT ";
	$SQL .= "  count( * ) AS counter ";
	$SQL .= "FROM baseball_tournament ";
	$SQL .= "WHERE is_enabled = 1 ";
	
	$SQL_Parameters = array(
	);

	$counter = 0;

	try {
		global $g_DB;
		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );

		foreach( $dataTable as $row ){
			$counter = $row[ 'counter' ];
			$g_Log->notice( "大会登録件数 : {$counter} 件", __FUNCTION__, basename( __FILE__ ) );
		}
	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '大会登録件数の取得に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return $counter;
}

# ==========================================================
# 大会登録内容取得処理
# @param int $p_pageCurrent 現在のページ番号
# @param int $p_pageRows 1ページの表示行数
# @return array 取得成功すればデータ配列、失敗すればfalse
# ==========================================================
function getTournamentList( $p_pageCurrent = 1, $p_pageRows = 10 ){

	global $g_Log;
	$g_Log->notice( "大会登録内容取得処理 : pageCurrent={$p_pageCurrent}, pageRows={$p_pageRows}", __FUNCTION__, basename( __FILE__ ) );

	if( is_numeric( $p_pageCurrent ) ){
		$pageCurrent = (int)$p_pageCurrent;
	} else {
		$pageCurrent = 1;
	}

	if( $pageCurrent < 1 ){
		$pageCurrent = 1;
	}

	if( is_numeric( $p_pageRows ) ){
		$pageRows = (int)$p_pageRows;
	} else {
		$pageRows = 10;
	}

	if( $pageRows < 1 ){
		$pageRows = 10;
	}
	
	$pageOffset  = ( $pageCurrent - 1 ) * $pageRows;
	
	// SQL文作成
	$SQL = "";
	$SQL .= " SELECT ";
	$SQL .= "   t.tournament_id ";
	$SQL .= " , CONCAT( t.tournament_title, ' ', t.tournament_text ) AS tournament_name ";
	$SQL .= " , t.tournament_title ";
	$SQL .= " , t.tournament_text ";
	$SQL .= " , t.tournament_start_date ";
	$SQL .= " , t.tournament_end_date ";
	$SQL .= " , t.tournament1_teams ";
	$SQL .= " , t.tournament1_text ";
	$SQL .= " , t.tournament2_teams ";
	$SQL .= " , t.tournament2_text ";
	$SQL .= " , t.tournament3_teams ";
	$SQL .= " , t.tournament3_text ";
	$SQL .= " , t.tournament_attachment_id ";
	$SQL .= " , IFNULL( a.physical_file_name, '' ) AS physical_file_name ";
	$SQL .= " , IFNULL( a.file_name, '' ) AS file_name ";
	$SQL .= " , IFNULL( a.file_path, '' ) AS file_path ";
	$SQL .= " , IFNULL( a.file_size, 0 ) AS file_size ";
	$SQL .= " , IFNULL( a.mime_type, '' ) AS mime_type ";
	$SQL .= " , IFNULL( a.file_description, '' ) AS file_description ";
	$SQL .= " FROM baseball_tournament AS t ";
	$SQL .= " LEFT JOIN baseball_attachment AS a ON t.tournament_attachment_id = a.attachment_id AND a.is_enabled = 1 ";
	$SQL .= " WHERE t.is_enabled = 1 ";
	$SQL .= " ORDER BY t.tournament_start_date DESC ";
	$SQL .= " , t.tournament_id DESC ";
	$SQL .= " LIMIT $pageRows OFFSET $pageOffset ";
	
	$SQL_Parameters = array(
	);

	$dataTable = array();

	try {
		global $g_DB;
		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '大会登録内容の取得に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
	}

	return $dataTable;
}

# ==========================================================
# 大会登録内容削除処理
# @param int $p_tournamentId 削除対象の大会ID
# @return boolean 削除成功すればtrue、失敗すればfalse
# ==========================================================
function deleteTournamentId( $p_tournamentId ){

	global $g_Log;
	$g_Log->notice( "大会登録内容削除処理 : tournamentId={$p_tournamentId}", __FUNCTION__, basename( __FILE__ ) );

	$tournamentId = 0;

	if( is_numeric( $p_tournamentId ) ){
		$tournamentId = (int)$p_tournamentId;
	} else {
		return false;
	}
	
	// SQL文作成
	$SQL = "";
	$SQL .= "UPDATE baseball_tournament ";
	$SQL .= "SET is_enabled = 0 ";
	$SQL .= "WHERE is_enabled = 1 ";
	$SQL .= "  AND tournament_id = :tournament_id ";
	
	$SQL_Parameters = array(
		'tournament_id' => $tournamentId,
	);

	try {
		global $g_DB;
		$stmt = $g_DB->execute( $SQL, $SQL_Parameters );

		if( $stmt === false ){
			throw new PDOException( "SQLの実行に失敗しました。" );
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '大会登録内容の削除に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# チーム登録内容取得処理
# @param int $p_tournamentId 大会ID
# @param int $p_pageCurrent 現在のページ番号
# @param int $p_pageRows 1ページの表示行数
# @return array 取得成功すればデータ配列、失敗すればfalse
# ==========================================================
function getTeamList( $p_tournamentId, $p_pageCurrent = 1, $p_pageRows = 10 ){

	global $g_Log;
	$g_Log->notice( "チーム登録内容取得処理 : tournamentId={$p_tournamentId}, pageCurrent={$p_pageCurrent}, pageRows={$p_pageRows}", __FUNCTION__, basename( __FILE__ ) );

	// ページ処理チェック
	if( is_numeric( $p_pageCurrent ) ){
		$pageCurrent = (int)$p_pageCurrent;
	} else {
		$pageCurrent = 1;
	}

	if( $pageCurrent < 1 ){
		$pageCurrent = 1;
	}

	if( is_numeric( $p_pageRows ) ){
		$pageRows = (int)$p_pageRows;
	} else {
		$pageRows = 10;
	}

	if( $pageRows < 1 ){
		$pageRows = 10;
	}
	
	$pageOffset  = ( $pageCurrent - 1 ) * $pageRows;
	
	// 大会IDチェック
	if( ! is_numeric( $p_tournamentId ) ){
		$g_Log->notice( "大会IDが数値ではありません : tournamentId={$p_tournamentId}", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	$tournamentId = ( int )$p_tournamentId;

	// SQL文作成
	$SQL = "";
	$SQL .= "SELECT ";
	$SQL .= "  tournament_id ";
	$SQL .= ", team_id ";
	$SQL .= ", team_name ";
	$SQL .= ", team_manager ";
	$SQL .= ", team_contact ";
	$SQL .= ", team_tel ";
	$SQL .= ", team_email ";
	$SQL .= ", team_access_cd ";
	$SQL .= ", tournament1_attend ";
	$SQL .= ", tournament2_attend ";
	$SQL .= ", tournament3_attend ";
	$SQL .= ", CASE tournament1_attend WHEN 1 THEN '参加' ELSE '不参加' END AS tournament1_attend_name ";
	$SQL .= ", CASE tournament2_attend WHEN 1 THEN '参加' ELSE '不参加' END AS tournament2_attend_name ";
	$SQL .= ", CASE tournament3_attend WHEN 1 THEN '参加' ELSE '不参加' END AS tournament3_attend_name ";
	$SQL .= "FROM baseball_team ";
	$SQL .= "WHERE is_enabled = 1 ";
	$SQL .= "  AND tournament_id = :tournament_id ";
	$SQL .= "ORDER BY team_name ASC ";
	$SQL .= ", team_id ASC ";
	$SQL .= "LIMIT $pageRows OFFSET $pageOffset ";

	$SQL_Parameters = array(
		'tournament_id' => $tournamentId,
	);

	$dataTable = array();

	try {
		global $g_DB;
		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = 'チーム登録内容の取得に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
	}

	return $dataTable;
}

# ==========================================================
# チーム登録内容削除処理
# @param int $p_teamId 削除対象のチームID
# @return boolean 削除成功すればtrue、失敗すればfalse
# ==========================================================
function deleteTeamId( $p_teamId ){

	global $g_Log;
	$g_Log->notice( "チーム登録内容削除処理 : teamId={$p_teamId}", __FUNCTION__, basename( __FILE__ ) );

	$teamId = 0;

	if( is_numeric( $p_teamId ) ){
		$teamId = (int)$p_teamId;
	} else {
		return false;
	}
	
	// SQL文作成
	$SQL = "";
	$SQL .= "UPDATE baseball_team ";
	$SQL .= "SET is_enabled = 0 ";
	$SQL .= "WHERE is_enabled = 1 ";
	$SQL .= "  AND team_id = :team_id ";
	
	$SQL_Parameters = array(
		'team_id' => $teamId,
	);

	try {
		global $g_DB;
		$stmt = $g_DB->execute( $SQL, $SQL_Parameters );

		if( $stmt === false ){
			throw new PDOException( "SQLの実行に失敗しました。" );
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = 'チーム登録内容の削除に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# 試合登録内容取得処理
# @param int $p_tournamentId 大会ID
# @param int $p_pageCurrent 現在のページ番号
# @param int $p_pageRows 1ページの表示行数
# @return array 取得成功すればデータ配列、失敗すればfalse
# ==========================================================
function getGameList( $p_tournamentId, $p_pageCurrent = 1, $p_pageRows = 10 ){

	global $g_Log;
	$g_Log->notice( "試合登録内容取得処理 : tournamentId={$p_tournamentId}, pageCurrent={$p_pageCurrent}, pageRows={$p_pageRows}", __FUNCTION__, basename( __FILE__ ) );

	// ページ処理チェック
	if( is_numeric( $p_pageCurrent ) ){
		$pageCurrent = (int)$p_pageCurrent;
	} else {
		$pageCurrent = 1;
	}

	if( $pageCurrent < 1 ){
		$pageCurrent = 1;
	}

	if( is_numeric( $p_pageRows ) ){
		$pageRows = (int)$p_pageRows;
	} else {
		$pageRows = 10;
	}

	if( $pageRows < 1 ){
		$pageRows = 10;
	}
	
	$pageOffset  = ( $pageCurrent - 1 ) * $pageRows;
	
	// 大会IDチェック
	if( ! is_numeric( $p_tournamentId ) ){
		$g_Log->notice( "大会IDが数値ではありません : tournamentId={$p_tournamentId}", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	$tournamentId = ( int )$p_tournamentId;

	// SQL文作成
	$SQL = "";
	$SQL .= "SELECT ";
	$SQL .= "  game_id ";
	$SQL .= ", tournament_id ";
	$SQL .= ", game_class ";
	$SQL .= ", game_count ";
	$SQL .= ", game_block ";
	$SQL .= ", game_name ";
	$SQL .= ", game_date ";
	$SQL .= ", game_place ";
	$SQL .= ", winner_id ";
	$SQL .= ", loser_id ";
	$SQL .= ", team1_id ";
	$SQL .= ", team2_id ";
	$SQL .= ", team1_name ";
	$SQL .= ", team2_name ";
	$SQL .= ", team1_score ";
	$SQL .= ", team2_score ";
	$SQL .= ", team1_last_game_id ";
	$SQL .= ", team2_last_game_id ";
	$SQL .= "FROM baseball_game ";
	$SQL .= "WHERE is_enabled = 1 ";
	$SQL .= "  AND tournament_id = :tournament_id ";
	$SQL .= "ORDER BY game_class ASC ";
	$SQL .= ", game_block ASC ";
	$SQL .= ", game_count ASC ";
	$SQL .= ", game_id ASC ";
	$SQL .= "LIMIT $pageRows OFFSET $pageOffset ";

	$SQL_Parameters = array(
		'tournament_id' => $tournamentId,
	);

	$dataTable = array();

	try {
		global $g_DB;
		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '試合登録内容の取得に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
	}

	return $dataTable;
}

# ==========================================================
# 試合登録内容削除処理
# @param int $p_gameId 削除対象の試合ID
# @return boolean 削除成功すればtrue、失敗すればfalse
# ==========================================================
function deleteGameId( $p_gameId ){

	global $g_Log;
	$g_Log->notice( "試合登録内容削除処理 : gameId={$p_gameId}", __FUNCTION__, basename( __FILE__ ) );

	$gameId = 0;

	if( is_numeric( $p_gameId ) ){
		$gameId = (int)$p_gameId;
	} else {
		return false;
	}
	
	// SQL文作成
	$SQL = "";
	$SQL .= "UPDATE baseball_game ";
	$SQL .= "SET is_enabled = 0 ";
	$SQL .= "WHERE is_enabled = 1 ";
	$SQL .= "  AND game_id = :game_id ";
	
	$SQL_Parameters = array(
		'game_id' => $gameId,
	);

	try {
		global $g_DB;
		$stmt = $g_DB->execute( $SQL, $SQL_Parameters );

		if( $stmt === false ){
			throw new PDOException( "SQLの実行に失敗しました。" );
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '試合登録内容の削除に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# 試合登録内容更新処理
# @param int $p_gameId 更新対象の試合ID
# @return boolean 更新成功すればtrue、失敗すればfalse
# ==========================================================
function updateGameCount( $p_gameId ){

	global $g_Log;
	$g_Log->notice( "試合登録内容更新処理 : gameId={$p_gameId}", __FUNCTION__, basename( __FILE__ ) );

	$gameId = 0;

	if( is_numeric( $p_gameId ) ){
		$gameId = (int)$p_gameId;
	} else {
		return false;
	}
	
	// SQL文作成
	$SQL = "";
	$SQL .= "WITH ranked AS ( ";
	$SQL .= "  SELECT ";
	$SQL .= "    bg.game_id ";
	$SQL .= "  , ROW_NUMBER() OVER (ORDER BY bg.game_count) AS new_rank ";
	$SQL .= "  , base.game_class ";
	$SQL .= "  , base.game_block ";
	$SQL .= "    FROM baseball_game bg ";
	$SQL .= "    JOIN baseball_game base ";
	$SQL .= "      ON base.game_id = :game_id ";
	$SQL .= "   WHERE bg.is_enabled = 1 ";
	$SQL .= "     AND bg.tournament_id = base.tournament_id ";
	$SQL .= "     AND bg.game_class = base.game_class ";
	$SQL .= "     AND bg.game_block = base.game_block ";
	$SQL .= ") ";
	$SQL .= "UPDATE baseball_game AS g ";
	$SQL .= "JOIN ranked r ON g.game_id = r.game_id ";
	$SQL .= "SET g.game_count = r.new_rank ";
	$SQL .= "  , g.game_name = CONCAT( '第', r.game_block, '回戦 第', r.new_rank, '試合', CASE r.game_class WHEN 1 THEN '（裏）' ELSE '' END ) "; // ゲーム名はゲームブロックから「第〇試合」をサブクエリで取得

	$SQL_Parameters = array(
		'game_id' => $gameId
	);

	try {
		global $g_DB;
		$stmt = $g_DB->execute( $SQL, $SQL_Parameters );

		if( $stmt === false ){
			throw new PDOException( "SQLの実行に失敗しました。" );
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '試合登録内容の更新に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# 試合未登録チーム内容取得処理
# @param int $p_tournamentId 大会ID
# @return array 取得成功すればデータ配列、失敗すればfalse
# ==========================================================
function getGameUnsetTeamList( $p_tournamentId ){

	global $g_Log;
	$g_Log->notice( "試合未登録チーム内容取得処理 : tournamentId={$p_tournamentId}", __FUNCTION__, basename( __FILE__ ) );
	
	// 大会IDチェック
	if( ! is_numeric( $p_tournamentId ) ){
		$g_Log->notice( "大会IDが数値ではありません : tournamentId={$p_tournamentId}", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	$tournamentId = ( int )$p_tournamentId;

	// SQL文作成
	$SQL = "";
	$SQL .= "SELECT ";
	$SQL .= "  t.tournament_id ";
	$SQL .= ", t.team_id ";
	$SQL .= ", t.team_name ";
	$SQL .= ", t.team_manager ";
	$SQL .= ", t.team_contact ";
	$SQL .= ", t.team_access_cd ";
	$SQL .= "FROM baseball_team t ";
	$SQL .= "LEFT JOIN baseball_game g ON ( t.team_id = g.team1_id OR t.team_id = g.team2_id ) AND g.is_enabled = 1 ";
	$SQL .= "WHERE t.is_enabled = 1 ";
	$SQL .= "  AND t.tournament_id = :tournament_id ";
	$SQL .= "  AND g.game_id IS NULL ";
	$SQL .= "ORDER BY t.team_name ASC ";
	$SQL .= ", t.team_id ASC ";

	$SQL_Parameters = array(
		'tournament_id' => $tournamentId,
	);

	$dataTable = array();

	try {
		global $g_DB;
		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = 'チーム登録内容の取得に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
	}

	return $dataTable;
}

# ==========================================================
# 試合登録内容取得処理
# @param int $p_tournamentId 大会ID
# @param int $p_pageCurrent 現在のページ番号
# @param int $p_pageRows 1ページの表示行数
# @return array 取得成功すればデータ配列、失敗すればfalse
# ==========================================================
function getGameList2( $p_tournamentId, $p_accessCd, $p_pageCurrent = 1, $p_pageRows = 10 ){

	global $g_Log;
	$g_Log->notice( "試合結果登録内容取得処理 : tournamentId={$p_tournamentId}, accessCd={$p_accessCd}, pageCurrent={$p_pageCurrent}, pageRows={$p_pageRows}", __FUNCTION__, basename( __FILE__ ) );

	// ページ処理チェック
	if( is_numeric( $p_pageCurrent ) ){
		$pageCurrent = (int)$p_pageCurrent;
	} else {
		$pageCurrent = 1;
	}

	if( $pageCurrent < 1 ){
		$pageCurrent = 1;
	}

	if( is_numeric( $p_pageRows ) ){
		$pageRows = (int)$p_pageRows;
	} else {
		$pageRows = 10;
	}

	if( $pageRows < 1 ){
		$pageRows = 10;
	}
	
	$pageOffset  = ( $pageCurrent - 1 ) * $pageRows;
	
	// 大会IDチェック
	if( ! is_numeric( $p_tournamentId ) ){
		$g_Log->notice( "大会IDが数値ではありません : tournamentId={$p_tournamentId}", __FUNCTION__, basename( __FILE__ ) );
		return array();
	}
	
	// アクセスコードチェック
###	if( ! checkText( $p_accessCd, true ) ){
###		$g_Log->notice( "アクセスコードが不正です : accessCd={$p_accessCd}", __FUNCTION__, basename( __FILE__ ) );
###		return array();
###	}

	$tournamentId = ( int )$p_tournamentId;
	$accessCd = htmlspecialchars( $p_accessCd, ENT_QUOTES, 'UTF-8' );

	// SQL文作成
	$SQL = "";
	$SQL .= "SELECT DISTINCT ";
	$SQL .= "  g.game_id ";
	$SQL .= ", g.tournament_id ";
	$SQL .= ", g.game_class ";
	$SQL .= ", g.game_count ";
	$SQL .= ", g.game_block ";
	$SQL .= ", g.game_name ";
	$SQL .= ", g.game_date ";
	$SQL .= ", g.game_place ";
	$SQL .= ", g.winner_id ";
	$SQL .= ", g.loser_id ";
	$SQL .= ", g.team1_id ";
	$SQL .= ", g.team2_id ";
	$SQL .= ", g.team1_name ";
	$SQL .= ", g.team2_name ";
	$SQL .= ", g.team1_score ";
	$SQL .= ", g.team2_score ";
	$SQL .= ", g.team1_last_game_id ";
	$SQL .= ", g.team2_last_game_id ";
	$SQL .= "FROM baseball_game AS g ";
	$SQL .= "INNER JOIN baseball_team t ON t.is_enabled = 1 AND t.tournament_id = g.tournament_id AND ( t.team_id = g.team1_id OR t.team_id = g.team2_id ) ";
	$SQL .= "WHERE g.is_enabled = 1 ";
	$SQL .= "  AND g.tournament_id = :tournament_id ";
###	$SQL .= "  AND t.team_access_cd = :access_cd ";
	$SQL .= "ORDER BY g.game_class ASC ";
	$SQL .= ", g.game_block ASC ";
	$SQL .= ", g.game_count ASC ";
	$SQL .= ", g.game_id ASC ";
	$SQL .= "LIMIT $pageRows OFFSET $pageOffset ";

	$SQL_Parameters = array(
		'tournament_id' => $tournamentId,
###		'access_cd' => $accessCd,
	);

	$dataTable = array();

	try {
		global $g_DB;
		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '試合結果登録内容の取得に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
	}

	return $dataTable;
}

# ==========================================================
# 添付ファイルアップロード処理
# @param string $p_File INPUT name属性の値
# @param int $p_attachmentType 添付ファイルの種別（0: 大会資料、1: その他）
# @return int アップロード成功すれば新しいファイルID、失敗すれば0
# ==========================================================
function uploadAttachement( $p_File, $p_attachmentType = 0 ){

	global $g_Log;
	$g_Log->notice( "添付ファイルアップロード処理 : fileName={$p_File}, attachmentType={$p_attachmentType}", __FUNCTION__, basename( __FILE__ ) );

	// ファイルアップロードの保存先ディレクトリ
	$t_SaveDir = 'attachment/';
	$newId = 0;

	// POSTリクエスト以外は処理しない
	if ( $_SERVER[ 'REQUEST_METHOD' ] !== 'POST' ) {
		$g_Log->warning( "POSTリクエストではありません : REQUEST_METHOD={$_SERVER[ 'REQUEST_METHOD' ]} fileName={$p_File}", __FUNCTION__, basename( __FILE__ ) );
		return $newId;
	}

	if ( ! isset( $_FILES[ $p_File ] ) ) {
		$g_Log->warning( "ファイルが選択されていません : FILES : {$_FILES[ $p_File ]} fileName={$p_File}", __FUNCTION__, basename( __FILE__ ) );
		return $newId;
	}

	if ( $_FILES[ $p_File ][ 'name' ] === '' ) {
		$g_Log->warning( "ファイルが選択されていません : name={$_FILES[ $p_File ][ 'name' ]} fileName={$p_File}", __FUNCTION__, basename( __FILE__ ) );
		return $newId;
	}
	
	if ( $_FILES[ $p_File ][ 'error' ] !== UPLOAD_ERR_OK ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = 'ファイルのアップロード中にエラーが発生しました';
		$g_Log->warning( "{$_SESSION[ 'FORM_ERR_MSG' ]} : error={$_FILES[ $p_File ][ 'error' ]} fileName={$p_File}", __FUNCTION__, basename( __FILE__ ) );
		return $newId;
	}

	$t_Path         = $_FILES[ $p_File ][ 'tmp_name' ]; // 一時保存先
	$t_OriginalName = $_FILES[ $p_File ][ 'name' ];     // 元のファイル名
	$t_FileSize     = $_FILES[ $p_File ][ 'size' ];     // ファイルサイズ
	$t_finfo        = new finfo( FILEINFO_MIME_TYPE );  // MIMEタイプを取得するためのfinfoオブジェクト
	$t_mimeType     = $t_finfo->file( $_FILES[ $p_File ][ 'tmp_name' ] ); // MIMEタイプ

	if ( ! is_dir( $t_SaveDir ) ) {
		mkdir( $t_SaveDir, 0777, true );
	}

	// 拡張子取得
	$t_File_Extension = pathinfo( $t_OriginalName, PATHINFO_EXTENSION );

	// ファイル名（拡張子なし）
	$t_File_BaseName  = pathinfo( $t_OriginalName, PATHINFO_FILENAME );

	// 16バイトのランダムなバイト列を生成
	$t_uuid_base = random_bytes( 16 );

	// バージョンを設定 (UUIDv4 = 4)
	// 7バイト目の上位4ビットを4に設定
	// ord($bytes[6])：1バイト文字→数値(0〜255)に変換
	// & 0x0f：上位4ビットを0にして、下位4ビットだけ残す（0000xxxxにする）
	// | 0x40：上位4ビットに 0100 を立てる（0100xxxxにする）
	// chr(...)：数値→1バイト文字に戻す
	$t_uuid_base[ 6 ] = chr( ( ord( $t_uuid_base[ 6 ] ) & 0x0f ) | 0x40 );

	// RFC 4122 バリアントを設定 (10xxxxxx)
	// 9番目のバイト（index 8）の「上位2ビット」を 10 になるようにセットします（RFC 4122 の variant）。
	// UUIDには “variant” があり、どのレイアウトのUUIDかを上位ビットで区別します。
	// RFC 4122（この規格）で定めるvariantは **上位2ビットが 10**です。
	// これを満たすと、文字列表現上、該当ニブルが 8/9/a/b のいずれかになります（多くの解説で触れられる性質）。
	// & 0x3f：上位2ビットを0にして下位6ビットを残す（00xxxxxx）
	// | 0x80：最上位ビットを1にする（10xxxxxx）
	// 結果として上位2ビットが 10 になる、という典型手順です
	$t_uuid_base[ 8 ] = chr( ( ord( $t_uuid_base[ 8 ] ) & 0x3f ) | 0x80 );

	// 8-4-4-4-12 に整形
	// UUIDは一般に「16進32桁を 8-4-4-4-12 に区切った表現」で扱われます。
	// UUIDの典型的な文字列表現は、この区切り（合計36文字、ハイフン4つ）です
	$t_uuid = vsprintf( '%s%s-%s-%s-%s-%s%s%s', str_split( bin2hex( $t_uuid_base ), 4 ) );

	// 新しいファイル名
	$t_newFileName = $t_uuid . '.' . $t_File_Extension;

	// 保存先パス
	$t_newFilePath = $t_SaveDir . $t_newFileName;

	// ファイルの保存
	if ( ! move_uploaded_file( $t_Path, $t_newFilePath ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = 'ファイルのアップロードに失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . $t_OriginalName, __FUNCTION__, basename( __FILE__ ) );
		return 0;
	}

	$g_Log->notice( "ファイルをアップロードしました。 : ファイル名={$t_OriginalName} -> {$t_newFilePath}", __FUNCTION__, basename( __FILE__ ) );

	// アップロードしたファイルの書込み
	$SQL = "";
	$SQL .= " INSERT INTO baseball_attachment ";
	$SQL .= " ( physical_file_name ";
	$SQL .= " , attachment_type ";
	$SQL .= " , file_name ";
	$SQL .= " , file_path ";
	$SQL .= " , file_size ";
	$SQL .= " , mime_type ";
	$SQL .= " , file_description ";
	$SQL .= " , is_enabled ";
	$SQL .= " , created_at ";
	$SQL .= " , created_by ";
	$SQL .= " , updated_at ";
	$SQL .= " , updated_by ";
	$SQL .= " ) ";
	$SQL .= " VALUES ";
	$SQL .= " ( :physical_file_name ";
	$SQL .= " , :attachment_type ";
	$SQL .= " , :file_name ";
	$SQL .= " , :file_path ";
	$SQL .= " , :file_size ";
	$SQL .= " , :mime_type ";
	$SQL .= " , :file_description ";
	$SQL .= " , 1 ";
	$SQL .= " , CURRENT_TIMESTAMP ";
	$SQL .= " , :created_by ";
	$SQL .= " , CURRENT_TIMESTAMP ";
	$SQL .= " , :updated_by ";
	$SQL .= " ) ";

	$SQL_Parameters = array(
		'physical_file_name' => $t_newFileName,
		'attachment_type' => $p_attachmentType,
		'file_name' => $t_OriginalName,
		'file_path' => $t_SaveDir,
		'file_size' => $t_FileSize,
		'mime_type' => $t_mimeType,
		'file_description' => '',
		'created_by' => basename( __FILE__ ),
		'updated_by' => basename( __FILE__ ),
	);
				
	try {
		global $g_DB;
		$stmt = $g_DB->execute2( $SQL, $SQL_Parameters );

		if( $stmt[ "rowCount" ] == 0 ){
			throw new PDOException( "SQLの実行に失敗しました。" );
		}

		$newId = $stmt[ "newId" ];
		return $newId;
	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = 'ファイルの登録に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return 0;
	}
	return $newId;
}
