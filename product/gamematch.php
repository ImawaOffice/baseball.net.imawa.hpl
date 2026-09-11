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
		'RES_TOURNAMENT_ID' => '',		// 大会ID
		'RES_TEAM1_ID'       => '',		// チームID1
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

		$FirstFlag = false;

		foreach ( getTournamentList() as $tournament ){
			if ( ! $FirstFlag ) {
				$_SESSION[ 'RES_TOURNAMENT_ID' ] = $tournament[ 'tournament_id' ];
				$g_Log->debug( "[ RES_TOURNAMENT_ID ] : " . $_SESSION[ 'RES_TOURNAMENT_ID' ], __FUNCTION__, basename( __FILE__ ) );
				$FirstFlag = true;
			}
		}

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

	// 試合分類の取得
	if( isset( $_POST[ 'game_class' ] ) ) {
		$g_Log->debug( "POST : game_class = " . $_POST[ 'game_class' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// チームIDの取得
	if( isset( $_POST[ 'team1_id' ] ) ) {
		$g_Log->debug( "POST : team1_id = " . $_POST[ 'team1_id' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// チームIDの取得
	if( isset( $_POST[ 'team2_id' ] ) ) {
		$g_Log->debug( "POST : team2_id = " . $_POST[ 'team2_id' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// チーム名の取得
	if( isset( $_POST[ 'team1_name' ] ) ) {
		$g_Log->debug( "POST : team1_name = " . $_POST[ 'team1_name' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// チーム名の取得
	if( isset( $_POST[ 'team2_name' ] ) ) {
		$g_Log->debug( "POST : team2_name = " . $_POST[ 'team2_name' ], __FUNCTION__, basename( __FILE__ ) );
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
	$recordsCount = getGameCount( $_POST[ 'tournament_id' ] );
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
# 大会登録件数取得処理
# @return int 取得成功すれば件数、失敗すればfalse
# ==========================================================
function getGameCount( $p_tournamentId ) {

	global $g_Log;
	$g_Log->debug( "大会登録件数取得処理", __FUNCTION__, basename( __FILE__ ) );
	
	// SQL文作成
	$SQL = "";
	$SQL .= "SELECT ";
	$SQL .= "  count( * ) AS counter ";
	$SQL .= "FROM baseball_game ";
	$SQL .= "WHERE is_enabled = 1 ";
	$SQL .= "AND tournament_id = :tournament_id ";
	
	$SQL_Parameters = array(
		"tournament_id" => $p_tournamentId,
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

	// 試合ブロック
	if( ! checkRequest( 'game_block', 'POST' ) ) {
		$_SESSION[ 'RES_ERR_MSG' ] = "試合ブロックエラー";
		$g_Log->debug( "試合ブロックのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 削除対象のID
	if ( deleteGameId( $_POST[ 'delete_button' ] ) ) {
		$g_Log->debug( "試合登録内容を削除しました： delete_button = " . $_POST[ 'delete_button' ], __FUNCTION__, basename( __FILE__ ) );

		updateGameCount( $_POST[ 'delete_button' ] ); // ゲーム数の更新
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

	// 試合分類
	if( ! checkRequest( 'game_class', 'POST' ) ) {
		$_SESSION[ 'RES_ERR_MSG' ] = "試合分類エラー";
		$g_Log->debug( "試合分類のチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}
	if( $_POST[ 'game_class' ] !== "0" && $_POST[ 'game_class' ] !== "1" ) {
		$_POST[ 'game_class' ] = "0";
	}

	// チーム名１
	if( ! checkRequest( 'team1_id', 'POST' ) ) {
		$_SESSION[ 'RES_ERR_MSG' ] = "チーム名１エラー";
		$g_Log->debug( "チーム名１のチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	if( ! checkText( $_POST[ 'team1_id' ] ) ) {
		$_SESSION[ 'RES_ERR_MSG' ] = "チーム名１エラー";
		$g_Log->debug( "チーム名１のチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// チーム名２
	if( ! checkRequest( 'team2_id', 'POST' ) ) {
		$_SESSION[ 'RES_ERR_MSG' ] = "チーム名２エラー";
		$g_Log->debug( "チーム名２のチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	if( ! checkText( $_POST[ 'team2_id' ] ) ) {
		$_SESSION[ 'RES_ERR_MSG' ] = "チーム名２エラー";
		$g_Log->debug( "チーム名２のチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
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
	$game_class     = checkText( $_POST[ 'game_class' ] );
	$game_id        = ""; // 試合IDは自動採番のため空文字
	$team1_id       = checkText( $_POST[ 'team1_id' ] );
	$team2_id       = checkText( $_POST[ 'team2_id' ] );
	$max_game_count = getMaxGameCount( $tournament_id, $game_class, 1 ); // ゲームブロックは初期値1
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
	$SQL .= ", :game_class "; // ゲームクラス
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
		'game_class' => $game_class,
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
# 試合内容追加処理
# @param int $p_tournamentId 更新対象の大会ID
# @return int 更新成功すれば最大ゲーム数、失敗すれば0
# ==========================================================
function getMaxGameCount( $p_tournamentId, $p_gameClass, $p_gameBlock ) {

	global $g_Log;
	$g_Log->debug( "試合内容追加処理", __FUNCTION__, basename( __FILE__ ) );

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
	$SQL .= "   AND game_class = :game_class ";
	$SQL .= "   AND game_block = :game_block ";
	
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
	
	// SQL文作成
	$SQL = "";
	$SQL .= "UPDATE baseball_game ";
	$SQL .= "SET team1_id = :team1_id ";
	$SQL .= "  , team2_id = :team2_id ";
	$SQL .= "  , team1_name = ( SELECT team_name FROM baseball_team WHERE team_id = :team1_id ) ";
	$SQL .= "  , team2_name = ( SELECT team_name FROM baseball_team WHERE team_id = :team2_id ) ";
	$SQL .= "  , updated_at = CURRENT_TIMESTAMP ";
	$SQL .= "  , updated_by = :updated_by ";
	$SQL .= "WHERE is_enabled = 1 ";
	$SQL .= "  AND tournament_id = :tournament_id ";
	$SQL .= "  AND game_id = :game_id ";
	
	$SQL_Parameters = array(
		'game_id' => $game_id,
		'tournament_id' => $tournament_id,
		'team1_id' => $team1_id,
		'team2_id' => $team2_id,
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
	$filename = __DIR__ . DIRECTORY_SEPARATOR . "article_gamematch.php";
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
