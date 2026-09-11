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
		'RES_TEAM_ID'       => '',		// チームID
		'RES_TEAM_NAME'     => '',		// チーム名
		'RES_TEAM_MANAGER'  => '',		// 責任者名
		'RES_TEAM_CONTACT'  => '',		// 連絡先
		'RES_TEAM_ACCESS_CODE' => '',	// アクセスコード
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
		$g_Log->debug( "GETリクエストのため大会ID抽出", __FUNCTION__, basename( __FILE__ ) );

		$FirstFlag = false;

		foreach ( getTournamentList() as $tournament ){
			if ( ! $FirstFlag ) {
				$_SESSION[ 'RES_TOURNAMENT_ID' ] = $tournament[ 'tournament_id' ];
				$g_Log->debug( "[ RES_TOURNAMENT_ID ] : " . $_SESSION[ 'RES_TOURNAMENT_ID' ], __FUNCTION__, basename( __FILE__ ) );
				$FirstFlag = true;
			}
		}
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

	// チームIDの取得
	if( isset( $_POST[ 'team_id' ] ) ) {
		$g_Log->debug( "POST : team_id = " . $_POST[ 'team_id' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// チーム名の取得
	if( isset( $_POST[ 'team_name' ] ) ) {
		$g_Log->debug( "POST : team_name = " . $_POST[ 'team_name' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// チーム責任者名の取得
	if( isset( $_POST[ 'team_manager' ] ) ) {
		$g_Log->debug( "POST : team_manager = " . $_POST[ 'team_manager' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// チーム連絡先の取得
	if( isset( $_POST[ 'team_contact' ] ) ) {
		$g_Log->debug( "POST : team_contact = " . $_POST[ 'team_contact' ], __FUNCTION__, basename( __FILE__ ) );
	}

	// チームアクセスコードの取得
	if( isset( $_POST[ 'team_access_code' ] ) ) {
		$g_Log->debug( "POST : team_access_code = " . $_POST[ 'team_access_code' ], __FUNCTION__, basename( __FILE__ ) );
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
	if ( deleteTeamId( $_POST[ 'delete_button' ] ) ) {
		$g_Log->debug( "チーム登録内容を削除しました： delete_button = " . $_POST[ 'delete_button' ], __FUNCTION__, basename( __FILE__ ) );
	} else {
		$g_Log->debug( "チーム登録内容の削除に失敗しました： delete_button = " . $_POST[ 'delete_button' ], __FUNCTION__, basename( __FILE__ ) );
		$_SESSION[ 'RES_ERR_MSG' ] = "チーム登録内容の削除に失敗しました。";
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

	// 大会ID
	if( ! checkRequest( 'team_tournament_id', 'POST' ) ) {
		$_SESSION[ 'RES_ERR_MSG' ] = "大会IDエラー";
		$g_Log->debug( "大会IDのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	if( ! checkText( $_POST[ 'team_tournament_id' ] ) ) {
		$_SESSION[ 'RES_ERR_MSG' ] = "大会IDエラー";
		$g_Log->debug( "大会IDのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// チームID(更新時のみチェック)
	if( ! checkRequest( 'update_button', 'POST' ) ) {
		if( ! checkRequest( 'team_id', 'POST' ) ) {
			$_SESSION[ 'RES_ERR_MSG' ] = "チームIDエラー";
			$g_Log->debug( "チームIDのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
			return false;
		}
	}

	// チーム名
	if( ! checkRequest( 'team_name', 'POST' ) ) {
		$_SESSION[ 'RES_ERR_MSG' ] = "チーム名エラー";
		$g_Log->debug( "チーム名のチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	if( ! checkText( $_POST[ 'team_name' ] ) ) {
		$_SESSION[ 'RES_ERR_MSG' ] = "チーム名エラー";
		$g_Log->debug( "チーム名のチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// チーム責任者名
	if( ! checkRequest( 'team_manager', 'POST' ) ) {
		$_SESSION[ 'RES_ERR_MSG' ] = "チーム責任者名エラー";
		$g_Log->debug( "チーム責任者名のチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	if( ! checkText( $_POST[ 'team_manager' ] ) ) {
		$_SESSION[ 'RES_ERR_MSG' ] = "チーム責任者名エラー";
		$g_Log->debug( "チーム責任者名のチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// チーム連絡先
	if( ! checkRequest( 'team_contact', 'POST' ) ) {
		$_SESSION[ 'RES_ERR_MSG' ] = "チーム連絡先エラー";
		$g_Log->debug( "チーム連絡先のチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	if( ! checkText( $_POST[ 'team_contact' ] ) ) {
		$_SESSION[ 'RES_ERR_MSG' ] = "チーム連絡先エラー";
		$g_Log->debug( "チーム連絡先のチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// アクセスコード
	if( ! checkRequest( 'team_access_code', 'POST' ) ) {
		$_SESSION[ 'RES_ERR_MSG' ] = "アクセスコードエラー";
		$g_Log->debug( "アクセスコードのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	if( ! checkText( $_POST[ 'team_access_code' ] ) ) {
		$_SESSION[ 'RES_ERR_MSG' ] = "アクセスコードエラー";
		$g_Log->debug( "アクセスコードのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
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
	$g_Log->debug( "[ RES_VIEW_STATE ] : " . $_SESSION[ 'RES_VIEW_STATE' ], __FUNCTION__, basename( __FILE__ ) );

	// 追加処理
	if( isset( $_POST[ 'add_button' ] ) && $_POST[ 'add_button' ] !== "" ) {
		if ( addTeam() ) {
			$g_Log->debug( "大会登録内容を追加しました", __FUNCTION__, basename( __FILE__ ) );
		} else {
			$g_Log->debug( "大会登録内容の追加に失敗しました", __FUNCTION__, basename( __FILE__ ) );
			$_SESSION[ 'RES_ERR_MSG' ] = "大会登録内容の追加に失敗しました。";
			return false;
		}
	}

	// 更新処理
	if( isset( $_POST[ 'update_button' ] ) && $_POST[ 'update_button' ] !== "" ) {
		if ( updateTeam() ) {
			$g_Log->debug( "大会登録内容を更新しました： tournament_id = " . $_POST[ 'tournament_id' ], __FUNCTION__, basename( __FILE__ ) );
		} else {
			$g_Log->debug( "大会登録内容の更新に失敗しました： tournament_id = " . $_POST[ 'tournament_id' ], __FUNCTION__, basename( __FILE__ ) );
			$_SESSION[ 'RES_ERR_MSG' ] = "大会登録内容の更新に失敗しました。";
			return false;
		}
	}

	return true;
}

# ==========================================================
# チーム内容追加処理
# @param int $p_tournamentId 更新対象の大会ID
# @return boolean 更新成功すればtrue、失敗すればfalse
# ==========================================================
function addTeam(){

	global $g_Log;
	$g_Log->debug( "チーム内容追加処理", __FUNCTION__, basename( __FILE__ ) );

	$team_tournament_id = checkText( $_POST[ 'team_tournament_id' ] );
	$team_id            = ""; // チームIDは自動採番のため空文字
	$team_name          = checkText( $_POST[ 'team_name' ] );
	$team_manager       = checkText( $_POST[ 'team_manager' ] );
	$team_contact       = checkText( $_POST[ 'team_contact' ] );
	$team_access_cd     = checkText( $_POST[ 'team_access_code' ] );
	
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
	$SQL .= "VALUES ";
	$SQL .= "( :team_name ";
	$SQL .= ", :team_manager ";
	$SQL .= ", :team_contact ";
	$SQL .= ", :team_access_cd ";
	$SQL .= ", :tournament_id ";
	$SQL .= ", 1 ";
	$SQL .= ", CURRENT_TIMESTAMP ";
	$SQL .= ", :created_by ";
	$SQL .= ", CURRENT_TIMESTAMP ";
	$SQL .= ", :updated_by ";
	$SQL .= ") ";
	
	$SQL_Parameters = array(
		'team_name' => $team_name,
		'team_manager' => $team_manager,
		'team_contact' => $team_contact,
		'team_access_cd' => $team_access_cd,
		'tournament_id' => $team_tournament_id,
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
		$_SESSION[ 'RES_ERR_MSG' ] = 'チーム登録内容の追加に失敗しました';
		$g_Log->error( $_SESSION[ 'RES_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# チーム登録内容更新処理
# @param int $p_teamId 更新対象のチームID
# @return boolean 更新成功すればtrue、失敗すればfalse
# ==========================================================
function updateTeam(){

	global $g_Log;
	$g_Log->debug( "チーム登録内容更新処理", __FUNCTION__, basename( __FILE__ ) );

	$team_tournament_id = checkText( $_POST[ 'team_tournament_id' ] );
	$team_id            = checkText( $_POST[ 'team_id' ] );
	$team_name          = checkText( $_POST[ 'team_name' ] );
	$team_manager       = checkText( $_POST[ 'team_manager' ] );
	$team_contact       = checkText( $_POST[ 'team_contact' ] );
	$team_access_cd     = checkText( $_POST[ 'team_access_code' ] );
	
	// SQL文作成
	$SQL = "";
	$SQL .= "UPDATE baseball_team ";
	$SQL .= "SET team_name = :team_name ";
	$SQL .= ", team_manager = :team_manager ";
	$SQL .= ", team_contact = :team_contact ";
	$SQL .= ", team_access_cd = :team_access_cd ";
	$SQL .= ", updated_at = CURRENT_TIMESTAMP ";
	$SQL .= ", updated_by = :updated_by ";
	$SQL .= "WHERE is_enabled = 1 ";
	$SQL .= "  AND tournament_id = :tournament_id ";
	$SQL .= "  AND team_id = :team_id ";
	
	$SQL_Parameters = array(
		'team_id' => $team_id,
		'team_name' => $team_name,
		'team_manager' => $team_manager,
		'team_contact' => $team_contact,
		'team_access_cd' => $team_access_cd,
		'tournament_id' => $team_tournament_id,
		'updated_by' => basename( __FILE__ ),
	);

	try {
		global $g_DB;
		$stmt = $g_DB->execute( $SQL, $SQL_Parameters );

		if( $stmt === false ){
			throw new PDOException( "SQLの実行に失敗しました。" );
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'RES_ERR_MSG' ] = 'チーム登録内容の更新に失敗しました';
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
	// チームメンテナンスの読み込み
	$filename = __DIR__ . DIRECTORY_SEPARATOR . "article_teammaintenance.php";
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
