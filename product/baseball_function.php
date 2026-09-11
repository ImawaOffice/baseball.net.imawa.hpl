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
$g_Log->debug( "S : " . basename( __FILE__ ), __FUNCTION__, basename( __FILE__ ) );

if( session_status() === PHP_SESSION_NONE ){
	$g_Log->debug( "セッション開始", __FUNCTION__, basename( __FILE__ ) );
	session_start();
}

# ==========================================================
# 認証チェック
# ==========================================================
function isAuthenticated(){

	global $g_Log;
	$g_Log->debug( "S : 認証チェック", __FUNCTION__, basename( __FILE__ ) );

	if( ! isset( $_SESSION[ 'user_id' ] ) ) {
		$g_Log->debug( "セッション変数[ user_id ]が未設定", __FUNCTION__, basename( __FILE__ ) );
		$g_Log->debug( "E : 認証チェック", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	if( ! isset( $_SESSION[ 'role_level' ] ) ) {
		$g_Log->debug( "セッション変数[ role_level ]が未設定", __FUNCTION__, basename( __FILE__ ) );
		$g_Log->debug( "E : 認証チェック", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	if( empty( $_SESSION[ 'user_id' ] ) ) {
		$g_Log->debug( "ユーザー未認証 : [ user_id ] = " . $_SESSION[ 'user_id' ], __FUNCTION__, basename( __FILE__ ) );
		$g_Log->debug( "E : 認証チェック", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	if( $_SESSION[ 'role_level' ] == 0 ) {
		$g_Log->debug( "ユーザー権限 : [ role_level ] = " . $_SESSION[ 'role_level' ], __FUNCTION__, basename( __FILE__ ) );
		$g_Log->debug( "E : 認証チェック", __FUNCTION__, basename( __FILE__ ) );
		return true;
	}

	if( empty( $_SESSION[ 'role_level' ] ) ) {
		$g_Log->debug( "ユーザー権限未設定 : [ role_level ] = " . $_SESSION[ 'role_level' ], __FUNCTION__, basename( __FILE__ ) );
		$g_Log->debug( "E : 認証チェック", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}


# ==========================================================
# メールアドレスチェック処理
# ==========================================================
function checkUserCd( $p_userCd = null ) {

	global $g_Log;
	$g_Log->debug( "S : ユーザコードチェック処理 : " . $p_userCd, __FUNCTION__, basename( __FILE__ ) );

	$userCd = empty( $p_userCd ) ? null : trim( $p_userCd );

	// NULLチェック
	if ( is_null( $userCd ) ) {
		$_SESSION[ 'error_msg' ] = 'ユーザコードを入力して下さい。';
		$g_Log->warning( "IS NULL : " . $userCd . " : " . $_SESSION[ 'error_msg' ], __FUNCTION__, basename( __FILE__ ) );
		$g_Log->debug( "E : ユーザコードチェック処理 : " . $userCd, __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 空文字チェック
	if( empty( $userCd ) ) {
		$_SESSION[ 'error_msg' ] = 'ユーザコードを入力して下さい。';
		$g_Log->warning( "IS EMPTY : " . $userCd . " : " . $_SESSION[ 'error_msg' ], __FUNCTION__, basename( __FILE__ ) );
		$g_Log->debug( "E : ユーザコードチェック処理 : " . $userCd, __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	$g_Log->debug( "E : ユーザコードチェック処理 : " . $p_userCd, __FUNCTION__, basename( __FILE__ ) );
	return true;
}

# ==========================================================
# パスワードチェック処理
# ==========================================================
function checkPassword( $p_password = null ) {

	global $g_Log;
	$g_Log->debug( "S : パスワードチェック処理 : " . $p_password, __FUNCTION__, basename( __FILE__ ) );

	$password = empty( $p_password ) ? null : trim( $p_password );

	// NULLチェック
	if ( is_null( $password ) ) {
		$_SESSION[ 'error_msg' ] = 'パスワードを入力して下さい。';
		$g_Log->warning( "IS NULL : " . $password . " : " . $_SESSION[ 'error_msg' ], __FUNCTION__, basename( __FILE__ ) );
		$g_Log->debug( "E : パスワードチェック処理 : " . $password, __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 空文字チェック
	if( empty( $password ) ) {
		$_SESSION[ 'error_msg' ] = 'パスワードを入力して下さい。';
		$g_Log->warning( "IS EMPTY : " . $password . " : " . $_SESSION[ 'error_msg' ], __FUNCTION__, basename( __FILE__ ) );
		$g_Log->debug( "E : パスワードチェック処理 : " . $password, __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 長さチェック
	if ( strlen( $password ) < 6 ) {
		$_SESSION[ 'error_msg' ] = 'パスワードは6文字以上で入力して下さい。';
		$g_Log->warning( "INVALID LENGTH : " . $password . " : " . $_SESSION[ 'error_msg' ], __FUNCTION__, basename( __FILE__ ) );
		$g_Log->debug( "E : パスワードチェック処理 : " . $password, __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	$g_Log->debug( "E : パスワードチェック処理 : " . $password, __FUNCTION__, basename( __FILE__ ) );

	return true;
}

# ==========================================================
# 認証チェック処理
# ==========================================================
function checkAuthenticate( $p_userCd = null, $p_password = null ) {

	global $g_Log;
	$g_Log->debug( "S : 認証チェック処理 : {$p_userCd} : {$p_password} : {$_SESSION[ 'password_hash' ]}", __FUNCTION__, basename( __FILE__ ) );

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

		if( count( $dataTable ) == 0 ){
			$_SESSION[ 'error_msg' ] = 'ユーザコードまたはパスワードが正しくありません。';
			$g_Log->warning( "NOT FOUND : {$userCd} : {$passwordVerify} : {$_SESSION[ 'error_msg' ]}", __FUNCTION__, basename( __FILE__ ) );
			$g_Log->debug( "E : 認証チェック処理 : {$p_userCd} : {$p_password}", __FUNCTION__, basename( __FILE__ ) );
			return false;
		}

		foreach( $dataTable as $row ){
			$_SESSION[ 'user_id' ] = $row[ 'user_id' ];
			$_SESSION[ 'user_cd' ] = $row[ 'user_cd' ];
			$_SESSION[ 'username' ] = $row[ 'username' ];
			$_SESSION[ 'password_hash' ] = $row[ 'password' ];
			$_SESSION[ 'email' ] = $row[ 'email' ];
			$_SESSION[ 'role_level' ] = $row[ 'role_level' ];
			$_SESSION[ 'role_name' ] = $row[ 'role_name' ];
			$_SESSION[ 'signin_at' ] = $row[ 'signin_at' ];
			$_SESSION[ 'signin_by' ] = $row[ 'signin_by' ];
			$g_Log->debug( "SESSION[ 'user_id' ] : {$row[ 'user_id' ]} セットしました", __FUNCTION__, basename( __FILE__ ) );
			$g_Log->debug( "SESSION[ 'user_cd' ] : {$row[ 'user_cd' ]} セットしました", __FUNCTION__, basename( __FILE__ ) );
			$g_Log->debug( "SESSION[ 'username' ] : {$row[ 'username' ]} セットしました", __FUNCTION__, basename( __FILE__ ) );
			$g_Log->debug( "SESSION[ 'password_hash' ] : {$row[ 'password' ]} セットしました", __FUNCTION__, basename( __FILE__ ) );
			$g_Log->debug( "SESSION[ 'email' ] : {$row[ 'email' ]} セットしました", __FUNCTION__, basename( __FILE__ ) );
			$g_Log->debug( "SESSION[ 'role_name' ] : {$row[ 'role_name' ]} セットしました", __FUNCTION__, basename( __FILE__ ) );
			$g_Log->debug( "SESSION[ 'signin_at' ] : {$row[ 'signin_at' ]} セットしました", __FUNCTION__, basename( __FILE__ ) );
			$g_Log->debug( "SESSION[ 'signin_by' ] : {$row[ 'signin_by' ]} セットしました", __FUNCTION__, basename( __FILE__ ) );
			$g_Log->debug( "SESSION[ 'role_level' ] : {$row[ 'role_level' ]} セットしました", __FUNCTION__, basename( __FILE__ ) );
		}
	} catch ( Exception $e ) {
		$_SESSION[ 'error_msg' ] = "ユーザコードまたはパスワードが正しくありません。";
		$g_Log->warning( "SELECT ERROR : {$userCd} : {$passwordVerify} : {$_SESSION[ 'error_msg' ]} : " . $e->getMessage(), __FUNCTION__, basename( __FILE__ ) );
		$g_Log->debug( "E : 認証チェック処理 : {$p_userCd} : {$p_password}", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	try {
		// パスワードの検証
		if( ! password_verify( $password, $_SESSION[ 'password_hash' ] ) ) {
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
			"user_id" => $_SESSION[ 'user_id' ],
			"signin_by" => basename( __FILE__ )
		);

		$stmt = $g_DB->execute( $SQL, $SQL_Parameters );

		if( $stmt === false ){
			throw new PDOException( "SQLの実行に失敗しました。" );
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'error_msg' ] = "ユーザコードまたはパスワードが正しくありません。";
		$g_Log->warning( "UPDATE ERROR : {$userCd} : {$passwordHash} : {$_SESSION[ 'error_msg' ]} : " . $e->getMessage(), __FUNCTION__, basename( __FILE__ ) );
		$g_Log->debug( "E : 認証チェック処理 : {$p_userCd} : {$p_password}", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	$g_Log->debug( "E : 認証チェック処理 : {$p_userCd} : {$p_password}", __FUNCTION__, basename( __FILE__ ) );
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
	$g_Log->debug( "S : クライアントIP取得処理", __FUNCTION__, basename( __FILE__ ) );

	$ip = $_SERVER[ 'HTTP_X_REAL_IP' ] ?? $_SERVER[ 'REMOTE_ADDR' ] ?? '0.0.0.0';

	$g_Log->debug( "E : クライアントIP取得処理 : {$ip}", __FUNCTION__, basename( __FILE__ ) );

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
	$g_Log->debug( "S : レート制限チェック : maxRequests={$maxRequests} : windowSeconds={$windowSeconds}", __FUNCTION__, basename( __FILE__ ) );

	$now = microtime(true);

	# クライアントIP取得
	$ip = getClientIp();

	// IPごとにファイルを作成
	global $g_Log_Path;
	$file = $g_Log_Path . DIRECTORY_SEPARATOR . $p_FormName . '_form_' . md5( $ip ) . '.json'; 

	$g_Log->debug( "ファイル名 : {$file}", __FUNCTION__, basename( __FILE__ ) );

	// ファイルロックして処理(c+は存在しない場合は作成、存在する場合は読み書き両方可能)
	$fp = fopen( $file, 'c+' );
	if ( ! $fp ) {	//	ファイルを開けないときはリターン
		$g_Log->debug( "ファイルを開けません : {$file}", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 排他（同時投稿で壊れないように書込み用ロック）
	flock( $fp, LOCK_EX );

	$data = stream_get_contents( $fp );
	$timestamps = [];
	if ( $data ) {
		$decoded = json_decode( $data, true );
		if ( is_array( $decoded ) ) $timestamps = $decoded;
	}

	// window外を削除
	$timestamps = array_values( array_filter( $timestamps, fn( $t ) => ( $now - (float)$t ) < $windowSeconds ) );

	// 今回分を追加
	$timestamps[] = $now;

	if ( count( $timestamps ) > $maxRequests ) {
		// 保存してから返す（連打に強くする）
		ftruncate( $fp, 0 );	//	ファイルを空にする
		rewind( $fp );	//	ファイルポインタを先頭に戻す
		fwrite( $fp, json_encode( $timestamps ) );	//	更新保存
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
	fwrite( $fp, json_encode( $timestamps ) );	//	更新保存
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

	$v = trim( ( string )( $value ?? '' ) );
	
	if ( $v !== '' ) {
		$ip = getClientIp();
		$g_Log->warning( "ハニーポットが埋まっているためボットとみなします : IP={$ip}", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# CSRF検証
# @return boolean CSRFトークンが一致すればtrue、そうでなければfalse
# ==========================================================
function verifyCsrfOrFail( $post_token, $session_token ): bool {
	
	global $g_Log;

	$posted  = $post_token ?? '';
	$session = $session_token ?? '';

	// トークンがない／不一致なら拒否（hash_equals推奨例あり）
	if ( ! $posted || ! $session || ! hash_equals( $session, $posted )) {
		$ip = getClientIp();
		$g_Log->warning( "CSRFトークンが一致しないため不正リクエストとみなします : IP={$ip}", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}
	return true;
}

# ==========================================================
# 問合せ内容件数取得処理
# @return int 取得成功すれば件数、失敗すればfalse
# ==========================================================
function getInquiryCount(){

	global $g_Log;
	$g_Log->debug( "問合せ内容件数取得処理", __FUNCTION__, basename( __FILE__ ) );
	
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
			$g_Log->debug( "問合せ内容件数 : {$counter} 件", __FUNCTION__, basename( __FILE__ ) );
		}
	} catch ( Exception $e ) {
		$_SESSION[ 'RES_ERR_MSG' ] = '問合せ内容の取得に失敗しました';
		$g_Log->error( $_SESSION[ 'RES_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
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
	$g_Log->debug( "問合せ内容取得処理 : pageCurrent={$p_pageCurrent}, pageRows={$p_pageRows}", __FUNCTION__, basename( __FILE__ ) );

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
		$_SESSION[ 'RES_ERR_MSG' ] = '問合せ内容の取得に失敗しました';
		$g_Log->error( $_SESSION[ 'RES_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
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
	$g_Log->debug( "問合せ内容削除処理 : inquiryId={$p_inquiryId}", __FUNCTION__, basename( __FILE__ ) );

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
		$_SESSION[ 'RES_ERR_MSG' ] = '問合せ内容の削除に失敗しました';
		$g_Log->error( $_SESSION[ 'RES_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
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
	$g_Log->debug( "大会登録件数取得処理", __FUNCTION__, basename( __FILE__ ) );
	
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
			$g_Log->debug( "大会登録件数 : {$counter} 件", __FUNCTION__, basename( __FILE__ ) );
		}
	} catch ( Exception $e ) {
		$_SESSION[ 'RES_ERR_MSG' ] = '大会登録件数の取得に失敗しました';
		$g_Log->error( $_SESSION[ 'RES_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
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
	$g_Log->debug( "大会登録内容取得処理 : pageCurrent={$p_pageCurrent}, pageRows={$p_pageRows}", __FUNCTION__, basename( __FILE__ ) );

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
	$SQL .= "  tournament_id ";
	$SQL .= ", tournament_title ";
	$SQL .= ", tournament_text ";
	$SQL .= ", tournament_start_date ";
	$SQL .= ", tournament_end_date ";
	$SQL .= "FROM baseball_tournament ";
	$SQL .= "WHERE is_enabled = 1 ";
	$SQL .= "ORDER BY tournament_start_date DESC ";
	$SQL .= ", tournament_id DESC ";
	$SQL .= "LIMIT $pageRows OFFSET $pageOffset ";
	
	$SQL_Parameters = array(
	);

	$dataTable = array();

	try {
		global $g_DB;
		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );

	} catch ( Exception $e ) {
		$_SESSION[ 'RES_ERR_MSG' ] = '大会登録内容の取得に失敗しました';
		$g_Log->error( $_SESSION[ 'RES_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
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
	$g_Log->debug( "大会登録内容削除処理 : tournamentId={$p_tournamentId}", __FUNCTION__, basename( __FILE__ ) );

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
		$_SESSION[ 'RES_ERR_MSG' ] = '大会登録内容の削除に失敗しました';
		$g_Log->error( $_SESSION[ 'RES_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# リクエストチェック処理
# @param string $p_name チェック対象のリクエスト名
# @param string $p_request リクエスト方法 (POST/GET)
# @param boolean $p_checkEmpty 空文字も許容するかどうか (true:空文字もNG / false:空文字はOK)
# @return boolean チェック成功すればtrue、失敗すればfalse
# ==========================================================
function checkRequest( $p_name, $p_request = 'POST' ){

	global $g_Log;
	$g_Log->debug( "リクエストチェック処理 : name={$p_name}, request={$p_request}", __FUNCTION__, basename( __FILE__ ) );

	$request = null;
	switch ( strtoupper( $p_request ) ) {
		case 'POST':
			$request = $_POST;
			break;
		case 'GET':
			$request = $_GET;
			break;
		default:
			return false;
	}

	// リクエストチェック
	if( ! isset( $request[ $p_name ] ) ) {
		$g_Log->debug( "リクエスト[ {$p_request} : {$p_name} ]が未設定です", __FUNCTION__, basename( __FILE__ ) );
		return false;
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
	$g_Log->debug( "日付チェック処理 : value={$p_date}", __FUNCTION__, basename( __FILE__ ) );

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
# @return boolean チェック成功すればtrue、失敗すればfalse
# ==========================================================
function checkText( $p_text, $p_checkEmpty = true ){

	global $g_Log;
	$g_Log->debug( "テキストチェック処理 : value={$p_text}", __FUNCTION__, basename( __FILE__ ) );


	if ( $p_text === null ) {
		$g_Log->debug( "IS NULL : value={$p_text}", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 前後空白を除去
	$text = trim( $p_text );

	// 空文字チェック
	if( $p_checkEmpty ){
		if ( $text === '' ) {
			$g_Log->debug( "IS EMPTY : value={$p_text}", __FUNCTION__, basename( __FILE__ ) );
			return false;
		}
	}

	return $text;
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
	$g_Log->debug( "チーム登録内容取得処理 : tournamentId={$p_tournamentId}, pageCurrent={$p_pageCurrent}, pageRows={$p_pageRows}", __FUNCTION__, basename( __FILE__ ) );

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
		$g_Log->debug( "大会IDが数値ではありません : tournamentId={$p_tournamentId}", __FUNCTION__, basename( __FILE__ ) );
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
	$SQL .= ", team_access_cd ";
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
		$_SESSION[ 'RES_ERR_MSG' ] = 'チーム登録内容の取得に失敗しました';
		$g_Log->error( $_SESSION[ 'RES_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
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
	$g_Log->debug( "チーム登録内容削除処理 : teamId={$p_teamId}", __FUNCTION__, basename( __FILE__ ) );

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
		$_SESSION[ 'RES_ERR_MSG' ] = 'チーム登録内容の削除に失敗しました';
		$g_Log->error( $_SESSION[ 'RES_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
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
	$g_Log->debug( "試合登録内容取得処理 : tournamentId={$p_tournamentId}, pageCurrent={$p_pageCurrent}, pageRows={$p_pageRows}", __FUNCTION__, basename( __FILE__ ) );

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
		$g_Log->debug( "大会IDが数値ではありません : tournamentId={$p_tournamentId}", __FUNCTION__, basename( __FILE__ ) );
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
		$_SESSION[ 'RES_ERR_MSG' ] = '試合登録内容の取得に失敗しました';
		$g_Log->error( $_SESSION[ 'RES_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
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
	$g_Log->debug( "試合登録内容削除処理 : gameId={$p_gameId}", __FUNCTION__, basename( __FILE__ ) );

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
		$_SESSION[ 'RES_ERR_MSG' ] = '試合登録内容の削除に失敗しました';
		$g_Log->error( $_SESSION[ 'RES_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
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
	$g_Log->debug( "試合登録内容更新処理 : gameId={$p_gameId}", __FUNCTION__, basename( __FILE__ ) );

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
		$_SESSION[ 'RES_ERR_MSG' ] = '試合登録内容の更新に失敗しました';
		$g_Log->error( $_SESSION[ 'RES_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
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
	$g_Log->debug( "試合未登録チーム内容取得処理 : tournamentId={$p_tournamentId}", __FUNCTION__, basename( __FILE__ ) );
	
	// 大会IDチェック
	if( ! is_numeric( $p_tournamentId ) ){
		$g_Log->debug( "大会IDが数値ではありません : tournamentId={$p_tournamentId}", __FUNCTION__, basename( __FILE__ ) );
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
		$_SESSION[ 'RES_ERR_MSG' ] = 'チーム登録内容の取得に失敗しました';
		$g_Log->error( $_SESSION[ 'RES_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
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
	$g_Log->debug( "試合結果登録内容取得処理 : tournamentId={$p_tournamentId}, accessCd={$p_accessCd}, pageCurrent={$p_pageCurrent}, pageRows={$p_pageRows}", __FUNCTION__, basename( __FILE__ ) );

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
		$g_Log->debug( "大会IDが数値ではありません : tournamentId={$p_tournamentId}", __FUNCTION__, basename( __FILE__ ) );
		return array();
	}
	
	// アクセスコードチェック
	if( ! checkText( $p_accessCd, true ) ){
		$g_Log->debug( "アクセスコードが不正です : accessCd={$p_accessCd}", __FUNCTION__, basename( __FILE__ ) );
		return array();
	}

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
	$SQL .= "  AND t.team_access_cd = :access_cd ";
	$SQL .= "ORDER BY g.game_class ASC ";
	$SQL .= ", g.game_block ASC ";
	$SQL .= ", g.game_count ASC ";
	$SQL .= ", g.game_id ASC ";
	$SQL .= "LIMIT $pageRows OFFSET $pageOffset ";

	$SQL_Parameters = array(
		'tournament_id' => $tournamentId,
		'access_cd' => $accessCd,
	);

	$dataTable = array();

	try {
		global $g_DB;
		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );

	} catch ( Exception $e ) {
		$_SESSION[ 'RES_ERR_MSG' ] = '試合結果登録内容の取得に失敗しました';
		$g_Log->error( $_SESSION[ 'RES_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
	}

	return $dataTable;
}
