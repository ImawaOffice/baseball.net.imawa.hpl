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
		'RES_VIEW_STATE'    => 0,		// 表示ステータス(0: 初期画面)
		'RES_ERR_MSG'       => '',		// エラーメッセージ
		'RES_PAGE_ROWS'     => 10,		// 表示行数
		'RES_PAGE_CURRENT'  => 1,		// ページ番号
		'RES_PAGE_OFFSET'   => 0,		// 表示オフセット
		'RES_PAGE_TOTAL'    => 0,		// 総ページ数
		'RES_PAGE_TOP'      => 1,		// ページの最初
		'RES_PAGE_LAST'     => 1,		// ページの最後
		'RES_PAGE_PREVIOUS' => 1,		// 前のページ
		'RES_PAGE_NEXT'     => 1,		// 次のページ
		'RES_BUTTON_ADD'    => '追加',	// 追加ボタン
		'RES_BUTTON_UPDATE' => '更新',	// 更新ボタン
		'RES_BUTTON_DELETE' => '削除',	// 削除ボタン
		'RES_BUTTON_SEARCH' => '検索',	// 検索ボタン
		'RES_TOURNAMENT_ID' => '',		// 大会ID
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

	// ページトップボタンの取得
	if( isset( $_POST[ 'page_top' ] ) ) {
		$g_Log->debug( "POST : page_top = " . $_POST[ 'page_top' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// ページ前へボタンの取得
	if( isset( $_POST[ 'page_previous' ] ) ) {
		$g_Log->debug( "POST : page_previous = " . $_POST[ 'page_previous' ], __FUNCTION__, basename( __FILE__ ) );
	}
	
	// 現在のページ番号の取得
	if( isset( $_POST[ 'page_current' ] ) ) {
		$g_Log->debug( "POST : page_current = " . $_POST[ 'page_current' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// ページ次へボタンの取得
	if( isset( $_POST[ 'page_next' ] ) ) {
		$g_Log->debug( "POST : page_next = " . $_POST[ 'page_next' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// ページ最後へボタンの取得
	if( isset( $_POST[ 'page_last' ] ) ) {
		$g_Log->debug( "POST : page_last = " . $_POST[ 'page_last' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// 表示件数の取得
	if( isset( $_POST[ 'page_rows' ] ) ) {
		$g_Log->debug( "POST : page_rows = " . $_POST[ 'page_rows' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// 検索ボタンの取得
	if( isset( $_POST[ 'search_button' ] ) ) {
		$g_Log->debug( "POST : search_button = " . $_POST[ 'search_button' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// 更新ボタンの取得
	if( isset( $_POST[ 'update_button' ] ) ) {
		$g_Log->debug( "POST : update_button = " . $_POST[ 'update_button' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// 追加ボタンの取得
	if( isset( $_POST[ 'add_button' ] ) ) {
		$g_Log->debug( "POST : add_button = " . $_POST[ 'add_button' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// 削除ボタンの取得
	if( isset( $_POST[ 'delete_button' ] ) ) {
		$g_Log->debug( "POST : delete_button = " . $_POST[ 'delete_button' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// 大会IDの取得
	if( isset( $_POST[ 'tournament_id' ] ) ) {
		$g_Log->debug( "POST : tournament_id = " . $_POST[ 'tournament_id' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// アクセスコードの取得
	if( isset( $_POST[ 'access_code' ] ) ) {
		$g_Log->debug( "POST : access_code = " . $_POST[ 'access_code' ], __FUNCTION__, basename( __FILE__ ) );
	}

	return true;
}

# ==========================================================
# チェック処理
# ==========================================================
function checkProc() {

	global $g_Log;
	$g_Log->debug( "チェック処理", __FUNCTION__, basename( __FILE__ ) );

	// ページネーションのチェック
	if( ! checkPageNation() ){
		$_SESSION[ 'RES_ERR_MSG' ] = "ページネーションエラー";
		$g_Log->debug( "ページネーションのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 大会ID(SELECT)
	if( ! checkRequest( 'tournament_id', 'POST' ) ) {
		$_SESSION[ 'RES_ERR_MSG' ] = "大会IDエラー";
		$g_Log->debug( "大会IDの取得に失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}
	$_SESSION[ 'RES_TOURNAMENT_ID' ] = $_POST[ 'tournament_id' ];
	$g_Log->debug( "[ RES_TOURNAMENT_ID ] : " . $_SESSION[ 'RES_TOURNAMENT_ID' ], __FUNCTION__, basename( __FILE__ ) );
	
	// アクセスコードの取得
	if( ! checkRequest( 'access_code', 'POST' ) ) {
		$_SESSION[ 'RES_ERR_MSG' ] = "アクセスコードエラー";
		$g_Log->debug( "アクセスコードの取得に失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}
	$_SESSION[ 'RES_ACCESS_CODE' ] = $_POST[ 'access_code' ];
	$g_Log->debug( "[ RES_ACCESS_CODE ] : " . $_SESSION[ 'RES_ACCESS_CODE' ], __FUNCTION__, basename( __FILE__ ) );

	// 参照チェック
	if( ! checkSearch() ) {
		$g_Log->debug( "参照チェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 追加チェック
	if( ! checkAdd() ) {
		$g_Log->debug( "追加チェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 更新チェック
	if( ! checkUpdate() ) {
		$g_Log->debug( "更新チェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 削除チェック
	if( ! checkDelete() ) {
		$g_Log->debug( "削除チェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}


	return true;
}

# ==========================================================
# ページネーションチェック処理
# ==========================================================
function checkPageNation() {

	global $g_Log;
	$g_Log->debug( "ページネーションチェック処理", __FUNCTION__, basename( __FILE__ ) );

	// 表示行数
	$pageRows	  = ( int )( $_POST[ 'page_rows' ] ?? $_SESSION[ 'RES_PAGE_ROWS' ] ?? 2 );
	$_SESSION[ 'RES_PAGE_ROWS' ]     = $pageRows;
	$g_Log->debug( "[ RES_PAGE_ROWS ] : $pageRows => " . $_SESSION[ 'RES_PAGE_ROWS' ], __FUNCTION__, basename( __FILE__ ) );

	// 総件数
	$recordsCount = getTournamentCount();
	$pageTotal    = ( int )ceil( $recordsCount / $pageRows );
	$_SESSION[ 'RES_PAGE_TOTAL' ]    = $pageTotal;
	$g_Log->debug( "[ RES_PAGE_TOTAL ] : $pageTotal => " . $_SESSION[ 'RES_PAGE_TOTAL' ], __FUNCTION__, basename( __FILE__ ) );
	
	// 現在ページ
	$pageCurrent  = ( int )( $_POST[ 'page_current' ] ?? $_SESSION[ 'RES_PAGE_CURRENT' ] ?? 1 );
	$_SESSION[ 'RES_PAGE_CURRENT' ]  = $pageCurrent;
	$g_Log->debug( "[ RES_PAGE_CURRENT ] : $pageCurrent => " . $_SESSION[ 'RES_PAGE_CURRENT' ], __FUNCTION__, basename( __FILE__ ) );

	// ページの最初
	if ( isset( $_POST[ 'page_top' ] ) ) {
		$pageCurrent = 1;
		$_SESSION[ 'RES_PAGE_CURRENT' ] = $pageCurrent;
		$g_Log->debug( "[ RES_PAGE_CURRENT ] : $pageCurrent => " . $_SESSION[ 'RES_PAGE_CURRENT' ], __FUNCTION__, basename( __FILE__ ) );
	}

	$pageTop      = 1;
	$_SESSION[ 'RES_PAGE_TOP' ]      = $pageTop;
	$g_Log->debug( "[ RES_PAGE_TOP ] : $pageTop => " . $_SESSION[ 'RES_PAGE_TOP' ], __FUNCTION__, basename( __FILE__ ) );

	// ページの最後
	if ( isset( $_POST[ 'page_last' ] ) ) {
		$pageCurrent = $pageTotal;
		$_SESSION[ 'RES_PAGE_CURRENT' ] = $pageCurrent;
		$g_Log->debug( "[ RES_PAGE_CURRENT ] : $pageCurrent => " . $_SESSION[ 'RES_PAGE_CURRENT' ], __FUNCTION__, basename( __FILE__ ) );
	}

	$pageLast     = $pageTotal;
	$_SESSION[ 'RES_PAGE_LAST' ]     = $pageLast;
	$g_Log->debug( "[ RES_PAGE_LAST ] : $pageLast => " . $_SESSION[ 'RES_PAGE_LAST' ], __FUNCTION__, basename( __FILE__ ) );

	// ページ前へ
	if ( isset( $_POST[ 'page_previous' ] ) ) {
		$pageCurrent = max( 1, $pageCurrent - 1 );
		$_SESSION[ 'RES_PAGE_CURRENT' ] = $pageCurrent;
		$g_Log->debug( "[ RES_PAGE_CURRENT ] : $pageCurrent => " . $_SESSION[ 'RES_PAGE_CURRENT' ], __FUNCTION__, basename( __FILE__ ) );
	}

	if ( isset( $_POST[ 'page_next' ] ) ) {
		$pageCurrent = min( $pageTotal, $pageCurrent + 1 );
		$_SESSION[ 'RES_PAGE_CURRENT' ] = $pageCurrent;
		$g_Log->debug( "[ RES_PAGE_CURRENT ] : $pageCurrent => " . $_SESSION[ 'RES_PAGE_CURRENT' ], __FUNCTION__, basename( __FILE__ ) );
	}

	$pagePrevious = max( 1, $pageCurrent - 1 );
	$_SESSION[ 'RES_PAGE_PREVIOUS' ] = $pagePrevious;
	$g_Log->debug( "[ RES_PAGE_PREVIOUS ] : $pagePrevious => " . $_SESSION[ 'RES_PAGE_PREVIOUS' ], __FUNCTION__, basename( __FILE__ ) );

	$pageNext     = min( $pageTotal, $pageCurrent + 1 );
	$_SESSION[ 'RES_PAGE_NEXT' ]     = $pageNext;
	$g_Log->debug( "[ RES_PAGE_NEXT ] : $pageNext => " . $_SESSION[ 'RES_PAGE_NEXT' ], __FUNCTION__, basename( __FILE__ ) );

	// 表示オフセット
	$pageOffset = ( $pageCurrent - 1 ) * $pageRows;
	$_SESSION[ 'RES_PAGE_OFFSET' ]   = $pageOffset;
	$g_Log->debug( "[ RES_PAGE_OFFSET ] : $pageOffset => " . $_SESSION[ 'RES_PAGE_OFFSET' ], __FUNCTION__, basename( __FILE__ ) );

	return true;
}

# ==========================================================
# 参照チェック処理
# ==========================================================
function checkSearch() {

	global $g_Log;
	$g_Log->debug( "参照チェック処理", __FUNCTION__, basename( __FILE__ ) );

	// 検索ボタンでサブミットされてなければ処理をスキップ
	if( ! checkRequest( 'search_button', 'POST' ) ) {
		return true;
	}

	if( ! checkForm() ) {
		$g_Log->debug( "フォームのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# 追加チェック処理
# ==========================================================
function checkAdd() {

	global $g_Log;
	$g_Log->debug( "追加チェック処理", __FUNCTION__, basename( __FILE__ ) );

	// 追加ボタンでサブミットされてなければ処理をスキップ
	if( ! checkRequest( 'add_button', 'POST' ) ) {
		return true;
	}

	if( ! checkForm() ) {
		$g_Log->debug( "フォームのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# 更新チェック処理
# ==========================================================
function checkUpdate() {

	global $g_Log;
	$g_Log->debug( "更新チェック処理", __FUNCTION__, basename( __FILE__ ) );

	// 更新ボタンでサブミットされてなければ処理をスキップ
	if( ! checkRequest( 'update_button', 'POST' ) ) {
		return true;
	}

	if( ! checkForm() ) {
		$g_Log->debug( "フォームのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# 削除チェック処理
# ==========================================================
function checkDelete() {

	global $g_Log;
	$g_Log->debug( "削除チェック処理", __FUNCTION__, basename( __FILE__ ) );
	
	// 削除ボタンでサブミットされてなければ処理をスキップ
	if( ! checkRequest( 'delete_button', 'POST' ) ) {
		return true;
	}

	// 削除対象のID
	if ( deleteGameId( $_POST[ 'delete_button' ] ) ) {
		$g_Log->debug( "試合登録内容を削除しました： delete_button = " . $_POST[ 'delete_button' ], __FUNCTION__, basename( __FILE__ ) );
	} else {
		$g_Log->debug( "試合登録内容の削除に失敗しました： delete_button = " . $_POST[ 'delete_button' ], __FUNCTION__, basename( __FILE__ ) );
		$_SESSION[ 'RES_ERR_MSG' ] = "試合登録内容の削除に失敗しました。";
		return false;
	}

	return true;
}

# ==========================================================
# フォームチェック処理
# ==========================================================
function checkForm(){
	
	global $g_Log;
	$g_Log->debug( "フォームチェック処理", __FUNCTION__, basename( __FILE__ ) );

	// アクセスコード
	if( ! checkRequest( 'access_code', 'POST' ) ) {
		$_SESSION[ 'RES_ERR_MSG' ] = "アクセスコードエラー";
		$g_Log->debug( "アクセスコードのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// チームID(更新時のみチェック)
	if( ! checkRequest( 'update_button', 'POST' ) ) {
		if( ! checkRequest( 'game_id', 'POST' ) ) {
			$_SESSION[ 'RES_ERR_MSG' ] = "試合IDエラー";
			$g_Log->debug( "試合IDのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
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
	$g_Log->debug( "セット処理", __FUNCTION__, basename( __FILE__ ) );

	$_SESSION[ 'RES_VIEW_STATE' ] = trim( $_POST[ 'view_state' ] );
	$g_Log->debug( "[ RES_VIEW_STATE ] : " . $_SESSION[ 'RES_VIEW_STATE' ], __FUNCTION__, basename( __FILE__ ) );

	// 参照処理
	if( isset( $_POST[ 'search_button' ] ) && $_POST[ 'search_button' ] !== "" ) {
	}

	// 追加処理
	if( isset( $_POST[ 'add_button' ] ) && $_POST[ 'add_button' ] !== "" ) {
		if ( addGame() ) {
			$g_Log->debug( "試合会登録内容を追加しました", __FUNCTION__, basename( __FILE__ ) );
		} else {
			$g_Log->debug( "試合大会登録内容の追加に失敗しました", __FUNCTION__, basename( __FILE__ ) );
			$_SESSION[ 'RES_ERR_MSG' ] = "試合大会登録内容の追加に失敗しました。";
			return false;
		}
	}

	// 更新処理
	if( isset( $_POST[ 'update_button' ] ) && $_POST[ 'update_button' ] !== "" ) {
		if ( updateGame() ) {
			$g_Log->debug( "試合登録内容を更新しました： game_id = " . $_POST[ 'game_id' ], __FUNCTION__, basename( __FILE__ ) );
		} else {
			$g_Log->debug( "試合登録内容の更新に失敗しました： game_id = " . $_POST[ 'game_id' ], __FUNCTION__, basename( __FILE__ ) );
			$_SESSION[ 'RES_ERR_MSG' ] = "試合登録内容の更新に失敗しました。";
			return false;
		}
	}

	return true;
}

# ==========================================================
# 試合内容追加処理
# @param int $p_tournamentId 更新対象の大会ID
# @return boolean 更新成功すればtrue、失敗すればfalse
# ==========================================================
function addGame(){

	global $g_Log;
	$g_Log->debug( "試合内容追加処理", __FUNCTION__, basename( __FILE__ ) );

	$tournament_id  = checkText( $_POST[ 'tournament_id' ] );
	$game_id        = ""; // 試合IDは自動採番のため空文字
	$team1_id       = checkText( $_POST[ 'team1_id' ] );
	$team2_id       = checkText( $_POST[ 'team2_id' ] );
	$max_game_count = getMaxGameCount( $tournament_id, 1, 1 ); // ゲームクラスは初期値1、ゲームブロックは初期値1
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
		$_SESSION[ 'RES_ERR_MSG' ] = '試合登録内容の追加に失敗しました';
		$g_Log->error( $_SESSION[ 'RES_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# 試合回数取得処理
# @param int $p_tournamentId 更新対象の大会ID
# @param int $p_gameClass 更新対象のゲームクラス
# @param int $p_gameBlock 更新対象のゲームブロック
# @return int 更新成功すれば最大ゲーム数、失敗すれば0
# ==========================================================
function getMaxGameCount( $p_tournamentId, $p_gameClass, $p_gameBlock ) {

	global $g_Log;
	$g_Log->debug( "試合回数取得処理 tournament_id: $p_tournamentId, game_class: $p_gameClass, game_block: $p_gameBlock", __FUNCTION__, basename( __FILE__ ) );

	$tournament_id      = $p_tournamentId;
	$game_class         = $p_gameClass;
	$game_block         = $p_gameBlock;
	
	// SQL文作成
	$SQL = "";
	$SQL .= "SELECT ";
	$SQL .= " MAX( game_count ) AS max_game_count ";
	$SQL .= " FROM baseball_game ";
	$SQL .= " WHERE is_enabled = 1 ";
	$SQL .= "   AND tournament_id = :tournament_id ";
	$SQL .= "   AND game_block = :game_block ";
	$SQL .= "   AND game_class = :game_class ";
	$SQL_Parameters = array(
		'tournament_id' => $tournament_id,
		'game_block' => $game_block,
		'game_class' => $game_class,
	);

	$max_game_count = 0;

	try {
		global $g_DB;
		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );

		foreach( $dataTable as $row ){
			$max_game_count = $row[ 'max_game_count' ];
			$g_Log->debug( "大会登録件数 : {$max_game_count} 件", __FUNCTION__, basename( __FILE__ ) );
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'RES_ERR_MSG' ] = '試合登録内容の追加に失敗しました';
		$g_Log->error( $_SESSION[ 'RES_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
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
	$g_Log->debug( "試合登録内容更新処理", __FUNCTION__, basename( __FILE__ ) );

	$tournament_id = checkText( $_POST[ 'tournament_id' ] );
	$game_id       = checkText( $_POST[ 'game_id' ] );
	$team1_id      = checkText( $_POST[ 'team1_id' ] );
	$team2_id      = checkText( $_POST[ 'team2_id' ] );
	$team1_score   = checkText( $_POST[ 'team1_score' ] );
	$team2_score   = checkText( $_POST[ 'team2_score' ] );
	$winner_id     = checkText( $_POST[ 'winner_id' ] );
	$loser_id      = 0; // 敗者IDは勝者IDから判定するため初期値0
	$game_class    = checkText( $_POST[ 'game_class' ] );
	$game_block    = checkText( $_POST[ 'game_block' ] );
	$game_count    = checkText( $_POST[ 'game_count' ] );

	// チーム１のスコアがチーム２のスコアより大きい場合、勝者IDをチーム１、敗者IDをチーム２に設定
	if ( $team1_score > $team2_score ) {
		$winner_id = $team1_id;
		$loser_id = $team2_id;
		$g_Log->debug( "勝者ID: $team1_id( $team1_score ) 敗者ID: $team2_id( $team2_score )", __FUNCTION__, basename( __FILE__ ) );
	}

	// チーム１のスコアがチーム２のスコアより小さい場合、勝者IDをチーム２、敗者IDをチーム１に設定
	if ( $team1_score < $team2_score ) {
		$winner_id = $team2_id;
		$loser_id = $team1_id;
		$g_Log->debug( "勝者ID: $team2_id( $team2_score ) 敗者ID: $team1_id( $team1_score )", __FUNCTION__, basename( __FILE__ ) );
	}

	// 同点の場合、勝者IDを設定
	if( $team1_score == $team2_score ) {
		if ( $winner_id != 0 ) {
			switch( $winner_id ) {
				case $team1_id:
					$loser_id = $team2_id;
					$g_Log->debug( "勝者ID: $team1_id( $team1_score ) 敗者ID: $team2_id( $team2_score )", __FUNCTION__, basename( __FILE__ ) );
					break;
				case $team2_id:
					$loser_id = $team1_id;
					$g_Log->debug( "勝者ID: $team2_id( $team2_score ) 敗者ID: $team1_id( $team1_score )", __FUNCTION__, basename( __FILE__ ) );
					break;
				default:
					$g_Log->debug( "勝者IDがチームIDと一致しないためスコアから勝者を判定します", __FUNCTION__, basename( __FILE__ ) );
					$winner_id = 0; // 勝者IDを0にリセット
			}
		}
	}
	
	// SQL文作成
	$SQL = "";
	$SQL .= "UPDATE baseball_game ";
	$SQL .= "SET winner_id = :winner_id ";
	$SQL .= "  , loser_id = :loser_id ";
	$SQL .= "  , team1_score = :team1_score ";
	$SQL .= "  , team2_score = :team2_score ";
	$SQL .= "  , updated_at = CURRENT_TIMESTAMP ";
	$SQL .= "  , updated_by = :updated_by ";
	$SQL .= "WHERE is_enabled = 1 ";
	$SQL .= "  AND tournament_id = :tournament_id ";
	$SQL .= "  AND game_id = :game_id ";
	
	$SQL_Parameters = array(
		'game_id' => $game_id,
		'tournament_id' => $tournament_id,
		'winner_id' => $winner_id,
		'loser_id' => $loser_id,
		'team1_score' => $team1_score,
		'team2_score' => $team2_score,
		'updated_by' => basename( __FILE__ ),
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

	// 勝者IDが0でない場合は次の試合登録内容を更新
	$new_game_block = $game_block + 1; // ゲームブロックはゲーム数+1
	$new_game_count = (int) ceil( $game_count / 2 ); // ゲーム数はゲームブロックの最大値+1

	if( $winner_id != 0 ) {
		$g_Log->debug( "次の試合内容登録を更新します", __FUNCTION__, basename( __FILE__ ) );
		if ( insertNextGame( $tournament_id, $game_class, $game_block, $game_count, $winner_id ) ) {
			$g_Log->debug( "次の試合登録内容を更新しました", __FUNCTION__, basename( __FILE__ ) );
		} else {
			$g_Log->debug( "次の試合登録内容の更新に失敗しました", __FUNCTION__, basename( __FILE__ ) );
			$_SESSION[ 'RES_ERR_MSG' ] = "次の試合登録内容の更新に失敗しました。";
			return false;
		}
	}

	if( $loser_id != 0 ) {
		if( $game_block == 1 ){ // ゲームブロックが1の場合は敗者復活戦のため裏大会を作成
			$g_Log->debug( "次の裏試合内容登録を更新します", __FUNCTION__, basename( __FILE__ ) );
			if ( insertUraGame( $tournament_id, 1, 1, $loser_id ) ) {
				$g_Log->debug( "次の裏試合登録内容を更新しました", __FUNCTION__, basename( __FILE__ ) );
			} else {
				$g_Log->debug( "次の裏試合登録内容の更新に失敗しました", __FUNCTION__, basename( __FILE__ ) );
				$_SESSION[ 'RES_ERR_MSG' ] = "次の裏試合登録内容の更新に失敗しました。";
				return false;
			}
		}
	}

	return true;
}

function getRoundCountFromTotalMatches(int $totalMatches): int {
    $teams = $totalMatches + 1;
	$round = (int)log($teams, 2);
    return $round;
}

/**
 * 64チーム固定（試合番号はあなたのCSVと同じ連番）で
 * (round, matchNo) -> (nextRound, nextMatchNo, slot) を返す
 */
function nextGameMatch( $p_tournamentId, $p_gameClass, int $round, int $matchNo): ?array {

	Global $g_Log;
	$g_Log->debug( "次の試合番号を計算します tournament_id: $p_tournamentId, game_class: $p_gameClass, round: $round, matchNo: $matchNo", __FUNCTION__, basename( __FILE__ ) );

	$gameMaxCount = getMaxGameCount( $p_tournamentId, $p_gameClass, 1 ); // ゲームブロックは初期値1
	$gameTotalRound = log($gameMaxCount, 2) + 1; // ゲーム数から現在のラウンドを計算
	$g_Log->debug( "試合数からラウンド数を計算します 総試合数: $gameMaxCount, 総ラウンド数: $gameTotalRound, tournament_id: $p_tournamentId, game_class: $p_gameClass, round: $round, matchNo: $matchNo", __FUNCTION__, basename( __FILE__ ) );

    // 各ラウンドの試合数（64チーム想定）
    $matchesPerRound = [1 => 32, 2 => 16, 3 => 8, 4 => 4, 5 => 2, 6 => 1];
	$matchesPerRound = [];
	for ($r = 1; $r <= $gameTotalRound; $r++) {
		$matchesPerRound[$r] = pow(2, $gameTotalRound - $r);
	}

    if (!isset($matchesPerRound[$round])) return null;
    if ($round === 6) return null; // 決勝は次がない

    // ラウンド開始番号を計算（Round1=1開始）
    $start = 1;
    for ($r = 1; $r < $round; $r++) {
        $start += $matchesPerRound[$r];
    }

    $indexInRound = $matchNo - $start;         // 0-based
    $nextRound = $round + 1;

    // 次ラウンド開始番号
    $nextStart = $start + $matchesPerRound[$round];

    // 次の試合番号
    $nextMatchNo = $nextStart + intdiv($indexInRound, 2);

    // どちらの枠に入るか（偶数index= A、奇数index= B）
    $slot = ($indexInRound % 2 === 0) ? 'team1_id' : 'team2_id';

	Global $g_Log;
	$g_Log->debug( "次の試合番号を計算しました nextRound: $nextRound, nextMatchNo: $nextMatchNo, slot: $slot", __FUNCTION__, basename( __FILE__ ) );

    return [$nextRound, $nextMatchNo, $slot];
}

# ==========================================================
# 試合内容追加処理
# @param int $p_tournamentId 更新対象の大会ID
# @param int $p_gameClass ゲームクラス
# @param int $p_gameBlock ゲームブロック
# @param int $p_gameCount ゲーム数
# @param int $p_teamId チームID
# @return boolean 更新成功すればtrue、失敗すればfalse
# ==========================================================
function insertNextGame( $p_tournamentId, $p_gameClass, $p_gameBlock, $p_gameCount, $p_teamId ){

	global $g_Log;
	$g_Log->debug( "次回戦追加処理 tournament_id: $p_tournamentId, game_class: $p_gameClass, game_block: $p_gameBlock, game_count: $p_gameCount, team_id: $p_teamId", __FUNCTION__, basename( __FILE__ ) );

	$tournament_id  = $p_tournamentId;
	$game_class     = $p_gameClass;
	$game_block     = $p_gameBlock;
	$game_count     = $p_gameCount;
	$team_id        = $p_teamId;
	$max_game_count = getMaxGameCount( $p_tournamentId, $p_gameClass, $p_gameBlock ); // ゲームブロックは初期値1

	$new_game_block = $game_block + 1; // ゲームブロックはゲーム数+1
	$new_game_count = (int) ceil( $game_count / 2 ); // ゲーム数はゲームブロックの最大値+1
	$game_odd_even = $game_count % 2; // ゲーム数の奇数偶数
	
	// ----------------------------------------------------------
	// チームが登録済みの場合の更新処理
	// ----------------------------------------------------------
	// SQL文作成
	$SQL_Select = "";
	$SQL_Select .= "SELECT ";
	$SQL_Select .= "  tournament_id ";
	$SQL_Select .= ", game_class ";
	$SQL_Select .= ", game_block ";
	$SQL_Select .= ", game_count ";
	$SQL_Select .= ", team1_id ";
	$SQL_Select .= ", team2_id ";
	$SQL_Select .= ", CASE WHEN team1_id = :team_id THEN 'team1' WHEN team2_id = :team_id THEN 'team2' ELSE '' END AS team_slot "; // チーム名はチームIDからサブクエリで取得
	$SQL_Select .= "FROM baseball_game ";
	$SQL_Select .= "WHERE is_enabled = 1 ";
	$SQL_Select .= "  AND tournament_id = :tournament_id ";
	$SQL_Select .= "  AND game_class = :game_class "; // ゲームクラスが裏試合の条件を満たす
	$SQL_Select .= "  AND game_block = :game_block "; // ゲームブロックが裏試合の条件を満たす
	$SQL_Select .= "  AND ( team1_id = :team_id OR team2_id = :team_id ) "; // チームIDは引数から取得
	
	$SQL_Select_Parameters = array(
		'tournament_id' => $tournament_id,
		'game_class' => $game_class,
		'game_block' => $new_game_block,
		'team_id' => $team_id,
	);

	try {
		$g_Log->debug( "試合で登録済みを検索します。[tournament_id={$tournament_id}, game_class={$game_class}, game_block={$new_game_block}, team_id={$team_id}]", __FUNCTION__, basename( __FILE__ ) );
		
		global $g_DB;
		$dataTable = $g_DB->select( $SQL_Select, $SQL_Select_Parameters );

		$g_Log->debug( "試合検索 : 件数=" . count( $dataTable ) . " 件", __FUNCTION__, basename( __FILE__ ) );

		foreach( $dataTable as $row ){
			$g_Log->debug( "ROW: tournament_id={$row['tournament_id']}, game_class={$row['game_class']}, game_block={$row['game_block']}, game_count={$row['game_count']},team1_id={$row['team1_id']}, team2_id={$row['team2_id']}, team_slot={$row['team_slot']}", __FUNCTION__, basename( __FILE__ ) );

			$game_count = $row[ 'game_count' ];
			$team1_id   = $row[ 'team1_id' ];
			$team2_id   = $row[ 'team2_id' ];
			$team_slot  = $row[ 'team_slot' ];
		}

		// 大会が存在しない場合は大会を作成
		if( count( $dataTable ) > 0 ){
			$g_Log->debug( "チームID:{$team_id} が登録済みのため処理をスキップします。", __FUNCTION__, basename( __FILE__ ) );
			return true; // 大会が存在する場合は更新成功とする
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'RES_ERR_MSG' ] = '大会内容の更新に失敗しました';
		$g_Log->error( $_SESSION[ 'RES_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}
	
	// ----------------------------------------------------------
	// チームが未登録の場合の更新処理
	// ----------------------------------------------------------

	$new_game = nextGameMatch( $tournament_id, $game_class, $game_block, $game_count );
	if ( $new_game === null ) {
		$g_Log->debug( "次の試合が存在しないため、次の試合登録内容の更新をスキップします", __FUNCTION__, basename( __FILE__ ) );
		return true; // 次の試合が存在しない場合は更新成功とする
	}
	$new_game_block = $new_game[0];
	$new_game_count = $new_game[1];
	$slot = $new_game[2];

	// SQL文作成
	$SQL_Update = "";
	$SQL_Update .= "UPDATE baseball_game ";
	$SQL_Update .= "SET $slot   = :team_id ";
	$SQL_Update .= "  , " . str_replace("_id", "_name", $slot) . " = ( SELECT team_name FROM baseball_team WHERE team_id = :team_id ) "; // チーム名はチームIDからサブクエリで取得
	$SQL_Update .= "  , updated_at = CURRENT_TIMESTAMP ";
	$SQL_Update .= "  , updated_by = :updated_by ";
	$SQL_Update .= "WHERE tournament_id = :tournament_id ";
	$SQL_Update .= "  AND game_class = :game_class ";
	$SQL_Update .= "  AND game_block = :game_block ";
	$SQL_Update .= "  AND game_count = :game_count ";
	$SQL_Update .= "  AND $slot      = 0 ";
	
	$SQL_Update_Parameters = array(
		'tournament_id' => $tournament_id,
		'game_class'    => $game_class,
		'game_block'    => $new_game_block,
		'game_count'    => $new_game_count,
		'team_id'       => $team_id,
		'updated_by'    => basename( __FILE__ ),
	);

	try {
		global $g_DB;
		$stmt = $g_DB->execute( $SQL_Update, $SQL_Update_Parameters );

		$g_Log->debug( "次試合更新処理 件数: $stmt", __FUNCTION__, basename( __FILE__ ) );

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
			$SQL_Insert .= ", :team2_id ";  // チーム2IDは初期値0
			$SQL_Insert .= ", IFNULL( ( SELECT team_name FROM baseball_team WHERE team_id = :team1_id ), '' ) "; // チーム名はチームIDからサブクエリで取得
			$SQL_Insert .= ", IFNULL( ( SELECT team_name FROM baseball_team WHERE team_id = :team2_id ), '' ) "; // チーム名2
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
				'game_class' => $game_class,
				'game_block' => $new_game_block,
				'game_count' => $new_game_count,
				'team1_id' => ($slot === 'team1_id') ? $team_id : 0,
				'team2_id' => ($slot === 'team2_id') ? $team_id : 0,
				'created_by' => basename( __FILE__ ),
				'updated_by' => basename( __FILE__ ),
			);

			$stmt = $g_DB->execute( $SQL_Insert, $SQL_Insert_Parameters );

			$g_Log->debug( "次試合追加処理 件数: $stmt", __FUNCTION__, basename( __FILE__ ) );

			if( $stmt === false ){
				throw new PDOException( "SQLの実行に失敗しました。" );
			}
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'RES_ERR_MSG' ] = '試合登録内容の追加に失敗しました';
		$g_Log->error( $_SESSION[ 'RES_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# 裏試合内容追加処理
# @param int $p_tournamentId 更新対象の大会ID
# @param int $p_gameClass ゲームクラス
# @param int $p_gameBlock ゲームブロック
# @param int $p_teamId チームID
# @return boolean 更新成功すればtrue、失敗すればfalse
# ==========================================================
function insertUraGame( $p_tournamentId, $p_gameClass, $p_gameBlock, $p_teamId ) {

	global $g_Log;
	$g_Log->debug( "裏試合情報登録処理 tournament_id: $p_tournamentId, team_id: $p_teamId, game_class: $p_gameClass, game_block: $p_gameBlock", __FUNCTION__, basename( __FILE__ ) );

	if( $p_gameClass != 1 || $p_gameBlock != 1 ) {
		$g_Log->debug( "ゲームクラスまたはゲームブロックが裏試合の条件を満たさないため、裏試合登録内容の更新をスキップします", __FUNCTION__, basename( __FILE__ ) );
		return true; // ゲームクラスまたはゲームブロックが裏試合の条件を満たさない場合は更新成功とする
	}

	$tournament_id  = $p_tournamentId;
	$game_class     = $p_gameClass;
	$game_block     = $p_gameBlock;
	$game_count	    = 1; // 裏試合のゲーム数は初期値1
	$team_id        = $p_teamId;
	
	// ----------------------------------------------------------
	// チームが登録済みの場合の更新処理
	// ----------------------------------------------------------
	// SQL文作成
	$SQL_Select = "";
	$SQL_Select .= "SELECT ";
	$SQL_Select .= "  tournament_id ";
	$SQL_Select .= ", game_class ";
	$SQL_Select .= ", game_block ";
	$SQL_Select .= ", game_count ";
	$SQL_Select .= ", team1_id ";
	$SQL_Select .= ", team2_id ";
	$SQL_Select .= ", CASE WHEN team1_id = :team_id THEN 'team1' WHEN team2_id = :team_id THEN 'team2' ELSE '' END AS team_slot "; // チーム名はチームIDからサブクエリで取得
	$SQL_Select .= "FROM baseball_game ";
	$SQL_Select .= "WHERE is_enabled = 1 ";
	$SQL_Select .= "  AND tournament_id = :tournament_id ";
	$SQL_Select .= "  AND game_class = :game_class "; // ゲームクラスが裏試合の条件を満たす
	$SQL_Select .= "  AND game_block = :game_block "; // ゲームブロックが裏試合の条件を満たす
	$SQL_Select .= "  AND ( team1_id = 0 OR team2_id = 0 ) "; // チームIDは引数から取得
	$SQL_Select .= "  AND ( team1_id = :team_id OR team2_id = :team_id ) "; // チームIDは引数から取得
	$SQL_Select .= "ORDER BY IFNULL( team1_id, 0 ) + IFNULL( team2_id, 0 ) DESC, game_id ASC "; // チームIDが0の試合を優先的に取得
	
	$SQL_Select_Parameters = array(
		'tournament_id' => $tournament_id,
		'game_class' => $game_class,
		'game_block' => $game_block,
		'team_id' => $team_id,
	);

	try {
		$g_Log->debug( "裏試合で登録済みを検索します。[tournament_id={$tournament_id}, game_class={$game_class}, game_block={$game_block}, team_id={$team_id}]", __FUNCTION__, basename( __FILE__ ) );
		
		global $g_DB;
		$dataTable = $g_DB->select( $SQL_Select, $SQL_Select_Parameters );

		$g_Log->debug( "裏試合検索 : 件数=" . count( $dataTable ) . " 件", __FUNCTION__, basename( __FILE__ ) );

		foreach( $dataTable as $row ){
			$g_Log->debug( "ROW: tournament_id={$row['tournament_id']}, game_class={$row['game_class']}, game_block={$row['game_block']}, game_count={$row['game_count']},team1_id={$row['team1_id']}, team2_id={$row['team2_id']}, team_slot={$row['team_slot']}", __FUNCTION__, basename( __FILE__ ) );

			$game_count = $row[ 'game_count' ];
			$team1_id   = $row[ 'team1_id' ];
			$team2_id   = $row[ 'team2_id' ];
			$team_slot  = $row[ 'team_slot' ];
		}

		// 裏大会が存在しない場合は裏大会を作成
		if( count( $dataTable ) > 0 ){
			$g_Log->debug( "チームID:{$team_id} が登録済みのため処理をスキップします。", __FUNCTION__, basename( __FILE__ ) );
			return true; // 裏大会が存在する場合は更新成功とする
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'RES_ERR_MSG' ] = '裏大会内容の更新に失敗しました';
		$g_Log->error( $_SESSION[ 'RES_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}
	
	// ----------------------------------------------------------
	// チームが未登録の場合の更新処理
	// ----------------------------------------------------------
	// SQL文作成
	$SQL_Select = "";
	$SQL_Select .= "SELECT ";
	$SQL_Select .= "  tournament_id ";
	$SQL_Select .= ", game_class ";
	$SQL_Select .= ", game_block ";
	$SQL_Select .= ", game_count ";
	$SQL_Select .= ", team1_id ";
	$SQL_Select .= ", team2_id ";
	$SQL_Select .= ", CASE WHEN team1_id = 0 THEN 'team1' WHEN team2_id = 0 THEN 'team2' ELSE '' END AS team_slot "; // チーム名はチームIDからサブクエリで取得
	$SQL_Select .= "FROM baseball_game ";
	$SQL_Select .= "WHERE is_enabled = 1 ";
	$SQL_Select .= "  AND tournament_id = :tournament_id ";
	$SQL_Select .= "  AND game_class = :game_class "; // ゲームクラスが裏試合の条件を満たす
	$SQL_Select .= "  AND game_block = :game_block "; // ゲームブロックが裏試合の条件を満たす
	$SQL_Select .= "  AND ( team1_id = 0 OR team2_id = 0 ) "; // チームIDは引数から取得
	$SQL_Select .= "ORDER BY IFNULL( team1_id, 0 ) + IFNULL( team2_id, 0 ) DESC, game_id ASC "; // チームIDが0の試合を優先的に取得
	
	$SQL_Select_Parameters = array(
		'tournament_id' => $tournament_id,
		'game_class' => $game_class,
		'game_block' => $game_block,
	);

	try {
		$g_Log->debug( "裏試合で対戦相手の決まっていない試合を検索します。", __FUNCTION__, basename( __FILE__ ) );
		
		global $g_DB;
		$dataTable = $g_DB->select( $SQL_Select, $SQL_Select_Parameters );

		$g_Log->debug( "裏試合検索 : 件数=" . count( $dataTable ) . " 件", __FUNCTION__, basename( __FILE__ ) );

		$match = false;

		foreach( $dataTable as $row ){
			$g_Log->debug( "ROW: tournament_id={$row['tournament_id']}, game_class={$row['game_class']}, game_block={$row['game_block']}, game_count={$row['game_count']}, team_slot={$row['team_slot']}", __FUNCTION__, basename( __FILE__ ) );
			$game_count = $row[ 'game_count' ];
			$team1_id   = $row[ 'team1_id' ];
			$team2_id   = $row[ 'team2_id' ];
			$team_slot  = $row[ 'team_slot' ];
			$g_Log->debug( "対戦相手が決定していない裏試合情報取得 : クラス:$game_class, ラウンド:$game_block, ゲーム数:$game_count, チームスロット:$team_slot", __FUNCTION__, basename( __FILE__ ) );
		}

		// 裏大会が存在しない場合は裏大会を作成
		if( count( $dataTable ) === 0 ){
			$max_game_count = getMaxGameCount( $p_tournamentId, $p_gameClass, $p_gameBlock ); // ゲームブロックは初期値1
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
			$SQL_Insert .= ", :team2_id ";  // チーム2IDは初期値0
			$SQL_Insert .= ", IFNULL( ( SELECT team_name FROM baseball_team WHERE team_id = :team1_id ), '' ) "; // チーム名はチームIDからサブクエリで取得
			$SQL_Insert .= ", IFNULL( ( SELECT team_name FROM baseball_team WHERE team_id = :team2_id ), '' ) "; // チーム名2
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
				'game_class'    => $game_class,
				'game_block'    => $game_block,
				'game_count'    => $max_game_count + 1, // ゲーム数は最大値+1
				'team1_id'      => $team_id,
				'team2_id'      => 0,
				'created_by'    => basename( __FILE__ ),
				'updated_by'    => basename( __FILE__ ),
			);
			$g_Log->debug( "INSERT: tournament_id={$tournament_id}, game_class={$game_class}, game_block={$game_block}, game_count=" . ( $max_game_count + 1 ) . ", team1_id={$team_id}, team2_id=0", __FUNCTION__, basename( __FILE__ ) );

			$stmt = $g_DB->execute( $SQL_Insert, $SQL_Insert_Parameters );

			$g_Log->debug( "裏試合を追加しました。", __FUNCTION__, basename( __FILE__ ) );

			if( $stmt === false ){
				throw new PDOException( "SQLの実行に失敗しました。" );
			}
		}
		else{
			// SQL文作成
			$SQL_Update = "";
			$SQL_Update .= "UPDATE baseball_game ";
			$SQL_Update .= "SET ${team_slot}_id = :team_id ";
			$SQL_Update .= "  , ${team_slot}_name = ( SELECT team_name FROM baseball_team WHERE team_id = :team_id ) "; // チーム名はチームIDからサブクエリで取得
			$SQL_Update .= "  , updated_at = CURRENT_TIMESTAMP ";
			$SQL_Update .= "  , updated_by = :updated_by ";
			$SQL_Update .= "WHERE tournament_id = :tournament_id ";
			$SQL_Update .= "  AND game_class = :game_class ";
			$SQL_Update .= "  AND game_block = :game_block ";
			$SQL_Update .= "  AND game_count = :game_count ";
			$SQL_Update .= "  AND ${team_slot}_id = 0 ";
	
			$SQL_Update_Parameters = array(
				'tournament_id' => $tournament_id,
				'game_class'    => $game_class,
				'game_block'    => $game_block,
				'game_count'    => $game_count,
				'team_id'       => $team_id,
				'updated_by'    => basename( __FILE__ ),
			);
			$g_Log->debug( "UPDATE: tournament_id={$tournament_id}, game_class={$game_class}, game_block={$game_block}, game_count={$game_count}, team1_id={$team_id}, team2_id=0", __FUNCTION__, basename( __FILE__ ) );

			$stmt = $g_DB->execute( $SQL_Update, $SQL_Update_Parameters );

			$g_Log->debug( "裏試合を更新しました。", __FUNCTION__, basename( __FILE__ ) );

			if( $stmt === false ){
				throw new PDOException( "SQLの実行に失敗しました。" );
			}

		}

	} catch ( Exception $e ) {
		$_SESSION[ 'RES_ERR_MSG' ] = '裏大会内容の更新に失敗しました';
		$g_Log->error( $_SESSION[ 'RES_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
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

<?php
	// 試合登録の読み込み
	$filename = __DIR__ . DIRECTORY_SEPARATOR . "article_gamemaintenance.php";
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
