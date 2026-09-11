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
		'FORM_BUTTON_SEARCH' => '検索',	// 検索ボタン
		'FROM_TOURNAMENT_ID' => '',		// 大会ID
		'RES_ACCESS_CODE'   => '',		// アクセスコード
		'RES_TEAM2_ID'       => '',		// チームID2
		'RES_TEAM1_NAME'     => '',		// チーム名1
		'RES_TEAM2_NAME'     => '',		// チーム名2
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

		$FirstFlag = false;

		foreach ( getTournamentList() as $tournament ){
			if ( ! $FirstFlag ) {
				$_SESSION[ 'FROM_TOURNAMENT_ID' ] = $tournament[ 'tournament_id' ];
				$g_Log->notice( "[ FROM_TOURNAMENT_ID ] : " . $_SESSION[ 'FROM_TOURNAMENT_ID' ], __FUNCTION__, basename( __FILE__ ) );
				$FirstFlag = true;
			}
		}

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

	// 検索ボタンの取得
	if( isset( $_POST[ 'search_button' ] ) ) {
		$g_Log->notice( "POST : search_button = " . $_POST[ 'search_button' ], __FUNCTION__, basename( __FILE__ ) );
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

	// 大会IDの取得
	if( isset( $_POST[ 'tournament_id' ] ) ) {
		$g_Log->notice( "POST : tournament_id = " . $_POST[ 'tournament_id' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// アクセスコードの取得
	if( isset( $_POST[ 'access_code' ] ) ) {
		$g_Log->notice( "POST : access_code = " . $_POST[ 'access_code' ], __FUNCTION__, basename( __FILE__ ) );
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

	// 大会ID(SELECT)
	if( ! checkRequest( 'tournament_id', 'POST' ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = "大会IDエラー";
		$g_Log->notice( "大会IDの取得に失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}
	$_SESSION[ 'FROM_TOURNAMENT_ID' ] = $_POST[ 'tournament_id' ];
	$g_Log->notice( "[ FROM_TOURNAMENT_ID ] : " . $_SESSION[ 'FROM_TOURNAMENT_ID' ], __FUNCTION__, basename( __FILE__ ) );
	
	// アクセスコードの取得
###	if( ! checkRequest( 'access_code', 'POST' ) ) {
###		$_SESSION[ 'FORM_ERR_MSG' ] = "アクセスコードエラー";
###		$g_Log->notice( "アクセスコードの取得に失敗しました", __FUNCTION__, basename( __FILE__ ) );
###		return false;
###	}
	$_SESSION[ 'RES_ACCESS_CODE' ] = $_POST[ 'access_code' ];
	$g_Log->notice( "[ RES_ACCESS_CODE ] : " . $_SESSION[ 'RES_ACCESS_CODE' ], __FUNCTION__, basename( __FILE__ ) );

	// 参照チェック
	if( ! checkSearch() ) {
		$g_Log->notice( "参照チェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
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
# 参照チェック処理
# ==========================================================
function checkSearch() {

	global $g_Log;
	$g_Log->notice( "参照チェック処理", __FUNCTION__, basename( __FILE__ ) );

	// 検索ボタンでサブミットされてなければ処理をスキップ
	if( ! checkRequest( 'search_button', 'POST' ) ) {
		return true;
	}

	if( ! checkForm() ) {
		$g_Log->notice( "フォームのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

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
	if ( deleteGameId( $_POST[ 'delete_button' ] ) ) {
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

	// アクセスコード
###	if( ! checkRequest( 'access_code', 'POST' ) ) {
###		$_SESSION[ 'FORM_ERR_MSG' ] = "アクセスコードエラー";
###		$g_Log->notice( "アクセスコードのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
###		return false;
###	}

	// 開催場所
	if( ! checkRequest( 'game_place', 'POST' ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = "開催場所エラー";
		$g_Log->notice( "開催場所のチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 開催日
	if( ! checkRequest( 'game_date', 'POST' ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = "開催日エラー";
		$g_Log->notice( "開催日のチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
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

	// 参照処理
	if( isset( $_POST[ 'search_button' ] ) && $_POST[ 'search_button' ] !== "" ) {
	}

	// 追加処理
	if( isset( $_POST[ 'add_button' ] ) && $_POST[ 'add_button' ] !== "" ) {
		if ( addGame() ) {
			$g_Log->notice( "試合会登録内容を追加しました", __FUNCTION__, basename( __FILE__ ) );
		} else {
			$g_Log->notice( "試合大会登録内容の追加に失敗しました", __FUNCTION__, basename( __FILE__ ) );
			$_SESSION[ 'FORM_ERR_MSG' ] = "試合大会登録内容の追加に失敗しました。";
			return false;
		}
	}

	// 更新処理
	if( isset( $_POST[ 'update_button' ] ) && $_POST[ 'update_button' ] !== "" ) {
		if ( updateGame() ) {
			$g_Log->notice( "試合登録内容を更新しました： game_id = " . $_POST[ 'game_id' ], __FUNCTION__, basename( __FILE__ ) );
		} else {
			$g_Log->notice( "試合登録内容の更新に失敗しました： game_id = " . $_POST[ 'game_id' ], __FUNCTION__, basename( __FILE__ ) );
			$_SESSION[ 'FORM_ERR_MSG' ] = "試合登録内容の更新に失敗しました。";
			return false;
		}
	}

	// 更新処理
	if( isset( $_POST[ 'upload_button' ] ) && $_POST[ 'upload_button' ] !== "" ) {
		$attachmentType = 0;
		$postName = '';
		switch ( $_POST[ 'upload_button' ] ) {
			case '31':	// 試合予定ファイル
				$attachmentType = 31;
				$postName = 'filename_plan';
				break;
			case '32':	// 試合結果ファイル
				$attachmentType = 32;
				$postName = 'filename_result';
				break;
			default:
				$g_Log->notice( "不正な更新対象のため以降の処理なし： upload_button = " . $_POST[ 'upload_button' ], __FUNCTION__, basename( __FILE__ ) );
				return false;
		}

		uploadAttachement( $postName, $attachmentType );
	}

	return true;
}

# ==========================================================
# 試合規定ファイル取得処理
# @param int $p_tournamentId 更新対象の大会ID
# @return boolean 更新成功すればtrue、失敗すればfalse
# ==========================================================
function getAttachmentFile( $p_attachmentType ){

	global $g_Log;
	$g_Log->notice( "試合規定ファイル取得処理", __FUNCTION__, basename( __FILE__ ) );

	// SQL文作成
	// アップロードしたファイルの書込み
	$SQL = "";
	$SQL .= " SELECT ";
	$SQL .= "   attachment_id ";
	$SQL .= " , attachment_type ";
	$SQL .= " , physical_file_name ";
	$SQL .= " , file_name ";
	$SQL .= " , file_path ";
	$SQL .= " , file_size ";
	$SQL .= " , mime_type ";
	$SQL .= " , file_description ";
	$SQL .= "   FROM baseball_attachment ";
	$SQL .= "  WHERE is_enabled = 1 ";
	$SQL .= "    AND attachment_type = :attachment_type ";	// attachment_typeが指定されたレコードを取得
	$SQL .= "  ORDER BY attachment_id DESC ";	// 取得したレコードのうち、attachment_idが最大のレコードを取得
	$SQL .= "  LIMIT 1 ";	// 取得件数を1件に限定
	$SQL_Parameters = array(
		'attachment_type' => $p_attachmentType
	);
	
	$_SESSION[ 'FORM_GAME_RULE_ID' ]       = 0;		// 試合規定ファイルID
	$_SESSION[ 'FORM_GAME_RULE_FILENAME' ] = '';		// 試合規定ファイル名

	$attachmentFileNName = '';

	try {
		global $g_DB;
		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );

		foreach( $dataTable as $row ){
			$attachmentFileNName = $row[ 'file_name' ];
		}
		$g_Log->notice( "FileName : {$attachmentFileNName} セットしました", __FUNCTION__, basename( __FILE__ ) );

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '試合規定ファイルの取得に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return $attachmentFileNName;
}

# ==========================================================
# 試合内容追加処理
# @param int $p_tournamentId 更新対象の大会ID
# @return boolean 更新成功すればtrue、失敗すればfalse
# ==========================================================
function addGame(){

	global $g_Log;
	$g_Log->notice( "試合内容追加処理", __FUNCTION__, basename( __FILE__ ) );

	$tournament_id  = checkText( $_POST[ 'tournament_id' ] );
	$game_id        = ""; // 試合IDは自動採番のため空文字
	$team1_id       = checkText( $_POST[ 'team1_id' ] );
	$team2_id       = checkText( $_POST[ 'team2_id' ] );
	$max_game_count = getMaxGameCount( $tournament_id, 1 ); // ゲームブロックは初期値1
	$game_count     = $max_game_count + 1; // ゲーム数はゲームブロックの最大値+1
	
	// SQL文作成
	$SQL = "";
	$SQL .= "INSERT INTO baseball_game ";
	$SQL .= "( tournament_id ";
	$SQL .= ", game_class ";
	$SQL .= ", game_block ";
	$SQL .= ", game_count ";
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
	$SQL .= ", is_enabled ";
	$SQL .= ", created_at ";
	$SQL .= ", created_by ";
	$SQL .= ", updated_at ";
	$SQL .= ", updated_by ";
	$SQL .= ") ";
	$SQL .= "VALUES ";
	$SQL .= "( :tournament_id ";
	$SQL .= ", :game_class "; // ゲームクラスは初期値0
	$SQL .= ", 1 "; // ゲーム数は初期値1
	$SQL .= ", :game_count "; // ゲームブロックは大会ごとの最大値+1
	$SQL .= ", ( SELECT CONCAT( '第1回戦 第', :game_count, '試合', CASE :game_class WHEN 1 THEN '（裏）' ELSE '' END ) ) "; // ゲーム名はゲームブロックから「第〇試合」をサブクエリで取得
	$SQL .= ", CURRENT_TIMESTAMP "; // ゲーム日付は現在日時
	$SQL .= ", '' "; // ゲーム場所は初期値空文字
	$SQL .= ", 0 "; // 勝者IDは初期値0
	$SQL .= ", 0 "; // 敗者IDは初期値0
	$SQL .= ", :team1_id ";
	$SQL .= ", :team2_id ";
	$SQL .= ", ( SELECT team_name FROM baseball_team WHERE team_id = :team1_id ) "; // チーム名はチームIDからサブクエリで取得
	$SQL .= ", ( SELECT team_name FROM baseball_team WHERE team_id = :team2_id ) "; // チーム名はチームIDからサブクエリで取得
	$SQL .= ", 0 "; // チーム1スコアは初期値0
	$SQL .= ", 0 "; // チーム2スコアは初期値0
	$SQL .= ", 0 "; // チーム1次の試合IDは初期値0
	$SQL .= ", 0 "; // チーム2次の試合IDは初期値0
	$SQL .= ", 1 "; // 無効フラグは初期値1
	$SQL .= ", CURRENT_TIMESTAMP ";
	$SQL .= ", :created_by ";
	$SQL .= ", CURRENT_TIMESTAMP ";
	$SQL .= ", :updated_by ";
	$SQL .= ") ";
	
	$SQL_Parameters = array(
		'tournament_id' => $tournament_id,
		'game_class' => 0,
		'game_count' => $game_count,
		'team1_id' => $team1_id,
		'team2_id' => $team2_id,
		'created_by' => basename( __FILE__ ),
		'updated_by' => basename( __FILE__ ),
	);

	try {
		global $g_DB;
		$stmt = $g_DB->execute( $SQL, $SQL_Parameters );

		if( $stmt === false ){
			throw new PDOException( "SQLの実行に失敗しました。" );
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '試合登録内容の追加に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# 試合内容追加処理
# @param int $p_tournamentId 更新対象の大会ID
# @return int 更新成功すれば最大ゲーム数、失敗すれば0
# ==========================================================
function getMaxGameCount( $p_tournamentId, $p_gameBlock ) {

	global $g_Log;
	$g_Log->notice( "試合回数取得処理 tournament_id: $p_tournamentId, game_block: $p_gameBlock", __FUNCTION__, basename( __FILE__ ) );

	$tournament_id      = $p_tournamentId;
	$game_block         = $p_gameBlock;
	
	// SQL文作成
	$SQL = "";
	$SQL .= "SELECT ";
	$SQL .= " MAX( game_count ) AS max_game_count ";
	$SQL .= " FROM baseball_game ";
	$SQL .= " WHERE is_enabled = 1 ";
	$SQL .= "   AND tournament_id = :tournament_id ";
	$SQL .= "   AND game_block = :game_block ";
	
	$SQL_Parameters = array(
		'tournament_id' => $tournament_id,
		'game_block' => $game_block,
	);

	$max_game_count = 0;

	try {
		global $g_DB;
		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );

		foreach( $dataTable as $row ){
			$max_game_count = $row[ 'max_game_count' ];
			$g_Log->notice( "大会登録件数 : {$max_game_count} 件", __FUNCTION__, basename( __FILE__ ) );
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '試合登録内容の追加に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return $max_game_count;
	}

	return $max_game_count;
}

# ==========================================================
# チーム登録内容更新処理
# @param int $p_teamId 更新対象のチームID
# @return boolean 更新成功すればtrue、失敗すればfalse
# ==========================================================
function updateGame(){

	global $g_Log;
	$g_Log->notice( "試合登録内容更新処理", __FUNCTION__, basename( __FILE__ ) );

	$tournament_id = checkText( $_POST[ 'tournament_id' ] );
	$game_id       = checkText( $_POST[ 'game_id' ] );
	$team1_id      = checkText( $_POST[ 'team1_id' ] );
	$team2_id      = checkText( $_POST[ 'team2_id' ] );
	$team1_score   = checkText( $_POST[ 'team1_score' ] );
	$team2_score   = checkText( $_POST[ 'team2_score' ] );
	$winner_id     = checkText( $_POST[ 'winner_id' ] );
	$loser_id      = 0; // 敗者IDは勝者IDから判定するため初期値0
	$game_count    = checkText( $_POST[ 'game_count' ] );
	$game_block    = checkText( $_POST[ 'game_block' ] );
	$game_place    = checkText( $_POST[ 'game_place' ] );
	$game_date     = checkText( $_POST[ 'game_date' ] );


	// 勝者IDが0の場合はスコアから勝者を判定
	if ( $winner_id == 0 ) {
		if ( $team1_score > $team2_score ) {
			$winner_id = $team1_id;
			$loser_id = $team2_id;
		} else if ( $team1_score < $team2_score ) {
			$winner_id = $team2_id;
			$loser_id = $team1_id;
		}
	}
	else{
		if ( $winner_id == $team1_id ) {
			$loser_id = $team2_id;
		} else if ( $winner_id == $team2_id ) {
			$loser_id = $team1_id;
		}
	}

	$new_game_block = $game_block + 1; // ゲームブロックはゲーム数+1
	$new_game_count = (int) ceil( $game_count / 2 ); // ゲーム数はゲームブロックの最大値+1
	
	// SQL文作成
	$SQL = "";
	$SQL .= "UPDATE baseball_game ";
	$SQL .= "SET game_place = :game_place ";
	$SQL .= "  , game_date = :game_date ";
	$SQL .= "  , updated_at = CURRENT_TIMESTAMP ";
	$SQL .= "  , updated_by = :updated_by ";
	$SQL .= "WHERE is_enabled = 1 ";
	$SQL .= "  AND tournament_id = :tournament_id ";
	$SQL .= "  AND game_id = :game_id ";
	
	$SQL_Parameters = array(
		'game_id' => $game_id,
		'tournament_id' => $tournament_id,
		'game_place' => $game_place,
		'game_date' => $game_date,
		'updated_by' => basename( __FILE__ ),
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

	// 勝者IDが0でない場合は次の試合登録内容を更新
	if( $winner_id != 0 ) {
		if ( insertNextGame( $tournament_id, $new_game_block, $new_game_count, $winner_id ) ) {
			$g_Log->notice( "次の試合登録内容を更新しました", __FUNCTION__, basename( __FILE__ ) );
		} else {
			$g_Log->notice( "次の試合登録内容の更新に失敗しました", __FUNCTION__, basename( __FILE__ ) );
			$_SESSION[ 'FORM_ERR_MSG' ] = "次の試合登録内容の更新に失敗しました。";
			return false;
		}
	}

	if( $loser_id != 0 ) {
		if( $game_block == 1 ){ // ゲームブロックが1の場合は敗者復活戦のため裏大会を作成
			$new_tournament_id = 0;
			do {
				$new_tournament_id = insertUraTournament( $tournament_id ); // 裏大会を作成して新しい大会IDを取得
			} while ( $new_tournament_id == 0 ); // ゲームブロックが3以下の場合は繰り返す
			$g_Log->notice( "裏大会の取得に成功しました 裏大会ID: " . $new_tournament_id, __FUNCTION__, basename( __FILE__ ) );
			
			if ( insertNextGame( $new_tournament_id, 1, 0, $loser_id ) ) {
				$g_Log->notice( "次の試合登録内容を更新しました", __FUNCTION__, basename( __FILE__ ) );
			} else {
				$g_Log->notice( "次の試合登録内容の更新に失敗しました", __FUNCTION__, basename( __FILE__ ) );
				$_SESSION[ 'FORM_ERR_MSG' ] = "次の試合登録内容の更新に失敗しました。";
				return false;
			}
		}
	}

	return true;
}

# ==========================================================
# 試合内容追加処理
# @param int $p_tournamentId 更新対象の大会ID
# @return boolean 更新成功すればtrue、失敗すればfalse
# ==========================================================
function insertNewTeam( $p_tournamentId, $p_teamId, $p_newTournamentId ) {

	global $g_Log;
	$g_Log->notice( "チーム情報登録処理 tournament_id: $p_tournamentId, team_id: $p_teamId, new_tournament_id: $p_newTournamentId", __FUNCTION__, basename( __FILE__ ) );

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
	$SQL .= "SELECT ";
	$SQL .= "  team_name ";
	$SQL .= ", team_manager ";
	$SQL .= ", team_contact ";
	$SQL .= ", team_access_cd ";
	$SQL .= ", :new_tournament_id ";
	$SQL .= ", 1 "; // 無効フラグは初期値1
	$SQL .= ", CURRENT_TIMESTAMP ";
	$SQL .= ", :created_by ";
	$SQL .= ", CURRENT_TIMESTAMP ";
	$SQL .= ", :updated_by ";
	$SQL .= "FROM baseball_team ";
	$SQL .= "WHERE is_enabled = 1 ";
	$SQL .= "  AND team_id = :team_id ";
	$SQL .= "  AND tournament_id = :tournament_id ";

	$SQL_Parameters = array(
		"tournament_id"     => $p_tournamentId,
		"new_tournament_id" => $p_newTournamentId,
		"team_id"           => $p_teamId,
		"created_by"        => basename( __FILE__ ),
		"updated_by"        => basename( __FILE__ )
	);

	try {
		global $g_DB;
		$stmt = $g_DB->execute2( $SQL, $SQL_Parameters );

		if( $stmt === false ){
			throw new PDOException( "SQLの実行に失敗しました。" );
		}

		$newId = $stmt[ 'newId' ] ?? 0;

		return $newId;

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = 'チーム情報の登録処理でエラーが発生しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return $newId;
}

# ==========================================================
# 試合内容追加処理
# @param int $p_tournamentId 更新対象の大会ID
# @return boolean 更新成功すればtrue、失敗すればfalse
# ==========================================================
function insertNextGame( $p_tournamentId, $p_gameBlock, $p_gameCount, $p_teamId ){

	global $g_Log;
	$g_Log->notice( "次回戦追加処理 tournament_id: $p_tournamentId, game_block: $p_gameBlock, game_count: $p_gameCount, team_id: $p_teamId", __FUNCTION__, basename( __FILE__ ) );

	$tournament_id  = $p_tournamentId;
	$game_block     = $p_gameBlock;
	$game_count     = $p_gameCount;
	$team_id        = $p_teamId;
	$max_game_count = getMaxGameCount( $p_tournamentId, $p_gameBlock ); // ゲームブロックは初期値1
	
	// SQL文作成
	$SQL_Update = "";
	$SQL_Update .= "UPDATE baseball_game ";
	$SQL_Update .= "SET team2_id   = :team2_id ";
	$SQL_Update .= "  , team2_name = ( SELECT team_name FROM baseball_team WHERE team_id = :team2_id ) "; // チーム名はチームIDからサブクエリで取得
	$SQL_Update .= "  , updated_at = CURRENT_TIMESTAMP ";
	$SQL_Update .= "  , updated_by = :updated_by ";
	$SQL_Update .= "WHERE tournament_id = :tournament_id ";
	$SQL_Update .= "  AND team2_id   = 0 ";
	$SQL_Update .= "  AND game_block = :game_block ";
	$SQL_Update .= "  AND game_count = :game_count ";
	
	$SQL_Update_Parameters = array(
		'tournament_id' => $tournament_id,
		'game_block'    => $game_block,
		'game_count'    => $game_count == 0 ? $max_game_count + 1 : $game_count, // ゲーム数が0の場合はゲームブロックの最大値+1、そうでない場合は引数のゲーム数
		'team2_id'      => $team_id,
		'updated_by'    => basename( __FILE__ ),
	);

	try {
		global $g_DB;
		$stmt = $g_DB->execute( $SQL_Update, $SQL_Update_Parameters );

		$g_Log->notice( "次試合更新処理 件数: $stmt", __FUNCTION__, basename( __FILE__ ) );

		if( $stmt === 0 ){
			// SQL文作成
			$SQL_Insert = "";
			$SQL_Insert .= "INSERT INTO baseball_game ";
			$SQL_Insert .= "( tournament_id ";
			$SQL_Insert .= ", game_class ";
			$SQL_Insert .= ", game_block ";
			$SQL_Insert .= ", game_count ";
			$SQL_Insert .= ", game_name ";
			$SQL_Insert .= ", game_date ";
			$SQL_Insert .= ", game_place ";
			$SQL_Insert .= ", winner_id ";
			$SQL_Insert .= ", loser_id ";
			$SQL_Insert .= ", team1_id ";
			$SQL_Insert .= ", team2_id ";
			$SQL_Insert .= ", team1_name ";
			$SQL_Insert .= ", team2_name ";
			$SQL_Insert .= ", team1_score ";
			$SQL_Insert .= ", team2_score ";
			$SQL_Insert .= ", team1_last_game_id ";
			$SQL_Insert .= ", team2_last_game_id ";
			$SQL_Insert .= ", is_enabled ";
			$SQL_Insert .= ", created_at ";
			$SQL_Insert .= ", created_by ";
			$SQL_Insert .= ", updated_at ";
			$SQL_Insert .= ", updated_by ";
			$SQL_Insert .= ") ";
			$SQL_Insert .= "VALUES ";
			$SQL_Insert .= "( :tournament_id ";
			$SQL_Insert .= ", :game_class ";
			$SQL_Insert .= ", :game_block ";
			$SQL_Insert .= ", :game_count ";
			$SQL_Insert .= ", ( SELECT CONCAT( '第', :game_block, '回戦 第', :game_count, '試合', CASE :game_class WHEN 1 THEN '（裏）' ELSE '' END ) ) "; // ゲーム名はゲームブロックから「第〇試合」をサブクエリで取得
			$SQL_Insert .= ", CURRENT_TIMESTAMP "; // ゲーム日付は現在日時
			$SQL_Insert .= ", '' "; // ゲーム場所は初期値空文字
			$SQL_Insert .= ", 0 "; // 勝者IDは初期値0
			$SQL_Insert .= ", 0 "; // 敗者IDは初期値0
			$SQL_Insert .= ", :team1_id "; // チームIDは引数から取得
			$SQL_Insert .= ", 0 ";  // チーム2IDは初期値0
			$SQL_Insert .= ", ( SELECT team_name FROM baseball_team WHERE team_id = :team1_id ) "; // チーム名はチームIDからサブクエリで取得
			$SQL_Insert .= ", '' "; // チーム名2
			$SQL_Insert .= ", 0 "; // チーム1スコアは初期値0
			$SQL_Insert .= ", 0 "; // チーム2スコアは初期値0
			$SQL_Insert .= ", 0 "; // チーム1次の試合IDは初期値0
			$SQL_Insert .= ", 0 "; // チーム2次の試合IDは初期値0
			$SQL_Insert .= ", 1 "; // 無効フラグは初期値1
			$SQL_Insert .= ", CURRENT_TIMESTAMP ";
			$SQL_Insert .= ", :created_by ";
			$SQL_Insert .= ", CURRENT_TIMESTAMP ";
			$SQL_Insert .= ", :updated_by ";
			$SQL_Insert .= ") ";
	
			$SQL_Insert_Parameters = array(
				'tournament_id' => $tournament_id,
				'game_block' => $game_block,
				'game_count' => $game_count == 0 ? $max_game_count + 1 : $game_count, // ゲーム数が0の場合はゲームブロックの最大値+1、そうでない場合は引数のゲーム数
				'team1_id' => $team_id,
				'game_class' => 0, // ゲームクラスは初期値0
				'created_by' => basename( __FILE__ ),
				'updated_by' => basename( __FILE__ ),
			);

			$stmt = $g_DB->execute( $SQL_Insert, $SQL_Insert_Parameters );

			$g_Log->notice( "裏大会追加処理 件数: $stmt", __FUNCTION__, basename( __FILE__ ) );

			if( $stmt === false ){
				throw new PDOException( "SQLの実行に失敗しました。" );
			}
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '試合登録内容の追加に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# 裏大会内容追加処理
# @param int $p_tournamentId 更新対象の大会ID
# @return boolean 更新成功すればtrue、失敗すればfalse
# ==========================================================
function insertUraTournament( $p_tournamentId ){

	global $g_Log;
	$g_Log->notice( "裏大会内容追加処理 tournament_id: $p_tournamentId", __FUNCTION__, basename( __FILE__ ) );

	$tournament_id  = $p_tournamentId;
	$new_tournament_id = 0; // 裏大会IDは自動採番のため0
	
	// SQL文作成
	$SQL_Select = "";
	$SQL_Select .= "SELECT ";
	$SQL_Select .= "  tournament_id ";
	$SQL_Select .= ", tournament_title ";
	$SQL_Select .= ", tournament_text ";
	$SQL_Select .= ", tournament_start_date ";
	$SQL_Select .= ", tournament_end_date ";
	$SQL_Select .= ", tournament_parent_id ";
	$SQL_Select .= "FROM baseball_tournament ";
	$SQL_Select .= "WHERE is_enabled = 1 ";
	$SQL_Select .= "  AND tournament_parent_id = :tournament_id ";
	
	$SQL_Select_Parameters = array(
		'tournament_id' => $tournament_id,
	);


	try {
		global $g_DB;
		$dataTable = $g_DB->select( $SQL_Select, $SQL_Select_Parameters );

		foreach( $dataTable as $row ){
			$new_tournament_id = $row[ 'tournament_id' ];
			$g_Log->notice( "裏大会ID取得 : tournament_id={$new_tournament_id} 件", __FUNCTION__, basename( __FILE__ ) );
			return $new_tournament_id;
		}

		$g_Log->notice( "裏大会検索 : 件数=" . count( $dataTable ) . " 件", __FUNCTION__, basename( __FILE__ ) );

		// 裏大会が存在しない場合は裏大会を作成
		if( count( $dataTable ) === 0 ){
			// SQL文作成
			$SQL_Insert = "";
			$SQL_Insert .= "INSERT INTO baseball_tournament ";
			$SQL_Insert .= "( tournament_title ";
			$SQL_Insert .= ", tournament_text ";
			$SQL_Insert .= ", tournament_start_date ";
			$SQL_Insert .= ", tournament_end_date ";
			$SQL_Insert .= ", tournament_parent_id ";
			$SQL_Insert .= ", is_enabled ";
			$SQL_Insert .= ", created_at ";
			$SQL_Insert .= ", created_by ";
			$SQL_Insert .= ", updated_at ";
			$SQL_Insert .= ", updated_by ";
			$SQL_Insert .= ") ";
			$SQL_Insert .= "SELECT ";
			$SQL_Insert .= "  tournament_title "; // 大会名はチームIDからサブクエリで取得
			$SQL_Insert .= ", CONCAT( tournament_text, ' (裏大会)' ) ";
			$SQL_Insert .= ", tournament_start_date ";
			$SQL_Insert .= ", tournament_end_date ";
			$SQL_Insert .= ", :tournament_id "; // 親大会IDは引数から取得
			$SQL_Insert .= ", 1 "; // 有効フラグは初期値1
			$SQL_Insert .= ", CURRENT_TIMESTAMP ";
			$SQL_Insert .= ", :created_by ";
			$SQL_Insert .= ", CURRENT_TIMESTAMP ";
			$SQL_Insert .= ", :updated_by ";
			$SQL_Insert .= "FROM baseball_tournament ";
			$SQL_Insert .= "WHERE tournament_id = :tournament_id ";
	
			$SQL_Insert_Parameters = array(
				'tournament_id' => $tournament_id,
				'created_by' => basename( __FILE__ ),
				'updated_by' => basename( __FILE__ ),
			);

			$stmt = $g_DB->execute( $SQL_Insert, $SQL_Insert_Parameters );

			if( $stmt === false ){
				throw new PDOException( "SQLの実行に失敗しました。" );
			}
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '裏大会内容の追加に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return 0;
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
	// 試合登録の読み込み
	$filename = __DIR__ . DIRECTORY_SEPARATOR . "article_gameschedule3.php";
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
