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
		'RES_ADD_BUTTON'    => '追加',	// 追加ボタン
		'RES_UPDATE_BUTTON' => '更新',	// 更新ボタン
		'RES_ID'            => '',		// ID
		'RES_NAME1'         => '',		// 名前1
		'RES_NAME2'         => '',		// 名前2
		'RES_DATE_START'    => '',		// 開始日
		'RES_DATE_END'      => '',		// 終了日
		'RES_TOURNAMENT1_TEAMS' => '',	// 大会1チーム数
		'RES_TOURNAMENT1_TEXT'  => '',	// 大会1テキスト
		'RES_TOURNAMENT2_TEAMS' => '',	// 大会2チーム数
		'RES_TOURNAMENT2_TEXT'  => '',	// 大会2テキスト
		'RES_TOURNAMENT3_TEAMS' => '',	// 大会3チーム数
		'RES_TOURNAMENT3_TEXT'  => '',	// 大会3テキスト
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

	// 大会IDの取得
	if( isset( $_POST[ 'tournament_id' ] ) ) {
		$g_Log->notice( "POST : tournament_id = " . $_POST[ 'tournament_id' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// 大会開始日の取得
	if( isset( $_POST[ 'tournament_startDate' ] ) ) {
		$g_Log->notice( "POST : tournament_startDate = " . $_POST[ 'tournament_startDate' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// 大会終了日の取得
	if( isset( $_POST[ 'tournament_endDate' ] ) ) {
		$g_Log->notice( "POST : tournament_endDate = " . $_POST[ 'tournament_endDate' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// 大会名１の取得
	if( isset( $_POST[ 'tournament_title' ] ) ) {
		$g_Log->notice( "POST : tournament_title = " . $_POST[ 'tournament_title' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// 大会名２の取得
	if( isset( $_POST[ 'tournament_text' ] ) ) {
		$g_Log->notice( "POST : tournament_text = " . $_POST[ 'tournament_text' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// 大会1チーム数の取得
	if( isset( $_POST[ 'tournament1_teams' ] ) ) {
		$g_Log->notice( "POST : tournament1_teams = " . $_POST[ 'tournament1_teams' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// 大会1テキストの取得
	if( isset( $_POST[ 'tournament1_text' ] ) ) {
		$g_Log->notice( "POST : tournament1_text = " . $_POST[ 'tournament1_text' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// 大会2チーム数の取得
	if( isset( $_POST[ 'tournament2_teams' ] ) ) {
		$g_Log->notice( "POST : tournament2_teams = " . $_POST[ 'tournament2_teams' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// 大会2テキストの取得
	if( isset( $_POST[ 'tournament2_text' ] ) ) {
		$g_Log->notice( "POST : tournament2_text = " . $_POST[ 'tournament2_text' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// 大会3チーム数の取得
	if( isset( $_POST[ 'tournament3_teams' ] ) ) {
		$g_Log->notice( "POST : tournament3_teams = " . $_POST[ 'tournament3_teams' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// 大会3テキストの取得
	if( isset( $_POST[ 'tournament3_text' ] ) ) {
		$g_Log->notice( "POST : tournament3_text = " . $_POST[ 'tournament3_text' ], __FUNCTION__, basename( __FILE__ ) );
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
	$recordsCount = getTournamentCount();
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

	if( ! checkRequest( 'tournament_id', 'POST' ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = "大会IDエラー";
		$g_Log->notice( "大会IDのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
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

	$g_Log->notice( "POST : delete_button = " . $_POST[ 'delete_button' ], __FUNCTION__, basename( __FILE__ ) );

	// 削除対象のID
	if ( deleteTournamentId( $_POST[ 'delete_button' ] ) ) {
		$g_Log->notice( "大会登録内容を削除しました： delete_button = " . $_POST[ 'delete_button' ], __FUNCTION__, basename( __FILE__ ) );
	} else {
		$_SESSION[ 'FORM_ERR_MSG' ] = "大会登録内容の削除に失敗しました。";
		$g_Log->notice( "{$_SESSION[ 'FORM_ERR_MSG' ]} ： delete_button = " . $_POST[ 'delete_button' ], __FUNCTION__, basename( __FILE__ ) );
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

	// 大会開始日
	if( checkRequest( 'tournament_startDate', 'POST' ) ) {
		if( ! isValidDate( $_POST[ 'tournament_startDate' ] ) ) {
			$_SESSION[ 'FORM_ERR_MSG' ] = "大会開始日エラー";
			$g_Log->notice( "大会開始日のチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
			return false;
		}
	}

	// 大会終了日
	if( checkRequest( 'tournament_endDate', 'POST' ) ) {
		if( ! isValidDate( $_POST[ 'tournament_endDate' ] ) ) {
			$_SESSION[ 'FORM_ERR_MSG' ] = "大会終了日エラー";
			$g_Log->notice( "大会終了日のチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
			return false;
		}
	}

	// 大会名１
	if( checkRequest( 'tournament_title', 'POST' ) ) {
		if( ! checkText( $_POST[ 'tournament_title' ] ) ) {
			$_SESSION[ 'FORM_ERR_MSG' ] = "大会名１エラー";
			$g_Log->notice( "大会名１のチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
			return false;
		}
	}

	// 大会名２
	if( checkRequest( 'tournament_text', 'POST' ) ) {
		if( ! checkText( $_POST[ 'tournament_text' ] ) ) {
			$_SESSION[ 'FORM_ERR_MSG' ] = "大会名２エラー";
			$g_Log->notice( "大会名２のチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
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
		if ( updateTournament( $_POST[ 'tournament_id' ], $_POST[ 'tournament_startDate' ], $_POST[ 'tournament_endDate' ], $_POST[ 'tournament_title' ], $_POST[ 'tournament_text' ] ) ) {
			$g_Log->notice( "大会登録内容を更新しました： tournament_id = " . $_POST[ 'tournament_id' ], __FUNCTION__, basename( __FILE__ ) );
		} else {
			$g_Log->notice( "大会登録内容の更新に失敗しました： tournament_id = " . $_POST[ 'tournament_id' ], __FUNCTION__, basename( __FILE__ ) );
			$_SESSION[ 'FORM_ERR_MSG' ] = "大会登録内容の更新に失敗しました。";
			return false;
		}
	}

	// 追加処理
	if( isset( $_POST[ 'add_button' ] ) && $_POST[ 'add_button' ] !== "" ) {
		if ( addTournament() ) {
			$g_Log->notice( "大会登録内容を追加しました", __FUNCTION__, basename( __FILE__ ) );
		} else {
			$g_Log->notice( "大会登録内容の追加に失敗しました", __FUNCTION__, basename( __FILE__ ) );
			$_SESSION[ 'FORM_ERR_MSG' ] = "大会登録内容の追加に失敗しました。";
			return false;
		}
	}

	return true;
}

# ==========================================================
# 大会登録内容追加処理
# @param int $p_tournamentId 更新対象の大会ID
# @return boolean 更新成功すればtrue、失敗すればfalse
# ==========================================================
function addTournament(){

	global $g_Log;
	$g_Log->notice( "大会登録内容追加処理", __FUNCTION__, basename( __FILE__ ) );

	$startDate = isValidDate( $_POST[ 'tournament_startDate' ] );
	$endDate = isValidDate( $_POST[ 'tournament_endDate' ] );
	$tournamentTitle = trim( $_POST[ 'tournament_title' ] );
	$tournamentText = trim( $_POST[ 'tournament_text' ] );
	$tournament1Teams = ( int )$_POST[ 'tournament1_teams' ];
	$tournament1Text = trim( $_POST[ 'tournament1_text' ] );
	$tournament2Teams = ( int )$_POST[ 'tournament2_teams' ];
	$tournament2Text = trim( $_POST[ 'tournament2_text' ] );
	$tournament3Teams = ( int )$_POST[ 'tournament3_teams' ];
	$tournament3Text = trim( $_POST[ 'tournament3_text' ] );
	$attachmentId = uploadAttachement( 'file_name' );

	// SQL文作成
	$SQL = "";
	$SQL .= " INSERT INTO baseball_tournament ";
	$SQL .= " ( tournament_title ";
	$SQL .= " , tournament_text ";
	$SQL .= " , tournament_start_date ";
	$SQL .= " , tournament_end_date ";
	$SQL .= " , tournament1_teams ";
	$SQL .= " , tournament1_text ";
	$SQL .= " , tournament2_teams ";
	$SQL .= " , tournament2_text ";
	$SQL .= " , tournament3_teams ";
	$SQL .= " , tournament3_text ";
	$SQL .= " , tournament_attachment_id ";
	$SQL .= " , is_enabled ";
	$SQL .= " , created_at ";
	$SQL .= " , created_by ";
	$SQL .= " , updated_at ";
	$SQL .= " , updated_by ";
	$SQL .= " ) ";
	$SQL .= " VALUES ";
	$SQL .= " ( :tournament_title ";
	$SQL .= " , :tournament_text ";
	$SQL .= " , :tournament_start_date ";
	$SQL .= " , :tournament_end_date ";
	$SQL .= " , :tournament1_teams ";
	$SQL .= " , :tournament1_text ";
	$SQL .= " , :tournament2_teams ";
	$SQL .= " , :tournament2_text ";
	$SQL .= " , :tournament3_teams ";
	$SQL .= " , :tournament3_text ";
	$SQL .= " , :tournament_attachment_id ";
	$SQL .= " , 1 ";
	$SQL .= " , CURRENT_TIMESTAMP ";
	$SQL .= " , :created_by ";
	$SQL .= " , CURRENT_TIMESTAMP ";
	$SQL .= " , :updated_by ";
	$SQL .= " ) ";
	
	$SQL_Parameters = array(
		'tournament_title' => $tournamentTitle,
		'tournament_text' => $tournamentText,
		'tournament_start_date' => $startDate,
		'tournament_end_date' => $endDate,
		'created_by' => basename( __FILE__ ),
		'updated_by' => basename( __FILE__ ),
		'tournament1_teams' => $tournament1Teams,
		'tournament1_text' => $tournament1Text,
		'tournament2_teams' => $tournament2Teams,
		'tournament2_text' => $tournament2Text,
		'tournament3_teams' => $tournament3Teams,
		'tournament3_text' => $tournament3Text,
		'tournament_attachment_id' => $attachmentId,
	);

	try {
		global $g_DB;
		$stmt = $g_DB->execute( $SQL, $SQL_Parameters );

		if( $stmt === false ){
			throw new PDOException( "SQLの実行に失敗しました。" );
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '大会登録内容の追加に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# 大会登録内容更新処理
# @param int $p_tournamentId 更新対象の大会ID
# @return boolean 更新成功すればtrue、失敗すればfalse
# ==========================================================
function updateTournament(){

	global $g_Log;
	$g_Log->notice( "大会登録内容更新処理", __FUNCTION__, basename( __FILE__ ) );

	$tournamentId = ( int )$_POST[ 'tournament_id' ];
	$tournamentTitle = trim( $_POST[ 'tournament_title' ] );
	$tournamentText = trim( $_POST[ 'tournament_text' ] );
	$startDate = isValidDate( $_POST[ 'tournament_startDate' ] );
	$endDate = isValidDate( $_POST[ 'tournament_endDate' ] );
	$tournament1Teams = ( int )$_POST[ 'tournament1_teams' ];
	$tournament1Text = trim( $_POST[ 'tournament1_text' ] );
	$tournament2Teams = ( int )$_POST[ 'tournament2_teams' ];
	$tournament2Text = trim( $_POST[ 'tournament2_text' ] );
	$tournament3Teams = ( int )$_POST[ 'tournament3_teams' ];
	$tournament3Text = trim( $_POST[ 'tournament3_text' ] );
	$attachmentId = uploadAttachement( 'file_name', 1 );
	if ( $attachmentId == 0 ) {
		if( isset( $_POST[ 'tournament_attachment_id' ] ) ) {
			$attachmentId = ( int )$_POST[ 'tournament_attachment_id' ];
		}
	}

	// SQL文作成
	$SQL = "";
	$SQL .= " UPDATE baseball_tournament ";
	$SQL .= " SET tournament_title = :tournament_title ";
	$SQL .= "   , tournament_text = :tournament_text ";
	$SQL .= "   , tournament_start_date = :tournament_start_date ";
	$SQL .= "   , tournament_end_date = :tournament_end_date ";
	$SQL .= "   , tournament1_teams = :tournament1_teams ";
	$SQL .= "   , tournament1_text = :tournament1_text ";
	$SQL .= "   , tournament2_teams = :tournament2_teams ";
	$SQL .= "   , tournament2_text = :tournament2_text ";
	$SQL .= "   , tournament3_teams = :tournament3_teams ";
	$SQL .= "   , tournament3_text = :tournament3_text ";
	$SQL .= "   , tournament_attachment_id = :tournament_attachment_id ";
	$SQL .= "   , updated_at = CURRENT_TIMESTAMP ";
	$SQL .= "   , updated_by = :updated_by ";
	$SQL .= "WHERE is_enabled = 1 ";
	$SQL .= "  AND tournament_id = :tournament_id ";
	
	$SQL_Parameters = array(
		'tournament_title' => $tournamentTitle,
		'tournament_text' => $tournamentText,
		'tournament_start_date' => $startDate,
		'tournament_end_date' => $endDate,
		'tournament1_teams' => $tournament1Teams,
		'tournament1_text' => $tournament1Text,
		'tournament2_teams' => $tournament2Teams,
		'tournament2_text' => $tournament2Text,
		'tournament3_teams' => $tournament3Teams,
		'tournament3_text' => $tournament3Text,
		'tournament_attachment_id' => $attachmentId,
		'updated_by' => basename( __FILE__ ),
		'tournament_id' => $tournamentId,
	);

	try {
		global $g_DB;
		$stmt = $g_DB->execute( $SQL, $SQL_Parameters );

		if( $stmt === false ){
			throw new PDOException( "SQLの実行に失敗しました。" );
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '大会登録内容の更新に失敗しました';
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
	// トーナメントメンテナンスの読み込み
	$filename = __DIR__ . DIRECTORY_SEPARATOR . "article_tournamentmaintenance.php";
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
