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
		'FROM_VIEW_STATE'    => 0,		// 表示ステータス(0: 初期画面)
		'FORM_ERR_MSG'       => '',		// エラーメッセージ
		'FORM_PAGE_ROWS'     => 10,		// 表示行数
		'FORM_PAGE_CURRENT'  => 1,		// ページ番号
		'FORM_PAGE_OFFSET'   => 0,		// 表示オフセット
		'FORM_PAGE_TOTAL'    => 0,		// 総ページ数
		'FORM_PAGE_TOP'      => 1,		// ページの最初
		'FORM_PAGE_LAST'     => 1,		// ページの最後
		'FORM_PAGE_PREVIOUS' => 1,		// 前のページ
		'FORM_PAGE_NEXT'     => 1,		// 次のページ
		'FORM_BUTTON_ADD'    => '追加',	// 追加ボタン
		'FORM_BUTTON_UPDATE' => '更新',	// 更新ボタン
		'FORM_BUTTON_DELETE' => '削除',	// 削除ボタン
		'RES_USER_ID'       => '',		// ユーザーID
		'RES_USER_CD'       => '',		// ユーザーCD
		'RES_USERNAME'      => '',		// ユーザー名
		'RES_EMAIL'         => '',		// メールアドレス
		'RES_ROLE_LEVEL'    => '',		// ユーザー権限
		'RES_SIGNIN_AT'     => '',		// 最終サインイン日時
		'RES_IS_ENABLED'    => '',		// 有効フラグ
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

	// 更新ボタンの取得
	if( isset( $_POST[ 'update_button' ] ) ) {
		$g_Log->notice( "POST : update_button = " . $_POST[ 'update_button' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// 追加ボタンの取得
	if( isset( $_POST[ 'add_button' ] ) ) {
		$g_Log->notice( "POST : add_button = " . $_POST[ 'add_button' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// 削除ボタンの取得
	if( isset( $_POST[ 'delete_button' ] ) ) {
		$g_Log->notice( "POST : delete_button = " . $_POST[ 'delete_button' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// ユーザーIDの取得
	if( isset( $_POST[ 'user_id' ] ) ) {
		$g_Log->notice( "POST : user_id = " . $_POST[ 'user_id' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// ユーザーCDの取得
	if( isset( $_POST[ 'user_cd' ] ) ) {
		$g_Log->notice( "POST : user_cd = " . $_POST[ 'user_cd' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// ユーザー名の取得
	if( isset( $_POST[ 'username' ] ) ) {
		$g_Log->notice( "POST : username = " . $_POST[ 'username' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// メールアドレスの取得
	if( isset( $_POST[ 'email' ] ) ) {
		$g_Log->notice( "POST : email = " . $_POST[ 'email' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// ユーザー権限の取得
	if( isset( $_POST[ 'role_level' ] ) ) {
		$g_Log->notice( "POST : role_level = " . $_POST[ 'role_level' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// 有効フラグの取得
	if( isset( $_POST[ 'is_enabled' ] ) ) {
		$g_Log->notice( "POST : is_enabled = " . $_POST[ 'is_enabled' ], __FUNCTION__, basename( __FILE__ ) );
	}

	return true;
}

# ==========================================================
# チェック処理
# ==========================================================
function checkProc() {

	global $g_Log;
	$g_Log->notice( "チェック処理", __FUNCTION__, basename( __FILE__ ) );

	// ページネーションのチェック
	if( ! checkPageNation() ){
		$_SESSION[ 'FORM_ERR_MSG' ] = "ページネーションエラー";
		$g_Log->notice( "ページネーションのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 追加チェック
	if( ! checkAdd() ) {
		$g_Log->notice( "追加チェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 更新チェック
	if( ! checkUpdate() ) {
		$g_Log->notice( "更新チェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 削除チェック
	if( ! checkDelete() ) {
		$g_Log->notice( "削除チェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}


	return true;
}

# ==========================================================
# ページネーションチェック処理
# ==========================================================
function checkPageNation() {

	global $g_Log;
	$g_Log->notice( "ページネーションチェック処理", __FUNCTION__, basename( __FILE__ ) );

	// 表示行数
	$pageRows	  = ( int )( $_POST[ 'page_rows' ] ?? $_SESSION[ 'FORM_PAGE_ROWS' ] ?? 2 );
	$_SESSION[ 'FORM_PAGE_ROWS' ]     = $pageRows;
	$g_Log->notice( "[ FORM_PAGE_ROWS ] : $pageRows => " . $_SESSION[ 'FORM_PAGE_ROWS' ], __FUNCTION__, basename( __FILE__ ) );

	// 総件数
	$recordsCount = getUserCount();
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
# 大会登録件数取得処理
# @return int 取得成功すれば件数、失敗すればfalse
# ==========================================================
function getUserCount() {

	global $g_Log;
	$g_Log->notice( "ユーザー登録件数取得処理", __FUNCTION__, basename( __FILE__ ) );
	
	// SQL文作成
	$SQL = "";
	$SQL .= "SELECT ";
	$SQL .= "  count( * ) AS counter ";
	$SQL .= "FROM baseball_user ";
	
	$SQL_Parameters = array(
	);

	$counter = 0;

	try {
		global $g_DB;
		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );

		foreach( $dataTable as $row ){
			$counter = $row[ 'counter' ];
			$g_Log->notice( "ユーザー登録件数 : {$counter} 件", __FUNCTION__, basename( __FILE__ ) );
		}
	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = 'ユーザー登録件数の取得に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return $counter;
}

# ==========================================================
# 追加チェック処理
# ==========================================================
function checkAdd() {

	global $g_Log;
	$g_Log->notice( "追加チェック処理", __FUNCTION__, basename( __FILE__ ) );

	// 追加ボタンでサブミットされてなければ処理をスキップ
	if( ! checkRequest( 'add_button', 'POST' ) ) {
		return true;
	}

	if( ! checkForm() ) {
		$g_Log->notice( "フォームのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# 更新チェック処理
# ==========================================================
function checkUpdate() {

	global $g_Log;
	$g_Log->notice( "更新チェック処理", __FUNCTION__, basename( __FILE__ ) );

	// 更新ボタンでサブミットされてなければ処理をスキップ
	if( ! checkRequest( 'update_button', 'POST' ) ) {
		return true;
	}

	if( ! checkForm() ) {
		$g_Log->notice( "フォームのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# 削除チェック処理
# ==========================================================
function checkDelete() {

	global $g_Log;
	$g_Log->notice( "削除チェック処理", __FUNCTION__, basename( __FILE__ ) );
	
	// 削除ボタンでサブミットされてなければ処理をスキップ
	if( ! checkRequest( 'delete_button', 'POST' ) ) {
		return true;
	}

	// 削除対象のID
	if ( deleteUserId( $_POST[ 'delete_button' ] ) ) {
		$g_Log->notice( "試合登録内容を削除しました： delete_button = " . $_POST[ 'delete_button' ], __FUNCTION__, basename( __FILE__ ) );

	} else {
		$g_Log->notice( "試合登録内容の削除に失敗しました： delete_button = " . $_POST[ 'delete_button' ], __FUNCTION__, basename( __FILE__ ) );
		$_SESSION[ 'FORM_ERR_MSG' ] = "試合登録内容の削除に失敗しました。";
		return false;
	}

	return true;
}

# ==========================================================
# フォームチェック処理
# ==========================================================
function checkForm(){
	
	global $g_Log;
	$g_Log->notice( "フォームチェック処理", __FUNCTION__, basename( __FILE__ ) );

	// ユーザーID
	if( ! checkRequest( 'user_id', 'POST' ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = "ユーザーIDエラー";
		$g_Log->notice( "ユーザーIDのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// ユーザーコード
	if( ! checkRequest( 'user_cd', 'POST' ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = "ユーザーコードエラー";
		$g_Log->notice( "ユーザーコードのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	if( ! checkText( $_POST[ 'user_cd' ] ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = "ユーザーコードエラー";
		$g_Log->notice( "ユーザーコードのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 権限レベル
	if( ! checkRequest( 'role_level', 'POST' ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = "ユーザー権限エラー";
		$g_Log->notice( "ユーザー権限のチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 有効フラグ
	if( ! checkRequest( 'is_enabled', 'POST' ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = "有効フラグエラー";
		$g_Log->notice( "有効フラグのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// チームID(更新時のみチェック)
	if( ! checkRequest( 'update_button', 'POST' ) ) {
		if( ! checkRequest( 'game_id', 'POST' ) ) {
			$_SESSION[ 'FORM_ERR_MSG' ] = "試合IDエラー";
			$g_Log->notice( "試合IDのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
			return false;
		}
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
	$g_Log->notice( "[ FROM_VIEW_STATE ] : " . $_SESSION[ 'FROM_VIEW_STATE' ], __FUNCTION__, basename( __FILE__ ) );

	// 更新処理
	if( isset( $_POST[ 'update_button' ] ) && $_POST[ 'update_button' ] !== "" ) {
		if ( updateUser() ) {
			$g_Log->notice( "ユーザ登録内容を更新しました： user_id = " . $_POST[ 'user_id' ], __FUNCTION__, basename( __FILE__ ) );
		} else {
			$g_Log->notice( "ユーザ登録内容の更新に失敗しました： user_id = " . $_POST[ 'user_id' ], __FUNCTION__, basename( __FILE__ ) );
			$_SESSION[ 'FORM_ERR_MSG' ] = "ユーザ登録内容の更新に失敗しました。";
			return false;
		}
	}

	return true;
}

# ==========================================================
# チーム登録内容更新処理
# @param int $p_teamId 更新対象のチームID
# @return boolean 更新成功すればtrue、失敗すればfalse
# ==========================================================
function updateUser(){

	global $g_Log;
	$g_Log->notice( "ユーザ登録内容更新処理", __FUNCTION__, basename( __FILE__ ) );

	$user_id    = checkText( $_POST[ 'user_id' ] );
	$username   = checkText( $_POST[ 'username' ] );
	$role_level = checkText( $_POST[ 'role_level' ] );
	$is_enabled = checkText( $_POST[ 'is_enabled' ] );
	
	// SQL文作成
	$SQL = "";
	$SQL .= " UPDATE baseball_user ";
	$SQL .= " SET username   = :username ";
	$SQL .= "   , role_level = :role_level ";
	$SQL .= "   , is_enabled = :is_enabled ";
	$SQL .= "   , updated_at = CURRENT_TIMESTAMP ";
	$SQL .= "   , updated_by = :updated_by ";
	$SQL .= " WHERE user_id = :user_id ";
	
	$SQL_Parameters = array(
		'user_id'    => $user_id,
		'username'   => $username,
		'role_level' => $role_level,
		'is_enabled' => $is_enabled,
		'updated_by' => basename( __FILE__ ),
	);

	try {
		global $g_DB;
		$stmt = $g_DB->execute( $SQL, $SQL_Parameters );

		if( $stmt === false ){
			throw new PDOException( "SQLの実行に失敗しました。" );
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = 'ユーザ登録内容の更新に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# ユーザ登録内容削除処理
# @param int $p_userId 削除対象のユーザID
# @return boolean 削除成功すればtrue、失敗すればfalse
# ==========================================================
function deleteUserId( $p_userId ){

	global $g_Log;
	$g_Log->notice( "ユーザ登録内容削除処理 : userId={$p_userId}", __FUNCTION__, basename( __FILE__ ) );

	$userId = 0;

	if( is_numeric( $p_userId ) ){
		$userId = (int)$p_userId;
	} else {
		return false;
	}
	
	// SQL文作成
	$SQL = "";
	$SQL .= " UPDATE baseball_user ";
	$SQL .= " SET is_enabled = 0 ";
	$SQL .= " WHERE is_enabled = 1 ";
	$SQL .= "   AND user_id = :user_id ";
	
	$SQL_Parameters = array(
		'user_id' => $userId,
	);

	try {
		global $g_DB;
		$stmt = $g_DB->execute( $SQL, $SQL_Parameters );

		if( $stmt === false ){
			throw new PDOException( "SQLの実行に失敗しました。" );
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = 'ユーザ登録内容の削除に失敗しました';
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
function getRoleList(){

	global $g_Log;
	$g_Log->notice( "ユーザ権限内容取得処理", __FUNCTION__, basename( __FILE__ ) );

	// SQL文作成
	$SQL = "";
	$SQL .= " SELECT ";
	$SQL .= "   role_id ";
	$SQL .= " , role_name ";
	$SQL .= " , role_level ";
	$SQL .= "FROM baseball_role ";
	$SQL .= "WHERE is_enabled = 1 ";
	$SQL .= "AND role_level <= :role_level ";
	$SQL .= "ORDER BY role_level ASC ";

	$SQL_Parameters = array(
		"role_level" => $_SESSION[ 'AUTH_ROLE_LEVEL' ] ?? 0,
	);

	$dataTable = array();

	try {
		global $g_DB;
		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = 'ユーザ権限内容の取得に失敗しました';
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
function getUserList( $p_pageCurrent = 1, $p_pageRows = 10 ){

	global $g_Log;
	$g_Log->notice( "ユーザ登録内容取得処理 : pageCurrent={$p_pageCurrent}, pageRows={$p_pageRows}", __FUNCTION__, basename( __FILE__ ) );

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

	$role_level = ( int )( $_SESSION[ 'AUTH_ROLE_LEVEL' ] ?? 0 );

	// SQL文作成
	$SQL = "";
	$SQL .= " SELECT ";
	$SQL .= "   u.user_id ";
	$SQL .= " , u.user_cd ";
	$SQL .= " , u.password ";
	$SQL .= " , u.username ";
	$SQL .= " , u.email ";
	$SQL .= " , u.role_level ";
	$SQL .= " , u.verification_cd ";
	$SQL .= " , u.verification_period ";
	$SQL .= " , u.signin_at ";
	$SQL .= " , u.signin_by ";
	$SQL .= " , u.is_enabled ";
	$SQL .= " , r.role_id ";
	$SQL .= " , r.role_name ";
	$SQL .= "FROM baseball_user AS u ";
	$SQL .= "INNER JOIN baseball_role r ON r.role_level = u.role_level ";
	$SQL .= "WHERE r.is_enabled = 1 ";
	$SQL .= "  AND u.role_level <= :role_level ";
	$SQL .= "ORDER BY u.role_level DESC ";
	$SQL .= ", u.user_cd ASC ";
	$SQL .= "LIMIT $pageRows OFFSET $pageOffset ";

	$SQL_Parameters = array(
		"role_level" => $_SESSION[ 'AUTH_ROLE_LEVEL' ] ?? 0,
	);

	$dataTable = array();

	try {
		global $g_DB;
		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = 'ユーザ登録内容の取得に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
	}

	return $dataTable;
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
	// ユーザ一覧の読み込み
	$filename = __DIR__ . DIRECTORY_SEPARATOR . "article_userlist.php";
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
